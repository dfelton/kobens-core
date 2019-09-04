<?php

namespace Kobens\Core\BinaryCalculator;

final class Add implements MathInterface
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
        return \bcadd($leftOperand, $rightOperand, $this->getPrecision($leftOperand, $rightOperand));
    }

    /**
     * @param string $leftOperand
     * @param string $rightOperand
     * @return int
     */
    public function getPrecision(string $leftOperand, string $rightOperand): int
    {
        $precision = 0;
        foreach ([$leftOperand, $rightOperand] as $value) {
            if (\strpos($value, '.') !== false) {
                $length = \strlen(\explode('.', $value)[1]);
                if ($length > $precision) {
                    $precision = $length;
                }
            }
        }
        return $precision;
    }

}
