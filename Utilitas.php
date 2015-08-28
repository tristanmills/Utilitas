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

}
