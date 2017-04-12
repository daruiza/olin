<?php namespace App\Core\Security;

use Illuminate\Database\Eloquent\Model;

class AppUser extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $table = 'seg_app_x_user';
	
	protected $fillable = ['app_id','user_id','active'];
			
}

