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
                abs($expectedWeight - $weight) < 0.0001,
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
            'CaFeBiBa' => 40.078022556685 + 55.84514413384 + 208.9804 + 137.3266716329,
            'Na2CO3' => 2 * 22.98976928 + 12.010735896734 + 3 * 15.999404924748,
            'C6H12O6' => 6 * 12.010735896734 + 12 * 1.0079407540258 + 6 * 15.999404924748,
            'NH4Cl' => 14.006703211018 + 4 * 1.0079407540258 + 35.453,
            'Ca(OH)2' => 40.078022556685 + 2 * (15.999404924748 + 1.0079407540258),
            'Mg2(C2H4(OH)2)3SO4' => 2 * 24.30505162813 + 3 * (2 * 12.010735896734 + 4 * 1.0079407540258 + 2 * (15.999404924748 + 1.0079407540258)) + 32.064787405608 + 4 * 15.999404924748,
            'C11H19N3O6S' => 11 * 12.010735896734 + 19 * 1.0079407540258 + 3 * 14.006703211018 + 6 * 15.999404924748 + 32.064787405608,
            'H15-N3-C12-O4-S2-Cl' => 15 * 1.0079407540258 + 3 * 14.006703211018 + 12 * 12.010735896734 + 4 * 15.999404924748 + 2 * 32.064787405608 + 35.453,
            'K[AlSi3O8]' => 39.0983 + 26.9815386 + 3 * 28.0855 + 8 * 15.999404924748,
            'Ca2(Fe2+)4Al[(Si7Al)O22(OH)2]' => 2 * 40.078022556685 + 4 * 55.84514413384 + 26.9815386 + 7 * 28.0855 + 26.9815386 + 22 * 15.999404924748 + 2 * (15.999404924748 + 1.0079407540258),
            'CaFe7Mg2B3Si6O31H3' => 40.078022556685 + 7 * 55.84514413384 + 2 * 24.30505162813 + 3 * 10.811 + 31 * 15.999404924748 + 6 * 28.0855 + 3 * 1.0079407540258,
            'Ca(Fe)2Fe(Fe)4Mg2(BO3)3Si6O18(OH)3O' => 40.078022556685 + 7 * 55.84514413384 + 2 * 24.30505162813 + 3 * 10.811 + 31 * 15.999404924748 + 6 * 28.0855 + 3 * 1.0079407540258,
            'Ca[(Fe)2Fe][(Fe)4Mg2](BO3)3Si6O18(OH)3O' => 40.078022556685 + 7 * 55.84514413384 + 2 * 24.30505162813 + 3 * 10.811 + 31 * 15.999404924748 + 6 * 28.0855 + 3 * 1.0079407540258,
            'Ca[(Fe3+)2Fe2+][(Fe3+)4Mg2](BO3)3Si6O18(OH)3O' => 40.078022556685 + 7 * 55.84514413384 + 2 * 24.30505162813 + 3 * 10.811 + 31 * 15.999404924748 + 6 * 28.0855 + 3 * 1.0079407540258,
            'Na2Fe2+(CaNa2)(Fe2+)13Al[(PO4)11(PO3OH)(OH)2]' => 2 * 22.98976928 + 55.84514413384 + 40.078022556685 + 2 * 22.98976928 + 13 * 55.84514413384 + 26.9815386 + 11 * (30.973761998 + 4 * 15.999404924748) + 30.973761998 + 3 * 15.999404924748 + 15.999404924748 + 1.0079407540258 + 2 * (15.999404924748 + 1.0079407540258),
            'Mg6Cr2CO3(OH)16 · 4H2O' => 6 * 24.30505162813 + 2 * 51.9961 + 12.010735896734 + 3 * 15.999404924748 + 16 * (15.999404924748 + 1.0079407540258) + 4 * (2 * 1.0079407540258 + 15.999404924748),
            '(NH4)2(UO2)2(PO4)2 · 6H2O' => 2 * (14.006703211018 + 4 * 1.0079407540258) + 2 * (238.02914609015 + 2 * 15.999404924748) + 2 * (30.973761998 + 4 * 15.999404924748) + 6 * (2 * 1.0079407540258 + 15.999404924748),
            'Pb4,5Sb4,5S11' => 4.5 * 207.216908331 + 4.5 * 121.75978116 + 11 * 32.064787405608,
            'Na(Li1,5Al1,5)Al6(BO3)3Si6O18(OH)3F' => 22.98976928 + 1.5 * 6.9400366060273 + 1.5 * 26.9815386 + 6 * 26.9815386 + 3 * (10.811 + 3 * 15.999404924748) + 6 * 28.0855 + 18 * 15.999404924748 + 3 * (15.999404924748 + 1.0079407540258) + 18.9984032,
            'Na12(K,Sr,Ce)3Ca6Mn3Zr3NbSi25O73(O,H2O,OH)5(OH,F,Cl)2' => 4116.3502809157,
            'Mn2+(Ti,Nb)5O12 · 9H2O' => 54.938044 + 5 * (47.8667450392 + 92.90637) + 12 * 15.999404924748 + 9 * (2 * 1.0079407540258 + 15.999404924748),
            'Ca(Fe3+)2(PO4)2(OH,F)2' => 40.078022556685 + 2 * 55.84514413384 + 2 * (30.973761998 + 4 * 15.999404924748) + 2 * (15.999404924748 + 1.0079407540258 + 18.9984032),
            '(Ba,Na)2(Na,Ti,Mn)4(Ti,Nb)2Si4O14(OH,O,F)5 · 3H2O' => 2 * (137.3266716329 + 22.98976928) + 4 * (22.98976928 + 47.8667450392 + 54.938044) + 2 * (47.8667450392 + 92.90637) + 4 * 28.0855 + 14 * 15.999404924748 + 5 * (15.999404924748 + 1.0079407540258 + 15.999404924748 + 18.9984032) + 3 * (2 * 1.0079407540258 + 15.999404924748),
            'C15-H13-Cl-N-Na-O3.2H2O' => 15 * 12.010735896734 + 13 * 1.0079407540258 + 35.453 + 14.006703211018 + 22.98976928 + 3 * 15.999404924748 + 2 * (2 * 1.0079407540258 + 15.999404924748),
            'C15-H13-Cl-N-Na-O3.2H2-O' => 15 * 12.010735896734 + 13 * 1.0079407540258 + 35.453 + 14.006703211018 + 22.98976928 + 3 * 15.999404924748 + 2 * (2 * 1.0079407540258 + 15.999404924748),
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
                            'mass' => 2 * 12.010735896734,
                        ], [
                            'type' => 'group',
                            'quantity' => 1,
                            'items' => [
                                [
                                    'type' => 'leaf',
                                    'symbol' => 'O5',
                                    'mass' => 5 * 15.999404924748,
                                ]
                            ]
                        ], [
                            'type' => 'group',
                            'quantity' => 3,
                            'items' => [
                                [
                                    'type' => 'leaf',
                                    'symbol' => 'P2',
                                    'mass' => 2 * 30.973761998,
                                ]
                            ]
                        ]
                    ],
                ], [
                    'type' => 'leaf',
                    'symnol' => 'S',
                    'mass' => 32.064787405608,
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
