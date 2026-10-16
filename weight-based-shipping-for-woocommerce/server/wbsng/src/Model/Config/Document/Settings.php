<?php declare(strict_types=1);

namespace Aikinomi\Wbsng\Model\Config\Document;


use Aikinomi\Wbsng\Mapping\Context;
use Aikinomi\Wbsng\Mapping\T;


class Settings
{
    public $disableSplitShipping;
    public $breakdownInCart;
    public $breakdownAtCheckout;

    public function __construct(bool $disableSplitShipping = false, bool $breakdownInCart = true, bool $breakdownAtCheckout = true)
    {
        $this->disableSplitShipping = $disableSplitShipping;
        $this->breakdownInCart = $breakdownInCart;
        $this->breakdownAtCheckout = $breakdownAtCheckout;
    }

    public static function unserialize(?array $data): self
    {
        $self = new self();

        if (!isset($data)) {
            return $self;
        }

        $data = Context::of($data);

        $self->disableSplitShipping = $data['disableSplitShipping']->map([T::class, 'bool']);
        $self->breakdownInCart = $data['breakdownInCart']->map([T::class, 'optionalBool'], $self->breakdownInCart);
        $self->breakdownAtCheckout = $data['breakdownAtCheckout']->map([T::class, 'optionalBool'], $self->breakdownAtCheckout);

        return $self;
    }
}