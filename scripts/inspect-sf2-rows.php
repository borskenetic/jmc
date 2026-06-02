<?php

require __DIR__.'/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$sheet = IOFactory::load(__DIR__.'/../resources/templates/sf2/sf2-template.xlsx')->getSheet(0);

echo "Merges affecting rows 10-16:\n";
foreach ($sheet->getMergeCells() as $range) {
    if (preg_match('/1[0-6]/', $range)) {
        echo "$range\n";
    }
}

foreach ([13, 14, 15] as $r) {
    echo "R$r A=".json_encode($sheet->getCell('A'.$r)->getValue())." B=".json_encode($sheet->getCell('B'.$r)->getValue())."\n";
}

echo "\nRow 6 merges containing school year:\n";
foreach ($sheet->getMergeCells() as $range) {
    if (str_contains($range, '6')) {
        echo "$range\n";
    }
}

// Check border on D14
$style = $sheet->getStyle('D14');
echo "\nD14 border diagonal: ".$style->getBorders()->getDiagonal()->getBorderStyle()."\n";
