=== Responsive Gallery Grid ===
Contributors: Jules Colle
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=j_colle%40hotmail%2ecom&lc=US&item_name=Jules%20Colle%20%2d%20WP%20plugins%20%2d%20Responsive%20Gallery%20Grid&item_number=rgg¤cy_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted
Tags: responsive, responsive gallery, justified gallery, native gallery
Requires at least: 3.0
Requires PHP: 5.2.4
Tested up to: 6.7
Stable tag: 2.3.18
License: GPLv2 or later


Transforms the native WordPress gallery to a responsive gallery, respecting image proportions.


== Description ==

Transforms the native WordPress gallery to a responsive gallery, respecting image proportions. Includes SimpleLightbox, but also compatible with most third party lightbox plugins.

<a href="https://responsive-gallery-grid.bdwm.be/demo/">View Demo</a>
<a href="https://responsive-gallery-grid.bdwm.be/shortcode-parameters/">Documentation</a>
<a href="https://responsive-gallery-grid.bdwm.be/shortcode-generator/">Shortcode generator</a>

== Installation ==

1. Upload the plugin contents to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in Wordpress Admin
1. That's it! Your default WordPress Galleries should now all look titled and responsive!

= Finetuning =

If you want to finetune the options per gallery you can add some parameters to the gallery shortcode (from text editor).

Documentation available at https://responsive-gallery-grid.bdwm.be/shortcode-parameters/

You Can also change the default options for all galleries under Settings > RGG Gallery

== Frequently Asked Questions ==

= How do I add a lightbox to the gallery? =

Since version 2.2.1 the plugin includes a copy of SimpleLightbox. A lightweight responsive lightbox, which you can activate from the RGG Gallery page in your wordpress dashboard.
If you don't like the lightbox, or you think it's too lightweight, you can keep it disabled and install a third party lightbox plugin. Most lightboxes that work with the native WP gallery will work.

= The images to the left and right of the grid are cut of when I mouse over them. How do I solve this? =

This will happen if one of the grid containers have the CSS property <code>overflow:hidden</code>. If possible, you will
need to change this to <code>overflow:visible</code>. If not, you can
wrap the gallery inside a div, and assign some margins to it. If that's no option either, you should just disable scaling, or use negative scaling
by setting the <code>scale</code> property to a value between 0.5 and 1 in the schortcode.

= How can I further configure and modify the gallery to my needs? =

Please take a look here: https://responsive-gallery-grid.bdwm.be/shortcode-parameters/

Need anything else? Please start a support thread?

= Will there be added more options? =

Sure. Please start a support thread for any of your requests.

== Upgrade Notice ==

1. RGG now uses native gallery features to retrieve the images, this means you can no longer add multiple instances of the same image to a single gallery. If you need this feature, please revert to version 1.3.

== Screenshots ==

1. Responsive Gallery Grid in action. By default the images will pop out on mouse-over.
2. The gallery shortcode can be extended with some options, from the text editor.
3. From the WYSIWYG view the gallery looks like an ordinary Wordpress gallery, so you can easily add and remove pictures the way you are used to.
4. You can pimp eacht individual gallery to your needs by updating the gallery shortcode paramaters. (Check out the documentation)

== Changelog ==

= 2.3.18 (2024-11-22) =
* fixed bug that locked out non-admin users and caused conflicts with ajax requests
* Tested with WP version 6.7

= 2.3.17 (2024-06-04) =
* PRO: Fix plugin update checker

= 2.3.16 (2024-06-04) =
* Tested with WP version 6.5
* Show captions in builtin lightbox
* Add permissions check to rgg_admin_init function

= 2.3.15 (2024-05-04) =
* Escape input field values in settings

= 2.3.14 (2024-03-08) =
* Add nonce to prevent unauthorized users to reset RGG options.

= 2.3.13 (2024-03-04) =
* Tested with WP version 6.4

= 2.3.12 (2024-03-04) =
* Tested with WP version 6.4

= 2.3.11 (2024-03-04) =
* Add extra sanitization of settings to prevent XSS

= 2.3.10 (2023-01-17) =
* Make sure changes made in 2.3.9 stay compatible with older PHP versions.

= 2.3.9 (2023-01-16) =
* Sanitize shortcode parameters (Thanks to Animesh from Automattic for reporting this security issue to me.)

= 2.3.8 (2022-12-28) =
* Fix PHP Warning: Attempt to read property "ID" on null

