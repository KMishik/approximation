<?php declare(strict_types=1);

namespace Idiacant\Approximation;

use Idiacant\Approximation\Approximators\AbstractApproximator;
use Idiacant\Approximation\Approximators\ExpApproximator;
use Idiacant\Approximation\Approximators\LinearApproximator;
use Idiacant\Approximation\Approximators\LogApproximator;
use Idiacant\Approximation\Approximators\QuadraticApproximator;
use Idiacant\Approximation\Exceptions\ApproximationException;

class ApproximatorManager
{
    const LINEAR = 1;
    const QUADR = 2;
    const EXPONENT = 3;
    const LOGARIFM = 4;

    /**
     * @var int
     */
    private int $mode;

    /**
     * @param int $mode
     */
    public function __construct(int $mode)
    {
        $this->mode = $mode;
    }

    /**
     * @return AbstractApproximator
     * @throws ApproximationException
     */
    public function getApproximator() : AbstractApproximator
    {
        switch ($this->mode) {
            case (self::LINEAR):
                return new LinearApproximator();
            case (self::QUADR):
                return new QuadraticApproximator();
            case (self::EXPONENT):
                return new ExpApproximator();
            case (self::LOGARIFM):
                return new LogApproximator();
            default:
                throw new ApproximationException("Undefined approximation function's type", -5);
        }
    }
}