=== Bogo ===
Contributors: takayukister
Tags: multilingual, localization, language, locale, admin
Requires at least: 3.5
Tested up to: 3.5.1
Donate link: http://www.pledgie.com/campaigns/17860
Stable tag: 2.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A straight-forward multilingual plugin. No more double-digit custom DB tables or hidden HTML comments that could cause you headaches later on.

== Description ==

http://ideasilo.wordpress.com/bogo/

Bogo is a straight-forward multilingual plugin for WordPress.

The core of WordPress itself has [the built-in localization capability](http://codex.wordpress.org/WordPress_in_Your_Language) so you can use the dashboard and theme in one language other than English. Bogo expands this capability to let you easily build a multilingual blog on a single WordPress install.

Here are some technical details for those interested. Bogo plugin assigns [one language per post](http://codex.wordpress.org/Multilingual_WordPress#Different_types_of_multilingual_plugins). It plays nice with WordPress – Bogo does not create any additional custom table on your database, unlike some other plugins in this category. This design makes Bogo a solid, reliable and conflict-free multilingual plugin.

= Getting Started with Bogo =

1. Install language files

	First, make sure you have installed language files (*.mo) for all languages used in your site. If you have a localized version of WordPress installed, you should already have these files for that language.

	If you don't have language files yet, you can download them from [the WordPress Localization Repository](http://codex.wordpress.org/Translating_WordPress#WordPress_Localization_Repository).

2. Select your language for admin screen (dashboard)

	Bogo allows each user to select a language for his/her own WordPress admin screen. Logged-in users can switch languages from the drop-down menu on the Admin Bar.

	If the Admin Bar is hidden, you can also switch language on your Profile page.

3. Translate posts and pages

	You can translate any posts and pages into your language you have set at the step 2 above with the Language post box.

	WordPress saves the contents of each post or page as usual, but Bogo adds '_locale' post_meta data. The '_locale' holds the language code of the post.

4. Add a language switcher to your site

	You will want to place a language switcher on your site that allows visitors to switch languages they see on the site. The easiest method is using the Language Switcher widget included in Bogo.

	Bogo also provides a shortcode "[bogo]" to allow you to place a language switcher inside a post or page content by simply inserting [bogo]. To embed a language switcher directly into your theme's template file, use this shortcode as follows:

	`<?php echo do_shortcode( '[bogo]' ); ?>`

== Installation ==

1. Upload the entire `bogo` folder to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.

== Screenshots ==

1. You can switch your admin language in the Admin Bar.
1. The Language Post Box manages language and translations of the Post/Page.