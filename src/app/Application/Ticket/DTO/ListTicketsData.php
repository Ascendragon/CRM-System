<?php

namespace App\Application\Ticket\DTO;

use App\Domain\Ticket\Enums\TicketPriority;
use App\Domain\Ticket\Enums\TicketStatus;

final class ListTicketsData
{
    public function __construct(
        public private(set) ?TicketStatus $status,
        public private(set) ?TicketPriority $priority,
        public private(set) ?int $clientId,
        public private(set) ?int $assignedTo,
        public private(set) ?string $createdFrom,
        public private(set) ?string $createdTo,
        public private(set) string $sort,
        public private(set) int $perPage,
    ) {}
}
