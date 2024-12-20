<section class="section section-no-border section-parallax bg-transparent custom-section-padding-1 custom-position-1 custom-xs-bg-size-cover parallax-no-overflow m-0" data-plugin-parallax data-plugin-options="{'speed': 1.5}" data-image-src="{{ $bgImg }}">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 position-relative custom-sm-margin-bottom-1">
                <img src="{{ $mainImg }}" class="img-fluid custom-border custom-image-position-2 custom-box-shadow-4" alt />
            </div>
            <div class="col-lg-6 col-xl-5">
                <span class="panorama-text"> {!!  $mainText !!}</span>
            </div>
            <div class="col-lg-2 col-xl-3 d-none d-lg-block">
                <img src="{{ asset('assets/img/pages/main/mouse.png') }}" custom-anim class="img-fluid custom-image-pos-1" alt />
            </div>
        </div>
    </div>
</section>

<div class="custom-about-me-links bg-color-light">
    <div class="container">
        <div class="row justify-content-end">
            <div class="col-lg-3 text-center custom-xs-border-bottom p-0">
                {!!  \ThemeTextHelper::SelectContinents()  !!}
            </div>
            <div class="col-lg-2 text-center custom-xs-border-bottom p-0">
                <a data-hash href="#say-hello" class="text-decoration-none">
                        <span class="custom-nav-button custom-divisors text-color-dark">
                            <i class="icon-envelope-open icons text-color-primary"></i> Reiseziele im
                        </span>
                </a>
            </div>
            <div class="col-lg-3 text-center p-0">
                <a href="#" class="text-decoration-none">
                    <span class="custom-nav-button text-color-dark"><i class="icon-cloud-download icons text-color-primary"></i> Neuen Ort vorschlagen123</span>
                </a>
            </div>
        </div>
    </div>
</div>

<x-breadcrumb
    :hide-path="$hide_path ?? false"
/>
