<?php

// For more info on the CsvReader/ Writer have a look at https://github.com/jwage/easy-csv

class Application_Service_Infrastructure_Csv_Base
{
    protected $handle;
    protected $delimiter = ';';
    protected $enclosure = '"';

    public function __construct($path, $mode = 'w+')
    {
        if (!file_exists($path)) {
            touch($path);
        }
        $this->handle = new \SplFileObject($path, $mode);
        $this->handle->setFlags(\SplFileObject::SKIP_EMPTY | \SplFileObject::DROP_NEW_LINE | \SplFileObject::READ_AHEAD);
    }

    public function __destruct()
    {
        $this->handle = null;
    }

    public function setDelimiter($delimiter)
    {
        $this->delimiter = $delimiter;
    }

    public function setEnclosure($enclosure)
    {
        $this->enclosure = $enclosure;
    }
}