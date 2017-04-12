<?php namespace App\Core\Security;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $table = 'seg_rol';
	
	protected $fillable = ['id','rol','description'];
			
}

