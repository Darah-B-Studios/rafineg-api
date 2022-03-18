<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PackageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "description" => $this->description,
            "image" => asset($this->image),
            "fee" => $this->fee,
            "lowInvestmentLimit" => $this->low_investment_limit,
            "highInvestmentLimit" => $this->high_investment_limit,
        ];
    }
}
