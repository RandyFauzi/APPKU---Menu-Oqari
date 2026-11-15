<?php

namespace App\Actions\Products;

use ZipArchive;

class ExportMenuTemplateAction
{
    /**
     * Dapatkan path file template bulk upload menu (.xlsx atau .csv).
     * Jika format xlsx dan belum ada, file akan digenerate otomatis.
     *
     * @return string Path absolut ke file template
     */
    public function execute(int $shopId, string $format = 'xlsx'): string
    {
        if ($format === 'csv') {
            return $this->generateCsvTemplate();
        }

        return $this->getOrGenerateXlsxTemplate();
    }

    /**
     * Dapatkan atau generate file template Excel (.xlsx) dengan styling rapi.
     */
    public function getOrGenerateXlsxTemplate(): string
    {
        $dir = public_path('templates');
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $xlsxPath = $dir.DIRECTORY_SEPARATOR.'template_menu_oqari.xlsx';

        if (file_exists($xlsxPath) && filesize($xlsxPath) > 500) {
            return $xlsxPath;
        }

        $headers = ['Nama Menu', 'Kategori', 'Harga', 'Deskripsi'];
        $sampleRows = [
            ['Americano', 'Coffee', 22000, 'Espresso dengan air panas'],
            ['Caffe Latte', 'Coffee', 28000, 'Espresso dengan steamed milk lembut'],
            ['Matcha Latte', 'Drinks', 30000, 'Matcha Jepang murni dengan susu segar'],
            ['Butter Croissant', 'Pastry', 24000, 'Pastry renyah dengan butter premium'],
            ['Beef Burger', 'Main Course', 45000, 'Patty daging sapi gurih dengan saus spesial'],
            ['Nasi Goreng Spesial', 'Main Course', 45000, 'Nasi goreng lezat dengan rempah pilihan dan telur'],
        ];

        $this->createOpenXmlSpreadsheet($xlsxPath, $headers, $sampleRows);

        return $xlsxPath;
    }

    /**
     * Generate template CSV sebagai alternatif atau fallback.
     */
    public function generateCsvTemplate(): string
    {
        $dir = storage_path('app/templates');
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $csvPath = $dir.DIRECTORY_SEPARATOR.'template_menu_oqari.csv';
        $handle = fopen($csvPath, 'w');

        // Tulis UTF-8 BOM agar Excel membukanya dengan benar
        fwrite($handle, "\xEF\xBB\xBF");

        fputcsv($handle, ['Nama Menu', 'Kategori', 'Harga', 'Deskripsi']);
        fputcsv($handle, ['Americano', 'Coffee', 22000, 'Espresso dengan air panas']);
        fputcsv($handle, ['Caffe Latte', 'Coffee', 28000, 'Espresso dengan steamed milk lembut']);
        fputcsv($handle, ['Matcha Latte', 'Drinks', 30000, 'Matcha Jepang murni dengan susu segar']);
        fputcsv($handle, ['Butter Croissant', 'Pastry', 24000, 'Pastry renyah dengan butter premium']);
        fputcsv($handle, ['Beef Burger', 'Main Course', 45000, 'Patty daging sapi gurih dengan saus spesial']);
        fputcsv($handle, ['Nasi Goreng Spesial', 'Main Course', 45000, 'Nasi goreng lezat dengan rempah pilihan dan telur']);

        fclose($handle);

        return $csvPath;
    }

    /**
     * Buat file .xlsx standar OpenXML murni tanpa dependensi eksternal.
     *
     * @param  array<int, string>  $headers
     * @param  array<int, array<int, mixed>>  $rows
     */
    private function createOpenXmlSpreadsheet(string $destinationPath, array $headers, array $rows): bool
    {
        $zip = new ZipArchive;
        if ($zip->open($destinationPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return false;
        }

        // [Content_Types].xml
        $contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
    <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
    <Default Extension="xml" ContentType="application/xml"/>
    <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
    <Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
    <Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>
</Types>';
        $zip->addFromString('[Content_Types].xml', $contentTypes);

        // _rels/.rels
        $rels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
    <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
</Relationships>';
        $zip->addFromString('_rels/.rels', $rels);

        // xl/workbook.xml
        $workbook = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
    <sheets>
        <sheet name="Template Menu" sheetId="1" r:id="rId1"/>
    </sheets>
</workbook>';
        $zip->addFromString('xl/workbook.xml', $workbook);

        // xl/_rels/workbook.xml.rels
        $workbookRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
    <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
    <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>
</Relationships>';
        $zip->addFromString('xl/_rels/workbook.xml.rels', $workbookRels);

        // xl/styles.xml
        $styles = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
    <fonts count="2">
        <font><name val="Calibri"/><sz val="11"/></font>
        <font><b/><name val="Calibri"/><sz val="11"/></font>
    </fonts>
    <fills count="2">
        <fill><patternFill patternType="none"/></fill>
        <fill><patternFill patternType="gray125"/></fill>
    </fills>
    <borders count="1">
        <border><left/><right/><top/><bottom/><diagonal/></border>
    </borders>
    <cellStyleXfs count="1">
        <xf numFmtId="0" fontId="0" fillId="0" borderId="0"/>
    </cellStyleXfs>
    <cellXfs count="2">
        <xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>
        <xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0" applyFont="1"/>
    </cellXfs>
</styleSheet>';
        $zip->addFromString('xl/styles.xml', $styles);

        // xl/worksheets/sheet1.xml
        $colLetters = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];
        $sheetXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'."\n";
        $sheetXml .= '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'."\n";
        $sheetXml .= '  <cols>'."\n";
        $sheetXml .= '    <col min="1" max="1" width="28" customWidth="1"/>'."\n";
        $sheetXml .= '    <col min="2" max="2" width="20" customWidth="1"/>'."\n";
        $sheetXml .= '    <col min="3" max="3" width="16" customWidth="1"/>'."\n";
        $sheetXml .= '    <col min="4" max="4" width="50" customWidth="1"/>'."\n";
        $sheetXml .= '  </cols>'."\n";
        $sheetXml .= '  <sheetData>'."\n";

        // Header row (row 1)
        $sheetXml .= '    <row r="1">'."\n";
        foreach ($headers as $cIdx => $header) {
            $cellRef = $colLetters[$cIdx].'1';
            $escaped = htmlspecialchars($header, ENT_XML1, 'UTF-8');
            $sheetXml .= '      <c r="'.$cellRef.'" t="inlineStr" s="1"><is><t>'.$escaped.'</t></is></c>'."\n";
        }
        $sheetXml .= '    </row>'."\n";

        // Data rows
        $rowNum = 2;
        foreach ($rows as $row) {
            $sheetXml .= '    <row r="'.$rowNum.'">'."\n";
            foreach ($row as $cIdx => $val) {
                $cellRef = $colLetters[$cIdx].$rowNum;
                if (is_numeric($val) && $cIdx === 2) {
                    $sheetXml .= '      <c r="'.$cellRef.'" t="n"><v>'.$val.'</v></c>'."\n";
                } else {
                    $escaped = htmlspecialchars((string) $val, ENT_XML1, 'UTF-8');
                    $sheetXml .= '      <c r="'.$cellRef.'" t="inlineStr"><is><t>'.$escaped.'</t></is></c>'."\n";
                }
            }
            $sheetXml .= '    </row>'."\n";
            $rowNum++;
        }

        $sheetXml .= '  </sheetData>'."\n";
        $sheetXml .= '</worksheet>';
        $zip->addFromString('xl/worksheets/sheet1.xml', $sheetXml);

        $zip->close();

        return true;
    }
}
