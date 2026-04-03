<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Throwable;
use Illuminate\Auth\AuthenticationException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
        'api_token',
        'token',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            if (app()->environment('production') && $this->shouldReport($e)) {
                \Illuminate\Support\Facades\Log::channel('daily')->error('Unhandled exception', [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]);
                $email = config('mail.from.address') ?: env('CRITICAL_ERROR_EMAIL');
                if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                try {
                    $msg = "Critical error: " . $e->getMessage() . "\nFile: " . $e->getFile() . ":" . $e->getLine();
                    \Illuminate\Support\Facades\Mail::raw($msg, function ($m) use ($email) {
                        $m->to($email)->subject('StayLBD Critical Error');
                    });
                } catch (\Throwable $ignored) {
                }
                }
            }
        });
    }

    /**
     * User-friendly error for Checkout / Payment (Global Exception Handling)
     */
    public function render($request, Throwable $e)
    {
        if ($e instanceof ValidationException && $request->is('user/register') && $request->isMethod('POST')) {
            return redirect()->route('user.register')
                ->withErrors($e->errors())
                ->withInput();
        }

        $isCheckoutOrPayment = $request->is('user/checkout*') || $request->is('user/deposit*') || $request->is('user/order*');
        $friendlyMessage = __('Something went wrong. Please try again or contact support.');

        if ($isCheckoutOrPayment && !config('app.debug')) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $friendlyMessage], 500);
            }
            return redirect()->back()->with('error', $friendlyMessage)->withInput();
        }

        return parent::render($request, $e);
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'remark' => 'unauthenticated',
                'status' => 'error',
                'message' => ['error' => ['Unauthorized request']]
            ], 401);
        }
        if (method_exists($exception, 'guard') && $exception->guard() === 'admin') {
            return redirect()->guest(route('admin.login'));
        }
        if (request()->is('api/*')) {
            return response()->json([
                'remark' => 'unauthenticated',
                'status' => 'error',
                'message' => ['error' => ['Unauthorized request']]
            ], 401);
        }
        return redirect()->guest(route('user.login'));
    }
}

