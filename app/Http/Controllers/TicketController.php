<?php

namespace App\Http\Controllers;

use App\Http\Requests\TicketStoreRequest;
use App\Http\Requests\TicketUpdateRequest;
use App\Jobs\ClassifyTicket;
use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $q = Ticket::query();

        // Filters: status, category, text search
        if ($status = $request->query('status'))
            $q->where('status', $status);
        
        if ($category = $request->query('category'))
            $q->where('category', $category);
        
        if ($search = $request->query('search')) {
            $q->where(function ($w) use ($search) {
                $w->where('subject', 'like', "%{$search}%")
                    ->orWhere('body', 'like', "%{$search}%");
            });
        }

        $perPage = (int) $request->query('per_page', 10);
        $tickets = $q->orderByDesc('created_at')->paginate($perPage);

        return response()->json($tickets);
    }

    public function store(TicketStoreRequest $request)
    {
        $ticket = Ticket::create($request->validated());
        return response()->json($ticket, 201);
    }

    public function show(string $id)
    {
        $ticket = Ticket::findOrFail($id);
        return response()->json($ticket);
    }

    public function update(TicketUpdateRequest $request, string $id)
    {
        $ticket = Ticket::findOrFail($id);
        $data = $request->validated();

    if (array_key_exists('category', $data)) 
        $ticket->category_overridden = true; // preserve manual override

        $ticket->fill($data)->save();
        return response()->json($ticket);
    }

    public function classify(string $id)
    {
        $ticket = Ticket::findOrFail($id);
        ClassifyTicket::dispatch($ticket->id);
        return response()->json(['queued' => true]);
    }
}