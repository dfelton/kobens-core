<?php

namespace Kobens\Core;

interface EmergencyShutdownInterface
{
    public function isShutdownModeEnabled(): bool;

    public function enableShutdownMode(\Exception $e): void;
}
