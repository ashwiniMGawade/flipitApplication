<?php

class EditorBallonText extends BaseEditorBallonText
{
    public static function deletetext($id)
    {
        $deleteText = Doctrine_Query::create()
            ->delete('EditorBallonText e')
            ->where('e.id ='.$id)
            ->execute();
        return $deleteText;
    }

    public static function getEditorText($userId)
    {
        if (!empty($userId)) {
            $editorTextInformation = Doctrine_Query::create()
                ->select("e.ballontext")
                ->from("EditorBallonText e")
                ->where('e.userid ='.$userId)
                ->fetchArray();
            return $editorTextInformation;
        } else {
            return true;
        }
    }
}
