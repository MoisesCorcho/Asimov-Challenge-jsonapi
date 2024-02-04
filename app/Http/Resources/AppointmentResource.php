<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'appointments',
            'id' => (string) $this->getRouteKey(),
            'attributes' => [
                'date' => $this->date,
                'start_time' => $this->start_time,
                'email' => $this->email
            ],
            'links' => [
                'self' => route('api.v1.appointments.show', $this)
            ]
        ];
    }

    public function toResponse($request)
    {
        return parent::toResponse($request)->withHeaders([
            'Location' => route('api.v1.appointments.show', $this)
        ]);
    }
}
