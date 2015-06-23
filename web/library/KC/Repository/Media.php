<?php
namespace KC\Repository;

class Media extends \Core\Domain\Entity\Media
{
    public function con()
    {
        $options = array(
                'script_url' => $this->getFullUrl().'/',
                'upload_dir' => ROOT_PATH .'images/upload/media/',
                'upload_url' => $this->getFullUrl().'images/upload/media/',
                'param_name' => 'files',
                // Set the following option to 'POST', if your server does not support
                // DELETE requests. This is a parameter sent to the client:
                'delete_type' => 'DELETE',
                // The php.ini settings upload_max_filesize and post_max_size
                // take precedence over the following max_file_size setting:
                'max_file_size' => '2097152',//null
                'min_file_size' => 1,//1
                'accept_file_types' => '([^\s]+(\.(?i)(jpg|png|gif|pdf))$)',
                // The maximum number of files for the upload directory:
                'max_number_of_files' => null,
                // Image resolution restrictions:
                'max_width' => null,
                'max_height' => null,
                'min_width' => 1,
                'min_height' => 1,
                // Set the following option to false to enable resumable uploads:
                'discard_aborted_uploads' => true,
                // Set to true to rotate images based on EXIF meta data, if available:
                'orient_image' => false,
                'image_versions' => array(
                    'thumbnail' => array(
                        'upload_dir' => ROOT_PATH .'images/upload/media/thumb/',
                        'upload_url' => $this->getFullUrl().'/images/upload/media/thumb/',
                        'max_width' => 80,
                        'max_height' => 80
                    )
                ),
                'image_versions_L' => array(
                    'thumbnail' => array(
                        'upload_dir' => ROOT_PATH .'images/upload/media/thumb_L/',
                        'upload_url' => $this->getFullUrl().'/images/upload/media/thumb/',
                        'max_width' => 126,
                        'max_height' => 90
                    )
                )
        );

        return $options;
    }

    protected function getFullUrl()
    {
        $https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
        $localePath =   (LOCALE == 'en') ? "" : '/' .LOCALE ;

        return
            ($https ? 'https://' : 'http://').
            (!empty($_SERVER['REMOTE_USER']) ? $_SERVER['REMOTE_USER'].'@' : '').
            (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ($_SERVER['SERVER_NAME'].
            ($https && $_SERVER['SERVER_PORT'] === 443 ||
            $_SERVER['SERVER_PORT'] === 80 ? '' : ':'.$_SERVER['SERVER_PORT']))).
            substr($_SERVER['SCRIPT_NAME'], 0, strrpos($_SERVER['SCRIPT_NAME'], '/')).$localePath;


    }

    protected function set_file_delete_url($file)
    {
        $conVar = Media::con();
        $file->delete_url = $conVar['script_url']
            .'?file='.rawurlencode($file->name);
        $file->delete_type = $conVar['delete_type'];
        if ($file->delete_type !== 'DELETE') {
            $file->delete_url .= '&_method=DELETE';
        }
    }

    protected function get_file_object($file_name)
    {
        $conVar = Media::con();
        $file_path = $conVar['upload_dir'].$file_name;
        if (is_file($file_path) && $file_name[0] !== '.') {
            $file = new \stdClass();
            $file->name = $file_name;
            $file->size = filesize($file_path);
            $file->url = $conVar['upload_url'].rawurlencode($file->name);
            foreach ($conVar['image_versions'] as $version => $options) {
                if (is_file($options['upload_dir'].$file_name)) {
                    $file->{$version.'_url'} = $options['upload_url']
                        .rawurlencode($file->name);
                }
            }
            $this->set_file_delete_url($file);
            return $file;
        }
        return null;
    }

    protected function get_file_objects()
    {
        $conVar = Media::con();
        return array_values(array_filter(array_map(
            array($this, 'get_file_object'),
            scandir($conVar['upload_dir'])
        )));
    }

