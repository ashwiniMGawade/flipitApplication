<?php
class DataTable_Helper extends \NeuroSYS\DoctrineDatatables\Table
{
    public static function createSearchRequest($override = array(), $props = array(), $search = array())
    {
         $request = array(
            'sEcho' => '1',
            'iColumns' => count($props),
            'sColumns' => '',
            'iDisplayStart' => '0',
            'iDisplayLength' => '100',
            'sSearch' => '',
            'bRegex' => 'false',
            'iSortingCols' => '1',
            'iSortCol_0' => '0',
            'sSortDir_0' => 'asc',
        );
        foreach ($props as $i => $prop) {
            $request['mDataProp_' . $i]   = $prop;
            $request['sSearch_' . $i]     = isset($search[$i]) ? $search[$i] : '';
            $request['bRegex_' . $i]      = 'false';
            $request['bSearchable_' . $i] = 'true';
            $request['bSortable_' . $i]   = 'true';
        }
        return array_merge($request, $override);
    }

    public static function createResponse($sEcho, $data, $totalNumberOfRecords)
    {
        $returnData = array(
            "sEcho" => $sEcho,
            "aaData" => $data,
            "iTotalRecords" => $totalNumberOfRecords,
            "iTotalDisplayRecords" => $totalNumberOfRecords
        );
        return $returnData;
    }
}
