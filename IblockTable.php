<?php
namespace Starblust;

class IblockTable
{
    /** @var int $id 
     * iblock id 
     **/
    private $id;
    /** @var string $code iblock_code */
    private $code;
    /** @var int $ttl cache live time - seconds */
    private $ttl = 3600;
    /** @var string $cache_id cache identificator */
    private $cache_id = 'default_id';
    /** @var string $cache_path cache folder name */
    private $cache_path = '/default_dir/';
    
    /**
     * constructor
     *
     * @param array $params array of params
     * @return instance
     * @throws 
     **/

    public function __construct(array $params = []) {
        if (!empty($params['id'])){
            $this->id = $params['id'];
        }
        if (!empty($params['code'])){
            $this->code = $params['code'];
        }
        if (!empty($params['ttl'])){
            $this->ttl = $params['ttl'];
        }
        if (!empty($params['cache_id'])){
            $this->cache_id = $params['cache_id'];
        }
        if (!empty($params['cache_path'])){
            $this->cache_path = $params['cache_path'];
        }
    }

    /**
     * method that return raw uncached data from database
     *
     * @param array $params array of bitrix CIBlockElement::getList() parameters
     * proccess_callback(callable) - callback_function that can modify element result
     * function($arr) {
     *    do something
     *    return $arr;
     * }
     * @return array
     * @throws 
     **/

    public function getList(array $params) : array {
        \CModule::includeModule('iblock');
        if (empty($params['filter']['iblock_id'])){
            $params['filter']['IBLOCK_ID'] = $this->id;
        }
        $db_res = \CIBlockElement::GetList(
            ($params['sort']) ? $params['sort'] : [], 
            $params['filter'], 
            (isset($params['group_by']) && is_array($params['group_by'])) ? $params['group_by'] : false, 
            (isset($params['nav_start_params']) && is_array($params['nav_start_params'])) ? $params['nav_start_params'] : false, 
            (!empty($params['select'])) ? $params['select'] : ['ID']
        );
        $result = [];
        while($arr = $db_res->GetNext()){
            if (isset($params['process_callback']) && is_callable($params['process_callback']))
            {
                $arr = $params['process_callback']($arr);
            }
            $result[] = $arr;
        }
        return $result;
    }

    /**
     * method that return a cached data
     *
     * @param array $queryParams array of getList() parameters
     * 
     * @param array $cacheParams array of cache parameters
     * ttl - time to live
     * cache_id - id of a cache
     * cache_path - folder where to save data
     * @return array
     * @throws 
     **/
    public function getCachedList(array $queryParams, array $cacheParams): array {
        $ttl = ($cacheParams['ttl']) ? $cacheParams['ttl'] : $this->ttl;
        $cache_id = ($cacheParams['cache_id']) ? $cacheParams['cache_id'] : $this->cache_id;
        $cache_path = ($cacheParams['cache_path']) ? $cacheParams['cache_path'] : $this->cache_path;
        $result = [];
        $obCache = new \CPHPCache();
        if( $obCache->InitCache($ttl, $cache_id, $cache_path) ) {
            $vars = $obCache->GetVars();
            $result = $vars['result'];
        }
        elseif( $obCache->StartDataCache()  ) {
            $result = $this->getList($queryParams);
            $obCache->EndDataCache(array('result' => $result));
        }
        return $result;
    }

}