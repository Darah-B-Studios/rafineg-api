<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NjangiGroupResource extends JsonResource
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
            "id" => $this->id,
            "name" => $this->name,
            "description" => $this->description,
            "minimumSavings" => $this->minimum_savings,
            "maximumSavings" => $this->maximum_savings,
            // "createdAt" => $this->created_at
        ];
    }
}