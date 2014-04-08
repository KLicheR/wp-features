=== Features ===
Contributors: KLicheR
Donate link: 
Tags: vcs,versioning,backup,database,migrate,environment,deployment,settings,options
Requires at least: 3.3
Tested up to: 3.8.2
Stable tag: 0.0.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A plugin inspired by Drupal's modules ([Features](https://drupal.org/project/features) and [Strongarm](https://drupal.org/project/strongarm)) that enable the versioning of options in Wordpress.

== Description ==

A plugin inspired by Drupal's modules ([Features](https://drupal.org/project/features) and [Strongarm](https://drupal.org/project/strongarm)) that enable the versioning of options in Wordpress.

The general idea is to export a maximum amount of settings and structural content of the database to files to be able to deploy them through [VCS](https://en.wikipedia.org/wiki/Revision_control) (like SVN, GIT, Mercurial).

For now, the only exportable settings are the entries of the "wp-options" table.

= Do you need this? =
If you're the only developer working on you're website, you're having only one development environment (production server) and you do not use [VCS](https://en.wikipedia.org/wiki/Revision_control): no, the only things you need is to close your eyes, cross your fingers and hope that everything go fine.

= How to use it? =
In the admin, a new page called *Features* is accessible under the *Tools* menu. This page list all the settings contain in your *features_options_data.php* file. If one of the setting in your database has not the same value of the one in the file, the *Revert* button will be accessible to click and a text field will show you the value from the database.

* If you want to **replace** what is in the **database** with the value from the file, click the *Revert* button.
* If you want to **replace** what is in the **file** with the value from the database (for versioning), copy the content of the text field to the file.

= Add a new option to the filter =
To add a new option from the *wp_options* table, use the *features_options* filter to alter the *options* array: column value as the *key* and the *option_value* column value as the *value*.

= What's next? =
This plugin is under development. If you know what your doing, you can use it and there will be an upgrade path, at least, a manuel one.

Like [Features](https://drupal.org/project/features), the goal would be to have plugins generation for *features* instead of manually edit the *features_options_data.php* file of the plugin.

== Installation ==

1. Upload `features` plugin directory to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Upgrade notice ==

Make sure to do a backup of your *features_options_data.php* file before updating this plugin.
