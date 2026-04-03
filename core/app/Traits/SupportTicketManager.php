<?php

namespace App\Traits;

use App\Constants\Status;
use App\Models\AdminNotification;
use App\Models\NotificationLog;
use App\Models\SupportAttachment;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use Carbon\Carbon;
use Illuminate\Http\Request;

trait SupportTicketManager
{
    protected $files;
    protected $allowedExtension = ['jpg', 'png', 'jpeg', 'pdf', 'doc', 'docx'];
    protected $userType;
    protected $user = null;
    protected $column;

    public function supportTicket()
    {
        $user = $this->user;
        if (!$user) {
            abort(404);
        }
        $pageTitle = "Messages";
        $query = SupportTicket::where($this->column, $user->id);
        if ($this->userType === 'user') {
            $query->where('created_at', '>=', Carbon::now()->subDays(30));
        }
        $supports = $query->with(['supportMessage' => fn ($q) => $q->orderBy('id', 'desc')->limit(1)])
            ->orderBy('id', 'desc')
            ->paginate(max(10, min((int) getPaginate(), 20)));
        $emptyMessage = __('No messages yet. Messages are automatically removed after 30 days.');
        return view($this->activeTemplate . $this->userType . '.support.index', compact('supports', 'pageTitle', 'emptyMessage'));
    }

    public function openSupportTicket()
    {
        $user = $this->user;

        if (!$user) {
            return to_route('home');
        }
        $pageTitle = "Open Ticket";
        return view($this->activeTemplate . $this->userType . '.support.create', compact('pageTitle', 'user'));
    }

    public function storeSupportTicket(Request $request)
    {
        $ticket  = new SupportTicket();
        $message = new SupportMessage();

        $this->validation($request);

        $column             = $this->column;
        $user               = $this->user;
        $ticket->$column    = $user->id;
        $ticket->ticket     = rand(100000, 999999);
        $ticket->name       = $request->name;
        $ticket->email      = $request->email;
        $ticket->subject    = $request->subject;
        $ticket->last_reply = Carbon::now();
        $ticket->status     = Status::TICKET_OPEN;
        $ticket->priority   = $request->priority;
        $ticket->save();


        $message->support_ticket_id   = $ticket->id;
        $message->message             = $request->message;
        $message->save();

        $adminNotification            = new AdminNotification();
        $adminNotification->$column   = $user->id;
        $adminNotification->title     = 'New message from ' . $user->username . ': ' . \Illuminate\Support\Str::limit(strip_tags($request->message ?? ''), 60);
        $adminNotification->click_url = urlPath('admin.users.notification.single', $user->id);
        $adminNotification->save();

        if ($request->hasFile('attachments')) {
            $uploadAttachments = $this->storeSupportAttachments($message->id);
            if ($uploadAttachments != 200) return back()->withNotify($uploadAttachments);;
        }

        $notify[] = ['success', 'Ticket opened successfully!'];


        return to_route($this->redirectLink, $ticket->ticket)->withNotify($notify);
    }

    public function viewTicket($ticket)
    {
        $user      = $this->user;
        $column    = $this->column;
        $pageTitle = "View Ticket";
        $userId    = 0;
        $layout    = $this->layout;

        $myTicket = SupportTicket::where('ticket', $ticket)->orderBy('id', 'desc')->firstOrFail();

        if ($myTicket->$column > 0) {
            if ($user) {
                $userId = $user->id;
            } else {
                return to_route($this->userType . '.login');
            }
        }

        $myTicket = SupportTicket::where('ticket', $ticket)->where($this->column, $userId)->orderBy('id', 'desc')->firstOrFail();
        if ($this->userType === 'user' && $myTicket->created_at < Carbon::now()->subDays(30)) {
            abort(404, 'This ticket has expired (messages older than 30 days are no longer visible).');
        }
        $messages = SupportMessage::where('support_ticket_id', $myTicket->id)->with('ticket', 'admin', 'attachments')->orderBy('id', 'desc')->get();

        return view($this->activeTemplate . $this->userType . '.support.view', compact('myTicket', 'messages', 'pageTitle', 'user', 'layout'));
    }


