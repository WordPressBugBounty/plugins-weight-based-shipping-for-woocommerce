<?php
namespace WbsVendors\Dgm\Shengine;

use Dgm\Shengine\Interfaces\IGrouping;
use Dgm\SimpleProperties\SimpleProperties;


/**
 * @property-read bool $capture
 * @property-read IGrouping $grouping
 * @property-read bool $requireAllPackages
 */
class RuleMatcherMeta extends \WbsVendors\Dgm\SimpleProperties\SimpleProperties
{
    public function __construct($capture, \WbsVendors\Dgm\Shengine\Interfaces\IGrouping $grouping, $requireAllPackages = false)
    {
        $this->capture = $capture;
        $this->grouping = $grouping;
        $this->requireAllPackages = $requireAllPackages;
    }


    protected $capture;
    protected $grouping;
    protected $requireAllPackages;
}