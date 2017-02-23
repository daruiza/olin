<?php namespace App\Core\Security;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $table = 'seg_module';
	
	protected $fillable = ['id','module','preference','description','active','app_id'];
			
}

