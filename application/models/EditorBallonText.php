<?php

class EditorBallonText extends BaseEditorBallonText
{
    public static function deletetext($id)
    {
        $data = Doctrine_Query::create()
            ->delete('EditorBallonText e')
            ->where('e.id ='.$id)
            ->execute();
        return $data;
    }

    public static function getEditorText($userId)
    {
        $editorTextInformation = Doctrine_Query::create()
            ->select("e.ballontext")
            ->from("EditorBallonText e")
            ->where('e.userid ='.$userId)
            ->fetchArray();
        return $editorTextInformation;
    }
}
