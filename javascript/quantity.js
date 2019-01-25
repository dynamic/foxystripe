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
		getLimit = function (element) {
			return element.parent().find("input[name='x:visibleQuantity']").data('limit');
		},
		updateLimit = function (element, newLimit) {
			return element.parent().find("input[name='x:visibleQuantity']").data('limit', newLimit);
		},
		disableIncreaseButton = function(element) {
			element.parent().find("button.increase").attr('disabled', true);
		},
		enableIncreaseButton = function(element) {
			element.parent().find("button.increase").attr('disabled', false);
		},
		disableSubmit = function (element) {
			element.parent().parent().parent().find('.fs-add-to-cart-button').attr('disabled', true);
		},
		enableSubmit = function (element) {
			element.parent().parent().parent().find('.fs-add-to-cart-button').attr('disabled', false);
		},
		responseToQuantity = function(response) {
			return response.substr(0, response.indexOf('||'));
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
				var quantityInput = clicked.parent().parent().parent().parent().find("input[name='quantity']");
				var visibleQuantityInput = clicked.parent().parent().find("input[name='x:visibleQuantity']");

				var oldQuantity = responseToQuantity(quantityInput.val());
				var newQuantity = responseToQuantity(response);
				if (oldQuantity == newQuantity && newQuantity != getLimit(clicked)) {
					updateLimit(clicked, newQuantity);
				}

				var limit = getLimit(clicked);
				if (limit != undefined && limit != -1 && visibleQuantityInput.val() >= limit) {
					visibleQuantityInput.val(limit);
					disableIncreaseButton(clicked);
				} else {
					enableIncreaseButton(clicked);
				}

				quantityInput.val(response);
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
