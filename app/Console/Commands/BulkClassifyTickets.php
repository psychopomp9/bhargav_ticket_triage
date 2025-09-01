<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Ticket;
use App\Jobs\ClassifyTicket;

class BulkClassifyTickets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tickets:bulk-classify {--limit=100}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch classification jobs for tickets missing explanation/confidence';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $limit = (int)$this->option('limit');
        $tickets = Ticket::whereNull('explanation')->orWhereNull('confidence')
            ->orderBy('created_at')->limit($limit)->get();

        foreach ($tickets as $t) {
            ClassifyTicket::dispatch($t->id);
        }

        $this->info("Dispatched jobs for {$tickets->count()} tickets.");
        return self::SUCCESS;
    }
}