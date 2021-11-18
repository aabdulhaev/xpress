<?php

namespace common\models\traits;

interface AggregateRoot
{
    /**
     * @return array
     */
    public function releaseEvents(): array;
}
