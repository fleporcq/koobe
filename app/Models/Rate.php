<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Rate
 *
 * @property integer $book_id
 * @property integer $user_id
 * @property boolean $rate
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Rate whereBookId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Rate whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Rate whereRate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Rate whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Rate whereUpdatedAt($value)
 */
class Rate extends Model
{


}
