import { registerBlockType } from "@wordpress/blocks";
import { useState, useEffect, useMemo } from "@wordpress/element";
import {
  SelectControl,
  ToggleControl,
  RangeControl,
  TextControl,
  PanelBody,
  Spinner,
  ComboboxControl,
} from "@wordpress/components";
import { __, sprintf } from "@wordpress/i18n";
import { InspectorControls, useBlockProps } from "@wordpress/block-editor";
import metadata from "./block.json";
import ServerSideRender from "@wordpress/server-side-render";
import { useSelect } from "@wordpress/data";

// Loading and error placeholders for SSR
const LoadingPlaceholder = () => <Spinner />;
const ErrorPlaceholder = (props) => {
  const err = props && props.error ? props.error : null;
  let msg = __("Error rendering block", "shutterpress-gallery");
  if (err) {
    if (typeof err === "string") msg = err;
    else if (err && typeof err.message === "string") msg = err.message;
    else {
      try {
        msg = JSON.stringify(err);
      } catch (e) {
        msg = String(err);
      }
    }
  }
  return (
    <div className="components-error">
      <p>{msg}</p>
    </div>
  );
};

// Register the block
registerBlockType(metadata.name, {
  attributes: {
    galleryId: {
      type: "number",
      default: 0,
    },
    useLightbox: {
      type: "boolean",
      default: true,
    },
    showDefaultButtons: {
      type: "boolean",
      default: true,
    },
    galleryLayout: {
      type: "string",
      default: "grid",
    },
    galleryGap: {
      type: "number",
      default: 20,
    },
    rowHeight: {
      type: "number",
      default: 300,
    },
    columns_desktop: {
      type: "number",
      default: 3,
    },
    columns_tablet: {
      type: "number",
      default: 2,
    },
    columns_mobile: {
      type: "number",
      default: 1,
    },
    hasLoadedDefaults: {
      type: "boolean",
      default: false,
    },
  },

  edit: function (props) {
    const { attributes, setAttributes } = props;
    const {
      galleryId,
      useLightbox,
      showDefaultButtons,
      galleryLayout,
      galleryGap,
      rowHeight,
      columns_desktop,
      columns_tablet,
      columns_mobile,
    } = attributes;

    // Remove local state for galleries, loading, error.

    const blockProps = useBlockProps();

    // Fetch galleries using useSelect at the top level (not in useEffect).
    const galleries = useSelect(
      (select) =>
        select("core").getEntityRecords("postType", "shutterpress-gallery", {
          per_page: -1,
          orderby: "title",
          order: "asc",
          _fields: ["id", "title"],
        }),
      []
    );
    const loading = galleries === null;
    const error = null;

    console.log("attributes", attributes);

    useEffect(() => {
      if (!attributes.hasLoadedDefaults) {
        const options =
          typeof window !== "undefined" && window.shutterpressDefaults
            ? window.shutterpressDefaults
            : {};

        const toInt = (v, fb) => {
          const n = parseInt(v, 10);
          return Number.isFinite(n) ? n : fb;
        };

        const next = {};
        if (attributes.useLightbox == null)
          next.useLightbox = !!options.use_lightbox;
        if (attributes.galleryLayout == null)
          next.galleryLayout = options.layout || "grid";
        if (attributes.galleryGap == null)
          next.galleryGap = toInt(options.gap, 20);
        if (attributes.rowHeight == null)
          next.rowHeight = toInt(options.row_height, 300);
        if (attributes.columns_desktop == null)
          next.columns_desktop = toInt(options.columns_desktop, 3);
        if (attributes.columns_tablet == null)
          next.columns_tablet = toInt(options.columns_tablet, 2);
        if (attributes.columns_mobile == null)
          next.columns_mobile = toInt(options.columns_mobile, 1);

        setAttributes({ ...next, hasLoadedDefaults: true });
      }
    }, [attributes.hasLoadedDefaults]);

    const galleryOptions = (() => {
      if (!loading && Array.isArray(galleries) && galleries.length > 0) {
        const base = galleries.map((g) => ({
          label:
            g.title?.rendered || __("Untitled Gallery", "shutterpress-gallery"),
          value: String(g.id),
        }));
        return [
          {
            label: __("Use Post Gallery", "shutterpress-gallery"),
            value: "0",
          },
          ...base,
        ];
      }
      return [];
    })();

    // Sanitize attributes for preview and exclude internal-only keys
    const sanitizeNumber = (val, fallback) =>
      Number.isFinite(val) ? val : fallback;
    const ssrAttributes = useMemo(() => {
      const base = {
        galleryId: sanitizeNumber(galleryId, 0),
        useLightbox: !!useLightbox,
        showDefaultButtons: !!showDefaultButtons,
        galleryLayout: galleryLayout ?? undefined,
        galleryGap: sanitizeNumber(galleryGap, 20),
        rowHeight:
          galleryLayout === "justified"
            ? sanitizeNumber(rowHeight, 300)
            : undefined,
        columns_desktop:
          galleryLayout === "grid" || galleryLayout === "masonry"
            ? sanitizeNumber(columns_desktop, 3)
            : undefined,
        columns_tablet:
          galleryLayout === "grid" || galleryLayout === "masonry"
            ? sanitizeNumber(columns_tablet, 2)
            : undefined,
        columns_mobile:
          galleryLayout === "grid" || galleryLayout === "masonry"
            ? sanitizeNumber(columns_mobile, 1)
            : undefined,
      };
      return Object.fromEntries(
        Object.entries(base).filter(([, v]) => v !== undefined && v !== null)
      );
    }, [
      galleryId,
      useLightbox,
      showDefaultButtons,
      galleryLayout,
      galleryGap,
      rowHeight,
      columns_desktop,
      columns_tablet,
      columns_mobile,
    ]);

    return (
      <div {...blockProps}>
        {loading && <Spinner />}

        {Number.isFinite(galleryId) &&
          galleryId === 0 &&
          !loading &&
          !error && (
            <div className="sp-gallery-block-preview">
              <div className="components-placeholder">
                <p>
                  {__(
                    "ShutterPress Gallery. View the post to see images.",
                    "shutterpress-gallery"
                  )}
                </p>
              </div>
            </div>
          )}

        {Number.isFinite(galleryId) && galleryId > 0 && !loading && !error && (
          <div className="sp-gallery-block-preview">
            <ServerSideRender
              block={metadata.name}
              httpMethod="GET"
              attributes={ssrAttributes}
              LoadingResponsePlaceholder={LoadingPlaceholder}
              ErrorResponsePlaceholder={ErrorPlaceholder}
            />
          </div>
        )}
        {error && (
          <p style={{ color: "red" }}>
            {sprintf(__("Error: %s", "shutterpress-gallery"), error)}
          </p>
        )}

        <InspectorControls>
          <PanelBody
            title={__("Gallery Settings", "shutterpress-gallery")}
            initialOpen={true}
          >
            <ComboboxControl
              label={__("Select Gallery", "shutterpress-gallery")}
              value={String(Number.isFinite(galleryId) ? galleryId : "")}
              options={galleryOptions}
              onChange={(newValue) => {
                const id = parseInt(newValue, 10);
                setAttributes({ galleryId: Number.isFinite(id) ? id : 0 });
              }}
              help={
                loading
                  ? __("Loading galleries…", "shutterpress-gallery")
                  : error
                    ? __("Error loading galleries", "shutterpress-gallery")
                    : __(
                        "Type to search by gallery name",
                        "shutterpress-gallery"
                      )
              }
              disabled={loading || !!error}
            />
            <ToggleControl
              label={__("Use Lightbox", "shutterpress-gallery")}
              checked={useLightbox}
              onChange={(newValue) =>
                setAttributes({ useLightbox: newValue ? true : false })
              }
            />
            <ToggleControl
              label={__("Use Default Buttons", "shutterpress-gallery")}
              checked={showDefaultButtons}
              onChange={(newValue) =>
                setAttributes({ showDefaultButtons: newValue ? true : false })
              }
            />
            <p className="components-base-control__help">
              {sprintf(
                __(
                  'If you want to add custom buttons, set the button HTML anchor to "%s".',
                  "shutterpress-gallery"
                ),
                `sp-gallery-filter-liked-photos-${galleryId}`
              )}{" "}
              <a
                href="https://shutterpress.io"
                target="_blank"
                rel="noopener noreferrer"
              >
                {__(
                  "See ShutterPress.io for more info.",
                  "shutterpress-gallery"
                )}
              </a>
            </p>
            <SelectControl
              label={__("Layout", "shutterpress-gallery")}
              value={galleryLayout}
              options={[
                { label: __("Grid", "shutterpress-gallery"), value: "grid" },
                {
                  label: __("Masonry", "shutterpress-gallery"),
                  value: "masonry",
                },
                {
                  label: __("Justified", "shutterpress-gallery"),
                  value: "justified",
                },
              ]}
              onChange={(newValue) =>
                setAttributes({ galleryLayout: newValue })
              }
            />
            <RangeControl
              label={__("Image Gap", "shutterpress-gallery")}
              value={galleryGap}
              onChange={(newValue) =>
                setAttributes({ galleryGap: parseInt(newValue, 10) })
              }
              min={0}
              max={100}
            />
            {galleryLayout === "justified" && (
              <RangeControl
                label={__("Row Height", "shutterpress-gallery")}
                value={rowHeight}
                onChange={(newValue) =>
                  setAttributes({ rowHeight: parseInt(newValue, 10) })
                }
                min={0}
                max={1000}
              />
            )}
            {(galleryLayout === "grid" || galleryLayout === "masonry") && (
              <>
                <SelectControl
                  label={__(
                    "Number of Desktop Columns",
                    "shutterpress-gallery"
                  )}
                  value={columns_desktop}
                  options={[
                    { label: "1", value: 1 },
                    { label: "2", value: 2 },
                    { label: "3", value: 3 },
                    { label: "4", value: 4 },
                    { label: "5", value: 5 },
                    { label: "6", value: 6 },
                    { label: "7", value: 7 },
                    { label: "8", value: 8 },
                    { label: "9", value: 9 },
                    { label: "10", value: 10 },
                  ]}
                  onChange={(newValue) =>
                    setAttributes({ columns_desktop: parseInt(newValue, 10) })
                  }
                />
                <SelectControl
                  label={__("Number of Tablet Columns", "shutterpress-gallery")}
                  value={columns_tablet}
                  options={[
                    { label: "1", value: 1 },
                    { label: "2", value: 2 },
                    { label: "3", value: 3 },
                    { label: "4", value: 4 },
                    { label: "5", value: 5 },
                    { label: "6", value: 6 },
                    { label: "7", value: 7 },
                    { label: "8", value: 8 },
                  ]}
                  onChange={(newValue) =>
                    setAttributes({ columns_tablet: parseInt(newValue, 10) })
                  }
                />
                <SelectControl
                  label={__("Number of Mobile Columns", "shutterpress-gallery")}
                  value={columns_mobile}
                  options={[
                    { label: "1", value: 1 },
                    { label: "2", value: 2 },
                    { label: "3", value: 3 },
                    { label: "4", value: 4 },
                  ]}
                  onChange={(newValue) =>
                    setAttributes({ columns_mobile: parseInt(newValue, 10) })
                  }
                />
              </>
            )}
          </PanelBody>
        </InspectorControls>
      </div>
    );
  },

  save: function () {
    return null; // Dynamic block, nothing is saved
  },
});
