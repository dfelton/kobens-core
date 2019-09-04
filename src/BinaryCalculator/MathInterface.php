<?php

namespace Kobens\Core\BinaryCalculator;

interface MathInterface
{
    public static function getInstance(): MathInterface;

    public function getResult(string $leftOperand, string $rightOperand): string;

    public function getPrecision(string $leftOperand, string $rightOperand): int;
}
