<?php

namespace App\Support\Traits;

use App\Support\Helper;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Trait ExportsRecords
 *
 * This trait provides functionality to export model records to an Excel file.
 *
 * @package App\Support\Traits
 */
trait ExportsRecords
{
    /**
     * Export model records to an Excel file.
     *
     * This function exports the given records to an Excel file using a template.
     * It saves the Excel file to storage and returns a download response.
     *
     * @param \Illuminate\Support\Collection $records The records to export.
     * @return \Illuminate\Http\Response The download response.
     */
    public static function exportRecordsAsExcel($records)
    {
        $className = static::class;

        // Load the Excel template
        $template = storage_path($className::EXCEL_TEMPLATE_STORAGE_PATH);
        $spreadsheet = IOFactory::load($template);
        $sheet = $spreadsheet->getActiveSheet();

        // Start adding records from first column and second row (A2)
        $columnIndex = 1;
        $row = 2;

        // Chunk records to avoid memory issues and iterate over each chunk
        $records->chunk(800, function ($recordsChunk) use (&$sheet, &$columnIndex, &$row) {
            $recordsChunk->load('comments'); // Load comments for performance

            foreach ($recordsChunk as $instance) {
                $columnIndex = 1;
                $columnValues = $instance->getExcelColumnValuesForExport();

                foreach ($columnValues as $value) {
                    $sheet->setCellValue([$columnIndex++, $row], $value);
                }

                // Increment row for the next record
                $row++;
            }
        });

        // Save the Excel file
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = date('Y-m-d H-i-s') . '.xlsx';
        $filename = Helper::escapeDuplicateFilename($filename, storage_path($className::EXCEL_EXPORT_STORAGE_PATH));
        $filePath = storage_path($className::EXCEL_EXPORT_STORAGE_PATH  . '/' . $filename);
        $writer->save($filePath);

        // Return download response
        return response()->download($filePath);
    }
}
