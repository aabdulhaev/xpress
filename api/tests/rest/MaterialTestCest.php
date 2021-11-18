<?php

namespace rest;

use common\tests\fixtures\SectionFixture;

/**
 * Class MaterialTestCest
 * @package rest
 *
 * @noinspection PhpUnused
 */
class MaterialTestCest
{
    public function _fixtures(): array
    {
        return [
            'sections' => SectionFixture::class,
        ];
    }
}
