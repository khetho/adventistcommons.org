$(function () {
	var productForm = productContainer();
	productForm.init();
});

productContainer = function () {
	var _selectors = {
		resetBtn: '#product-filter-form .reset',
		titleField: '#product-filter-title',
		availableInField: '#product-filter-available-in',
		audienceField: '#product-filter-audience',
		authorField: '#product-filter-author',
		typeField: '#product-filter-type',
		bindingField: '#product-filter-binding',
		sortByTitle: '#sort-by-title',
		productsForm: '#product-filter-form',
		paginationLinks: '.pagination a',
		pageField: '#product-filter-page',
		paginationPageParameter: 'ci-pagination-page'
	}

	var _init = function () {
		/*
		 * 
		 * Reset products list form
		 */
		$(_selectors.resetBtn).on("click", function () {
			var control = $(_selectors.titleField).selectize();
			if (typeof control[0] !== 'undefined') {
				control[0].selectize.clear();
			}

			control = $(_selectors.availableInField).selectize();
			if (typeof control[0] !== 'undefined') {
				control[0].selectize.clear();
			}

			control = $(_selectors.audienceField).selectize();
			if (typeof control[0] !== 'undefined') {
				control[0].selectize.clear();
			}

			control = $(_selectors.authorField).selectize();
			if (typeof control[0] !== 'undefined') {
				control[0].selectize.clear();
			}

			control = $(_selectors.typeField).selectize();
			if (typeof control[0] !== 'undefined') {
				control[0].selectize.clear();
			}

			control = $(_selectors.bindingField).selectize();
			if (typeof control[0] !== 'undefined') {
				control[0].selectize.clear();
			}

			$(_selectors.sortByTitle).prop("checked", true);

			$(_selectors.productsForm).submit();
		});

		$(_selectors.paginationLinks).attr('href', 'javascript:void(0)');
		$(_selectors.paginationLinks).on( "click", function(e) {
			$(_selectors.pageField).val($(this).data(_selectors.paginationPageParameter));
			$(_selectors.productsForm).attr('action', '/products/?p=' + $(this).data(_selectors.paginationPageParameter));
			$(_selectors.productsForm).submit();
		});
	};

	return {
		init: _init
	};
};