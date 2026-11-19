<?php
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$path = 'public/documents/CEFET - Ficha Individual de Frequência.xlsx';
$spreadsheet = IOFactory::load($path);
$sheetNames = $spreadsheet->getSheetNames();

echo "=== SHEETS (" . count($sheetNames) . ") ===" . PHP_EOL;
foreach ($sheetNames as $i => $name) {
    echo "  [$i] $name" . PHP_EOL;
}

foreach ($sheetNames as $sheetName) {
    $sheet = $spreadsheet->getSheetByName($sheetName);
    echo PHP_EOL . "=============================" . PHP_EOL;
    echo "SHEET: '$sheetName'" . PHP_EOL;
    echo "Highest row: " . $sheet->getHighestRow() . ", Highest col: " . $sheet->getHighestColumn() . PHP_EOL;
    echo "=============================" . PHP_EOL;

    // Print all non-empty cells
    $maxRow = $sheet->getHighestRow();
    $maxCol = $sheet->getHighestColumn();

    for ($row = 1; $row <= $maxRow; $row++) {
        for ($col = 'A'; $col <= $maxCol; $col++) {
            $cell = $sheet->getCell($col . $row);
            $val = $cell->getValue();
            if ($val !== null && $val !== '') {
                $display = is_string($val) ? substr($val, 0, 120) : print_r($val, true);
                echo $col . $row . ": " . $display . PHP_EOL;
            }
            if ($col == $maxCol) break;
            $col++;
            $col--;
        }
    }

    // Also show merged cells
    echo PHP_EOL . "--- Merged Cells ---" . PHP_EOL;
    foreach ($sheet->getMergeCells() as $mergeCell) {
        echo $mergeCell . PHP_EOL;
    }
}
