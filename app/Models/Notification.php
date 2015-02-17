<?php namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    public $timestamps = false;

    protected $fillable = ['user_id', 'message', 'type'];

    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);
        $this->pushed_at = new Carbon();
    }
}