<?php declare(strict_types=1);

namespace Idiacant\Approximation\Approximators;

use Idiacant\Approximation\Exceptions\ApproximationException;

final class ExpApproximator extends AbstractApproximator
{
    public function __construct()
    {
        $this->coefficients = ['a' => 0.0, 'b' => 0.0];
    }

    /**
     * @throws ApproximationException
     */
    public function calculateCoeff()
    {
        $sumX = array_sum($this->valX);
        $sumLnY = array_reduce($this->valY, function ($carry,$item) {
                return $carry + ($item > 0 ? log($item) : 0);
            }, 0);
        $sumQuadX = array_reduce($this->valX, function ($carry,$item) {
            return $carry + pow($item,2);
        }, 0);
        $arrKeyX = range(0, $this->cardinalityXSet - 1);
        $tempX = &$this->valX;
        $tempY = &$this->valY;
        $sumMulXLnY = array_reduce($arrKeyX, function ($carry, $index) use ($tempX, $tempY) {
            return $carry + ( $tempX[$index] * (array_key_exists($index, $tempY) && $tempY[$index] > 0 ?
                        log($tempY[$index]) : 0) );
        }, 0);
        unset($tempX, $tempY, $arrKeyX);

        $detMain = $sumQuadX * $this->cardinalityXSet - $sumX * $sumX;
        if (!$detMain) {
            throw new ApproximationException("\nMain Determinant is equal 0.\nUse Gauss method instead", -3);
        }

        $detA = $sumMulXLnY * $this->cardinalityXSet - $sumLnY * $sumX;
        $detLnB = $sumQuadX * $sumLnY - $sumX * $sumMulXLnY;

        $this->coefficients['a'] = $detA / $detMain;
        $coefLnB = $detLnB / $detMain;
        $this->coefficients['b'] = exp($coefLnB);
    }

    /**
     * @param float $argument
     * @return float
     */
    public function calculateValue(float $argument): float
    {
        $result = $this->coefficients['b'] * exp($this->coefficients['a'] * $argument);
        return (float)$result;
    }
}