{{--<div class="modal fade" id="largeModal" tabindex="-1" aria-labelledby="largeModalLabel" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="largeModalLabel">Large Modal Title</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur pellentesque neque eget diam posuere porta. Quisque ut nulla at nunc <a href="#">vehicula</a> lacinia. Proin adipiscing porta tellus, ut feugiat nibh adipiscing sit amet. In eu justo a felis faucibus ornare vel id metus. Vestibulum ante ipsum primis in faucibus.</p>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur pellentesque neque eget diam posuere porta. Quisque ut nulla at nunc <a href="#">vehicula</a> lacinia. Proin adipiscing porta tellus, ut feugiat nibh adipiscing sit amet. In eu justo a felis faucibus ornare vel id metus. Vestibulum ante ipsum primis in faucibus.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>--}}





<div class="modal fade" id="erleben_picture1_modal" tabindex="-1" aria-labelledby="defaultModalLabel" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="defaultModalLabel">Was kann man in {{ $location->title }} erleben?</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="row"  style="min-height: 400px">
                    <div class="col-12">
                        <div style="background-repeat: no-repeat; background-size :cover;background-position :center;background-image: url('{{ asset("img/location_main_img/{$location->continent->alias}/{$location->country->alias}/{$location->alias}/klimatabelle-{$location->alias}.webp") }}')" class="full-width h-100 figure-img img-fluid custom-border d-flex">
                            <div class="mt-auto ms-auto">
                                <div class="bg-opacity-75 bg-white rounded text-dark p-2 m-2">{{ $location->text_pic3 }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
{{--            <div class="modal-footer">--}}
{{--                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>--}}
{{--            </div>--}}
        </div>
    </div>
</div>

<div class="modal fade" id="erleben_picture2_modal" tabindex="-1" aria-labelledby="defaultModalLabel" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="defaultModalLabel">Was kann man in {{ $location->title }} erleben?</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="row"  style="min-height: 400px">
                    <div class="col-12">
                        <div style="background-repeat: no-repeat; background-size :cover;background-position :center;background-image: url('{{ asset("img/location_main_img/{$location->continent->alias}/{$location->country->alias}/{$location->alias}/klima-{$location->alias}.webp") }}')" class="full-width h-100 figure-img img-fluid custom-border d-flex">
                            <div class="mt-auto ms-auto">
                                <div class="bg-opacity-75 bg-white rounded text-dark p-2 m-2">{{ $location->text_pic2 }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
{{--            <div class="modal-footer">--}}
{{--                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>--}}
{{--            </div>--}}
        </div>
    </div>
</div>
