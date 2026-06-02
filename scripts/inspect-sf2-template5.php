<?php

require __DIR__.'/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$sheet = IOFactory::load(__DIR__.'/../resources/templates/sf2/sf2-template.xlsx')->getSheet(0);

for ($r = 66; $r <= 84; $r++) {
    $ab = trim((string) $sheet->getCell('AB'.$r)->getCalculatedValue());
    $ah = $sheet->getCell('AH'.$r)->getCalculatedValue();
    $ai = $sheet->getCell('AI'.$r)->getCalculatedValue();
    $aj = $sheet->getCell('AJ'.$r)->getCalculatedValue();
    if ($ab || $ah || $ai || $aj) {
        echo "R$r AB=".substr(str_replace("\n", ' ', $ab), 0, 50)." | AH=$ah AI=$ai AJ=$aj\n";
    }
}

// Teacher name area near row 87-90
for ($r = 86; $r <= 90; $r++) {
    foreach (['AB','AC','AD','AE','AF','AG','W','X','Y'] as $col) {
        $v = $sheet->getCell($col.$r)->getValue();
        if ($v) echo "R$r $col=$v\n";
    }
}
