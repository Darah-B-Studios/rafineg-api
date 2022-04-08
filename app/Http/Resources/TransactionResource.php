<?php

namespace App\Http\Resources;

use App\Models\CampayTransaction;
use App\Models\Transaction;
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
        // $campayTransaction = CampayTransaction::first();
        // $campayTransaction = CampayTransaction::where('code', $this->code)->first();

        // return [
        //     "id" => $this->id,
        //     "code" => $this->code,
        //     "status" => $this->status,
        //     "amount" => $this->amount,
        //     "telephone" => $this->telephone,
        //     "package" => $this->collectionTypeCode,
        //     "description" => $this->description,
        //     "detail" => new CampayTransactionResource($campayTransaction),
        //     "user" => new UserResource(User::find($this->user_id)),
        //     "lastTransaction" => new TransactionResource(Transaction::find($this->transaction_id))
        // ];

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
            'method' => $this->method
        ];
    }
}