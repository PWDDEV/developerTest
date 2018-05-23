<?php
$_SERVER['DOCUMENT_ROOT'] = '/var/www/workspace/kplus/';

require $_SERVER['DOCUMENT_ROOT'] .'/bitrix/modules/main/include/prolog_before.php';

use \Bitrix\Main;

class CIBlockElementCache
{
	/**
	 * Read data from cache
	 * 
	 * @param int $ttl
	 * @param string $cacheId
	 * @param string $cacheDir
	 */
	public static function readFromCache($ttl, $cacheId)
	{
		if ($ttl > 0) {
			$cache = Main\Application::getInstance()->getManagedCache();
			
			if($cache->read($ttl, $cacheId)) {
				return $cache->get($cacheId);
			}
		}

		return null;
	}

	/**
	 * Write data to cache
	 * 
	 * @param array  $data
	 * @param int    $ttl
	 * @param string $cacheId
	 * @param string $cacheDir
	 */
	public static function writeToCache($data, $ttl, $cacheId)
	{
		if ($ttl > 0) {
			$cache = Main\Application::getInstance()->getManagedCache();
			$ret = $cache->set($cacheId, $data);
		}
	}

	public static function GetList()
	{
		$args  = func_get_args();
		$cache = end($args);

		if (isset($cache['ttl'])) {
			array_pop($args);
		} else {
			$cache = ['ttl' => 600, 'fetchMethod' => 'Fetch'];
		}

		$cacheId = md5(__METHOD__ . serialize($args));
		$data = static::readFromCache($cache['ttl'], $cacheId);

		if ($data !== null) { echo 'dsf'; die;
			return $data;
		}

		$result = call_user_func_array(['\CIBlockElement', __METHOD__], $args);
		$return = [];

		while($item = call_user_func([$result, $cache['fetchMethod'] ?: 'Fetch'])) {
			$return[] = $item;
		}

		static::writeToCache($return, $cache['ttl']);

		return $return;
	}
}


$items = CIBlockElementCache::GetList(
	$order  = ['ID' => 'ASC'],
	$filter = ['IBLOCK_ID' => 1]
);

print_r($items);