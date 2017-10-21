<?php

namespace FiiSoft\Test\MolecularWeightCalculator;

use FiiSoft\MolecularWeightCalculator\MolecularWeightCalculator;

class MolecularWeightCalculatorTest extends \PHPUnit_Framework_TestCase
{
    /** @var MolecularWeightCalculator */
    private $calc;
    
    protected function setUp()
    {
        parent::setUp();
        
        $this->calc = new MolecularWeightCalculator();
    }
    
    public function test_get_data()
    {
        self::assertInternalType('array', $this->calc->getData());
    }
    
    public function test_it_can_compute_molecural_weights_from_various_chemical_formulas()
    {
        foreach ($this->getTestData() as $chemicalFormula => $expectedWeight) {
            $weight = $this->calc->computeWeight($chemicalFormula);
            self::assertTrue(
                abs($expectedWeight - $weight) < 0.001,
                'test: '.$chemicalFormula.', expected weight: '.$expectedWeight.', computed weight: '.$weight
            );
        }
    }
    
    /**
     * @return array
     */
    private function getTestData()
    {
        return [
            'Na2CO3' => 2 * 22.98976928 + 12.0107 + 3 * 15.9994,
            'C6H12O6' => 6 * 12.0107 + 12 * 1.00794 + 6 * 15.9994,
            'NH4Cl' => 14.0067 + 4 * 1.00794 + 35.453,
            'Ca(OH)2' => 40.078 + 2 * (15.9994 + 1.00794),
            'Mg2(C2H4(OH)2)3SO4' => 2 * 24.305 + 3 * (2 * 12.0107 + 4 * 1.00794 + 2 * (15.9994 + 1.00794)) + 32.065 + 4 * 15.9994,
            'C11H19N3O6S' => 11 * 12.0107 + 19 * 1.00794 + 3 * 14.0067 + 6 * 15.9994 + 32.065,
            'H15-N3-C12-O4-S2-Cl' => 15 * 1.00794 + 3 * 14.0067 + 12 * 12.0107 + 4 * 15.9994 + 2 * 32.065 + 35.453,
            'K[AlSi3O8]' => 39.0983 + 26.9815386 + 3 * 28.0855 + 8 * 15.9994,
            'Ca2(Fe2+)4Al[(Si7Al)O22(OH)2]' => 2 * 40.078 + 4 * 55.845 + 26.9815386 + 7 * 28.0855 + 26.9815386 + 22 * 15.9994 + 2 * (15.9994 + 1.00794),
            'CaFe7Mg2B3Si6O31H3' => 40.078 + 7 * 55.845 + 2 * 24.305 + 3 * 10.811 + 31 * 15.9994 + 6 * 28.0855 + 3 * 1.00794,
            'Ca(Fe)2Fe(Fe)4Mg2(BO3)3Si6O18(OH)3O' => 40.078 + 7 * 55.845 + 2 * 24.305 + 3 * 10.811 + 31 * 15.9994 + 6 * 28.0855 + 3 * 1.00794,
            'Ca[(Fe)2Fe][(Fe)4Mg2](BO3)3Si6O18(OH)3O' => 40.078 + 7 * 55.845 + 2 * 24.305 + 3 * 10.811 + 31 * 15.9994 + 6 * 28.0855 + 3 * 1.00794,
            'Ca[(Fe3+)2Fe2+][(Fe3+)4Mg2](BO3)3Si6O18(OH)3O' => 40.078 + 7 * 55.845 + 2 * 24.305 + 3 * 10.811 + 31 * 15.9994 + 6 * 28.0855 + 3 * 1.00794,
            'Na2Fe2+(CaNa2)(Fe2+)13Al[(PO4)11(PO3OH)(OH)2]' => 2 * 22.98976928 + 55.845 + 40.078 + 2 * 22.98976928 + 13 * 55.845 + 26.9815386 + 11 * (30.973762 + 4 * 15.9994) + 30.973762 + 3 * 15.9994 + 15.9994 + 1.00794 + 2 * (15.9994 + 1.00794),
            'Mg6Cr2CO3(OH)16 · 4H2O' => 6 * 24.305 + 2 * 51.9961 + 12.0107 + 3 * 15.9994 + 16 * (15.9994 + 1.00794) + 4 * (2 * 1.00794 + 15.9994),
            '(NH4)2(UO2)2(PO4)2 · 6H2O' => 2 * (14.0067 + 4 * 1.00794) + 2 * (238.02891 + 2 * 15.9994) + 2 * (30.973762 + 4 * 15.9994) + 6 * (2 * 1.00794 + 15.9994),
            'Pb4,5Sb4,5S11' => 4.5 * 207.2 + 4.5 * 121.760 + 11 * 32.065,
            'Na(Li1,5Al1,5)Al6(BO3)3Si6O18(OH)3F' => 22.98976928 + 1.5 * 6.941 + 1.5 * 26.9815386 + 6 * 26.9815386 + 3 * (10.811 + 3 * 15.9994) + 6 * 28.0855 + 18 * 15.9994 + 3 * (15.9994 + 1.00794) + 18.9984032,
            'Na12(K,Sr,Ce)3Ca6Mn3Zr3NbSi25O73(O,H2O,OH)5(OH,F,Cl)2' => 4116.36155276,
            'Mn2+(Ti,Nb)5O12 · 9H2O' => 54.938045 + 5 * (47.867 + 92.90638) + 12 * 15.9994 + 9 * (2 * 1.00794 + 15.9994),
            'Ca(Fe3+)2(PO4)2(OH,F)2' => 40.078 + 2 * 55.845 + 2 * (30.973762 + 4 * 15.9994) + 2 * (15.9994 + 1.00794 + 18.9984032),
            '(Ba,Na)2(Na,Ti,Mn)4(Ti,Nb)2Si4O14(OH,O,F)5 · 3H2O' => 2 * (137.327 + 22.98976928) + 4 * (22.98976928 + 47.867 + 54.938045) + 2 * (47.867 + 92.90638) + 4 * 28.0855 + 14 * 15.9994 + 5 * (15.9994 + 1.00794 + 15.9994 + 18.9984032) + 3 * (2 * 1.00794 + 15.9994),
        ];
    }
    
