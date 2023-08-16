<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Student;

class Bussing extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'bussing_data';

    public static function updateOrInsert($bussingDataRow, $bussingDate)
	{
		$existingStudent = Student::where('index_number', trim($bussingDataRow['index_number']))
			->withTrashed()->first();

		if (!$existingStudent) return false;

		$bussingData = Bussing::where('date', '=', $bussingDate)
			->where('student_id', $existingStudent->id)
			->first();

		if ($bussingData) {
			//update
			$bussingData->present = $bussingDataRow['st_attn'];
			$bussingData->number_bussed = $bussingDataRow['twn_attn'];
            $bussingData->cloudinary_img_id = $bussingDataRow['cloudinary_img_id'];
			$bussingData->save();
		} else {
			//create new
			$bussingData = new Bussing();
			$bussingData->date = $bussingDate;
			$bussingData->student_id = $existingStudent->id;
			$bussingData->student_addmission_number = $existingStudent->index_number;
			$bussingData->class_id = $existingStudent->class_id;
			$bussingData->present = $bussingDataRow['st_attn'];
			$bussingData->number_bussed = $bussingDataRow['twn_attn'];
            $bussingData->cloudinary_img_id = $bussingDataRow['cloudinary_img_id'];

			$bussingData->save();
		}

		return $bussingData;
	}


}
