<?php namespace App\Core\Homage;

use Illuminate\Database\Eloquent\Model;

class HomRequest extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $table = 'hom_request';
	
	protected $fillable = ['id','name','fhone','cellfhone','identification_headline','name_headline','seat','identification_homage','name_homage','location_homage','orden_service','date_service'];
			
}

