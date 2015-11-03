jQuery(document).ready(function ($) {

	if (!get_agreement_val()) {
		$(".payment-methods-wrapper input[type='submit']").attr('disabled', true);
	}

	itExchange.hooks.addAction('itExchangeSW.stateUpdated', function () {

		if (get_agreement_val()) {
			$("#agree-terms").prop('checked', true);
		} else {
			$(".payment-methods-wrapper input[type='submit']").attr('disabled', true);
		}
	});

	$(document).on('click', '#show-terms', function () {

		var terms = $(".terms");

		if (terms.is(':visible')) {

			$(this).text(ITETOS.show);

			terms.hide();
		} else {

			$(this).text(ITETOS.hide);

			terms.show();
		}
	});

	$(document).on('click', '#agree-terms', function () {

		if ($(this).is(':checked')) {

			store_agreement_val(true);

			$(".payment-methods-wrapper input[type='submit']").attr('disabled', false);
		} else {
			$(".payment-methods-wrapper input[type='submit']").attr('disabled', true);

			store_agreement_val(false);
		}
	});

	$(document).on('submit', '.payment-methods-wrapper form', function () {
		remove_agreement_val();
	});

	/**
	 * Store the value of agreeing to the ToS.
	 *
	 * @since 1.0
	 *
	 * @param val
	 */
	function store_agreement_val(val) {

		if (isLocalStorageSupported()) {
			localStorage.setItem('itetos_agree', val.toString());
		} else {
			$(document).data('itetos_agree', val.toString());
		}
	}

	/**
	 * Get the value of agreeing to the ToS.
	 *
	 * @since 1.0
	 *
	 * @returns {boolean}
	 */
	function get_agreement_val() {

		var val = false;

		if (localStorage) {
			val = localStorage.getItem('itetos_agree');
		} else {
			val = $(document).data('itetos_agree');
		}

		if (!val) {
			return false;
		}

		return val == 'true';
	}

	/**
	 * Remove the agreement value from local storage.
	 *
	 * @isnce 1.0
	 */
	function remove_agreement_val() {
		if (isLocalStorageSupported()) {
			localStorage.removeItem('itetos_agree');
		} else {
			$(document).data('itetos_agree', false);
		}
	}

	function isLocalStorageSupported() {
		var testKey = 'test', storage = window.localStorage;
		try {
			storage.setItem(testKey, '1');
			storage.removeItem(testKey);
			return true;
		} catch (error) {
			return false;
		}
	}
});