<?php declare(strict_types=1);

namespace Idiacant\Approximation\Approximators;

use Interpolate\Exceptions\AisMainDetZeroException;
use Interpolate\Exceptions\AisShortArgVecException;

final class LinearApproximator extends AisMathAbstract
{
    /**
     * @param array $argsValues
     * @param array $fnValues
     * @throws AisShortArgVecException
     */
    public function __construct(array $argsValues, array $fnValues)
    {
        parent::__construct($argsValues, $fnValues);
        $this->coefficients = ['a' => 0.0, 'b' => 0.0];
    }

    /**
     * @throws AisMainDetZeroException
     */
    public function calculateCoeff()
    {
        $sumX = array_sum($this->valX);
        $sumY = array_sum($this->valY);
        $sumQuadX = array_reduce($this->valX, function ($carry,$item) {
                return $carry + pow($item,2);
            }, 0);
        $arrKeyX = range(0, $this->cardinalityXSet - 1);
        $tempX = &$this->valX;
        $tempY = &$this->valY;
        $sumMulXY = array_reduce($arrKeyX, function ($carry, $index) use ($tempX, $tempY) {
                return $carry + ( $tempX[$index] * (array_key_exists($index, $tempY) ? $tempY[$index] : 0) );
            }, 0);
        unset($tempX, $tempY, $arrKeyX);

        $detMain = $sumQuadX * $this->cardinalityXSet - $sumX * $sumX;
        if (!$detMain) {
            throw new AisMainDetZeroException();
        }

        $detA = $sumMulXY * $this->cardinalityXSet - $sumY * $sumX;
        $detB = $sumQuadX * $sumY - $sumX * $sumMulXY;

        $this->coefficients['a'] = $detA / $detMain;
        $this->coefficients['b'] = $detB / $detMain;
    }

    /**
     * @param float $argument
     * @return float
     */
    public function calculateValue(float $argument): float
    {
        $result = $this->coefficients['a'] * $argument + $this->coefficients['b'];
        return (float)$result;
    }
}