# Savellab Plugin Boilerplate

## Requirements

* `nodejs` installed

* `composer` installed
* `PHP 7.0` installed on your local machine

## Setup

1. Change **plugin_name** in `/package.hson` file with the plugin folder name you are going to develop
1. Change **{Plugin_Name}**, **{Plugin_Description}**, **{Plugin_Prefix}**, and **{Plugin_URI}** with the appropriate information
1. Change **{Author}**, **{Author_URI}**, **{Developer_name}**, and **{Developer_URI}** with the appropriate information
1. Change all occurrences of namespace **Plugin_Scope** with your desire namespace
1. Change all occurrences of **{Text_Domain}** with the plugin's name which should be the same as folder name
1. Change Text_Domain.pot file's which should be the same as folder name
1. In case the plugin depends on Woocommerce you should define it in the `/bootstrap.php` file
    * For enabling WC support and Settings page `define(__NAMESPACE__ . '\WOOCOMMERCE_PLUGIN', true);`
1. In case the plugin uses Woocommerce payment gateway you should define it in the  `/bootstrap.php` file
    * So the settings page will appear`define(__NAMESPACE__ . '\PAYMENT_GATEWAY', true);`
1. In case the plugin depends on Gravity Forms you should define it in the `/bootstrap.php` file
    * For enabling GF support and Settings page `define(__NAMESPACE__ . '\GFORMS_ADDON', true);`
    * Also define `define(__NAMESPACE__ . '\GFORMS_ADDON_NAME', 'GF_Addon_Name');` and `define(__NAMESPACE__ . '\GFORMS_CLASS_NAME', 'GF_Class_Name');`
    * Then replace **GF_Addon_Name** with a `One_Word` name over the project
    * Then replace **GF_Class_Name** with a `GFPluginNameAddon` class name over the project
1. Run `npm install`
1. If you're going to use composer run `composer install` and uncomment autoloader including at `/bootstrap.php`
1. For compiling:
   * run `npm run dev` for development

      * **Note:** after the initial build, webpack will continue to watch for changes in any of the resolved files and it will sync the root folder
   * run `npm run prod` for production

1. for testing:
   * run `npm test` for running JS and PHP tests
   
1. After steps above have been done, remove this README file until the line below and refresh all the information

=== {Plugin_Name} ===
Contributors: (this should be a list of wordpress.org userid's)
Donate link: {Author_URI}
Tags: ecommerce
Requires at least: 5.0
Tested up to: 5.0
Stable tag: 5.0
Requires PHP: 7.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

{Plugin_Description}

== Description ==

This is the long description.  No limit, and you can use Markdown (as well as in the following sections).

For backwards compatibility, if this section is missing, the full length of the short description will be used, and
Markdown parsed.

A few notes about the sections above:

*   "Contributors" is a comma separated list of wordpress.org usernames
*   "Tags" is a comma separated list of tags that apply to the plugin
*   "Requires at least" is the lowest version that the plugin will work on
*   "Tested up to" is the highest version that you've *successfully used to test the plugin*. Note that it might work on
higher versions... this is just the highest one you've verified.
*   Stable tag should indicate the Subversion "tag" of the latest stable version, or "trunk," if you use `/trunk/` for
stable.

    Note that the `readme.txt` of the stable tag is the one that is considered the defining one for the plugin, so
if the `/trunk/readme.txt` file says that the stable tag is `4.3`, then it is `/tags/4.3/readme.txt` that'll be used
for displaying information about the plugin.  In this situation, the only thing considered from the trunk `readme.txt`
is the stable tag pointer.  Thus, if you develop in trunk, you can update the trunk `readme.txt` to reflect changes in
your in-development version, without having that information incorrectly disclosed about the current stable version
that lacks those changes -- as long as the trunk's `readme.txt` points to the correct stable tag.

    If no stable tag is provided, it is assumed that trunk is stable, but you should specify "trunk" if that's where
you put the stable version, in order to eliminate any doubt.

== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Upload the plugin files to the `/wp-content/plugins/plugin-name` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress
1. Use the Settings->Plugin Name screen to configure the plugin
1. (Make your instructions match the desired user flow for activating and installing your plugin. Include any steps that might be needed for explanatory purposes)


== Frequently Asked Questions ==

= A question that someone might have =

An answer to that question.

= What about foo bar? =

Answer to foo bar dilemma.

== Screenshots ==

1. This screen shot description corresponds to screenshot-1.(png|jpg|jpeg|gif). Note that the screenshot is taken from
the /assets directory or the directory that contains the stable readme.txt (tags or trunk). Screenshots in the /assets
directory take precedence. For example, `/assets/screenshot-1.png` would win over `/tags/4.3/screenshot-1.png`
(or jpg, jpeg, gif).
2. This is the second screen shot

== Changelog ==

= 1.0 =
* A change since the previous version.
* Another change.

= 0.5 =
* List versions from most recent at top to oldest at bottom.

== Upgrade Notice ==

= 1.0 =
Upgrade notices describe the reason a user should upgrade.  No more than 300 characters.

= 0.5 =
This version fixes a security related bug.  Upgrade immediately.

== Arbitrary section ==

You may provide arbitrary sections, in the same format as the ones above.  This may be of use for extremely complicated
plugins where more information needs to be conveyed that doesn't fit into the categories of "description" or
"installation."  Arbitrary sections will be shown below the built-in sections outlined above.
