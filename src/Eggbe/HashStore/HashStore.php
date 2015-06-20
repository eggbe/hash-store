<?php
namespace Eggbe\HashStore;

use \Eggbe\Helpers\Hash;

class HashStore {

	/**
	 * @const int
	 */
	const BY_DATE = 0b0001;

	/**
	 * @const int
	 */
	const BY_WORD = 0b0010;

	/**
	 * @const int
	 */
	const BY_DESC = 0b1000;

	/**
	 * @var string
	 */
	private $path = null;

	/**
	 * @var array
	 */
	private $Hashes = [];

	/**
	 * @param string $path
	 * @param int $flags
	 * @throws \Exception
	 */
	public final function __construct($path, $flags = self::BY_DATE){
		if (!file_exists($path) || !is_dir($path) || !is_writable($path)){
			throw new \Exception('Storage path "' . $this->path . '" is not exists or not writable!');
		}
		$this->path = $path;

		$Hashes = [];
		foreach(glob($this->path . DIRECTORY_SEPARATOR . '*') as $file){
			$Data = preg_split('/;+/', file_get_contents($file), -1, PREG_SPLIT_NO_EMPTY);
			if (count($Data) < 2){
				throw new \Exception('Invalid has format [1]!');
			}
			if (!is_numeric($Data[0])){
				throw new \Exception('Invalid has format [2]!');
			}
			if (!preg_match('/^[a-z0-9]{32}$/', $Data[1])){
				throw new \Exception('Invalid has format [3]!');
			}
			$Hashes[$Data[0]][basename($file)] = trim($Data[1]);
		}

		if ($flags & self::BY_DATE){
			ksort($Hashes, SORT_NUMERIC);
			if ($flags & self::BY_DESC){
				$Hashes = array_reverse($Hashes, true);
			}
		}

		foreach($Hashes as $Hash){
			foreach($Hash as $key => $value) {
				$this->Hashes[$key] = $value;
			}
		}

		if ($flags & self::BY_WORD){
			ksort($this->Hashes, SORT_STRING | SORT_FLAG_CASE);
			if ($flags & self::BY_DESC){
				$this->Hashes = array_reverse($this->Hashes);
			}
		}

	}

	/**
	 * @return array
	 */
	public final function all() {
		return $this->Hashes;
	}

	/**
	 * @param string $key
	 * @throws \Exception
	 */
	public final function create($key){
		$file = $this->path . DIRECTORY_SEPARATOR . preg_replace('/:+/', '_', strtolower($key));
		if (file_exists($file)){
			throw new \Exception('Hash "' . $key . '" already exists!');
		}
		file_put_contents($file, time() . ';' . md5($key . Hash::solt(10)));
	}

	/**
	 * @param string $key
	 * @throws \Exception
	 */
	public final function remove($key){
		if (!array_key_exists(($key = strtolower($key)), $this->Hashes)){
			throw new \Exception('Unknown hash key "' . $key . '"!');
		}
		unlink($this->path . DIRECTORY_SEPARATOR . $key);
		unset($this->Hashes[$key]);
	}

}

