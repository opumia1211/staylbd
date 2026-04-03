<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Courierapi;
use App\Models\OrderShipmentTracking;
use App\Services\Courier\CourierManager;
use Illuminate\Http\Request;
use App\Traits\OrderConfirmation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;

class OrderController extends Controller
{
	use OrderConfirmation;

	/** Insert courier log only if courier_logs table exists (keeps all লেনদেন saved when table is present). */
	private function insertCourierLog(array $data): void
	{
		if (!Schema::hasTable('courier_logs')) {
			return;
		}
		$data['created_at'] = $data['updated_at'] = now();
		DB::table('courier_logs')->insert($data);
	}

	public function index(Request $request)
	{
		$pageTitle = __('All Orders');
		$orders = $this->orderData($request, null);
		$stats = $this->orderStats(null);
		$scope = 'all';
		$emptyMessage = __('No orders found');
		$pathaoData = $this->pathaoDropdownData();
		return view('admin.order.index', compact('pageTitle', 'orders', 'stats', 'scope', 'emptyMessage') + $pathaoData);
	}

	public function pending(Request $request)
	{
		$pageTitle = __('Pending Orders');
		$scope = 'pending';
		$orders = $this->orderData($request, $scope);
		$stats = $this->orderStats($scope);
		$emptyMessage = __('No pending orders');
		$pathaoData = $this->pathaoDropdownData();
		return view('admin.order.index', compact('pageTitle', 'orders', 'stats', 'scope', 'emptyMessage') + $pathaoData);
	}

	public function confirmed(Request $request)
	{
		$pageTitle = __('Confirmed Orders');
		$orders = $this->orderData($request, 'confirmed');
		$stats = $this->orderStats('confirmed');
		$scope = 'confirmed';
		$emptyMessage = __('No confirmed orders');
		$pathaoData = $this->pathaoDropdownData();
		return view('admin.order.index', compact('pageTitle', 'orders', 'stats', 'scope', 'emptyMessage') + $pathaoData);
	}

	public function processing(Request $request)
	{
		$pageTitle = __('Processing Orders');
		$orders = $this->orderData($request, 'processing');
		$stats = $this->orderStats('processing');
		$scope = 'processing';
		$emptyMessage = __('No processing orders');
		$pathaoData = $this->pathaoDropdownData();
		return view('admin.order.index', compact('pageTitle', 'orders', 'stats', 'scope', 'emptyMessage') + $pathaoData);
	}

	public function packaging(Request $request)
	{
		$pageTitle = __('Packaging Orders');
		$orders = $this->orderData($request, 'packaging');
		$stats = $this->orderStats('packaging');
		$scope = 'packaging';
		$emptyMessage = __('No packaging orders');
		$pathaoData = $this->pathaoDropdownData();
		return view('admin.order.index', compact('pageTitle', 'orders', 'stats', 'scope', 'emptyMessage') + $pathaoData);
	}

	public function shipped(Request $request)
	{
		$pageTitle = __('Shipped Orders');
		$orders = $this->orderData($request, 'shipped');
		$stats = $this->orderStats('shipped');
		$scope = 'shipped';
		$emptyMessage = __('No shipped orders');
		$pathaoData = $this->pathaoDropdownData();
		return view('admin.order.index', compact('pageTitle', 'orders', 'stats', 'scope', 'emptyMessage') + $pathaoData);
	}

	public function delivered(Request $request)
	{
		$pageTitle = __('Delivered Orders');
		$orders = $this->orderData($request, 'delivered');
		$stats = $this->orderStats('delivered');
		$scope = 'delivered';
		$emptyMessage = __('No delivered orders');
		$pathaoData = $this->pathaoDropdownData();
		return view('admin.order.index', compact('pageTitle', 'orders', 'stats', 'scope', 'emptyMessage') + $pathaoData);
	}

	public function cancel(Request $request)
	{
		$pageTitle = __('Cancelled Orders');
		$orders = $this->orderData($request, 'cancel');
		$stats = $this->orderStats('cancel');
		$scope = 'cancel';
		$emptyMessage = __('No cancelled orders');
		$pathaoData = $this->pathaoDropdownData();
		return view('admin.order.index', compact('pageTitle', 'orders', 'stats', 'scope', 'emptyMessage') + $pathaoData);
	}

