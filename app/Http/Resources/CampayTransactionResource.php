<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CampayTransactionResource extends JsonResource
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
			'status' => $this->status,
			'reference' => $this->reference,
			'amount' => $this->amount,
			'currency' => $this->currency,
			'operator' => $this->operator,
			'code' => $this->code,
			'operatorReference' => $this->operator_reference,
		];
	}
}
