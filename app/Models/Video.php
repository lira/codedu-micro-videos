<?php

namespace App\Models;

use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Video
 *
 * @property string $id
 * @property string $title
 * @property string $description
 * @property int $year_launched
 * @property bool $opened
 * @property string $rating
 * @property int $duration
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Category[] $categories
 * @property-read int|null $categories_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Genre[] $genres
 * @property-read int|null $genres_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Video onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereOpened($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereYearLaunched($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Video withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Video withoutTrashed()
 * @mixin \Eloquent
 */
class Video extends Model
{
    use SoftDeletes, Uuid;

    const RATING_LIST = ['L', '10', '12', '14', '16', '18'];

    protected $fillable = [
        'title',
        'description',
        'year_launched',
        'opened',
        'rating',
        'duration',
    ];

    protected $dates = ['deleted_at'];

    protected $casts = [
        'id' => 'string',
        'opened' => 'boolean',
        'duration' => 'integer',
        'year_launched' => 'integer',
    ];

    public $incrementing = false;

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function genres()
    {
        return $this->belongsToMany(Genre::class);
    }
}
