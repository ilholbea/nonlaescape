=== ShutterPress Gallery - Masonry, justified and grid galleries, lightbox and infinite scroll ===
Contributors: shutterpressgallery
Donate link: https://shutterpress.io
Tags: gallery, photography, masonry, infinite scroll, lightbox
Requires at least: 6.3
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 1.7.12
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

WordPress gallery plugin for photographers. Create fast, responsive image galleries with Masonry, Justified, Grid, Lightbox, and Infinite Scroll.

== Description ==

### **Wordpress Gallery Plugin**

**ShutterPress Gallery** is a flexible, WordPress gallery plugin designed for photgraphers or artisits who need to share and deliver images online. Build stunning, responsive galleries with reusable **Gallery Templates** and fine-grained **styling options** that let you control colors, icons, and hover effects — all from an easy-to-use admin panel.

With built-in support for Gutenberg and Elementor, you can display each gallery on its own dedicated page, or embed it anywhere on your site using Gutenberg blocks, Elementor widgets, or shortcodes. Each gallery can be customized with templates, layout settings, and feature toggles, including optional favorites, downloads, and lightbox.

**Key Features:**
* Create unlimited photo galleries.
* Save time with reusable **Gallery Templates**.
* Choose from **Grid, Masonry, or Justified** layouts.
* Customize **colors, icons, and hover effects** with styling options.
* Enable **Infinite Scroll** for seamless browsing.
* Let users mark favorite images.
* Optional **image download** support.
* Display galleries via **shortcode**, **Gutenberg** block, or **Elementor** widget.
* Mobile-friendly and fully **responsive**.
* Lightweight and fast.

**Perfect for:**
* Wedding & event photographers.
* Studio galleries.
* Client delivery and proofing.
* Portfolio presentations.

