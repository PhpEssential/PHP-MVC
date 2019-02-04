<?php

namespace framework\utils;

use framework\Config;

class FileUtils {
	public static function checkFile($postedFile, $allowedExt){
		
		try {
			// Undefined | Multiple Files | $_FILES Corruption Attack
			// If this request falls under any of them, treat it invalid.
			if (!isset($postedFile['error']) || is_array($postedFile['error'])) {
				throw new \RuntimeException('Invalid parameters.');
			}
			
			// Check $_FILES['upfile']['error'] value.
			switch ($postedFile['error']) {
				case UPLOAD_ERR_OK:
					break;
				case UPLOAD_ERR_NO_FILE:
					throw new \RuntimeException('No file sent.');
				case UPLOAD_ERR_INI_SIZE:
				case UPLOAD_ERR_FORM_SIZE:
					throw new \RuntimeException('Exceeded filesize limit.');
				default:
					throw new \RuntimeException('Unknown errors.');
			}
			
			// You should also check filesize here.
			if ($postedFile['size'] > 1000000) {
				throw new \RuntimeException('Exceeded filesize limit.');
			}
			
			$extAllowed = array(
					'jpg' => 'image/jpeg',
					'png' => 'image/png',
					'gif' => 'image/gif',
			);
			
			// DO NOT TRUST $_FILES['upfile']['mime'] VALUE !!
			// Check MIME Type by yourself.
			$finfo = new \finfo(FILEINFO_MIME_TYPE);
			if (false === array_search(
					$finfo->file($postedFile['tmp_name']),
					$extAllowed,
					true
			)) {
				throw new \RuntimeException('Invalid file format.');
			}
		} catch(\Exception $e) {
			throw $e;
		}
	}
	
	public static function uploadFile($postedFile){
		try {
			$extAllowed = array(
					'jpg' => 'image/jpeg',
					'png' => 'image/png',
					'gif' => 'image/gif',
			);
			
			// DO NOT TRUST $_FILES['upfile']['mime'] VALUE !!
			// Check MIME Type by yourself.
			$finfo = new \finfo(FILEINFO_MIME_TYPE);
			if (false === $ext = array_search(
				$finfo->file($postedFile['tmp_name']),
				$extAllowed,
				true
			)) {
				throw new \RuntimeException('Invalid file format.');
			}
			
			// You should name it uniquely.
			// DO NOT USE $_FILES['upfile']['name'] WITHOUT ANY VALIDATION !!
			// On this example, obtain safe unique name from its binary data.
			$fileName = sha1_file($postedFile['tmp_name']);
			if (!move_uploaded_file(
					$postedFile['tmp_name'],
					sprintf(self::getRootPath() . "images/uploads/%s.%s", $fileName, $ext)
			)) {
				throw new \RuntimeException('Failed to move uploaded file.');
			}
		} catch(\Exception $e) {
			throw $e;
		}
	}
	
	public static function getRootPath(){
		return substr($_SERVER['DOCUMENT_ROOT'], 0, -1) . Config::get(Config::ROOT_PATH);
	}
}

