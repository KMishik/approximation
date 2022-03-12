<?php declare(strict_types=1);

namespace Idiacant\Approximation\Approximators;


use Idiacant\Approximation\Exceptions\ApproximationException;

final class QuadraticApproximator extends AbstractApproximator
{
    public function __construct()
    {
        $this->coefficients = ['a' => 0.0, 'b' => 0.0, 'c' => 0.0];
    }

    /**
     * @throws ApproximationException
     */
    public function calculateCoeff()
    {
        $sumX = array_sum($this->valX);
        $sumY = array_sum($this->valY);
        $sumQuadX = array_reduce($this->valX, function ($carry,$item) {
            return $carry + pow($item,2);
        }, 0);
        $sumCubX = array_reduce($this->valX, function ($carry,$item) {
            return $carry + pow($item,3);
        }, 0);
        $sumFourthX = array_reduce($this->valX, function ($carry,$item) {
            return $carry + pow($item,4);
        }, 0);
        $arrKeyX = range(0, $this->cardinalityXSet - 1);
        $tempX = &$this->valX;
        $tempY = &$this->valY;
        $sumMulXY = array_reduce($arrKeyX, function ($carry, $index) use ($tempX, $tempY) {
            return $carry + ( $tempX[$index] * (array_key_exists($index, $tempY) ? $tempY[$index] : 0) );
        }, 0);
        $sumMulQuadXY = array_reduce($arrKeyX, function ($carry, $index) use ($tempX, $tempY) {
            return $carry + ( pow($tempX[$index],2) * (array_key_exists($index, $tempY) ? $tempY[$index] : 0) );
        }, 0);
        unset($tempX, $tempY, $arrKeyX);

        $detMain = ($sumFourthX * $sumQuadX * $this->cardinalityXSet) + ($sumQuadX * $sumCubX * $sumX) +
            ($sumCubX * $sumX * $sumQuadX) - ($sumQuadX * $sumQuadX * $sumQuadX) - ($sumFourthX * $sumX * $sumX) -
            ($sumCubX * $sumCubX * $this->cardinalityXSet);
        if (!$detMain) {
            throw new ApproximationException("\nMain Determinant is equal 0.\nUse Gauss method instead", -3);
        }

        $detA = ($sumMulQuadXY * $sumQuadX * $this->cardinalityXSet) + ($sumCubX * $sumX * $sumY) +
            ($sumQuadX * $sumMulXY * $sumX) - ($sumY * $sumQuadX * $sumQuadX) - ($sumX * $sumX * $sumMulQuadXY) -
            ($this->cardinalityXSet * $sumMulXY * $sumCubX);
        $detB = ($sumFourthX * $sumMulXY * $this->cardinalityXSet) + ($sumMulQuadXY * $sumX * $sumQuadX) +
            ($sumQuadX * $sumCubX * $sumY) - ($sumQuadX * $sumMulXY * $sumQuadX) - ($sumY * $sumX * $sumFourthX) -
            ($this->cardinalityXSet * $sumCubX * $sumMulQuadXY);
        $detC = ($sumFourthX * $sumQuadX * $sumY) + ($sumCubX * $sumMulXY * $sumQuadX) +
            ($sumMulQuadXY * $sumCubX * $sumX) - ($sumQuadX * $sumQuadX * $sumMulQuadXY) -
            ($sumX * $sumMulXY * $sumFourthX) - ($sumY * $sumCubX * $sumCubX) ;

        $this->coefficients['a'] = $detA / $detMain;
        $this->coefficients['b'] = $detB / $detMain;
        $this->coefficients['c'] = $detC / $detMain;
    }

    /**
     * @param float $argument
     * @return float
     */
    public function calculateValue(float $argument): float
    {
        $result =
            $this->coefficients['a'] * pow($argument, 2)
            + $this->coefficients['b'] * $argument + $this->coefficients['c'];
        return $result;
    }
}