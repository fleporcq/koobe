<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Language
 *
 * @property integer $id 
 * @property string $lang 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Language whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Language whereLang($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Language whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Language whereUpdatedAt($value)
 */
class Language extends Model
{
    protected $fillable = array('lang');
}