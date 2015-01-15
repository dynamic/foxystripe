jQuery('option.outOfStock').prop('disabled', true)

if (jQuery('option:selected').hasClass('outOfStock')) {
	jQuery('option.outOfStock').append(document.createTextNode(" (out of stock)"));
	jQuery('.field.checkoutbtn input').hide();
	jQuery('.submitPrice').after("<p class='unavailableText'><strong><em>Currently out of stock</em></strong></p>");
};

jQuery('select').on('change', function() {
	
	var OutOfStock = jQuery('option:selected').hasClass('outOfStock');
	
	if (OutOfStock) {
		jQuery('.field.checkoutbtn input').hide();
		if (jQuery('p.unavailableText').length > 0) {

		} else {
			jQuery('.submitPrice').after("<p class='unavailableText'><strong><em>Currently out of stock</em></strong></p>");
		};
	} else {
		jQuery('.field.checkoutbtn input').show();
		jQuery('p.unavailableText').remove();
	}
});