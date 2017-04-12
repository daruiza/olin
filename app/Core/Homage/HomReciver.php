<?php namespace App\Core\Homage;

use Illuminate\Database\Eloquent\Model;

class HomReciver extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $table = 'hom_reciver';
	
	protected $fillable = ['id','email','name','topic','description','active'];
			
}

