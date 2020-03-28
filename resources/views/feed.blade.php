<?= '<?xml version="1.0" encoding="UTF-8" ?>'; ?>
<rss version="2.0">
    <channel>
        <title>Blog Bachecubano</title>
        <link>https://www.bachecubano.com/blog</link>
        <description>Noticias, tutoriales, guías y consejos para el comercio electrónico en Cuba</description>
        @foreach($blog_posts as $blog_post)
        <item>
            <title>{{ $blog_post->title }}</title>
            <link>{{ post_url($blog_post) }}</link>
            <description>{{ Str::words($blog_post->body, 20) }}</description>
        </item>
        @endForeach
    </channel>
</rss>