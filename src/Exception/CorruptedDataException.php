<?php

namespace FiiSoft\MolecularWeightCalculator\Exception;

final class CorruptedDataException extends \UnexpectedValueException
{
    /**
     * @param string $dataFile
     * @return CorruptedDataException
     */
    public static function inFile($dataFile)
    {
        return new self('Corrupted data in file '.$dataFile);
    }
}