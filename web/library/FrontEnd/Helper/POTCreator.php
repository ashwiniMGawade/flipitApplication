<?php
/**
 * 这是一个用于生成 GETTEXT 的 POT 文件的类 - This class can help you to create a POT file for GETTEXT
 * @author	Hpyer <coolhpy@163.com>
 * @version	1.0
 * @link	http://hpyer.cn/codes/potcreator
 * @license	MIT License <http://www.opensource.org/licenses/mit-license.php>
 */
class FrontEnd_Helper_POTCreator {
	/**
	 * 要搜索的根目录 - The root directory that will be searched
	 * @access	private
	 * @var		string
	 */
	private $root;
	
	/**
	 * 允许的扩展名，多个扩展名用管道符 | 分割 - Allowed extensions, split multi extensions with |
	 * @access	private
	 * @var		string
	 */
	private $exts;
	
	/**
	 * 是否读取子目录 - Whether read sub directories
	 * @access	private
	 * @var		boolean
	 */
	private $read_subdir;
	
	/**
	 * 语音包的位置到你所设置的根目录的相对路径 - Relative path from language file to the root path you set
	 * @access	private
	 * @var		string
	 */
	private $base_path;
	
	/**
	 * 提取消息的正则表达式 - The regular expression for extract messages
	 * @access	private
	 * @var		string
	 */
	private $regular;
	
	/**
	 * 初始化各种参数 - Reset all parameter
	 * @access	public
	 * @var		string
	 */
	public function __construct() {
		$this->root = array();
		$this->exts = 'php';
		$this->base_path = '';
		$this->read_subdir = true;
		$this->regular = "/_[_|e]\([\"|\']([^\"|\']+)[\"|\']\)/i";
	}
	
	/**
	 * 设置 root - SETTER for root
	 * @access	public
	 * @param	string	$root	允许的扩展名，多个扩展名用管道符 | 分割 - Allowed extensions, split multi extensions with |
	 * @return	void
	 */
	public function set_root($root) {
		
		foreach ($root as $key => $r){
			
			if (file_exists($r)) $this->root[$key] = str_replace('\\', '/', $r);
		}
	}
	
	/**
	 * 设置 exts - SETTER for exts
	 * @access	public
	 * @param	string	$exts	允许的扩展名，多个扩展名用管道符 | 分割 - Allowed extensions, split multi extensions with |
	 * @return	void
	 */
	public function set_exts($exts) {
		if (is_string($exts)) $this->exts = $exts;
	}
	
	/**
	 * 设置 regular - SETTER for regular
	 * @access	public
	 * @param	string	$regular	提取消息的正则表达式 - The regular expression for extract messages
	 * @return	void
	 */
	public function set_regular($regular) {
		if (is_string($regular)) $this->regular = $regular;
	}
	
	public function make_utf8($string) {
		// From http://w3.org/International/questions/qa-forms-utf-8.html
		$result = preg_match_all('%(?:
				[\x09\x0A\x0D\x20-\x7E] # ASCII
				| [\xC2-\xDF][\x80-\xBF] # non-overlong 2-byte
				| \xE0[\xA0-\xBF][\x80-\xBF] # excluding overlongs
				| [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} # straight 3-byte
				| \xED[\x80-\x9F][\x80-\xBF] # excluding surrogates
				| \xF0[\x90-\xBF][\x80-\xBF]{2} # planes 1-3
				| [\xF1-\xF3][\x80-\xBF]{3} # planes 4-15
				| \xF4[\x80-\x8F][\x80-\xBF]{2} # plane 16
		)*%xs', $string, $matches);
		return join($matches[0]);
	}
	
	
	/**
	 * 设置 base_path - SETTER for base_path
	 * @access	public
	 * @param	string	$base_path	语音包的位置到你所设置的根目录的相对路径 - Relative path from language file to the root path you set
	 * @return	void
	 */
	public function set_base_path($base_path) {
		if (is_string($base_path)) $this->base_path = $base_path;
	}
	
	/**
	 * 设置 read_subdir - SETTER for read_subdir
	 * @access	public
	 * @param	boolean	$read_subdir	设置为 true 表示读取子目录 - Set true means it will read sub directories
	 * @return	void
	 */
	public function set_read_subdir($read_subdir) {
		$this->read_subdir = $read_subdir ? true : false;
	}
	
