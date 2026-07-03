<?php

namespace App\Application\Ticket\UseCases;

use App\Application\Ticket\DTO\CreateTicketData;
use App\Domain\Ticket\Enums\TicketStatus;
use App\Models\Ticket;

class CreateTicket
{
    public function handle(CreateTicketData $data): Ticket
    {
        return Ticket::create([
            'client_id' => $data->clientId,
            'created_by' => $data->createdBy,
            'assigned_to' => $data->assignedTo,
            'title' => $data->title,
            'description' => $data->description,
            'status' => TicketStatus::New,
            'priority' => $data->priority,
            'closed_at' => null,
        ]);
    }
}
