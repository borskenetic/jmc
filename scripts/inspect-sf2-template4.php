<?php

require __DIR__.'/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

$sheet = IOFactory::load(__DIR__.'/../resources/templates/sf2/sf2-template.xlsx')->getSheet(0);

for ($r = 84; $r <= 93; $r++) {
    $parts = [];
    foreach (['A','B','W','AB','AE','AH'] as $col) {
        $v = $sheet->getCell($col.$r)->getCalculatedValue();
        if ($v) $parts[] = "$col$r=$v";
    }
    if ($parts) echo "R$r: ".implode(' | ', $parts)."\n";
}

// Summary M F TOTAL columns
echo "\nSummary headers row 64-67 cols AH-AJ:\n";
for ($r = 64; $r <= 67; $r++) {
    echo "R$r: AH=".$sheet->getCell('AH'.$r)->getValue()." AI=".$sheet->getCell('AI'.$r)->getValue()." AJ=".$sheet->getCell('AJ'.$r)->getValue()."\n";
}
