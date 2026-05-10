// --- "Back to Top" Button ---
export function initBackToTop() {
  jQuery(document).on('click', '#sp-gallery-back-to-top', function () {
    if (shutterpressData.debug) console.log('Back to top clicked');
    jQuery('html, body').animate({ scrollTop: 0 }, 1000);
  });
}