    protected function create_scaled_image($file_name, $options)
    {
        $conVar = Media::con();

        if (!file_exists($conVar['upload_dir'])) {
            mkdir($conVar['upload_dir'], 776, true);
        }

        $file_path = $conVar['upload_dir'].$file_name;
        $new_file_path = $options['upload_dir'].$file_name;

        if (!file_exists($options['upload_dir'])) {
            mkdir($options['upload_dir'], 776, true);
        }


        list($img_width, $img_height) = @getimagesize($file_path);
        if (!$img_width || !$img_height) {
            return false;
        }
        $scale = min(
            $options['max_width'] / $img_width,
            $options['max_height'] / $img_height
        );
        if ($scale >= 1) {
            if ($file_path !== $new_file_path) {
                return copy($file_path, $new_file_path);
            }
            return true;
        }
        $new_width = $img_width * $scale;
        $new_height = $img_height * $scale;
        $new_img = @imagecreatetruecolor($new_width, $new_height);
        switch (strtolower(substr(strrchr($file_name, '.'), 1))) {
            case 'jpg':
            case 'jpeg':
                $src_img = @imagecreatefromjpeg($file_path);
                $write_image = 'imagejpeg';
                $image_quality = isset($options['jpeg_quality']) ?
                $options['jpeg_quality'] : 75;
                break;
            case 'gif':
                @imagecolortransparent($new_img, @imagecolorallocate($new_img, 0, 0, 0));
                $src_img = @imagecreatefromgif($file_path);
                $write_image = 'imagegif';
                $image_quality = null;
                break;
            case 'png':
                @imagecolortransparent($new_img, @imagecolorallocate($new_img, 0, 0, 0));
                @imagealphablending($new_img, false);
                @imagesavealpha($new_img, true);
                $src_img = @imagecreatefrompng($file_path);
                $write_image = 'imagepng';
                $image_quality = isset($options['png_quality']) ?
                $options['png_quality'] : 9;
                break;
            default:
                $src_img = null;
        }
        $success = $src_img && @imagecopyresampled(
            $new_img,
            $src_img,
            0,
            0,
            0,
            0,
            $new_width,
            $new_height,
            $img_width,
            $img_height
        ) && $write_image($new_img, $new_file_path, $image_quality);
        @imagedestroy($src_img);
        @imagedestroy($new_img);
        return $success;
    }

    protected function validateImage($uploaded_file, $file, $error, $index)
    {
        $conVar = Media::con();
        if ($error) {
            $file->error = 'maxFileSize';
            return false;
        }
        if (!$file->name) {
            $file->error = 'missingFileName';
            return false;
        }
        if (!preg_match($conVar['accept_file_types'], $file->name)) {
            $file->error = 'acceptFileTypes';
            return false;
        }
        if ($uploaded_file && is_uploaded_file($uploaded_file)) {
            $file_size = filesize($uploaded_file);
        } else {
            $file_size = $_SERVER['CONTENT_LENGTH'];
        }

        if ($conVar['max_file_size'] && (
                $file_size > $conVar['max_file_size'] ||
                $file->size > $conVar['max_file_size'])
        ) {
            $file->error = 'maxFileSize';
            return false;
        }
        if ($conVar['min_file_size'] &&
                $file_size < $conVar['min_file_size']) {
            $file->error = 'minFileSize';
            return false;
        }
        if (is_int($conVar['max_number_of_files']) && (
                count($this->get_file_objects()) >= $conVar['max_number_of_files'])
        ) {
            $file->error = 'maxNumberOfFiles';
            return false;
        }
        list($img_width, $img_height) = @getimagesize($uploaded_file);
        if (is_int($img_width)) {
            if ($conVar['max_width'] && $img_width > $conVar['max_width'] ||
                    $conVar['max_height'] && $img_height > $conVar['max_height']) {
                $file->error = 'maxResolution';
                return false;
            }
            if ($conVar['min_width'] && $img_width < $conVar['min_width'] ||
                    $conVar['min_height'] && $img_height < $conVar['min_height']) {
                $file->error = 'minResolution';
                return false;
            }
        }
        return true;
    }

    protected function upcount_name_callback($matches)
    {
        $index = isset($matches[1]) ? intval($matches[1]) + 1 : 1;
        $ext = isset($matches[2]) ? $matches[2] : '';
        return ' ('.$index.')'.$ext;
    }

    protected function upcount_name($name)
    {
        return preg_replace_callback(
            '/(?:(?: \(([\d]+)\))?(\.[^.]+))?$/',
            array($this, 'upcount_name_callback'),
            $name,
            1
        );
    }

