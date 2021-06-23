=== Table of Contents Plus ===
Contributors: conjur3r
Tags: table of contents, indexes, toc, sitemap, cms, options, list, page listing, category listing
Requires at least: 3.2
Tested up to: 5.7
Stable tag: 2106
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A powerful yet user friendly plugin that automatically creates a table of contents. Can also output a sitemap listing all pages and categories.


== Description ==

A powerful yet user friendly plugin that automatically creates a context specific index or table of contents (TOC) for long pages (and custom post types).  More than just a table of contents plugin, this plugin can also output a sitemap listing pages and/or categories across your entire site.

Built from the ground up and with Wikipedia in mind, the table of contents by default appears before the first heading on a page.  This allows the author to insert lead-in content that may summarise or introduce the rest of the page.  It also uses a unique numbering scheme that doesn't get lost through CSS differences across themes.

This plugin is a great companion for content rich sites such as content management system oriented configurations.  That said, bloggers also have the same benefits when writing long structured articles.  [Discover how Google](http://dublue.com/2012/05/12/another-benefit-to-structure-your-web-pages/) uses this index to provide 'Jump To' links to your content.

Includes an administration options panel where you can customise settings like display position, define the minimum number of headings before an index is displayed, other appearance, and more.  For power users, expand the advanced options to further tweak its behaviour - eg: exclude undesired heading levels like h5 and h6 from being included; disable the output of the included CSS file; adjust the top offset and more.  Using shortcodes, you can override default behaviour such as special exclusions on a specific page or even to hide the table of contents altogether.

Prefer to include the index in the sidebar?  Go to Appearance > Widgets and drag the TOC+ to your desired sidebar and position.

Custom post types are supported, however, auto insertion works only when the_content() has been used by the custom post type.  Each post type will appear in the options panel, so enable the ones you want.

Collaborate, participate, fork this plugin on [Github](https://github.com/zedzedzed/table-of-contents-plus/).  Reach out on Github or place them at [http://dublue.com/plugins/toc/](http://dublue.com/plugins/toc/)


== Screenshots ==

1. An example of the table of contents, positioned at the top, right aligned, and a width of 275px
2. An example of the sitemap_pages shortcode
3. An example of the sitemap_posts shortcode
4. The options panel found in Settings > TOC+
5. Some advanced options
6. The sitemap tab


== Installation ==

The normal plugin install process applies, that is search for `table of contents plus` from your plugin screen or via the manual method:

1. Upload the `table-of-contents-plus` folder into your `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

That's it!  The table of contents will appear on pages with at least four or more headings.

You can change the default settings and more under Settings > TOC+

This plugin requires PHP 5.


== Shortcodes ==
The plugin was designed to be as seamless and painfree as possible and did not require you to insert a shortcode for operation.  However, using the shortcode allows you to fully control the position of the table of contents within your page.  The following shortcodes are available with this plugin.

When attributes are left out for the shortcodes below, they will fallback to the settings you defined under Settings > TOC+.  The following are detailed in the help tab.

= [toc] =
Lets you generate the table of contents at the preferred position.  Useful for sites that only require a TOC on a small handful of pages.  Supports the following attributes:
* "label": text, title of the table of contents
* "no_label": true/false, shows or hides the title
* "wrapping": text, either "left" or "right"
* "heading_levels": numbers, this lets you select the heading levels you want included in the table of contents.  Separate multiple levels with a comma.  Example: include headings 3, 4 and 5 but exclude the others with `heading_levels="3,4,5"`
* "class": text, enter CSS classes to be added to the container. Separate multiple classes with a space.

= [no_toc] =
Allows you to disable the table of contents for the current post, page, or custom post type.

= [sitemap] =
Produces a listing of all pages and categories for your site. You can use this on any post, page or even in a text widget.  Note that this will not include an index of posts so use sitemap_posts if you need this listing.

= [sitemap_pages] =
Lets you print out a listing of only pages. The following attributes are accepted:
* "heading": number between 1 and 6, defines which html heading to use
* "label": text, title of the list
* "no_label": true/false, shows or hides the list heading
* "exclude": IDs of the pages or categories you wish to exclude
* "exclude_tree": ID of the page or category you wish to exclude including its all descendants

= [sitemap_categories] =
Same as `[sitemap_pages]` but for categories.

= [sitemap_posts] =
This lets you print out an index of all published posts on your site.  By default, posts are listed in alphabetical order grouped by their first letters.  The following attributes are accepted:
* "order": text, either ASC or DESC
* "orderby": text, popular options include "title", "date", "ID", and "rand". See [WP_Query](https://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters) for a list.
* "separate": true/false (defaults to true), does not separate the lists by first letter when set to false.
Use the following CSS classes to customise the appearance of your listing:
* toc_sitemap_posts_section
* toc_sitemap_posts_letter
* toc_sitemap_posts_list


== I love it, how can I show my appreciation? ==
If you have been impressed with this plugin and would like to somehow show some appreciation, rather than send a donation my way, please donate to your charity of choice.

I will never ask for any form of reward or compensation.  Helping others achieve their goals is satisfying for me :)


== Changelog ==
= 2106 =
* Released: 23 June 2021
* Add compatibility with Rank Math SEO
* Bump tested WordPress version to 5.7
* Add PHP coding style
* Adhere to majority of coding tips

= 2002 =
* Released: 9 February 2020
* Fixed encoding when using %PAGE_TITLE% or %PAGE_NAME%
* Bump tested WordPress version to 5.3
* Removed all local translations as you can find more up to date ones at translate.wordpress.org
* Removed translators links from readme

= 1601 =
* Released: 5 January 2016
* Bump tested WordPress version to 4.4
* Add 'enable' and 'disable' API functions so a developer can better control the execution.
* Add Brazilian Portuguese translation thanks to Blog de Niterói
* Add Spanish translation thanks to David Saiz
* TOC+ widget now adheres to a blank title if none provided. Thanks to [Dirk](http://dublue.com/plugins/toc/comment-page-11/#comment-5140) for the cue.
* Updated jQuery Smooth Scroll 1.5.5 to 1.6.0
* Updated text domain to better support translation packs.

= 1509 =
* Released: 4 September 2015
* Added Hebrew translation thanks to Ahrale
* Added Japaense translation thanks to シカマル
* Added Greek translation thanks to Dimitrios Kaisaris
* Updated jQuery Smooth Scroll 1.4.10 to 1.5.5
* Supply both minified and unminified CSS and JS files, use minified versions.
* Convert accented characters to ASCII in anchors.
* Bump tested WordPress version to 4.3
* Fixed: PHP notice introduced in WP 4.3
* Fixed: javascript error with $.browser testing for Internet Explorer 7.
* Plugin has moved to [GitHub](https://github.com/zedzedzed/table-of-contents-plus/) for better collaboration.
* Help needed: preg_match_all failing with bad UTF8 characters producing no TOC. If you can help, please participate in this [issue](https://github.com/zedzedzed/table-of-contents-plus/issues/105).

= 1507 =
* Released: 5 July 2015
* Added Danish translation courtesy of Cupunu
* Simplified the translation duty by moving the help material to the plugin's website.
* Updated translation file.

= 1505 =
* Released: 2 May 2015
* Huge thanks to Jason for an updated Simplified Chinese translation.
* Added collapse property to the toc shortcode.  When set to true, this will hide the table of contents when it loads.  Example usage: [toc collapse="true"]
* Added label_show and label_hide properties to the toc shortcode.  This lets you change the "show" and "hide" link text when using the shortcode.
* Bump tested WordPress version to 4.2.1.

= 1408 =
* Released: 1 August 2014
* Added a human German translation courtesy Ben
* Added "class" attribute to the TOC shortcode to allow for custom CSS classes to be added to the container.  Thanks to Joe for [suggesting it](http://dublue.com/plugins/toc/comment-page-7/#comment-2803)

= 1407 =
* Released: 5 July 2014
* Added Ukrainian translation courtesy Michael Yunat
* Added French translation courtesy Jean-Michel Duriez
* Empty headings are now ignored, as suggested by [archon810](http://wordpress.org/support/topic/patch-ignore-empty-tags)
* Removed German translation, may have been machine translated, [ref](http://wordpress.org/support/topic/excluding-headlines-special-characters)
* Fixed: Special chars in TOC+ > Settings > Exclude Headings no longer get mangled on save.  Thanks to N-Z for [reporting it](http://wordpress.org/support/topic/excluding-headlines-special-characters).

= 1404 =
* Released: 18 April 2014
* Bump WordPress support to 3.9
* Fixed: Strip HTML tags from post titles for sitemap_posts so those items do not appear under a < heading. Thanks to [Rose](http://dublue.com/plugins/toc/comment-page-6/#comment-2311) for reporting it.
* Fictitious: This release was powered by three blind mice.

= 1402 =
* Released: 19 February 2014
* Added German translation courtesy Cord Peter
* Modify toc_get_index API function to also reset minimum number of headings to 0.
* Removing the TOC+ widget from the sidebar no longer requires you to uncheck the 'Show the table of contents only in the sidebar' option. It will be reset on removal.
* Delay count of headings until disqualified have been removed. Thanks to [Simone di Saintjust](http://dublue.com/plugins/toc/comment-page-6/#comment-2190) for raising it.
* Using the TOC+ widget, you can now limit the display to selected post types. Thanks to [Pete Markovic](http://dublue.com/plugins/toc/comment-page-6/#comment-2248) for the idea.
* Updated translation file (extra options added).

= 1311 =
* Released: 10 November 2013
* Added third parameter to toc_get_index API function to enable eligibility check (eg apply minimum heading check, is post type enabled, etc). This has been switched off by default and only affects those using the API. Thanks [Jonon](http://dublue.com/plugins/toc/comment-page-5/#comment-1943) for your comment.
* Added Dutch translation courtesy Renee
* Apply bullet option to TOC+ widget, thanks to [Thomas Pani for the patch](http://dublue.com/plugins/toc/comment-page-5/#comment-2040).

= 1308 =
* Released: 5 August 2013
* Fix javascript issue with minimum jQuery version check (broke smooth scrolling using WordPress 3.6).
* Replaced Slovak translation with a human translated version courtesy Boris Gereg.
* Remove <!--TOC--> signature from source when using the shortcode but not allowed to print (eg on homepage).
* Add "separate" attribute for sitemap_posts shortcode to not split by letter, thanks [DavidMjps](http://wordpress.org/support/topic/exclude-alphabetical-headings-on-sitemap) for the suggestion.

= 1303.1 =
* Released: 22 March 2013
* New: added Polish translation, curtesy Jakub
* Fixed: an issue in 1303 that ignored headings with the opening tag on the first line and the heading text on a new line.  Thanks to [richardsng](http://wordpress.org/support/topic/unable-to-display-the-full-toc) for the quick discovery.

= 1303 =
* Released: 21 March 2013
* New: option auto insert after the first heading.  Thanks to [@thelawnetwork](http://dublue.com/plugins/toc/comment-page-4/#comment-1782) for requesting it.
* New: allow headings to be excluded from the table of contents.  This is available both globally under the advanced section of Settings > TOC+ and individually with the TOC shortcode.  Check out the help material for examples.  Thanks to the many of you that requested it.
* New: advanced option to lowercase all anchors.  The default is off.
* New: advanced option to use hyphens rather than underscores in anchors.  The default is off.
* New: shortcode to list all posts in alphabetical order grouped by first letter.
* New: added Slovak translation, curtesy Branco Radenovich.
* Add version numbers to CSS/JS files to better support setups that cache these files heavily for timely invalidation.  Thanks to [boxcarpress](http://wordpress.org/support/topic/some-changes-we-made-that-you-might-consider) for the amendments.
* Add CSS class 'contracted' to #toc_container when the table of contents is hidden. Thanks to [Sam](http://wordpress.org/support/topic/hide-link-not-working?replies=6#post-3968019) for suggesting it.
* With smooth scroll enabled, do not use an offset if no admin bar is present and the offset value is default.  This means that public users do not have the offset space at the top.
* New help material for developers under the help tab.
* Added API function: toc_get_index() lets you retrieve a table of contents to be placed within PHP.  Check out the new developer help material for examples.
* Allow anchors to be filterable using toc_url_anchor_target to customise further through code.  Check the new developer help material for an example.  Thanks to [Russell Heimlich](http://dublue.com/plugins/toc/comment-page-4/#comment-1713) for the tip.
* Adjust CSS and JS registration.
* Updated jQuery Smooth Scroll to 1.4.10.
* Fixed: When using the widget, addressed an issue where the index with special characters (such as ' and ") would not link to the correct spot within the content.  Thanks to [esandman](http://wordpress.org/support/topic/problems-with-apostrophes-and-quotation-marks) for raising it.
* Fixed: Saving at Settings > TOC+ resets TOC+ widget options.  Thanks to [Chris](http://dublue.com/plugins/toc/comment-page-4/#comment-1808) for reporting it.

= 1211 =
* Released: 17 November 2012
* New: allow %PAGE_TITLE% to be used in the TOC title.  Note that this also works in the widget title too.  When used, this variable will be replaced with the current page's title.  Thanks to [Peter](http://dublue.com/plugins/toc/comment-page-3/#comment-4782) for the request.
* New: new option to hide the TOC initially.  Thanks to [Jonas](http://dublue.com/plugins/toc/comment-page-2/#comment-852), [Jonathan](http://dublue.com/plugins/toc/comment-page-2/#comment-2161), and [Doc Germanicus](http://dublue.com/plugins/toc/comment-page-4/#comment-5048) for requesting it.
* New: added ability to customise visited TOC link colour.
* New: option to restrict generation to a URL path match.  For example, you can restrict to wiki pages that fall under http://domain/wiki/ by entering /wiki/ into the field.  The setting can be found in the advanced options.  Thanks to [Tux](http://dublue.com/plugins/toc/comment-page-3/#comment-4466) and [Justine Smithies](http://dublue.com/plugins/toc/comment-page-3/#comment-5000) for suggesting it.
* Make regular expressions less greedy.  That means you can have multiple headings on a single line whereas before you needed to ensure each heading was on their own line.  Thanks to [drdamour](http://wordpress.org/support/topic/widget-isnt-showing-up) for raising and providing a fix.
* Also make regular expressions match across multiple lines.  This means you can have your single heading split across many lines.
* Better accessibility: when using smooth scrolling, allow for focus to follow the target, eg tabbing through will continue from the content block you clicked through to.
* Better performance: as requested by a few, javascript files have been consolidated into one and both javascript and CSS files are now minified.
* 'Auto' is now the default width which means it'll take up the needed amount of space up to 100%.  The previous default was a fixed width of 275px.
* Added the ability to exclude entire branches when using [sitemap_pages] and [sitemap_categories] using the exclude_tree attribute.  Thanks to [Benny Powers](http://dublue.com/plugins/toc/comment-page-3/#comment-3607) for requesting it.
* Wrap index numbers around span tags to enable easier CSS customisation.  The spans are have two classes: toc_number and toc_depth_X where X is between 1 and 6.  Thanks to [Matthias Krok](http://dublue.com/plugins/toc/comment-page-3/#comment-3922) for requesting it.
* Moved the 'preserve theme bullets' option into the advanced section.
* Updated and simplified the translation file.
* Fixed: [sitemap_categories] using the wrong label when none was specified.  Thanks to [brandt-net](http://wordpress.org/support/topic/plugin-table-of-contents-plus-sitemap-setting-categories-label-of-sitemap_categories-not-shown) for raising it.  The labels for both [sitemap_pages] and [sitemap_categories] may be removed in a future update as you can insert the title within your content.

= 1208 =
* Released: 2 August 2012
* New: advanced option to prevent the output of this plugin's CSS.  This option allows the site owner to incorporate styles in one of their existing style sheets.  Thanks to [Ivan](http://dublue.com/plugins/toc/comment-page-1/#comment-226) and [Swashata](http://dublue.com/plugins/toc/comment-page-3/#comment-3312) for suggesting it.
* Added Simplified Chinese translation thanks to icedream
* Make more translatable by adding a translation POT file in the languages folder.  Translations welcome!
* Adjust multibyte string detection as reported by [johnnyvaughan](http://wordpress.org/support/topic/plugin-table-of-contents-plus-multibyte-string-detetction)
* Support PHP 5.4.x installations.  Thanks to [Josh](http://dublue.com/plugins/toc/comment-page-3/#comment-3477) for raising it.
* Fixed: -2 appearing in links when using the TOC+ widget.  Thanks to [Arturo](http://dublue.com/plugins/toc/comment-page-3/#comment-3337) for raising it.

= 1207 =
* Released: 23 July 2012
* New: when smooth scrolling is enabled, allow the top offset to be specified to support more than the WordPress admin bar (such as Twitter Bootstrap).  The offset is displayed in the advanced section after you have enabled smooth scrolling.  Thanks to [Nicolaus](http://dublue.com/2012/05/12/another-benefit-to-structure-your-web-pages/#comment-2611) for the suggestion.
* Allow 2 headings to be set as the minimum (used to be 3).  Thanks to [Fran](http://dublue.com/plugins/toc/comment-page-2/#comment-779) for justifying it.
* Run later in the process so other plugins don't alter the anchor links (eg Google Analytics for WordPress).
* Do not show a TOC in RSS feeds.  Thanks to [Swashata](http://dublue.com/plugins/toc/comment-page-3/#comment-2875) for raising it.
* Bump tested version to WordPress 3.5-alpha.
* Added help material about why some headings may not be appearing.
* Added banner image for WordPress repository listing.
* Updated readme.txt with GPLv2 licensing.

= 1112.1 =
* Released: 9 December 2011
* Forgot to update version number.

= 1112 =
* Released: 9 December 2011
* New: auto width option added which takes up only the needed amount of horizontal space up to 100%.
* Removed trailing - or _ characters from the anchor to make it more pretty.
* This plugin's long name has changed from "Table of Contents+" to "Table of Contents Plus".  The short name remains as "TOC+".
* Fixed: when using the TOC shortcode within your content, your post or article would display the TOC on the homepage despite having the exclude from homepage option enabled.  If you also used the "more tag", then you may have resulted with an empty TOC box.  These are now addressed.
* Fixed: all anchors ending with "-2" when no headings were repeated.  This was caused by plugins and themes that trigger `the_content` filter.  The counters are now reset everytime `the_content` is run rather than only on initialisation.

= 1111 =
* Released: 11 November 2011
* New: option to adjust the font size.  Thanks to [DJ](http://dublue.com/plugins/toc/comment-page-1/#comment-323) for the suggestion.  The default remains at 95%.
* New: advanced option to select the heading levels (1 to 6) to be included.  Thanks to those that hinted about wanting to achieve this.
* New: you can now have the TOC appear in the sidebar via the TOC+ widget.  Thanks to [Nick Daugherty](http://dublue.com/plugins/toc/comment-page-1/#comment-172) and [DJ](http://dublue.com/plugins/toc/comment-page-1/#comment-323) for the suggestion.
* The TOC shortcode now supports the *heading_levels* attribute to allow you to limit the headings you want to appear in the table of contents on a per instance basis.  Separate multiple headings with a comma.  For example: include headings 3, 4 and 5 but exclude the others with `[toc heading_levels="3,4,5"]`
* The TOC shortcode also supports the *wrapping* attribute with possible values: "left" or "right".  This lets you wrap text next to the table of contents on a per instance basis.  Thanks to [Phil](http://dublue.com/plugins/toc/comment-page-1/#comment-331) for the suggestion.
* Better internal numbering system to avoid repeated headings.  This means that for non-repeated headings, there is no trailing number in the anchor.
* Consolidated information about shortcodes and their attributes into the help tab.
* Fixed: repeated headings on the same level are no longer broken.  For users with international character sets, please report any strange garbage characters in your headings (eg a character ends up being a question mark, square symbol, or diamond).  Thanks to [Juozas](http://dublue.com/plugins/toc/comment-page-2/#comment-441) for the assistance.
* Fixed: removed PHP notices on a verbosely configured PHP setup.
* Fixed: suppress TOC frame output when heading count was less than the minimum required.
* Note: when removing the last TOC+ widget, please make sure you disable the "Show the table of contents only in the sidebar" option otherwise your table of contents won't appear where you'd expect.  I will look to address this in the future.

= 1109 =
* Released: 12 September 2011
* Adjusted hide action for a smoother transition.
* Apply custom link and hover colours (when selected) to show/hide link in the title.
* Renamed jquery.cookie.min.js to jquery.c.min.js to overcome false positive with [mod_security](https://www.modsecurity.org/tracker/browse/CORERULES-29).  Mod_security would block requests to this file which would break the ability to save a user's show/hide preference.  In some cases, it has also broken other javascript functionality.  Additionally, a better graceful non disruptive fallback is now in place to prevent possible repeat.  Thanks goes to Shonie for helping debug the issue.
* Moved 'visibility option' into 'heading text'.
* Fixed: restored smooth scroll effect for Internet Explorer since 1108.2 introduced 'pathname' checks.

= 1108.2 =
* Released: 26 August 2011
* New: visibility option to show/hide the table of contents.  This option is enabled by default so if you don't want it, turn it off in the options.  Thanks to [Wafflecone](http://dublue.com/plugins/toc/#comment-123) and [Mike](http://dublue.com/plugins/toc/comment-page-1/#comment-160) for the suggestion.
* New: transparent presentation option added.
* New: custom presentation option with colour wheel for you to select your own background, border, title and link colours.
* TOC display on homepage has been disabled by default as most configurations would not require it there.  If you want to enable it, you can do so under a new advanced admin option "Include homepage".
* Make smooth scrolling less zealous with anchors and be more compatible with other plugins that may use # to initiate custom javascript actions.
* Minor admin cross browser CSS enhancements like background gradients and indents.

= 1108.1 =
* Released: 3 August 2011
* Anchor targets (eg anything after #) are now limited to ASCII characters as some mobile user agents do not accept internationalised characters.  This is also a recommendation in the [HTML spec](http://www.w3.org/TR/html4/struct/links.html#h-12.2.1).  A new advanced admin option has been added to specify the default prefix when no characters qualify.
* Make TOC, Pages and Category labels compatible with UTF-8 characters.
* Support ' " \ characters in labels as it was being escaped by WordPress before saving.

= 1108 =
* Released: 1 August 2011
* New: option to hide the title on top of the table of contents.  Thanks to [Andrew](http://dublue.com/plugins/toc/#comment-82) for the suggestion.
* New: option to preserve existing theme specified bullet images for unordered list elements.
* New: option to set the width of the table of contents.  You can select from a number of common widths, or define your own.
* Allow 3 to be set as the minimum number of headings for auto insertion.  The default stays at 4.
* Now accepts heading 1s (h1) within the body of a post, page or custom post type.
* Now creates new span tags for the target rather than the id of the heading.
* Now uses the heading as the anchor target rather than toc_index.
* Adjusted CSS styles for lists to be a little more consistent across themes (eg list-style, margins & paddings).
* Fixed: typo 'heirarchy' should be 'hierarchy'.  Also thanks to Andrew.
* Fixed: addressed an issue while saving on networked installs using sub directories.  Thanks to [Aubrey](http://dublue.com/plugins/toc/#comment-79).
* Fixed: closing of the last list item when deeply nested.

= 1107.1 =
* Released: 10 July 2011
* New: added `[toc]` shortcode to generate the table of contents at the preferred position.  Also useful for sites that only require a TOC on a small handful of pages.
* New: smooth scroll effect added to animate to anchor rather than jump.  It's off by default.
* New: appearance options to match your theme a little bit more.

= 1107 =
* Released: 1 July 2011
* First world release (functional & feature packed)


== Frequently Asked Questions ==

Check out the FAQs / Scenarios at [http://dublue.com/plugins/toc/#Scenarios_FAQs](http://dublue.com/plugins/toc/#Scenarios_FAQs)


== Upgrade Notice ==

Update folder with the latest files.  All previous options will be saved.
