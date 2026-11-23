<?php declare(strict_types=1);
namespace WbsVendors\Dgm\Shengine\Woocommerce\Converters;

use Dgm\Arrays\Arrays;
use Dgm\Shengine\Interfaces\IRate;
use Dgm\Shengine\Model\Rate;
use WC_Shipping_Rate;


class RateConverter
{
    /**
     * @param WC_Shipping_Rate[] $_rates
     * @return IRate[]
     */
    public static function fromWoocommerceToCore(array $_rates): array
    {
        return \WbsVendors\Dgm\Arrays\Arrays::map($_rates, function(WC_Shipping_Rate $_rate) {
            return new \WbsVendors\Dgm\Shengine\Model\Rate($_rate->get_cost(), $_rate->get_label(), null, $_rate->get_meta_data());
        });
    }

    /**
     * @param IRate[] $rates
     * @param bool $zeroNegatives  If true negative costs will be replaced with zero.
     * @return array  Woocommerce rates to add with WC_Shipping_Method::add_rate()
     */
    public static function fromCoreToWoocommerce(
        array   $rates,
        string  $defaultTitle,
        ?string $idPrefix = null,
        bool    $zeroNegatives = false
    ): array {
        $_rates = [];

        $wcRateIdsCounters = [];

        foreach ($rates as $rate) {

            $title = $rate->getTitle();
            if (!isset($title) || $title === '') {
                $title = $defaultTitle;
            }

            $idParts = [];

            $hash = substr(md5($title), 0, 8);
            $idParts[] = $hash;

            $slug = strtolower($title);
            $slug = preg_replace('/[^a-z0-9]+/', '_', $slug);
            $slug = preg_replace('/_+/', '_', $slug);
            $slug = trim($slug, '_');
            if ($slug !== '') {
                $idParts[] = $slug;
            }

            $id = join('_', $idParts);

            $count = $wcRateIdsCounters[$id] = ($wcRateIdsCounters[$id] ?? -1) + 1;
            if ($count > 0) {
                $id .= '_'.($count+1);
            }

            if (isset($idPrefix)) {
                $id = $idPrefix.$id;
            }

            $cost = $rate->getCost();
            if ($zeroNegatives && $cost <= 0) {
                $cost = 0;
            }

            $_rates[] = [
                'id' => $id,
                'label' => $title,
                'cost' => $cost,
                'taxes' => $rate->isTaxable() === false ? false : null,
                'meta_data' => $rate->getMeta(),
            ];
        }

        return $_rates;
    }
}