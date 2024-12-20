// let range_elements = ['night_temp','daily_temp'];
// // range_elements.forEach()
//
// jQuery.each( range_elements, function( i, val ) {
//     alert(val);
//     let current_slider_id = 'details_search_climate_' + val;
//     let daily_temp_rangeInput = document.querySelectorAll("#details_search_climate #" + current_slider_id +  " .range-input input"),
//         daily_temp_priceInput = document.querySelectorAll("#details_search_climate #" + current_slider_id +  "  .price-input input"),
//         daily_temp_range = document.querySelector("#details_search_climate #" + current_slider_id +  " .slider .progress"),
//         daily_temp_priceGap = $("#details_search_climate #" + current_slider_id).attr('step');
//
//     daily_temp_priceInput.forEach(input =>{
//
//         input.addEventListener("input", e =>{
//             let minPrice = parseInt(daily_temp_priceInput[0].value),
//                 maxPrice = parseInt(daily_temp_priceInput[1].value);
//
//             if((maxPrice - minPrice >= daily_temp_priceGap) && maxPrice <= daily_temp_rangeInput[1].max){
//                 if(e.target.className === "input-min"){
//                     daily_temp_rangeInput[0].value = minPrice;
//                     daily_temp_range.style.left = ((minPrice / daily_temp_rangeInput[0].max) * 100) + "%";
//
//                 }else{
//                     daily_temp_rangeInput[1].value = maxPrice;
//                     daily_temp_range.style.right = 100 - (maxPrice / daily_temp_rangeInput[1].max) * 100 + "%";
//                 }
//             }
//         });
//     });
//
//     daily_temp_rangeInput.forEach(input =>{
//         input.addEventListener("input", e =>{
//             let minVal = parseInt(daily_temp_rangeInput[0].value),
//                 maxVal = parseInt(daily_temp_rangeInput[1].value);
//
//             if((maxVal - minVal) < daily_temp_priceGap){
//                 if(e.target.className === "range-min"){
//                     daily_temp_rangeInput[0].value = maxVal - daily_temp_priceGap
//                 }else{
//                     daily_temp_rangeInput[1].value = minVal + daily_temp_priceGap;
//                 }
//             }else{
//                 daily_temp_priceInput[0].value = minVal;
//                 daily_temp_priceInput[1].value = maxVal;
//                 daily_temp_range.style.left = ((minVal / daily_temp_rangeInput[0].max) * 100) + "%";
//                 daily_temp_range.style.right = 100 - (maxVal / daily_temp_rangeInput[1].max) * 100 + "%";
//             }
//         });
//     });
//
//
// });


let current_slider_id = 'details_search_climate_daily_temp';
let daily_temp_rangeInput = document.querySelectorAll("#details_search_climate #" + current_slider_id +  " .range-input input"),
    daily_temp_priceInput = document.querySelectorAll("#details_search_climate #" + current_slider_id +  "  .price-input input"),
    daily_temp_range = document.querySelector("#details_search_climate #" + current_slider_id +  " .slider .progress"),
    daily_temp_priceGap = $("#details_search_climate #" + current_slider_id).attr('step');


/*$('.set_current_slider').on('click', function () {
    // $('.sidebar-left-nav').toggleClass('closed');
    // let status = $('#sidebar-arrow').hasClass('fa-arrow-right');
    // $('#sidebar-arrow').toggleClass('fa-arrow-right', !status);
    // $('#sidebar-arrow').toggleClass('fa-arrow-left', status);
    // window.location = $(this).attr('href') + "/month/" + $('#urlaub_type_month').find(":selected").val();
    alert($(this).attr('id') );
    alert(priceGap)
    current_slider_id = $(this).attr('id')
    priceGap = $(this).attr('step');
    rangeInput = document.querySelectorAll("#details_search_climate #" + current_slider_id +  " .range-input input");
    priceInput = document.querySelectorAll("#details_search_climate #" + current_slider_id +  "  .price-input input");
    range = document.querySelector("#details_search_climate #" + current_slider_id +  " .slider .progress");
})*/

daily_temp_priceInput.forEach(input =>{

    input.addEventListener("input", e =>{
        let minPrice = parseInt(daily_temp_priceInput[0].value),
            maxPrice = parseInt(daily_temp_priceInput[1].value);

        if((maxPrice - minPrice >= daily_temp_priceGap) && maxPrice <= daily_temp_rangeInput[1].max){
            if(e.target.className === "input-min"){
                daily_temp_rangeInput[0].value = minPrice;
                daily_temp_range.style.left = ((minPrice / daily_temp_rangeInput[0].max) * 100) + "%";
            }else{
                daily_temp_rangeInput[1].value = maxPrice;
                daily_temp_range.style.right = 100 - (maxPrice / daily_temp_rangeInput[1].max) * 100 + "%";
            }
        }
    });
});

daily_temp_rangeInput.forEach(input =>{
    input.addEventListener("input", e =>{
        let minVal = parseInt(daily_temp_rangeInput[0].value),
            maxVal = parseInt(daily_temp_rangeInput[1].value);

        if((maxVal - minVal) < daily_temp_priceGap){
            if(e.target.className === "range-min"){
                daily_temp_rangeInput[0].value = maxVal - daily_temp_priceGap
            }else{
                daily_temp_rangeInput[1].value = minVal + daily_temp_priceGap;
            }
        }else{
            daily_temp_priceInput[0].value = minVal;
            daily_temp_priceInput[1].value = maxVal;
            daily_temp_range.style.left = ((minVal / daily_temp_rangeInput[0].max) * 100) + "%";
            daily_temp_range.style.right = 100 - (maxVal / daily_temp_rangeInput[1].max) * 100 + "%";
        }
    });
});
