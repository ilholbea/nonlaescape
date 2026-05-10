// SHUTTERPRESS GALLERY PUBLIC JS (modular entry)
// Import your Isotope custom modes
import "./shutterpress-gallery-isotope";
//import { loadUserData, handleLikedImages } from './utils';
//import { bindLikeButtonClicks } from './likes';
import { generateDynamicGalleryStyles } from "./dynamic-styles";
import { initializeGalleryLayouts } from "./layouts";
//import { initializeLightGalleries } from './lightbox';
//import { setupFilterButtons } from './filters';
//import { bindDownloadHandler } from './downloads';
//import { initBackToTop } from './backtotop';
//import { getLikedImages, setLikedImages, getIsLoggedIn, setIsLoggedIn } from './state';

// --- MAIN SEQUENCE ---
jQuery(document).ready(function ($) {
  if (
    typeof shutterpressData !== "undefined" &&
    shutterpressData &&
    shutterpressData.debug
  ) {
    console.log("Page loaded:", window.location.href);
  }

  if (!window.spGalleriesIsotope) window.spGalleriesIsotope = {};
  if (!window.spGalleriesJustified) window.spGalleriesJustified = {};
  if (!window.spVirtualisers) window.spVirtualisers = {};
  if (!window.spGalleries) window.spGalleries = {};

  // Initialize only when a gallery exists (and only once per instance)
  function mountGalleriesInEditor() {
    // Prefer the editor iframe if present
    const iframe =
      document.querySelector('iframe[name="editor-canvas"]') ||
      document.querySelector(".editor-canvas__iframe") ||
      document.querySelector(".block-editor__iframe");

    // Choose the correct document and jQuery
    const ctxDoc = iframe?.contentDocument || document;
    const $ctx = iframe?.contentWindow?.jQuery || jQuery;

    // Find galleries in the correct document
    let $pending = $ctx(".sp-gallery").not("[data-sp-initialized]");
    if (!$pending.length) return;

    $pending.each(function () {
      const el = this;
      const doc = el.ownerDocument; // target doc for this gallery
      const $doc = doc.defaultView?.jQuery || jQuery;

      // Wait for the block to have a width
      (function whenSized(cb, tries = 0) {
        const w = el.clientWidth;
        if (w && w > 0) return cb();
        if (tries > 40) return cb();
        setTimeout(() => whenSized(cb, tries + 1), 50);
      })(() => {
        // Use imagesLoaded from the same window as the gallery
        const hasImagesLoaded = typeof $doc.fn.imagesLoaded === "function";
        const imgLoaded = hasImagesLoaded
          ? (sel, fn) => $doc(sel).imagesLoaded(fn)
          : (sel, fn) => fn();

        imgLoaded(el, () => {
          // Ensure dynamic styles exist inside the same document as the gallery
          const STYLE_ID = "shutterpress-gallery-inline-style";
          // 1) regenerate styles in parent as usual
          jQuery("#" + STYLE_ID).remove();
          generateDynamicGalleryStyles();
          // 2) copy them into the iframe document so CSS applies there
          const parentStyle = document.getElementById(STYLE_ID);
          if (parentStyle && doc !== document) {
            const existing = doc.getElementById(STYLE_ID);
            if (existing) existing.remove();
            const clone = doc.createElement("style");
            clone.id = STYLE_ID;
            clone.textContent = parentStyle.textContent || "";
            doc.head.appendChild(clone);
          }

          // Now run layouts
          // Unhide only after the first layout completes to avoid FOUC
          $doc(el).one("layoutComplete.spInit", function () {
            $doc(this)
              .removeClass("sp-gallery-hidden")
              .attr("data-sp-initialized", "1");
          });

          // Safety fallback: if layoutComplete never fires, unhide after 1.5s
          setTimeout(function () {
            const $g = $doc(el);
            if ($g.hasClass("sp-gallery-hidden")) {
              $g.removeClass("sp-gallery-hidden").attr(
                "data-sp-initialized",
                "1"
              );
            }
          }, 1500);

          initializeGalleryLayouts($doc, true);
        });
      });
    });
  }

  // First attempt (if SSR was fast)
  mountGalleriesInEditor();

  // Watch the canvas for SSR insertions / edits
  const canvas =
    document.querySelector(".block-editor-block-list__layout") ||
    document
      .querySelector('iframe[name="editor-canvas"]')
      ?.contentDocument?.querySelector(".block-editor-block-list__layout") ||
    document.body;
  const observer = new MutationObserver((muts) => {
    for (const m of muts) {
      if (m.addedNodes && m.addedNodes.length) {
        mountGalleriesInEditor();
        break;
      }
    }
  });
  observer.observe(canvas, { childList: true, subtree: true });

  // If the canvas is in an iframe that loads after this script, attach once it’s ready
  const editorIframe =
    document.querySelector('iframe[name="editor-canvas"]') ||
    document.querySelector(".editor-canvas__iframe") ||
    document.querySelector(".block-editor__iframe");
  if (editorIframe && editorIframe.contentWindow) {
    editorIframe.addEventListener("load", () => {
      try {
        const innerCanvas =
          editorIframe.contentDocument?.querySelector(
            ".block-editor-block-list__layout"
          ) || editorIframe.contentDocument?.body;
        if (innerCanvas) {
          observer.observe(innerCanvas, { childList: true, subtree: true });
          mountGalleriesInEditor();
        }
      } catch (e) {
        // noop
      }
    });
  }

  // Also run when the editor signals DOM is ready
  if (window.wp && wp.domReady) wp.domReady(mountGalleriesInEditor);

  // Extra safety: re-run when block attributes change
  if (window.wp?.data?.subscribe) {
    let tick;
    wp.data.subscribe(() => {
      clearTimeout(tick);
      tick = setTimeout(mountGalleriesInEditor, 150);
    });
  }
});
