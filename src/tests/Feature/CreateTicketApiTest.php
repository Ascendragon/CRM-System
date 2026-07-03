<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Domain\Ticket\Enums\TicketPriority;
use App\Domain\Ticket\Enums\TicketStatus;
use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class CreateTicketApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_authorized_user_can_create_ticket(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create();

        $response = $this
            ->actingAs($user)
            ->postJson('/api/tickets', [
                'client_id' => $client->id,
                'title' => 'Cannot access B2B dashboard',
                'description' => 'Client reports 500 error on dashboard page.',
                'priority' => TicketPriority::High->value,
            ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.client_id', $client->id)
            ->assertJsonPath('data.created_by', $user->id)
            ->assertJsonPath('data.title', 'Cannot access B2B dashboard')
            ->assertJsonPath('data.status', TicketStatus::New->value)
            ->assertJsonPath('data.priority', TicketPriority::High->value);

        $this->assertDatabaseHas('tickets', [
            'client_id' => $client->id,
            'created_by' => $user->id,
            'title' => 'Cannot access B2B dashboard',
            'status' => TicketStatus::New->value,
            'priority' => TicketPriority::High->value,
        ]);
    }

    public function test_guest_cannot_create_ticket(): void
    {
        $client = Client::factory()->create();

        $response = $this->postJson('/api/tickets', [
            'client_id' => $client->id,
            'title' => 'Cannot access B2B dashboard',
        ]);

        $response->assertUnauthorized();
    }

    public function test_client_id_is_required(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->postJson('/api/tickets', [
                'title' => 'Cannot access B2B dashboard',
            ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['client_id']);
    }

    public function test_title_is_required(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create();

        $response = $this
            ->actingAs($user)
            ->postJson('/api/tickets', [
                'client_id' => $client->id,
            ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['title']);
    }

    public function test_unknown_priority_is_rejected(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create();

        $response = $this
            ->actingAs($user)
            ->postJson('/api/tickets', [
                'client_id' => $client->id,
                'title' => 'Cannot access B2B dashboard',
                'priority' => 'mega_urgent_from_hell',
            ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['priority']);
    }

    public function test_status_from_request_is_ignored(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create();

        $response = $this
            ->actingAs($user)
            ->postJson('/api/tickets', [
                'client_id' => $client->id,
                'title' => 'Cannot access B2B dashboard',
                'status' => TicketStatus::Closed->value,
            ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.status', TicketStatus::New->value);

        $this->assertDatabaseHas('tickets', [
            'client_id' => $client->id,
            'created_by' => $user->id,
            'status' => TicketStatus::New->value,
        ]);
    }
}