	/** Pathao dropdown data for modals (stores, cities). Returns empty arrays if not configured. */
	private function pathaoDropdownData(): array
	{
		$api = Courierapi::where(['status' => 1, 'type' => 'pathao'])->first();
		if (!$api) {
			return ['pathaostore' => [], 'pathaocities' => []];
		}
		try {
			$courierManager = app(CourierManager::class);
			$driver = $courierManager->driver('pathao');
			$pathaostore = $driver->getOptions($api, 'stores', request());
			$pathaocities = $driver->getOptions($api, 'cities', request());
			$normalize = function ($arr) {
				if (!is_array($arr)) return ['data' => []];
				return isset($arr['data']) ? $arr : ['data' => $arr];
			};
			return [
				'pathaostore' => $normalize($pathaostore),
				'pathaocities' => $normalize($pathaocities),
			];
		} catch (\Throwable $e) {
			return ['pathaostore' => ['data' => []], 'pathaocities' => ['data' => []]];
		}
	}

	/** Order list with filters, date range, payment type, per-page. */
	private function orderData(Request $request, $scope = null)
	{
		$query = $scope ? Order::$scope() : Order::query();
		$query->with(['user', 'orderDetail', 'deposit.gateway']);

		$searchCols = ['order_no', 'user:username', 'user:email'];
		if (Schema::hasColumn('orders', 'guest_phone')) {
			$searchCols = array_merge($searchCols, ['guest_name', 'guest_phone', 'guest_email']);
		}
		$query->searchable($searchCols);

		if ($request->filled('date_from')) {
			$query->whereDate('created_at', '>=', $request->date_from);
		}
		if ($request->filled('date_to')) {
			$query->whereDate('created_at', '<=', $request->date_to);
		}
		if (in_array($request->payment_type, [ (string) Status::PAYMENT_ONLINE, (string) Status::PAYMENT_OFFLINE ], true)) {
			$query->where('payment_type', (int) $request->payment_type);
		}

		$perPage = (int) $request->get('per_page', getPaginate());
		$perPage = in_array($perPage, [10, 20, 50, 100, 200], true) ? $perPage : getPaginate();

		return $query->latest('created_at')->paginate($perPage)->withQueryString();
	}

	/** Summary stats for the current scope (count, total value, today count). */
	private function orderStats(?string $scope): array
	{
		$base = $scope ? Order::$scope() : Order::query();
		$todayStart = now()->startOfDay();

		return [
			'total_count' => (clone $base)->count(),
			'total_value' => (clone $base)->sum('total'),
			'today_count' => (clone $base)->where('created_at', '>=', $todayStart)->count(),
			'today_value' => (clone $base)->where('created_at', '>=', $todayStart)->sum('total'),
		];
	}

