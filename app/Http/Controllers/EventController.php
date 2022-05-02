<?php

namespace App\Http\Controllers;

use App\Http\Resources\EventResource;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventController
{
    public function getFilteredEvents(Request $request): JsonResource
    {
        $query = Event::query();
        if ((int)$request->input('active') === 1) {
            $query->active();
        }

        return EventResource::collection($query->get());
    }
}
