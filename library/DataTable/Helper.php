<?php
class DataTable_Helper extends \NeuroSYS\DoctrineDatatables\Table
{
    public static function createSearchRequest($override = array(), $props = array(), $search = array())
    {
        foreach ($props as $i => $prop) {
            $override['mDataProp_' . $i]   = $prop;
        }
        return $override;
    }

    public static function getResponse($data, $params)
    {
        return array('aaData' => $data);
    }
}