    protected function trim_file_name($name, $type, $index)
    {
        $conVar = Media::con();
        $file_name = trim(basename(stripslashes($name)), ".\x00..\x20");
        if (strpos($file_name, '.') === false &&
                preg_match('/^image\/(gif|jpe?g|png)/', $type, $matches)) {
            $file_name .= '.'.$matches[1];
        }
        if ($conVar['discard_aborted_uploads']) {
            while (is_file($conVar['upload_dir'].$file_name)) {
                $file_name = $this->upcount_name($file_name);
            }
        }
        return $file_name;
    }

    protected function handle_form_data($file, $index)
    {
    }

    protected function orient_image($file_path)
    {
        $exif = @exif_read_data($file_path);
        if ($exif === false) {
            return false;
        }
        $orientation = intval(@$exif['Orientation']);
        if (!in_array($orientation, array(3, 6, 8))) {
            return false;
        }
        $image = @imagecreatefromjpeg($file_path);
        switch ($orientation) {
            case 3:
                $image = @imagerotate($image, 180, 0);
                break;
            case 6:
                $image = @imagerotate($image, 270, 0);
                break;
            case 8:
                $image = @imagerotate($image, 90, 0);
                break;
            default:
                return false;
        }
        $success = imagejpeg($image, $file_path);
        @imagedestroy($image);
        return $success;
    }

    protected function handle_file_upload($uploaded_file, $name, $size, $type, $error, $index = null)
    {
        $conVar = Media::con();
        $file = new \stdClass();
        $file->name = $this->trim_file_name($name, $type, $index);
        $file->size = intval($size);
        $file->type = $type;
        if ($this->validateImage($uploaded_file, $file, $error, $index)) {
            $this->handle_form_data($file, $index);

            if (!file_exists($conVar['upload_dir'])) {
                mkdir($conVar['upload_dir'], 776, true);
            }

            $file_path = $conVar['upload_dir'].$file->name;
            $append_file = !$conVar['discard_aborted_uploads'] &&
                is_file($file_path) && $file->size > filesize($file_path);
            clearstatcache();
            if ($uploaded_file && is_uploaded_file($uploaded_file)) {
                if ($append_file) {
                    file_put_contents(
                        $file_path,
                        fopen($uploaded_file, 'r'),
                        FILE_APPEND
                    );
                } else {
                    $fileNameUrl = $file->name;
                    $thumbnailUrl = $file->name;
                    $file->id = $this->databaseConnectOperation($fileNameUrl, $thumbnailUrl, 'insert');
                    move_uploaded_file($uploaded_file, $file_path);
                }
            } else {
                file_put_contents(
                    $file_path,
                    fopen('php://input', 'r'),
                    $append_file ? FILE_APPEND : 0
                );
            }
            $file_size = filesize($file_path);
            if ($file_size === $file->size) {
                if ($conVar['orient_image']) {
                    $this->orient_image($file_path);
                }
                $file->url = $conVar['upload_url'].rawurlencode($file->name);
                foreach ($conVar['image_versions'] as $version => $options) {
                    if ($this->create_scaled_image($file->name, $options)) {
                        if (!file_exists($conVar['upload_dir'])) {
                            mkdir($conVar['upload_dir'], 776, true);
                        }
                        if ($conVar['upload_dir'] !== $options['upload_dir']) {
                            $file->{$version.'_url'} = $options['upload_url']
                                .rawurlencode($file->name);
                        } else {
                            clearstatcache();
                            $file_size = filesize($file_path);
                        }
                    }
                }
                foreach ($conVar['image_versions_L'] as $version => $options) {
                    if ($this->create_scaled_image($file->name, $options)) {
                        if ($conVar['upload_dir'] !== $options['upload_dir']) {
                            $file->{$version.'_url'} = $options['upload_url']
                            .rawurlencode($file->name);
                        } else {
                            clearstatcache();
                            $file_size = filesize($file_path);
                        }
                    }
                }
            } elseif ($conVar['discard_aborted_uploads']) {
                unlink($file_path);
                $file->error = 'abort';
            }
            $file->size = $file_size;
            $this->set_file_delete_url($file);
        }
        return $file;
    }

    public function getfile()
    {
        $file_name = isset($_REQUEST['file']) ?
            basename(stripslashes($_REQUEST['file'])) : null;
        if ($file_name) {
            $info = $this->get_file_object($file_name);
        } else {
            $info = $this->get_file_objects();
        }
        header('Content-type: application/json');
    }

