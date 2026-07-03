<?php

namespace App\Http\Requests\Ticket;

use App\Application\Ticket\DTO\CreateTicketData;
use App\Domain\Ticket\Enums\TicketPriority;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'client_id' => ['required', 'integer', 'exists:clients,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority' => [
                'sometimes',
                'string',
                Rule::enum(TicketPriority::class),
            ],
            'assigned_to' => ['nullable', 'integer', 'exists:users,id'],
        ];
    }

    public function toData(): CreateTicketData
    {
        $user = $this->user();

        if ($user === null) {
            abort(401);
        }

        /** @var array{client_id:int, title:string, description?:string|null, priority?:string|null, assigned_to?:int|null} $validated */
        $validated = $this->validated();

        return new CreateTicketData(
            clientId: $validated['client_id'],
            createdBy: $user->id,
            title: $validated['title'],
            description: $validated['description'] ?? null,
            priority: isset($validated['priority'])
                ? TicketPriority::from($validated['priority'])
                : TicketPriority::Normal,
            assignedTo: $validated['assigned_to'] ?? null,
        );
    }
}
