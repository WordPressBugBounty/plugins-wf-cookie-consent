=== WF Cookie Consent ===
Contributors: wunderfarm
Donate link: https://www.wunderfarm.com
Tags: cookie consent, cookie law, gdpr, privacy, cookies
Requires at least: 4.7
Tested up to: 7.0
Requires PHP: 7.0
Stable tag: 1.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The `wunderfarm-way` to show how your website complies with the EU Cookie Law - very easy, 100% responsive and with multi-language support!

== Description ==

WF Cookie Consent shows the user a clear message that the site uses cookies.
This plugin supports multi-language installations with the polylang-plugin from Chouby or WPML-plugin from wpml.org. It has a wide array of settings for controlling the style and contents.
WF Cookie Consent is the "wunderfarm-way" to show how your website complies with the EU Cookie Law.

== Installation ==

1. Upload `WF Cookie Consent` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. If you want to set custom settings, go to Settings menu > WF Cookie Consent.

== Screenshots ==
1. Example showing the wf-cookie-consent-bar
2. WF Cookie Consent options section
3. Multi-language support

== Changelog ==

= 1.3 =
New: choose from ready-made banner styles (Light, Dark, Minimal, Card) with optional automatic dark mode that follows the visitor's browser setting, plus optional custom colors. All styling moved from inline CSS in JavaScript to a proper enqueued stylesheet using CSS classes and custom properties - no inline styles and no !important. The close button is now pure CSS instead of an embedded image.

= 1.2.1 =
Housekeeping: updated readme (name, tags, WordPress 7.0 compatibility). Security hardening: stricter output escaping on the settings page, input sanitization, and a sanitize callback for stored options.

= 1.2.0 =
Removed iubenda integration.

= 1.1.4 =
Important fix for a XSS vulnerability on the 'Settings-Page'.

= 1.1.3 =
Improved performance by loading a minified js file. Added rel="noopener" to the link referring to the policy page.

= 1.1.2 =
Added a direct link to generate a cookie policy.

= 1.1.1 =
Added Hungarian Language. Minor Bugfixes.

= 1.1.0 =
Better admin default options, performance improvements and iubenda integration added.

= 1.0.1 =
Bugfix: Unescaped HTML in text output

= 1.0.0 =
Improved performance by loading WF Cookie Consent later

= 0.9.9 =
Compatibility for Polylang Version 1.8

= 0.9.8 =
Compatibility for WPML Version 3.2 and above

= 0.9.7 =
Improved compatibility with WPML Multilingual CMS 3.2.6

= 0.9.5 =
Compatibility for IE8 and previous IE versions

= 0.9.4 =
Bugfix: The page-selector is now showing all entries.

= 0.9.3 =
Updated Readme and Upgrade Notice.

= 0.9.2 =
Bugfix: Compatibility with Polylang and WPML.

= 0.9.1 =
Improved compatibility with Polylang and WPML.

= 0.9.0 =
Bugfix: For websites using earlier versions of WPML.

= 0.8.9 =
Bugfix: Include js file.

= 0.8.8 =
Bugfix: Improved wf_get_languages().

= 0.8.7 =
Improved compatibility with other plugins and themes.

= 0.8.6 =
Bugfix: Set cookie path (thx for the bug report adfasyxcv!)

= 0.8.5 =
Improved compatibility with other plugins.

= 0.8.4 =
Escaped text strings for echoing in JS & option field descriptions.

= 0.8.3 =
Bugfix: Custom settings with Polylang.

= 0.8.2 =
Bugfixes: Custom fields for default language (en) and selected more-info page.

= 0.8.1 =
Support for WPML and Polylang.

== Upgrade Notice ==

= 0.9.2 =
Fixes a list of bugs that settings could not be saved.
