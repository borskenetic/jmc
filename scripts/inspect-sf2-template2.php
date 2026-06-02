<?php

require __DIR__.'/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

$path = getenv('USERPROFILE').DIRECTORY_SEPARATOR.'Downloads'.DIRECTORY_SEPARATOR.'School Form 2 (SF2) Daily Attendance Report of Learners (1).xlsx';
$sheet = IOFactory::load($path)->getSheet(0);

foreach ($sheet->getMergeCells() as $range) {
    if (preg_match('/^[A-Z]+[6-9]/', $range) || preg_match('/^A1/', $range)) {
        echo "Merge: $range = ".($sheet->getCell(explode(':', $range)[0])->getCalculatedValue() ?? '')."\n";
    }
}

echo "\nMerged in header area 5-9:\n";
foreach ($sheet->getMergeCells() as $range) {
    [$start] = explode(':', $range);
    if (preg_match('/(\d+)/', $start, $m) && (int)$m[1] >= 5 && (int)$m[1] <= 9) {
        echo "$range\n";
    }
}

echo "\nRow 6 all non-empty:\n";
for ($c = 1; $c <= 40; $c++) {
    $col = Coordinate::stringFromColumnIndex($c);
    $v = $sheet->getCell($col.'6')->getCalculatedValue();
    if ($v) echo "$col=$v ";
}

echo "\n\nLearner rows 13-17 (A, B, C):\n";
for ($r = 13; $r <= 17; $r++) {
    echo "R$r: A=".json_encode($sheet->getCell("A$r")->getCalculatedValue())." B=".json_encode($sheet->getCell("B$r")->getCalculatedValue())."\n";
}

echo "\nPage 2 area row 63+ sample:\n";
for ($r = 63; $r <= 75; $r++) {
    $a = $sheet->getCell("A$r")->getCalculatedValue();
    $w = $sheet->getCell("W$r")->getCalculatedValue();
    if ($a || $w) echo "R$r: A=$a | W=$w\n";
}

// Count day columns D to ?
$lastDayCol = 'D';
for ($c = 4; $c <= 30; $c++) {
    $col = Coordinate::stringFromColumnIndex($c);
    $h10 = $sheet->getCell($col.'10')->getCalculatedValue();
    $h11 = $sheet->getCell($col.'11')->getCalculatedValue();
    if ($c <= 10 || $h10 || $h11) {
        echo "Col $col row10=".json_encode($h10)." row11=".json_encode($h11)."\n";
    }
}

echo "\nAC col index: ".Coordinate::columnIndexFromString('AC')."\n";
echo "D col index: ".Coordinate::columnIndexFromString('D')."\n";
echo "AB col index: ".Coordinate::columnIndexFromString('AB')."\n";
