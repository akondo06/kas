<?php

class FileManager {

	public static function upload($file, $destination, $filename) {
		$return = "UPLOAD_ERR_NO_FILE";
		$errors = array("UPLOAD_ERR_OK", "UPLOAD_ERR_INI_SIZE", "UPLOAD_ERR_FORM_SIZE", "UPLOAD_ERR_PARTIAL", "UPLOAD_ERR_NO_FILE", "UPLOAD_ERR_NO_TMP_DIR", "UPLOAD_ERR_CANT_WRITE", "UPLOAD_ERR_EXTENSION");
		if(is_uploaded_file($file['tmp_name'])) {
			if(array_key_exists('error', $file)) {
				$error = $file['error'];
				$return = $errors[$error];

				$allowed_types = array('jpg' => 'image/jpeg', 'png' => 'image/png', 'bmp' => 'image/bmp', 'gif' => 'image/gif', 'swf' => 'application/x-shockwave-flash');

				if(array_key_exists('tmp_name', $file) && array_key_exists('name', $file) && $error === UPLOAD_ERR_OK) {
					$finfo = new finfo(FILEINFO_MIME_TYPE);
					$file_type = array_search($finfo->file($file['tmp_name']), $allowed_types, true);

					if($file_type === false) {
						$return = "UPLOAD_ERR_TYPE_NOT_ALLOWED";
					} else {
						if(move_uploaded_file($file['tmp_name'], $destination."".$filename.".".$file_type)) {
							$return = true;
						} else {
							$return = "UPLOAD_ERR_MOVING_FILE";
						}
					}
				}
			}
		}
		return $return;
	}

	public static function image_convert($file, $tofile, $extension='jpg', $in_extension=null) {
		$allowed_types = array(
			'jpg' => array('type' => 'image/jpeg', 'create' => 'imagecreatefromjpeg', 'save' => 'imagejpeg'),
			'png' => array('type' => 'image/png', 'create' => 'imagecreatefrompng', 'save' => 'imagepng'),
			'bmp' => array('type' => 'image/bmp', 'create' => 'imagecreatefrombmp', 'save' => 'imagewbmp'),
			'gif' => array('type' => 'image/gif', 'create' => 'imagecreatefromgif', 'save' => 'imagegif')
		);

		$file_type = null;

		if(class_exists('finfo')) {
			$finfo = new finfo(FILEINFO_MIME_TYPE);
			$mime_type = $finfo->file($file);
		} else {
			$ext = $in_extension;
			if($ext === 'jpg') {
				$ext = 'jpeg';
			}
			$mime_type = 'image/'.$ext;
		}

		foreach($allowed_types as $key => $value) {
			if($value['type'] === $mime_type) {
				$file_type = $allowed_types[$key];
				break;
			}
		}

		if($file_type === null) {
			return false;
		}

		$source_image = call_user_func($file_type['create'], $file);

		if($source_image === null || $source_image === false) {
			return false;
		}

		$source_image_size = getimagesize($file);
		$destination_image = imagecreatetruecolor($source_image_size[0], $source_image_size[1]);
		ImageCopyResampled($destination_image, $source_image, 0, 0, 0, 0, $source_image_size[0], $source_image_size[1], $source_image_size[0], $source_image_size[1]);

		if(!array_key_exists($extension, $allowed_types)) {
			imagedestroy($source_image);
			imagedestroy($destination_image);
			return false;
		}

		$save = $allowed_types[$extension];
		@call_user_func_array($save['save'], array($destination_image, $tofile.".".$extension, 95));

		imagedestroy($source_image);
		imagedestroy($destination_image);

		return true;
	}
	
	public static function download($url=null) {
		$return = null;
		if($url != null && is_string($url) && filter_var($url, FILTER_VALIDATE_URL)) {
			$return = array();
			
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			curl_setopt($curl, CURLOPT_FAILONERROR, true);
			
			$data = curl_exec($curl);
			
			$return = curl_getinfo($curl);
			curl_close($curl);
			
			// If the specified url does not respond to the request .. curl_getinfo returns false.
			if($return == false) {
				//exit("Could not connect to the URL: ".$url.". cURL error:".curl_error($curl).". Response Code: ".$return['http_code'].". ".$return['content']);
				$return = null;
			} else {
				if(isset($return['http_code']) && $return['http_code'] == "200") {
					$return['content'] = $data;
				} else {
					$return = null;
				}
			}
		}

		return $return;
	}

	public static function delete($uri) {
		if(is_file($uri)) {
			return unlink($uri);
		}
		return false;
	}

	public static function copy($file, $tofile) {
		$current = @file_get_contents($file);
		if($current !== false) {
			return @file_put_contents($tofile, $current);
		}
		return false;
	}
	
	public static function rename($name, $newname) {
		if(is_file($name) && !is_file($newname)) {
			return rename($name, $newname);
		}
		return false;
	}

	public static function size($file, $setup = null) {
		$FZ = ($file && @is_file($file)) ? filesize($file) : NULL;
		$FS = array("B","kB","MB","GB","TB","PB","EB","ZB","YB");
		if(!$setup && $setup !== 0) {
			return number_format($FZ/pow(1024, $I=floor(log($FZ, 1024))), ($i >= 1) ? 2 : 0).' '.$FS[$I];
		} elseif ($setup == 'INT') {
			return number_format($FZ);
		} else {
			return number_format($FZ/pow(1024, $setup), ($setup >= 1) ? 2 : 0 ).' '.$FS[$setup];
		}
	}
}

?>