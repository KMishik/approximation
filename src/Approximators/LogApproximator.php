<?php declare(strict_types=1);

namespace Idiacant\Approximation\Approximators;

use Idiacant\Approximation\Exceptions\ApproximationException;

final class LogApproximator extends AbstractApproximator
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
        $sumLnX = array_reduce($this->valX, function ($carry,$item) {
                return $carry + ($item > 0 ? log($item) : 0);
            }, 0);
        $sumY = array_sum($this->valY);
        $sumQuadLnX = array_reduce($this->valX, function ($carry,$item) {
                return $carry + pow(($item > 0 ? log($item) : 0),2);
            }, 0);
        $arrKeyX = range(0, $this->cardinalityXSet - 1);
        $tempX = &$this->valX;
        $tempY = &$this->valY;
        $sumMulLnXY = array_reduce($arrKeyX, function ($carry, $index) use ($tempX, $tempY) {
                return $carry + ( ( $tempX[$index] > 0 ? log($tempX[$index]) : 0) *
                    (array_key_exists($index, $tempY) ? $tempY[$index] : 0) );
            }, 0);
        unset($tempX, $tempY, $arrKeyX);

        $detMain = $sumQuadLnX * $this->cardinalityXSet - $sumLnX * $sumLnX;
        if (!$detMain) {
            throw new ApproximationException("\nMain Determinant is equal 0.\nUse Gauss method instead", -3);
        }

        $detA = $sumMulLnXY * $this->cardinalityXSet - $sumY * $sumLnX;
        $detB = $sumQuadLnX * $sumY - $sumLnX * $sumMulLnXY;

        $this->coefficients['a'] = $detA / $detMain;
        $this->coefficients['b'] = $detB / $detMain;
    }

    /**
     * @param float $argument
     * @return float
     * @throws ApproximationException
     */
    public function calculateValue(float $argument): float
    {
        if ($argument <= 0) {
            throw new ApproximationException("\nArgument Logarithmic function must be great than 0", -4);
        }

        $result = $this->coefficients['a'] * log($argument) + $this->coefficients['b'];
        return (float)$result;
    }
}