	/** Export pending (or current scope) orders as CSV. */
	public function export(Request $request)
	{
		$scope = $request->get('scope', 'pending');
		$query = in_array($scope, ['pending', 'confirmed', 'processing', 'packaging', 'shipped', 'delivered', 'cancel'], true)
			? Order::$scope()
			: Order::query();
		$query->with('user');
		$query->searchable(['order_no', 'user:username', 'user:email']);
		if ($request->filled('date_from')) {
			$query->whereDate('created_at', '>=', $request->date_from);
		}
		if ($request->filled('date_to')) {
			$query->whereDate('created_at', '<=', $request->date_to);
		}
		$orders = $query->latest('created_at')->limit(5000)->get();

		$filename = 'orders-' . ($scope ?: 'all') . '-' . date('Y-m-d-His') . '.csv';
		$headers = [
			'Content-Type' => 'text/csv; charset=UTF-8',
			'Content-Disposition' => 'attachment; filename="' . $filename . '"',
		];

		$callback = function () use ($orders) {
			$out = fopen('php://output', 'w');
			fputcsv($out, ['Order No', 'Customer', 'Email', 'Total', 'Payment', 'Status', 'Date']);
			$statusMap = [
				Status::ORDER_PENDING => 'Pending',
				Status::ORDER_CONFIRMED => 'Confirmed',
				Status::ORDER_PROCESSING => 'Processing',
				Status::ORDER_PACKAGING => 'Packaging',
				Status::ORDER_SHIPPED => 'Shipped',
				Status::ORDER_DELIVERED => 'Delivered',
				Status::ORDER_CANCEL => 'Cancelled',
			];
			foreach ($orders as $o) {
				$status = $statusMap[$o->order_status] ?? 'N/A';
				$customer = $o->isGuest() ? ($o->guest_name ?? '') : ($o->user->username ?? '');
				$email = $o->isGuest() ? ($o->guest_email ?? '') : ($o->user->email ?? '');
				fputcsv($out, [
					$o->order_no,
					$customer,
					$email,
					$o->total,
					$o->payment_type == Status::PAYMENT_ONLINE ? 'Online' : 'COD',
					$status,
					$o->created_at->format('Y-m-d H:i'),
				]);
			}
			fclose($out);
		};

		return response()->stream($callback, 200, $headers);
	}

	public function details($id)
	{
		$pageTitle = 'Order Detail';
		$order = Order::where('id', $id)->with(['orderDetail.product', 'coupon', 'shipping', 'deposit', 'user', 'orderDetail', 'shipmentTrackings'])->firstOrFail();
		$orderCategory = $this->orderCategoryForStatus($order->order_status);
		return view('admin.order.detail', compact('pageTitle', 'order', 'orderCategory'));
	}

	/** Route name and label for the order list tab this order belongs to. */
	private function orderCategoryForStatus(int $orderStatus): array
	{
		$map = [
			Status::ORDER_PENDING => ['route' => 'admin.orders.pending', 'label' => __('Pending')],
			Status::ORDER_CONFIRMED => ['route' => 'admin.orders.confirmed', 'label' => __('Confirmed')],
			Status::ORDER_PROCESSING => ['route' => 'admin.orders.processing', 'label' => __('Processing')],
			Status::ORDER_PACKAGING => ['route' => 'admin.orders.packaging', 'label' => __('Packaging')],
			Status::ORDER_SHIPPED => ['route' => 'admin.orders.shipped', 'label' => __('Shipped')],
			Status::ORDER_DELIVERED => ['route' => 'admin.orders.delivered', 'label' => __('Delivered')],
			Status::ORDER_CANCEL => ['route' => 'admin.orders.cancel', 'label' => __('Cancelled')],
			Status::ORDER_OUT_FOR_DELIVERY => ['route' => 'admin.orders.shipped', 'label' => __('Shipped')],
			Status::ORDER_DELIVERY_FAILED => ['route' => 'admin.orders.shipped', 'label' => __('Shipped')],
			Status::ORDER_RETURNED => ['route' => 'admin.orders.shipped', 'label' => __('Shipped')],
		];
		return $map[$orderStatus] ?? ['route' => 'admin.orders.index', 'label' => __('All Orders')];
	}

	public function invoice($id)
	{
		$pageTitle = 'Print Invoice';
		$order = Order::where('id', $id)->with(['orderDetail.product', 'coupon', 'shipping', 'deposit', 'user', 'orderDetail'])->firstOrFail();

		$needsRefresh = false;
		if (\Illuminate\Support\Facades\Schema::hasColumn('orders', 'delivery_scan_token')) {
			if (trim((string)($order->delivery_scan_token ?? '')) === '') {
				$order->delivery_scan_token = \Illuminate\Support\Str::random(48);
				$order->save();
				$needsRefresh = true;
			}
		}
		if (\Illuminate\Support\Facades\Schema::hasColumn('orders', 'delivery_driver_scan_token')) {
			if (trim((string)($order->delivery_driver_scan_token ?? '')) === '') {
				$order->delivery_driver_scan_token = \Illuminate\Support\Str::random(48);
				$order->save();
				$needsRefresh = true;
			}
		}
		if ($needsRefresh) {
			$order->refresh();
		}

		return view('admin.order.invoice', compact('order'));
	}

