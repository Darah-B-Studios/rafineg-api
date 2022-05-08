<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
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
            'id' => $this->id,
            'user' => new UserResource(User::find($this->user_id)),
            'code' => $this->code,
            'reference' => $this->reference,
            'phoneNumber' => $this->phoneNumber,
            'amount' => $this->amount,
            'description' => $this->description,
            'currency' => $this->currency,
            'operator' => $this->operator,
            'operatorReference' => $this->operatorReference,
            'externalReference' => $this->externalReference,
            'status' => $this->status,
            'collectionType' => $this->collectionType,
            'collectionTypeCode' => $this->collectionTypeCode,
            'method' => $this->method,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at
        ];
    }
}