    /**
     * @dataProvider getDataForTestItCanDetectInvalidChemicalFormula
     * @expectedException \FiiSoft\MolecularWeightCalculator\Exception\InvalidChemicalFormulaException
     */
    public function test_it_can_detect_invalid_chemical_formula($chemicalFormula)
    {
        $this->calc->computeWeight($chemicalFormula);
    }
    
    public function getDataForTestItCanDetectInvalidChemicalFormula()
    {
        return [
            ['MnOp3'],
            ['COZ'],
            ['Ch3(CO2)('],
            ['()Na'],
            ['H2O5(S)Na)'],
        ];
    }
    
    public function test_compute_weight_from_data_structure()
    {
        $structure = [
            'type' => 'group',
            'quantity' => 1,
            'items' => [
                [
                    'type' => 'leaf',
                    'symbol' => 'K',
                    'mass' => 39.0983,
                ], [
                    'type' => 'group',
                    'quantity' => 2,
                    'items' => [
                        [
                            'type' => 'leaf',
                            'symbol' => 'C2',
                            'mass' => 2 * 12.0107,
                        ], [
                            'type' => 'group',
                            'quantity' => 1,
                            'items' => [
                                [
                                    'type' => 'leaf',
                                    'symbol' => 'O5',
                                    'mass' => 5 * 15.9994,
                                ]
                            ]
                        ], [
                            'type' => 'group',
                            'quantity' => 3,
                            'items' => [
                                [
                                    'type' => 'leaf',
                                    'symbol' => 'P2',
                                    'mass' => 2 * 30.973762,
                                ]
                            ]
                        ]
                    ],
                ], [
                    'type' => 'leaf',
                    'symnol' => 'S',
                    'mass' => 32.065,
                ]
            ],
        ];
        
        $compute = null;
        $compute = function (array $node) use (&$compute) {
            if ($node['type'] === 'group') {
                return $node['quantity'] * array_sum(array_map($compute, $node['items']));
            }
            return $node['mass'];
        };
    
        $mass = $compute($structure);
        
        self::assertTrue(abs(650.885244 - $mass) < 0.001);
    }
}
