import { updateGuestLikedImages, handleLikedImages } from './utils';
import { setLikedImages, getLikedImages, getIsLoggedIn } from './state';

// --- Like Button Logic ---
export function bindLikeButtonClicks() {
  const $doc = jQuery(document);

  // Remove any prior handler once (namespaced)
  $doc.off('click.sp-like');

  // Delegate so appended items work
  $doc.on('click.sp-like', '.sp-gallery-like-icon', function () {
    const $this = jQuery(this);
    const imageId = $this.data('image-id');
    const isLoggedIn = getIsLoggedIn();

    // Optimistic UI
    $this.toggleClass('sp-gallery-liked-image');
    jQuery('.sp-gallery-item-' + imageId).toggleClass('sp-gallery-item-liked');

    if (isLoggedIn) {
      fetch(`${shutterpressData.rest_url}shutterpress/v1/toggle-like`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-WP-Nonce': shutterpressData.nonce,
        },
        credentials: 'include',
        body: JSON.stringify({ image_id: imageId }),
      })
        .then((res) => res.json())
        .then((response) => {
          if (response.liked !== undefined) {
            setLikedImages(updateGuestLikedImages(imageId, response.liked));
            handleLikedImages(getLikedImages());
          }
        })
        .catch((err) => {
          if (shutterpressData.debug) console.error('Toggle like failed:', err);
        });
    } else {
      try {
        const liked = $this.hasClass('sp-gallery-liked-image');
        setLikedImages(updateGuestLikedImages(imageId, liked));
        handleLikedImages(getLikedImages());
        if (shutterpressData.debug) console.log('Liked Images:', getLikedImages());
      } catch (e) {
        if (shutterpressData.debug) console.error('Cookie update failed:', e);
      }
    }
  });
}
