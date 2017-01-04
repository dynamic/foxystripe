/**
 * Created by jsirish on 1/3/17.
 */
jQuery(function(){

    var trigger = '#FoxyStripePurchaseForm_PurchaseForm #price',
        shownPrice = '#FoxyStripePurchaseForm_PurchaseForm_submitPrice';

    jQuery(trigger).change(function(){
        refreshAddToCartPrice();
    });

    function refreshAddToCartPrice(){
        var amount = jQuery(trigger).val().replace('$','');
        jQuery(shownPrice).html('$'+parseFloat(amount).toFixed(2));
    }

    if(jQuery(trigger).length > 0) refreshAddToCartPrice();
});
