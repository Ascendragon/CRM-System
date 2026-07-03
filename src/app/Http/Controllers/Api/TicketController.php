<?php

namespace App\Http\Controllers\Api;

use App\Application\Ticket\UseCases\CreateTicket;
use App\Http\Controllers\Controller;
use App\Http\Requests\Ticket\CreateTicketRequest;
use App\Http\Resources\TicketResource;

class TicketController extends Controller
{
    public function store(CreateTicketRequest $request, CreateTicket $createTicket)
    {
        $ticket = $createTicket->handle($request->toData());

        return new TicketResource($ticket)
            ->response()
            ->setStatusCode(201);
    }
}
