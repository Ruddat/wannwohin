$('.quick_search_result_count').on('change', function () {
    refresh_quick_search_result();
})


function refresh_quick_search_result(){
    $.ajax({
        type: 'POST',
        url: "https://www.wann-wohin.de/quick_serach/count",
        data: $('#quick_search_form').serialize(),
        /*data: {
            country_id : country_id
        },*/
        success: function (data) {
            $('span.refresh_quick_search_result').text(data);

        },
        error: function (data) {
            alert('error');
        },
        complete: function (data) {

        }
    });
}
