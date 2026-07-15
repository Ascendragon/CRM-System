<?php

namespace App\Http\Controllers\Api;

use App\Application\Ticket\UseCases\CreateTicket;
use App\Application\Ticket\UseCases\ListTickets;
use App\Http\Controllers\Controller;
use App\Http\Requests\Ticket\CreateTicketRequest;
use App\Http\Requests\Ticket\ListTicketsRequest;
use App\Http\Resources\TicketResource;

class TicketController extends Controller
{
    public function index(ListTicketsRequest $request, ListTickets $listTickets)
    {
        $tickets = $listTickets->handle($request->toData());
        $data = $request->toData();

        return TicketResource::collection($tickets);
    }

    public function store(CreateTicketRequest $request, CreateTicket $createTicket)
    {
        $ticket = $createTicket->handle($request->toData());

        return new TicketResource($ticket)
            ->response()
            ->setStatusCode(201);
    }
}
