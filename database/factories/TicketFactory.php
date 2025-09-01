<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subjects = ['Login issue', 'Payment failed', 'Feature request: Dark Mode', 
            'App crash on startup', 'Email not received', 'Slow performance', 'Bug: 500 on dashboard', 
            'Cannot reset password', 'Export to CSV broken',
        ];

        $status = collect(['open','in_progress','resolved','closed'])->random();
        
        return [
            'subject' => $this->faker->randomElement($subjects),
            'body' => $this->faker->paragraphs(3, true),
            'status' => $status,
            'category' => null,
            'category_overridden' => false,
            'note' => $this->faker->boolean(30) ? $this->faker->sentence() : null,
            'explanation' => null,
            'confidence' => null,
        ];
    }
}