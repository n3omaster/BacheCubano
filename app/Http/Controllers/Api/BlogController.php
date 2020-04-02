<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Notifications\PostFacebook;
use App\Notifications\PostTelegram;
use App\Notifications\PostTwitter;
use App\Post;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * Some Blog moderation here
 */
class BlogController extends Controller
{

    public function __construct()
    {
    }

    /**
     * Blog Post approve and viralice
     */
    public function approve_post($post_id, $telegram = true, $twitter = true, $push = true, $facebook = true)
    {
        $blog_post = Post::findOrFail($post_id);

        //Set approved
        $blog_post->update(['enabled' => 1]);


        //Send this entry Blog Post to Telegram Instant View
        try {
            $blog_post->notify(new PostTelegram);
        } catch (Exception $e) {
        }

        //Send this Post to Twitter
        try {
            $blog_post->notify(new PostTwitter);
        } catch (Exception $e) {
        }

        //Send Push notification for the Blog entry
        try {
            PushController::send_notification_post($blog_post);
        } catch (Exception $e) {
            Log::error(json_encode($e));
        }

        //Faxcebook Blog Post
        try {
            $blog_post->notify(new PostFacebook);
        } catch (Exception $e) {
        }

        //Redirect to the current post entry
        return redirect(post_url($blog_post));
    }
}
