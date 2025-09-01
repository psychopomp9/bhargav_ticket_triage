<?php

namespace App\Http\Controllers;

use App\Models\Ticket;

class StatsController extends Controller
{
    public function index()
    {
        $perStatus = Ticket::selectRaw('status, COUNT(*) as c')
            ->groupBy('status')->pluck('c', 'status');

        $perCategory = Ticket::selectRaw('COALESCE(category, "uncategorized") as cat, COUNT(*) as c')
            ->groupBy('cat')->pluck('c', 'cat');

        return response()->json([
            'perStatus' => $perStatus,
            'perCategory' => $perCategory,
            'total' => Ticket::count()
        ]);
    }
}