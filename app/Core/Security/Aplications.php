<?php namespace App\Core\Security;

use Illuminate\Database\Eloquent\Model;

class Aplications extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $table = 'seg_app';
	
	protected $fillable = ['id','app','description','preferences','active'];
	
	public function appAplications(){
		return $this->hasMany('App\Core\Security\AppUser');
	}
	
	public function appModules(){
		return $this->hasMany('App\Core\Security\Module');
	}
			
}

