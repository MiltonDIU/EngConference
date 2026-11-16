<?php

namespace App\Services;

use App\Models\Paper;
use App\Models\PaperAuthor;
use Illuminate\Support\Collection;
use ZipArchive;

/**
 * Paid papers with the first author and every co-author, each with their affiliation.
 * The Excel file follows BNC_2026_Blank_Author_Paper_Template.xlsx (header style, frozen
 * header row, Excel table), with Paper Title as the second column and only the
 * co-author columns the data needs.
 */
class PaperAuthorsReportService
{
    public static function rows(): Collection
    {
        return Paper::where('payment_status', '1')
            ->with('authors.country')
            ->orderBy('submission_id')
            ->get()
            ->map(function (Paper $paper) {
                // authors() is ordered by author_order, so the first one is the first author
                $authors = $paper->authors->values();
                $first = $authors->first();

                return (object) [
                    'submission_id' => $paper->submission_id,
                    'title' => $paper->title,
                    'first_author' => $first ? self::person($first) : null,
                    'co_authors' => $authors->slice(1)->map(fn ($a) => self::person($a))->values(),
                ];
            });
    }

    /**
     * Only as many co-author columns as the paper with the most co-authors needs.
     */
    public static function coAuthorColumns(Collection $rows): int
    {
        return (int) $rows->max(fn ($row) => $row->co_authors->count());
    }

    public static function headers(int $coAuthorColumns): array
    {
        $headers = ['Submission ID', 'Paper Title', 'First Author Name', 'First Author Affiliation'];
        for ($i = 1; $i <= $coAuthorColumns; $i++) {
            $headers[] = "Co-Author {$i} Name";
            $headers[] = "Co-Author {$i} Affiliation";
        }

        return $headers;
    }

    public static function cells(object $row, int $coAuthorColumns): array
    {
        $cells = [
            $row->submission_id,
            $row->title,
            $row->first_author['name'] ?? '',
            $row->first_author['affiliation'] ?? '',
        ];
        for ($i = 0; $i < $coAuthorColumns; $i++) {
            $cells[] = $row->co_authors[$i]['name'] ?? '';
            $cells[] = $row->co_authors[$i]['affiliation'] ?? '';
        }

        return $cells;
    }

