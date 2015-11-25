<?php

namespace Utilitas;

class Utilitas {

	/**
	 * Encodes a filename to be safe on all file systems.
	 *
	 * @param string $filename The string to be encoded.
	 * @return string An encoded string.
	 */
	public static function fileEncode($filename) {

		$restricted_list = array('\\', '|', '/', ':', '?', '"', '*', '<', '>');

		$replacement_list = array('_', '_', '_', '_', '_', '_', '_', '_', '_');

		return str_replace($restricted_list, $replacement_list, $filename);

	}

	/**
	 * Deletes filename and any contents. A E_USER_WARNING level error will be generated on failure.
	 *
	 * @param string $filename Path to the file or directory.
	 * @return bool Returns TRUE on success or FALSE on failure.
	 */
	public static function rmtree($filename) {

		if (file_exists($filename) === false) {

			trigger_error('rmtree(' . $filename . '): No such file or directory', E_USER_WARNING);

			return false;

		}

		if (is_dir($filename)) {

			$objects = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($filename, \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::CHILD_FIRST);

			foreach ($objects as $object) {

				if ($object->isDir()) {

					rmdir($object->getPathname());

				} else {

					unlink($object->getPathname());

				}
			}

			return rmdir($filename);

		} elseif (is_file($filename)) {

			return unlink($filename);

		} else {

			trigger_error('rmtree(' . $filename . '): Invalid argument', E_USER_WARNING);

			return false;

		}

	}

	/**
	 * .
	 *
	 * @param string $source Path to the file or directory to be zipped.
	 * @param string $destination Path to the zip being created.
	 * @return bool Returns TRUE on success or FALSE on failure.
	 */
	public static function zip($source, $destination) {

		$destination_directory = dirname($destination);

		if (is_dir($destination_directory) === false) {

			mkdir($destination_directory);

		}

		if (is_dir($source)) {

			$ZipArchive = new \ZipArchive;

			$ZipArchive->open($destination, \ZipArchive::CREATE);

			$objects = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($source, \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::CHILD_FIRST);

			foreach ($objects as $object) {

				$filename = $object->getPathname();

				$localname = str_replace($source, '', $filename);

				if ($object->isDir()) {

					$ZipArchive->addEmptyDir($localname);

				} else {

					$ZipArchive->addFile($filename, $localname);

				}

			}

			return $ZipArchive->close();

		} elseif (is_file($source)) {

			$ZipArchive = new \ZipArchive;

			$ZipArchive->open($destination, \ZipArchive::CREATE);

			$ZipArchive->addFile($source);

			return $ZipArchive->close();

		} else {

			trigger_error('zip(' . $source . ', ' . $destination . '): Source file or directory does not exist', E_USER_WARNING);

			return false;

		}

	}

	/**
	 * Encrypts a message.
	 *
	 * @param string $message The message to be encrypted.
	 * @param string $password The password to encrypt with.
	 * @return string Returns a url encoded encrypted string.
	 */
	public static function encrypt($message, $password) {

		$iv_size = openssl_cipher_iv_length('AES-128-CBC');

		$iv = openssl_random_pseudo_bytes($iv_size);

		$encrypted_message = openssl_encrypt($message, 'AES-128-CBC', $password, OPENSSL_RAW_DATA, $iv);

		$encrypted_message = $iv . $encrypted_message;

		$encoded_encrypted_message = rawurlencode(base64_encode($encrypted_message));

		return $encoded_encrypted_message;

	}

	/**
	 * Decrypts a message.
	 *
	 * @param string $encoded_encrypted_message The message to be decrypted.
	 * @param string $password The password to decrypt with.
	 * @return mixed Returns a plain text message on success or FALSE on failure.
	 */
	public static function decrypt($encoded_encrypted_message, $password) {

		$encrypted_message = base64_decode(rawurldecode($encoded_encrypted_message));

		$iv_size = openssl_cipher_iv_length('AES-128-CBC');

		$iv = substr($encrypted_message, 0, $iv_size);

		$message = openssl_decrypt(substr($encrypted_message, $iv_size), 'AES-128-CBC', $password, OPENSSL_RAW_DATA, $iv);

		return $message;

	}
	
}
