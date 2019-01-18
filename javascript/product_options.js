;(function ($) {
	var shownPrice = $('[id*="_submitPrice"]'),
		trigger = $('.product-options'),
		isAvailable = $('[name="action_x:submit"]').length ? true : false,
		unavailable = trigger.closest('form').find('[id*="_unavailableText"]');

	$('option:disabled').each(function () {
		if ($(this).prop('disabled')) {
			$(this).addClass('outOfStock').append(document.createTextNode(" (out of stock)"));
		}
	});

	trigger.on('change', function () {
		var options = [],
			selected = $(this).val();

		if (selected.length > 0) {
			selected = selected.substring(selected.lastIndexOf('{') + 1, selected.lastIndexOf('}')).split('|')[0].split(':')[1];
		}

		$(this).each(function () {
			var currentOption = $(this).val();
			currentOption = currentOption.substring(currentOption.lastIndexOf('{') + 1, currentOption.lastIndexOf('}')).split('|');

			if (currentOption.length) {
				$.each(currentOption, function (k, v) {
					if (v !== '') {
						console.log(v.split(':')[1]);
						options[v.split(':')[1]] = v.split(':')[1];
					}
				});
			}
		});

		console.log(selected in options);

		if (selected in options) {
			shownPrice.html('$' + Number.parseFloat(options[selected]).toFixed(2));
		}
	});

	if (isAvailable === false) {
		shownPrice.addClass('hidden');
		unavailable.removeClass('hidden');
	} else {
		if (trigger.length > 0) {
			trigger.change();
		}

	}
})(jQuery);
