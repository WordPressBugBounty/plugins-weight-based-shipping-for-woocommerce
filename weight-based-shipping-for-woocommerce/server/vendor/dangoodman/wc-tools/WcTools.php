<?php declare(strict_types=1);

namespace WbsVendors\Dgm\WcTools;


class WcTools
{
    public static function isActive(): bool
    {
        if (!function_exists('is_plugin_active')) {
            include_once(ABSPATH.'wp-admin/includes/plugin.php');
        }
        if (is_plugin_active('woocommerce/woocommerce.php')) {
            return true;
        }

        return false;
    }

    public static function purgeShippingCache(): void
    {
        if (!class_exists('WC_Cache_Helper') || !method_exists('WC_Cache_Helper', 'get_transient_version')) {

            global $wpdb;

            /** @noinspection SqlDialectInspection */
            /** @noinspection SqlNoDataSourceInspection */
            $transients = $wpdb->get_col("
                SELECT SUBSTR(option_name, LENGTH('_transient_') + 1)
                FROM `{$wpdb->options}`
                WHERE option_name LIKE '_transient_wc_ship_%'
            ");

            foreach ($transients as $transient) {
                delete_transient($transient);
            }

            return;
        }

        \WC_Cache_Helper::get_transient_version('shipping', true);
    }

    /**
     * Have Woocommerce count the given global (not bound to a shipping zone) shipping methods.
     *
     * Woocommerce assumes a shipping method is either zoned or global, never both, and tells the two apart by
     * supports('shipping-zones') — see wc_get_shipping_method_count(). A method that is both goes missing from the
     * global method count, and a store served solely by such methods looks to Woocommerce like a store with no
     * shipping methods at all. Woocommerce then:
     *
     *  — hides the shipping section entirely (WC_Cart::needs_shipping(), WC_Order::needs_shipping());
     *  — takes the pickup-only branch of wc_get_default_shipping_method_for_package(), which preselects the first
     *    rate of the package, so a delivery rate silently replaces local pickup as the default;
     *  — hides the delivery/pickup switcher of the checkout block, leaving local pickup unreachable altogether
     *    (Blocks\Utils\CartCheckoutUtils::shipping_methods_exist()).
     *
     * Woocommerce caches the counts in a transient, so the missing methods are added to it as the value is read.
     * Every caller accounts for its own methods only, so that the adjustments add up rather than multiply.
     *
     * @param string[] $methodIds Ids of the caller's own shipping methods.
     */
    public static function countGlobalMethods(array $methodIds): void
    {
        add_action('woocommerce_init', function() use ($methodIds) {

            $wcver = WC()->version;

            if (version_compare($wcver, '9.7.0') >= 0) {
                $transient = 'wc_shipping_method_count';
                $field = 'legacy';
                $fields = ['legacy', 'enabled', 'disabled', 'version'];
            }
            else if (version_compare($wcver, '3.6.0') >= 0) {
                $transient = 'wc_shipping_method_count_legacy';
                $field = 'value';
                $fields = ['value', 'version'];
            }
            else {
                return;
            }

            $cached = function($value) use ($fields): bool {

                if (!is_array($value)) {
                    return false;
                }

                foreach ($fields as $key) {
                    if (!isset($value[$key])) {
                        return false;
                    }
                }

                return $value['version'] === \WC_Cache_Helper::get_transient_version('shipping');
            };

            $uncounted = function() use ($methodIds): int {

                // Global instances are registered under their method id, zoned ones under their instance id.
                $methods = WC()->shipping()->get_shipping_methods();

                $count = 0;

                foreach ($methodIds as $id) {
                    $method = $methods[$id] ?? null;

                    if ($method && empty($method->instance_id) && ($method->enabled ?? null) === 'yes') {
                        $count++;
                    }
                }

                return $count;
            };

            add_filter("transient_{$transient}", function($value) use ($transient, $field, $cached, $uncounted) {

                static $running = false;

                // Our own reads below have to see the value as Woocommerce cached it.
                if ($running) {
                    return $value;
                }

                $running = true;
                try {
                    if (!$cached($value)) {

                        // Woocommerce recalculates the counts whenever the cached ones are missing or outdated, and
                        // returns them as they are, without consulting this filter. Have it recalculate and cache
                        // them now, so that there is an up to date value to adjust.
                        wc_get_shipping_method_count(true);
                        $value = get_transient($transient);

                        if (!$cached($value)) {
                            return $value;
                        }
                    }

                    $value[$field] = (int)$value[$field] + $uncounted();
                }
                finally {
                    $running = false;
                }

                return $value;
            }, PHP_INT_MAX);

        }, 0);
    }

    public static function yesNo2Bool($value): bool
    {
        return is_bool($value) ? $value : $value === 'yes';
    }

    public static function bool2YesNo($value): string
    {
        return (bool)$value ? 'yes' : 'no';
    }
}