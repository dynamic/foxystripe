/**
 * Created by jsirish on 1/3/17.
 */
;(function ($) {

    var form = '#FoxyStripePurchaseForm_PurchaseForm',
        trigger = $('#$Trigger'),
        shownPrice = '#FoxyStripePurchaseForm_PurchaseForm_submitPrice';

    $(trigger).change(function () {
        refreshAddToCartPrice();
    });

    function refreshAddToCartPrice()
    {
        var amount = $(trigger).val().replace('$', '');
        $(shownPrice).html('$' + parseFloat(amount).toFixed(2));
    }

    if ($(trigger).length > 0) {
        refreshAddToCartPrice();
    }

    $(form).validate({
        rules: {
            price: {
                required: true
            }
        },
        submitHandler: function (form) {
            $.ajax({
                type: 'GET',
                data: 'Price=' + $(trigger).val(),
                url: "$UpdateURL",
                success: function (options) {
                    trigger.attr('name', options.Price);
                },
                complete: function () {
                    form.submit();
                },
                error: function (er) {
                    console.log(er.responseText);
                }
            });
        }
    });

})(jQuery);
