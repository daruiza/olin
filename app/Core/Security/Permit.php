<?php namespace App\Core\Security;

use Illuminate\Database\Eloquent\Model;

class Permit extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $table = 'seg_permit';
	
	protected $fillable = ['rol_id','module_id','option_id'];
	
	public function modules(){
		return $this->hasOne('App\Core\Security\Module','id', 'module_id');
	}
	public function options(){
		return $this->hasOne('App\Core\Security\Option','id', 'option_id');
	}
			
}

