<?php

namespace FiiSoft\MolecularWeightCalculator;

use FiiSoft\MolecularWeightCalculator\Exception\CorruptedDataException;
use FiiSoft\MolecularWeightCalculator\Exception\InvalidChemicalFormulaException;
use FiiSoft\MolecularWeightCalculator\Exception\UnableToComputeMolecularWeightException;
use InvalidArgumentException;

final class MolecularWeightCalculator
{
    const SIMPLE_FORMULA = '/^[\d\p{L},]+$/u';
    const FORMULA_WITH_GROUPS = '/^[,\d\p{L}\(\)\[\]]+$/u';
    const STARTS_WITH_DIGITS = '/^(?P<number>\d+)/u';
    const FORMULA_WITH_IONS = '/\d+(\+|\-)/u';
    const COMPOUND_FORMULA = '/\s+·\s+/u';
    
    /** @var string */
    private $symbolAndQuantityPattern;
    
    /** @var array */
    private $data = [];
    
    /** @var array */
    private $masses = [];
    
    /**
     * @throws CorruptedDataException when read data failed
     */
    public function __construct()
    {
        $this->init();
    }
    
    /**
     * @throws CorruptedDataException
     * @return void
     */
    private function init()
    {
        $dataFile = __DIR__ . DIRECTORY_SEPARATOR . 'atoms_weights_data.php';
        
        $data = include $dataFile;
        $numOfItems = 4 * 112; //112 elements in data
        
        if (!is_array($data) || count($data) !== $numOfItems) {
            $this->throwExceptionCorruptedData($dataFile);
        }
    
        $prevAtomicNumber = 1;
        
        for ($i = 0; $i < $numOfItems; $i += 4) {
            if (!isset($data[$i], $data[$i+1], $data[$i+2], $data[$i+3])) {
                $this->throwExceptionCorruptedData($dataFile);
            }
            
            $atomicNumber = (int) $data[$i];
            $name = trim($data[$i+1]);
            $symbol = trim($data[$i+2]);
            $atomicMass = (float) $data[$i+3];
    
            if ($atomicNumber !== $prevAtomicNumber++ || $name === '' || $symbol === '' || $atomicMass < 1.0
                || isset($this->data[$symbol])
            ) {
                $this->throwExceptionCorruptedData($dataFile);
            }
            
            $this->data[$symbol] = [
                'number' => $atomicNumber,
                'name' => $name,
                'symbol' => $symbol,
                'mass' => $atomicMass,
            ];
            
            $this->masses[$symbol] = $atomicMass;
        }
    
        $symbols = array_keys($this->masses);
        rsort($symbols);
    
        $this->symbolAndQuantityPattern = str_replace(
            '{symbols}', implode('|', $symbols), '/(?P<symbol>{symbols})(?P<quantity>[\d,]+)?/u'
        );
    }
    
    /**
     * @param string $chemicalFormula
     * @throws UnableToComputeMolecularWeightException
     * @throws InvalidChemicalFormulaException
     * @throws InvalidArgumentException
     * @return float
     */
    public function computeWeight($chemicalFormula)
    {
        $chemicalFormula = is_string($chemicalFormula) ? trim($chemicalFormula) : '';
        if ($chemicalFormula === '') {
            throw new InvalidArgumentException('Invalid param chemicalFormula');
        }
        
        $weight = $this->computeFromSimpleFormula($chemicalFormula)
            ?: $this->computeFromFormulaWithGroups($chemicalFormula)
            ?: $this->computeFromFormulaWithDashes($chemicalFormula)
            ?: $this->computeFromFormulaWithIons($chemicalFormula)
            ?: $this->computeFromCompoundFormula($chemicalFormula);
        
        if ($weight !== false) {
            return $weight;
        }
        
        $this->throwExceptionUnableToComputeWeight($chemicalFormula);
    }
    
    /**
     * @param string $chemicalFormula
     * @throws UnableToComputeMolecularWeightException
     * @throws InvalidChemicalFormulaException
     * @throws InvalidArgumentException
     * @return float|false
     */
    private function computeFromCompoundFormula($chemicalFormula)
    {
        if (preg_match(self::COMPOUND_FORMULA, $chemicalFormula)) {
            $formulas = preg_split(self::COMPOUND_FORMULA, $chemicalFormula, -1, PREG_SPLIT_NO_EMPTY);
            if (is_array($formulas)) {
                return array_sum(array_map(function ($formula) use ($chemicalFormula) {
                    if (preg_match(self::STARTS_WITH_DIGITS, $formula, $matches) && isset($matches['number'])) {
                        $number = $this->asFloat($matches['number']);
                        $formula = preg_replace(self::STARTS_WITH_DIGITS, '', $formula);
                    } else {
                        $number = 1.0;
                    }
                    
                    $weight = $this->computeWeight($formula);
                    if ($weight !== false) {
                        return $number === 1.0 ? $weight : $weight * $number;
                    }
                    
                    $this->throwExceptionUnableToComputeWeight($chemicalFormula);
                }, $formulas));
            }
        }
        
        return false;
    }
    
    /**
     * @param string $chemicalFormula
     * @throws UnableToComputeMolecularWeightException
     * @throws InvalidChemicalFormulaException
     * @throws InvalidArgumentException
     * @return float|false
     */
    private function computeFromFormulaWithIons($chemicalFormula)
    {
        return preg_match(self::FORMULA_WITH_IONS, $chemicalFormula)
            ? $this->computeWeight(preg_replace(self::FORMULA_WITH_IONS, '', $chemicalFormula))
            : false;
    }
    
