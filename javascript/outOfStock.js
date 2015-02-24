var unavailable = jQuery('.unavailableText'),
	submit = jQuery('#Form_PurchaseForm_action_');

unavailable.hide();
jQuery('option:disabled').each(function(){
	jQuery(this).prop('disabled', false).addClass('outOfStock').append(document.createTextNode(" (out of stock)"));
});

if (jQuery('option:selected').hasClass('outOfStock')) {
	submit.hide();
	unavailable.show();
}

jQuery('select').on('change', function() {

	var OutOfStock = jQuery('option:selected').hasClass('outOfStock');

	if (OutOfStock) {
		submit.hide();
		unavailable.show();
	} else {
		submit.show();
		unavailable.remove();
	}
});