	public function updateAddress(Request $request, $id)
	{
		$request->validate([
			'address' => 'required|string|max:500',
			'address_2' => 'nullable|string|max:500',
			'state' => 'nullable|string|max:100',
			'city' => 'nullable|string|max:100',
			'zip' => 'nullable|string|max:20',
			'country' => 'nullable|string|max:100',
			'division' => 'nullable|string|max:100',
			'thana' => 'nullable|string|max:100',
		]);
		$order = Order::findOrFail($id);
		$address = is_string($order->address) ? json_decode($order->address, true) : (array) $order->address;
		$address['address'] = $request->address;
		$address['address_2'] = $request->input('address_2', '');
		$address['state'] = $request->input('state', '');
		$address['city'] = $request->input('city', '');
		$address['zip'] = $request->input('zip', '');
		$address['country'] = $request->input('country', $address['country'] ?? '');
		$address['division'] = $request->input('division', '');
		$address['thana'] = $request->input('thana', '');
		$order->address = $address;
		$order->save();
		$notify[] = ['success', __('Order address updated.')];
		return back()->withNotify($notify);
	}

	public function status(Request $request, $id)
	{
		$request->validate([
			'order_status' => 'required|integer',
		]);

		$order = Order::where('id', $id)->with('user', 'orderDetail')->firstOrFail();
		$order->order_status = $request->order_status;
		$user = $order->user;

		$statusLabel = '';
		$notifyMessage = '';

		if ($request->order_status == Status::ORDER_CONFIRMED) {
			$statusLabel = 'Confirmed';
			$notifyMessage = __('Your order has been confirmed. We have received your order.');
		} elseif ($request->order_status == Status::ORDER_PROCESSING) {
			$statusLabel = 'Processing';
			$notifyMessage = __('Your order is now being processed. We are preparing your items.');
		} elseif ($request->order_status == Status::ORDER_PACKAGING) {
			$statusLabel = 'Packaging';
			$notifyMessage = __('Your order is being packed. It will be shipped soon.');
		} elseif ($request->order_status == Status::ORDER_SHIPPED) {
			$statusLabel = 'Shipped';
			$notifyMessage = __('Your product is ready for delivery / has been shipped. You can track your order.');
		} elseif ($request->order_status == Status::ORDER_DELIVERED) {
			$statusLabel = 'Delivered';

			if ($order->payment_type == Status::PAYMENT_OFFLINE) {
				$order->payment_status = Status::ORDER_PAYMENT_SUCCESS;
			}

			$productSellUpdate = [];
			foreach ($order->orderDetail as $detail) {
				$productSellUpdate[(int) $detail->product_id] = (int) $detail->quantity;
			}
			if (!empty($productSellUpdate)) {
				$updateValues = [];
				foreach ($productSellUpdate as $pid => $quantity) {
					$pid = (int) $pid;
					$quantity = (int) $quantity;
					$updateValues[] = "WHEN id = {$pid} THEN sale_count + {$quantity}";
				}
				$updateValues = implode(' ', $updateValues);
				Product::whereIn('id', array_keys($productSellUpdate))->update([
					'sale_count' => DB::raw("CASE $updateValues ELSE sale_count END")
				]);
			}
			$notifyMessage = __('Your order has been delivered. Thank you for shopping with us.');
		} else {
			$statusLabel = 'Cancelled';
			$notifyMessage = __('Your order has been cancelled.');
			static::orderCancel($order);
		}

		$order->save();

		notify($user, 'ORDER_STATUS', [
			'method_name' => $notifyMessage ?: ('Your order has now ' . $statusLabel),
			'user_name' => $user->username,
			'order_no' => $order->order_no,
			'total' => showAmount($order->total),
			'link' => route('user.order.detail', $order->id),
		]);

		$notify[] = ['success', 'Order status change successfully.'];
		return back()->withNotify($notify);
	}

