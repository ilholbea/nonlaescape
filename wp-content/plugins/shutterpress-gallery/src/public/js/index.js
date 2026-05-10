// SHUTTERPRESS GALLERY PUBLIC JS (modular entry)
// Import your Isotope custom modes
import { loadUserData, handleLikedImages } from "./utils";
import { bindLikeButtonClicks } from "./likes";
import { generateDynamicGalleryStyles } from "./dynamic-styles";
import { initializeGalleryLayouts } from "./layouts";
import { initializeLightGalleries } from "./lightbox";
import { setupFilterButtons } from "./filters";
import { bindDownloadHandler } from "./downloads";
import { initBackToTop } from "./backtotop";
import {
  getLikedImages,
  setLikedImages,
  getIsLoggedIn,
  setIsLoggedIn,
} from "./state";

jQuery(document).ready(function ($) {
  if (!window.spGalleriesIsotope) window.spGalleriesIsotope = {};
  if (!window.spGalleriesJustified) window.spGalleriesJustified = {};

  // --- User Data (Likes, Logged In) ---
  setLikedImages([]);
  setIsLoggedIn(false);
  window.isLoggedIn = false;

  if (typeof shutterpressData !== "undefined" && shutterpressData !== null) {
    if (shutterpressData.debug)
      console.log("shutterpressData:", shutterpressData);

    if (!getIsLoggedIn()) {
      const cookieLikes = Cookies.get("liked_images");
      if (cookieLikes) {
        try {
          setLikedImages(JSON.parse(cookieLikes));
          handleLikedImages(getLikedImages());
        } catch (e) {
          if (shutterpressData.debug)
            console.error("Invalid liked_images cookie");
        }
      }
    } else {
      loadUserData()
        .then(function (response) {
          if (Array.isArray(response.liked_images)) {
            setLikedImages(response.liked_images);
            handleLikedImages(getLikedImages());
          }
          const loggedIn = !!response.logged_in;
          setIsLoggedIn(loggedIn);
          window.isLoggedIn = loggedIn;

          if (shutterpressData.debug) {
            console.log("Liked Images:", getLikedImages());
            console.log("Logged In:", getIsLoggedIn());
          }
        })
        .catch(function (err) {
          if (shutterpressData.debug)
            console.error("User data fetch error:", err);
          handleLikedImages(getLikedImages());
        });
    }
  }

  // --- MAIN SEQUENCE ---
  if (shutterpressData.debug) console.log("Page loaded:", window.location.href);
  window.spGalleries = {};
  window.spVirtualisers = {};

  generateDynamicGalleryStyles();
  initializeGalleryLayouts();

  jQuery(".sp-gallery").each(function () {
    const $g = jQuery(this);
    $g.one("layoutComplete.spInit", function () {
      $g.removeClass("sp-gallery-hidden").attr("data-sp-initialized", "1");
    });
    // Safety fallback in case layoutComplete never fires
    setTimeout(function () {
      if ($g.hasClass("sp-gallery-hidden")) {
        $g.removeClass("sp-gallery-hidden").attr("data-sp-initialized", "1");
      }
    }, 1500);
  });

  bindLikeButtonClicks();
  setupFilterButtons();
  initializeLightGalleries();
  bindDownloadHandler();
  initBackToTop();
});
