<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d'),
            'cover' => new DocumentResource($this->cover),
            'created_by' =>  $this->createdBy->name ?? 'Administrator',
            'date' => $this->date,
            'documents' => DocumentResource::collection($this->whenLoaded('documents')),
        ];
    }
}
