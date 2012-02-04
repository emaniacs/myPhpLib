<?php
/* 
   (f) DB.php
   (c) 2011, emaniacs
   (s) chars19.blogspot.com
   (l) GPL+

   Fri Sep 16 21:06:04 WIT 2011
   Created by : GNU Emacs 23.2.3
*/


final class Lib_db {
    private $CONF = array( 
        /* you must define this value */
         'driver'    => 'mysql', /* mysql, sqlite */
         'dbName'    => '',      /* database name */
         'tblName'   => '',      /* table name */

         /* ignore on sqlite */
         'host'      => '127.0.0.1', /* hostname */
         'user'      => 'user',      /* username */
         'pass'      => 'password',  /* password for database connection */
         'port'      => null,        /* if null, will using default port */
         'persistent' => true        /* true or false */
    );

    /* for api, sql builder */
    private $API ;

    /* database driver */
    private static $DRIVER ;
    /* database utilitiy */
    private static $UTILS;

    /* fetch results as.. */
    private $_fetchAs = 'object';

    /* temporary result  query, maybe need results in  other method or
       function */
    public $RESULTS ;

    public function __construct ($conf = array(), $newConn=false) {
        /* replace $this->CONF with conf */
        $this->CONF = $conf + $this->CONF;

		/* just one connection to database or set $newConn to create
           other connection to database server
        */
		if ($newConn || ! isset(self::$DRIVER))
			self::$DRIVER = $this->_load_driver();

        /* load api for query builder */
		$this->API = $this->_load_api();

        /* set api driver with connection, it will using for escape
           character based on database connection (sqlite escape
           character is different with mysql) */
		$this->API->driver = self::$DRIVER->driver;
    }

    /* fetch database type */
    public function  fetchAs ($f) {
        $this->_fetchAs = $f;
    }

    /* return result based on db query */
    public function results ($fetchAs = null) {
        if (null === $fetchAs)
            $fetchAs = $this->_fetchAs;

		$this->RESULTS = self::$DRIVER->exec ($this->API->QUERY, $fetchAs);

        /* if fetchAs null, it means we need return query exec */
        if (null===$fetchAs)
            return $this->RESULTS;

        /* if query just one result return with one array */
		if (count ($this->RESULTS) == 1)
			$this->RESULTS = $this->RESULTS[0];

		return $this->RESULTS; 
    }

    /* execute query to db */
    public function execQuery ($query, $fetchAs=null) {
        if (null === $fetchAs)
            $fetchAs = $this->_fetchAs;

		/* there are no escape character in here */
		return self::$DRIVER->exec ($query, $fetchAs);
    }

    /* escape character */
    public function escape ($q) {
		return self::$DRIVER->escape ($q);
    }

    /* begin transaction */
    public function begin() {
		self::$DRIVER->begin();
    }

    /* commit transaction */
    public function commit() {
		self::$DRIVER->commit() ;
    }

    /* rollback */
    public function rollback() {
		self::$DRIVER->rollback();
    }

    /* call everything to API (for query builder) */
    public function __call ($fn, $arg) {
		$exec = call_user_func_array (array ($this->API, $fn), $arg);
		if ($exec)
			return $this->results($this->_fetchAs);

		return $this;
    }

    /* set api properties */
    public function __set ($name, $val) {
		$this->API->$name = $val ;
    }

    /* get api properties */
    public function __get ($name) {
		return $this->API->$name ;
    }

    /* close connection to db */
    public function __destruct() {
		self::$DRIVER->close();
    }

    /* load database utility */
    public function loadUtility ($newUtil=false) {
		/* one utility */
		if (self::$UTILS && ! $newUtil ||)
			return self::$UTILS;

        /* driver name is uppercase */
        $name = strtoupper ($this->CONF['driver']) ;
        $class = 'DB_UTILITY_' . $name ;

        /* dont call __autoload */
        if (!class_exists ($name, false))
            require './db/'.$this->CONF['driver'].'/utility.'.$name.'.php';

		self::$UTILS = new $class ($this->CONN);
        return self::$UTILS ;
    }

    /* change api type 
       if you wanna change table in database, do like this
       assume $db is instance of LIB_DB;
       $db->tblName = 'newTable';
     */
    public function  changeApiTo ($apiName) {
        $this->CONF['api'] = $apiName;
        $this->API = $this->_load_api();
        $this->API->driver = self::$DRIVER->driver ;

        return $this;
    }

    /* load api and driver */
    private function _load_api () {
		$apiName = strtoupper ($this->CONF['api']);
		$class = 'DB_API_' . $apiName ;

		if (! class_exists ($class, false))
			require './db/api.' . $apiName . '.php' ;

        /* tblName is parameter for api */
		return new $class ($this->CONF['tblName']);
    }

    private function _load_driver () {
		$dName = strtoupper ($this->CONF['driver']) ;
		$class = 'DB_DRIVER_' . $dName;

		if (! class_exists ($class, false))
			require './db/'.$this->CONF['driver'].'/'.$dName.'.php';

		return new $class ($this->CONF);
    }
}


/* interface for database driver */
interface DB_DRIVER {
    /* for execute query */
    public function exec ($query, $fetch_as) ;
    public function close();
    public function begin();
    public function commit();
    public function rollback();
    public function escape ($q);
}
