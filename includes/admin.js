(function($) {
	_bogo = _bogo || {};

	$(function() {
		$('.menu-item').bogoAddLocaleSelector();
	});

	$(document).ajaxSuccess(function(event, request, settings) {
		$('.menu-item').bogoAddLocaleSelector();
	});

	$.fn.bogoAddLocaleSelector = function() {
		return this.each(function() {
			if (_bogo.hasSelector(this)) {
				return;
			}

			var id = $(this).attr('id').replace('menu-item-', '');
			var $selector = _bogo.selector(id);
			$(this).find('.menu-item-actions').first().before($selector);
		});
	}

	_bogo.hasSelector = function(elm) {
		return $(elm).is(':has(.bogo-locale-options)');
	}

	_bogo.selector = function(id) {
		var $selector = $('<div class="bogo-locale-options"></div>');

		if (_bogo.available_languages) {
			$.each(_bogo.available_languages, function(i, val) {
				var checked = false;

				if (! _bogo.locales[id] || -1 < $.inArray(i, _bogo.locales[id])) {
					checked = true;
				}

				$selector.append(_bogo.checkbox(id, i, checked));
			});
		}

		return $selector;
	}

	_bogo.checkbox = function(id, locale, checked) {
		var prefix = 'menu-item-bogo-locale';
		var $cb = $('<input type="checkbox" />');

		$cb.attr('name', prefix + '[' + id + '][' + locale + ']');
		$cb.attr('value', 1);

		if (checked) {
			$cb.prop('checked', true);
		}

		$cb = $('<span class="bogo-locale-option"></span>').append($cb);
		$cb.append(_bogo.langName(locale));

		return $cb;
	}

	_bogo.langName = function(locale) {
		return _bogo.available_languages[locale] || '';
	}

})(jQuery);