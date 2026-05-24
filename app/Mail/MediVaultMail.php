<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MediVaultMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $viewName;
    public $subjectLine;
    public $mailData;

    /**
     * Create a new message instance.
     */
    public function __construct(string $viewName, string $subjectLine, array $mailData)
    {
        $this->viewName = $viewName;
        $this->subjectLine = $subjectLine;
        $this->mailData = $mailData;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subjectLine,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: $this->viewName,
            with: $this->mailData,
        );
    }
}