= 2.3.7 (2022-12-23) =
* Fix bug: Remove href attribute when link="none". Thanks for [reporting](https://wordpress.org/support/topic/link-to-feature-doesnt-work/) @cahajla
* Worked a bit on the development environment. Preparing to release some more updates in the near future.

= 2.3.6 (Dec 10, 2020) =
* Disabled Swipebox because it's not compatible with jQuery 3.5.1 included with WP 5.6. Swipebox now automatically falls back to SimpleLightbox. (Thanks to Christopher Jones for the detailed report)
* Include slick library in project instead of loading it from CDN

= 2.3.5 (Sep 25, 2020) =
* Make even more compatible with Real Media Gallery plugin (thanks to the guys who make RML)

= 2.3.4 (Apr 9, 2020) =
* Remove print_r statement

= 2.3.3 (Apr 29, 2020) =
* Make compatibel with Real Media Library plugin (https://bit.ly/3d9QSUb) - Big thanks to @mguenter for the patch and @bit024 for reporting
* Add the image alt text as aria-label (can't use alt attribute because the images are CSS background-images)

= 2.3.2 (Apr 11, 2020) =
* Tested with WP 5.4 (no other changes)

= 2.3.1 (Apr 19, 2019) =
* Fixed small bug after resizing window + some code re-organization.

= 2.3 (Apr 19, 2019) =
* Added alternative to lightbox: Show image above gallery. This will create a synchronized slider above the gallery. On clicking an image in the gallery, the corresponding image becomes the active slide.

= 2.2.2 (Apr 16, 2019) =
* added additional lightbox: simplelightbox (https://simplelightbox.com/). I recommend using simplelightbox as swipebox seems to be no longer maintained by its developer.

= 2.2.1 (Jun 7, 2018) =
* Fixed bug that was breaking gallery after update 2.2

= 2.2 (Jun 6, 2018) =
* Added Swipebox as a built-in lighthbox. Plugin still works fine with Responsve Lightbox. But Responsve Lightbox is getting a bit heavy and recently added it's own gallery solution, which looks confusing if you want to use it together with RGG. So I decided to include a lightweight lightbox.
* ignore caption in and out times if caption_effect is none (Pro only)

= 2.1.5 (Apr 04, 2018) =
* Use Scale parameter for zoom effect (Pro only)
* Add "last row behavior" option to settings screen (Pro only)

= 2.1.4 (Mar 15, 2018) =
* Completed documentation: https://responsive-gallery-grid.bdwm.be/shortcode-parameters/
* Some design changes in admin interface
* Improved compatibility with the plugins responsive-lightbox and wp-gallery-custom-links
* Fix bug: Responsive Lightbox loading when rel=""

= 2.1.3 (Feb 13, 2018) =
* Added Zoom and Fade effects (Pro Only)

= 2.1.2 (Jan 28, 2018) =
* add link parameter, so pictures in gallery can be linked to attachment page, media file (default) and None.
* Make compatible with responsive-lightbox with zero configuration (no more need to "force lightbox")
* implement last-row behavior (last row same height as previous, justified, align center, align right) (Pro Only)
* make captions work when no animation is selected (Pro Only)

= 2.1.1 (Jan 18, 2018) =
* get rid of ridiculously high z-indexes for images, as they were overlapping modal windows in some cases.
* fix PHP warning problem with pligins and themes, that call post_gallery hook without the optional 3th paramater $instance.

= 2.1 (Jan 07, 2018) =
* Big changes. Completely rewritten. (December 31, 2017)
* Merged RGG Pro and RGG Free.
* Make ready for release.

= 1.8-beta-1 (November 16, 2016) =
* prevent jumping images (beta 1)

= 1.7 (June 28, 2016) =
* updated jquery.imagesloaded plugin to 4.1.0
* added additional caption styles and effects

= 1.6.2 (June 27, 2016) =
* Added CSS line `img { height:auto; }` to fix problem with aspect ratio's (https://wordpress.org/support/topic/onpageload-wrong-aspect-ratio?replies=4)

= 1.6.1 (June 26, 2016) =
* Fix problem with media library not loading. (some commented-out html code related to the update-nag got sent together with the json response)

= 1.6 (June 26, 2016) =
* Applied changes also appied in RGG 2.0.2 (free)

= 1.1 (March 16, 2015) =
* Test update

= 1.0 (March 16, 2015) =
* First release