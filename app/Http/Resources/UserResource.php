<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $parentReferal = User::where('code', '=', $this->referedBy)->first();

        return [
            "id" => $this->id,
            "firstName" => $this->firstname,
            "lastName" => $this->lastname,
            "email" => $this->email,
            "phoneNumber" => $this->phone_number,
            "isRegistered" => $this->is_registered,
            "isVerified" => $this->isVerified,
            "isAdmin" => $this->isAdmin,
            "code" => $this->code,
            "referedBy" => $parentReferal ? $parentReferal->id : null
        ];
    }
}
