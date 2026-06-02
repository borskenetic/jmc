<?php

require __DIR__.'/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

$template = getenv('USERPROFILE').'/Downloads/School Form 2 (SF2) Daily Attendance Report of Learners (1).xlsx';
$filled = getenv('USERPROFILE').'/Downloads/Filled up SF2.xlsx';

foreach (['template' => $template, 'filled' => $filled] as $label => $path) {
    if (! is_file($path)) {
        echo "$label: not found ($path)\n";
        continue;
    }
    echo "=== $label ===\n";
    $s = IOFactory::load($path)->getSheet(0);
    foreach ([6, 7, 8] as $r) {
        $parts = [];
        for ($c = 1; $c <= 34; $c++) {
            $col = Coordinate::stringFromColumnIndex($c);
            $v = $s->getCell($col.$r)->getCalculatedValue();
            if ($v !== null && $v !== '') {
                $parts[] = $col.$r.'='.$v;
            }
        }
        echo 'R'.$r.': '.implode(' | ', $parts)."\n";
    }
    echo "R13 A-C: ".$s->getCell('A13')->getCalculatedValue()." | D13=".$s->getCell('D13')->getCalculatedValue()."\n";
    echo "R35 A: ".$s->getCell('A35')->getCalculatedValue()."\n";
}