	// Courier API Methods
	public function pathaocity(CourierManager $courierManager)
	{
		$api = Courierapi::where(['status' => 1, 'type' => 'pathao'])->first();
		if (!$api)
			return response()->json(['data' => []]);

		$driver = $courierManager->driver('pathao');
		$cities = $driver->getOptions($api, 'cities', request());
		return response()->json(['data' => $cities]);
	}

	public function pathaozone(CourierManager $courierManager)
	{
		$api = Courierapi::where(['status' => 1, 'type' => 'pathao'])->first();
		if (!$api)
			return response()->json(['data' => []]);

		$driver = $courierManager->driver('pathao');
		$zones = $driver->getOptions($api, 'zones', request());
		return response()->json(['data' => $zones]);
	}

	public function order_pathao(Request $request)
	{
		$request->validate([
			'order_ids' => 'required|array',
			'pathaostore' => 'required',
			'pathaocity' => 'required',
			'pathaozone' => 'required',
			'pathaoarea' => 'required'
		]);

		$orders_id = $request->order_ids;
		$success_count = 0;
		$error_count = 0;

		foreach ($orders_id as $order_id) {
			$order = Order::with('shipping')->find($order_id);

			if (!$order) {
				$error_count++;
				continue;
			}

			// Pathao API
			$pathao_info = Courierapi::where(['status' => 1, 'type' => 'pathao'])->select('id', 'type', 'url', 'token', 'status')->first();

			if ($pathao_info) {
				$response = Http::withHeaders([
					'Authorization' => 'Bearer ' . $pathao_info->token,
					'Accept' => 'application/json',
					'Content-Type' => 'application/json',
				])->post($pathao_info->url . '/api/v1/orders', [
							'store_id' => $request->pathaostore,
							'merchant_order_id' => $order->order_no,
							'sender_name' => 'Store Owner',
							'sender_phone' => $order->shipping ? $order->shipping->phone : '',
							'recipient_name' => $order->shipping ? $order->shipping->name : '',
							'recipient_phone' => $order->shipping ? $order->shipping->phone : '',
							'recipient_address' => $order->shipping ? $order->shipping->address : '',
							'recipient_city' => $request->pathaocity,
							'recipient_zone' => $request->pathaozone,
							'recipient_area' => $request->pathaoarea,
							'delivery_type' => 48,
							'item_type' => 2,
							'special_instruction' => 'Please handle with care',
							'item_quantity' => 1,
							'item_weight' => 0.5,
							'amount_to_collect' => round($order->total),
							'item_description' => 'Product delivery',
						]);

				$apiPayload = [
					'store_id' => $request->pathaostore,
					'merchant_order_id' => $order->order_no,
					'recipient_name' => $order->shipping ? $order->shipping->name : '',
					'recipient_city' => $request->pathaocity,
					'recipient_zone' => $request->pathaozone,
					'recipient_area' => $request->pathaoarea,
				];

				if ($response->status() == 200) {
					$resData = $response->json();
					DB::transaction(function () use ($order, $resData, $apiPayload) {
						$order->order_status = Status::ORDER_SHIPPED;
						$order->save();
						$this->insertCourierLog([
							'order_id' => $order->id,
							'courier_type' => 'pathao',
							'courier_order_id' => $resData['data']['consignment_id'] ?? ($resData['consignment_id'] ?? null),
							'status' => 'success',
							'request_data' => json_encode($apiPayload),
							'response_data' => json_encode($resData),
							'error_message' => null,
						]);
					});
					$success_count++;
				} else {
					$this->insertCourierLog([
						'order_id' => $order->id,
						'courier_type' => 'pathao',
						'courier_order_id' => null,
						'status' => 'failed',
						'request_data' => json_encode($apiPayload),
						'response_data' => $response->body(),
						'error_message' => 'HTTP ' . $response->status() . ': ' . $response->body(),
					]);
					$error_count++;
				}
			} else {
				$error_count++;
			}
		}

		if ($success_count > 0) {
			$notify[] = ['success', $success_count . ' orders sent to Pathao successfully'];
		}

		if ($error_count > 0) {
			$notify[] = ['error', $error_count . ' orders failed to send to Pathao'];
		}

		return back()->withNotify($notify);
	}

