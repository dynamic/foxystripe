;(function ($) {
    var unavailable = $('.unavailable-text'),
        priceDisplay = $('.submit-price'),
        formName = "#$FormName_action",
        submit = $(formName),
        showElement = function (element) {
            element.removeClass('hidden');
        },
        hideElement = function (element) {
            element.addClass('hidden');
        };

    hideElement(unavailable);

    $('option:disabled').each(function () {
        $(this).prop('disabled', false).addClass('outOfStock').append(document.createTextNode(" (out of stock)"));
    });

    if ($('option:selected').hasClass('outOfStock')) {
        hideElement(submit);
        hideElement(priceDisplay);
        showElement(unavailable);
    }

    $('select').on('change', function () {

        var outOfStock = $('option:selected').hasClass('outOfStock');

        if (outOfStock) {
            hideElement(submit);
            hideElement(priceDisplay);
            showElement(unavailable);
        } else {
            hideElement(unavailable);
            showElement(submit);
            showElement(priceDisplay);
        }
    });
})(jQuery);
