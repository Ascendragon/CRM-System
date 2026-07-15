<?php

namespace App\Http\Requests\Ticket;

use App\Application\Ticket\DTO\ListTicketsData;
use App\Domain\Ticket\Enums\TicketPriority;
use App\Domain\Ticket\Enums\TicketStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ListTicketsRequest extends FormRequest
{
    private const DEFAULT_PER_PAGE = 15;

    private const MAX_PER_PAGE = 100;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => ['sometimes', 'string', Rule::enum(TicketStatus::class)],
            'priority' => ['sometimes', 'string', Rule::enum(TicketPriority::class)],
            'client_id' => ['integer', 'sometimes', 'exists:clients,id'],
            'assigned_to' => ['sometimes', 'integer', 'exists:users,id'],
            'created_from' => ['sometimes', 'date_format:Y-m-d'],
            'created_to' => ['sometimes', 'date_format:Y-m-d', 'after_or_equal:created_from'],
            'sort' => ['sometimes', 'string', Rule::in(['created_at', '-created_at'])],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:'.self::MAX_PER_PAGE],
        ];
    }

    public function toData(): ListTicketsData
    {
        /**
         * @var array{
         *     status?: string,
         *     priority?: string,
         *     client_id?: int|string,
         *     assigned_to?: int|string,
         *     created_from?: string,
         *     created_to?: string,
         *     sort?: string,
         *     per_page?: int|string
         * } $validated
         */
        $validated = $this->validated();

        return new ListTicketsData(
            status: isset($validated['status'])
                ? TicketStatus::from($validated['status'])
                : null,
            priority: isset($validated['priority'])
                ? TicketPriority::from($validated['priority'])
                : null,
            clientId: isset($validated['client_id'])
                ? (int) $validated['client_id']
                : null,
            assignedTo: isset($validated['assigned_to'])
                ? (int) $validated['assigned_to']
                : null,
            createdFrom: $validated['created_from'] ?? null,
            createdTo: $validated['created_to'] ?? null,
            sort: $validated['sort'] ?? '-created_at',
            perPage: isset($validated['per_page'])
                ? (int) $validated['per_page']
                : self::DEFAULT_PER_PAGE,
        );
    }
}
