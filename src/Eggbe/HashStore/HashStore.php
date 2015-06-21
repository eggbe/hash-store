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
	public function __construct($path, $flags = self::BY_DATE){
		if (!file_exists($path) || !is_dir($path) || !is_writable($path)){
			throw new \Exception('Storage path "' . $this->path . '" is not exists or not writable!');
		}
		$this->path = $path;

		$Hashes = [];
		foreach(scandir($this->path, SCANDIR_SORT_NONE) as $file){
			if (preg_match('/^\./', $file)){
				continue;
			}
			$file = $this->path . DIRECTORY_SEPARATOR . $file;
			if (!is_file($file)){
				continue;
			}
			$Data = preg_split('/;+/', file_get_contents($file), -1, PREG_SPLIT_NO_EMPTY);
			if (count($Data) < 2) {
				throw new \Exception('Invalid hash format [1] found in "' . $file . '"!');
			}
			if (!is_numeric($Data[0])) {
				throw new \Exception('Invalid hash format [2] found in "' . $file . '"!');
			}
			if (!preg_match('/^[a-z0-9]{32}$/', $Data[1])) {
				throw new \Exception('Invalid hash format [3] found in "' . $file . '"!');
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
	 * It try to find value by key.
	 * @param string $key
	 * @return string|false
	 */
	public function find($key){
		return array_key_exists(($key = strtolower(trim($key))), $this->Hashes)
			? $this->Hashes[$key] : false;
	}

	/**
	 * It try to search value by hash.
	 * @param string $hash
	 * @return string|false
	 */
	public function search($hash){
		return array_search($hash, $this->Hashes, true);
	}

	/**
	 * @return array
	 */
	public function all() {
		return $this->Hashes;
	}

	/**
	 * @param string $key
	 * @return string
	 * @throws \Exception
	 */
	public function create($key){
		$file = $this->path . DIRECTORY_SEPARATOR . preg_replace('/:+/', '_', ($key = strtolower(trim($key))));
		if (file_exists($file)){
			throw new \Exception('Hash for "' . $key . '" already exists!');
		}
		$hash = md5($key . Hash::solt(10));
		file_put_contents($file, time() . ';' . $hash);
		return $hash;
	}

	/**
	 * @param string $key
	 * @throws \Exception
	 */
	public function remove($key){
		if (!array_key_exists(($key = strtolower(trim($key))), $this->Hashes)){
			throw new \Exception('Unknown hash key "' . $key . '"!');
		}
		unlink($this->path . DIRECTORY_SEPARATOR . $key);
		unset($this->Hashes[$key]);
	}

}

