<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Theme
 *
 * @property integer $id
 * @property string $name
 * @property string $slug
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Theme whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Theme whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Theme whereSlug($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Theme whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Theme whereUpdatedAt($value)
 * @method static \App\Models\Theme whereToken($slug)
 */
class Theme extends Model
{

    use \KDuma\Eloquent\Slugabble;

    protected $hidden = array('pivot');

    protected $fillable = array('name');

    protected function SluggableString(){
        return $this->name;
    }

}
