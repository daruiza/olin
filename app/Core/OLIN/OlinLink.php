<?php namespace App\Core\OLIN;

use Illuminate\Database\Eloquent\Model;

class OlinLink extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $table = 'oli_link';
	
	protected $fillable = ['id','link','description'];
			
}

