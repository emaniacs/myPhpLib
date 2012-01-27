<?php

class DB_DRIVER_SQLITE implements DB_DRIVER {
    private $CONN ;

    public $driver;

    public function __construct (array $conf) {
        $this->_init($conf);
    }

    private function  _init($conf) {
		$dbName = $conf['dbName'];

		try {
			$this->CONN = new SQLITE3 ($dbName);
		}
		catch (Exception $e) {
			exit ($e->getMessage() . " ('{$dbName}')");
		}

		$this->driver = $this;
    }

    public function exec ($query, $fetchAs) {
		if (null === $fetchAs)
			return $this->CONN->exec ($query);

		$res = $this->CONN->query ($query);

		if (false === $res)
			return null;

		return $this->_fetch_as ($res, $fetchAs);
    }

    public function begin() {
		$this->CONN->exec ('BEGIN');
    }

    public function commit() {
		$this->CONN->exec ('COMMIT');
    }

    public function rollback() {
		$this->CONN->exec ('ROLLBACK');
    }

    public function close() {
		@$this->CONN->close();
    }

    public function escape ($q) {
		return $this->CONN->escapeString($q);
    }

    private function _fetch_as ($res, $fetchAs) {
		$ret = array() ;

		if ('object' == $fetchAs || 'assoc' == $fetchAs)
			$f = SQLITE3_ASSOC ;
        elseif ('num' == $fetchAs)
            $f = SQLITE_NUM;
		else
			$f = SQLITE3_BOTH ;

		while ($row = $res->fetchArray($f)) {

			if ('object' == $fetchAs)
				$ret[] = (object) $row;
			else
				$ret[] = $row ;
		}

		return $ret;
    }

}