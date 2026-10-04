<?php
/**
 * Plugin Name: Weight Based Shipping for WooCommerce
 * Plugin URI: https://wordpress.org/plugins/weight-based-shipping-for-woocommerce/
 * Description: Simple yet flexible shipping by country, weight, and subtotal for WooCommerce.
 * License: GPLv2 or later
 * Version: 6.19.0
 * Author: weightbasedshipping.com
 * Author URI: https://weightbasedshipping.com
 * Requires PHP: 7.3
 * Requires at least: 5.8
 * Tested up to: 7.1
 * WC requires at least: 7.0
 * WC tested up to: 11.1
 */

if (!class_exists('WbsVendors\Dgm\WpPluginBootstrapGuard\Guard', false)) {
    require_once(__DIR__.'/server/vendor/dangoodman/wp-plugin-bootstrap-guard/Guard.php');
}
WbsVendors\Dgm\WpPluginBootstrapGuard\Guard::checkPrerequisitesAndBootstrap(__FILE__, __DIR__.'/bootstrap.php');