    public function post()
    {
        $conVar = Media::con();
        if (isset($_REQUEST['_method']) && $_REQUEST['_method'] === 'DELETE') {
            return $this->deleteMedia();
        }
        $upload = isset($_FILES[$conVar['param_name']]) ?
            $_FILES[$conVar['param_name']] : null;
        $info = array();
        if ($upload && is_array($upload['tmp_name'])) {
            foreach ($upload['tmp_name'] as $index => $value) {
                $info[] = $this->handle_file_upload(
                    $upload['tmp_name'][$index],
                    isset($_SERVER['HTTP_X_FILE_NAME']) ?
                    $_SERVER['HTTP_X_FILE_NAME'] : $upload['name'][$index],
                    isset($_SERVER['HTTP_X_FILE_SIZE']) ?
                    $_SERVER['HTTP_X_FILE_SIZE'] : $upload['size'][$index],
                    isset($_SERVER['HTTP_X_FILE_TYPE']) ?
                    $_SERVER['HTTP_X_FILE_TYPE'] : $upload['type'][$index],
                    $upload['error'][$index],
                    $index
                );
            }
        } elseif ($upload || isset($_SERVER['HTTP_X_FILE_NAME'])) {
            $info[] = $this->handle_file_upload(
                isset($upload['tmp_name']) ? $upload['tmp_name'] : null,
                isset($_SERVER['HTTP_X_FILE_NAME']) ?
                $_SERVER['HTTP_X_FILE_NAME'] : (isset($upload['name']) ?
                        $upload['name'] : null),
                isset($_SERVER['HTTP_X_FILE_SIZE']) ?
                $_SERVER['HTTP_X_FILE_SIZE'] : (isset($upload['size']) ?
                        $upload['size'] : null),
                isset($_SERVER['HTTP_X_FILE_TYPE']) ?
                $_SERVER['HTTP_X_FILE_TYPE'] : (isset($upload['type']) ?
                        $upload['type'] : null),
                isset($upload['error']) ? $upload['error'] : null
            );
        }
        header('Vary: Accept');

        $json = json_encode($info);
        $redirect = isset($_REQUEST['redirect']) ?
            stripslashes($_REQUEST['redirect']) : null;
        if ($redirect) {
            header('Location: '.sprintf($redirect, rawurlencode($json)));
            return;
        }
        if (isset($_SERVER['HTTP_ACCEPT']) &&
            (strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
            header('Content-type: application/json');
        } else {
            header('Content-type: text/plain');
        }
        echo $json;

    }

    public function deleteMedia()
    {
        $conVar = Media::con();
        $file_name = isset($_REQUEST['file']) ?
            basename(stripslashes($_REQUEST['file'])) : null;
        $file_path = $conVar['upload_dir'].$file_name;
        $success = is_file($file_path) && $file_name[0] !== '.' && unlink($file_path);
        if ($success) {
            foreach ($conVar['image_versions'] as $version => $options) {
                $file = $options['upload_dir'].$file_name;
                if (is_file($file)) {
                    unlink($file);
                }
            }
        }
        header('Content-type: application/json');
    }


    public function databaseConnectOperation($fileNameUrl, $thumbnailUrl, $opt)
    {
        if ($opt=='insert') {
            $authorName = null;
            $authorId = null;
            $image = new \KC\Entity\MediaImage();

            if ($fileNameUrl) {
                $image->path = $fileNameUrl;
                $image->name = $fileNameUrl;
                $image->ext = '';
                $image->deleted = 0;
                $image->created_at = new \DateTime('now');
                $image->updated_at = new \DateTime('now');
                \Zend_Registry::get('emLocale')->persist($image);
                \Zend_Registry::get('emLocale')->flush();
            }

            $media = new \KC\Entity\Media();
            $media->name = $fileNameUrl;
            $media->fileurl = $fileNameUrl;
            $media->mediaimage = \Zend_Registry::get('emLocale')
                ->getRepository('KC\Entity\MediaImage')
                ->find($image->id);
            $media->authorName = \Zend_Auth::getInstance()->getIdentity()->firstName;
            $media->authorId = \Zend_Auth::getInstance()->getIdentity()->id;
            $media->deleted = 0;
            $media->created_at = new \DateTime('now');
            $media->updated_at = new \DateTime('now');
            \Zend_Registry::get('emLocale')->persist($media);
            \Zend_Registry::get('emLocale')->flush();
            return $image->id;
        } elseif ($opt=='delete') {
            mysql_query("delete from imageDetails where filename ='".$fileNameUrl."'");
        }
    }

    public static function getmediaList($params)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $mediaList = $queryBuilder
            ->select('m')
            ->from("KC\Entity\Media", "m");
        return $mediaList;
    }

