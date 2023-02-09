<?php
/**
 * Plugin Name: Table of Contents Plus
 * Plugin URI : http://dublue.com/plugins/toc/
 * Description: A powerful yet user friendly plugin that automatically creates a table of contents. Can also output a sitemap listing all pages and categories.
 * Author:      Michael Tran
 * Author URI:  http://dublue.com/
 * Text Domain: table-of-contents-plus
 * Domain Path: /languages
 * Version:     2302
 * License:     GPL2
 */

/**
Table of Contents Plus is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

Table of Contents Plus is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Table of Contents Plus. If not, see
https://github.com/zedzedzed/table-of-contents-plus/blob/main/LICENSE.md
*/

/**
 * GPL licenced Oxygen icon used for the colour wheel
 * http://www.iconfinder.com/search/?q=iconset%3Aoxygen
 */

require_once __DIR__ . '/class-toc-plus.php';

// do the magic
$toc_plus = new TOC_Plus();
