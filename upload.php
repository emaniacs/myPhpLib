<?php
/* 
   (f) upload.php
   (i) library for handle upload file.
   (c) 2011, emaniacs
   (s) chars19.blogspot.com
   (l) GPL+

   Mon Sep 12 21:59:02 WIT 2011
   Created by : GNU Emacs 23.2.3
 */

class LIB_UPLOAD {
    private $conf = array (
        /* destination directory to moved upload file */
        'dirName'     => '',    /* cannot be empty */

        /* auto rename file upload */
        'autoRename'  => false,

        /* function to rename file */
        'funcRename'  => true,

        /* force upload file, will created directory if not exists */
        'forceUpload' => false,

        /* allowed type file 
           if true it means allowed all extension to be upload.
           or use comma separated.
         */
        'allowedType' => true,

        /* maximum size 
           it must in byte,
           if true allowed upload all size.
         */
        'maxSize' => true
    );

    /* properties setelah upload */
    public $info ;
    public $totalUpload = 0 ;
    public $totalSaved = 0;
    public $error = '';

    /* for library */
    private $multiUpload = false ;
    private $fieldName = '';
    private $tmpType = '';

    public function __construct ($conf=array()) {
        /* is conf is string, it will be directory name */
		if (is_array ($conf))
			$this->conf = $conf + $this->conf;
		else 
			$this->conf['dirName'] = $conf ;

        /* fixed on double slash */
        $this->conf['dirName'] = rtrim ($this->conf['dirName'], '/');
        $this->conf['dirName'] .= '/';
    }

    /* doUpload()
       @param:
        $field -> field name for upload tag file
       @return:
        upload status
     */
    public function doUpload ($field='userfile') {
		/* return null jika belum siap upload */
		if (false == $this->readyToUpload ())
			return null ;

		$this->fieldName = $field ;

		/* multi upload */
		if (is_array ($_FILES[$field]['error']))
            $return = $this->_multiUpload($field);
        
        /* single upload */
        else
            $return = $this->_singleUpload($field);

        return $return;
    }

    /* multi file upload handler */
    private function  _multiUpload($field) {
        $this->multiUpload = true;
        $return = false ;
        
        foreach ($_FILES[$field]['error'] as $key => $e) {
            
            $this->info[$key]['name'] = $_FILES[$field]['name'][$key] ;
            $this->info[$key]['move_to'] = $e ;
            
            $name = $this->getFilename ($_FILES[$field]['name'][$key]) ;
            
            /* no error, upload success, move it! */
            if (! $e) {
                ++$this->totalUpload;
                
                $return = 
                    move_uploaded_file ($_FILES[$field]['tmp_name'][$key] ,
                        $this->conf['dirName'] . $name );
                if ($return) {
                    ++$this->totalSaved ;
                    $this->info[$key]['move_to'] = $this->conf['dirName'] . $name ;
                }
            }
        }

        return $return;
    }

    /* single file upload handler */
    private function  _singleUpload($field) {
        $return = false;

        $this->info['name'] = $_FILES[$field]['name'] ;
        $this->info['move_to'] = $_FILES[$field]['error'] ;
        
        $name = $this->getFilename ($this->info['name']);
        
        if (! $_FILES[$field]['error']) {
            /* file has been uploaded, count the files */
            ++$this->totalUpload ;

            /* check on upload success */
            $return = $this->_readyToMoved ($_FILES[$field]);

            if ($return) {
                $return = 
                    move_uploaded_file ($_FILES[$field]['tmp_name'] ,
                        $this->conf['dirName'] . $name );
                if ($return) {
                    ++$this->totalSaved ;
                    $this->info['move_to'] = $this->conf['dirName'] . $name ;
                }
            }

        }

		return $return ;
    }

    private function  _readyToMoved ($files) {
        /* check allowed extension */
        $type = $this->_allowedType ($files['type']);
        if (false === $ext)
            return $this->setErrorMsg (6);

        /* size problem */
        if ($files['size'] > $this->conf['maxSize'])
            return $this->setErrorMsg (7);

    }

    private function  _allowedType ($ext) {
        /* if allowed type is true, allowed all type */
        if (true === $this->conf['allowedType'])
            return true;

        /* if tmpType not array, convert it */
        if (! is_array ($this->tmpType))
            $this->tmpType = split (',', $this->conf['allowedType']);

        return in_array();
    }

    public function getInfo() {
		if ($this->multiUpload)
			foreach ($_FILES[$this->fieldName] as $name => $arr) {
				foreach ($arr as $key => $val)
					$this->info[$key][$name] = $val;
			}

		else 
			$this->info += $_FILES[$this->fieldName] ;

		return $this->info ;
    }


    private function readyToUpload () {
		if (empty ($_FILES))
			return $this->setErrorMsg(0);

		if (empty ($this->conf['dirName']))
			return $this->setErrorMsg (1) ;

		if (! is_dir ($this->conf['dirName'])) {
			if ($this->conf['forceUpload']) {
				$ret = @mkdir ($this->conf['dirName'], 0755 , true);
				if (! $ret)
					return $this->setErrorMsg (3);
			}
			else 
				return $this->setErrorMsg (2) ;
        }

		if (! is_writeable ($this->conf['dirName']))
			return $this->setErrorMsg (4);

		if ($this->conf['autoRename'])
			if (is_string ($this->conf['funcRename'])) {
				if (! is_callable ($this->conf['funcRename']))
					return $this->setErrorMsg(5);
			}
			else 
				$this->conf['funcRename'] = true;

		return true ;
    }

    private function getFilename ($fname) {
		$ret = $fname ;

		if ($this->conf['autoRename']) {
			if (true === $this->conf['funcRename'])
				$ret = substr (md5 ($fname), mt_rand (0, 23), 9);
			else
				$ret = call_user_func_array ($this->conf['funcRename'], $fname);
        }

		return "{$ret}";
    }

    private function setErrorMsg ($id) {
		switch ($id) {
		case 0:			/* no upload file */
			$this->error = 'No upload activity detected!';
			break;
		case 1: 		/* empty dirName */
			$this->error = 'directory name cannot be empty!.';
			break;
		case 2:			/* dirName not exists */
			$this->error = '`' . $this->conf['dirName'] . '` not exists!.';
			break;
		case 3:			/* gagal buat direktory */
			$this->error = 'Cannot create `'. $this->conf['dirName'] . '`!. ';
			break;
		case 4:			/* dirName unwriteable */
			$this->error = '`' . $this->conf['dirName'] . '` unwriteable!.';
			break;
		case 5:			/* function name not exists */
			$fn = is_array($this->conf['funcRename']) ? 
                join ($this->conf['funcRename'], '::') :
                $this->conf['funcRename'] ;
			$this->error = "Function {$fn} cannot be execute!";
			break;
        case 6:                 /* forbidden extension */
            $this->error = $this->name . ': Unallowed extension!';
            break;
		}

		return false;
    }

}

?>
