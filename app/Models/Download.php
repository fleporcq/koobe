<?php namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Download
 *
 * @property integer $book_id
 * @property integer $user_id
 * @property string $downloaded_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Download whereBookId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Download whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Download whereDownloadedAt($value)
 */
class Download extends Model
{

    public $timestamps = false;

    protected $fillable = ['book_id', 'user_id'];

    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);
        $this->downloaded_at = new Carbon();
    }

}
