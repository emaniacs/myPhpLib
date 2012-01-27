<?php

class DB_DRIVER_MYSQL implements DB_DRIVER{
    private $CONN ;

    public $driver ;

    public function __construct (array $conf) {
        $this->_init ($conf);
    }

    private function  _init ($conf) {
		if (is_null($conf['port']))
			$conf['port'] = 3306;

		if ($conf['persistent'])
			$connect = 'mysql_pconnect';
		else
			$connect = 'mysql_connect';

		$this->CONN = 
			$connect ($conf['host'] .':'. $conf['port'], $conf['user'], $conf['pass']) ;
		if (! $this->CONN)
			die ('Connection failure!');

		if (! mysql_select_db ($conf['dbName'], $this->CONN))
			die ('Error while selecting database!');

		$this->driver = $this;
    }

    public function exec ($query, $fetchAs) {
		if (null === $fetchAs)
			return mysql_query ($query, $this->CONN);

		$res = mysql_query ($query, $this->CONN) ;

		/* return if boolean */
		if (is_bool ($res))
			return $res;

		return $this->_fetch_as ($res, $fetchAs) ;
    }

    public function close () {
		@mysql_close($this->CONN);
    }

    public function begin() {
		$this->exec ('BEGIN', null);
    }

    public function commit() {
		$this->exec ('COMMIT', null);
    }

    public function rollback() {
		$this->exec ('ROLLBACK', null);
    }

    public function escape ($q) {
		return mysql_real_escape_string ($q, $this->CONN);
    }

    private function _fetch_as ($res, $fetchAs) {
		$ret = null;
		while ($obj = mysql_fetch_object ($this->RES, 'stdClass')) {
			if ('array' == $f)
				$ret[] = (array) $obj;
			else
				$ret[] = $obj ;
		}

		return $ret;
    }
}
