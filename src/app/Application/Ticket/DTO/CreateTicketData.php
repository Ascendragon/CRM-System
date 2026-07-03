<?php

namespace App\Application\Ticket\DTO;

use App\Domain\Ticket\Enums\TicketPriority;

final class CreateTicketData
{
    public function __construct(
        public private(set) int $clientId,
        public private(set) int $createdBy,
        public private(set) string $title,
        public private(set) ?string $description,
        public private(set) TicketPriority $priority,
        public private(set) ?int $assignedTo
    ) {}
}
