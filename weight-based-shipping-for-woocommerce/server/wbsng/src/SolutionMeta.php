<?php declare(strict_types=1);
namespace Aikinomi\Wbsng;

use Aikinomi\Wbsng\Mapping\Context;
use Aikinomi\Wbsng\Model\Calc\Solution;
use Throwable;


class SolutionMeta
{
    public const Key = 'wbsng_solution';

    public static function serialize(Solution $solution): string
    {
        return json_encode($solution->serialize());
    }

    public static function unserialize($json, ?string &$error = null): ?Solution
    {
        if ($json === null || $json === '' || $json === false) {
            return null;
        }

        $ctx = Context::of($json);
        try {

            $data = $ctx->map(function($x) {
                return json_decode($x, true, 512, JSON_THROW_ON_ERROR);
            });

            return Solution::unserialize($data);

        } catch (Throwable $e) {
            $dataStr = var_export($json, true);
            $error =
                "shipping breakdown unserializing at {$ctx->origin($e)->path()}: {$e->getMessage()}\n".
                "DATA: $dataStr\n".
                "{$e->getTraceAsString()}";
            return null;
        }
    }
}