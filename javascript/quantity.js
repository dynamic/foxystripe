;(function ($) {
	var field = $("input[name='x:visibleQuantity']"),
		quantityField = $("input[name='quantity']"),
		getLink = function (element) {
			return element.parent().find("input[name='x:visibleQuantity']").data('link');
		},
		getLinkURL = function (link, data) {
			var delimiter = '?';
			if (link.indexOf('?') != -1) {
				delimiter = '&';
			}
			return link + delimiter + $.param(data)
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
		getVisibleQuantityField = function (element) {
			return element.parent().parent().find("input[name='x:visibleQuantity']");
		},
		disableIncreaseButton = function(element) {
			element.parent().find("button.increase").attr('disabled', true);
		},
		enableIncreaseButton = function(element) {
			element.parent().find("button.increase").attr('disabled', false);
		},
		hideButtons = function(element) {
			element.parent().find("button.increase, button.reduced")
				.attr('disabled', true)
				.addClass('hidden');
		},
		outOfStock = function(element) {
			var form = element.parents('form[id^=FoxyStripePurchaseForm_PurchaseForm_]')
			var id = form.attr('id');

			form.find('fieldset')
				.html('<h4 id="' + id + '_unavailableText">Currently Out of Stock</h4>');
			form.find('input[name=action_x\\:submit]').remove();
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
				url: getLinkURL(link, quantData),
			}).done(function (response) {
				var data = JSON.parse(response);

				if (data.hasOwnProperty('limit')) {
					updateLimit(clicked, data.limit);

					var visibleQuantity = getVisibleQuantityField(clicked);
					if (data.limit < visibleQuantity.val()) {
						visibleQuantity.val(data.limit);
					}

					if (data.limit == 0) {
						outOfStock(clicked);
					} else if (data.limit == 1) {
						hideButtons(clicked);
					} else if (data.limit == data.quantity) {
						disableIncreaseButton(clicked);
					} else {
						enableIncreaseButton(clicked);
					}
				}

				clicked.parent().parent().parent().parent().find("input[name='quantity']")
					.val(data.quantityGenerated);
				enableSubmit(clicked);
			}).fail(function (xhr) {
				// because the form field no longer exists if it is out of stock
				if (xhr.status == 404 &&
					xhr.responseText == "I can't handle sub-URLs on class SilverStripe\\Forms\\FormRequestHandler."
				) {
					outOfStock(clicked);
				} else {
					console.log('Error: ' + xhr.responseText);
				}
			});
		};

	$(document).on('click', 'button.increase', function (event) {
		var visibleQuantity = getVisibleQuantityField($(this)),
			currentVal = visibleQuantity.val(),
			newValue = parseInt(currentVal) + 1;

		disableSubmit($(this));
		queryNewValue(getCode($(this)), newValue, getLink($(this)), getId($(this)), $(this));
		visibleQuantity.val(newValue);
	});

	$(document).on('click', 'button.reduced', function (event) {
		var visibleQuantity = getVisibleQuantityField($(this)),
			currentVal = visibleQuantity.val(),
			newValue = parseInt(currentVal) - 1;

		if (currentVal > 1) {
			disableSubmit($(this));
			queryNewValue(getCode($(this)), newValue, getLink($(this)), getId($(this)), $(this));
			visibleQuantity.val(newValue);
		}
	});

	$(document).ready(function() {
		$('button.increase').each(function() {
			var limit = getLimit($(this));
			if (limit == 1) {
				hideButtons($(this));
			} else if (limit == 0) {
				outOfStock($(this));
			}
		});
	});
}(jQuery));
