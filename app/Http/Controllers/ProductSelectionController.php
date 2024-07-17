<?php

namespace App\Http\Controllers;

use App\Support\Helper;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Color;

class ProductSelectionController extends Controller
{
    const EXCEL_TEMPLATE_STORAGE_PATH = 'app/excel/templates/products-selection.xlsx';
    const EXCEL_EXPORT_STORAGE_PATH = 'app/excel/exports/products-selection';

    const DEFAULT_COUNTRIES = [
        'KZ',
        'TM',
        'KG',
        'AM',
        'TJ',
        'UZ',
        'GE',
        'MN',
        'RU',
        'AZ',
        'AL',
        'KE',
        'DO',
        'KH',
        'MM',
    ];

    const FIRST_DEFAULT_COUNTRY_COLUMN_LETTER = 'J';
    const LAST_DEFAULT_COUNTRY_COLUMN_LETTER = 'X';
    const TITLES_ROW = 2;
    const RECORDS_INSERT_START_ROW = 4;

    public function export(Request $request)
    {
        $model = Helper::addFullNamespaceToModel($request->model);
        $model::mergeExportQueryingParamsToRequest($request);

        $query = $model::getRecordsFinalized($request, finaly: 'query');
        $filepath = self::generateExcelFileFromQuery($query, $model);

        return response()->download($filepath);
    }

    private static function generateExcelFileFromQuery($query, $model)
    {
        // Load Excel template
        $templatePath = storage_path(self::EXCEL_TEMPLATE_STORAGE_PATH);
        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();

        // Collect all records
        $records = collect();
        $query->chunk(400, function ($chunked) use (&$records) {
            $records = $records->merge($chunked);
        });

        // Prepare records before export
        self::prepareRecordsForExport($records, $model);

        // Get additional country names
        $additionalCountries = self::insertAdditionalCountriesIntoSheet($sheet, $records, $model);

        // insert records into sheet
        self::fillSheetWithRecords($sheet, $records, $model, $additionalCountries);

        // Save modified spreadsheet
        $filepath = self::saveSpreadsheet($spreadsheet);

        return $filepath;
    }

    private static function prepareRecordsForExport($records, $model)
    {
        switch ($model) {
            case 'App\Models\Product':
                // Append coincident_kvpps manually, so it won`t load many times
                $records->each(function ($record) {
                    $record->loaded_coincident_kvpps = $record->coincident_kvpps;
                });
                break;
        }
    }

    private static function insertAdditionalCountriesIntoSheet($sheet, $records, $model)
    {
        $additionalCountries = self::getAdditionalCountries($records, $model);

        // insert additional country titles between last default country and ZONE 4B columns
        $lastCountryColumnLetter = self::LAST_DEFAULT_COUNTRY_COLUMN_LETTER;
        $lastCountryColumnIndex = Coordinate::columnIndexFromString($lastCountryColumnLetter);

        foreach ($additionalCountries as $country) {
            // Insert new country column
            $nextColumnIndex = $lastCountryColumnIndex + 1;
            $nextColumnLetter = Coordinate::stringFromColumnIndex($nextColumnIndex); // Convert numeric index to column letter
            $sheet->insertNewColumnBefore($nextColumnLetter, 1);

            $insertedColumnIndex = $nextColumnIndex;
            $insertedColumnLetter = $nextColumnLetter;
            $insertedCellCoordinates = [$insertedColumnIndex, self::TITLES_ROW];
            $sheet->setCellValue($insertedCellCoordinates, $country);

            // Update cell styles
            $sheet->getColumnDimension($insertedColumnLetter)->setWidth(5);
            $cellStyle = $sheet->getCell($insertedCellCoordinates)->getStyle();
            $cellStyle->getFill()->getStartColor()->setARGB('00FFFF');
            $cellStyle->getFont()->setColor(new Color(Color::COLOR_BLACK));
            $lastCountryColumnIndex = $insertedColumnIndex;
        }

        return $additionalCountries;
    }

    private static function getAdditionalCountries($records, $model)
    {
        // Collect unique additional countries
        switch ($model) {
            case 'App\Models\Product':
                $uniqueCountries = $records->flatMap->loaded_coincident_kvpps->pluck('country.name')->unique();
                break;
        }

        // Remove countries which already present in default countries
        $additionalCountries = $uniqueCountries->diff(self::DEFAULT_COUNTRIES);

        return $additionalCountries;
    }

    private static function fillSheetWithRecords($sheet, $records, $model, $additionalCountries)
    {
        // Join default and additional countries
        $allCountries = collect(self::DEFAULT_COUNTRIES)->merge($additionalCountries);

        $row = self::RECORDS_INSERT_START_ROW;
        $recordsCounter = 1;

        foreach ($records as $record) {
            $columnIndex = 1;

            $sheet->setCellValue([$columnIndex++, $row], $recordsCounter);
            $sheet->setCellValue([$columnIndex++, $row], $record->inn->name);
            $sheet->setCellValue([$columnIndex++, $row], $record->form->name);
            $sheet->setCellValue([$columnIndex++, $row], $record->dosage);
            $sheet->setCellValue([$columnIndex++, $row], $record->pack);
            $sheet->setCellValue([$columnIndex++, $row], $record->moq);
            $sheet->setCellValue([$columnIndex++, $row], $record->shelfLife->name);

            $firstCountryColumnLetter = self::FIRST_DEFAULT_COUNTRY_COLUMN_LETTER;  // Reset value for each row
            $firstCountryColumnIndex = Coordinate::columnIndexFromString($firstCountryColumnLetter);
            $countryColumnIndexCounter = $firstCountryColumnIndex; // used only for looping

            foreach ($allCountries as $country) {
                $countryCellIndex = [$countryColumnIndexCounter, $row];
                $cellStyle = $sheet->getCell($countryCellIndex)->getStyle();

                if ($record->loaded_coincident_kvpps->contains('country.name', $country)) {
                    $sheet->setCellValue($countryCellIndex, '1');

                    // Update cell styles
                    $cellStyle->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $cellStyle->getFill()->getStartColor()->setARGB('92D050');
                } else {
                    // Reset background color because new inserted rows copy previous row styles
                    $cellStyle->getFill()->getStartColor()->setARGB('FFFFFF');
                }

                $countryColumnIndexCounter++;
            }

            $row++;
            $recordsCounter++;
            $sheet->insertNewRowBefore($row, 1);  // Insert new rows to escape rewriting default countries list
        }

        self::removeRedundantRow($sheet, $records, $row);
    }

    private static function removeRedundantRow($sheet, $records, $row)
    {
        // Remove last inserted redundant row
        if ($records->isNotEmpty()) {
            $sheet->removeRow($row);
        }
    }

    private static function saveSpreadsheet($spreadsheet)
    {
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = date('Y-m-d H-i-s') . '.xlsx';
        $filename = Helper::escapeDuplicateFilename($filename, storage_path(self::EXCEL_EXPORT_STORAGE_PATH));
        $filename = Helper::sanitizeFilename($filename);
        $filePath = storage_path(self::EXCEL_EXPORT_STORAGE_PATH  . '/' . $filename);
        $writer->save($filePath);

        return $filePath;
    }
}
