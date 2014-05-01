<?php

class Utils {

	//modificata da http://www.dyn-web.com/code/random_image/random_img_php.php
	public static function getFilesFromDir($directory, $extensions, $nToShow = '', $shuffleArray = '') {
		if ($nToShow != '')
			$nToShow = (int) $nToShow;
		if ($shuffleArray != '')
			$shuffleArray = (int) $shuffleArray;
		$arrayExtensions = explode(',', $extensions);
		$pregExtensions = "/(\.";
		for ($i = 0; $i < count($arrayExtensions); $i++) {
			$pregExtensions .= $arrayExtensions[$i];
			if ($i < (count($arrayExtensions) - 1)) {
				$pregExtensions .= "|\\.";
			}
		}
		$pregExtensions .= ")$/";
		$files = array();
		$filesToShow = array();

		$dir = @opendir($directory);
		if ($dir) {
			while (false !== ($file = readdir($dir))) {
				if (preg_match($pregExtensions, $file)) {
					$files[] = $file;
				}
			}
			closedir($dir);
		}

		if ($shuffleArray == 1) {
			shuffle($files);
		}

		$filesToShow = $files;
		if (is_int($nToShow) && $nToShow <= count($files)) {
			$filesToShow = array();

			for ($i = 0; $i < $nToShow; $i++) {
				$filesToShow[$i] = $files[$i];
			}
		}

		return $filesToShow;
	}

	public static function translate($word) {
		global $session;
		$lang = $session->getCurrentLang();
		$result = SysDictionaryTable::getInstance()->createQuery('d')
				->leftJoin('d.Language l')
				->where('d.name = ?', $word)
				->andWhere('l.code = ?', $lang)
				->fetchOne(null, Doctrine::HYDRATE_ARRAY);
		return $result ? $result['value'] : null;
	}


	public static function autoload($className) {
		$fileName = null;

		$fileName = dirname(__FILE__) . "/../lib/$className.class.php";
		if (!is_readable($fileName)) {
			$fileName = dirname(__FILE__) . "/../lib/class.$className.php";
			if (!is_readable($fileName))
				$fileName = null;
		}

		if (empty($fileName)) {
			$fileName = dirname(__FILE__) . "/../functions/$className.class.php";
			if (!is_readable($fileName)) {
				$fileName = dirname(__FILE__) . "/../functions/class.$className.php";
				if (!is_readable($fileName))
					$fileName = null;
			}
		}

		if (!empty($fileName) && is_readable($fileName))
			require_once $fileName;
	}

	public static function getRandomString($length = 8) {
		$charset = "abcdefghijklmnopqrstuvwxyz1234567890ABCDEFGHILMNOPQRSTUVZ";
		$password = "";
		for ($i = 0; $i < $length; $i++) {
			$password .= substr($charset, rand(0, strlen($charset) - 1), 1);
		}
		return $password;
	}

	public static function str_rot47($str) {
		return strtr($str,
		'!"#$%&\'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\]^_`abcdefghijklmnopqrstuvwxyz{|}~',
		'PQRSTUVWXYZ[\]^_`abcdefghijklmnopqrstuvwxyz{|}~!"#$%&\'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNO');
	}

	public static function isEmail($email) {
		$regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/';
		return preg_match($regex, $email);
	}

	public static function ArrayKeyIs($array, $key, $value) {
		if (!isset($array[$key])) return false;
		return ($array[$key] == $value);
	}

	public static function sendmail($from, $to, $subject, $bodyhtml, $bodytxt = "", $allegati = array(), $cc = array(), $bcc = array()) {
		//Genero i boundary
		$boundary1 = "XXMAILXX" . md5(time()) . "XXMAILXX";
		$boundary2 = "YYMAILYY" . md5(time()) . "YYMAILYY";

		//Se i corpi in formato txt o html sono vuoti, li inizializzo con il contenuto dell'altro formato
		if ($bodytxt == "" && $bodyhtml != "") {
			$bodytxt = str_replace("<br>", "\n", $bodyhtml);
			$bodytxt = strip_tags($bodyhtml);
		}
		if ($bodytxt != "" && $bodyhtml == "") {
			$bodyhtml = $bodytxt;
		}

		//Mittente
		if (is_array($from))
			$headers = "From: \"{$from['name']}\" <{$from['email']}>" . "\n";
		else
			$headers = "From: \"$from\" <$from>" . "\n";

		//Destinatari in copia
		if (count($cc)) {
			$list = implode(',', $cc);
			$headers .= "CC: " . $list . "\n";
		}
		//Destinatari in copia nascosta
		if (count($bcc)) {
			$list = implode(',', $bcc);
			$headers .= "BCC: " . $list . "\n";
		}

		//MIME e Content Type
		$headers .= "MIME-Version: 1.0\n";
		if (count($allegati)) {
			$headers .= "Content-Type: multipart/mixed;\n";
			$headers .= " boundary=\"$boundary1\";\n\n";
			$headers .= "--$boundary1\n";
		}
		$headers .= "Content-Type: multipart/alternative;\n";
		$headers .= " boundary=\"$boundary2\";\n\n";

		//mail solo testo
		$body = "--$boundary2\n";
		$body .= "Content-Type: text/plain; charset=ISO-8859-15; format=flowed\n";
		$body .= "Content-Transfer-Encoding: 7bit\n\n";
		$body .= "$bodytxt\n";
		//mail html
		$body .= "--$boundary2\n";
		$body .= "Content-Type: text/html; charset=ISO-8859-15\n";
		$body .= "Content-Transfer-Encoding: 7bit\n\n";
		$body .= "$bodyhtml\n\n";
		$body .= "--$boundary2--\n";
		//allegati
		foreach ($allegati as $allegato) {
			if (is_array($allegato)) {
				if (isset($allegato['path'])) {
					$fp = @fopen($allegato['path'], "r");
					if ($fp)
						$data = fread($fp, filesize($allegato['path']));
					$curr = chunk_split(base64_encode($data));
					$body .= "--$boundary1\n";
					$body .= "Content-Type: application/octet-stream;";
					$body .= "name=\"" . $allegato['name'] . "\"\n";
					$body .= "Content-Disposition: attachment\n";
					$body .= "Content-Transfer-Encoding: base64\n\n";
					$body .= "$curr\n";
				} elseif (isset($allegato['content'])) {
					$curr = chunk_split(base64_encode($allegato['content']));
					$body .= "--$boundary1\n";
					$body .= "Content-Type: application/octet-stream;";
					$body .= " name=\"" . $allegato['name'] . "\"\n";
					$body .= "Content-Disposition: attachment\n";
					$body .= "Content-Transfer-Encoding: base64\n\n";
					$body .= "$curr\n";
				}
			} else {
				$info_allegato = pathinfo($allegato);
				$fp = @fopen($allegato, "r");
				if ($fp)
					$data = fread($fp, filesize($allegato));
				$curr = chunk_split(base64_encode($data));
				$body .= "--$boundary1\n";
				$body .= "Content-Type: application/octet-stream;";
				$body .= " name=\"" . $info_allegato['basename'] . "\"\n";
				$body .= "Content-Disposition: attachment\n";
				$body .= "Content-Transfer-Encoding: base64\n\n";
				$body .= "$curr\n";
			}
		}
		if (count($allegati))
			$body .= "--$boundary1--\n";

		if (@mail($to, $subject, $body, $headers)) {
			return true;
		} else {
			return false;
		}
	}

}

?>