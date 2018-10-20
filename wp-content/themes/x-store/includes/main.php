<?php
/**
 * Load files.
 *
 * @package x-Store
 */

// Customizer additions.
require_once trailingslashit( get_template_directory() ) . '/includes/customizer/customizer.php';

// Load core functions.
require_once trailingslashit( get_template_directory() ) . '/includes/customizer/core.php';

// Load helper functions.
require_once trailingslashit( get_template_directory() ) . '/includes/helpers.php';

// Custom template tags for this theme.
require_once trailingslashit( get_template_directory() ) . '/includes/template-tags.php';

// Custom functions that act independently of the theme templates.
require_once trailingslashit( get_template_directory() ) . '/includes/extras.php';

// Load widgets.
require_once trailingslashit( get_template_directory() ) . '/includes/widgets/widgets.php';