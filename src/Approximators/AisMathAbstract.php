<?php declare(strict_types=1);

namespace Idiacant\Approximation\Approximators;

use Idiacant\Approximation\Exceptions\ApproximationException;

abstract class AisMathAbstract
{
    /**
     * @var array
     */
    protected array $valX;
    /**
     * @var array
     */
    protected array $valY;
    /**
     * @var int
     */
    protected int $cardinalityXSet;
    /**
     * @var array
     */
    protected array $coefficients;

    /**
     * @param array $argsValues
     * @param array $fnValues
     * @throws ApproximationException
     */
    public function __construct(array $argsValues, array $fnValues) {

        if (count($argsValues) < 2) {
            throw new ApproximationException("\nArgument Vector must have 2 or more values.\nHave received fewer",
                -2);
        }

        $this->valX = $argsValues;
        $this->valY = $fnValues;
        $this->cardinalityXSet = count($this->valX);
        $this->coefficients = [];
    }

    /**
     * @return mixed
     */
    abstract function calculateCoeff();

    /**
     * @param float $argument
     * @return float
     */
    abstract function calculateValue(float $argument): float;

    /**
     * @return array
     */
    public function getCoefficients() : array {
        return $this->coefficients;
    }
}