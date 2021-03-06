<?php

namespace App;

use App\Mail\Exception;
use Illuminate\Database\Eloquent\Model;
use Rennokki\Rating\Traits\CanBeRated;
use Rennokki\Rating\Contracts\Rateable;
use Illuminate\Notifications\Notifiable;

use Overtrue\LaravelLike\Traits\Likeable;

/**
 * ads, ad_description, ad_location, ad_promo, ad_resource, ad_stats
 */
class Ad extends Model implements Rateable
{
    //Like and Rating Traits
    use Likeable, CanBeRated;

    //Allow this model to be used as Notifications feature for paid ads
    use Notifiable;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'secret', 'ip'
    ];

    /**
     * Get the Ad category.
     */
    public function category()
    {
        return $this->belongsTo('App\Category');
    }

    /**
     * Get the ad description
     */
    public function description()
    {
        return $this->hasOne('App\AdDescription')->withDefault([
            'title' => "invalid title",
            'description' => "invalid Description"
        ]);
    }

    /**
     * Get the location
     */
    public function location()
    {
        return $this->hasOne('App\AdLocation', 'id', 'region_id')->withDefault([
            'title' => 'La Habana'
        ]);
    }

    /**
     * Get the promotype
     */
    public function promo()
    {
        return $this->hasOne('App\AdPromo')->withDefault([
            'promotype' => "0",
        ]);
    }

    /**
     * Get the resources
     */
    public function resources()
    {
        return $this->hasMany('App\AdResource');
    }

    /**
     * Get the stats
     */
    public function stats()
    {
        return $this->hasOne('App\AdStats')->withDefault([
            'hits' => 0,
        ]);
    }

    /**
     * User Owner of the ad
     */
    public function owner()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    // this is a recommended way to declare event handlers
    public static function boot()
    {
        parent::boot();
        static::deleting(function ($ad) { // before delete() method call this

            try {
                $ad->description()->delete();
            } catch (Exception $e) {
            }

            try {
                $ad->promo()->delete();
            } catch (Exception $e) {
            }

            try {
                $ad->stats()->delete();
            } catch (Exception $e) {
            }

            try {
                $ad->resources()->delete();
            } catch (Exception $e) {
            }
        });
    }
}

class AdDescription extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'ad_id';

    /**
     * Get the Ad that owns the stats.
     */
    public function ad()
    {
        return $this->belongsTo('App\Ad')->withDefault([
            'title' => "invalid title",
            'description' => "invalid Description"
        ]);
    }
}

class AdPromo extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    protected $fillable = ['ad_id', 'promotype', 'end_promo'];

    /**
     * Get the Ad that owns the stats.
     */
    public function ad()
    {
        return $this->belongsTo('App\Ad')->withDefault([
            'promotype' => "0",
        ]);
    }
}

class AdResource extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get the Ad that owns the stats.
     */
    public function ad()
    {
        return $this->belongsTo('App\Ad');
    }
}

class AdStats extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    protected $fillable = ['ad_id', 'hits'];

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'ad_id';

    /**
     * Get the Ad that owns the stats.
     */
    public function ad()
    {
        return $this->belongsTo('App\Ad')->withDefault([
            'hits' => 0,
        ]);
    }
}
