<?php
class Application_Service_Infrastructure_Csv_Writer extends Application_Service_Infrastructure_Csv_Base
{
    public function writeRow($row)
    {
        if (is_string($row)) {
            $row = explode(',', $row);
            $row = array_map('trim', $row);
        }
        return $this->handle->fputcsv($row, $this->delimiter, $this->enclosure);
    }

    public function writeFromArray(array $array)
    {
        foreach ($array as $key => $value) {
            $this->writeRow($value);
        }
    }
}