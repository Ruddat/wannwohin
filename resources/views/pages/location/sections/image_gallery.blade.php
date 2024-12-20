<section id="erleben" class="section section-no-border bg-color-primary m-0">
{{--<section id="experience" class="section section-secondary section-no-border m-0 pt-0">--}}
    <div class="container">
        <div class="row">
            <div class="row col-12">
                <div class="col-12">
                    <h2 class="text-color-dark font-weight-extra-bold text-end">Urlaubsfotos von {{ $location->title }}</h2>
                </div>
                <div class="col">
                    <section class="timeline custom-timeline" id="timeline">
                        <div class="timeline-body">
{{--                        beginn image--}}
                            <div class="post-image ms-0">
                                <div class="lightbox" data-plugin-options="{'delegate': 'a', 'type': 'image', 'gallery': {'enabled': true}, 'mainClass': 'mfp-with-zoom', 'zoom': {'enabled': true, 'duration': 300}}">
                                    <div class="row mx-0">

                                        @foreach($location_image_gallery as $image)

                                            <div class="col-6 col-md-4 p-0">
                                                <a href="{{$image }}">
														<span class="thumb-info thumb-info-no-borders thumb-info-centered-icons">
															<span class="thumb-info-wrapper">
																<img src="{{$image }}" class="img-fluid" alt="">
																<span class="thumb-info-action">
																	<span class="thumb-info-action-icon thumb-info-action-icon-light"><i class="fas fa-plus text-dark"></i></span>
																</span>
															</span>
														</span>
                                                </a>
                                            </div>
                                        @endforeach

                                    </div>
                                </div>
                            </div>
{{--                            end image--}}

                        </div>
                    </section>
                </div>
            </div>
            <div class="col-2"></div>
        </div>
    </div>
</section>


