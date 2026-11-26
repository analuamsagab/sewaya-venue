<?php
declare(strict_types=1);

foreach (glob(__DIR__ . '/components/*.php') ?: [] as $komponen) {
    require_once $komponen;
}
