<?php
/* 
   (f) api.SIMPLE.php
   (c) 2011, emaniacs
   (s) chars19.blogspot.com
   (l) GPL+


   Fri Sep 16 21:09:36 WIT 2011
   Created by : GNU Emacs 23.2.3
     
*/


class DB_API_SIMPLE{
    public $QUERY ;
    public $tblName ;

    public $driver ;

    public function __construct ($tblName) {
		$this->tblName = $tblName ;
    }

    /* extract where 
       EXAMPLE where
       > string
          result --> WHERE 'string'
       > array ('id' => 0)
          result --> WHERE id = 0
       > array ('id' => 0, 'name' => 'root')
          result --> WHERE id = 0 AND name = root
       > array (array ('id', '<' , 2)
                'AND'
                array ('name', 'like' , '%use%'))
         result --> WHERE id < 2 AND name like %use%
     */
    private function _where ($where) {
		/* return variable */
		$ret = array();

		if (! empty($where)) {
			$i=0;
			$ret[] = 'WHERE';

			if (is_array ($where)) {
				foreach ($where as $key => $val) {
                    $i++;
                    /* cek logical */
					if (($i % 2) == 0) {
						$ret[] = $val;
                        continue;
                    }

                    if (is_array ($val)) 
                        $ret[] = $val[0] .' '. $val[1] .' "'. $this->driver->escape ($val[2]).'"'; 
                    elseif (is_string ($key))
						$ret[] = "{$key} = '" . $this->driver->escape ($val) . "'";
                    else
                        $ret[] = $val;
				}
			}
			else $ret[] = $where;
		}

		return implode (' ', $ret);
    }

    /* extract row and value, insert backtick or ' */
    private function _extract ($row, $escape=false) {
        /* if empty row return '*' */
		if (empty ($row))
			return '*';

        /* convert $row to array.
           maybe $row is 'id,nick,name,email'
         */
		if (! is_array ($row)) {
            /* if first character is : it means query is db
               function */
			if (strpos ($row, ':') === 0)
				return trim (substr($row, 1));

			if (trim ($row) == '*')
				return '*';

			$row = explode (',', $row);
		}

		/* escape character for value */
		if ($escape) {
			$eChar = "'";
			/* array_map or array_walk is slowly */
			foreach ($row as &$val)
				$val = $this->driver->escape ($val);
		}

		/* remove whitespace in row, we do explode() before */
		else {
			$eChar = '`';
			foreach ($row as &$val)
				$val = trim($val);
		}

		return "{$eChar}" . implode ("{$eChar},{$eChar}", $row) . "{$eChar}";
    }

    /* extract for SET(update) query 
       > array ('user'=>'newUser', 'id'=>7)
          result --> "user = newUser, id = 7"
     */
    private function  _setExtract ($data) {
        $ret = array();

        foreach ($data as $key => $val)
            $ret[] = "`{$key}` = ". $this->driver->escape($val);

        return implode (',', $ret); 
    }

    public function select ($row='*', $where=array(), $other='') {
		$this->QUERY = 
			sprintf ('SELECT %s FROM %s %s %s', 
					 $this->_extract($row),
					 $this->tblName ,
					 $this->_where($where) ,
					 $other) ;

		/* query langsung diexecute */
		return true;
    }

    public function insert (array $data, $where=array(), $other='') {
		$this->QUERY = 
			sprintf ('INSERT INTO %s (%s) VALUES (%s) %s %s',
					 $this->tblName ,
					 $this->_extract (array_keys ($data)) ,
					 $this->_extract ($data, true) ,
					 $this->_where ($where) ,
					 $other);

		/*  always return true, it means execute query */
		return true;
    }

    public function update (array $data, $where=array(), $other='') {
		$this->QUERY =
			sprintf('UPDATE %s SET %s %s %s',
					$this->tblName ,
					$this->_setWhere($data),
					$this->_where($where),
					$other );

		/* always return true, it means execute query */
		return true;
    }

    public function delete($where=array(), $other='') {
		$this->QUERY = 
			sprintf ('DELETE FROM %s %s %s',
					 $this->tblName ,
					 $this->_where($where) ,
					 $other);

		/*  always return true, it means execute query */
		return true;
    }


    /* note: $other (maybe) using for limit,order by or other */
}
