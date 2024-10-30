=== Markets ===
Contributors: rixeo
Tags: e-commerce, ecommerce, sync, product-sync, backup, restore, dukagate, dukapress, marketpress, dukagate sync, dukapress sync, marketpress sync
Requires at least: 4.0
Tested up to: 5.1.1
Stable tag: 2.0.1
Requires PHP: 5.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Markets is an open source e-commerce sync solution built for Wordpress.

== Description ==


Markets is a WordPress plugin that allows you to copy products from one ecommerce plguin to another. The plugin makes it easier for you to test or export your products on another plugin


Main Features:

* Select your backup type
* Backup your products
* Copy products from one e-commerce plugin to the other. Currently supported are Dukapress, Dukagate and MarktPress


I am still working on getting all product items copied and restored well. Currently the following are not backed up:

* Meta Data
* Images
* Eternal service backup like Google Drive, Amazon, Drop Box. This will allow copying and migrating products from any installation that you own


== Installation ==

1. Upload the Markets folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Once the plugin is activated there will be an admin section where you can configure credentials

== Frequently Asked Questions ==

= How to start =
First make sure the supported plugins are active. Then do a backup of the products

= Restoring Products =
We will restore the products from the latest backup and delete all the ones in the current installation

= Copying Products =
Products will be copied from the last backup done



== Changelog ==

= 2.0.1 =
Working on new release to bring marketplaces to your sites

= 2.0 =
* Plugin data stored in current installation
* Code redone
* Copying of products