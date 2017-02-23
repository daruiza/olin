<?php namespace App\Core\Security;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $table = 'seg_option';
	
	protected $fillable = ['id','option','action','preferences','active'];
			
}

