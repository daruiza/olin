<?php namespace App\Core\OLIN;

use Illuminate\Database\Eloquent\Model;

class OlinLog extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $table = 'oli_log';
	
	protected $fillable = ['id','action','description','date','user_id'];
			
}

