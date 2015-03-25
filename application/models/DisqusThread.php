<?php
class DisqusThread extends BaseDisqusThread
{
    public static function saveDisqusThread($thread)
    {
        $disqusThread  = new DisqusThread();
        $disqusThread->id = $thread->id;
        $disqusThread->title = $thread->title;
        $disqusThread->link = $thread->link;
        $disqusThread->created = strtotime($thread->createdAt."+0000");
        $disqusThread->save();
        $tempFiles = glob(PUBLIC_PATH.'tmp/*');
        foreach ($tempFiles as $tempFile) {
            if (is_file($tempFile)) {
                unlink($tempFile);
            }
        }
    }
}
