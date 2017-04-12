<?php namespace App\Core\OLIN;

use Illuminate\Database\Eloquent\Model;

class OlinSeat extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $table = 'oli_seat';
	
	protected $fillable = ['id','seat','description'];
			
}

