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
		var $selector = $('<fieldset class="bogo-locale-options"></fieldset>');

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
		var name_attr = prefix + '[' + id + '][' + locale + ']';
		var id_attr = 'edit-' + prefix + '-' + id + '-' + locale;

		var $cb = $('<input type="checkbox" />');
		$cb.attr('name', name_attr);
		$cb.attr('id', id_attr);
		$cb.attr('value', 1);
		$cb.prop('checked', checked);

		var $label = $('<label class="bogo-locale-option"></label>');
		$label.attr('for', id_attr);
		$label.append(_bogo.langName(locale));

		return $label.prepend($cb);
	}

	_bogo.langName = function(locale) {
		return _bogo.available_languages[locale] || '';
	}

})(jQuery);