{{--<div class="modal fade" id="google_map_modal" tabindex="-1" aria-labelledby="defaultModalLabel" style="display: none;" aria-hidden="true">--}}
{{--    <div class="modal-dialog modal-lg">--}}
{{--        <div class="modal-content">--}}
{{--            <div class="modal-header">--}}
{{--                <h4 class="modal-title" id="defaultModalLabel">Position auf der Karte</h4>--}}
{{--                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true">×</button>--}}
{{--            </div>--}}
{{--            <div class="modal-body">--}}
{{--                <div class="row"  style="min-height: 400px">--}}
{{--                    <div class="col-12">--}}

{{--                        <iframe--}}
{{--                            width="600"--}}
{{--                            height="450"--}}
{{--                            style="border:0"--}}
{{--                            loading="lazy"--}}
{{--                            allowfullscreen--}}
{{--                            referrerpolicy="no-referrer-when-downgrade"--}}
{{--                            src="https://www.google.com/maps/dir/{{$country_alias}}/{{$location->alias }}">--}}
{{--                        </iframe>--}}

{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div class="modal-footer">--}}
{{--                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}


<div class="modal fade" id="google_map_modal" tabindex="-1" aria-labelledby="defaultModalLabel" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="defaultModalLabel">Position auf der Karte</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="row"  style="min-height: 400px">
                    <div class="col-12">
                        <div class="mapouter">
                            <div class="gmap_canvas">
                                <iframe width="770" height="510" id="gmap_canvas" src="https://maps.google.com/maps?q={{$location->alias}},{{$country_alias}}&t=&z=10&ie=UTF8&iwloc=&output=embed" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>
                                </div></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


