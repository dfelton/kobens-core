<?php

namespace Kobens\Core\BinaryCalculator;

/**
 * This class cannot be instantiated, nor used. It intentionally exists solely
 * to serve as a reminder as to why not to use devision.
 */
final class Divide implements MathInterface
{
    private function __construct()
    {
        throw new \LogicException('Division can potentially yield irrational numbers. Find a way to solve your problem without the use of division');
    }

    public static function getInstance(): MathInterface
    {
        return new self();
    }

    public function getResult(string $leftOperand, string $rightOperand): string
    {
        return '';
    }

    public function getPrecision(string $leftOperand, string $rightOperand): int
    {
        return 0;
    }

}
