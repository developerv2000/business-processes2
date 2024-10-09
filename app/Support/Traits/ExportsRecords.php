<?php

namespace App\Support\Traits;

use App\Support\Helper;
use Illuminate\Support\Facades\Gate;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Trait ExportsRecords
 *
 * Provides functionality to export model records to an Excel file using a predefined template.
 * The trait supports exporting records for both admin and non-admin users, where admin users
 * can export all records using chunking and non-admin users are limited to a specified number of records.
 *
 * @package App\Support\Traits
 */
trait ExportsRecords
{
    /**
     * Export model records to an Excel file.
     *
     * Exports the provided records query to an Excel file using a predefined template.
     * Admin users will export the records in chunks to avoid memory issues, while non-admin
     * users will only export a limited number of records.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query The query containing the records to export.
     * @return \Illuminate\Http\Response The response to download the Excel file.
     */
    public static function exportRecordsAsExcel($query)
    {
        $className = static::class;
        $unlimited = Gate::allows('export-unlimited-excel');

        // Load the Excel template
        $template = storage_path($className::EXCEL_TEMPLATE_STORAGE_PATH);
        $spreadsheet = IOFactory::load($template);
        $sheet = $spreadsheet->getActiveSheet();

        // Export records based on user role
        if ($unlimited) {
            // Admin users: process large record sets in chunks
            static::fillSheetByChunkingRecords($query, $sheet, $className);
        } else {
            // Non-admin users: limit records for export
            static::fillSheetByLimitedRecords($query, $sheet, $className);
        }

        // Save and return the Excel file
        return static::saveAndDownloadExcel($spreadsheet, $className);
    }

    /**
     * Fill the Excel sheet by chunking records (admin users).
     *
     * For admin users, process records in chunks to avoid memory issues when dealing
     * with large datasets. Each chunk of records is loaded and written to the Excel sheet.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query The query to fetch records.
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet The Excel sheet to fill.
     * @param string $className The name of the class invoking this method.
     * @return void
     */
    private static function fillSheetByChunkingRecords($query, &$sheet, $className)
    {
        $columnIndex = 1;
        $row = 2;

        // Chunk the records to handle large datasets efficiently
        $query->chunk(800, function ($recordsChunk) use (&$sheet, &$columnIndex, &$row, $className) {
            // Eager load necessary relations (e.g., comments) for better performance
            $recordsChunk->load('comments');

            // Prepare records for export if the class defines a method for it
            if (method_exists($className, 'prepareRecordsForExport')) {
                $className::prepareRecordsForExport($recordsChunk);
            }

            // Write each record to the Excel sheet
            foreach ($recordsChunk as $instance) {
                $columnIndex = 1;
                $columnValues = $instance->getExcelColumnValuesForExport();

                foreach ($columnValues as $value) {
                    $sheet->setCellValue([$columnIndex++, $row], $value);
                }

                // Move to the next row for the next record
                $row++;
            }
        });
    }

    /**
     * Fill the Excel sheet with a limited number of records (non-admin users).
     *
     * For non-admin users, limit the query to a specific number of records (50) and
     * write those records to the Excel sheet.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query The query to fetch records.
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet The Excel sheet to fill.
     * @param string $className The name of the class invoking this method.
     * @return void
     */
    private static function fillSheetByLimitedRecords($query, &$sheet, $className)
    {
        $columnIndex = 1;
        $row = 2;

        // Limit the records query to 50 for non-admin users
        $limitedRecords = $query->limit(50)->get();
        $limitedRecords->load('comments'); // Eager load comments for performance

        // Prepare records for export if necessary
        if (method_exists($className, 'prepareRecordsForExport')) {
            $className::prepareRecordsForExport($limitedRecords);
        }

        // Write the limited records to the Excel sheet
        foreach ($limitedRecords as $instance) {
            $columnIndex = 1;
            $columnValues = $instance->getExcelColumnValuesForExport();

            foreach ($columnValues as $value) {
                $sheet->setCellValue([$columnIndex++, $row], $value);
            }

            // Move to the next row for the next record
            $row++;
        }
    }

    /**
     * Save the Excel file and return a download response.
     *
     * Saves the generated Excel file to the appropriate storage path and returns a response
     * that prompts the user to download the file.
     *
     * @param \PhpOffice\PhpSpreadsheet\Spreadsheet $spreadsheet The generated spreadsheet.
     * @param string $className The name of the class invoking this method.
     * @return \Illuminate\Http\Response The response to download the Excel file.
     */
    private static function saveAndDownloadExcel($spreadsheet, $className)
    {
        // Create a writer and generate a unique filename for the export
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = date('Y-m-d H-i-s') . '.xlsx';
        $filename = Helper::escapeDuplicateFilename($filename, storage_path($className::EXCEL_EXPORT_STORAGE_PATH));
        $filePath = storage_path($className::EXCEL_EXPORT_STORAGE_PATH . '/' . $filename);

        // Save the Excel file
        $writer->save($filePath);

        // Return a download response
        return response()->download($filePath);
    }
}
