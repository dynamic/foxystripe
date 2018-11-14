;(function ($) {
	var field = $("input[name='x:visibleQuantity']"),
		quantityField = $("input[name='quantity']"),
		getLink = function (element) {
			return element.parent().find("input[name='x:visibleQuantity']").data('link');
		},
		getCode = function (element) {
			return element.parent().find("input[name='x:visibleQuantity']").data('code');
		},
		getId = function (element) {
			return element.parent().find("input[name='x:visibleQuantity']").data('id');
		},
		disableSubmit = function (element) {
			element.parent().parent().parent().find('.fs-add-to-cart-button').attr('disabled', true);
		},
		enableSubmit = function (element) {
			element.parent().parent().parent().find('.fs-add-to-cart-button').attr('disabled', false);
		},
		queryNewValue = function (code, newValue, link, id, clicked) {
			var quantData = {
				'code': code,
				'value': newValue,
				'id': id,
				'isAjax': 1
			};

			$.ajax({
				type: 'get',
				url: link + '?' + $.param(quantData),
			}).done(function (response) {
				clicked.parent().parent().parent().parent().find("input[name='quantity']").val(response);
				enableSubmit(clicked);
			}).fail(function (xhr) {
				console.log('Error: ' + xhr.responseText);
			});//*/
		};

	$(document).on('click', 'button.increase', function (event) {
		var visibleQuantity = $(this).parent().parent().find("input[name='x:visibleQuantity']"),
			currentVal = visibleQuantity.val(),
			newValue = parseInt(currentVal) + 1;

		disableSubmit($(this));
		queryNewValue(getCode($(this)), newValue, getLink($(this)), getId($(this)), $(this));
		visibleQuantity.val(newValue);
	});

	$(document).on('click', 'button.reduced', function (event) {
		var visibleQuantity = $(this).parent().parent().find("input[name='x:visibleQuantity']"),
			currentVal = visibleQuantity.val(),
			newValue = parseInt(currentVal) - 1;

		if (currentVal > 1) {
			disableSubmit($(this));
			queryNewValue(getCode($(this)), newValue, getLink($(this)), getId($(this)), $(this));
			visibleQuantity.val(newValue);
		}
	});
}(jQuery));
