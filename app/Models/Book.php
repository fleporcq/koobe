<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\Book
 *
 * @property integer $id
 * @property string $md5
 * @property string $title
 * @property integer $language_id
 * @property string $slug
 * @property integer $year
 * @property string $description
 * @property boolean $enabled
 * @property float $average_rate
 * @property integer $checker_id
 * @property string $checked_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Author[] $authors
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Theme[] $themes
 * @property-read \App\Models\Language $language
 * @property-read \App\Models\User $checker
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Book whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Book whereMd5($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Book whereTitle($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Book whereLanguageId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Book whereSlug($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Book whereYear($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Book whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Book whereEnabled($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Book whereAverageRate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Book whereCheckerId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Book whereCheckedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Book whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Book whereUpdatedAt($value)
 * @method static \App\Models\Book whereToken($slug)
 * @method static \App\Models\Book bySlug($slug)
 */
class Book extends Model
{
    use \KDuma\Eloquent\Slugabble;

    const NO_COVER_FILE = "no-cover";
    const COVERS_DIRECTORY = "covers";
    const EPUBS_DIRECTORY = "epubs";
    const SEEDS_DIRECTORY = "epubs";

    public function authors()
    {
        return $this->belongsToMany('App\Models\Author');
    }

    public function themes()
    {
        return $this->belongsToMany('App\Models\Theme');
    }

    public function language()
    {
        return $this->belongsTo('App\Models\Language');
    }

    public function checker()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function scopeBySlug($query, $slug)
    {
        return $query->whereSlug($slug)->first();
    }

    public function scopeSearch($query, $terms){
       // return $query->where('title', 'LIKE', "%$terms%")->whereEnabled(true);
        return $query->whereEnabled(true)->whereRaw("MATCH(title) AGAINST(? IN BOOLEAN MODE)", array("$terms"));
    }
}