    /**
     * Builds the .xlsx and returns the path of a temporary file.
     */
    public static function buildXlsx(Collection $rows): string
    {
        $coAuthorColumns = self::coAuthorColumns($rows);
        $headers = self::headers($coAuthorColumns);
        $lastColumn = self::columnLetter(count($headers));
        $lastRow = max(2, $rows->count() + 1);

        $strings = [];
        $stringIndex = function (string $value) use (&$strings) {
            if (!isset($strings[$value])) {
                $strings[$value] = count($strings);
            }
            return $strings[$value];
        };

        $sheetRows = [];
        $headerCells = '';
        foreach ($headers as $i => $header) {
            $headerCells .= '<c r="' . self::columnLetter($i + 1) . '1" s="1" t="s"><v>' . $stringIndex($header) . '</v></c>';
        }
        $sheetRows[] = '<row r="1" ht="38.1" customHeight="1">' . $headerCells . '</row>';

        foreach ($rows->values() as $index => $row) {
            $r = $index + 2;
            $rowCells = '';
            foreach (self::cells($row, $coAuthorColumns) as $i => $value) {
                $ref = self::columnLetter($i + 1) . $r;
                // Submission ID is kept as text (style 3), the rest wrap (style 2)
                $style = $i === 0 ? 3 : 2;
                $value = trim((string) $value);
                $rowCells .= $value === ''
                    ? '<c r="' . $ref . '" s="' . $style . '"/>'
                    : '<c r="' . $ref . '" s="' . $style . '" t="s"><v>' . $stringIndex($value) . '</v></c>';
            }
            $sheetRows[] = '<row r="' . $r . '">' . $rowCells . '</row>';
        }

        // Column widths: ID, title, then name / affiliation pairs
        $cols = '<col min="1" max="1" width="17" customWidth="1"/><col min="2" max="2" width="60" customWidth="1"/>';
        for ($c = 3; $c <= count($headers); $c++) {
            $cols .= '<col min="' . $c . '" max="' . $c . '" width="' . ($c % 2 ? 26 : 45) . '" customWidth="1"/>';
        }

        $ns = 'xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"';
        $rel = 'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"';

        $sheet = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<worksheet ' . $ns . ' ' . $rel . '>'
            . '<dimension ref="A1:' . $lastColumn . $lastRow . '"/>'
            . '<sheetViews><sheetView tabSelected="1" workbookViewId="0"><pane ySplit="1" topLeftCell="A2" activePane="bottomLeft" state="frozen"/><selection pane="bottomLeft" activeCell="A2" sqref="A2"/></sheetView></sheetViews>'
            . '<sheetFormatPr defaultRowHeight="14.25"/>'
            . '<cols>' . $cols . '</cols>'
            . '<sheetData>' . implode('', $sheetRows) . '</sheetData>'
            . '<pageMargins left="0.7" right="0.7" top="0.75" bottom="0.75" header="0.3" footer="0.3"/>'
            . '<tableParts count="1"><tablePart r:id="rId1"/></tableParts>'
            . '</worksheet>';

        $tableColumns = '';
        foreach ($headers as $i => $header) {
            $tableColumns .= '<tableColumn id="' . ($i + 1) . '" name="' . self::xml($header) . '"/>';
        }
        $table = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<table ' . $ns . ' id="1" name="AuthorPaperMaster" displayName="AuthorPaperMaster" ref="A1:' . $lastColumn . $lastRow . '" totalsRowShown="0">'
            . '<autoFilter ref="A1:' . $lastColumn . $lastRow . '"/>'
            . '<tableColumns count="' . count($headers) . '">' . $tableColumns . '</tableColumns>'
            . '<tableStyleInfo name="TableStyleMedium2" showFirstColumn="0" showLastColumn="0" showRowStripes="1" showColumnStripes="0"/>'
            . '</table>';

        $sharedStrings = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<sst ' . $ns . ' count="' . count($strings) . '" uniqueCount="' . count($strings) . '">';
        foreach (array_keys($strings) as $value) {
            $sharedStrings .= '<si><t xml:space="preserve">' . self::xml((string) $value) . '</t></si>';
        }
        $sharedStrings .= '</sst>';

        // Styles copied from the template: 1 = navy header, 2 = wrapped text, 3 = wrapped text-format
        $styles = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<styleSheet ' . $ns . '>'
            . '<fonts count="2"><font><sz val="11"/><name val="Carlito"/></font><font><b/><sz val="11"/><color rgb="FFFFFFFF"/><name val="Carlito"/></font></fonts>'
            . '<fills count="3"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill><fill><patternFill patternType="solid"><fgColor rgb="FF182C45"/></patternFill></fill></fills>'
            . '<borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders>'
            . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            . '<cellXfs count="4">'
            . '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
            . '<xf numFmtId="0" fontId="1" fillId="2" borderId="0" xfId="0" applyFont="1" applyFill="1" applyAlignment="1"><alignment horizontal="center" vertical="center" wrapText="1"/></xf>'
            . '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0" applyAlignment="1"><alignment vertical="center" wrapText="1"/></xf>'
            . '<xf numFmtId="49" fontId="0" fillId="0" borderId="0" xfId="0" applyNumberFormat="1" applyAlignment="1"><alignment vertical="center" wrapText="1"/></xf>'
            . '</cellXfs>'
            . '<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>'
            . '<dxfs count="0"/><tableStyles count="0" defaultTableStyle="TableStyleMedium2" defaultPivotStyle="PivotStyleLight16"/>'
            . '</styleSheet>';

        $files = [
            '[Content_Types].xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
                . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
                . '<Default Extension="xml" ContentType="application/xml"/>'
                . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
                . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
                . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
                . '<Override PartName="/xl/sharedStrings.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml"/>'
                . '<Override PartName="/xl/tables/table1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.table+xml"/>'
                . '</Types>',
            '_rels/.rels' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
                . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
                . '</Relationships>',
            'xl/workbook.xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                . '<workbook ' . $ns . ' ' . $rel . '>'
                . '<sheets><sheet name="Author &amp; Paper" sheetId="1" r:id="rId1"/></sheets>'
                . '</workbook>',
            'xl/_rels/workbook.xml.rels' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
                . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
                . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
                . '<Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings" Target="sharedStrings.xml"/>'
                . '</Relationships>',
            'xl/worksheets/sheet1.xml' => $sheet,
            'xl/worksheets/_rels/sheet1.xml.rels' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
                . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/table" Target="../tables/table1.xml"/>'
                . '</Relationships>',
            'xl/tables/table1.xml' => $table,
            'xl/styles.xml' => $styles,
            'xl/sharedStrings.xml' => $sharedStrings,
        ];

        $path = tempnam(sys_get_temp_dir(), 'authors_') . '.xlsx';
        $zip = new ZipArchive();
        if ($zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Could not create the Excel file.');
        }
        foreach ($files as $name => $content) {
            $zip->addFromString($name, $content);
        }
        $zip->close();

        return $path;
    }

    /**
     * Name plus affiliation: designation, department, institution, country.
     */
    private static function person(PaperAuthor $author): array
    {
        $affiliation = collect([$author->designation, $author->department, $author->institution, $author->country->name ?? null])
            ->map(fn ($part) => trim((string) $part))
            ->filter()
            ->implode(', ');

        return ['name' => trim((string) $author->name), 'affiliation' => $affiliation];
    }

    private static function columnLetter(int $number): string
    {
        $letter = '';
        while ($number > 0) {
            $mod = ($number - 1) % 26;
            $letter = chr(65 + $mod) . $letter;
            $number = intdiv($number - 1, 26);
        }

        return $letter;
    }

    private static function xml(string $value): string
    {
        // Drop control characters XML does not allow
        $value = preg_replace('/[^\x{9}\x{A}\x{D}\x{20}-\x{D7FF}\x{E000}-\x{FFFD}]/u', '', $value) ?? '';

        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
