@extends('user.layout')

@section('user_section')

<div class="page-content">
    <div class="inner-box">
        <div class="dashboard-box">
            <h2 class="dashbord-title">Mis publicaciones</h2>
        </div>
        <div class="dashboard-wrapper">
            <div class="row">
                <div class="col-12 mb-2">
                    <div class="row">
                        <div class="col-2  text-center">
                            Visitas
                        </div>
                        <div class="col-8">
                            Título
                        </div>
                        <div class="col-2">
                            Operaciones
                        </div>
                    </div>
                </div>
                @php
                $total_hits = 0;
                @endphp
                @foreach($latest_blog_post as $blog_post)
                <div class="col-12">
                    <div class="row">
                        <div class="col-2 text-center">
                            {{ $blog_post->hits }}
                        </div>
                        <div class="col-8">
                            <a href="{{ post_url($blog_post) }}">{{ $blog_post->title }}</a>
                        </div>
                        <div class="col-2">
                            <a class="btn btn-warning btn-block btn-sm mb-5 mt-0" href="{{ route('blog_post_edit', ['post_id' => $blog_post->id]) }}">Editar</a>
                        </div>
                    </div>
                </div>
                @php
                $total_hits += $blog_post->hits;
                @endphp
                @endforeach
                <div class="col-12 mb-2">
                    <div class="row">
                        <div class="col-2 text-center" title="Total de visitas">
                            {{ $total_hits }}
                        </div>
                        <div class="col-8">
                        </div>
                        <div class="col-2 text-right" title="Ingresos estimados">
                            ${{ number_format($total_hits > 0 ? ($total_hits / 1000) * 0.40 : 0, 2) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection