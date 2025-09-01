<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ticket;

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Ticket::factory()->count(30)->create();

        // A few with predefined categories & notes
        Ticket::factory()->create([
            'subject' => 'Billing question: refund policy',
            'body' => 'Customer asked about refund on annual plan due to double charge.',
            'status' => 'open',
            'category' => 'billing',
            'category_overridden' => true,
            'note' => 'Handled previously by AR team.',
        ]);

        Ticket::factory()->create([
            'subject' => 'Bug report: CSV export encoding',
            'body' => 'Diacritics are garbled in exported CSV. Please investigate.',
            'status' => 'in_progress',
            'note' => 'Likely UTF-8 vs ISO-8859-1',
        ]);
    }
}
