<?php

namespace App\Support;

use RuntimeException;
use ZipArchive;

class SimpleXlsxReader
{
    public function __construct(private readonly string $path)
    {
        if (! is_file($path)) {
            throw new RuntimeException("File XLSX tidak ditemukan: {$path}");
        }
    }

    public function rows(string $sheetName): array
    {
        $zip = new ZipArchive;

        if ($zip->open($this->path) !== true) {
            throw new RuntimeException("Gagal membuka XLSX: {$this->path}");
        }

        $sharedStrings = $this->sharedStrings($zip);
        $sheetPath = $this->sheetPath($zip, $sheetName);
        $xml = simplexml_load_string($zip->getFromName($sheetPath));
        $xml->registerXPathNamespace('main', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');

        $rows = [];
        foreach ($xml->sheetData->row as $row) {
            $values = [];
            foreach ($row->c as $cell) {
                $ref = (string) $cell['r'];
                $index = $this->columnIndex($ref);
                while (count($values) <= $index) {
                    $values[] = null;
                }

                $type = (string) $cell['t'];
                $value = isset($cell->v) ? (string) $cell->v : null;

                if ($type === 's' && $value !== null) {
                    $value = $sharedStrings[(int) $value] ?? null;
                } elseif ($type === 'inlineStr') {
                    $value = isset($cell->is->t) ? (string) $cell->is->t : null;
                }

                $values[$index] = $value === '' ? null : $value;
            }

            if (collect($values)->filter(fn ($value) => $value !== null && $value !== '')->isNotEmpty()) {
                $rows[] = $values;
            }
        }

        $zip->close();

        return $rows;
    }

    public function records(string $sheetName, array $requiredHeaders): array
    {
        $rows = $this->rows($sheetName);
        $headerIndex = null;
        $headers = [];

        foreach ($rows as $index => $row) {
            $normalized = array_map(fn ($value) => $this->normalizeHeader((string) $value), $row);
            if (count(array_intersect($requiredHeaders, $normalized)) >= min(2, count($requiredHeaders))) {
                $headerIndex = $index;
                $headers = $normalized;
                break;
            }
        }

        if ($headerIndex === null) {
            return [];
        }

        $records = [];
        foreach (array_slice($rows, $headerIndex + 1) as $row) {
            $record = [];
            foreach ($headers as $column => $header) {
                if ($header === '') {
                    continue;
                }

                $record[$header] = $row[$column] ?? null;
            }

            if ($this->isDataRecord($record, $requiredHeaders)) {
                $records[] = $record;
            }
        }

        return $records;
    }

    private function sharedStrings(ZipArchive $zip): array
    {
        if ($zip->locateName('xl/sharedStrings.xml') === false) {
            return [];
        }

        $xml = simplexml_load_string($zip->getFromName('xl/sharedStrings.xml'));
        $xml->registerXPathNamespace('main', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
        $strings = [];

        foreach ($xml->si as $item) {
            $item->registerXPathNamespace('main', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
            $text = '';
            foreach ($item->xpath('.//main:t') ?: [] as $part) {
                $text .= (string) $part;
            }

            if ($text === '') {
                $text = (string) $item->t;
            }

            $strings[] = trim($text);
        }

        return $strings;
    }

    private function sheetPath(ZipArchive $zip, string $sheetName): string
    {
        $workbook = simplexml_load_string($zip->getFromName('xl/workbook.xml'));
        $workbook->registerXPathNamespace('rel', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships');
        $rels = simplexml_load_string($zip->getFromName('xl/_rels/workbook.xml.rels'));
        $relations = [];

        foreach ($rels->Relationship as $relation) {
            $relations[(string) $relation['Id']] = (string) $relation['Target'];
        }

        foreach ($workbook->sheets->sheet as $sheet) {
            if ((string) $sheet['name'] !== $sheetName) {
                continue;
            }

            $attributes = $sheet->attributes('http://schemas.openxmlformats.org/officeDocument/2006/relationships');
            $target = $relations[(string) $attributes['id']] ?? null;

            if ($target === null) {
                break;
            }

            return str_starts_with($target, 'xl/') ? $target : 'xl/'.ltrim($target, '/');
        }

        throw new RuntimeException("Sheet {$sheetName} tidak ditemukan pada {$this->path}");
    }

    private function columnIndex(string $cellRef): int
    {
        preg_match('/[A-Z]+/i', $cellRef, $matches);
        $letters = strtoupper($matches[0] ?? 'A');
        $index = 0;

        foreach (str_split($letters) as $letter) {
            $index = ($index * 26) + ord($letter) - 64;
        }

        return $index - 1;
    }

    private function normalizeHeader(string $header): string
    {
        $header = strtolower(trim(str_replace(['.', ' '], ['_', '_'], $header)));

        return trim(preg_replace('/_+/', '_', $header), '_');
    }

    private function isDataRecord(array $record, array $requiredHeaders): bool
    {
        foreach ($requiredHeaders as $header) {
            $value = $record[$header] ?? null;
            if ($value === null || $value === '') {
                return false;
            }
        }

        $first = strtolower((string) reset($record));

        return ! str_contains($first, 'kolom') && ! str_contains($first, 'data ini') && ! str_contains($first, 'kode_');
    }
}
