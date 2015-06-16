<?php 
/**
* This service class is used for image operations within the application. The
* main function is to generate square avatars from an abritary sized image.
*
*/
class BackEnd_Helper_resize
{
	public static function resizeImage($file,$orFileName)
	{
		//print_r($file);
		//die();
		$image =$file["name"];
		$uploadedfile = $file['tmp_name'];
		if ($image)
		{
	
			 $filename = $file['name'];
			//print_r($filename);
			//die();
			$extension = self::getImageExtension($filename);
			$extension = strtolower($extension);
			if (($extension != "jpg") && ($extension != "jpeg") && ($extension != "png") && ($extension != "gif"))
			{
	
				echo "Error 1";
			}
			else
			{
	
				$size=filesize($file['tmp_name']);
				if ($size > 400*1024)
				{
					echo 'Max size';
				}
	
	
				if($extension=="jpg" || $extension=="jpeg" )
				{
					$uploadedfile = $file['tmp_name'];
					$src = imagecreatefromjpeg($uploadedfile);
	
				}
				else if($extension=="png")
				{
					$uploadedfile = $file['tmp_name'];
					$src = imagecreatefrompng($uploadedfile);
	
				}
				else
				{
					$src = imagecreatefromgif($uploadedfile);
				}
				list($width,$height)=getimagesize($uploadedfile);
	
	
				$newwidth=60;
				$newheight=($height/$width)*$newwidth;
				$tmp=imagecreatetruecolor($newwidth,$newheight);
	
				$newwidth1=25;
				$newheight1=($height/$width)*$newwidth1;
				$tmp1=imagecreatetruecolor($newwidth1,$newheight1);
	
				imagecopyresampled($tmp,$src,0,0,0,0,$newwidth,$newheight,$width,$height);
	
				imagecopyresampled($tmp1,$src,0,0,0,0,$newwidth1,$newheight1,$width,$height);
				$filename = ROOT_PATH . "images/upload/". $orFileName ;
	
				$filename1 = ROOT_PATH . "images/upload/thum_".$orFileName;
	
	
	
				imagejpeg($tmp,$filename,100);
	
				imagejpeg($tmp1,$filename1,100);
	
				imagedestroy($src);
				imagedestroy($tmp);
				imagedestroy($tmp1);
			}
		}
	
	}
	}