export function updateGuestLikedImages(Cookies, imageId, liked) {
  let currentLiked = Cookies.get('liked_images') ? JSON.parse(Cookies.get('liked_images')) : [];
  if (liked && !currentLiked.includes(imageId)) currentLiked.push(imageId);
  else if (!liked) currentLiked = currentLiked.filter((id) => id !== imageId);
  Cookies.set('liked_images', JSON.stringify(currentLiked), { expires: 365, path: '/', sameSite: 'Strict' });
  return currentLiked;
}
