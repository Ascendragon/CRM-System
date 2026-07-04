<?php

namespace App\Domain\Audit\Enums;

enum AuditAction: string
{
    case TicketCreated = 'ticket_created';
    case TicketStatusChanged = 'ticket_status_changed';
    case TicketAssigned = 'ticket_assigned';
    case TicketCommentAdded = 'ticket_comment_added';
}
