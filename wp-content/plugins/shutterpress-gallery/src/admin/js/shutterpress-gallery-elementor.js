jQuery(document).ready(function ($) {
  // Hook into Elementor's frontend when the widget is ready

  // Debounce function for resizing
  function debounce(func, wait, immediate) {
    var timeout;
    return function () {
      var context = this,
        args = arguments;
      var later = function () {
        timeout = null;
        if (!immediate) func.apply(context, args);
      };
      var callNow = immediate && !timeout;
      clearTimeout(timeout);
      timeout = setTimeout(later, wait);
      if (callNow) func.apply(context, args);
    };
  }

  // Helper to bind initial layout, window resize, and ResizeObserver with a debounced handler
  function bindRelayout($scope, $container, layoutFn) {
    if ($container.length === 0) return;

    // Create one debounced function per container
    var relayout = debounce(function () {
      layoutFn($scope);
    }, 100);

    // Run once now
    relayout();

    // Window resize
    $(window).on("resize.shutterpress", relayout);

    // Observe container size changes
    if (typeof ResizeObserver !== "undefined") {
      var ro = new ResizeObserver(relayout);
      ro.observe($container[0]);
      // store to container so it can be disconnected later if needed
      $container.data("sp-ro", ro);
    }
  }

  // Masonry layout initialization function
  function initializeMasonry($scope) {
    var $container = $scope.find(".sp-gallery-masonry-gallery");
    var $items = $container.find(".sp-gallery-masonry-item");

    // Ensure that the container and items are available
    if ($container.length === 0 || $items.length === 0) {
      //console.error('Masonry container or items not found.');
      return;
    }

    // Use imagesLoaded to ensure all images are fully loaded before initializing Masonry
    var run = function () {
      var windowWidth = $(window).width();
      var breakpoints = elementorFrontend.config.breakpoints;
      const dataColumns = $container.data("columns");

      var columns;

      if (windowWidth >= breakpoints.lg) {
        columns = dataColumns.desktop;
      } else if (
        windowWidth >= breakpoints.md &&
        windowWidth < breakpoints.lg
      ) {
        columns = dataColumns.tablet;
      } else if (windowWidth < breakpoints.md) {
        columns = dataColumns.mobile;
      }

      ///const columns = parseInt($container.attr('data-columns')) || 3;
      let gap = parseInt($container.attr("data-gap"));
      // If gap is null, NaN, or invalid, fallback to 10, but keep 0 as a valid value
      gap = isNaN(gap) || gap === null ? 10 : gap;
      const containerWidth = $container.width();
      const columnWidth = (containerWidth - (columns - 1) * gap) / columns;
      const columnHeights = Array(columns).fill(0);

      // Clear previous absolute positioning before recalculation
      $items.each(function () {
        this.style.removeProperty("top");
        this.style.removeProperty("left");
      });

      $items.each(function () {
        const $item = $(this);
        const minHeight = Math.min(...columnHeights);
        const columnIndex = columnHeights.indexOf(minHeight);

        // Position each item in the grid
        $item.css({
          position: "absolute",
          top: `${minHeight}px`,
          left: `${columnIndex * (columnWidth + gap)}px`,
          "margin-bottom": "0px",
        });

        // Update column height for the next item
        columnHeights[columnIndex] += $item.outerHeight(true) + gap;
      });

      // Set the height of the container to the tallest column
      const maxHeight = Math.max(...columnHeights);
      $container.css("height", `${maxHeight}px`);
    };
    if (typeof $container.imagesLoaded === "function") {
      $container.imagesLoaded(run);
    } else {
      run();
    }
  }

  function initializeJustifiedGallery($scope) {
    var $container = $scope.find(".sp-gallery-justified-gallery");
    if ($container.length === 0) return;

    var run = function () {
      var rowHeight = parseInt($container.attr("data-row-height")) || 200;
      var gap = parseInt($container.attr("data-gap"), 10);
      if (isNaN(gap)) gap = 10;
      var containerWidth = $container.width();

      if (shutterpressData.debug) {
        console.log("containerWidth:", containerWidth);
      }

      var items = $container.find(".sp-gallery-justified-item");

      // Clear previous layout before recalculation
      items.each(function () {
        this.style.removeProperty("width");
        this.style.removeProperty("height");
        this.style.removeProperty("left");
        this.style.removeProperty("top");
        this.style.removeProperty("position");
      });

      var row = [];
      var currentX = 0;
      var currentY = 0;

      items.each(function () {
        var $item = $(this);
        var $img = $item.find("img")[0];

        var naturalWidth =
          $img.naturalWidth || parseInt($img.getAttribute("width"));
        var naturalHeight =
          $img.naturalHeight || parseInt($img.getAttribute("height"));
        var aspectRatio = naturalWidth / naturalHeight;
        var itemWidth = rowHeight * aspectRatio;

        row.push({
          $el: $item,
          width: itemWidth,
          height: rowHeight,
          aspectRatio: aspectRatio,
        });

        currentX += itemWidth + gap;

        // If row is full or this is the last item, lay out the row
        var isLast = $item.is(items.last());
        if ((currentX > containerWidth && row.length > 1) || isLast) {
          var totalGaps = (row.length - 1) * gap;
          var totalWidth = row.reduce((sum, img) => sum + img.width, 0);

          // If this is the last row and it did not exceed the container width, do not scale
          var isUnderfullLastRow = isLast && currentX <= containerWidth;
          var scale = isUnderfullLastRow
            ? 1
            : (containerWidth - totalGaps) / totalWidth;

          var offsetX = 0;
          row.forEach(function (imgObj) {
            var scaledWidth = Math.round(imgObj.width * scale);
            var scaledHeight = Math.round(rowHeight * scale);

            // For an underfull last row, keep the configured rowHeight exactly
            if (isUnderfullLastRow) {
              scaledHeight = rowHeight;
            }

            imgObj.$el.css({
              width: scaledWidth + "px",
              height: scaledHeight + "px",
              position: "absolute",
              left: offsetX + "px",
              top: currentY + "px",
            });

            offsetX += scaledWidth + gap;
          });

          currentY +=
            (isUnderfullLastRow ? rowHeight : Math.round(rowHeight * scale)) +
            gap;
          currentX = 0;
          row = [];
        }
      });

      // Set container height to fit all rows
      $container.css("height", currentY + "px");
      $container.css("position", "relative");
    };
    if (typeof $container.imagesLoaded === "function") {
      $container.imagesLoaded(run);
    } else {
      run();
    }
  }

  function initializeGrid($scope) {
    var $container = $scope.find(".sp-gallery-grid-gallery");
    if ($container.length === 0) return;

    var windowWidth = $(window).width();
    var breakpoints = elementorFrontend.config.breakpoints;
    const dataColumns = $container.data("columns");

    var columns;
    if (windowWidth >= breakpoints.lg) {
      columns = dataColumns.desktop;
    } else if (windowWidth >= breakpoints.md && windowWidth < breakpoints.lg) {
      columns = dataColumns.tablet;
    } else {
      columns = dataColumns.mobile;
    }

    let gap = parseInt($container.attr("data-gap"));
    gap = isNaN(gap) || gap === null ? 10 : gap; // keep 0 valid

    // Clear any inline sizes set server side so the preview sizes correctly
    $container.find(".sp-gallery-grid-item").each(function () {
      this.style.removeProperty("width");
      this.style.removeProperty("height");
    });

    // Apply CSS Grid so the first load preview is correct
    $container.css({
      display: "grid",
      gridTemplateColumns: "repeat(" + columns + ", 1fr)",
      gridGap: gap + "px",
      gap: gap + "px",
    });

    // Ensure items are visible and container participates in layout
    $container.find(".sp-gallery-grid-item").css({ opacity: 1 });
    $container.css("position", "relative");
  }

  // Run Masonry on first load
  elementorFrontend.hooks.addAction(
    "frontend/element_ready/shutterpress_gallery.default",
    function ($scope) {
      // Run and bind layouts
      var $masonry = $scope.find(".sp-gallery-masonry-gallery");
      var $justified = $scope.find(".sp-gallery-justified-gallery");
      var $grid = $scope.find(".sp-gallery-grid-gallery");

      if ($masonry.length) {
        bindRelayout($scope, $masonry, initializeMasonry);
      }
      if ($justified.length) {
        bindRelayout($scope, $justified, initializeJustifiedGallery);
      }
      if ($grid.length) {
        bindRelayout($scope, $grid, initializeGrid);
      }

      $scope
        .find(".sp-gallery-elementor-gallery")
        .removeClass("sp-gallery-hidden");
    }
  );
});
