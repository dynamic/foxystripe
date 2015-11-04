/**
 * Created by nhorstmeier on 2/18/15.
 */
jQuery(function () {

    var trigger = '.foxycartOptionsContainer .dropdown .middleColumn select',
        shownPrice = '#Form_PurchaseForm_submitPrice',
        selects = '#Form_PurchaseForm select',
        initialPrice = jQuery(shownPrice).html().replace('$', '');

    jQuery(trigger).change(function () {
        refreshAddToCartPrice();
    });

    function refreshAddToCartPrice() {
        var price = jQuery(shownPrice).html(),
            newProductPrice = parseFloat(initialPrice);

        jQuery(selects).each(function () {

            if (jQuery(this).attr('id') == 'qty') {
                // todo: modify newProductPrice by Quantity?

            } else {
                var currentOption = jQuery(this).val();
                //get an array of the modifiers
                currentOption = currentOption.substring(currentOption.lastIndexOf('{') + 1, currentOption.lastIndexOf('}')).split('|');

                //build a different array of key-value pairs, options[p,c,w] = value
                //more reliable than hoping price is the first array index of currentOption..
                var options = [];
                for (i = 0; i < currentOption.length; i++) {
                    var k = currentOption[i].substr(0, 1),
                        val = currentOption[i].substr(1);
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
        jQuery(shownPrice).html('$' + newProductPrice.toFixed(2));
    }

    if (jQuery(trigger).length > 0) refreshAddToCartPrice();
});
