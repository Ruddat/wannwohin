<article class="timeline-box right custom-box-shadow-2 box-shadow-2">
    <div class="row">
        <div {{ $imgContent->attributes->class(['experience-info col-lg-3 col-sm-5 bg-color-primary p-0 article-img']) }}>{!! $imgContent !!}</div>
        <div class="experience-description col-lg-9 col-sm-7 bg-color-light {{ $contentClass }}">
            {{ $slot }}
        </div>
    </div>
</article>
