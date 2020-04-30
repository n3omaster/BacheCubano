<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\PushController;
use App\Post;
use App\PostCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use SEOMeta;
use OpenGraph;
use Twitter;
use Artesaos\SEOTools\Facades\TwitterCard;

use Illuminate\Support\Facades\Cache;
use Spatie\SchemaOrg\Schema;
use Spatie\SchemaOrg\Graph;

use App\Notifications\PostFacebook;
use App\Notifications\PostTelegram;
use App\Notifications\PostTwitter;
use Exception;
use Illuminate\Support\Facades\Log;

use App\User;

class BlogController extends Controller
{
    /**
     * Blog Index
     */
    public function index(Request $request, $category = "")
    {
        //SEO Data
        $seo_data = [
            'title' => "Blog de Noticias Comercio y Compra venta en Cuba",
            'desc' => "Noticias, ofertas, reviews e información general sobre la compra venta en Cuba",
        ];
        if ($request->has('s')) {
            $seo_data = [
                'title' => "Buscando " . $request->input('s') . " en el Blog Bachecubano",
                'desc' => "Buscando " . $request->input('s') . " en el Blog Bachecubano. Todo sobre tecnolog'ia y noticias de negocios en Cuba"
            ];
        }
        SEOMeta::setTitle($seo_data['title']);
        SEOMeta::setDescription($seo_data['desc']);
        Twitter::setTitle($seo_data['title']);
        OpenGraph::setTitle($seo_data['title']);
        OpenGraph::setDescription($seo_data['desc']);
        OpenGraph::addProperty('type', 'website');

        //Get Category post if its submitted
        if ($category !== "") {
            //Try to get this Category Details or fail
            $category = PostCategory::where('slug', $category)->firstOrFail();
            //Latest 10 post
            $post_query = Post::where('enabled', 1)->where('category_id', $category->id)->with('owner', 'category')->latest();
        } else {
            //Latest 10 post
            $post_query = Post::where('enabled', 1)->with('owner', 'category')->latest();
        }

        //Search terms
        if ($request->has('s') && $request->input('s') != "") {
            $post_query->where('title', 'LIKE', "%{$request->input('s')}%");
            $post_query->where('body', 'LIKE', "%{$request->input('s')}%");
            $post_query->where('tags', 'LIKE', "%{$request->input('s')}%");
        }

        $posts = $post_query->paginate(10);

        //Schema


        //Bog Categories
        $blog_categories = Cache::remember('blog_categories', 60 * 24, function () {
            return PostCategory::where('enabled', 1)->get();
        });

        //BreadCrumbs
        $BreadCrumbs = Schema::breadcrumbList()
            ->itemListElement([
                Schema::ListItem()
                    ->position(1)
                    ->name("Blog Bachecubano")
                    ->item(config('app.url') . "blog/")
            ]);

        return view('blog.index', compact('posts', 'blog_categories', 'BreadCrumbs', 'category', 'seo_data'));
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $blog_category_slug = "", $entry_slug)
    {
        //Cache Post Entry
        $blog_post = Cache::remember('cached_post_' . $entry_slug, 120, function () use ($entry_slug) {
            return Post::with('category')->where('slug', $entry_slug)->firstOrFail();
        });

        //SEO Data
        $seo_data = [
            'title' => $blog_post->title,
            'desc' => text_clean(Str::limit(strip_tags($blog_post->body), 160)),
        ];
        SEOMeta::setTitle($seo_data['title']);
        SEOMeta::setDescription($seo_data['desc']);
        Twitter::setTitle($seo_data['title']);
        TwitterCard::setType('summary_large_image');
        Twitter::addValue('creator', "@" . twitter_username($blog_post->owner->social_twitter));
        OpenGraph::setTitle($seo_data['title']);
        OpenGraph::setDescription($seo_data['desc']);
        OpenGraph::addProperty('type', 'article');                          //This is an article
        OpenGraph::addImage(config('app.img_url') . "blog/" . $blog_post->cover, ['height' => 480, 'width' => 768]);

        //Latest 5 post
        $posts = Post::latest()->take(5)->get();

        //Bog Categories
        $blog_categories = Cache::remember('blog_categories', 60 * 24, function () {
            return PostCategory::where('enabled', 1)->get();
        });

        //Check for A REAL VISIT HERE
        $r = $this->hit_visit($request, $blog_post);

        //BreadCrumbs
        $BreadCrumbs = Schema::breadcrumbList()
            ->itemListElement([
                Schema::ListItem()
                    ->position(1)
                    ->name("Blog Bachecubano")
                    ->item(config('app.url') . "blog/"),
                Schema::ListItem()
                    ->position(2)
                    ->name($blog_post->category->name)
                    ->item(route('blog_index', ['blog_category_slug' => $blog_post->category->slug])),
                Schema::ListItem()
                    ->position(3)
                    ->name($blog_post->title)
                    ->item(post_url($blog_post))
            ]);

        //Schema
        $Article = Schema::Article()
            ->name($seo_data['title'])
            ->image(config('app.img_url') . "blog/" . $blog_post->cover)
            ->articleBody(strip_tags($blog_post->body))
            ->author($blog_post->owner->name)
            ->datePublished($blog_post->created_at)
            ->dateModified($blog_post->modified_at)
            ->headline($seo_data['title'])
            ->publisher(
                Schema::organization()
                    ->name(config('app.name'))
                    ->logo(asset("android-chrome-512x512.png"))
            )
            ->aggregateRating(
                Schema::aggregateRating()
                    ->ratingValue(5)
                    ->reviewCount(1)
            );

        $SchemaLD = new Graph();
        $SchemaLD->add($Article);
        $SchemaLD->add($BreadCrumbs);


        //AMP or not View
        return view($this->getView('blog.show'), compact('posts', 'blog_post', 'blog_categories', 'BreadCrumbs', 'SchemaLD'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        //Latest 5 post
        $posts = Post::latest()->take(5)->get();

        //Get All Categories
        //Retrieve Ad with aditional data
        $blog_categories = Cache::remember('post_categories', 120, function () {
            return PostCategory::all();
        });

        $edit = false;

        // view create form
        return view('blog.create', compact('posts', 'blog_categories', 'edit'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // validate incoming request data with validation rules
        $request->validate([
            'title' => 'required|min:1|max:255',
            'category' => 'required|numeric',
            'body'  => 'required|min:1',
            'tags' => 'required',
            'cover' => 'required'
        ]);

        // store data with create() method
        $blog_post = Post::create([
            'user_id'   => Auth::id(),
            'title'     => $request->input('title'),
            'slug'      => Str::slug(request()->title),
            'body'      => $request->input('body'),
            'cover'     => $request->input('cover') ? $request->input('cover') : "",
            'category_id' => $request->input('category'),
            'enabled' => 0,
            'monetized' => 0,
            'hits' => 0,
            'tags' => $request->input('tags')
        ]);

        //Notify the admin for activation/deactivation of the post entry via email ??
        //Yes, notify admin to approve this

        // redirect to show post URL
        return redirect(post_url($blog_post));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($post_id)
    {
        $blog_post = Post::with('owner', 'category')->findOrFail($post_id);

        if (Auth::check() && (Auth::id() !== $blog_post->user_id || (User::find(Auth::id()))->hasRole('moderator'))) {
            
            $edit = true;

            //Get All Categories
            //Retrieve Ad with aditional data
            $blog_categories = Cache::remember('post_categories', 120, function () {
                return PostCategory::all();
            });

            // we are using route model binding 
            // view edit page with post data
            return view('blog.create')->with(['blog_post' => $blog_post, 'edit' => $edit, 'blog_categories' => $blog_categories]);

        } else {
            abort(404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $post_id)
    {
        $blog_post = Post::with('owner', 'category')->findOrFail($post_id);

        //Get logged in user and permissions of it
        if (!Auth::check() || (Auth::id() !== $blog_post->user_id && Auth::id() !== 1)) {
            abort(404);
        }

        // validate incoming request data with validation rules
        $this->validate(request(), [
            'title' => 'required|min:1|max:255',
            'body'  => 'required|min:1'
        ]);

        //¿Cover Update?

        //Get the Blog Post
        $blog_post = Post::with('owner', 'category')->findOrFail($post_id);

        // update post with new data using update() method
        $blog_post->update([
            'title'     => $request->input('title'),
            'body'      => $request->input('body'),
            'tags' => $request->input('tags')
        ]);

        // return to show post URL
        return redirect(post_url($blog_post));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $post_id)
    {
    }

    /**
     * Get Feeds
     */
    public function feeds()
    {
        $blog_posts = Post::getFeedItems();
        return response()->view('feed', compact('blog_posts'))->header('Content-Type', 'text/xml');
    }

    /**
     * Checks for a certain parameters befor give the real visit
     */
    public function hit_visit(Request $request, $blog_post)
    {
        //Is Bot this?
        function is_bot($user_agent)
        {
            return preg_match('/(abot|dbot|ebot|hbot|kbot|lbot|mbot|nbot|obot|pbot|rbot|sbot|tbot|vbot|ybot|zbot|bot\.|bot\/|_bot|\.bot|\/bot|\-bot|\:bot|\(bot|crawl|slurp|spider|seek|accoona|acoon|adressendeutschland|ah\-ha\.com|ahoy|altavista|ananzi|anthill|appie|arachnophilia|arale|araneo|aranha|architext|aretha|arks|asterias|atlocal|atn|atomz|augurfind|backrub|bannana_bot|baypup|bdfetch|big brother|biglotron|bjaaland|blackwidow|blaiz|blog|blo\.|bloodhound|boitho|booch|bradley|butterfly|calif|cassandra|ccubee|cfetch|charlotte|churl|cienciaficcion|cmc|collective|comagent|combine|computingsite|csci|curl|cusco|daumoa|deepindex|delorie|depspid|deweb|die blinde kuh|digger|ditto|dmoz|docomo|download express|dtaagent|dwcp|ebiness|ebingbong|e\-collector|ejupiter|emacs\-w3 search engine|esther|evliya celebi|ezresult|falcon|felix ide|ferret|fetchrover|fido|findlinks|fireball|fish search|fouineur|funnelweb|gazz|gcreep|genieknows|getterroboplus|geturl|glx|goforit|golem|grabber|grapnel|gralon|griffon|gromit|grub|gulliver|hamahakki|harvest|havindex|helix|heritrix|hku www octopus|homerweb|htdig|html index|html_analyzer|htmlgobble|hubater|hyper\-decontextualizer|ia_archiver|ibm_planetwide|ichiro|iconsurf|iltrovatore|image\.kapsi\.net|imagelock|incywincy|indexer|infobee|informant|ingrid|inktomisearch\.com|inspector web|intelliagent|internet shinchakubin|ip3000|iron33|israeli\-search|ivia|jack|jakarta|javabee|jetbot|jumpstation|katipo|kdd\-explorer|kilroy|knowledge|kototoi|kretrieve|labelgrabber|lachesis|larbin|legs|libwww|linkalarm|link validator|linkscan|lockon|lwp|lycos|magpie|mantraagent|mapoftheinternet|marvin\/|mattie|mediafox|mediapartners|mercator|merzscope|microsoft url control|minirank|miva|mj12|mnogosearch|moget|monster|moose|motor|multitext|muncher|muscatferret|mwd\.search|myweb|najdi|nameprotect|nationaldirectory|nazilla|ncsa beta|nec\-meshexplorer|nederland\.zoek|netcarta webmap engine|netmechanic|netresearchserver|netscoop|newscan\-online|nhse|nokia6682\/|nomad|noyona|nutch|nzexplorer|objectssearch|occam|omni|open text|openfind|openintelligencedata|orb search|osis\-project|pack rat|pageboy|pagebull|page_verifier|panscient|parasite|partnersite|patric|pear\.|pegasus|peregrinator|pgp key agent|phantom|phpdig|picosearch|piltdownman|pimptrain|pinpoint|pioneer|piranha|plumtreewebaccessor|pogodak|poirot|pompos|poppelsdorf|poppi|popular iconoclast|psycheclone|publisher|python|rambler|raven search|roach|road runner|roadhouse|robbie|robofox|robozilla|rules|salty|sbider|scooter|scoutjet|scrubby|search\.|searchprocess|semanticdiscovery|senrigan|sg\-scout|shai\'hulud|shark|shopwiki|sidewinder|sift|silk|simmany|site searcher|site valet|sitetech\-rover|skymob\.com|sleek|smartwit|sna\-|snappy|snooper|sohu|speedfind|sphere|sphider|spinner|spyder|steeler\/|suke|suntek|supersnooper|surfnomore|sven|sygol|szukacz|tach black widow|tarantula|templeton|\/teoma|t\-h\-u\-n\-d\-e\-r\-s\-t\-o\-n\-e|theophrastus|titan|titin|tkwww|toutatis|t\-rex|tutorgig|twiceler|twisted|ucsd|udmsearch|url check|updated|vagabondo|valkyrie|verticrawl|victoria|vision\-search|volcano|voyager\/|voyager\-hc|w3c_validator|w3m2|w3mir|walker|wallpaper|wanderer|wauuu|wavefire|web core|web hopper|web wombat|webbandit|webcatcher|webcopy|webfoot|weblayers|weblinker|weblog monitor|webmirror|webmonkey|webquest|webreaper|websitepulse|websnarf|webstolperer|webvac|webwalk|webwatch|webwombat|webzinger|wget|whizbang|whowhere|wild ferret|worldlight|wwwc|wwwster|xenu|xget|xift|xirq|yandex|yanga|yeti|yodao|zao\/|zippp|zyborg|\.\.\.\.)/i', $user_agent);
        }

        //If the Ad owner its the same who visits it
        if ($blog_post->user_id == Auth::id())
            return false;

        if (is_bot($request->server('HTTP_USER_AGENT')))
            return false;

        $blog_post->increment('hits');

        return true;
    }

    /**
     * Dinamic view return based on amp or not
     */
    private function getView($viewName)
    {
        if (request()->segment(1) == 'amp') {
            if (view()->exists($viewName . '-amp')) {
                $viewName .= '-amp';
            } else {
                abort(404);
            }
        }
        return $viewName;
    }

    /**
     * Blog Post approve and viralice
     */
    public function approve_post($post_id, $telegram = "1", $twitter = "1", $push = "1", $facebook = "1")
    {
        //Get logged in user and permissions of it
        if (!Auth::check() || Auth::id() !== 1) {
            abort(404);
        }

        $blog_post = Post::findOrFail($post_id);

        //Set approved
        $blog_post->update(['enabled' => 1]);


        //Send this entry Blog Post to Telegram Instant View
        if ($telegram == "1") {
            try {
                $blog_post->notify(new PostTelegram);
            } catch (Exception $e) {
            }
        }


        //Send this Post to Twitter
        if ($twitter == "1") {
            try {
                $blog_post->notify(new PostTwitter);
            } catch (Exception $e) {
            }
        }

        //Send Push notification for the Blog entry
        if ($push == "1") {
            try {
                //ANORMAAAAAAAAAAAALLLLLLLLLLLLL
                PushController::send_notification_post($blog_post);
            } catch (Exception $e) {
                Log::error(json_encode($e));
            }
        }

        //Faxcebook Blog Post
        if ($facebook == "1") {
            try {
                $blog_post->notify(new PostFacebook);
            } catch (Exception $e) {
            }
        }

        //Redirect to the current post entry
        return redirect(post_url($blog_post));
    }
}
