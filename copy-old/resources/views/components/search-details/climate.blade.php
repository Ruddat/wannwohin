<div class="row set_current_slider m-1" id="{{$name}}_range_section">
    <div class="form-group col p-3 custom-box-shadow-1">
        <div class="d-flex my-2 mx-2">
            <i class="{{config('custom.details_search_options.'.$name.'.icon')}} fa-2x me-3" title="{{config('custom.details_search_options.'.$name.'.title')}}"></i><label>{{config('custom.details_search_options.'.$name.'.title')}}</label>
        </div>
        <div class="price-input">
            <div class="field">
                <span>Min</span>
                <input name="{{$name}}_min" type="text" class="input-min details_search_result_count" value="{{ (request()->{$name.'_min'} =='')? config('custom.details_search_options.'.$name.'.min') :  request()->{$name.'_min'} }}" readonly>
            </div>
            <div class="separator">-</div>
            <div class="field">
                <span>Max</span>
                <input name="{{$name}}_max" type="text" class="input-max details_search_result_count" value="{{ (request()->{$name.'_max'} =='')? config('custom.details_search_options.'.$name.'.max') :  request()->{$name.'_max'} }}" readonly>
            </div>
        </div>
        <div class="set_input_field details_search_result_count" id="{{$name}}_range_slider" data-slider-name="{{$name}}"></div>
    </div>
</div>
@push('js')
<script>
    rangeSlider(document.querySelector('#{{$name}}_range_slider'), {
        {{--value: [{{config('custom.details_search_options.'.$name.'.min')}}, {{config('custom.details_search_options.'.$name.'.max')}}],--}}
        value: [{{ (request()->{$name.'_min'} =='')? config('custom.details_search_options.'.$name.'.min') :  request()->{$name.'_min'} }},{{ (request()->{$name.'_max'} =='')? config('custom.details_search_options.'.$name.'.max') :  request()->{$name.'_max'} }}],
        min:{{config('custom.details_search_options.'.$name.'.min')}},
        max: {{config('custom.details_search_options.'.$name.'.max')}},
        {{--min:{{ (request()->{$name.'_min'} =='')? config('custom.details_search_options.'.$name.'.min') :  request()->{$name.'_min'} }},--}}
        {{--max:{{ (request()->{$name.'_max'} =='')? config('custom.details_search_options.'.$name.'.max') :  request()->{$name.'_max'} }},--}}
        step: {{config('custom.details_search_options.'.$name.'.step')}},
        thumbsDisabled: [false, false],
        // onInput: test_me(),
    })


    function test_me(){
        alert('fdffffffffffffffffff');
    }

</script>

@endpush
