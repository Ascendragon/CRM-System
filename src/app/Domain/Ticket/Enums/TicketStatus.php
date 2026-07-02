<?php

namespace App\Domain\Ticket\Enums;

enum TicketStatus: string
{
    case New = 'new';
    case InProgress = 'in_progress';
    case WaitingClient = 'waiting_client';
    case Resolved = 'resolved';
    case Closed = 'closed';
    case Cancelled = 'cancelled';

    public function isFinal(): bool
    {
        return match ($this) {
            self::Closed, self::Cancelled => true,
            default => false,
        };
    }
}
