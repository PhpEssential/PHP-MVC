<?php
namespace framework\utils;

class FileUtils {

	public static function checkFile($postedFile, array $allowedExt, int $sizeLimit) {
		// Undefined | Multiple Files | $_FILES Corruption Attack
		// If this request falls under any of them, treat it invalid.
		if (! isset($postedFile ['error']) || is_array($postedFile ['error'])) {
			throw new \RuntimeException('Invalid parameters.');
		}

		// Check errors.
		switch ($postedFile ['error']) {
			case UPLOAD_ERR_OK :
				break;
			case UPLOAD_ERR_NO_FILE :
				throw new \RuntimeException('No file sent.');
			case UPLOAD_ERR_INI_SIZE :
			case UPLOAD_ERR_FORM_SIZE :
				throw new \RuntimeException('Exceeded filesize limit.');
			default :
				throw new \RuntimeException('Unknown errors.');
		}

		// Check filesize.
		if ($postedFile ['size'] > $sizeLimit) {
			throw new \RuntimeException('Exceeded filesize limit.');
		}

		// Check MIME Type.
		$finfo = new \finfo(FILEINFO_MIME_TYPE);
		if (false === array_search($finfo->file($postedFile ['tmp_name']), $allowedExt, true)) {
			throw new \RuntimeException('Invalid file format.');
		}
	}

	/**
	 *
	 * @param string $fileName
	 *        	- Name of file to delete
	 *
	 * @throws \RuntimeException
	 * @throws \Exception
	 */
	public static function removeUploadedFile(string $fileName) {
		$filePath = sprintf(APPLICATION_ROOT . "images" . DIRECTORY_SEPARATOR . "uploads" . DIRECTORY_SEPARATOR . "%s", $fileName);
		if (! file_exists($filePath)) {
			LogUtils::warning("File not found: " . $filePath);
		} else {
			if (! unlink($filePath)) {
				throw new \RuntimeException('Failed to remove file.');
			}
		}
	}

	/**
	 *
	 * @param mixed $postedFile
	 *        	- File receive into $_FILES
	 *
	 * @throws \RuntimeException
	 * @throws \Exception
	 *
	 * @return string - File name
	 */
	public static function uploadFile($postedFile): string {
		try {
			// Generate token for file name
			$fileName = SecurityUtils::generateToken(64);
			$postedFileName = $postedFile ['name'];
			$ext = strtolower(pathinfo($postedFileName, PATHINFO_EXTENSION));
			if (! move_uploaded_file($postedFile ['tmp_name'], sprintf(APPLICATION_ROOT . "images" . DIRECTORY_SEPARATOR . "uploads" . DIRECTORY_SEPARATOR . "%s.%s", $fileName, $ext))) {
				throw new \RuntimeException('Failed to move uploaded file.');
			}
			return $fileName . "." . $ext;
		} catch ( \Exception $e ) {
			throw $e;
		}
	}
}