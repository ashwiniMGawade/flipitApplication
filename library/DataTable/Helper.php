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
        return array(
        'sEcho' => isset($params['sEcho']) ? $params['sEcho'] : 0,
        'aaData' => $data,
        "iTotalRecords" => 500,
        "iTotalDisplayRecords" => isset($params['iDisplayLength']) ? $params['iDisplayLength'] : 0
        );
    }
}
