/**
 * Created by jsirish on 1/3/17.
 */
$(window).on('load', function(){
	var form = $('form[id^="FoxyStripePurchaseForm_PurchaseForm"]'),
		trigger = $('#visiblePrice'),
		price = $('#price')
		shownPrice = $('[id*="submitPrice"]');

	$(trigger).change(function () {
		refreshAddToCartPrice();
	});

	function refreshAddToCartPrice()
	{
		var amount = $(trigger).val().replace('$', '');
		shownPrice.html('$' + parseFloat(amount).toFixed(2));

		$.ajax({
			type: 'GET',
			data: 'Price=' + $(trigger).val(),
			url: form.data('updateurl'),
			success: function (options) {
				price.attr('value', options.Price);
			},
			complete: function () {
				//form.submit();
			},
			error: function (er) {
				console.log(er.responseText);
			}
		});
	}

	if ($(trigger).length > 0) {
		refreshAddToCartPrice();
	}

	$(form).validate({
		rules: {
			price: {
				required: true
			}
		}
	});
});
