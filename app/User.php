<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $table = 'seg_user';
	
	protected $fillable = ['id','name','email','password','active','login','ip','rol_id'];
		
	public function setPasswordAttribute($password){
		$this->attributes['password'] = \Hash::make($password);
	}
	
	public function profile(){
		return $this->hasOne('App\Core\Security\UserProfile');
	}
	
	public function userAplications(){
		return $this->hasMany('App\Core\Security\AppUser');
	}
	
}
