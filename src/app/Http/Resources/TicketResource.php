<?php

namespace App\Http\Resources;

use App\Models\Ticket;
use Carbon\CarbonInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use LogicException;

class TicketResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $ticket = $this->resource;

        if (! $ticket instanceof Ticket) {
            throw new LogicException('TicketResource expects Ticket Model');
        }

        return [
            'id' => $ticket->getKey(),
            'client_id' => $ticket->getAttribute('client_id'),
            'created_by' => $ticket->getAttribute('created_by'),
            'assigned_to' => $ticket->getAttribute('assigned_to'),
            'title' => $ticket->getAttribute('title'),
            'description' => $ticket->getAttribute('description'),
            'status' => $ticket->getStatus()->value,
            'priority' => $ticket->getPriority()->value,
            'closed_at' => $this->formatDate($ticket->getAttribute('closed_at')),
            'created_at' => $this->formatDate($ticket->getAttribute('created_at')),
            'updated_at' => $this->formatDate($ticket->getAttribute('updated_at')),
        ];
    }

    private function formatDate(mixed $date): ?string
    {
        if ($date === null) {
            return null;
        }

        if ($date instanceof CarbonInterface) {
            return $date->toISOString();
        }

        return (string) $date;
    }
}