    public static function permanentDeleteMedia($id)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        if ($id) {
            $sel = $queryBuilder->select('m, mi')
            ->from('KC\Entity\Media', 'm')
            ->leftJoin('m.mediaimage', 'mi')
            ->where("m.id=". $id)
            ->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

            $del = $queryBuilder->delete('KC\Entity\Media', 'm')
                ->where('m.id=' . $id)
                ->getQuery()
                ->execute();

            if (!empty($sel['mediaimage']['id'])) {
                $media = \Zend_Registry::get('emLocale')->find('KC\Entity\Media', $id);
                $del1 = $queryBuilder->delete('KC\Entity\Image', 'i')
                ->where('i.id=' . $sel['mediaimage']['id'])
                ->getQuery()
                ->execute();
            }

            unlink(ROOT_PATH.'images/upload/media/'.$sel['fileUrl']);
            unlink(ROOT_PATH.'images/upload/media/thumb/'.$sel['fileUrl']);
            unlink(ROOT_PATH.'images/upload/media/thumb_L/'.$sel['fileUrl']);
        } else {
            $id = null;
        }
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_media_list');
        return $id;
    }

    public static function getMediadata($id)
    {
        if ($id) {
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $u = \Zend_Registry::get('emLocale')
                ->getRepository('KC\Entity\Media')
                ->find($id);
            $mediaList = $queryBuilder
            ->select("m")
            ->from("KC\Entity\Media", "m")
            ->leftJoin('m.mediaimage', 'mi')
            ->where('mi.id='.$id)
            ->orderBy("m.id", "DESC");
            $arr = $mediaList->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
            return $arr;
        }
    }

    public static function updateMediaRecord($params)
    {
        for ($i=0; $i<count($params['name']); $i++) {
            if ($params['name']) {
                $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
                echo $data = $queryBuilder
                ->update('KC\Entity\Media', 'm')
                ->set(
                    'm.name',
                    $queryBuilder->expr()->literal(
                        htmlentities(
                            \BackEnd_Helper_viewHelper::stripSlashesFromString($params['name'][$i])
                        )
                    )
                )
                ->set(
                    'm.alternatetext',
                    $queryBuilder->expr()->literal(
                        \BackEnd_Helper_viewHelper::stripSlashesFromString($params['alternateText'][$i])
                    )
                )
                ->set(
                    'm.caption',
                    $queryBuilder->expr()->literal(
                        \BackEnd_Helper_viewHelper::stripSlashesFromString($params['caption'][$i])
                    )
                )
                ->set(
                    'm.description',
                    $queryBuilder->expr()->literal(
                        \BackEnd_Helper_viewHelper::stripSlashesFromString($params['description'][$i])
                    )
                )
                ->where('m.mediaimage='.$params['hid'][$i]);
                $data->getQuery()->execute();
            }
        }
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_media_list');
        return $data->id=1;
    }

    public function editMediaRecord($params)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $queryBuilder
            ->update('KC\Entity\Media', 'm')
            ->set(
                'm.name',
                $queryBuilder->expr()->literal(
                    htmlentities(
                        \BackEnd_Helper_viewHelper::stripSlashesFromString($params['name'])
                    )
                )
            )
            ->set(
                'm.alternatetext',
                $queryBuilder->expr()->literal(
                    \BackEnd_Helper_viewHelper::stripSlashesFromString($params['alternateText'])
                )
            )
            ->set(
                'm.caption',
                $queryBuilder->expr()->literal(
                    \BackEnd_Helper_viewHelper::stripSlashesFromString($params['caption'])
                )
            )
            ->set(
                'm.description',
                $queryBuilder->expr()->literal(
                    \BackEnd_Helper_viewHelper::stripSlashesFromString($params['description'])
                )
            )
            ->where('m.id='.$params['id'])
            ->getQuery()
            ->execute();
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_media_list');
        return true;
    }
}
