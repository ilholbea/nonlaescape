// Keep Shutterpress custom Isotope layouts available even if other plugins
// replace the global Isotope instance after we load.
(function () {
	if (typeof window === 'undefined') {
		return;
	}

	window.spGallery = window.spGallery || {};

	function defineGridLayout(LayoutMode) {
		if (LayoutMode.modes.spGridLayout) {
			return;
		}

		var SpGridLayout = LayoutMode.create('spGridLayout');

		SpGridLayout.prototype._resetLayout = function () {
			this.itemIndex = 0;

			var container = this.isotope.element;
			var gap = parseInt(container.getAttribute('data-gap')) || 0;
			var breakpoints = JSON.parse(container.getAttribute('data-breakpoints'));
			var dataColumns = JSON.parse(container.getAttribute('data-columns'));
			var containerWidth = window.innerWidth;

			var columns = dataColumns.desktop;

			if (containerWidth <= breakpoints.mobile) {
				columns = dataColumns.mobile;
			} else if (containerWidth <= breakpoints.tablet) {
				columns = dataColumns.tablet;
			}

			this.cols = columns;
			var totalGap = (columns - 1) * gap;
			var columnWidth = (this.isotope.size.innerWidth - totalGap) / columns;

			this.columnWidth = columnWidth;
			this.rowHeight = columnWidth;
			this.gap = gap;
			this.positions = [];
		};

		SpGridLayout.prototype._getItemLayoutPosition = function (item) {
			var col = this.itemIndex % this.cols;
			var row = Math.floor(this.itemIndex / this.cols);
			var x = col * (this.columnWidth + this.gap);
			var y = row * (this.rowHeight + this.gap);

			var element = item.element;
			element.style.width = this.columnWidth + 'px';
			element.style.height = this.rowHeight + 'px';

			this.positions.push({ x: x, y: y });
			this.itemIndex++;

			return { x: x, y: y };
		};

		SpGridLayout.prototype._getContainerSize = function () {
			var rowCount = Math.ceil(this.itemIndex / this.cols);
			return {
				height: rowCount * (this.rowHeight + this.gap) - this.gap,
			};
		};

		SpGridLayout.prototype.needsResizeLayout = function () {
			return true;
		};

		LayoutMode.modes.spGridLayout = SpGridLayout;
	}

	function defineJustifiedLayout(LayoutMode) {
		if (LayoutMode.modes.spJustifiedLayout) {
			return;
		}

		var SpJustifiedLayout = LayoutMode.create('spJustifiedLayout');

		SpJustifiedLayout.prototype._resetLayout = function () {
			var el = this.isotope.element;
			this.rowHeight = parseInt(el.getAttribute('data-row-height')) || 200;
			this.gap = parseInt(el.getAttribute('data-gap'), 10) || 10;
			this.containerWidth = this.isotope.size.innerWidth;
			this._posCache = new Map();
		};

		SpJustifiedLayout.prototype._getItemLayoutPosition = function (item) {
			var cached = this._posCache.get(item.id);
			if (cached) {
				item.element.style.width = cached.width + 'px';
				item.element.style.height = cached.height + 'px';
				this._posCache.delete(item.id);
				return { x: cached.x, y: cached.y };
			}

			var list = this.isotope.filteredItems && this.isotope.filteredItems.length ? this.isotope.filteredItems : this.isotope.items;
			var start = list.indexOf(item);
			if (start < 0) {
				return { x: 0, y: 0 };
			}

			var i = 0;
			var yBase = 0;
			while (i < start) {
				var wSumPrev = 0;
				var rowLenPrev = 0;
				var jPrev = i;
				while (jPrev < list.length) {
					var itPrev = list[jPrev];
					var elPrev = itPrev.element;
					var imgPrev = elPrev.querySelector('img');
					var nwPrev = parseInt(elPrev.getAttribute('data-width')) || (imgPrev && parseInt(imgPrev.getAttribute('width')));
					var nhPrev = parseInt(elPrev.getAttribute('data-height')) || (imgPrev && parseInt(imgPrev.getAttribute('height')));
					var arPrev = parseFloat(elPrev.getAttribute('data-ratio')) || (nwPrev && nhPrev ? nwPrev / nhPrev : 1);
					var wPrev = this.rowHeight * arPrev;
					wSumPrev += wPrev;
					rowLenPrev++;
					if (wSumPrev + this.gap * (rowLenPrev - 1) > this.containerWidth && rowLenPrev > 1) {
						break;
					}
					jPrev++;
				}
				var gapsPrev = (rowLenPrev - 1) * this.gap;
				var scalePrev = wSumPrev + gapsPrev > 0 ? (this.containerWidth - gapsPrev) / wSumPrev : 1;
				var rowHPrev = Math.round(this.rowHeight * scalePrev);
				yBase += rowHPrev + this.gap;
				i += rowLenPrev;
			}

			var row = [];
			var wSum = 0;
			var k = start;
			while (k < list.length) {
				var it = list[k];
				var el = it.element;
				var img = el.querySelector('img');
				var nw = parseInt(el.getAttribute('data-width')) || (img && parseInt(img.getAttribute('width')));
				var nh = parseInt(el.getAttribute('data-height')) || (img && parseInt(img.getAttribute('height')));
				var ar = parseFloat(el.getAttribute('data-ratio')) || (nw && nh ? nw / nh : 1);
				var w = this.rowHeight * ar;
				row.push({ id: it.id, w: w });
				wSum += w;
				if (wSum + this.gap * (row.length - 1) > this.containerWidth && row.length > 1) {
					break;
				}
				k++;
			}

			var gaps = (row.length - 1) * this.gap;
			var isLastRow = k >= list.length;
			var fullFitScale = wSum + gaps > 0 ? (this.containerWidth - gaps) / wSum : 1;
			var scale = isLastRow ? Math.min(1, fullFitScale) : fullFitScale;
			var rowH = Math.round(this.rowHeight * scale);

			var x = 0;
			for (var r = 0; r < row.length; r++) {
				var targetW = Math.floor(row[r].w * scale);
				this._posCache.set(row[r].id, { x: x, y: yBase, width: targetW, height: rowH });
				x += targetW + this.gap;
			}

			var now = this._posCache.get(item.id);
			if (now) {
				item.element.style.width = now.width + 'px';
				item.element.style.height = now.height + 'px';
				this._posCache.delete(item.id);
				return { x: now.x, y: now.y };
			}

			return { x: 0, y: yBase };
		};

		SpJustifiedLayout.prototype._getContainerSize = function () {
			var list = this.isotope.filteredItems && this.isotope.filteredItems.length ? this.isotope.filteredItems : this.isotope.items;

			var i = 0;
			var totalHeight = 0;
			while (i < list.length) {
				var wSum = 0;
				var rowLen = 0;
				var j = i;
				while (j < list.length) {
					var it = list[j];
					var el = it.element;
					var img = el.querySelector('img');
					var nw = parseInt(el.getAttribute('data-width')) || (img && parseInt(img.getAttribute('width')));
					var nh = parseInt(el.getAttribute('data-height')) || (img && parseInt(img.getAttribute('height')));
					var ar = parseFloat(el.getAttribute('data-ratio')) || (nw && nh ? nw / nh : 1);
					var w = this.rowHeight * ar;
					wSum += w;
					rowLen++;
					if (wSum + this.gap * (rowLen - 1) > this.containerWidth && rowLen > 1) {
						break;
					}
					j++;
				}
				var gaps = (rowLen - 1) * this.gap;
				var isLastRow = j >= list.length;
				var fullFitScale = wSum + gaps > 0 ? (this.containerWidth - gaps) / wSum : 1;
				var scale = isLastRow ? Math.min(1, fullFitScale) : fullFitScale;
				var rowH = Math.round(this.rowHeight * scale);
				totalHeight += rowH + this.gap;
				i += rowLen;
			}

			if (totalHeight > 0) {
				totalHeight -= this.gap;
			}

			return { height: totalHeight };
		};

		SpJustifiedLayout.prototype.needsResizeLayout = function () {
			return true;
		};

		LayoutMode.modes.spJustifiedLayout = SpJustifiedLayout;
	}

	function defineNoneLayout(LayoutMode) {
		if (LayoutMode.modes.none) {
			return;
		}

		var NoneLayout = LayoutMode.create('none');

		NoneLayout.prototype._resetLayout = function () {
			return false;
		};

		NoneLayout.prototype._getItemLayoutPosition = function () {
			return {};
		};

		NoneLayout.prototype._getContainerSize = function () {
			return { height: 0 };
		};

		NoneLayout.prototype.needsResizeLayout = function () {
			return false;
		};

		LayoutMode.modes.none = NoneLayout;
	}

	function registerLayouts() {
		var iso = window.Isotope;
		if (!iso || !iso.LayoutMode) {
			return;
		}

		var LayoutMode = iso.LayoutMode;

		defineGridLayout(LayoutMode);
		defineJustifiedLayout(LayoutMode);
		defineNoneLayout(LayoutMode);

		window.spGallery.Isotope = iso;
	}

	function ensureLayouts() {
		registerLayouts();
	}

	window.spGallery.ensureIsotopeLayouts = ensureLayouts;

	ensureLayouts();

	if (!window.spGallery._isotopeEnsurer) {
		window.spGallery._isotopeEnsurer = setInterval(ensureLayouts, 1000);
	}

	if (document && document.addEventListener) {
		document.addEventListener('readystatechange', ensureLayouts);
	}
	if (window && window.addEventListener) {
		window.addEventListener('load', ensureLayouts);
	}

	// Watch for galleries that become visible after being display:none (e.g., tabs/modals)
	(function ($) {
		if (!$ || !$.fn || typeof $.fn.isotope !== 'function') {
			return;
		}

		const visibilityMap = new WeakMap();
		const hasRenderableSize = (el) =>
			el && el.offsetParent !== null && el.clientWidth > 0 && el.clientHeight >= 0;

		function handleVisibilityChange(el) {
			const $el = $(el);
			const isoInstance = $el.data('isotope');
			if (!isoInstance) {
				return;
			}
			const wasVisible = visibilityMap.get(el);
			const nowVisible = hasRenderableSize(el);
			if (nowVisible && wasVisible === false) {
				if (window.shutterpressData && window.shutterpressData.debug) {
					console.log('[SP] Gallery visible again, relayout forced for', el.id || el);
				}
				$el.isotope('layout');
			}
			visibilityMap.set(el, nowVisible);
		}

		function primeVisibilityState(el) {
			if (!visibilityMap.has(el)) {
				visibilityMap.set(el, hasRenderableSize(el));
			}
		}

		function pollGalleries() {
			$('.sp-gallery').each(function () {
				primeVisibilityState(this);
				handleVisibilityChange(this);
			});
		}

		$(document).ready(function () {
			if (window.spGallery && window.spGallery.visibilityPoller) {
				return;
			}
			pollGalleries();
			window.spGallery = window.spGallery || {};
			window.spGallery.visibilityPoller = setInterval(pollGalleries, 400);
		});
	})(window.jQuery);
})();
