<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentClass extends Model
{
    use HasFactory;

    use SoftDeletes;
	protected $table = 'class';
	protected $guarded = ['id'];

    public function students()
	{
		return $this->hasMany('\App\Student', 'class_id', 'id');
	}
}
