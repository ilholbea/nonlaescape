// Shared gallery state (minimal, to avoid logic changes)
export let likedImages = [];
export let isLoggedIn = false;

export function setLikedImages(arr) {
  likedImages = Array.isArray(arr) ? arr : [];
}
export function getLikedImages() {
  return likedImages;
}

export function setIsLoggedIn(v) {
  isLoggedIn = !!v;
}
export function getIsLoggedIn() {
  return isLoggedIn;
}
