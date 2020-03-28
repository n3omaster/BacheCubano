@push('style')
<style>
    .twPc-div {
        background: #fff none repeat scroll 0 0;
        border: 1px solid #e1e8ed;
        border-radius: 6px;
        width: 100%;
        /*max-width: 340px;*/
    }

    .twPc-bg {
        /*Cool Gradient Here*/
        background-image: linear-gradient(to right, #4facfe 0%, #00f2fe 100%);
        background-position: 0 50%;
        background-size: 100% auto;
        border-bottom: 1px solid #e1e8ed;
        border-radius: 4px 4px 0 0;
        height: 120px;
        width: 100%;
    }

    .twPc-block {
        display: block !important;
    }

    .twPc-button {
        margin: -35px -10px 0;
        text-align: right;
        width: 100%;
    }

    .twPc-mmg {
        margin-top: -50px;
    }

    .twPc-avatarLink {
        background-color: #fff;
        border-radius: 6px;
        display: inline-block !important;
        float: left;
        margin: -30px 5px 0 8px;
        max-width: 100%;
        padding: 1px;
        vertical-align: bottom;
    }

    .twPc-avatarImg {
        border: 2px solid #fff;
        border-radius: 7px;
        box-sizing: border-box;
        color: #fff;
        height: 72px;
        width: 72px;
    }

    .twPc-divName {
        font-size: 18px;
        font-weight: 700;
        line-height: 21px;
    }

    .twPc-divName a {
        color: white;
        text-shadow: 1px 1px 2px #000000;
    }

    .twPc-divStats {
        margin-left: 11px;
        padding: 10px 0;
    }
</style>
@endpush

<div class="container">
    <div class="row">
        <div class="twPc-div">
            <div class="twPc-bg twPc-block"></div>
            <div class="twPc-mmg">
                <a title="{{ $blog_post->owner->name }}" href="{{ $blog_post->owner->social_twitter }}" class="twPc-avatarLink">
                    {{-- Twitter profile picture? --}}
                    <img alt="{{ $blog_post->owner->name }}" src="{{ profile_logo($blog_post->owner) }}" class="twPc-avatarImg">
                </a>

                <div class="twPc-divUser">
                    <div class="twPc-divName">
                        <a href="{{ $blog_post->owner->social_twitter }}">{{ $blog_post->owner->name }}</a>
                    </div>
                    <span>
                        <a href="{{ $blog_post->owner->social_twitter }}">{{ "@" . twitter_username($blog_post->owner->social_twitter) }}</a>
                    </span>
                </div>

                <div class="twPc-divStats">
                    {!! nl2br($blog_post->owner->signature) !!}
                </div>
            </div>
        </div>
        <!-- code end -->
    </div>
</div>