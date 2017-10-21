<?php

namespace FiiSoft\MolecularWeightCalculator\Exception;

final class UnableToComputeMolecularWeightException extends \RuntimeException
{
    /**
     * @param string $chemicalFormula
     * @return UnableToComputeMolecularWeightException
     */
    public static function fromFormula($chemicalFormula)
    {
        return new self('I do not know how to calculate weight from formula '.$chemicalFormula);
    }
}