@extends('layouts.app')

@section('content')

<!-- Page Header Start -->
<div class="page-header">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="breadcrumb-wrapper">
                    <a href="{{ URL::current() }}">
                        <h1 class="product-title">{{ $seo_data['title'] }}</h1>
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
    @if(isset($category->name))
    <li class="ml-2">/</li>
    <li class="current ml-2">{{ $category->name }}</li>
    @endif
</ol>

<!-- Start Content -->
<div id="content" class="section-padding">
    <div class="container">
        <div class="row">

            <div class="col-lg-8 col-md-12 col-xs-12">

                @if(isset($posts) && $posts->count() > 0)
                @foreach($posts as $blog_post)

                <!-- Start Post -->
                <div class="blog-post">
                    <!-- Post thumb -->
                    <div class="post-thumb">
                        <a href="{{ post_url($blog_post) }}"><img class="img-fluid" src="{{ config('app.img_url') }}blog/{{ $blog_post->cover }}" alt="{{ $blog_post->title }}"></a>
                        <div class="hover-wrap"></div>
                    </div>
                    <!-- End Post post-thumb -->
                    <!-- Post Content -->
                    <div class="post-content">
                        <ul class="list-inline cat-meta">
                            <li class="tr-cats"><a href="{{ route('blog_index', ['blog_category_slug' => $blog_post->category->slug]) }}"> {{ $blog_post->category->name }}</a></li>
                        </ul>
                        <h2 class="post-title"><a href="{{ post_url($blog_post) }}">{{ $blog_post->title }}</a></h2>
                        <div class="meta">
                            <span class="meta-part"><a href="{{ $blog_post->owner->social_twitter }}"><i class="lni-user"></i> {{ $blog_post->owner->name }}</a></span>
                            <span class="meta-part text-secondary"><i class="lni-pencil"></i> {{ $blog_post->updated_at->format('d/m/Y') }}</span>
                            <span class="meta-part text-secondary"><i class="lni-alarm-clock"></i> {{ ceil((strlen($blog_post->body) / 30) / 60) }} minutos</span>
                            <span class="meta-part text-secondary"><i class="lni-eye"></i> {{ $blog_post->hits }}</span>
                            <span class="meta-part"><a href="{{ post_url($blog_post) }}#disqus_thread"><i class="lni-comments-alt"></i> Comentarios</a></span>
                        </div>
                        <div class="entry-summary">
                            {!! Str::words($blog_post->body, 100) !!}
                        </div>
                    </div>
                    <!-- Post Content -->
                </div>
                <!-- End Post -->

                @endforeach
                @endif

                @include('gads.h')

                <!-- Start Post -->
                <div class="blog-post quote-post">
                    <div class="quote-wrap">
                        <i class="fa fa-quote-left"></i>
                        <blockquote class="text-center">
                            ¿Quieres escribir para nuestro Blog?
                            <br>
                            Queremos escucharte, <a href="https://www.bachecubano.com/otros/empleos/quieres-escribir-para-el-blog-de-bachecubano/174014">aplica aquí</a>
                        </blockquote>
                        <i class="fa fa-quote-right"></i>
                    </div>
                </div>
                <!-- End Post -->

            </div>

            <!-- Start Pagination -->
            @if(isset($posts) && $posts->count() > 0)
            <div class="pagination-bar">
                {{ $posts->links() }}
            </div>
            @endif
            <!-- End Pagination -->

            @include('blog.sidebar')

        </div>
    </div>
</div>
<!-- End Content -->

@endsection