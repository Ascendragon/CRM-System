<?php

namespace App\Application\Ticket\UseCases;

use App\Application\Ticket\DTO\ListTicketsData;
use App\Models\Ticket;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListTickets
{
    public function handle(ListTicketsData $data): LengthAwarePaginator
    {
        $query = Ticket::query()
            ->with(['client', 'creator', 'assignee']);

        if ($data->status !== null) {
            $query->where('status', $data->status);
        }
        if ($data->priority !== null) {
            $query->where('priority', $data->priority);
        }
        if ($data->clientId !== null) {
            $query->where('client_id', $data->clientId);
        }
        if ($data->assignedTo !== null) {
            $query->where('assigned_to', $data->assignedTo);
        }
        if ($data->createdFrom !== null) {
            $query->whereDate('created_at', '>=', $data->createdFrom);
        }
        if ($data->createdTo !== null) {
            $query->whereDate('created_at', '<=', $data->createdTo);
        }
        $direction = $data->sort === '-created_at' ? 'desc' : 'asc';

        return $query
            ->orderBy('created_at', $direction)
            ->paginate($data->perPage);
    }
}
