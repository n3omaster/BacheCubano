<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Overtrue\LaravelLike\Traits\CanBeLiked;

use Spatie\Feed\Feedable;
use Spatie\Feed\FeedItem;

class Post extends Model implements Feedable
{
    use CanBeLiked;

    // table name to be used
    protected $table = 'posts';

    // columns to be allowed in mass-assingment 
    protected $fillable = ['user_id', 'category_id', 'title', 'slug', 'body', 'cover', 'enabled', 'monetized', 'hits', 'tags'];

    /* Relations */
    // One to many inverse relationship with User model
    public function owner()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    /**
     * Get the Post Category.
     */
    public function category()
    {
        return $this->belongsTo('App\PostCategory');
    }

    public function toFeedItem()
    {
        return FeedItem::create()
            ->id($this->id)
            ->title($this->title)
            ->summary($this->title)
            ->updated($this->updated_at)
            ->link(post_url($this))
            ->author($this->owner->name);
    }

    /**
     * Get All Blog Post latest 40 
     * for Feed syndication
     */
    public function getFeedItems()
    {
        return Post::latest()->take(40)->get();
    }
}

/**
 * Blog Categories
 */
class PostCategory extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    // table name to be used
    protected $table = 'post_categories';

    /**
     * Childs cats
     */
    public function childs()
    {
        return $this->hasMany('App\Post', 'category_id', 'id');
    }
}
