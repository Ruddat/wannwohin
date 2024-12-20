$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': token
        }
    });
})

let details_search_values = {};
$('.details_search_store_value_local').on('change', function () {
    details_search_values[this.name] = this.value;
    localStorage.setItem('details_search_values', JSON.stringify(details_search_values));
});

$('.details_search_get_value_local').each( function () {
    $(this).children(`option[value=${JSON.parse(localStorage.getItem('details_search_values'))[this.name]}]`).attr('selected', true);
});

$('.details_search_result_count').on('change', function () {
    show_loading();
    refresh_details_search_result();
})


$(document).on('click', '.range-slider__thumb[aria-valuenow]', function(e) {
    show_loading();
    refresh_details_search_result();
});

function show_loading(){
    $('span.refresh_details_search_result').html('<div class="spinner-border spinner-border-sm text-primary" role="status"><span class="sr-only">Loading...</span></div>');
}

function refresh_details_search_result(){
    $.ajax({
        type: 'POST',
        url: "https://www.wann-wohin.de/details_search/count",
        data: $('#details_search_form').serialize(),
        /*data: {
            country_id : country_id
        },*/
        success: function (data) {
            $('span.refresh_details_search_result').text(data);
        },
        error: function (data) {
            alert('error');
        },
        complete: function (data) {

        }
    });
}
