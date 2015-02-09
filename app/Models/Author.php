<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Author
 *
 * @property integer $id
 * @property string $name
 * @property string $slug
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Book[] $books
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Author whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Author whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Author whereSlug($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Author whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Author whereUpdatedAt($value)
 * @method static \App\Models\Author whereToken($slug)
 */
class Author extends Model
{

    use \KDuma\Eloquent\Slugabble;

    protected $hidden = array('pivot');

    protected $fillable = array('name');

    protected function SluggableString(){
        return $this->name;
    }

    public function books()
    {
        return $this->belongsToMany('App\Models\Book');
    }

}
