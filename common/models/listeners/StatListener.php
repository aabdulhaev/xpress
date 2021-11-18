<?php

namespace common\models\listeners;

use common\models\events\Stats;
use common\models\SessionRating;

class StatListener
{
    public function handle(Stats $event): void
    {
        /** @var SessionRating $rating */
        $rating = $event->sessionRating;
        \common\components\helpers\RatingHelper::update($rating);
    }
}
