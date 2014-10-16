<?php
class DataTable_Helper
{
    public static function createSearchRequest($override = array(), $props = array(), $search = array())
    {
        foreach ($props as $i => $prop) {
            $override['mDataProp_' . $i]   = $prop;
        }
        return $override;
    }

    public static function getResponseArray($data, $params)
    {
        return array(
        'sEcho' => $params['sEcho'],
        'aaData' => $data,
        "iTotalRecords" => 500,
        "iTotalDisplayRecords" => $params['iDisplayLength']
        );
    }
}
