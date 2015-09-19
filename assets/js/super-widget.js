jQuery(document).ready(function ($) {

	if (!get_agreement_val()) {
		$(".payment-methods-wrapper input[type='submit']").attr('disabled');
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
			terms.hide();
		} else {
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

	/**
	 * Store the value of agreeing to the ToS.
	 *
	 * @since 1.0
	 *
	 * @param val
	 */
	function store_agreement_val(val) {

		if (localStorage) {
			localStorage.setItem('itetos_agree', val.toString());
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

		if (localStorage) {

			var val = localStorage.getItem('itetos_agree');

			if (!val) {
				return false;
			}

			return val == 'true';
		}

		return false;
	}
});