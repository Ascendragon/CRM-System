<?php

namespace Tests\Feature;

use App\Domain\Ticket\Enums\TicketPriority;
use App\Domain\Ticket\Enums\TicketStatus;
use App\Models\Client;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListTicketsApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_authorized_user_can_get_paginated_tickets_list(): void
    {
        $user = User::factory()->create();

        Ticket::factory()
            ->count(3)
            ->create();

        $response = $this
            ->actingAs($user)
            ->getJson('/api/tickets');

        $response->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'client_id',
                        'created_by',
                        'assigned_to',
                        'client',
                        'creator',
                        'assignee',
                        'title',
                        'description',
                        'status',
                        'priority',
                        'closed_at',
                        'created_at',
                        'updated_at',
                    ],
                ],
                'links',
                'meta',
            ]);

    }

    public function test_guest_cannot_get_tickets_list(): void
    {
        $response = $this->getJson('/api/tickets');

        $response->assertUnauthorized();
    }

    public function test_tickets_can_be_filtered_by_status(): void
    {
        $user = User::factory()->create();

        Ticket::factory()->create([
            'status' => TicketStatus::New,
        ]);

        Ticket::factory()->create([
            'status' => TicketStatus::Resolved,
        ]);

        $response = $this
            ->actingAs($user)
            ->getJson('/api/tickets?status='.TicketStatus::Resolved->value);

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.status', TicketStatus::Resolved->value);
    }

    public function test_tickets_can_be_filtered_by_priority(): void
    {
        $user = User::factory()->create();

        Ticket::factory()->create([
            'priority' => TicketPriority::Low,
        ]);

        Ticket::factory()->create([
            'priority' => TicketPriority::Urgent,
        ]);

        $response = $this
            ->actingAs($user)
            ->getJson('/api/tickets?priority='.TicketPriority::Urgent->value);

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.priority', TicketPriority::Urgent->value);
    }

    public function test_tickets_can_be_filtered_by_client(): void
    {
        $user = User::factory()->create();

        $targetClient = Client::factory()->create();
        $otherClient = Client::factory()->create();

        Ticket::factory()->create([
            'client_id' => $targetClient->id,
        ]);

        Ticket::factory()->create([
            'client_id' => $otherClient->id,
        ]);

        $response = $this
            ->actingAs($user)
            ->getJson('/api/tickets?client_id='.$targetClient->id);

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.client_id', $targetClient->id);
    }

    public function test_tickets_can_be_filtered_by_assignee(): void
    {
        $user = User::factory()->create();

        $assignee = User::factory()->create();
        $otherAssignee = User::factory()->create();

        $targetTicket = Ticket::factory()->create([
            'assigned_to' => $assignee->getKey(),
        ]);

        Ticket::factory()->create([
            'assigned_to' => $otherAssignee->getKey(),
        ]);

        // Проверяем подготовку тестовых данных.
        $this->assertDatabaseHas('tickets', [
            'id' => $targetTicket->getKey(),
            'assigned_to' => $assignee->getKey(),
        ]);

        // Проверяем сам запрос без HTTP-слоя.
        $this->assertSame(
            1,
            Ticket::query()
                ->where('assigned_to', $assignee->getKey())
                ->count()
        );

        $response = $this
            ->actingAs($user)
            ->getJson('/api/tickets?assigned_to='.$assignee->getKey());

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $targetTicket->getKey())
            ->assertJsonPath('data.0.assigned_to', $assignee->getKey());
    }

    public function test_invalid_status_filter_is_rejected(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->getJson('/api/tickets?status=banana');

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['status']);
    }

    public function test_per_page_has_upper_limit(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->getJson('/api/tickets?per_page=1000');

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['per_page']);
    }
}
