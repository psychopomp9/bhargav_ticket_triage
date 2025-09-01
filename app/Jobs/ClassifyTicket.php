<?php

namespace App\Jobs;

use App\Models\Ticket;
use App\Services\TicketClassifier;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ClassifyTicket implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public string $ticketId)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(TicketClassifier $classifier): void
    {
        $ticket = Ticket::find($this->ticketId);
        if (!$ticket) return;

        $result = $classifier->classify($ticket->subject, $ticket->body);

        // Preserve manual category if overridden
        if (!$ticket->category_overridden) {
            $ticket->category = $result['category'] ?? $ticket->category;
        }

        $ticket->explanation = $result['explanation'] ?? null;
        $ticket->confidence = isset($result['confidence']) ? (float)$result['confidence'] : null;
        $ticket->save();
    }
}
