<?php

namespace App\Support\Traits;

use App\Support\Helper;
use PhpOffice\PhpSpreadsheet\IOFactory;

trait ExportsItems
{
    /**
     * Export items to Excel file.
     *
     * This function exports the given items to an Excel file using a template.
     * It saves the Excel file to storage and returns a download response.
     *
     * @param \Illuminate\Support\Collection $items The items to export.
     * @return \Illuminate\Http\Response The download response.
     */
    public static function exportItemsAsExcel($items)
    {
        $className = static::class;

        // Load the Excel template
        $template = storage_path($className::EXCEL_TEMPLATE_STORAGE_PATH);
        $spreadsheet = IOFactory::load($template);
        $sheet = $spreadsheet->getActiveSheet();

        // Start adding items from first column and second row (A2)
        $columnIndex = 1;
        $row = 2;

        // Chunk items to avoid memory issues and iterate over each chunk
        $items->chunk(800, function ($itemsChunk) use (&$sheet, &$columnIndex, &$row) {
            foreach ($itemsChunk as $instance) {
                $columnIndex = 1;
                $columnValues = $instance->getExcelColumnValuesForExport();

                foreach ($columnValues as $value) {
                    $sheet->setCellValue([$columnIndex++, $row], $value);
                }

                // Increment row for the next item
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
