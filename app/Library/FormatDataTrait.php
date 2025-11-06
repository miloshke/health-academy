<?php

namespace App\Library;

trait FormatDataTrait
{
    /**
     * Format date to a readable format
     */
    protected function dateAt($date): ?string
    {
        if (!$date) {
            return null;
        }

        return $date instanceof \DateTimeInterface
            ? $date->format('Y-m-d H:i:s')
            : $date;
    }
}
