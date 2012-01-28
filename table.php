<?php
/* 
   (f) table.php
   (i) library for building table tag.
   (c) 2012, emaniacs
   (s) chars19.blogspot.com
   (l) GPL+

   Sat Jan 28 14:03:47 WIT 2012
   Created by : GNU Emacs 23.2.3
*/


class lib_TABLE {
    public $height;
    public $width;

    private $tblAttr;
    private $theadAttr;
    private $tbodyAttr;

    private $thData;
    private $trthAttr;

    private $tdData;
    private $trtdAttr;

    public function __construct ($height=0, $width=0) {
        $this->height = $height;
        $this->width = $width;
    }

    /* this for th and td, becase them have attribut tag 
       @param
         $text = text will show in tag
         $attr = attribut tag, something like style, href.
         &$input = td or th variable.
     */

    private function _setTextAndAttr ($text, $attr, &$input) {
        $input[0] = $text ;

        if (empty ($attr))
            $input[1] = '';
        else
            $this->_setAttr ($attr, $input[1]);
    }

    private function _setAttr ($attr, &$input) {
        if (is_array ($attr)) {
            /* restructure attribut */
            foreach ($attr as $key => $val)
                $tmpAttr[] = $key .'="'. $val .'"';

            /* insert attribute */
            $input = ' '. implode (' ', $tmpAttr) .' ';
        }

        /* if string */
        else
            $input = ' '. trim($attr) .' ';
    }

    /* flush() -> generate table
       @param $echo for indicated do echo or not.
     */
    public function flush ($echo=true) {

        /* process if height, width and tdData is not empty */
        if (empty ($this->height) ||
            empty ($this->width)  ||
            empty ($this->tdData) )
            return;

        $ret[] = '<table'. $this->tblAttr .'>';

        /* set header / <th> */
        $ret[] = '<thead'. $this->theadAttr .'>';
        if (! empty ($this->thData)) {
            $ret[] = '<tr'. $this->trthAttr .'>';

            for ($x=0; $x<$this->width; ++$x)
                $TH[] = '<th'. $this->thData[$x][1] .'>'. $this->thData[$x][0] .'</th>';

            $ret[] = implode($TH);
            $ret[] = '</tr>';
        }
        $ret[] = '</thead>';

        /* set body data, td tag. */
        $ret[] = '<tbody'. $this->tbodyAttr .'>';

        for ($x=0; $x<$this->height; ++$x) {
            $ret[] = '<tr'. $this->trtdAttr[$x] .'>';
            $TD = array();

            for($y=0; $y<$this->width; ++$y)
                $TD[] = '<td'. $this->tdData[$x][$y][1] .'>'. $this->tdData[$x][$y][0] .'</td>';

            $ret[] = implode ($TD);
            $ret[] = '</tr>';
        }
        $ret[] = '</tbody>';

        $ret[] = '</table>';

        if (! $echo)
            return implode ($ret);

        echo implode ($ret);
    }  

    /* set th tag
       @param:
        $text -> text will be shown.
        $attr -> attribute for tag.
        $col  -> column number,
                 adding automatic array if null.
     */
    public function setTh ($text , $attr='', $col=null) {
        if (is_numeric ($col))
            $this->_setTextAndAttr ($text, $attr, $this->thData[$col]);
        else
            $this->_setTextAndAttr ($text, $attr, $this->thData[]);
    }

    /* set td tag
       @param: like setTh(), but adding new variable a row number.
     */
    public function setTd ($text, $attr='', $row=null, $col=null) {
        if (is_numeric ($row)) {
            if (is_numeric ($col))
                $this->_setTextAndAttr ($text, $attr, $this->tdData[(int)$row][(int)$col]);
            else
                $this->_setTextAndAttr ($text, $attr, $this->tdData[(int)$row][]);
        }
        else {
            if (is_numeric ($col))
                $this->_setTextAndAttr ($text, $attr, $this->tdData[][(int)$row]);
            else
                $this->_setTextAndAttr ($text, $attr, $this->tdData[][]);
        }
    }

    /* set tr attribute was parent by td  */
    public function setTrTdAttr ($attr, $row=null) {
        if (is_numeric ($row))
            $this->_setAttr ($attr, $this->trtdAttr[$row]);
        else
            $this->_setAttr ($attr, $this->trtdAttr[]);
    }

    public function setTrThAtrr ($attr) {
        $this->_setAttr ($attr, $this->trthAttr);
    }

    public function setTheadAtrr ($attr) {
        $this->_setAttr ($attr, $this->theadAttr);
    }

    public function setTbodyAttr ($attr) {
        $this->_setAttr ($attr, $this->tbodyAttr);
    }

    public function setTableAttr ($attr) {
        $this->_setAttr ($attr, $this->tblAttr);
    }

    /* for test only */
    public function dump() {
        var_dump ($this->tblAttr, $this->theadAttr, $this->tbodyAttr, $this->thData, $this->tdData);
    }

}
