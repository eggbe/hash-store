<?php
namespace Eggbe\HashStore;

class HashStore {

	/**
	 * @const string
	 */
	const SORT_BY_DATE = 'by_date';

	/**
	 * @const string
	 */
	const SORT_BY_ABC = 'by_abc';

	/**
	 * @const string
	 */
	const SORT_TYPE_ASC = 'asc';

	/**
	 * @const string
	 */
	conST SORT_TYPE_DESC = 'desc';

	/**
	 * @var string
	 */
	private $path = null;

	/**
	 * @param array $Config
	 * @param string $sortBy
	 * @param string $sortType
	 * @throws \Exception
	 */
	public final function __construct(array $Config = [], $sortBy = self::SORT_BY_DATE, $sortType = self::SORT_TYPE_ASC){
		if (!array_key_exists('path', $Config)){
			throw new \Exception('Storage path in not defined!');
		}
		$this->path = $Config['path'];
		if (file_exists($this->path) || !is_dir($this->path) || !is_writable($this->path)){
			throw new \Exception('Storage path "' . $this->path . '" is not exists or not writable!');
		}

		_dumpe(glob($this->path . DIRECTORY_SEPARATOR . '*'));
	}

	public final function all(){

	}

}

