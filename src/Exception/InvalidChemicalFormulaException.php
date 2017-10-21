<?php

namespace FiiSoft\MolecularWeightCalculator\Exception;

final class InvalidChemicalFormulaException extends \InvalidArgumentException
{
    /**
     * @param string $chemicalFormula
     * @return InvalidChemicalFormulaException
     */
    public static function invalidFormula($chemicalFormula)
    {
        return new self('Chemical formula seems to be invalid: '.$chemicalFormula);
    }
}