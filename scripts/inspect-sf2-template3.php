<?php

require __DIR__.'/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

$path = __DIR__.'/../resources/templates/sf2/sf2-template.xlsx';
$sheet = IOFactory::load($path)->getSheet(0);

// Probe value cells by writing markers - read merge top-left
$probes = ['C6','I6','K6','X6','C8','X8','AC8','D11','D13','AC13'];
foreach ($probes as $addr) {
    echo "$addr: ".json_encode($sheet->getCell($addr)->getValue())."\n";
}

// Row 12 D-AB for DOW labels in template
echo "\nRow 12 D-AB:\n";
for ($i = 4; $i <= 28; $i++) {
    $col = Coordinate::stringFromColumnIndex($i);
    $v = $sheet->getCell($col.'12')->getValue();
    if ($v) echo "$col=$v ";
}

echo "\n\nFemale start row:\n";
for ($r = 34; $r <= 38; $r++) {
    echo "R$r A=".json_encode($sheet->getCell('A'.$r)->getValue())."\n";
}
