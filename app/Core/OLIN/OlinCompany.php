<?php namespace App\Core\OLIN;

use Illuminate\Database\Eloquent\Model;

class OLinCompany extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $table = 'oli_company';
	
	protected $fillable = ['id','company','description','seat_id','active'];
			
}

