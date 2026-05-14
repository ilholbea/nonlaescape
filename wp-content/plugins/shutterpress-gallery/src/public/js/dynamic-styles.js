// --- Dynamic Gallery Styles (Grid & Masonry) ---
export function generateDynamicGalleryStyles() {
  const $ = jQuery;
  const galleryElements = $('[id^="sp-gallery-"].sp-gallery');
  if (galleryElements.length === 0) return;
  let css = "";
  galleryElements.each(function () {
    const $gallery = $(this);
    const galleryId = $gallery.data("id");
    const lgGalleryCssId = `.lg-sp-gallery-${galleryId}`;
    const breakpoints = $gallery.data("breakpoints");
    const layout = $gallery.data("layout");
    const galleryWidth = $gallery.get(0).clientWidth;
    const gap = $gallery.data("gap");
    const lg = $gallery.data("lg");
    const iconSize = $gallery.data("icon-size");
    const iconSizeH = $gallery.data("icon-size-h");
    const columns = $gallery.data("columns");
    const colors = $gallery.data("colors");
    const customCSS = $gallery.attr("data-custom-css");

    function getItemWidth(columns) {
      return (columns - 1) * gap;
    }

    css += `
    
    #sp-gallery-buttons-${galleryId}, 
    #sp-gallery-${galleryId}, 
    #sp-gallery-page-load-container-${galleryId}, 
    #sp-gallery-pagination-${galleryId},
    #sp-gallery-button-bar-${galleryId},
    #sp-gallery-after-images-${galleryId} {
      --sp-gallery-color-primary: ${colors.primary};
      --sp-gallery-color-accent: ${colors.accent};
    }

    /* Default for desktop */
    #sp-gallery-${galleryId} {
      ${iconSizeH && iconSizeH.desktop ? `--sp-gallery-hover-icon-size: ${iconSizeH.desktop}px;` : ""}
      ${iconSize && iconSize.desktop ? `--sp-gallery-icon-size: ${iconSize.desktop}px;` : ""}
    }

    /* For tablets (max-width: ${breakpoints.tablet}px) */
    @media (max-width: ${breakpoints.tablet}px) {
      #sp-gallery-${galleryId} {
          ${iconSizeH && iconSizeH.tablet ? `--sp-gallery-hover-icon-size: ${iconSizeH.tablet}px;` : ""}
          ${iconSize && iconSize.tablet ? `--sp-gallery-icon-size: ${iconSize.tablet}px;` : ""}
      }
    }

    /* For mobile (max-width: ${breakpoints.mobile}px) */
    @media (max-width: ${breakpoints.mobile}px) {
      #sp-gallery-${galleryId} {
          ${iconSizeH && iconSizeH.mobile ? `--sp-gallery-hover-icon-size: ${iconSizeH.mobile}px;` : ""}
          ${iconSize && iconSize.mobile ? `--sp-gallery-icon-size: ${iconSize.mobile}px;` : ""}
      }
    }`;

    if (lg) {
      css += `

      ${lgGalleryCssId} {
        --lg-icon-color: ${lg.iconColor};
        --lg-hover-color: ${colors.primary};
        --lg-background-color: ${lg.bgColor};
      }`;
    }

    if (layout === "grid") {
      const baseSelector = `#sp-gallery-${galleryId}.sp-gallery-grid-gallery `;
      css += `

      ${baseSelector} {
        display: grid;
        grid-template-columns: repeat(${columns.desktop}, 1fr);
        grid-gap: ${gap}px;
      }
      @media (max-width: ${breakpoints.tablet}px) {
        ${baseSelector} { grid-template-columns: repeat(${columns.tablet}, 1fr); }
      }
      @media (max-width: ${breakpoints.mobile}px) {
        ${baseSelector} { grid-template-columns: repeat(${columns.mobile}, 1fr); }
      }
     `;
    }

    if (layout === "masonry") {
      const baseSelector = `#sp-gallery-${galleryId}.sp-gallery-masonry-gallery `;
      css += `

      ${baseSelector} {
        --sp-gallery-width: ${galleryWidth}px;
      }

      ${baseSelector} {
        display: grid;
        grid-template-columns: repeat(${columns.desktop}, 1fr);
        grid-gap: ${gap}px;
      }

      ${baseSelector} .sp-gallery-masonry-item {
        margin-bottom: ${gap}px;
        width: calc((var(--sp-gallery-width) - ${getItemWidth(columns.desktop)}px) / ${columns.desktop});
      }

      @media (max-width: ${breakpoints.tablet}px) {
        ${baseSelector} {
          grid-template-columns: repeat(${columns.tablet}, 1fr);
        }
        ${baseSelector} .sp-gallery-masonry-item {
          width: calc((var(--sp-gallery-width) - ${getItemWidth(columns.tablet)}px) / ${columns.tablet});
        }
      }

      @media (max-width: ${breakpoints.mobile}px) {
        ${baseSelector} {
          grid-template-columns: repeat(${columns.mobile}, 1fr);
        }
        ${baseSelector} .sp-gallery-masonry-item {
          width: calc((var(--sp-gallery-width) - ${getItemWidth(columns.mobile)}px) / ${columns.mobile});
        }
      }
      `;
    }
    if (customCSS) {
      css += customCSS;
    }
  });

  let $style = jQuery("#shutterpress-gallery-inline-style");
  if ($style.length === 0) {
    $style = jQuery("<style>", {
      type: "text/css",
      id: "shutterpress-gallery-inline-style",
    }).appendTo("head");
  }
  $style.text(css);
}