    public function replyTicket(Request $request, $id)
    {
        $user = $this->user;
        $userId = 0;
        if ($user) {
            $userId = $user->id;
        }
        $ticket = SupportTicket::where('id', $id)->firstOrFail();
        if (($this->userType == 'user') && ($userId != $ticket->user_id)) {
            abort(404);
        }
        $message = new SupportMessage();

        $request->merge(['ticket_reply' => 1]);

        $this->validation($request);

        $ticket->status = $this->userType != 'admin' ? Status::TICKET_REPLY : Status::TICKET_ANSWER;
        $ticket->last_reply = Carbon::now();
        $ticket->save();
        $message->support_ticket_id = $ticket->id;
        if ($this->userType == 'admin') {
            $message->admin_id = $user->id;
        }

        $message->message = $request->message;
        $message->save();

        if ($request->hasFile('attachments')) {
            $uploadAttachments = $this->storeSupportAttachments($message->id);
            if ($uploadAttachments != 200) {
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json(['success' => false, 'message' => __('File could not upload.')], 422);
                }
                return back()->withNotify($uploadAttachments);
            }
        }

        if ($this->userType == 'user') {
            $ticket->load('user');
            if ($ticket->user_id && $ticket->user) {
                $adminNotif = new AdminNotification();
                $adminNotif->user_id   = $ticket->user_id;
                $adminNotif->title    = __('User replied') . ' (' . $ticket->user->username . '): ' . \Illuminate\Support\Str::limit(strip_tags($request->message ?? ''), 50);
                $adminNotif->click_url = urlPath('admin.users.notification.single', $ticket->user_id);
                $adminNotif->save();
            }
        }

        if ($this->userType == 'admin') {
            $createLog = false;
            $user = $ticket;
            if ($ticket->user_id != 0) {
                $createLog = true;
                $user = $ticket->user;
            }

            notify($user, 'ADMIN_SUPPORT_REPLY', [
                'ticket_id' => $ticket->ticket,
                'ticket_subject' => $ticket->subject,
                'reply' => $request->message,
                'link' => route('message.view', $ticket->ticket),
            ], null, $createLog);

            // Ensure user sees in-app notification even if email template is missing
            if ($ticket->user_id && \Illuminate\Support\Facades\Schema::hasTable('notification_logs')) {
                try {
                    NotificationLog::create([
                        'user_id' => $ticket->user_id,
                        'notification_type' => 'email',
                        'sender' => gs()->site_name ?? 'Admin',
                        'subject' => __('Admin replied to your message'),
                        'message' => \Illuminate\Support\Str::limit($request->message, 200),
                        'click_url' => route('message.view', $ticket->ticket),
                    ]);
                } catch (\Throwable $e) {
                    // ignore
                }
            }
        }


        $notify[] = ['success', 'Support ticket replied successfully!'];

        if ($request->wantsJson() || $request->ajax()) {
            $message->load(['admin', 'attachments']);
            $tz = config('app.timezone', 'UTC');
            $dt = $message->created_at->timezone($tz);
            $dateLabel = $dt->isToday() ? __('Today') : ($dt->isYesterday() ? __('Yesterday') : $dt->format('d/m/Y'));
            $attachments = $message->attachments->map(fn ($a) => route('admin.ticket.download', encrypt($a->id)))->toArray();
            return response()->json([
                'success' => true,
                'message' => [
                    'id' => $message->id,
                    'message' => $message->message,
                    'is_admin' => (bool) $message->admin_id,
                    'name' => $message->admin_id ? ($message->admin->name ?? 'Staff') : $ticket->name,
                    'created_at' => $dt->format('g:i A'),
                    'created_at_full' => $dt->format('d M Y, g:i A'),
                    'date_label' => $dateLabel,
                    'attachments' => array_values($attachments),
                ],
            ]);
        }

