<?php

namespace App\Http\Resources;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class CashboxResource extends JsonResource
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
            "balance" => $this->balance,
            "lastTransaction" => new TransactionResource(
                Auth::user()->transactions()->orderBy('id', 'desc')->first()
            )
        ];
    }

    public function casbox_user()
    {
        return User::find($this->user);
    }
}