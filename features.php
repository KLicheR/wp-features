<?php
/*
Plugin Name: Features
Description: Help to migrate entries from "wp_options" table from multiple environments.
Version: 0.0.1
Author: KLicheR
Author URI: ---
License: GPLv2 or later
*/

/*  Copyright 2013  Kristoffer Laurin-Racicot  (email : kristoffer.lr@gmail.com)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as 
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

require_once dirname(__FILE__) . '/features_options.php';

class Features {
	public function __construct() {
		if (is_admin()) {
			new Features_options();
		}
	}
}
new Features();
?>