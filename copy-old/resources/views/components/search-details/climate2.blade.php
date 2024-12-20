<div class="row set_current_slider m-1" id="{{$name}}_range_section">
    <div class="form-group col p-3 custom-box-shadow-1">
        <div class="d-flex my-2 mx-2">
            <i class="{{config('custom.details_search_options.'.$name.'.icon')}} fa-2x me-3" title="{{config('custom.details_search_options.'.$name.'.title')}}"></i><label>{{config('custom.details_search_options.'.$name.'.title')}}</label>
        </div>
        <div class="price-input">
            <div class="field">
                <span>Min</span>
                <input id="{{$name}}_min" type="text" class="input-min" value="{{config('custom.details_search_options.'.$name.'.min')}}" min="{{config('custom.details_search_options.'.$name.'.min')}}" max="{{config('custom.details_search_options.'.$name.'.max')}}" disabled>
            </div>
            <div class="separator">-</div>
            <div class="field">
                <span>Max</span>
                <input id="{{$name}}_max" type="text" class="input-max" value="{{config('custom.details_search_options.'.$name.'.max')}}" disabled>
            </div>
        </div>
        <div class="set_input_field" id="{{$name}}_range_slider" data-slider-name="{{$name}}"></div>
    </div>
</div>
@push('js')
<script>
    rangeSlider(document.querySelector('#{{$name}}_range_slider'), {
        value: [{{config('custom.details_search_options.'.$name.'.min')}}, {{config('custom.details_search_options.'.$name.'.max')}}],
        min:{{config('custom.details_search_options.'.$name.'.min')}},
        max: {{config('custom.details_search_options.'.$name.'.max')}},
        step: {{config('custom.details_search_options.'.$name.'.step')}},
        thumbsDisabled: [false, false],
    })
</script>
@endpush
