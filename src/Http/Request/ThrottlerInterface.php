<?php

namespace Kobens\Core\Http\Request;

interface ThrottlerInterface
{
    public function throttle(): void;
}
