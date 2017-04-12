<?php namespace App\Core\Homage;

use Illuminate\Database\Eloquent\Model;

class HomState extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $table = 'hom_state';
	
	protected $fillable = ['id','state','alert','order','description','active'];
			
}

