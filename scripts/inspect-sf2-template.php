<?php

require __DIR__.'/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$path = getenv('USERPROFILE').DIRECTORY_SEPARATOR.'Downloads'.DIRECTORY_SEPARATOR.'School Form 2 (SF2) Daily Attendance Report of Learners (1).xlsx';
if (! is_file($path)) {
    $path = __DIR__.'/../resources/templates/sf2/sf2-template.xlsx';
}
if (! is_file($path)) {
    exit("No template\n");
}

$sheet = IOFactory::load($path)->getSheet(0);

function cell($sheet, $addr) {
    $v = $sheet->getCell($addr)->getCalculatedValue();
    return $v === null ? '' : str_replace(["\n"], ' ', (string) $v);
}

echo "Header value cells (rows 5-9):\n";
for ($r = 5; $r <= 9; $r++) {
    $line = [];
    foreach (range('A', 'AK') as $col) {
        $v = cell($sheet, $col.$r);
        if ($v !== '') {
            $line[] = "$col$r=$v";
        }
    }
    if ($line) echo "R$r: ".implode(' | ', $line)."\n";
}

echo "\nRows 11-20 col A, D, AC, AD, AE:\n";
for ($r = 11; $r <= 20; $r++) {
    echo "R$r: A=".cell($sheet,"A$r")." | D=".cell($sheet,"D$r")." | AC=".cell($sheet,"AC$r")." | AD=".cell($sheet,"AD$r")."\n";
}

echo "\nSearch MALE/FEMALE labels:\n";
for ($r = 1; $r <= 93; $r++) {
    $a = cell($sheet, 'A'.$r);
    if (stripos($a, 'MALE') !== false || stripos($a, 'FEMALE') !== false || stripos($a, 'Combined') !== false) {
        echo "R$r A=$a\n";
    }
}

echo "\nDay header row 11 (D-AH):\n";
for ($col = 'D'; $col <= 'AB'; $col++) {
    $v = cell($sheet, $col.'11');
    if ($v !== '') echo "$col11=$v ";
}