    /**
     * @param string $chemicalFormula
     * @throws UnableToComputeMolecularWeightException
     * @throws InvalidChemicalFormulaException
     * @throws InvalidArgumentException
     * @return float|false
     */
    private function computeFromFormulaWithDashes($chemicalFormula)
    {
        return false !== strpos($chemicalFormula, '-')
            ? $this->computeWeight(str_replace('-', '', $chemicalFormula))
            : false;
    }
    
    /**
     * @param string $chemicalFormula
     * @throws InvalidChemicalFormulaException
     * @return float|false
     */
    private function computeFromFormulaWithGroups($chemicalFormula)
    {
        if (!preg_match(self::FORMULA_WITH_GROUPS, $chemicalFormula)) {
            return false;
        }
    
        $formula = str_replace(['[', ']'], ['(', ')'], $chemicalFormula);
        
        $pieces = preg_split('/(\(|\))/u', $formula, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        if (!is_array($pieces)) {
            return false;
        }
    
        $numOfGroups = substr_count($formula, '(');
        if ($numOfGroups !== substr_count($formula, ')')) {
            $this->throwExceptionInvalidFormula($chemicalFormula);
        }
        
        $groupPrototype = [
            'type' => 'group',
            'quantity' => 1.0,
            'items' => [],
        ];
        
        $level = 0;
        $levels = [];
        $structure = $groupPrototype;
        $levels[$level] = &$structure;
        
        for ($i = 0, $max = count($pieces); $i < $max; ++$i) {
            $item = $pieces[$i];
            if ($item === '(') {
                unset($group);
                $group = $groupPrototype;
                $levels[$level]['items'][] = &$group;
                $levels[++$level] = &$group;
            } elseif ($item === ')') {
                if ($level > 0) {
                    $number = 1;
                    if ($i + 1 < $max) {
                        $nextItem = $pieces[$i + 1];
                        if (ctype_digit($nextItem)) {
                            $number = $this->asFloat($nextItem);
                            ++$i;
                        } else {
                            $matches = [];
                            if (preg_match(self::STARTS_WITH_DIGITS, $nextItem, $matches) && isset($matches['number'])) {
                                $number = $this->asFloat($matches['number']);
                                $pieces[$i + 1] = preg_replace(self::STARTS_WITH_DIGITS, '', $nextItem);
                            }
                        }
                    }
                    $levels[$level--]['quantity'] = $number;
                } else {
                    $this->throwExceptionInvalidFormula($chemicalFormula);
                }
            } else {
                $mass = $this->computeFromSimpleFormula($item);
                if ($mass !== false) {
                    $levels[$level]['items'][] = [
                        'type' => 'leaf',
                        'item' => $item,
                        'mass' => $mass,
                    ];
                } else {
                    $this->throwExceptionInvalidFormula($chemicalFormula);
                }
            }
        }
    
        $compute = null;
        $compute = function (array $node) use (&$compute, $chemicalFormula) {
            if ($node['type'] === 'group') {
                if (empty($node['items'])) {
                    $this->throwExceptionInvalidFormula($chemicalFormula);
                }
                return $node['quantity'] * array_sum(array_map($compute, $node['items']));
            }
            return $node['mass'];
        };

        return $compute($structure);
    }
    
    /**
     * @param string $chemicalFormula
     * @throws InvalidChemicalFormulaException
     * @return float|false
     */
    private function computeFromSimpleFormula($chemicalFormula)
    {
        if (!preg_match(self::SIMPLE_FORMULA, $chemicalFormula)) {
            return false;
        }
    
        $formula = preg_replace('/(?<=\p{L}),(?=\p{L})/u', '', $chemicalFormula);
        if (!preg_match_all($this->symbolAndQuantityPattern, $formula, $matches, PREG_SPLIT_DELIM_CAPTURE)) {
            return false;
        }
        
        $totalMass = 0.0;
    
        foreach ($matches as $match) {
            if (0 === strpos($formula, $match['symbol'])) {
                $formula = substr($formula, strlen($match['symbol']));
            } else {
                $this->throwExceptionInvalidFormula($chemicalFormula);
            }
    
            if (isset($match['quantity'])) {
                if (0 === strpos($formula, $match['quantity'])) {
                    $formula = substr($formula, strlen($match['quantity']));
                } else {
                    $this->throwExceptionInvalidFormula($chemicalFormula);
                }
                
                $quantity = $this->asFloat($match['quantity']);
            } else {
                $quantity = 1.0;
            }
            
            $totalMass += $quantity * $this->masses[$match['symbol']];
        }
    
        if ($formula !== '') {
            $this->throwExceptionInvalidFormula($chemicalFormula);
        }
        
        return $totalMass;
    }
    
    /**
     * @param string $value
     * @return float
     */
    private function asFloat($value)
    {
        return (float) str_replace(',', '.', $value);
    }
    
    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
    
    /**
     * @param string $chemicalFormula
     * @throws InvalidChemicalFormulaException
     * @return void
     */
    private function throwExceptionInvalidFormula($chemicalFormula)
    {
        throw InvalidChemicalFormulaException::invalidFormula($chemicalFormula);
    }
    
    /**
     * @param string $chemicalFormula
     * @throws UnableToComputeMolecularWeightException
     * @return void
     */
    private function throwExceptionUnableToComputeWeight($chemicalFormula)
    {
        throw UnableToComputeMolecularWeightException::fromFormula($chemicalFormula);
    }
    
    /**
     * @param string $dataFile
     * @throws CorruptedDataException
     * @return void
     */
    private function throwExceptionCorruptedData($dataFile)
    {
        throw CorruptedDataException::inFile($dataFile);
    }
}