<?php
namespace WbsVendors\Dgm\Shengine\Grouping;

use Dgm\Shengine\Interfaces\IGrouping;
use Dgm\Shengine\Interfaces\IItem;


class NoopGrouping implements \WbsVendors\Dgm\Shengine\Interfaces\IGrouping
{
    public function getPackageIds(\WbsVendors\Dgm\Shengine\Interfaces\IItem $item)
    {
        return ['noop'];
    }

    public function multiplePackagesExpected()
    {
        return false;
    }
}
