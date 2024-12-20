$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': token
        }
    });
})

$('#sidebarButton').on('click', function () {
    $('.sidebar-left-nav').toggleClass('closed');
    let status = $('#sidebar-arrow').hasClass('fa-arrow-right');
    $('#sidebar-arrow').toggleClass('fa-arrow-right', !status);
    $('#sidebar-arrow').toggleClass('fa-arrow-left', status);
})

$(".urlaub-type-url").on('click', function (e) {
    e.stopImmediatePropagation();
    e.preventDefault();
    window.location = $(this).attr('href') + "/month/" + $('#urlaub_type_month').find(":selected").val();
})


$(".continent-select-header").on('change', function (e) {
    window.location = $(this).attr('data-url') + "/" + $('.continent-select-header').find(":selected").val();
})

$(".urlaub_type_month").on('change', function (e) {
    $(".urlaub_type_month").val($(this).val());
})



// moment.locale("de")
// moment.months().forEach((item, key) => {
//     $('#Urlaub').append('<option '+(key+1 === 7 ? 'selected' : '')+' value="'+(key+1)+'">'+item+'</option>')
// })

/*function addMonthToUrlaubTypeUrl(){
    window.location = this.href() + "/" + 5
    alert('dsdsd');
    // alert('dsdsdsd');
    // return false
    // $("#abc").attr("action", "/yourapp/" + temp).submit();
    //
    //
    // let url=this.href()
    // let month = $('#urlaub_type_month').find(":selected").val();
    // $(location).attr('href',url + '/' + month);
}*/
