<?php

namespace Kobens\Core\BinaryCalculator;

final class Compare implements MathInterface
{
    const EQUAL              = '0';
    const RIGHT_LESS_THAN    = '1';
    const LEFT_GREATER_THAN  = '1';
    const LEFT_LESS_THAN     = '-1';
    const RIGHT_GREATER_THAN = '-1';

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
        // Return string to adhere to Interface. Calling code will have to expect this.
        return (string) \bccomp($leftOperand, $rightOperand, $this->getPrecision($leftOperand, $rightOperand));
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
