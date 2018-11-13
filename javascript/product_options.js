;(function ($) {

    var trigger = '.foxycartOptionsContainer .dropdown .middleColumn select',
        formName = "#$FormName",
        shownPrice = '#$FormName_submitPrice',
        selects = '#$FormName select',
        initialPrice = $(shownPrice).html().replace('$', '');

    $(trigger).change(function () {
        refreshCartPrice();
    });

    var refreshCartPrice = function refreshAddToCartPrice()
    {
        var price = $(shownPrice).html();
        var newProductPrice = parseFloat(initialPrice);

        $(selects).each(function () {

            if ($(this).attr('id') == 'qty') {
                // todo: modify newProductPrice by Quantity?
            } else {
                var currentOption = $(this).val();
                //get an array of the modifiers
                currentOption = currentOption.substring(currentOption.lastIndexOf('{') + 1, currentOption.lastIndexOf('}')).split('|');

                //build a different array of key-value pairs, options[p,c,w] = value
                //more reliable than hoping price is the first array index of currentOption..
                var options = [];
                for (i = 0; i < currentOption.length; i++) {
                    var k = currentOption[i].substr(0, 1);
                    var val = currentOption[i].substr(1);
                    options[k] = val;
                }

                if (typeof options['p'] != 'undefined') {
                    var pricemodifier = options['p'].substr(0, 1); // return +,-,:
                    if (pricemodifier == ':') {
                        newProductPrice = parseFloat(options['p'].substr(1));
                    } else {
                        newProductPrice = newProductPrice + parseFloat(options['p']);
                    }
                }
            }
        });
        $(shownPrice).html('$' + newProductPrice.toFixed(2));
    };

    if ($(trigger).length > 0) {
        refreshCartPrice();
    }
})(jQuery);
