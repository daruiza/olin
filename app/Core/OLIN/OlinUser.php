<?php

namespace App\Core\OLIN;

use Illuminate\Database\Eloquent\Model;

class OlinUser extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $table = 'oli_user';
	
	protected $fillable = ['id','identification','name','date','company_id','link_id'];
		
	
}
