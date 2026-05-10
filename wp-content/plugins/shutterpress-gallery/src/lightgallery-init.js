// src/lightgallery-init.js

// Import LightGallery and its plugins
import lightGallery from '../node_modules/lightgallery/dist/lightgallery.umd.js';
import lgThumbnail from '../node_modules/lightgallery/dist/plugins/thumbnail/lg-thumbnail.umd.js';
import lgZoom from '../node_modules/lightgallery/dist/plugins/zoom/lg-zoom.umd.js';
import lgFullscreen from '../node_modules/lightgallery/dist/plugins/fullscreen/lg-fullscreen.umd.js';
import lgAutoplay from '../node_modules/lightgallery/dist/plugins/autoplay/lg-autoplay.umd.js';

// Assign lightGallery and each plugin to the global window object
window.lightGallery = lightGallery;
window.lgThumbnail = lgThumbnail;
window.lgZoom = lgZoom;
window.lgFullscreen = lgFullscreen;
window.lgAutoplay = lgAutoplay;
