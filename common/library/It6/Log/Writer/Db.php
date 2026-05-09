<?php

require_once 'Zend/Log/Writer/Abstract.php';

class It6_Log_Writer_Db extends Zend_Log_Writer_Abstract
{
    /**
     * Database adapter instance
     * @var Zend_Db_Adapter
     */
    private $_db;

    /**
     * Name of the log table in the database
     * @var string
     */
    private $_table;

    /**
     * Relates database columns names to log data field keys.
     *
     * @var null|array
     */
    private $_columnMap;

    /**
     * Class constructor
     *
     * @param Zend_Db_Adapter $db   Database adapter instance
     * @param string $table         Log table in database
     * @param array $columnMap
     */
    public function __construct($db, $table, $columnMap = null)
    {
        $this->_db    = $db;
        $this->_table = $table;
        $this->_columnMap = $columnMap;
    }

    /**
     * Create a new instance of Zend_Log_Writer_Db
     * 
     * @param  array|Zend_Config $config
     * @return Zend_Log_Writer_Db
     * @throws Zend_Log_Exception
     */
    static public function factory($config)
    {
        $config = self::_parseConfig($config);
        $config = array_merge(array(
            'db'        => null, 
            'table'     => null, 
            'columnMap' => null,
        ), $config);
        
        if (isset($config['columnmap'])) {
            $config['columnMap'] = $config['columnmap'];
        }
        
        return new self(
            $config['db'],
            $config['table'],
            $config['columnMap']
        );
    }

    /**
     * Formatting is not possible on this writer
     */
    public function setFormatter($formatter)
    {
        require_once 'Zend/Log/Exception.php';
        throw new Zend_Log_Exception(get_class() . ' does not support formatting');
    }

    /**
     * Remove reference to database adapter
     *
     * @return void
     */
    public function shutdown()
    {
        $this->_db = null;
    }

    /**
     * Write a message to the log.
     *
     * @param  array  $event  event data
     * @return void
     */
    protected function _write($event)
    {
        $db = $this->getDb();

        if (array_key_exists('timestamp', $event) && is_numeric($event['timestamp']))
            $event['timestamp'] = It6_Date::timestampToDb(intval($event['timestamp']));

        if ($this->_columnMap === null) {
            $dataToInsert = $event;
        } else {
            $dataToInsert = array();
            foreach ($this->_columnMap as $columnName => $fieldKey) {
                $dataToInsert[$columnName] = $event[$fieldKey];
            }
        }
        try {
            $db->insert($this->_table, $dataToInsert);
        }
        catch (Exception $e) {
            // and log is lost...
        }
    }
    
    private function getDb() {
        if ($this->_db === null) {
            require_once 'Zend/Log/Exception.php';
            throw new Zend_Log_Exception('Database name is null');
        }
        
        $db = null;
        $_dbs = explode('|', $this->_db);
        foreach ( $_dbs as $_db ) {
            if ( Zend_Registry::isRegistered($_db) ) {
                $db = Zend_Registry::get($_db);
                break;
            }
        }
        
        if ($db === null) {
            require_once 'Zend/Log/Exception.php';
            throw new Zend_Log_Exception('Database adapter is null');
        }
        
        return $db;
    }
}
