<?php

namespace Poppy\MgrApp\Classes\Grid\Exporters;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Poppy\MgrApp\Classes\Table\Column\Column;
use function collect;
use function response;

class CsvExporter extends AbstractExporter
{

    /**
     * @inheritDoc
     */
    public function export()
    {
        $filename = $this->fileName . '.csv';

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');

            $titles = [];

            $this->chunk(function (Collection $records) use ($handle, &$titles) {
                if (empty($titles)) {
                    $titles = $this->getHeaderRow();

                    // Add CSV headers
                    fputcsv($handle, $titles);
                }

                $records = $this->getFormattedRecords($records);
                foreach ($records as $record) {
                    fputcsv($handle, $record);
                }
            });

            // Close the output stream
            fclose($handle);
        }, 200, [
            'Content-Encoding'    => 'UTF-8',
            'Content-Type'        => 'text/csv;charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ])->send();
    }

    /**
     * 获取 Header, 从记录中获取 Header
     * @return array
     */
    private function getHeaderRow(): array
    {
        $titles = collect();
        collect($this->table->visibleColsName())->each(function ($name) use ($titles) {
            if ($name === Column::NAME_ACTION) {
                return;
            }
            /** @var Column $column */
            $column = $this->table->visibleCols()->first(function (Column $column) use ($name) {
                return $column->name === $name;
            });

            if ($column) {
                $titles->push($column->label);
            } else {
                $titles->push(Str::ucfirst($name));
            }
        });
        return $titles->toArray();
    }

    /**
     * @param Collection $data
     * @return array
     */
    private function getFormattedRecords(Collection $data): array
    {
        return $data->map(function ($row) {
            $newRow = collect();
            $this->table->visibleCols()->each(function (Column $column) use ($row, $newRow) {
                if ($column->name === Column::NAME_ACTION) {
                    return;
                }
                $newRow->push($column->fillVal($row));
            });
            return $newRow->toArray();
        })->toArray();
    }
}