	public function bulk_courier(Request $request, $slug, CourierManager $courierManager)
	{
		$pageTitle = 'Bulk Courier - ' . ucfirst($slug);

		$driver = $courierManager->driver($slug);
		if (!$driver) {
			$notify[] = ['error', 'Courier driver not supported.'];
			return to_route('admin.api.courier.manage')->withNotify($notify);
		}

		$api = Courierapi::where('type', $slug)->first();
		if (!$api) {
			$notify[] = ['error', 'Courier provider not found. Please add it first.'];
			return to_route('admin.api.courier.manage')->withNotify($notify);
		}

		$query = Order::whereIn('order_status', [Status::ORDER_CONFIRMED, Status::ORDER_PACKAGING])->with("user")->latest();

		if ($request->filled('search')) {
			$q = '%' . $request->search . '%';
			$query->where(function ($qry) use ($q) {
				$qry->where('order_no', 'like', $q)->orWhereHas('user', function ($u) use ($q) {
					$u->where('username', 'like', $q);
				});
			});
		}
		if ($request->filled('date_from')) {
			$query->whereDate('created_at', '>=', $request->date_from);
		}
		if ($request->filled('date_to')) {
			$query->whereDate('created_at', '<=', $request->date_to);
		}

		$orders = $query->paginate(getPaginate())->withQueryString();

		$sentOrderIds = [];
		if (Schema::hasTable('courier_logs')) {
			$sentOrderIds = DB::table('courier_logs')
				->where('courier_type', $slug)
				->where('status', 'success')
				->pluck('order_id')
				->unique()
				->values()
				->all();
		}

		$activeProviders = Courierapi::where('status', 1)->orderBy('sort_order')->orderBy('name')->get();
		$currentProvider = $api;

		// Fetch dynamic options from driver
		$driverOptions = [];
		if ($api) {
			if ($slug === 'pathao') {
				$driverOptions['stores'] = $driver->getOptions($api, 'stores', $request);
				$driverOptions['cities'] = $driver->getOptions($api, 'cities', $request);
			}
			// Add other driver-specific options here or make it generic
		}

		return view('admin.order.bulk_courier', compact('pageTitle', 'orders', 'sentOrderIds', 'activeProviders', 'currentProvider', 'driverOptions', 'driver'));
	}

	public function order_steadfast(Request $request)
	{
		$request->validate([
			'order_ids' => 'required|array',
			'order_ids.*' => 'exists:orders,id',
			'consignment_type' => 'required|in:1,2',
			'delivery_type' => 'required|in:1,2',
			'city' => 'required|string',
			'area' => 'required|string'
		]);

		try {
			$steadfast = Courierapi::where('type', 'steadfast')->where('status', 1)->first();
			if (!$steadfast) {
				return back()->with('error', 'Steadfast API not configured or disabled');
			}

			$orders = Order::with('user')->whereIn('id', $request->order_ids)->get();
			$successCount = 0;
			$errorCount = 0;

			foreach ($orders as $order) {
				try {
					// Prepare Steadfast API data
					$apiData = [
						'consignment_type' => $request->consignment_type,
						'delivery_type' => $request->delivery_type,
						'city' => $request->city,
						'area' => $request->area,
						'order_id' => $order->id,
						'order_no' => $order->order_no,
						'customer_name' => $order->user->username ?? 'N/A',
						'customer_phone' => $order->user->mobile ?? 'N/A',
						'customer_address' => $order->shipping_address ?? 'N/A',
						'amount' => $order->total,
						'weight' => 1, // Default weight
						'notes' => 'Order from website'
					];

					// Send to Steadfast API
					$response = Http::withHeaders([
						'Authorization' => 'Bearer ' . $steadfast->token,
						'Content-Type' => 'application/json'
					])->post($steadfast->url . '/api/consignment', $apiData);

					if ($response->successful()) {
						$responseData = $response->json();
						$this->insertCourierLog([
							'order_id' => $order->id,
							'courier_type' => 'steadfast',
							'courier_order_id' => $responseData['consignment_id'] ?? null,
							'status' => 'success',
							'request_data' => json_encode($apiData),
							'response_data' => json_encode($responseData),
							'error_message' => null,
						]);
						$successCount++;
					} else {
						throw new \Exception('API request failed: ' . $response->body());
					}

				} catch (\Exception $e) {
					$this->insertCourierLog([
						'order_id' => $order->id,
						'courier_type' => 'steadfast',
						'courier_order_id' => null,
						'status' => 'failed',
						'request_data' => json_encode($apiData ?? []),
						'response_data' => null,
						'error_message' => $e->getMessage(),
					]);
					$errorCount++;
				}
			}

			$message = "Steadfast: {$successCount} orders sent successfully";
			if ($errorCount > 0) {
				$message .= ", {$errorCount} orders failed";
			}

			return back()->with('success', $message);

		} catch (\Exception $e) {
			return back()->with('error', 'Error sending orders to Steadfast: ' . $e->getMessage());
		}
	}

