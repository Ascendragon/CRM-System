<?php

namespace Tests\Feature;

use App\Domain\Ticket\Enums\TicketPriority;
use App\Domain\Ticket\Enums\TicketStatus;
use App\Models\Client;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketPersistenceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_ticket_can_be_created_with_client_and_creator(): void
    {
        $client = Client::factory()->create();
        $creator = User::factory()->create();

        $ticket = Ticket::factory()->create([
            'client_id' => $client->id,
            'created_by' => $creator->id,
            'status' => TicketStatus::New,
            'priority' => TicketPriority::Normal,
        ]);

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'client_id' => $client->id,
            'created_by' => $creator->id,
            'status' => TicketStatus::New->value,
            'priority' => TicketPriority::Normal->value,
        ]);
        $this->assertSame(TicketStatus::New, $ticket->getStatus());
        $this->assertSame(TicketPriority::Normal, $ticket->getPriority());
    }
}
