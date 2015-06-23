<?php

class GlobalExportPassword extends BaseGlobalExportPassword
{
    public static function savePasswordForExportDownloads($type)
    {
        $globalExportInformation = Doctrine_Query::create()
            ->select("g.id")
            ->from("GlobalExportPassword g")
            ->where('g.exportType = "'.$type.'"')
            ->fetchArray();

        if (!empty($globalExportInformation)) {
            $globalExportPassword = Doctrine_Core::getTable("GlobalExportPassword")
                ->find($globalExportInformation[0]['id']);
            $globalExportPassword->password = mt_rand();
            $globalExportPassword->save();
        } else {
            $globalExportPassword = new GlobalExportPassword();
            $globalExportPassword->password = mt_rand();
            $globalExportPassword->exportType = $type;
            $globalExportPassword->save();
        }
        return true;
    }

    public static function getPasswordForExportDownloads($type)
    {
        $globalExportInformation = Doctrine_Query::create()
            ->select("g.password")
            ->from("GlobalExportPassword g")
            ->where('g.exportType = "'.$type.'"')
            ->fetchArray();

        $globalExportPassword = '';
        if (!empty($globalExportInformation)) {
            $globalExportPassword = $globalExportInformation[0]['password'];
        }

        return $globalExportPassword;
    }

    public function checkPasswordForExport($password, $type)
    {
        $globalExportInformation = Doctrine_Query::create()
            ->select("g.password")
            ->from("GlobalExportPassword g")
            ->where('g.password = "'.$password.'"')
            ->andWhere('g.exportType = "'.$type.'"')
            ->fetchArray();

        if (!empty($globalExportInformation)) {
            return true;
        } else {
            return false;
        }
    }
}