**Demos**
[Grid Gallery](https://shutterpress.io/demo-galleries/grid-gallery-infinite-scroll/)
[Masonry Gallery](https://shutterpress.io/demo-galleries/masonry-gallery-infinite-scroll/)
[Justified Gallery](https://shutterpress.io/demo-galleries/justified-gallery-infinite-scroll/)

### **ShutterPress Gallery Pro**

 Take your galleries to the next level with [ShutterPress Gallery Pro](https://shutterpress.io), a powerful extension of the ShutterPress Gallery plugin. Designed for professional photographers and advanced users, the Pro version unlocks a suite of features that enhance security, customisation, and client workflows. Whether you’re showcasing work for clients or managing large-scale projects, ShutterPress Pro offers tools to streamline your process and deliver a premium experience.

Pro Features:

* **WooCommerce Integration**: Sell your photos or prints directly from your gallery using WooCommerce integration.
* **Watermarking**: Automatically add customisable text or image watermarks to your photos to protect your intellectual property.
* **Password Protection**: Secure galleries with passwords, ensuring only authorised clients or viewers can access them.
* **User Access Controls**: Restrict access to galleries based on user roles or individual user permissions.
* **Download Management**: Enable clients to download the full gallery as a ZIP file.
* **PDF Licensing**: Include a downloadable PDF license agreement in gallery ZIP files for professional projects.
* **Client Interaction Stats**: Gain insights into gallery views, downloads, and client preferences with advanced statistics.
* **Email Notifications**: Notify clients automatically when their gallery is ready to view.
* **Email Clients from Gallery**: Quickly send emails to clients using reusable email templates linked to each gallery.

Why Upgrade to Pro?
ShutterPress Gallery Pro provides the tools you need to protect, manage, and monetize your galleries with ease. It’s the perfect solution for photographers looking to offer a seamless, professional experience to their clients while maintaining full control over their images.

Upgrade now and start leveraging these powerful features to enhance your workflow and client satisfaction.

Visit [shutterpress.io](https://shutterpress.io) for more information. 

Whether you're a professional photographer or an enthusiast, **ShutterPress Gallery** is the perfect tool for transforming your WordPress site into a visual gallery that showcases your unique creative vision.

**Liking images and filtering in action**

https://youtu.be/tti4Y0HCZXc

== Installation ==
1. Upload the `shutterpress-gallery` folder to the `/wp-content/plugins/` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Start creating stunning galleries with the SP Galleries menu option in the admin area.

== Usage ==
1. Manage your galleries through the ShutterPress menu option. Create a new gallery, upload your photos, and customize the layout.
2. View the gallery on your site at it's own URL, **your-website.com/gallery/your-gallery-name**.
3. **Shortcode:** Use the shortcode `[shutterpress_gallery id="your-gallery-id"]` to display a gallery on any page or post.
4. **Gutenberg Block:** Alternatively, use the ShutterPress Gallery block in the Gutenberg editor to add a gallery directly to your post or page.
5. **Elementor Widget:** If you're using Elementor, you can add a gallery widget to any page or post.

== Frequently Asked Questions ==

= How do I create a gallery? =
You can create galleries through the SP Galleries menu option in the WordPress admin dashboard. Once created, you can display them directly at their own URL or by using a shortcode or the Gutenberg block.

= Can I choose different layouts for my galleries? =
Yes, you can select between a **masonry** layout for a more artistic and free-flowing feel or a **grid** layout for a more structured display.

= Can users download the images? =
Yes, viewers can download images making it easy to access high-quality versions of your photos.

= How do I display a gallery on a page or post? =
Use the shortcode `[shutterpress_gallery id="your-gallery-id"]` or the ShutterPress Gallery Gutenberg block to embed your gallery anywhere.

= What does the "favorite image" feature do? =
The favorite image feature allows users to select and store their favorite image. Logged-in users' preferences are saved in the database, while guests' preferences are stored in cookies.

= Can I password protect client galleries? =
This can be done with the upcoming Shutterpress Pro Plugin or by using other wordpress plugins.

== Screenshots ==
1. Grid Gallery
2. Masonry Gallery
3. Justified Gallery
4. Gallery Admin Page
5. Gutenberg Block Settings
6. Elementor Widget Settings
7. Gallery Template Options
8. Image Hover Examples

== Commercial ==  

This plugin offers a commercial version with additional features and priority support at https://shutterpress.io.

== Changelog ==

= 1.7.12 =

* Fix - Adds compatibility with Avada/Fusion page builder

= 1.7.11 =

* Fix - RWMB_Custom_Image_Select_Field now allows for multiple fields

= 1.7.10 =

* Fix - Add Isotope layout mode registration guard to prevent conflict with other Isotope instances

= 1.7.9 =

* Fix - Issue that preventing float styles from being applied to images

= 1.7.8 =

* Update - Add new Shutterpress_Gallery_Files Class
* Fix - Minor bug fixes and code refactoring

= 1.7.7 =

* Fix - Gallery Block not rendering correctly

= 1.7.6 =

* Update - Shortcodes show current post gallery when no id is set
* Update - Refactor Gallery Block Defaults
* Fix - Shortcodes take defaults from gallery template

= 1.7.5 =

* Fix - Localization and translations
* New - French Spanish and German Translations

= 1.7.4 =

* New - Add help page in WP Admin
* Fix - Minor layout fixes

= 1.7.3 =

* Fix - Fix autoload bug causing fatal error

= 1.7.2 =

* Fix - Minor bug fixes

= 1.7.1 =

* Fix - Remove debug output from error log

= 1.7.0 =

* New - Template system for Gallery Templates, create and reuse gallery design presets
* Update - Visual improvements for gallery filtering
* Update - Unified icons across the gallery and lightbox
* Update - Custom lightgallery script based on the original script
* Update - Refactored defaults and sanitisation schema into a single source of truth (Shutterpress_Gallery_Defaults)
* Update - Isotope and infinite scroll scripts and plugins are now bundled locally instead of loaded from external CDN
* Security - Added comprehensive sanitisation and escaping across templates, block rendering, and REST endpoints
* Security - Added nonce verification to form submissions and REST requests where missing
* Security - Restricted and sanitized custom CSS fields to prevent unsafe code injection
* Fix - Removed deprecated load_plugin_textdomain() usage for translations
* Fix - Addressed multiple PHPCS and PluginCheck warnings (escaping, sanitisation, offloaded assets)
* Improvement - Unified filters for defaults and schema, making developer extensions safer and more consistent
* Improvement - General code clean-up and compliance with WordPress coding standards

= 1.6.7 =

* Update - Add href to Lightbox Links for SEO
* Fix - Prevent elementor lightbox from running if ShutterPress lightbox is initialized
* Fix - Set infinite scroll loaded to constant height to prevent visual flicker
* Fix - Change guest download link from nonce to origin check to prevent caching issues
* Fix - Fix issue where images on last page of infinite scroll may not load

= 1.6.6 =

* Fix - Only showing 10 most recent galleries in Gutenberg block drop down - Now show all and search.
* Fix - Only showing 10 most recent galleries in Elementor Widget drop down - Now show all and search.
* Fix - Defult icon size set on first load of plugin

= 1.6.5 =

* Update - Unbundle shutterpress-gallery-isotope.js from shutterpress-gallery-public.js
* Fix - Prevent inifinite scrolling loading when just one page
* Fix - Add scroll to top icon on infinite scroll

= 1.6.4 =

* New - Add live view in gutenberg block
* New - Add virtualisation to large galleries for improved performance
* Update - Gutenberg block now shows current post gallery if no gallery is selected
* Update - Load infinite scroll via REST endpoint
* Update - Improve masonry gallery rendering to prevent image overlaps
* Update - Move sp_gallery_get_site_color() sp_gallery_darken_color() get_inline_styles() sp_gallery_is_elementor_in_edit_mode() to Shutterpress_Gallery_Utils class
* Update - Split shutterpress-gallery-public.js into smaller files and compile with webpack
* Update - Combine shutterpress-gallery-isotope.js into shutterpress-gallery-public.js

= 1.6.3 =

* New - Allow setting the image icon size to a custom px value
* Update - Rename metabox to meta_box for consistency
* Update - Change icons to Lucide icons for consistency

= 1.6.2 =

* Fix - Remove loading=lazy from gallery images to prevent rendering issues in some situations

= 1.6.1 =

* Fix - Issue where grid gallery may not display correctly on some mobile devices

= 1.6.0 =

* New - Justified Gallery Layout
* New - Gallery Pagination
* New - Gallery Infinite Scroll
* Update - Move gallery rendering to Gallery and Image classes
* Update - Collapses gallery images on admin page and makes it expandable
* Update - Rename custom post type from Galleries to ShutterPress Galleries
* Update - Add Sass css compiler

= 1.5.0 =

* Update - Changes class structure to be psr4 compatible
* Update - Change menu title to ShutterPress
* Update - Elementor widget now recognises dafault gallery
* Fix - Add parent selector to edit page

== Upgrade Notice ==

= 1.7.0 =

* Important - Add galley templates

= 1.5.0 =

* Important - Change file and class structure

= 1.4.0 =

* Important - class changes and updates

= 1.3.0 =

* Important - updates for compatibility with pro plugin

= 1.2.3 =

* Important - fix for bug that prevented image deletion.

= 1.2.0 =

* New - Adds additional functionality and improves performance

= 1.0.0 =

* Initial release.

== License ==
This plugin is licensed under the GPLv2 or later. You can modify and redistribute it under the terms of the GNU General Public License.

This plugin includes a modified version of LightGallery, copyright © Sachin N. (https://www.lightgalleryjs.com/), distributed under the GNU General Public License v3.
	•	The original LightGallery source is available from the official project repository.
	•	This plugin may include modifications to LightGallery for integration with ShutterPress Gallery. These modifications are ©Rock Solid Communictions Ltd, 2025, and are likewise released under GPLv3.

Both the original and modified versions are free software. You may redistribute them and/or modify them under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.