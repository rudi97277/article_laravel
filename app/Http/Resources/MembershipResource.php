<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MembershipResource extends JsonResource
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
            'name' => $this->name,
            'status' => $this->status,
            'link_schooler' => $this->link_schooler,
            'link_scoopus' => $this->link_scoopus,
            'verified' => $this->verified,
            'evidence' => new DocumentResource($this->whenLoaded('evidence'))
        ];
    }
}
