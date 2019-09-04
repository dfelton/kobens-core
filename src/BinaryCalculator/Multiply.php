<?php

namespace Kobens\Core\BinaryCalculator;

final class Multiply implements MathInterface
{

    /**
     * @var MathInterface
     */
    private static $instance;

    private function __construct() { }

    public static function getInstance(): MathInterface
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @param string $leftOperand
     * @param string $rightOperand
     * @return string
     */
    public function getResult(string $leftOperand, string $rightOperand): string
    {
        $result = \bcmul($leftOperand, $rightOperand, $this->getPrecision($leftOperand, $rightOperand));
        if (\strpos($result, '.') !== false) {
            $result = \rtrim($result, '0');
        }
        return $result;
    }

    /**
     * @param string $leftOperand
     * @param string $rightOperand
     * @return int
     */
    public function getPrecision(string $leftOperand, string $rightOperand): int
    {
        $precision = 0;
        if (\strpos($leftOperand, '.') !== false) {
            $precision += \strlen(\explode('.', $leftOperand)[1]);
        }
        if (\strpos($rightOperand, '.') !== false) {
            $precision += \strlen(\explode('.', $rightOperand)[1]);
        }
        return $precision;
    }

}
