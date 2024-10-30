jQuery(function ($) {

	/**
	 * jQuery.fn.sortElements
	 * --------------
	 * @author James Padolsey (http://james.padolsey.com)
	 * @version 0.11ß
	 * @updated 18-MAR-2010
	 * --------------
	 * @param Function comparator:
	 *   Exactly the same behaviour as [1,2,3].sort(comparator)
	 *
	 * @param Function getSortable
	 *   A function that should return the element that is
	 *   to be sorted. The comparator will run on the
	 *   current collection, but you may want the actual
	 *   resulting sort to occur on a parent or another
	 *   associated element.
	 *
	 *   E.g. $('td').sortElements(comparator, function(){
   *      return this.parentNode;
   *   })
	 *
	 *   The <td>'s parent (<tr>) will be sorted instead
	 *   of the <td> itself.
	 */

	$.fn.sortElements = (function () {

		var sort = [].sort

		return function (comparator, getSortable) {

			getSortable = getSortable || function () {
				return this
			}

			var placements = this.map(function () {

				var sortElement = getSortable.call(this),
					parentNode = sortElement.parentNode,

					// Since the element itself will change position, we have
					// to have some way of storing it's original position in
					// the DOM. The easiest way is to have a 'flag' node:
					nextSibling = parentNode.insertBefore(
						document.createTextNode(''),
						sortElement.nextSibling,
					)

				return function () {

					if (parentNode === this) {
						throw new Error(
							'You can\'t sort elements if any one is a descendant of another.',
						)
					}

					// Insert before flag:
					parentNode.insertBefore(this, nextSibling)
					// Remove flag:
					parentNode.removeChild(nextSibling)

				}

			})

			return sort.call(this, comparator).each(function (i) {
				placements[i].call(getSortable.call(this))
			})

		}

	})();

	var table = $('.tudosobresites_hosting.table')

	/**
	 * Sorts hosting tables
	 * Simple sortability for tables by Linas Jusys
	 */
	table.find('th.sortable').wrapInner('<span class="sorting-arrows" title="sort this column"/>').each(function () {

		var th = $(this),
			thIndex = th.index(),
			inverse = false,
			currentWrapper = th.children('.sorting-arrows'),
			neighbouringWrappers = th.siblings().children('.sorting-arrows')

		th.click(function () {

			if (currentWrapper.hasClass('descending')) {
				currentWrapper.removeClass('descending').addClass('ascending')
			}

			if (currentWrapper.hasClass('ascending')) {
				currentWrapper.removeClass('ascending').addClass('descending')
			}

			if (neighbouringWrappers.hasClass('sorted')) {
				neighbouringWrappers.removeClass('sorted ascending descending')
			}

			if (!currentWrapper.hasClass('sorted')) {
				currentWrapper.addClass('sorted ascending')
			}

			table.find('td').filter(function () {

				return $(this).index() === thIndex

			}).sortElements(function (a, b) {

				a = $.text([a]).trim()
				b = $.text([b]).trim()

				if (/\d+/.test(a)) a = Number(parseInt(a))
				if (/\d+/.test(b)) b = Number(parseInt(b))
				if (/[\d+\.\,]+/.test(a)) a = Number(parseFloat(a))
				if (/[\d+\.\,]+/.test(b)) b = Number(parseFloat(b))

				return a > b ? ( inverse ? -1 : 1 )
					: ( inverse ? 1 : -1 )

			}, function () {

				// parentNode is the element we want to move
				return this.parentNode

			})

			inverse = !inverse

		})

	});

})