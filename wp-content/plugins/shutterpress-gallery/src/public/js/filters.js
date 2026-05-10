import { getButtonTextSelector } from './utils';

// --- Filter Liked Photos ---
export function filterLikedPhotos(gallerySelector) {
  var $gallery = jQuery(gallerySelector);
  $gallery.isotope({ filter: '.sp-gallery-item-liked' });
  if (shutterpressData.debug) console.log('Isotope filtering applied');
}

export function handleFilterClick($button, buttonSelector, buttonTextSelector, galleryId) {
  $button.blur();
  var gallerySelector = '#sp-gallery-' + galleryId;
  var $gallery = jQuery(gallerySelector);
  var filterActive = $gallery.data('filterActive') || false;
  var hasLikedItems = $gallery.find('.sp-gallery-item-liked').length > 0;

  if (!filterActive && !hasLikedItems) {
    if (shutterpressData.debug) console.log('Filter ignored: no liked photos in gallery', galleryId);
    return;
  }

  if (!filterActive) {
    jQuery(buttonSelector).addClass('sp-gallery-filter-active');
    if (buttonTextSelector !== null) jQuery(buttonTextSelector).text(wp.i18n.__('Show All Photos', 'shutterpress-gallery'));
    filterLikedPhotos(gallerySelector);
  } else {
    jQuery(buttonSelector).removeClass('sp-gallery-filter-active');
    if (buttonTextSelector !== null) jQuery(buttonTextSelector).text(wp.i18n.__('Show Favorite Photos', 'shutterpress-gallery'));
    $gallery.isotope({ filter: '*' });
    $gallery.isotope();
  }

  const galleryEl = document.getElementById('sp-gallery-' + galleryId);
  const newFilterActive = !filterActive;
  const lgKey = 'sp-gallery-' + galleryId;
  const instance = window.spGalleries[lgKey];

  if (instance && instance.settings) {
    const want = newFilterActive ? '.sp-gallery-item-liked .sp-gallery-item-lightbox' : '.sp-gallery-item .sp-gallery-item-lightbox';

    if (instance.settings.selector !== want) {
      instance.settings.selector = want;
      try {
        instance.refresh();
      } catch (e) {}
    }
  }

  $gallery.data('filterActive', !filterActive);
}

export function setupFilterButtons() {
  jQuery('[id^="sp-gallery-filter-liked-photos-"]').each(function () {
    var $button = jQuery(this);
    var galleryId = $button.attr('id').replace('sp-gallery-filter-liked-photos-', '');
    $button.off('click').on('click', function () {
      var buttonTextSelector = getButtonTextSelector($button);
      handleFilterClick($button, '#' + $button.attr('id'), buttonTextSelector, galleryId);
    });
  });
}
