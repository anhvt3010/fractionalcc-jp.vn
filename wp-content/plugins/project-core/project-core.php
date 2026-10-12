<?php
/**
 * Plugin Name: Fractional CC – Project Core
 * Description: Fractional CC content management, products, combos and versioned default initialization.
 * Version: 0.3.3
 * Requires at least: 6.3
 * Requires PHP: 7.4
 * Text Domain: project-core
 * License: GPL-3.0-or-later
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/src/Admin/website-guide.php';
require_once __DIR__ . '/src/Admin/content-lists.php';
require_once __DIR__ . '/src/Admin/product-quick-edit.php';
require_once __DIR__ . '/src/Fractional/content.php';
require_once __DIR__ . '/src/Fractional/fields.php';
require_once __DIR__ . '/src/Fractional/setup.php';
register_activation_hook( __FILE__, 'project_core_fcc_queue_initial_defaults' );
require_once __DIR__ . '/src/Fractional/featured.php';
require_once __DIR__ . '/src/Fractional/smtp.php';
require_once __DIR__ . '/src/Fractional/orders.php';
