<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SupportTicket;
use App\Models\Notification;
use Carbon\Carbon;

class SupportTicketController extends Controller
{
    /**
     * Show support request form and user's ticket list.
     */
    public function create()
    {
        $user = Auth::user();
        
        $tickets = SupportTicket::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('support.create', [
            'tickets' => $tickets
        ]);
    }

    /**
     * Store a new support ticket.
     */
    public function store(Request $request)
    {
        $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
        ]);

        SupportTicket::create([
            'user_id' => Auth::id(),
            'subject' => $request->subject,
            'message' => $request->message,
            'status'  => 'pending',
        ]);

        return redirect()->back()->with('success', 'Your support ticket has been submitted successfully to the administrator.');
    }

    /**
     * Reply/Resolve a support ticket (Admin only).
     */
    public function reply(Request $request, $ticketId)
    {
        if (Auth::user()->role !== 'Admin') {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'reply' => ['required', 'string', 'min:5', 'max:5000'],
        ]);

        $ticket = SupportTicket::findOrFail($ticketId);
        $ticket->update([
            'reply'      => $request->reply,
            'status'     => 'resolved',
            'replied_at' => Carbon::now(),
        ]);

        // Send In-App notification
        Notification::create([
            'user_id' => $ticket->user_id,
            'title'   => '🎫 Support Ticket Replied',
            'message' => 'Admin has responded to your ticket: "' . $ticket->subject . '"',
            'type'    => 'success',
        ]);

        return redirect()->back()->with('success', 'Support ticket has been successfully resolved and replied.');
    }
}
