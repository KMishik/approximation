<?php declare(strict_types=1);

namespace Idiacant\Approximation\Approximators;

use Interpolate\Exceptions\AisMainDetZeroException;
use Interpolate\Exceptions\AisShortArgVecException;

final class LogApproximator extends AisMathAbstract
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
        $sumLnX = array_reduce($this->valX, function ($carry,$item) {
                return $carry + log($item);
            }, 0);
        $sumY = array_sum($this->valY);
        $sumQuadLnX = array_reduce($this->valX, function ($carry,$item) {
                return $carry + pow(log($item),2);
            }, 0);
        $arrKeyX = range(0, $this->cardinalityXSet - 1);
        $tempX = &$this->valX;
        $tempY = &$this->valY;
        $sumMulLnXY = array_reduce($arrKeyX, function ($carry, $index) use ($tempX, $tempY) {
                return $carry + ( log($tempX[$index]) * (array_key_exists($index, $tempY) ? $tempY[$index] : 0) );
            }, 0);
        unset($tempX, $tempY, $arrKeyX);

        $detMain = $sumQuadLnX * $this->cardinalityXSet - $sumLnX * $sumLnX;
        if (!$detMain) {
            throw new AisMainDetZeroException();
        }

        $detA = $sumMulLnXY * $this->cardinalityXSet - $sumY * $sumLnX;
        $detB = $sumQuadLnX * $sumY - $sumLnX * $sumMulLnXY;

        $this->coefficients['a'] = $detA / $detMain;
        $this->coefficients['b'] = $detB / $detMain;
    }

    /**
     * @param float $argument
     * @return float
     */
    public function calculateValue(float $argument): float
    {
        $result = $this->coefficients['a'] * log($argument) + $this->coefficients['b'];
        return (float)$result;
    }
}