	/**
	 * 提取消息并按POT文件格式写入文件 - Read messages and write into file
	 * @access	public
	 * @param	string	$filename	文件名 - Filename
	 * @return	void
	 */
	public function write_pot($filename) {

		file_put_contents($filename, $this->get_pot());
	}
	
	/**
	 * 提取消息并按POT文件格式输出内容 - Read messages and output the content like POT file
	 * @access	public
	 * @return	string
	 */
	public function get_pot() {
		foreach ($this->root as $path){
			$files[] = $this->read_dir($path);
		}
		
		$msgs = $this->file2msg($files);
		//echo "<pre>"; print_r($msgs); die;
		return $this->msg2pot($msgs);
	}
	
	/**
	 * 消息转成POT文件 - Make messages to POT file
	 * @access	private
	 * @param	array	$msgs	消息列表 - Message list
	 * @return	string
	 */
	private function msg2pot($msgs) {
		$pot = 'msgid ""
		msgstr ""
		"Project-Id-Version: kortingscode.nl \n"
		"POT-Creation-Date: ' . date('Y-m-d H:iO') . '\n"
		"PO-Revision-Date: ' . date('Y-m-d H:iO') . '\n"
		"Last-Translator: FULL NAME <EMAIL@ADDRESS>\n"
		"Language-Team: LANGUAGE <LL@li.org>\n"
		"Language: Nederlands (NL_nl)\n"
		"MIME-Version: 1.0\n"
		"Content-Type: text/plain; charset=UTF-8\n"
		"Content-Transfer-Encoding: 8bit\n"
		"X-Poedit-Basepath: ' . $this->base_path . '\n"
		';
		//echo "<pre>"; print_r($msgs); die;
		foreach ($msgs as $msgid => $files) {
			$pot .= "\n";
			
			foreach ($files as $file) {
				$pot .= $file . "\n";
			}
			if (preg_match('/[\"|\']([^"]+)[\"|\']/', $msgid, $m)) {
				
				if(!stristr($pot, 'msgid "' . self::make_utf8($m[1]) . '"')){
					$pot .= 'msgid "' . self::make_utf8($m[1]) . "\"\n";
					$pot .= "msgstr \"\"\n";
				}
			} 
			
		}
		//echo "<pre>"; print_r($pot);
		//die();
		return $pot;
	}
	
	/**
	 * 提取文件中的消息 - Extract messages from files
	 * @access	private
	 * @param	array	$files	文件列表 - File list
	 * @return	array
	 */
	private function file2msg($files) {
		$msgs = array();
		$fileline = '';
		$msgid = '';
		//echo "<pre>"; print_r($files); die;
		foreach ($files as $key => $fileArray) {
			
			foreach ($fileArray as $file){
				
				$lines = file($this->root[$key] . '/' . $file);
				
				foreach ($lines as $line_num => $line) {
					$fileline = "#: $file:" . ($line_num + 1);
					$matches = array();
					if (preg_match_all($this->regular, $line, $matches)) {
						$matches = array_reverse($matches);
						for ($i=0,$c=count($matches[1]); $i<$c; $i++) {
							$msgid = $matches[1][$i];
							
							if (!array_key_exists($msgid, $msgs) || !in_array($fileline, $msgs[$msgid])) $msgs[$msgid][] = $fileline;
						}
					}
				}
			}
			
		}
		
		return $msgs;
	}
	
	/**
	 * 根据允许的扩展名搜索到的文件 - Get files with allowed extensions
	 * @access	private
	 * @param	string	$root	读取的目录 - Directory to read
	 * @param	string	$parent	上级目录，保留默认值 - Parent directory, keep default value
	 * @return	array
	 */
	private function read_dir($root, $parent='.') {
		
		
		$files = array();
		$abs_file = '';
		$rel_file = '';
				
			$dh = opendir($root);
			if ($dh) {
				
				while (($file = readdir($dh)) !== false) {
					 
					if ($file == '.' || $file == '..') continue;
					$abs_file = $root . '/' . $file;
					$rel_file = $parent . '/' . $file;
					
					//echo $rel_file;
					
					if (is_file($abs_file) && preg_match('/.+\.(' . $this->exts . ')$/i', $file)) {
						$files[] = ltrim($rel_file, './');
					} else {
						// 如果读取子目录 - If read sub directories
						if (is_dir($abs_file) && $this->read_subdir) {
							$files = array_merge($files, $this->read_dir($abs_file, $rel_file));
							
						}
					}
					
				}
				
				closedir($dh);
			}
		
		
		return $files;
	}
}

?>