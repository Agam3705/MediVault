<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Message;
use App\Models\AccessGrant;
use App\Models\AccessRequest;
use Carbon\Carbon;

class ChatController extends Controller
{
    /**
     * Authorize chat access between current user and target user.
     */
    protected function authorizeChat(User $targetUser)
    {
        $authUser = Auth::user();

        if ($authUser->id === $targetUser->id) {
            abort(403, 'You cannot chat with yourself.');
        }

        if ($authUser->role === 'Patient' && $targetUser->role === 'Doctor') {
            $patient = $authUser->patient;
            $doctor = $targetUser->doctor;

            if (!$patient || !$doctor) {
                abort(403, 'Invalid profiles.');
            }

            // Patients are allowed to initiate chats with any verified doctor
            if (!$doctor->isVerified()) {
                abort(403, 'You can only chat with verified doctors.');
            }

            return ['patient' => $patient, 'doctor' => $doctor];

        } elseif ($authUser->role === 'Doctor' && $targetUser->role === 'Patient') {
            $doctor = $authUser->doctor;
            $patient = $targetUser->patient;

            if (!$doctor || !$patient) {
                abort(403, 'Invalid profiles.');
            }

            // Check active grant, access request, or if there are existing messages (patient initiated)
            $hasGrant = AccessGrant::where('doctor_id', $doctor->id)
                ->where('patient_id', $patient->id)
                ->where('is_active', true)
                ->where(function($q) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>', Carbon::now());
                })->exists();

            $hasRequest = AccessRequest::where('doctor_id', $doctor->id)
                ->where('patient_id', $patient->id)
                ->exists();

            $hasMessages = Message::where(function($q) use ($authUser, $targetUser) {
                $q->where('sender_id', $authUser->id)->where('receiver_id', $targetUser->id);
            })->orWhere(function($q) use ($authUser, $targetUser) {
                $q->where('sender_id', $targetUser->id)->where('receiver_id', $authUser->id);
            })->exists();

            if (!$hasGrant && !$hasRequest && !$hasMessages) {
                abort(403, 'You do not have clinical contact with this patient.');
            }

            return ['patient' => $patient, 'doctor' => $doctor];
        }

        abort(403, 'Unauthorized chat connection.');
    }

    /**
     * Show the chat view.
     */
    public function show(User $user)
    {
        $this->authorizeChat($user);

        return view('chat.show', [
            'targetUser' => $user,
        ]);
    }

    /**
     * API: Fetch messages history and mark unread as read.
     */
    public function fetchMessages(User $user)
    {
        $this->authorizeChat($user);
        $authId = Auth::id();
        $targetId = $user->id;

        // Fetch messages between users
        $messages = Message::where(function($q) use ($authId, $targetId) {
            $q->where('sender_id', $authId)->where('receiver_id', $targetId);
        })->orWhere(function($q) use ($authId, $targetId) {
            $q->where('sender_id', $targetId)->where('receiver_id', $authId);
        })->orderBy('created_at', 'asc')->get();

        // Mark incoming messages as read
        Message::where('sender_id', $targetId)
            ->where('receiver_id', $authId)
            ->whereNull('read_at')
            ->update(['read_at' => Carbon::now()]);

        $formatted = $messages->map(function($msg) use ($authId) {
            return [
                'id' => $msg->id,
                'sender_id' => $msg->sender_id,
                'is_me' => $msg->sender_id === $authId,
                'message' => $msg->message,
                'file_path' => $msg->file_path ? (str_starts_with($msg->file_path, 'http') ? $msg->file_path : asset('storage/' . $msg->file_path)) : null,
                'file_name' => $msg->file_name,
                'file_type' => $msg->file_type,
                'time' => $msg->created_at ? $msg->created_at->format('h:i A') : '',
            ];
        });

        return response()->json($formatted);
    }

    /**
     * API: Send a new message.
     */
    public function sendMessage(Request $request, User $user)
    {
        $this->authorizeChat($user);

        $request->validate([
            'message' => ['nullable', 'string', 'max:2000'],
            'file'    => ['nullable', 'file', 'max:10240'], // 10MB limit
        ]);

        if (!$request->message && !$request->hasFile('file')) {
            return response()->json(['success' => false, 'error' => 'Cannot send empty message.'], 422);
        }

        $filePath = null;
        $fileName = null;
        $fileType = null;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = $file->getClientOriginalName();
            $fileType = $file->getMimeType();
            
            try {
                $uploaded = cloudinary()->upload($file->getRealPath(), [
                    'folder' => 'medivault/chats',
                    'resource_type' => 'auto',
                ]);
                $filePath = $uploaded->getSecurePath();
            } catch (\Exception $e) {
                // Fallback: store locally in public disk
                $storedPath = $file->store('chats', 'public');
                $filePath   = $storedPath;
            }
        }

        $msg = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $user->id,
            'message' => $request->message,
            'file_path' => $filePath,
            'file_name' => $fileName,
            'file_type' => $fileType,
        ]);

        \App\Models\Notification::create([
            'user_id' => $user->id,
            'title' => '💬 New Message',
            'message' => $request->hasFile('file')
                ? Auth::user()->name . ' shared a file: ' . $fileName
                : 'New message from ' . Auth::user()->name . ': ' . \Illuminate\Support\Str::limit($request->message, 50),
            'type' => 'info',
        ]);

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $msg->id,
                'sender_id' => $msg->sender_id,
                'is_me' => true,
                'message' => $msg->message,
                'file_path' => $msg->file_path ? (str_starts_with($msg->file_path, 'http') ? $msg->file_path : asset('storage/' . $msg->file_path)) : null,
                'file_name' => $msg->file_name,
                'file_type' => $msg->file_type,
                'time' => $msg->created_at ? $msg->created_at->format('h:i A') : now()->format('h:i A'),
            ]
        ]);
    }
}
