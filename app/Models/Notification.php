<?php namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Notification
 *
 * @property integer $user_id
 * @property string $pushed_at
 * @property string $readed_at
 * @property string $message
 * @property string $type
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Notification whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Notification wherePushedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Notification whereReadedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Notification whereMessage($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Notification whereType($value)
 * @property integer $id 
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Notification whereId($value)
 */
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