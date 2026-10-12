<?php
/**
 * Plugin Name: Fractional CC – Project Core
 * Description: Fractional CC content management, products, combos and versioned default initialization.
 * Version: 0.3.1
 * Requires at least: 6.3
 * Requires PHP: 7.4
 * Text Domain: project-core
 * License: GPL-3.0-or-later
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/src/Admin/website-guide.php';
require_once __DIR__ . '/src/Fractional/content.php';
require_once __DIR__ . '/src/Fractional/fields.php';
require_once __DIR__ . '/src/Fractional/setup.php';
require_once __DIR__ . '/src/Fractional/smtp.php';
require_once __DIR__ . '/src/Fractional/orders.php';
