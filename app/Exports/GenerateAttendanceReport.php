<?php

namespace App\Exports;

use App\Models\AnagkazoAttendance;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class GenerateAttendanceReport implements FromCollection, WithHeadings
{
    use Exportable;
    protected $datefrom, $dateTo, $event, $headings, $classId, $calculatePointFlag, $headingDates;

    public function __construct($datefrom, $dateTo, $event, $classId, $calculatePoint)
    {
        $this->datefrom = $datefrom;
        $this->dateTo = $dateTo;
        $this->event = $event;
        $this->classId = $classId;
        $this->calculatePointFlag = $calculatePoint;
        $this->headings = ['index number', 'name', 'batch'];
    }

    public function generateHeadingsOfDates($datefrom, $dateTo, $event)
    {

        $this->headingDates = AnagkazoAttendance::getDateHeadingsFromRange($datefrom, $dateTo, $event);
        $this->headings = array_merge($this->headings, $this->headingDates);

        return $this->headings;
    }

    public function collection()
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '300');
        $resultantArray = [];

        Log::info($this->headingDates);

        $resultantArray = AnagkazoAttendance::getAttendanceExcelStructure($this->datefrom, $this->dateTo, $this->event, $this->classId, $this->headingDates);

        return new Collection($resultantArray);
    }

    public function headings(): array
    {
        return $this->generateHeadingsOfDates($this->datefrom, $this->dateTo, $this->event);
    }
}
