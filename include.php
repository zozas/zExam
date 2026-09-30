<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

class ini {
	private $file = NULL;
	private $data = array();
	public function open($file) {
		$this->file = $file;
		if($file != '') {
			if(is_readable($file)) {
				$this->file = $file;
				return(TRUE);
			} else {
				return(FALSE);
			}
		} else {
			return(FALSE);
		}
	}
	public function read() {
		$this->data = parse_ini_file(realpath($this->file), TRUE);
		if ($this->data == FALSE) {
			return(FALSE);
		} else {
			return(TRUE);
		}
	}
	public function write() {
		$content = NULL;
		foreach ($this->data as $section => $data) {
			$content = $content.'['.$section.']'.PHP_EOL;
			foreach ($data as $key => $val) {
				if (is_array($val)) {
					foreach ($val as $v) {
						$content = $content.$key.'[] = '.(is_numeric($v) ? $v : '"'.$v.'"').PHP_EOL;
					}
				} elseif (empty($val)) {
					$content = $content.$key.' = '.PHP_EOL;
				} else {
					$content = $content.$key.' = '.(is_numeric($val) ? $val : '"'.$val.'"').PHP_EOL;
				}
			}
			$content = $content.PHP_EOL;
		}
		return (($handle = fopen($this->file, 'w')) && fwrite($handle, ";<?php".PHP_EOL.";die();".PHP_EOL.";/*".PHP_EOL.trim($content).PHP_EOL.";*/".PHP_EOL.";?>".PHP_EOL) && fclose($handle)) ? TRUE : FALSE;
	}
	public function exist($section, $key = NULL) {
		if ($key != NULL ) {
			if (isset($this->data[$section][$key])) {
				return(TRUE);
			} else {
				return(FALSE);
			}
		} else {
			if (isset($this->data[$section])) {
				return(TRUE);
			} else {
				return(FALSE);
			}
		}
	}
	public function get($section, $key) {
		if (isset($this->data[$section][$key])) {
			return $this->data[$section][$key];
		} else {
			return(FALSE);
		}
	}
	public function set($section, $key, $value) {
		if (isset($this->data[$section][$key])) {
			$this->data[$section][$key] = $value;
			return(TRUE);
		} else {
			$this->data[$section][$key] = $value;
			return(FALSE);
		}
	}
	public function delete($section, $key = NULL) {
		if ($key != NULL ) {
			if (isset($this->data[$section][$key])) {
				unset($this->data[$section][$key]);
				return(TRUE);
			} else {
				return(FALSE);
			}
		} else {			
			if (isset($this->data[$section])) {
				unset($this->data[$section]);
				return(TRUE);
			} else {
				return(FALSE);
			}
		}
	}
}
class session {
	public function __construct() {
		if(!isset($_SESSION)) {
			session_start();
			return TRUE;
		} else {
			return FALSE;
		}
	}
	public function erase_session() {
		//setcookie(session_name(), NULL, 0, "/");
		session_destroy();
		session_unset();
	}
	public function set($key, $value) {
		return $_SESSION[$key] = $value;
	}
	public function exist($key) {
		if(isset($_SESSION[$key])) {
			return TRUE;
		}
		return FALSE;
	}
	public function delete($key) {
		unset($_SESSION[$key]);
	}
	public function get($key) {
		if(!isset($_SESSION[$key])) {
			return FALSE;
		}
		return $_SESSION[$key];
	}
	function ip() {
		if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip_list = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
			return trim($ip_list[0]);
		}
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			return $_SERVER['HTTP_CLIENT_IP'];
		}
		return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
	}
}
class template {
	protected $file;
	protected $values = array();
	public function open($file) {
		$this->file = $file;
		if($file != '') {
			if(is_readable($file)) {
				$this->file = $file;
				return(TRUE);
			} else {
				return(FALSE);
			}
		}
	}
	public function set($key, $value) {
		$this->values[$key] = $value;
	}
	public function get() {
		if (!file_exists($this->file)) {
			die($this->file);
		}
		$output = file_get_contents($this->file);
		foreach ($this->values as $key => $value) {
			$tagToReplace = "[@$key]";
			$output = str_replace($tagToReplace, $value, $output);
		}
		return $output;
	}
}
class DatabaseGenerator {
	private $meta = [];
	private $questions = [];
	public function __construct($meta = []) {
        $this->meta = array_merge(["ADMIN" => "0001", "AUTHOR" => "UNKNOWN", "ENTRIES" => 0, "INSTRUCTIONS" => "", "LANGUAGE"     => "el", "PIN" => "0000", "QUESTIONS" => 0, "TITLE" => "UNTITLED", "VERSION" => "1.0"], $meta);
    }
	public function addQuestion($title, $answers, $correctIndex) {
		$cleanAnswers = array_map(function($a) { return is_array($a) ? json_encode($a, JSON_UNESCAPED_UNICODE) : $a; }, $answers);
        $this->questions[] = ["title" => $title, "answers" => $cleanAnswers, "correct" => $correctIndex];
    }
	public function generate() {
		$output  = ";<?php\n;die();\n;/*\n";
		$this->meta["ENTRIES"] = count($this->questions);
		$this->meta["QUESTIONS"] = count($this->questions);
		$output .= "[DATABASE]\n";
		foreach ($this->meta as $key => $value) {
			if (is_array($value)) {
				$value = json_encode($value, JSON_UNESCAPED_UNICODE);
			}
			$value = str_replace('"', '\"', $value);
			$output .= "\t{$key}\t= \"{$value}\"\n";
		}
		$i = 1;
		foreach ($this->questions as $q) {
			$output .= "[Q_{$i}]\n";
			$title = str_replace('"', '\"', $q['title']);
			$output .= "\tTITLE = \"{$title}\"\n";
			foreach ($q["answers"] as $index => $ans) {
				$num = $index + 1;
				$ans = str_replace('"', '\"', $ans);
				$output .= "\tANSWER_{$num} = \"{$ans}\"\n";
			}
			$output .= "\tCORRECT = {$q['correct']}\n";
			$i++;
		}
		$output .= "*/\n;?>";
		return $output;
	}
}
?>
