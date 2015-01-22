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

    public static function getEditorText($shopId)
    {
        if (!empty($shopId)) {
            $editorTextInformation = Doctrine_Query::create()
                ->select("e.ballontext")
                ->from("EditorBallonText e")
                ->where('e.shopid ='.$shopId)
                ->fetchArray();
            return $editorTextInformation;
        } else {
            $editorTextInformation = array();
        }
        return $editorTextInformation;
    }
}
