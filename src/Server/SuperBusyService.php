<?php

declare(strict_types=1);

namespace App\Server;

final class SuperBusyService
{
    public function execute(): bool
    {
        $tableOfOKs = [];
        for ($i = 0; $i < 100; $i++) {
            for ($j = 0; $j < 100; $j++) {
                $tableOfOKs[$i][$j] = 'OK';
            }
        }

        return !empty($tableOfOKs);
    }
}
