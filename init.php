<?php
/**
 * Load the main plugin functions, hooks, and settings.
 *
 * @author Iron Bound Designs
 * @since  1.0
 */

namespace ITETOS;

use ITETOS\Product\Feature\Base;

Settings::init();

new Hooks();
new Base();