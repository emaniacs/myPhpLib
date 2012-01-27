<?php
/* 
   (f) request.php
   (i) library for handle for POST, GET and COOKIE
   (c) 2011, emaniacs
   (s) chars19.blogspot.com
   (l) GPL+
-
   @return method
    null  -> if there are no request.
    true  -> method without parameter and exists request.
    false -> exists request but request variable not found.
    $var  -> exists request and exists variable
-
   Mon Aug  1 14:32:53 WIT 2011
   Created by : GNU Emacs 23.2.3
 */

class LIB_REQUEST implements IteratorAggregate {
    public $cookieExpired = '';
    public $defaultRequest ;

    public function __construct ($dR='post') {
        $this->defaultRequest = $dR ;
		$this->cookieExpired = time () + 7300; /* 2 hours */
    }

    /* mengambil nilai request berdasarkan defaultRequest  */
    public function __get ($name) {
        $dR = $this->defaultRequest ;

        return $this->$dR ($name) ;
    }

    /* __set only for set cookie value */
    public function __set ($name , $val) {
        $this->cookie ($name, $val) ;
    }

    public function post ($name=null) {
        if (null===$name)
            return empty($_POST) ? null : true ;

		if (isset ($_POST[$name]))
			return $_POST[$name];

		return false;
    }

    public function get ($name=null) {
        if (null===$name)
            return empty($_GET) ? null : true ;

		if (isset ($_GET[$name]))
			return $_GET[$name];

		return false;
    }

    /* 2 parameter untuk mengeset/mengambil nilai */
    public function cookie ($name=null, $val=null) {
        /* jika name==null maka return status cookie */
        if (null === $name)
            return empty ($_COOKIE) ? null : true ;

        /* jika val==null, return cookie[name] atau false */
        if (null === $val)
            return isset ($_COOKIE[$name]) ? $_COOKIE[$name] : false ;

		/* set cookie */
        return setcookie ($name, $val, $this->cookieExpired);
    }

    /* return all request variable. */
    public function  allRequest ($req='post') {
        if ('post' == $req)
            return $_POST;

        if ('get' == $req)
            return $_GET;

        if ('cookie' == $req)
            return $_COOKIE;
        
        return array();
    }

    /* for iterate, optional you can use allRequest() */
    public function  getIterator () {
        return new ArrayIterator ($this->allRequest ($this->defaultRequest));
    }

}

?>