        if ($request->filled('redirect_to')) {
            $url = $request->redirect_to;
            if (filter_var($url, FILTER_VALIDATE_URL) || str_starts_with((string) $url, '/')) {
                return redirect($url)->withNotify($notify);
            }
        }
        return back()->withNotify($notify);
    }

    protected function storeSupportAttachments($messageId)
    {
        $path = getFilePath('ticket');

        foreach ($this->files as  $file) {
            try {
                $attachment = new SupportAttachment();
                $attachment->support_message_id = $messageId;
                $attachment->attachment = fileUploader($file, $path);
                $attachment->save();
            } catch (\Exception $exp) {
                $notify[] = ['error', 'File could not upload'];
                return $notify;
            }
        }

        return 200;
    }

    protected function validation($request)
    {
        $maxSize = substr(ini_get('upload_max_filesize'), 0, -1);

        $this->maxSize = $maxSize;
        $this->files = $request->file('attachments');

        $request->validate([
            'attachments' => [
                'max:4096',
                function ($attribute, $value, $fail) {
                    $mimeMap = [
                        'png'  => ['image/png'],
                        'jpg'  => ['image/jpeg', 'image/jpg'],
                        'jpeg' => ['image/jpeg', 'image/jpg'],
                        'pdf'  => ['application/pdf'],
                        'doc'  => ['application/msword'],
                        'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
                    ];
                    foreach ($this->files as $file) {
                        $ext = strtolower($file->getClientOriginalExtension());
                        if (($file->getSize() / 1000000) > $this->maxSize) {
                            return $fail("Maximum $this->maxSize MB file size allowed!");
                        }
                        if (!in_array($ext, $this->allowedExtension)) {
                            return $fail("Only png, jpg, jpeg, pdf, doc, docx files are allowed");
                        }
                        if (isset($mimeMap[$ext])) {
                            $finfo = finfo_open(FILEINFO_MIME_TYPE);
                            $mime = $finfo ? finfo_file($finfo, $file->getRealPath()) : '';
                            if ($finfo) {
                                finfo_close($finfo);
                            }
                            if ($mime && !in_array($mime, $mimeMap[$ext])) {
                                return $fail("File MIME type does not match extension for {$ext}");
                            }
                        }
                    }
                    if (count($this->files) > 5) {
                        return $fail("Maximum 5 files can be uploaded");
                    }
                },
            ],
            'name'      => 'required_without:ticket_reply',
            'email'     => 'required_without:ticket_reply|email|max:255',
            'subject'   => 'required_without:ticket_reply|max:255',
            'priority'  => 'required_without:ticket_reply|in:1,2,3',
            'message'   => 'required',
        ]);
    }

    public function closeTicket($id)
    {
        $user = $this->user;
        $ticket = SupportTicket::where('id', $id)->firstOrFail();
        if ($this->userType != 'admin') {
            $column = $this->column;
            if ($user->id != $ticket->$column) {
                abort(403);
            }
        }

        $ticket->status = Status::TICKET_CLOSE;
        $ticket->save();
        $notify[] = ['success', 'Support ticket closed successfully!'];
        return back()->withNotify($notify);
    }

    public function ticketDownload($ticket_id)
    {
        try {
            $attachmentId = decrypt($ticket_id);
        } catch (\Throwable $e) {
            abort(404, 'Invalid attachment');
        }

        $attachment = SupportAttachment::with('supportMessage.ticket')->find($attachmentId);
        if (!$attachment) {
            abort(404, 'Attachment not found');
        }

        $ticket = $attachment->supportMessage->ticket ?? null;
        if (!$ticket) {
            abort(404, 'Ticket not found');
        }

        if ($this->userType === 'user' && $this->user) {
            $column = $this->column ?? 'user_id';
            if ((int) $ticket->$column !== (int) $this->user->id) {
                abort(403, 'Unauthorized');
            }
        }

        $file = $attachment->attachment;
        if (empty($file) || str_contains($file, '..') || str_contains($file, "\0")) {
            abort(400, 'Invalid file path');
        }

        $ticketPath = getFilePath('ticket');
        $basePath = realpath(public_path($ticketPath)) ?: realpath(base_path($ticketPath)) ?: realpath(dirname(base_path()) . '/' . $ticketPath);
        if (!$basePath || !is_dir($basePath)) {
            abort(500, 'Storage path not found');
        }
        $full_path = realpath($basePath . DIRECTORY_SEPARATOR . $file);
        if (!$full_path || !is_file($full_path) || !str_starts_with($full_path, $basePath)) {
            abort(404, 'File not found');
        }

        $title = slug($ticket->subject ?? 'attachment');
        $ext = pathinfo($file, PATHINFO_EXTENSION) ?: 'bin';
        $mimetype = mime_content_type($full_path) ?: 'application/octet-stream';
        $inline = request()->boolean('inline');
        $headers = ['Content-Type' => $mimetype];
        if ($inline && str_starts_with($mimetype, 'image/')) {
            $headers['Content-Disposition'] = 'inline; filename="' . $title . '.' . $ext . '"';
            return response()->file($full_path, $headers);
        }
        return response()->download($full_path, $title . '.' . $ext, $headers);
    }
}
