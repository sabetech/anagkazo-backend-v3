<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\AttendancePerSheet;

class AttendanceExport implements WithMultipleSheets
{
    use Exportable;

    protected $batch, $start, $end, $event;

    public function __construct($batch, $event, $start, $end)
    {
        $this->batch = $batch;
        $this->start = $start;
        $this->end = $end;
        $this->event = $event;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        foreach($batches as $batch) {
            $sheets[] = new AttendancePerSheet($batch);
        }

        return $sheets;
    }
}
