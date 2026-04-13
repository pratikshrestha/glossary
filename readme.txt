=== OP Glossary Plugin ===
Contributors: pratikshrestha
Tags: glossary, definitions, custom post type, shortcode
Requires at least: 5.8
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.00.11
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.en.html

Create a glossary hub page with searchable glossary terms and dedicated definition pages.

== Description ==

OP Glossary Plugin adds a glossary custom post type for managing definitions and publishing them on the front end.

Features included in this version:

* Glossary terms managed through a custom post type
* Searchable glossary hub rendered with the `[op-glossary]` shortcode
* A-Z filtering on the hub page
* Individual definition pages at `/glossary/[term-name]`
* Alphabetical previous and next links on single definition pages
* Theme override support for both hub and single glossary templates
* Schema.org `DefinedTerm` markup on glossary term pages

== Installation ==

1. Upload the plugin folder to `/wp-content/plugins/` or install it through WordPress.
2. Activate the plugin from the WordPress admin.
3. Create glossary entries under `Glossary Terms` in the admin menu.
4. Go to `Settings > Permalinks` and save once if your site does not pick up the new routes immediately.

== Usage ==

After activation, set up the glossary hub page:

1. Create a normal WordPress page titled `Glossary`.
2. Set the page slug to `glossary`.
3. Add the shortcode `[op-glossary]` to the page content.
4. Publish the page.

This gives you the expected URL structure:

* Hub page: `/glossary/`
* Definition page: `/glossary/[term-name]`

The shortcode supports these attributes:

* `[op-glossary]` to show all terms
* `[op-glossary categories="category-slug"]` to filter by glossary category
* `[op-glossary limit="20"]` to limit the number of displayed terms

== Search And Navigation ==

The glossary hub includes:

* A search field for filtering glossary terms
* A-Z navigation for jumping between terms by their starting letter
* Linked term titles above each definition excerpt/content block

Single definition pages include:

* The glossary term title
* The glossary definition content
* Previous and next links ordered alphabetically by glossary title

== Theme Overrides ==

You can override the plugin templates in your active theme:

* Hub template: add `op-glossary-template.php` to your theme
* Single term template: add `op-glossary-single.php` to your theme

Breadcrumbs are intentionally not rendered by the plugin. Add them in your theme template if needed.

== Release Builder ==

A local release builder is included for packaging the plugin:

* Script: `builder/build-release.sh`
* Output: `dist/glossary-1.00.xx.zip`
* Behavior: bumps the plugin version to the next `1.00.xx` release, prepends a changelog entry, creates the zip, and commits the tracked version changes with a release message

Examples:

* Preview the next version without changing files: `bash builder/build-release.sh --dry-run`
* Build the next release zip and create a release commit: `bash builder/build-release.sh`
* Build, commit, and push the release commit: `bash builder/build-release.sh --push`

The `builder/` directory and generated zip files are excluded from Git.

== Frequently Asked Questions ==

= Why is the hub page not created automatically? =

The plugin keeps the hub page as a normal WordPress page so editors can control page-level content, SEO settings, and theme layout.

= Why do glossary terms use `/glossary/term-name/` URLs? =

The plugin registers glossary terms under the `glossary` rewrite base for cleaner, more consistent glossary URLs.

= What should I do after changing permalink settings or activating the plugin? =

If routes do not work immediately, visit `Settings > Permalinks` and click `Save Changes` once to refresh rewrite rules.

== Changelog ==

= 1.00.11 =

* Release package update

= 1.00.10 =

* Release package update

= 1.00.09 =

* Release package update

= 1.00.08 =

* Release package update

= 1.00.07 =

* Release package update

= 1.00.06 =

* Release package update

= 1.00.05 =

* Release package update

= 1.00.04 =

* Release package update

= 1.0.03 =

* Release package update

= 1.0.02 =

* Release package update

= 1.0.01 =

* Release package update

= 1.0.0 =

* Added glossary single-term template support
* Changed glossary term URLs to use the `/glossary/` base
* Added alphabetical previous and next navigation on single terms
* Expanded plugin usage documentation
