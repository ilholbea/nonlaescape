// --- Utilities & Helper Functions ---
export function loadUserData() {
  return fetch(`${shutterpressData.rest_url}shutterpress/v1/user-data`, {
    method: 'GET',
    credentials: 'include',
    headers: { 'X-WP-Nonce': shutterpressData.nonce },
  })
    .then((res) => {
      if (!res.ok) throw new Error('Failed to fetch user data');
      return res.json();
    })
    .catch((err) => {
      if (shutterpressData.debug) console.error('Fetch failed:', err);
      throw err;
    });
}

export function handleLikedImages(likedImages) {
  if (!Array.isArray(likedImages)) likedImages = [];
  likedImages.forEach(function (imageId) {
    jQuery('.sp-gallery-like-icon[data-image-id="' + imageId + '"]').addClass('sp-gallery-liked-image');
    jQuery('.sp-gallery-item-' + imageId).addClass('sp-gallery-item-liked');
  });
}

export function updateGuestLikedImages(imageId, liked) {
  let currentLiked = Cookies.get('liked_images') ? JSON.parse(Cookies.get('liked_images')) : [];
  if (liked && !currentLiked.includes(imageId)) {
    currentLiked.push(imageId);
  } else if (!liked) {
    currentLiked = currentLiked.filter((id) => id !== imageId);
  }
  Cookies.set('liked_images', JSON.stringify(currentLiked), { expires: 365, path: '/', sameSite: 'Strict' });
  return currentLiked;
}

export function getButtonTextSelector($button) {
  if ($button.hasClass('wp-block-button')) return '#' + $button.attr('id') + ' .wp-element-button';
  if ($button.hasClass('elementor-button')) return '#' + $button.attr('id') + ' .elementor-button-text';
  if ($button.hasClass('sp-gallery-button')) return '#' + $button.attr('id');
  return null;
}
