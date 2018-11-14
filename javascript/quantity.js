;(function ($) {
	var field = $("input[name='x:visibleQuantity']"),
		quantityField = $("input[name='quantity']"),
		increment = $('.increase'),
		decrement = $('.reduced'),
		getLink = function (element) {
			return element.parent().find("input[name='x:visibleQuantity']").data('link');
		},
		getCode = function (element) {
			return element.parent().find("input[name='x:visibleQuantity']").data('code');
		},
		disableSubmit = function (element) {
			element.parent().parent().parent().find('.fs-add-to-cart-button').attr('disabled', true);
		},
		enableSubmit = function (element) {
			element.parent().parent().parent().find('.fs-add-to-cart-button').attr('disabled', false);
		},
		queryNewValue = function (code, newValue, link, clicked) {
			var quantData = {
				'code': code,
				'value': newValue,
				'isAjax': 1
			};

			$.ajax({
				type: 'get',
				url: link + '?' + $.param(quantData),
			}).done(function (response) {
				quantityField.val(response);
				enableSubmit(clicked)
			}).fail(function (xhr) {
				console.log('Error: ' + xhr.responseText);
			});//*/
		};

	increment.on('click', function (event) {
		var currentVal = field.val(),
			newValue = parseInt(currentVal) + 1;

		disableSubmit($(this));
		queryNewValue(getCode($(this)), newValue, getLink($(this)), $(this));
		field.val(newValue);
	});

	decrement.on('click', function (event) {
		var currentVal = field.val(),
			newValue = parseInt(currentVal) - 1;

		if (currentVal > 1) {
			disableSubmit($(this));
			queryNewValue(getCode($(this)), newValue, getLink($(this)), $(this));
			field.val(newValue);
		}
	});
}(jQuery));
