<?php

namespace Database\Factories;

use App\Domain\Ticket\Enums\TicketPriority;
use App\Domain\Ticket\Enums\TicketStatus;
use App\Models\Client;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ticket>
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
        return [
            'client_id' => Client::factory(),
            'created_by' => User::factory(),
            'assigned_to' => null,
            'title' => fake()->sentence(6),
            'description' => fake()->paragraph(),
            'status' => TicketStatus::New,
            'priority' => TicketPriority::Normal,
            'closed_at' => null,
        ];
    }
}
