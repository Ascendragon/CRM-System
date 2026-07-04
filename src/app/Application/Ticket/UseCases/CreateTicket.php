<?php

namespace App\Application\Ticket\UseCases;

use App\Application\Ticket\DTO\CreateTicketData;
use App\Domain\Audit\Enums\AuditAction;
use App\Domain\Ticket\Enums\TicketStatus;
use App\Models\AuditLog;
use App\Models\Ticket;
use App\Models\TicketStatusHistory;
use Illuminate\Support\Facades\DB;

class CreateTicket
{
    public function handle(CreateTicketData $data): Ticket
    {
        return DB::transaction(function () use ($data): Ticket {
            $ticket = Ticket::create([
                'client_id' => $data->clientId,
                'created_by' => $data->createdBy,
                'assigned_to' => $data->assignedTo,
                'title' => $data->title,
                'description' => $data->description,
                'status' => TicketStatus::New,
                'priority' => $data->priority,
                'closed_at' => null,
            ]);

            TicketStatusHistory::create([
                'ticket_id' => $ticket->getKey(),
                'old_status' => null,
                'new_status' => TicketStatus::New,
                'changed_by' => $data->createdBy,
                'reason' => 'Ticket created',
            ]);

            AuditLog::create([
                'actor_id' => $data->createdBy,
                'entity_type' => 'ticket',
                'entity_id' => $ticket->getKey(),
                'action' => AuditAction::TicketCreated,
                'payload' => [
                    'client_id' => $data->clientId,
                    'assigned_to' => $data->assignedTo,
                    'status' => TicketStatus::New->value,
                    'priority' => $data->priority->value,
                ],
            ]);

            return $ticket;
        });
    }
}
