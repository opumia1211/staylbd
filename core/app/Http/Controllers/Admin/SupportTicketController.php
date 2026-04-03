<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use App\Traits\SupportTicketManager;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class SupportTicketController extends Controller
{
    use SupportTicketManager;

    public function __construct()
    {
        parent::__construct();
        $this->middleware(function ($request, $next) {
            $this->user = auth()->guard('admin')->user();
            return $next($request);
        });

        $this->userType = 'admin';
        $this->column = 'admin_id';
    }

    protected function applyFilters($query)
    {
        if (request()->filled('channel') && \Illuminate\Support\Facades\Schema::hasColumn('support_tickets', 'channel')) {
            $query->where('channel', request('channel'));
        }
        $allowedSubjects = ['Live Chat Message', 'General Inquiry', 'Report a Problem', 'Order Support'];
        if (request()->filled('subject') && in_array(request('subject'), $allowedSubjects)) {
            $query->where('subject', request('subject'));
        }
        return $query;
    }

    /**
     * Message Center: one token/card per user. All messages from that user in one thread.
     */
    public function tickets()
    {
        $pageTitle = __('Support Ticket');
        $query = SupportTicket::orderBy('last_reply', 'desc')->with('user');
        $query = $this->applyFilters($query);
        $items = $this->buildConversationsFromQuery($query);
        $hasChannelColumn = Schema::hasColumn('support_tickets', 'channel');
        $totalConversations = SupportTicket::query()->when(request()->filled('subject'), function ($q) {
            $q->where('subject', request('subject'));
        })->when(request()->filled('channel') && Schema::hasColumn('support_tickets', 'channel'), function ($q) {
            $q->where('channel', request('channel'));
        })->get()->groupBy(function ($t) {
            return $t->user_id ? 'u' . $t->user_id : 'g' . $t->id;
        })->count();
        $totalMessages = SupportMessage::count();
        return view('admin.support.tickets', compact('items', 'pageTitle', 'hasChannelColumn', 'totalConversations', 'totalMessages'));
    }

    protected function buildConversationsFromQuery($query)
    {
        $tickets = $query->get();
        $grouped = $tickets->groupBy(function ($t) {
            return $t->user_id ? 'u' . $t->user_id : 'g' . $t->id;
        });
        $conversations = collect();
        foreach ($grouped as $group) {
            $first = $group->sortByDesc('last_reply')->first();
            $first->load('user');
            $conversations->push((object) [
                'user_id' => $first->user_id,
                'name' => $first->user_id ? ($first->user->fullname ?? $first->user->username ?? $first->name) : $first->name,
                'email' => $first->email ?? '',
                'last_reply' => $first->last_reply,
                'status' => $first->status,
                'status_badge' => $first->statusBadge,
                'subjects' => $group->pluck('subject')->unique()->values()->toArray(),
                'ticket_count' => $group->count(),
                'primary_ticket_id' => $first->id,
                'is_guest' => !$first->user_id,
                'channel' => $first->channel ?? null,
                'priority' => $first->priority,
            ]);
        }
        $page = request('page', 1);
        $perPage = getPaginate();
        return new \Illuminate\Pagination\LengthAwarePaginator(
            $conversations->forPage($page, $perPage)->values(),
            $conversations->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    public function pendingTicket()
    {
        $pageTitle = __('Support Ticket') . ' - ' . __('Pending');
        $query = SupportTicket::whereIn('status', [Status::TICKET_OPEN, Status::TICKET_REPLY])->orderBy('last_reply', 'desc')->with('user');
        $query = $this->applyFilters($query);
        $items = $this->buildConversationsFromQuery($query);
        $hasChannelColumn = Schema::hasColumn('support_tickets', 'channel');
        $totalConversations = $items->total();
        $totalMessages = SupportMessage::count();
        return view('admin.support.tickets', compact('items', 'pageTitle', 'hasChannelColumn', 'totalConversations', 'totalMessages'));
    }

    public function closedTicket()
    {
        $pageTitle = __('Support Ticket') . ' - ' . __('Closed');
        $query = SupportTicket::where('status', Status::TICKET_CLOSE)->orderBy('last_reply', 'desc')->with('user');
        $query = $this->applyFilters($query);
        $items = $this->buildConversationsFromQuery($query);
        $hasChannelColumn = Schema::hasColumn('support_tickets', 'channel');
        $totalConversations = $items->total();
        $totalMessages = SupportMessage::count();
        return view('admin.support.tickets', compact('items', 'pageTitle', 'hasChannelColumn', 'totalConversations', 'totalMessages'));
    }

    public function answeredTicket()
    {
        $pageTitle = __('Support Ticket') . ' - ' . __('Answered');
        $query = SupportTicket::where('status', Status::TICKET_ANSWER)->orderBy('last_reply', 'desc')->with('user');
        $query = $this->applyFilters($query);
        $items = $this->buildConversationsFromQuery($query);
        $hasChannelColumn = Schema::hasColumn('support_tickets', 'channel');
        $totalConversations = $items->total();
        $totalMessages = SupportMessage::count();
        return view('admin.support.tickets', compact('items', 'pageTitle', 'hasChannelColumn', 'totalConversations', 'totalMessages'));
    }

    public function ticketReply($id)
    {
        $ticket = SupportTicket::with('user')->where('id', $id)->firstOrFail();
        $pageTitle = 'Reply Ticket';
        $messages = SupportMessage::with('ticket', 'admin', 'attachments')
            ->where('support_ticket_id', $ticket->id)
            ->orderBy('id', 'asc')
            ->get();
        $hasChannelColumn = Schema::hasColumn('support_tickets', 'channel');
        return view('admin.support.reply', compact('ticket', 'messages', 'pageTitle', 'hasChannelColumn'));
    }

    /**
     * Messages filtered by subject – clean URLs: /messages/LiveChat, /messages/GeneralInquiry, etc.
     */
    public function ticketsBySubject()
    {
        $subjectMap = [
            'LiveChat' => 'Live Chat Message',
            'GeneralInquiry' => 'General Inquiry',
            'ReportProblem' => 'Report a Problem',
            'OrderSupport' => 'Order Support',
        ];
        $segment = request()->segment(count(request()->segments()));
        $subject = $subjectMap[$segment] ?? null;
        if (!$subject) {
            return redirect()->route('admin.ticket.index');
        }
        $pageTitle = __('Support Ticket') . ' - ' . $subject;
        $query = SupportTicket::orderBy('last_reply', 'desc')->with('user')->where('subject', $subject);
        if (Schema::hasColumn('support_tickets', 'channel')) {
            if (request()->filled('channel')) {
                $query->where('channel', request('channel'));
            }
        }
        $query = $this->applyFilters($query);
        $items = $this->buildConversationsFromQuery($query);
        $hasChannelColumn = Schema::hasColumn('support_tickets', 'channel');
        return view('admin.support.tickets', compact('items', 'pageTitle', 'hasChannelColumn'));
    }

    /**
     * Format message for JSON: app timezone, correct date/time labels.
     */
    protected function formatMessageForJson($m, $userName, $today, $yesterday)
    {
        $dt = $m->created_at->timezone(config('app.timezone', 'UTC'));
        $dateLabel = $dt->isSameDay($today) ? __('Today') : ($dt->isSameDay($yesterday) ? __('Yesterday') : $dt->format('d/m/Y'));
        return [
            'id' => $m->id,
            'message' => $m->message,
            'is_admin' => (bool) $m->admin_id,
            'name' => $m->admin_id ? ($m->admin->name ?? 'Staff') : $userName,
            'created_at' => $dt->format('g:i A'),
            'created_at_full' => $dt->format('d M Y, g:i A'),
            'date_label' => $dateLabel,
            'attachments' => $m->attachments->map(fn ($a) => route('admin.ticket.download', encrypt($a->id)))->values()->toArray(),
        ];
    }

    /**
     * JSON API for admin reply page: fetch messages for polling (instant updates).
     * Time/date in app timezone.
     */
    public function getTicketMessagesJson($id)
    {
        $ticket = SupportTicket::where('id', $id)->firstOrFail();
        $messages = SupportMessage::with('admin', 'attachments')
            ->where('support_ticket_id', $ticket->id)
            ->orderBy('id', 'asc')
            ->get();
        $tz = config('app.timezone', 'UTC');
        $today = Carbon::today($tz);
        $yesterday = Carbon::yesterday($tz);
        $list = [];
        foreach ($messages as $m) {
            $list[] = $this->formatMessageForJson($m, $ticket->name, $today, $yesterday);
        }
        return response()->json(['messages' => $list]);
    }

    protected static $allowedSubjects = ['Live Chat Message', 'General Inquiry', 'Report a Problem', 'Order Support'];

    /**
     * One token per user: all messages from all tickets of this user, one thread.
     * Optional ?subject= filter. JSON for polling on "view by user" page.
     */
    public function getTicketMessagesJsonByUser($userId)
    {
        $userId = (int) $userId;
        $query = SupportTicket::where('user_id', $userId)->orderBy('id');
        if (request()->filled('subject') && in_array(request('subject'), self::$allowedSubjects)) {
            $query->where('subject', request('subject'));
        }
        $tickets = $query->get();
        if ($tickets->isEmpty()) {
            return response()->json(['messages' => []]);
        }
        $ticketIds = $tickets->pluck('id')->toArray();
        $userName = $tickets->first()->name;
        if ($user = $tickets->first()->user) {
            $userName = $user->fullname ?? $user->username ?? $userName;
        }
        $messages = SupportMessage::with('admin', 'attachments', 'ticket')
            ->whereIn('support_ticket_id', $ticketIds)
            ->orderBy('id', 'asc')
            ->get();
        $tz = config('app.timezone', 'UTC');
        $today = Carbon::today($tz);
        $yesterday = Carbon::yesterday($tz);
        $list = [];
        foreach ($messages as $m) {
            $name = $m->admin_id ? ($m->admin->name ?? 'Staff') : ($m->ticket->name ?? $userName);
            $list[] = $this->formatMessageForJson($m, $name, $today, $yesterday);
        }
        return response()->json(['messages' => $list]);
    }

    /**
     * Reply page by user: one token per user, all messages in one thread. Optional subject filter.
     * Reply goes to primary ticket. Subject tabs: Live Chat Message, General Inquiry, Report a Problem, Order Support.
     */
    public function ticketReplyByUser($userId)
    {
        $userId = (int) $userId;
        $query = SupportTicket::where('user_id', $userId)->with('user')->orderBy('last_reply', 'desc');
        $subjectFilter = request('subject');
        if ($subjectFilter && in_array($subjectFilter, self::$allowedSubjects)) {
            $query->where('subject', $subjectFilter);
        }
        $tickets = $query->get();
        if ($tickets->isEmpty()) {
            abort(404);
        }
        $primary = $tickets->first();
        $messages = SupportMessage::with('ticket', 'admin', 'attachments')
            ->whereIn('support_ticket_id', $tickets->pluck('id'))
            ->orderBy('id', 'asc')
            ->get();
        $pageTitle = __('Conversation') . ' – ' . ($primary->user->fullname ?? $primary->user->username ?? $primary->name);
        $hasChannelColumn = Schema::hasColumn('support_tickets', 'channel');
        $byUser = true;
        $ticket = $primary;
        return view('admin.support.reply', compact('ticket', 'messages', 'pageTitle', 'hasChannelColumn', 'byUser', 'subjectFilter'));
    }

    public function ticketDelete($id)
    {
        $message = SupportMessage::findOrFail($id);
        $path = getFilePath('ticket');
        if ($message->attachments()->count() > 0) {
            foreach ($message->attachments as $attachment) {
                fileManager()->removeFile($path.'/'.$attachment->attachment);
                $attachment->delete();
            }
        }
        $message->delete();
        $notify[] = ['success', __('Message deleted successfully.')];
        return back()->withNotify($notify);
    }

    /**
     * Bulk delete messages (50, 100, 200, 300, 400, 500, 1000 or selected IDs).
     * When user_id is provided (view by user), delete_last applies across all that user's tickets.
     */
    public function bulkDeleteMessages(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required_without:user_id|nullable|exists:support_tickets,id',
            'user_id' => 'nullable|integer|exists:users,id',
            'message_ids' => 'nullable|array',
            'message_ids.*' => 'integer|exists:support_messages,id',
            'delete_last' => 'nullable|integer|in:20,50,100,200,300,400,500,1000',
        ]);
        $path = getFilePath('ticket');
        $ids = [];
        $ticketId = $request->filled('ticket_id') ? (int) $request->ticket_id : null;
        $userId = $request->filled('user_id') ? (int) $request->user_id : null;

        if ($request->filled('delete_last')) {
            $limit = (int) $request->delete_last;
            if ($userId) {
                $ticketIds = SupportTicket::where('user_id', $userId)->pluck('id')->toArray();
                $ids = SupportMessage::whereIn('support_ticket_id', $ticketIds)->orderBy('id', 'desc')->limit($limit)->pluck('id')->toArray();
            } elseif ($ticketId) {
                $ids = SupportMessage::where('support_ticket_id', $ticketId)->orderBy('id', 'desc')->limit($limit)->pluck('id')->toArray();
            }
        } elseif ($request->filled('message_ids')) {
            $ids = array_map('intval', $request->message_ids);
            if ($userId) {
                $ticketIds = SupportTicket::where('user_id', $userId)->pluck('id')->toArray();
                $ids = SupportMessage::whereIn('support_ticket_id', $ticketIds)->whereIn('id', $ids)->pluck('id')->toArray();
            } elseif ($ticketId) {
                $ids = SupportMessage::where('support_ticket_id', $ticketId)->whereIn('id', $ids)->pluck('id')->toArray();
            } else {
                $ids = SupportMessage::whereIn('id', $ids)->pluck('id')->toArray();
            }
        }

        if (empty($ids)) {
            $notify[] = ['error', __('No messages selected.')];
            return back()->withNotify($notify);
        }
        $deleted = 0;
        foreach (SupportMessage::whereIn('id', $ids)->with('attachments')->get() as $message) {
            foreach ($message->attachments as $attachment) {
                fileManager()->removeFile($path.'/'.$attachment->attachment);
                $attachment->delete();
            }
            $message->delete();
            $deleted++;
        }
        $notify[] = ['success', __(':count message(s) deleted.', ['count' => $deleted])];
        return back()->withNotify($notify);
    }

    /**
     * Bulk delete conversations (selected user rows from message center list).
     */
    public function bulkDeleteConversations(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'integer|exists:users,id',
        ]);
        $userIds = array_unique(array_map('intval', $request->user_ids));
        $path = getFilePath('ticket');
        $tickets = SupportTicket::whereIn('user_id', $userIds)->get();
        $deletedTickets = 0;
        $deletedMessages = 0;
        foreach ($tickets as $ticket) {
            foreach (SupportMessage::where('support_ticket_id', $ticket->id)->with('attachments')->get() as $message) {
                foreach ($message->attachments as $attachment) {
                    fileManager()->removeFile($path.'/'.$attachment->attachment);
                    $attachment->delete();
                }
                $message->delete();
                $deletedMessages++;
            }
            $ticket->delete();
            $deletedTickets++;
        }
        $notify[] = ['success', __(':count conversation(s) and :msg message(s) deleted.', ['count' => $deletedTickets, 'msg' => $deletedMessages])];
        return back()->withNotify($notify);
    }

    /**
     * Delete last N messages from entire system (message center list page).
     * Options: 20, 50, 100, 200, 300, 400, 500, 1000.
     */
    public function bulkDeleteMessagesGlobal(Request $request)
    {
        $request->validate([
            'delete_last' => 'required|integer|in:20,50,100,200,300,400,500,1000',
        ]);
        $limit = (int) $request->delete_last;
        $path = getFilePath('ticket');
        $ids = SupportMessage::orderBy('id', 'desc')->limit($limit)->pluck('id')->toArray();
        $deleted = 0;
        foreach (SupportMessage::whereIn('id', $ids)->with('attachments')->get() as $message) {
            foreach ($message->attachments as $attachment) {
                fileManager()->removeFile($path.'/'.$attachment->attachment);
                $attachment->delete();
            }
            $message->delete();
            $deleted++;
        }
        $notify[] = ['success', __(':count message(s) deleted.', ['count' => $deleted])];
        return back()->withNotify($notify);
    }

}
