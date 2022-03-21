<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampayTransation extends Model
{
	use HasFactory;

	protected $guarded = [];
	protected $primaryKey = 'code';
}
