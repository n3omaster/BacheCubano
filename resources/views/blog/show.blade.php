@extends('layouts.app')

@push('head')
<link rel="amphtml" href="{{ post_url_amp($blog_post) }}">
@endpush

@section('content')
<!-- Page Header Start -->
<div class="page-header">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="breadcrumb-wrapper">
                    <a href="{{ URL::current() }}">
                        <h1 class="product-title">{{ $blog_post->title }}</h1>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Page Header End -->

<ol class="breadcrumb">
    <li><a href="{{ config('app.url') }}">Inicio</a></li>
    <li class="ml-2">/</li>
    <li class="ml-2"><a href="{{ route('blog_index') }}">Blog</a></li>
    <li class="ml-2">/</li>
    <li class="ml-2"><a href="{{ route('blog_index', ['blog_category_slug' => $blog_post->category->slug]) }}">{{ $blog_post->category->name }}</a></li>
    <li class="ml-2">/</li>
    <li class="ml-2">{{ $blog_post->title }}</li>
</ol>

<!-- Start Content -->
<div id="content" class="section-padding">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-md-12 col-xs-12">
                <!-- Start Post -->
                <div class="blog-post single-post">
                    <!-- Post thumb -->
                    <div class="post-thumb">
                        <a href="{{ URL::current() }}">
                            <img class="img-fluid tg-post-cover" src="{{ config('app.img_url') }}blog/{{ $blog_post->cover }}" alt="{{ $blog_post->title }}" loading=lazy>
                        </a>
                        <div class="hover-wrap"></div>
                    </div>
                    <!-- End Post post-thumb -->

                    <!-- Post Content -->
                    <div class="post-content pb-2">
                        <ul class="list-inline cat-meta">
                            <li class="tr-cats">
                                <span class="meta-part"><a href="{{ route('blog_index', ['blog_category_slug' => $blog_post->category->slug]) }}"><i class="lni-folder"></i> <span class="tg-category">{{ $blog_post->category->name }}</span></a></span>
                            </li>
                        </ul>
                        <div class="meta">
                            <span class="meta-part"><a href="{{ $blog_post->owner->social_twitter }}"><i class="lni-user"></i> <span class="tg-author">{{ $blog_post->owner->name }}</span></a></span>
                            <span class="tg-created d-none">{{ $blog_post->created_at->timestamp }}</span>
                            <span class="meta-part text-secondary"><i class="lni-pencil"></i> {{ $blog_post->created_at->format('d/m/Y') }}</span>
                            <span class="meta-part text-secondary"><i class="lni-alarm-clock"></i> {{ ceil((strlen($blog_post->body) / 30) / 60) }} minutos</span>
                            <span class="meta-part text-secondary"><i class="lni-eye"></i> {{ $blog_post->hits }}</span>
                            <span class="meta-part text-secondary"><a href="{{ post_url_amp($blog_post) }}">⚡</a></span>
                            <span class="meta-part"><a href="{{ post_url($blog_post) }}#disqus_thread"><i class="lni-comments-alt"></i> Comentarios</a></span>
                        </div>

                        <div class="entry-summary">
                            {!! nl2br($blog_post->body) !!}
                        </div>

                        <div class="share-items">
                            <ul class="list-inline">
                                <li>Compartir: </li>
                                <li class="fb-share"><a href="{{ route('share', ['network' => 'facebook', 'url' => base64_encode(URL::current()), 'text' => base64_encode($blog_post->title)]) }}" target="_blank"><i class="lni-facebook-filled"></i></a></li>
                                <li class="tw-share"><a href="{{ route('share', ['network' => 'twitter', 'url' => base64_encode(URL::current()), 'text' => base64_encode($blog_post->title)]) }}" target="_blank"><i class="lni-twitter-filled"></i></a></li>
                                <li class="li-share"><a href="{{ route('share', ['network' => 'linkedin', 'url' => base64_encode(URL::current()), 'text' => base64_encode($blog_post->title)]) }}" target="_blank"><i class="lni-linkedin-filled"></i></a></li>
                                <li class="te-share"><a href="{{ 'https://telegram.me/share/url?url=https://t.me/iv?url=' . urlencode(URL::current() . '&rhash=0929b8713a7588') }}" target="_blank"><i class="lni-telegram"></i></a></li>
                            </ul>
                        </div>

                    </div>
                    <!-- Post Content -->
                </div>
                <!-- End Post -->

                <!-- Author Bio -->
                @include('blog.author-bio')
                <!-- End Author Bio -->

                <div id="disqus_thread"></div>

            </div>
            @include('blog.sidebar')
        </div>
    </div>
</div>
<!-- End Content -->

@push('script')
<script>
    /**
     *  RECOMMENDED CONFIGURATION VARIABLES: EDIT AND UNCOMMENT THE SECTION BELOW TO INSERT DYNAMIC VALUES FROM YOUR PLATFORM OR CMS.
     *  LEARN WHY DEFINING THESE VARIABLES IS IMPORTANT: https://disqus.com/admin/universalcode/#configuration-variables*/
    var disqus_config = function() {
        this.page.url = "{{ URL::current() }}"; // Replace PAGE_URL with your page's canonical URL variable
        this.page.identifier = "{{ $blog_post->id }}"; // Replace PAGE_IDENTIFIER with your page's unique identifier variable
    };
    (function() { // DON'T EDIT BELOW THIS LINE
        var d = document,
            s = d.createElement('script');
        s.src = 'https://bachecubano.disqus.com/embed.js';
        s.setAttribute('data-timestamp', +new Date());
        (d.head || d.body).appendChild(s);
    })();
</script>
<noscript>Please enable JavaScript to view the <a href="https://disqus.com/?ref_noscript">comentarios vía Disqus.</a></noscript>
@endpush

@endsection