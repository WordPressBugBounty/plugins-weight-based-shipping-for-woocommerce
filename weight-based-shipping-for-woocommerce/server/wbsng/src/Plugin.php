<?php declare(strict_types=1);

namespace Aikinomi\Wbsng;

use Automattic\WooCommerce\StoreApi\Schemas\V1\CartSchema;
use WC_Order_Item;
use WC_Order_Item_Shipping;
use WC_Shipping_Rate;


class Plugin
{
    public const ID = 'wbsng';

    /**
     * @readonly
     * @var PluginMeta
     */
    public $meta;
    

    public static function setupOnce(string $entrypoint): void
    {
        if (!isset(self::$instance)) {
            $plugin = new Plugin($entrypoint);
            $plugin->setup();
            self::$instance = $plugin;
        }
    }

    public static function instance(): self
    {
        return self::$instance;
    }

    public function setup(): void
    {
        Api::init();

        Multicurrency::install();
        
        add_filter('woocommerce_shipping_methods', static function($shippingMethods) {

            $idx = array_search('wbs', array_keys($shippingMethods));
            if ($idx === false) {
                $idx = count($shippingMethods);
            }

            return
                array_slice($shippingMethods, 0, $idx, true) +
                [self::ID => ShippingMethod::class] +
                array_slice($shippingMethods, $idx, null, true);
        });

        // Show the solution breakdown on the frontend.
        add_action('woocommerce_after_shipping_rate', function(WC_Shipping_Rate $rate) {
            $data = $rate->get_meta_data()[SolutionMeta::Key] ?? null;
            if ($data !== null && self::breakdownVisibility($rate)[is_checkout() ? 'checkout' : 'cart']) {
                $this->renderSolution($data);
            }
        });

        // Show the solution breakdown in the Cart and Checkout blocks. They don't fire woocommerce_after_shipping_rate,
        // so the breakdown is rendered server-side and passed to the client via the Store API cart extensions.
        add_action('init', function() {
            woocommerce_store_api_register_endpoint_data([
                'endpoint' => CartSchema::IDENTIFIER,
                'namespace' => self::ID,
                'data_callback' => function() { return $this->renderBlocksSolutions(); },
                'schema_callback' => function() { return []; },
                'schema_type' => ARRAY_A,
            ]);
        });
        foreach (['woocommerce/cart', 'woocommerce/checkout'] as $block) {
            add_filter("render_block_$block", function(string $content) {
                $paths = $this->meta->paths;
                wp_enqueue_style('wbsng-solution-details-css', $paths->serverAssetUrl('solution-details.css'));
                wp_enqueue_script(
                    'wbsng-solution-blocks-js',
                    $paths->serverAssetUrl('solution-blocks.js'),
                    ['wc-blocks-data-store', 'wp-data'],
                    $this->meta->version,
                    true
                );
                return $content;
            });
        }

        // Show the solution breakdown on the backend order page.
        // At the moment, the breakdown is not editable and is not reset on shipping method change.
        add_action('woocommerce_before_order_itemmeta', function($_, WC_Order_Item $orderItem) {
            if ($orderItem instanceof WC_Order_Item_Shipping) {
                foreach ($orderItem->get_meta_data() as $meta) {
                    if ($meta->key === SolutionMeta::Key) {
                        $this->renderSolution($meta->value, false);
                    }
                }
            }
        }, 10, 2);

        add_filter('woocommerce_hidden_order_itemmeta', function(array $keys) {
            $keys[] = SolutionMeta::Key;
            return $keys;
        });
    }


    /** @var self */
    private static $instance;

    private function __construct(string $entrypoint)
    {
        $this->meta = new PluginMeta(wp_normalize_path($entrypoint));
    }

    private function renderSolution($data, $silent = true): void
    {
        $solution = SolutionMeta::unserialize($data, $error);
        if ($error && $silent) {
            wc_get_logger()->error($error);
            return;
        }

        $paths = $this->meta->paths;
        wp_enqueue_style('wbsng-solution-details-css', $paths->serverAssetUrl('solution-details.css'));
        include($paths->tpl('solution.tpl.php'));
    }

    /**
     * @return array<string|int, array<string, array{html: string, cart: bool, checkout: bool}>>
     *     Rendered breakdowns by package id and rate id.
     */
    private function renderBlocksSolutions(): array
    {
        $html = [];
        foreach (WC()->shipping()->get_packages() as $key => $package) {
            /** @var WC_Shipping_Rate $rate */
            foreach ($package['rates'] ?? [] as $rate) {
                $data = $rate->get_meta_data()[SolutionMeta::Key] ?? null;
                if ($data === null) {
                    continue;
                }
                $visibility = self::breakdownVisibility($rate);
                if (!$visibility['cart'] && !$visibility['checkout']) {
                    continue;
                }
                ob_start();
                $this->renderSolution($data);
                $html[$package['package_id'] ?? $key][$rate->get_id()] = ['html' => ob_get_clean()] + $visibility;
            }
        }
        return $html;
    }

    /**
     * Read from the rate's method config to keep it out of the rate meta, which WooCommerce copies to the order.
     *
     * @return array{cart: bool, checkout: bool}
     */
    private static function breakdownVisibility(WC_Shipping_Rate $rate): array
    {
        $settings = (new ShippingMethod($rate->get_instance_id()))->config()->settings;
        return [
            'cart' => $settings->breakdownInCart,
            'checkout' => $settings->breakdownAtCheckout,
        ];
    }
}