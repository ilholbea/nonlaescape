jQuery(document).ready(function ($) {
  if (shutterpressData.debug) {
    console.log("Page loaded:", window.location.href);
  }
  if (typeof shutterpressData !== "undefined" && shutterpressData !== null) {
    if (shutterpressData.debug) {
      console.log("shutterpressData:", shutterpressData);
    }
  }
  // Function to get the value of a query parameter
  function getQueryParam(param) {
    var urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(param);
  }

  // Check if we are on the specific options page (e.g., 'sp-gallery-settings')
  var currentPage = getQueryParam("page");

  if (currentPage === "sp-gallery-settings") {
    // Adjust this value to match your options page slug

    // Get all tab links and tab content sections
    var tabs = document.querySelectorAll(".nav-tab");
    var tabContents = document.querySelectorAll(".tab-content");

    // Function to hide all tab contents
    function hideAllTabContents() {
      tabContents.forEach(function (content) {
        content.style.display = "none"; // Hide each tab content
      });
    }

    // Function to deactivate all tabs
    function deactivateAllTabs() {
      tabs.forEach(function (tab) {
        tab.classList.remove("nav-tab-active"); // Remove active class from each tab
      });
    }

    // Function to show the active tab content based on the tab ID (parameter value)
    function showTabContent(tabId) {
      var targetTabContent = document.querySelector("#" + tabId);
      if (targetTabContent) {
        targetTabContent.style.display = "block"; // Show the targeted tab content
      }
    }

    // Add click event listeners to tabs
    tabs.forEach(function (tab) {
      tab.addEventListener("click", function (e) {
        e.preventDefault(); // Prevent default anchor behavior

        var tabId = this.getAttribute("href").split("tab=")[1]; // Get the tab parameter value from the href
        deactivateAllTabs(); // Deactivate all tabs
        hideAllTabContents(); // Hide all tab contents

        // Activate the clicked tab and show its content
        this.classList.add("nav-tab-active"); // Activate the clicked tab
        showTabContent(tabId); // Show the content for the clicked tab

        // Get the page and post_type parameters, defaulting to 'sp-gallery-settings' and 'shutterpress-gallery' if they are missing
        var pageParam = getQueryParam("page") || "sp-gallery-settings";
        var postTypeParam =
          getQueryParam("post_type") || "shutterpress-gallery";

        // Update the URL without reloading the page, ensuring the `page` and `post_type` parameters are preserved
        var newUrl =
          window.location.protocol +
          "//" +
          window.location.host +
          window.location.pathname +
          "?post_type=" +
          postTypeParam +
          "&page=" +
          pageParam +
          "&tab=" +
          tabId;
        window.history.pushState({ path: newUrl }, "", newUrl);
      });
    });

    // Handle page load with a tab query parameter
    var activeTab = getQueryParam("tab");
    if (activeTab) {
      deactivateAllTabs(); // Ensure all tabs are deactivated
      hideAllTabContents(); // Ensure all content is hidden
      var initialTab = document.querySelector(
        'a[href*="tab=' + activeTab + '"]'
      ); // Fix: Ensure we match the correct href

      // If the tab exists in the document, activate it
      if (initialTab) {
        initialTab.classList.add("nav-tab-active"); // Activate the tab corresponding to the query parameter
        showTabContent(activeTab); // Show the tab content corresponding to the query parameter
      } else {
        // Fallback to the default tab if the tab parameter is invalid
        tabs[0].classList.add("nav-tab-active");
        tabContents[0].style.display = "block";
      }
    } else {
      // Default to the first tab if no tab parameter is present
      tabs[0].classList.add("nav-tab-active");
      tabContents[0].style.display = "block";
    }
    // $('.sp-gallery-color-picker').wpColorPicker();
  }

  function debounce(func, wait) {
    let timeout;
    return function (...args) {
      const context = this;
      clearTimeout(timeout);
      timeout = setTimeout(() => func.apply(context, args), wait);
    };
  }

  function sortMediaListByIds(imageIds) {
    const mediaList = $(".sp_gallery_images-array .rwmb-media-list");

    // Temporarily store reordered items
    const reorderedItems = [];
    const detachedItemIds = new Set(); // Track detached item IDs to prevent duplication

    // Find each item by ID in the specified order, detach it, and add to reorderedItems
    imageIds.forEach((id) => {
      if (!detachedItemIds.has(id)) {
        // Only detach if we haven't already
        const item = mediaList
          .find(`input.rwmb-media-input[value="${id}"]`)
          .closest("li.rwmb-image-item");
        if (item.length) {
          reorderedItems.push(item.detach());
          detachedItemIds.add(id); // Mark this ID as detached
          if (shutterpressData.debug) {
            console.log(`Item ID ${id} found and detached:`, item); // Log each item as it is detached
          }
        } else {
          if (shutterpressData.debug) {
            console.log(`Item ID ${id} not found`); // Log if an ID is not found
          }
        }
      } else {
        if (shutterpressData.debug) {
          console.log(`Item ID ${id} already detached, skipping`);
        }
      }
    });

    // Log all detached items before clearing the list
    if (shutterpressData.debug) {
      console.log(
        "Reordered items (detached):",
        reorderedItems.map((i) => i.find("input.rwmb-media-input").val())
      );
    }

    // Clear the mediaList to avoid duplicates
    mediaList.empty();
    if (shutterpressData.debug) {
      console.log("mediaList emptied");
      console.log("mediaList", mediaList);
    }

    // Append each item in reorderedItems to mediaList, ensuring no duplicates
    reorderedItems.forEach((item) => {
      mediaList.append(item);
      if (shutterpressData.debug) {
        console.log(
          `Appended item ID: ${item.find("input.rwmb-media-input").val()}`
        );
      }
    });

    // Log the current state of mediaList after appending reordered items
    const currentIds = mediaList
      .children("li.rwmb-image-item")
      .map(function () {
        return $(this).find("input.rwmb-media-input").val();
      })
      .get();
    if (shutterpressData.debug) {
      console.log("Current mediaList item IDs after appending:", currentIds);
    }

    // Refresh the sortable to acknowledge the new order
    mediaList.sortable("refresh");
    if (shutterpressData.debug) {
      console.log("mediaList sortable refreshed");
    }

    if (typeof sp_gallery_pro_update_status === "function") {
      if (shutterpressData.debug) {
        console.log("Calling sp_gallery_pro_update_status");
      }
      sp_gallery_pro_update_status();
    }
  }

  // Listen for changes on inputs within the metabox
  // Debounce the AJAX call for sliders
  const saveFieldDebounced = debounce(function (fieldID, fieldValue, postID) {
    // Send AJAX request

    if (!fieldID || typeof fieldValue === "undefined" || !postID) {
      if (shutterpressData.debug) {
        console.warn("Aborting saveFieldDebounced: missing required data", {
          fieldID,
          fieldValue,
          postID,
        });
      }
      return;
    }
    fetch(`${shutterpressData.rest_url}shutterpress/v1/save-meta`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-WP-Nonce": shutterpressData.nonce,
      },
      credentials: "same-origin",
      body: JSON.stringify({
        post_id: postID,
        field_id: fieldID,
        field_value: fieldValue,
      }),
    })
      .then((response) => {
        if (!response.ok) throw new Error("Network response was not ok");
        return response.json();
      })
      .then((data) => {
        if (shutterpressData.debug) {
          console.log(`Field ${fieldID} saved via REST API.`);
        }
        if (data.image_order && fieldValue !== "drag_n_drop") {
          sortMediaListByIds(data.image_order);
        }
      })
      .catch((error) => {
        if (shutterpressData.debug) {
          console.error("REST request failed:", error);
        }
      });
  }, 500); // Adjust the debounce wait time as needed (300ms is common)

  window.saveFieldDebounced = saveFieldDebounced;
  let initialGalleryImageIds = [];

  let userInteracted = false; // Flag to track if the user has interacted

  // Function to get the current image IDs in the gallery
  function getCurrentGalleryImageIds(sorted = false) {
    const imageIds = $(
      ".sp_gallery_images-array .rwmb-media-list .rwmb-media-input"
    )
      .map(function () {
        return $(this).val();
      })
      .get(); // Convert jQuery object to an array

    // Optionally sort the IDs if the 'sorted' parameter is set to true
    return sorted
      ? imageIds.sort(function (a, b) {
          return Number(a) - Number(b);
        })
      : imageIds;
  }

  // Set after DOM is ready, defer one tick so RWMB can finish rendering
  setTimeout(function () {
    userInteracted = true;
    initialGalleryImageIds = getCurrentGalleryImageIds();
  }, 0);

  // Store the previous value of the specific input we want to track
  let previousWatermarkImageValue = $(
    'input[name="sp_gallery_watermark_image"]'
  ).val();

  // Listen for changes on inputs within the metabox
  $(document).on(
    "change input",
    ".rwmb-meta-box input, .rwmb-meta-box select, .rwmb-meta-box textarea",
    function () {
      if (!userInteracted) {
        return; // Skip the save if it's triggered during the initial setup
      }
      const rawName = $(this).attr("name") || "";
      const fieldID = rawName.replace(/\[\]$/, "");
      let fieldValue;

      // Special check for sp_gallery_watermark_image to handle only when its value changes
      if (fieldID === "sp_gallery_watermark_image") {
        const newValue = $(this).val();

        if (newValue === previousWatermarkImageValue) {
          // If the value hasn't changed, exit the function
          return;
        }

        // Update previous value and proceed
        previousWatermarkImageValue = newValue;
      }

      // Additional check for _sp_gallery_images_array to only trigger if the list has changed
      if (fieldID === "_sp_gallery_images_array") {
        const currentGalleryImageIds = getCurrentGalleryImageIds();
        if (shutterpressData.debug) {
          console.log("Current Gallery Image IDs:", currentGalleryImageIds);
          console.log("Initial Gallery Image IDs:", initialGalleryImageIds);
        }

        // Compare the current and initial gallery image IDs
        if (
          JSON.stringify(initialGalleryImageIds) ===
          JSON.stringify(currentGalleryImageIds)
        ) {
          // If the gallery order hasn't changed, skip the save
          return;
        }

        // Update the initial state to the current state and proceed
        initialGalleryImageIds = currentGalleryImageIds;

        // Set fieldValue to the current gallery image IDs array for saving
        fieldValue = currentGalleryImageIds;
      } else {
        // Handle other input types
        if ($(this).attr("type") === "checkbox") {
          fieldValue = $(this).is(":checked") ? 1 : 0;
        } else if ($(this).attr("type") === "radio") {
          fieldValue = $(`input[name="${rawName}"]:checked`).val();
        } else {
          fieldValue = $(this).val();
        }
      }
      if (shutterpressData.debug) {
        console.log("Metabox changed:", fieldID, fieldValue);
      }

      const postID = $("#post_ID").val();

      saveFieldDebounced(fieldID, fieldValue, postID);

      if (typeof sp_gallery_pro_update_status === "function") {
        if (shutterpressData.debug) {
          console.log("Calling sp_gallery_pro_update_status");
        }
        sp_gallery_pro_update_status();
      }
    }
  );

  // Trigger saving when the order is updated
  $(document).on(
    "sortupdate",
    ".sp_gallery_images-array .rwmb-media-list",
    function (event, ui) {
      if (shutterpressData.debug) {
        console.log("Image order updated");
      }
      const postID = $("#post_ID").val(); // Get the current post ID
      const fieldID = "_sp_gallery_images_array"; // Use your specific field name
      // Collect image IDs in the new order
      const sortedIDs = [];
      $(
        ".sp_gallery_images-array .rwmb-media-list .rwmb-image-item input.rwmb-media-input"
      ).each(function () {
        sortedIDs.push($(this).val()); // Get the image ID from each hidden input
      });
      if (shutterpressData.debug) {
        console.log("New image order:", sortedIDs);
      }
      // Use the debounced save function for consistency
      saveFieldDebounced(fieldID, sortedIDs, postID);
      initialGalleryImageIds = getCurrentGalleryImageIds();
    }
  );

  function waitForSortableInitialization() {
    const mediaList = $(".sp_gallery_images-array .rwmb-media-list");

    // Check if sortable is initialized
    if (mediaList.data("ui-sortable") !== undefined) {
      toggleSortableBasedOnSelect();
    } else {
      // Retry after a short delay if not yet initialized
      setTimeout(waitForSortableInitialization, 100);
    }
  }

  function toggleSortableBasedOnSelect() {
    const mediaList = $(".sp_gallery_images-array .rwmb-media-list");
    const selectedOption = $("#sp_gallery_image_sorting").val();

    if (selectedOption === "drag_n_drop") {
      mediaList.sortable("enable");
    } else {
      mediaList.sortable("disable");
    }
  }

  $(document).on(
    "click",
    ".sp-gallery-review-notice .notice-dismiss",
    function () {
      fetch(
        `${shutterpressData.rest_url}shutterpress/v1/dismiss-review-notice`,
        {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-WP-Nonce": shutterpressData.nonce,
          },
          credentials: "same-origin",
          body: JSON.stringify({}),
        }
      );
    }
  );

  // Start polling for initialization
  waitForSortableInitialization();
  $("#sp_gallery_image_sorting").on("change", toggleSortableBasedOnSelect);

  const $mediaField = $("#sp_gallery_images-box");

  function initCollapsibleBox() {
    const $handleActions = $mediaField.find(".handle-actions");
    const $list = $("#sp_gallery_images-box .rwmb-media-list");

    if (shutterpressData.debug) {
      console.log("List:", $list);
    }

    const $wrappedList = $list
      .wrap('<div class="sp-gallery-collapsible-media-box"></div>')
      .parent();
    const $collapsibleBox = $wrappedList;

    if (
      $mediaField.length &&
      !$mediaField.hasClass("sp-collapsible-initialized")
    ) {
      $mediaField.addClass("sp-collapsible-initialized");

      if ($collapsibleBox.length && $collapsibleBox.outerHeight() > 600) {
        $collapsibleBox.addClass("collapsed");
        const $button = $(
          '<button type="button" class="sp-gallery-toggle-expand-btn">Expand</button>'
        );
        $handleActions.prepend($button);
      }
    }
  }

  if ($mediaField.length && typeof $.fn.imagesLoaded === "function") {
    $mediaField.imagesLoaded().done(initCollapsibleBox);
  } else {
    // Fallback, run immediately without waiting for images
    initCollapsibleBox();
  }

  $(document).on("click", ".sp-gallery-toggle-expand-btn", function () {
    const $box = $mediaField.find(".sp-gallery-collapsible-media-box");
    $box.toggleClass("collapsed");

    const expanded = !$box.hasClass("collapsed");
    $(this).text(expanded ? "Collapse" : "Expand");
  });

  // Reusable function to toggle field visibility based on checkbox
  function setupToggleField(checkboxSelector, targetSelector) {
    function toggle() {
      var isChecked = $(checkboxSelector).is(":checked");
      $(targetSelector).toggle(isChecked);
    }
    $(checkboxSelector).on("change", toggle);
    toggle(); // Initial call
  }

  setupToggleField("#sp_gallery_lightbox", "#sp-gallery-lightbox-options");
  setupToggleField(
    "#sp_gallery_hover_float",
    "#sp-gallery-lightbox-float-options"
  );
  setupToggleField(
    "#sp_gallery_hover_overlay",
    "#sp-gallery-color-overlay-options"
  );
  setupToggleField(
    "#sp_gallery_show_hover_overlay_icon",
    "#sp-gallery-icon-overlay-options"
  );
  setupToggleField(
    "#sp_gallery_rounded_corners",
    "#sp-gallery-rounded-corner-options"
  );

  function toggleLayoutOptions() {
    var selected = $('input[name="sp_gallery_layout"]:checked').val();

    // Hide all by default
    $("#sp-gallery-grid-options, #sp-gallery-justified-options").hide();

    // Show the one that matches
    if (selected === "grid" || selected === "masonry") {
      $("#sp-gallery-grid-options").show();
    } else if (selected === "justified") {
      $("#sp-gallery-justified-options").show();
    }
  }

  // Run on page load
  toggleLayoutOptions();

  // Run on change
  $(document).on(
    "change",
    'input[name="sp_gallery_layout"]',
    toggleLayoutOptions
  );

  if (typeof wp.codeEditor !== "undefined" && wp.codeEditor.initialize) {
    document.querySelectorAll("textarea.rwmb-code").forEach(function (el) {
      let settings = wp.codeEditor.defaultSettings || {};
      settings = JSON.parse(JSON.stringify(settings)); // deep clone
      settings.codemirror = settings.codemirror || {};
      const mode = el.getAttribute("data-mode");
      if (mode) {
        settings.codemirror.mode = mode;
      }
      wp.codeEditor.initialize(el, settings);
    });
  }

  // Attach immediately, we are already inside jQuery ready, so DOMContentLoaded has fired
  const spForm = document.getElementById("sp-help-subscribe");
  if (spForm) {
    spForm.addEventListener("submit", function (e) {
      e.preventDefault();

      // require confirmation
      const confirmBox = this.querySelector("[name=confirm]");
      if (!confirmBox || !confirmBox.checked) {
        alert(
          "Please confirm that you would like to receive emails before subscribing."
        );
        return false;
      }

      // post in the background, no page change
      const body = new URLSearchParams(new FormData(this));

      fetch(this.action, {
        method: "POST",
        mode: "no-cors",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body,
      }).catch(() => {
        /* ignore */
      });

      // swap the form for a confirmation message
      this.style.display = "none";
      const successMsg = document.querySelector(".sp-form-success");
      if (successMsg) successMsg.style.display = "block";
      return false;
    });
  }
});
