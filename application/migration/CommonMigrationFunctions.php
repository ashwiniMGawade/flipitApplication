<?php
class CommonMigrationFunctions {
	
	/**
	 * copyDirectory
	 *
	 * @param Source directory
	 *
	 * @param Destination directory
	 */
	
	public static function copyDirectory($source,$destination)
	{
		if(!is_dir($destination)){
			$oldumask = umask(0);
			mkdir($destination, 01777); // so you get the sticky bit set
			umask($oldumask);
		}
		$dir_handle = @opendir($source) or die("Unable to open");
		while ($file = readdir($dir_handle))
		{
			if($file!="." && $file!=".." && !is_dir("$source/$file")) //if it is file
				copy("$source/$file","$destination/$file");
			if($file!="." && $file!=".." && is_dir("$source/$file")) //if it is folder
				self::copyDirectory("$source/$file","$destination/$file");
		}
		closedir($dir_handle);
	}
	
	/**
	 * deleteDirectory
	 *
	 * @param directory path
	 *
	 */
	
	public static function deleteDirectory($directoryPath) {
		if (! is_dir($directoryPath)) {
			throw new InvalidArgumentException("$directoryPath must be a directory");
		}
		if (substr($directoryPath, strlen($directoryPath) - 1, 1) != '/') {
			$directoryPath .= '/';
		}
		$files = glob($directoryPath . '*', GLOB_MARK);
		foreach ($files as $file) {
			if (is_dir($file)) {
				self::deleteDirectory($file);
			} else {
				unlink($file);
			}
		}
		rmdir($directoryPath);
	}
	
}

?>