$(function() {

    // Initiate Slider
    $('#slider-range').slider({
        range: true,
        min: 10000,
        max: 110000,
        step: 100,
        values: [45000, 75000]
    });

    // Move the range wrapper into the generated divs
    $('.ui-slider-range').append($('.range-wrapper'));

    // Apply initial values to the range container
    $('.range').html('<span class="range-value"><sup>$</sup>' + $('#slider-range').slider("values", 0).toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") + '</span><span class="range-divider"></span><span class="range-value"><sup>$</sup>' + $("#slider-range").slider("values", 1).toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") + '</span>');

    // Show the gears on press of the handles
    $('.ui-slider-handle, .ui-slider-range').on('mousedown', function() {
        $('.gear-large').addClass('active');
    });

    // Hide the gears when the mouse is released
    // Done on document just incase the user hovers off of the handle
    $(document).on('mouseup', function() {
        if ($('.gear-large').hasClass('active')) {
            $('.gear-large').removeClass('active');
        }
    });

    // Rotate the gears
    var gearOneAngle = 0,
        gearTwoAngle = 0,
        rangeWidth = $('.ui-slider-range').css('width');

    $('.gear-one').css('transform', 'rotate(' + gearOneAngle + 'deg)');
    $('.gear-two').css('transform', 'rotate(' + gearTwoAngle + 'deg)');

    $('#slider-range').slider({
        slide: function(event, ui) {

            // Update the range container values upon sliding

            $('.range').html('<span class="range-value"><sup>$</sup>' + ui.values[0].toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") + '</span><span class="range-divider"></span><span class="range-value"><sup>$</sup>' + ui.values[1].toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") + '</span>');

            // Get old value
            var previousVal = parseInt($(this).data('value'));

            // Save new value
            $(this).data({
                'value': parseInt(ui.value)
            });

            // Figure out which handle is being used
            if (ui.values[0] == ui.value) {

                // Left handle
                if (previousVal > parseInt(ui.value)) {
                    // value decreased
                    gearOneAngle -= 7;
                    $('.gear-one').css('transform', 'rotate(' + gearOneAngle + 'deg)');
                } else {
                    // value increased
                    gearOneAngle += 7;
                    $('.gear-one').css('transform', 'rotate(' + gearOneAngle + 'deg)');
                }

            } else {

                // Right handle
                if (previousVal > parseInt(ui.value)) {
                    // value decreased
                    gearOneAngle -= 7;
                    $('.gear-two').css('transform', 'rotate(' + gearOneAngle + 'deg)');
                } else {
                    // value increased
                    gearOneAngle += 7;
                    $('.gear-two').css('transform', 'rotate(' + gearOneAngle + 'deg)');
                }

            }

            if (ui.values[1] === 110000) {
                if (!$('.range-alert').hasClass('active')) {
                    $('.range-alert').addClass('active');
                }
            } else {
                if ($('.range-alert').hasClass('active')) {
                    $('.range-alert').removeClass('active');
                }
            }
        }
    });

    // Prevent the range container from moving the slider
    $('.range, .range-alert').on('mousedown', function(event) {
        event.stopPropagation();
    });

});