	public function bulk_courier_send(Request $request, CourierManager $courierManager)
	{
		$request->validate([
			'courier_type' => 'required|string',
			'order_ids' => 'required|array',
			'order_ids.*' => 'required|integer|exists:orders,id',
		]);

		$api = Courierapi::where('type', $request->courier_type)->first();
		if (!$api) {
			$notify[] = ['error', 'Courier provider not found.'];
			return back()->withNotify($notify);
		}

		$driver = $courierManager->driver($request->courier_type);
		if (!$driver) {
			$notify[] = ['error', 'Courier driver not supported.'];
			return back()->withNotify($notify);
		}

		$orderIds = $request->order_ids;
		$successCount = 0;
		$failCount = 0;
		$errors = [];

		foreach ($orderIds as $orderId) {
			$order = Order::with('user', 'shipping')->find($orderId);
			if (!$order)
				continue;

			// Check if already sent
			$alreadySent = DB::table('courier_logs')
				->where('order_id', $order->id)
				->where('courier_type', $request->courier_type)
				->where('status', 'success')
				->exists();

			if ($alreadySent) {
				$successCount++;
				continue;
			}

			// Create log entry (initially pending/failed)
			$logId = DB::table('courier_logs')->insertGetId([
				'order_id' => $order->id,
				'courier_type' => $request->courier_type,
				'request_data' => json_encode($request->all()),
				'status' => 'pending',
				'created_at' => now(),
				'updated_at' => now(),
			]);

			list($success, $courierOrderId, $error) = $driver->sendOrder($api, $order, $request->all());

			if ($success) {
				$successCount++;
				DB::table('courier_logs')->where('id', $logId)->update([
					'status' => 'success',
					'courier_order_id' => $courierOrderId,
					'response_data' => json_encode(['courier_order_id' => $courierOrderId]),
					'updated_at' => now(),
				]);

				// Update order status if needed (optional, depends on business logic)
				if ($order->order_status == Status::ORDER_CONFIRMED) {
					$order->order_status = Status::ORDER_SHIPPED;
					$order->save();

					// Create tracking entry
					OrderShipmentTracking::create([
						'order_id' => $order->id,
						'status' => OrderShipmentTracking::STATUS_PICKED,
						'tracking_number' => $courierOrderId,
						'courier_name' => $api->name,
						'notes' => 'Handed over to ' . $api->name,
					]);
				}
			} else {
				$failCount++;
				$errors[] = "Order #{$order->order_no}: " . ($error ?? 'Unknown error');
				DB::table('courier_logs')->where('id', $logId)->update([
					'status' => 'failed',
					'error_message' => $error,
					'updated_at' => now(),
				]);
			}
		}

		$msg = "Processed " . count($orderIds) . " orders. Success: $successCount, Failed: $failCount.";
		if ($failCount > 0) {
			$notify[] = ['warning', $msg];
			return back()->withNotify($notify)->with('courier_errors', $errors);
		}

		$notify[] = ['success', $msg];
		return back()->withNotify($notify);
	}
}
