<?php
/* 
   (f) session.php
   (i) library for session.
   (c) 2011, emaniacs
   (s) chars19.blogspot.com
   (l) GPL+


   Mon Aug  1 14:32:53 WIT 2011
   Created by : GNU Emacs 23.2.3
*/

class LIB_SESSION implements IteratorAggregate{

    public function __construct ($conf=array()) {
        $dConf = array (
            'name' => 'Esession',
            'expired' => 120);

        /* ambil konfigurasi utamakan vari $conf */
        $dConf = $conf + $dConf;

		/* start session */
		$this->start($dConf);
    }

    public function __set ($name, $val) {
		$_SESSION[$name] = $val;
    }

    public function __get ($name) {
		if (isset ($_SESSION[$name]))
			return $_SESSION[$name] ;
		else
			return null;
    }

    public function del($name) {
		unset ($_SESSION[$name]);
    }

    public function destroy() {
		session_destroy();
    }

    private function start($conf) {
		if (session_id () == '') {
			session_cache_expire ($conf['expired']);
			session_name ($conf['name']);
			session_start ();
		}

		else {
			foreach ($_SESSION as $key => $val)
				$this->$key = $val;
			$this->conf['name'] = session_name ();
			$this->conf['id'] = session_id ();
			$this->conf['expired'] = session_cache_expire ();
		}
    }

    /* iterator */
    public function  getIterator() {
        return new ArrayIterator ($_SESSION);
    }
}

?>
