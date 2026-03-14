<?php

declare(strict_types=1);

namespace statistics\model;

class PlayerStats {

    public function __construct(
        public int $kills = 0,
        public int $deaths = 0,
    ) {}

}