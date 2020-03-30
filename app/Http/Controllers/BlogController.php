<?php

namespace App\Http\Controllers;

use App\Post;
use App\PostCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use SEOMeta;
use OpenGraph;
use Twitter;

use Illuminate\Support\Facades\Cache;
use Spatie\SchemaOrg\Schema;
use stdClass;

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
    public function show($blog_category_slug = "", $entry_slug)
    {
        //Cache Post Entry
        $blog_post = Cache::remember('cached_post_' . $entry_slug, 120, function () use ($entry_slug) {
            return Post::with('category')->where('slug', $entry_slug)->firstOrFail();
        });

        //SEO Data
        $seo_data = [
            'title' => $blog_post->title,
            'desc' => text_clean(Str::limit($blog_post->body, 160)),
        ];
        SEOMeta::setTitle($seo_data['title']);
        SEOMeta::setDescription($seo_data['desc']);
        Twitter::setTitle($seo_data['title']);
        OpenGraph::setTitle($seo_data['title']);
        OpenGraph::setDescription($seo_data['desc']);
        OpenGraph::addProperty('type', 'website');
        OpenGraph::addImage(config('app.img_url') . "blog/" . $blog_post->cover);

        //Latest 5 post
        $posts = Post::latest()->take(5)->get();

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

        //SchemaOrg

        return view('blog.show', compact('posts', 'blog_post', 'blog_categories', 'BreadCrumbs'));
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

        //Get logged in user and permissions of it
        if (!Auth::check() || (Auth::id() !== $blog_post->user_id && Auth::id() !== 1)) {
            abort(404);
        }

        $edit = true;

        //Get All Categories
        //Retrieve Ad with aditional data
        $blog_categories = Cache::remember('post_categories', 120, function () {
            return PostCategory::all();
        });

        // we are using route model binding 
        // view edit page with post data
        return view('blog.create')->with(['blog_post' => $blog_post, 'edit' => $edit, 'blog_categories' => $blog_categories]);
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
}
