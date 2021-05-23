<?php
//新加
require_once('fpdf.php');
require_once('color.inc.php');
require_once('htmlparser.inc.php');
include "chinese.php";
define ('FPDF_UNICODE_ENCODING', 'UCS-2BE');

class PDF_Unicode extends PDF_Chinese{
    var $left;
    var $right;
    var $top;
    var $bottom;
    var $width;
    var $height;
    var $defaultFontFamily ;
    var $defaultFontStyle;
    var $defaultFontSize;
      var $FloorSign;
    var $Language;
  var $charset;
  var $isUnicode;

  //function PDF_Unicode ($charset = 'UTF-8'){
  //  $this->FPDF ('P', 'mm', 'A4');
  //  $this->charset = strtoupper(str_replace ('-', '', $charset));
  //  $this->isUnicode = in_array ($this->charset, array ('UTF8', 'UTF16', 'UCS2'));
  //}

    public function __construct($charset = 'UTF-8') {
        $this->FPDF ('P', 'mm', 'A4');
        $this->charset = strtoupper(str_replace ('-', '', $charset));
        $this->isUnicode = in_array ($this->charset, array ('UTF8', 'UTF16', 'UCS2'));
    }

    function AddUniCNShwFont ($family='Uni', $name='PMingLiU'){
        for($i=32;$i<=126;$i++)
            $cw[chr($i)]=500;
        $CMap='UniCNS-UCS2-H';
        $registry=array('ordering'=>'CNS1','supplement'=>0);
        $this->AddCIDFonts($family,$name,$cw,$CMap,$registry);
        }

    function AddUniCNSFont ($family='Uni', $name='PMingLiU'){
        $cw=$GLOBALS['Big5_widths'];
        $CMap='UniCNS-UCS2-H';
        $registry=array('ordering'=>'CNS1','supplement'=>0);
        $this->AddCIDFonts($family,$name,$cw,$CMap,$registry);
        }

    function AddUniGBhwFont ($family='uGB', $name='AdobeSongStd-Light-Acro'){
        for($i=32;$i<=126;$i++)
            $cw[chr($i)]=500;
        $CMap='UniGB-UCS2-H';
        $registry=array('ordering'=>'GB1','supplement'=>4);
        $this->AddCIDFonts($family,$name,$cw,$CMap,$registry);
        }

    function AddUniGBFont ($family='uGB', $name='AdobeSongStd-Light-Acro'){
        $cw=$GLOBALS['GB_widths'];
        $CMap='UniGB-UCS2-H';
        $registry=array('ordering'=>'GB1','supplement'=>4);
        $this->AddCIDFonts($family,$name,$cw,$CMap,$registry);
        }

    function GetStringWidth ($s){
        if ($this->isUnicode) {
            $txt = mb_convert_encoding ($s, FPDF_UNICODE_ENCODING, $this->charset);
            $oEnc = mb_internal_encoding();
            mb_internal_encoding (FPDF_UNICODE_ENCODING);
            $w = $this->GetUniStringWidth ($txt);
            mb_internal_encoding ($oEnc);
            return $w;
            }
        else
            return parent::GetStringWidth($s);
        }

    function Text ($x, $y, $txt){
        if ($this->isUnicode){
            $txt = mb_convert_encoding ($txt, FPDF_UNICODE_ENCODING, $this->charset);
            $oEnc = mb_internal_encoding();
            mb_internal_encoding (FPDF_UNICODE_ENCODING);
            $this->UniText ($x, $y, $txt);
            mb_internal_encoding ($oEnc);
            }
        else
            parent::Text ($x, $y, $txt);
        }

    function Cell ($w,$h=0,$txt='',$border=0,$ln=0,$align='',$fill=0,$link=''){
        if($this->isUnicode){
            $txt = mb_convert_encoding ($txt, FPDF_UNICODE_ENCODING, $this->charset);
              $oEnc = mb_internal_encoding();
              mb_internal_encoding (FPDF_UNICODE_ENCODING);
              $this->UniCell ($w, $h, $txt, $border, $ln, $align, $fill, $link);
              mb_internal_encoding ($oEnc);
            }
        else
             parent::Cell ($w, $h, $txt, $border, $ln, $align, $fill, $link);
         }

    function MultiCell ($w,$h,$txt,$border=0,$align='J',$fill=0){
        if($this->isUnicode){
              $txt = mb_convert_encoding ($txt, FPDF_UNICODE_ENCODING, $this->charset);
              $oEnc = mb_internal_encoding();
              mb_internal_encoding (FPDF_UNICODE_ENCODING);
              $this->UniMultiCell ($w, $h, $txt, $border, $align, $fill);
              mb_internal_encoding ($oEnc);
            }
        else{
              parent::MultiCell ($w, $h, $txt, $border, $align, $fill);
        }
      }

  function Write ($h,$txt,$link='')
  {
    if ($this->isUnicode) {
      $txt = mb_convert_encoding ($txt, FPDF_UNICODE_ENCODING, $this->charset);
      $oEnc = mb_internal_encoding();
      mb_internal_encoding (FPDF_UNICODE_ENCODING);
      $this->UniWrite ($h, $txt, $link);
      mb_internal_encoding ($oEnc);
    } else {
      parent::Write ($h, $txt, $link);
    }
  }

  // implementation in Unicode version

  function GetUniStringWidth ($s)
  {
    //Unicode version of GetStringWidth()
    $l=0;
    $cw=&$this->CurrentFont['cw'];
    $nb=mb_strlen($s);
    $i=0;
    while($i<$nb) {
      $c=mb_substr($s,$i,1);
      $ord = hexdec(bin2hex($c));
      if($ord<128) {
    $l+=$cw[chr($ord)];
      } else {
    $l+=1000;
      }
      $i++;
    }
    return $l*$this->FontSize/1000;
  }

  function UniText ($x, $y, $txt)
  {
    // copied from parent::Text but just modify the line below
    $s=sprintf('BT %.2f %.2f Td <%s> Tj ET',$x*$this->k,($this->h-$y)*$this->k, bin2hex($txt));

    if($this->underline && $txt!='')
      $s.=' '.$this->_dounderline($x,$y,$txt);
    if($this->ColorFlag)
      $s='q '.$this->TextColor.' '.$s.' Q';
    $this->_out($s);
  }

  function UniCell ($w,$h=0,$txt='',$border=0,$ln=0,$align='',$fill=0,$link='')
  {
    // copied from parent::Text but just modify the line with an output "BT %.2f %.2f Td <%s> Tj ET" ...
    $k=$this->k;
    if($this->y+$h>$this->PageBreakTrigger && !$this->InFooter && $this->AcceptPageBreak())
      {
    //Automatic page break
    $x=$this->x;
    $ws=$this->ws;
    if($ws>0)
      {
        $this->ws=0;
        $this->_out('0 Tw');
      }
    $this->AddPage($this->CurOrientation);
    $this->x=$x;
    if($ws>0)
      {
        $this->ws=$ws;
        $this->_out(sprintf('%.3f Tw',$ws*$k));
      }
      }
    if($w==0)
      $w=$this->w-$this->rMargin-$this->x;
    $s='';
    if($fill==1 || $border==1)
      {
    if($fill==1)
      $op=($border==1) ? 'B' : 'f';
    else
      $op='S';
    $s=sprintf('%.2f %.2f %.2f %.2f re %s ',$this->x*$k,($this->h-$this->y)*$k,$w*$k,-$h*$k,$op);
      }
    if(is_string($border))
      {
    $x=$this->x;
    $y=$this->y;
    if(strpos($border,'L')!==false)
      $s.=sprintf('%.2f %.2f m %.2f %.2f l S ',$x*$k,($this->h-$y)*$k,$x*$k,($this->h-($y+$h))*$k);
    if(strpos($border,'T')!==false)
      $s.=sprintf('%.2f %.2f m %.2f %.2f l S ',$x*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-$y)*$k);
    if(strpos($border,'R')!==false)
      $s.=sprintf('%.2f %.2f m %.2f %.2f l S ',($x+$w)*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
    if(strpos($border,'B')!==false)
      $s.=sprintf('%.2f %.2f m %.2f %.2f l S ',$x*$k,($this->h-($y+$h))*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
      }
    if($txt!=='')
      {
    if($align=='R')
      $dx=$w-$this->cMargin-$this->GetUniStringWidth($txt);
    elseif($align=='C')
      $dx=($w-$this->GetUniStringWidth($txt))/2;
    else
      $dx=$this->cMargin;
    if($this->ColorFlag)
      $s.='q '.$this->TextColor.' ';
    $s.=sprintf('BT %.2f %.2f Td <%s> Tj ET',($this->x+$dx)*$k,
            ($this->h-($this->y+.5*$h+.3*$this->FontSize))*$k,bin2hex($txt));
    if($this->underline)
      $s.=' '.$this->_dounderline($this->x+$dx,$this->y+.5*$h+.3*$this->FontSize,$txt);
    if($this->ColorFlag)
      $s.=' Q';
    if($link)
      $this->Link($this->x+$dx,$this->y+.5*$h-.5*$this->FontSize,$this->GetUniStringWidth($txt),$this->FontSize,$link);
      }
    if($s)
      $this->_out($s);
    $this->lasth=$h;
    if($ln>0)
      {
    //Go to next line
    $this->y+=$h;
    if($ln==1)
      $this->x=$this->lMargin;
      }
    else
      $this->x+=$w;
  }

  function UniMultiCell($w,$h,$txt,$border=0,$align='L',$fill=0)
  {
    //Unicode version of MultiCell()

    $enc = mb_internal_encoding();

    $cw=&$this->CurrentFont['cw'];
    if($w==0)
      $w=$this->w-$this->rMargin-$this->x;
    $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
    $s = $txt;
    $nb=mb_strlen($s);
    if ($nb>0 && mb_substr($s,-1)==mb_convert_encoding("\n", $enc, $this->charset))
      $nb--;
    $b=0;
    if($border)
      {
    if($border==1)
      {
        $border='LTRB';
        $b='LRT';
        $b2='LR';
      }
    else
      {
        $b2='';
        if(is_int(strpos($border,'L')))
          $b2.='L';
        if(is_int(strpos($border,'R')))
          $b2.='R';
        $b=is_int(strpos($border,'T')) ? $b2.'T' : $b2;
      }
      }
    $sep=-1;
    $i=0;
    $j=0;
    $l=0;
    $nl=1;
    while($i<$nb)
      {
    //Get next character
    $c=mb_substr($s,$i,1);
    $ord = hexdec(bin2hex($c));
    $ascii = ($ord < 128);
    if($c==mb_convert_encoding("\n", $enc, $this->charset))
      {
        //Explicit line break
        $this->UniCell($w,$h,mb_substr($s,$j,$i-$j),$b,2,$align,$fill);
        $i++;
        $sep=-1;
        $j=$i;
        $l=0;
        $nl++;
        if($border && $nl==2)
          $b=$b2;
        continue;
      }
    if(!$ascii || $c==mb_convert_encoding(' ', $enc, $this->charset))
      {
        $sep=$i;
        $ls=$l;
      }
    $l+=$ascii ? $cw[chr($ord)] : 1000;
    if($l>$wmax)
      {
        //Automatic line break
        if($sep==-1 || $i==$j)
          {
        if($i==$j)
          $i++; //=$ascii ? 1 : 2;
        $this->UniCell($w,$h,mb_substr($s,$j,$i-$j),$b,2,$align,$fill);
          }
        else
          {
        $this->UniCell($w,$h,mb_substr($s,$j,$sep-$j),$b,2,$align,$fill);
        $i=(mb_substr($s,$sep,1)==mb_convert_encoding(' ', $enc, $this->charset)) ? $sep+1 : $sep;
          }
        $sep=-1;
        $j=$i;
        $l=0;
        $nl++;
        if($border && $nl==2)
          $b=$b2;
      }
    else
      $i++; //=$ascii ? 1 : 2;
      }
    //Last chunk
    if($border && is_int(strpos($border,'B')))
      $b.='B';
    $this->UniCell($w,$h,mb_substr($s,$j,$i-$j),$b,2,$align,$fill);
    $this->x=$this->lMargin;
  }

  function UniWrite($h,$txt,$link='')
  {
    //Unicode version of Write()
    $enc = mb_internal_encoding();
    $cw=&$this->CurrentFont['cw'];
    $w=$this->w-$this->rMargin-$this->x;
    $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
    $s = $txt;

    $nb=mb_strlen($s);
    $sep=-1;
    $i=0;
    $j=0;
    $l=0;
    $nl=1;
    while($i<$nb)
      {
    //Get next character
    $c=mb_substr($s,$i,1);
    //Check if ASCII or MB
    $ord = hexdec(bin2hex($c));
    $ascii=($ord < 128);
    if($c==mb_convert_encoding("\n", $enc, $this->charset))
      {
        //Explicit line break
        $this->UniCell($w,$h,mb_substr($s,$j,$i-$j),0,2,'',0,$link);
        $i++;
        $sep=-1;
        $j=$i;
        $l=0;
        if($nl==1)
          {
        $this->x=$this->lMargin;
        $w=$this->w-$this->rMargin-$this->x;
        $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
          }
        $nl++;
        continue;
      }
    if(!$ascii || $c==mb_convert_encoding(' ', $enc, $this->charset))
      $sep=$i;
    $l+=$ascii ? $cw[chr($ord)] : 1000;
    if($l>$wmax)
      {
        //Automatic line break
        if($sep==-1 || $i==$j)
          {
        if($this->x>$this->lMargin)
          {
            //Move to next line
            $this->x=$this->lMargin;
            $this->y+=$h;
            $w=$this->w-$this->rMargin-$this->x;
            $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
            $i++;
            $nl++;
            continue;
          }
        if($i==$j)
          $i++; //=$ascii ? 1 : 2;
        $this->UniCell($w,$h,mb_substr($s,$j,$i-$j),0,2,'',0,$link);
          }
        else
          {
        $this->UniCell($w,$h,mb_substr($s,$j,$sep-$j),0,2,'',0,$link);
        $i=(mb_substr($s,$sep,1)==mb_convert_encoding(' ', $enc, $this->charset)) ? $sep+1 : $sep;
          }
        $sep=-1;
        $j=$i;
        $l=0;
        if($nl==1)
          {
        $this->x=$this->lMargin;
        $w=$this->w-$this->rMargin-$this->x;
        $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
          }
        $nl++;
      }
    else
      $i++; //=$ascii ? 1 : 2;
      }
    //Last chunk
    if($i!=$j)
      $this->UniCell($l/1000*$this->FontSize,$h,mb_substr($s,$j,$i-$j),0,0,'',0,$link);
  }
    function PDFTable($orientation='P',$unit='mm',$format='A4'){
        parent::FPDF($orientation,$unit,$format);
        $this->SetMargins(20,20,20);
        $this->SetAuthor('Pham Minh Dung');
        $this->_makePageSize();
        }

    function SetMargins($left,$top,$right=-1){
        parent::SetMargins($left, $top, $right);
        $this->_makePageSize();
        }

    function SetLeftMargin($margin){
        parent::SetLeftMargin($margin);
        $this->_makePageSize();
        }

    function SetRightMargin($margin){
        parent::SetRightMargin($margin);
        $this->_makePageSize();
        }

    function Header(){
        $this->_makePageSize();
        }

    function _makePageSize(){
        if ($this->CurOrientation=='P'){
            $this->left        = $this->lMargin;
            $this->right    = $this->fw - $this->rMargin;
            $this->top        = $this->tMargin;
            $this->bottom    = $this->fh - $this->bMargin;
            $this->width    = $this->right - $this->left;
            $this->height    = $this->bottom - $this->tMargin;
            }
        else{
            $this->left        = $this->tMargin;
            $this->right    = $this->fh - $this->bMargin;
            $this->top        = $this->rMargin;
            $this->bottom    = $this->fw - $this->rMargin;
            $this->width    = $this->right - $this->left;
            $this->height    = $this->bottom - $this->lMargin;
            }
        }

    function getLineHeight($fontSize = 0){
        if($fontSize == 0) $fontSize = $this->FontSizePt;
        return ($fontSize/$this->k)+1;
        }

    function countLine($w,$txt){
        $cw=&$this->CurrentFont['cw'];
        if($w==0)
            $w=$this->w-$this->rMargin-$this->x;
        $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
        $s=str_replace("\r",'',$txt);
        $nb=strlen($s);
        if($nb>0 and $s[$nb-1]=="\n")
            $nb--;
        $sep=-1;
        $i=$j=$l=0;
        $nl=1;
        while($i<$nb){
            $c=$s[$i];
            if($c=="\n"){
                $i++;
                $sep=-1;
                $j=$i;
                $l=0;
                $nl++;
                continue;
            }
            if($c==' ')
                $sep=$i;
            $l+=$cw[$c];
            if($l>$wmax){
                if($sep==-1){
                    if($i==$j)
                        $i++;
                }
                else
                    $i=$sep+1;
                $sep=-1;
                $j=$i;
                $l=0;
                $nl++;
            }
            else
                $i++;
        }
        return $nl;
        }

    function _tableParser(&$html){
        $align = array('left'=>'L','center'=>'C','right'=>'R','top'=>'T','middle'=>'M','bottom'=>'B');
        $t = new TreeHTML(new HTMLParser($html), 0);
        $row    = $col    = -1;
        $table['nc'] = $table['nr'] = 0;
        $table['repeat'] = array();
        $cell   = array();
        foreach ($t->name as $i=>$element){
            if ($t->type[$i] != NODE_TYPE_ELEMENT && $t->type[$i] != NODE_TYPE_TEXT) continue;
            switch ($element){
                case 'table':
                    $a    = &$t->attribute[$i];
                    if (isset($a['width']))        $table['w']    = intval($a['width']);
                    if (isset($a['height']))    $table['h']    = intval($a['height']);
                    if (isset($a['align']))        $table['a']    = $align[strtolower($a['align'])];
                    $table['border'] = (isset($a['border']))?    $a['border']: 0;
                    if (isset($a['bgcolor']))    $table['bgcolor'][-1]    = $a['bgcolor'];
                    break;
                case 'tr':
                    $row++;
                    $table['nr']++;
                    $col = -1;
                    $a    = &$t->attribute[$i];
                    if (isset($a['bgcolor']))    $table['bgcolor'][$row]    = $a['bgcolor'];
                    if (isset($a['repeat']))    $table['repeat'][]        = $row;
                    break;
                case 'td':
                    $col++;while (isset($cell[$row][$col])) $col++;
                    //Update number column
                    if ($table['nc'] < $col+1)        $table['nc']        = $col+1;
                    $cell[$row][$col] = array();
                    $c = &$cell[$row][$col];
                    $a    = &$t->attribute[$i];
                    $c['text'] = array();
                    $c['s']    = 2;
                    if (isset($a['width']))        $c['w']        = intval($a['width']);
                    if (isset($a['height']))    $c['h']        = intval($a['height']);
                    if (isset($a['align']))        $c['a']        = $align[strtolower($a['align'])];
                    if (isset($a['valign']))    $c['va']    = $align[strtolower($a['valign'])];
                    if (isset($a['border']))    $c['border']    = $a['border'];
                        else                    $c['border']    = $table['border'];
                    if (isset($a['bgcolor']))    $c['bgcolor']    = $a['bgcolor'];
                    $cs = $rs = 1;
                    if (isset($a['colspan']) && $a['colspan']>1)    $cs = $c['colspan']    = intval($a['colspan']);
                    if (isset($a['rowspan']) && $a['rowspan']>1)    $rs = $c['rowspan']    = intval($a['rowspan']);
                    if (isset($a['size']))        $c['fontSize']       = $a['size'];
                    if (isset($a['family']))    $c['fontFamily']     = $a['family'];
                    if (isset($a['style']))        $c['fontStyle']      = $a['style'];
                    if (isset($a['color']))        $c['color']         = $a['color'];
                    //Chiem dung vi tri de danh cho cell span
                    for ($k=$row;$k<$row+$rs;$k++) for($l=$col;$l<$col+$cs;$l++){
                        if ($k-$row || $l-$col)
                            $cell[$k][$l] = 0;
                    }
                    if (isset($a['nowrap']))    $c['nowrap']= 1;
                    break;
                case 'Text':
                    $this->_setTextAndSize($c,$this->_html2text($t->value[$i]));
                    break;
                case 'br':
                    break;
            }
        }
        $table['cells'] = $cell;
        $table['wc']    = array_pad(array(),$table['nc'],array('miw'=>0,'maw'=>0));
        $table['hr']    = array_pad(array(),$table['nr'],0);
        return $table;
        }

    function _setTextAndSize(&$c, $text){
        $c['text'][] = $text;
        $this->_setFontText($c);
        $width =  $this->GetStringWidth($text)+3;
        if (!isset($c['s']) || $c['s'] < $width) $c['s'] = $width;
        }

    function _html2text($text){
        $text = str_replace('&nbsp;',' ',$text);
        $text = str_replace('&lt;','<',$text);
        return $text;
        }

    function _tableColumnWidth(&$table){
        $cs = &$table['cells'];
        $mw = $this->getStringWidth('W');
        $nc = $table['nc'];
        $nr = $table['nr'];
        $listspan = array();
        //Xac dinh do rong cua cac cell va cac cot tuong ung
        for ($j=0;$j<$nc;$j++){
            $wc = &$table['wc'][$j];
            for ($i=0;$i<$nr;$i++){
                if (isset($cs[$i][$j]) && $cs[$i][$j]){
                    $c   = &$cs[$i][$j];
                    $miw = $mw;
                    $c['maw']    = $c['s'];
                    if (isset($c['nowrap']))            $miw = $c['maw'];
                    if (isset($c['w'])){
                        if ($miw<$c['w'])    $c['miw'] = $c['w'];
                        if ($miw>$c['w'])    $c['miw'] = $c['w']      = $miw;
                        if (!isset($wc['w'])) $wc['w'] = 1;
                    }else{
                        $c['miw'] = $miw;
                    }
                    if ($c['maw']  < $c['miw'])            $c['maw']  = $c['miw'];
                    if (!isset($c['colspan'])){
                        if ($wc['miw'] < $c['miw'])        $wc['miw']    = $c['miw'];
                        if ($wc['maw'] < $c['maw'])        $wc['maw']    = $c['maw'];
                    }else $listspan[] = array($i,$j);
                }
            }
        }
        //Xac dinh su anh huong cua cac cell colspan len cac cot va nguoc lai
        $wc = &$table['wc'];
        foreach ($listspan as $_z=>$span){
            list($i,$j) = $span;
            $c = &$cs[$i][$j];
            $lc = $j + $c['colspan'];
            if ($lc > $nc) $lc = $nc;

            $wis = $wisa = 0;
            $was = $wasa = 0;
            $list = array();
            for($k=$j;$k<$lc;$k++){
                $wis += $wc[$k]['miw'];
                $was += $wc[$k]['maw'];
                if (!isset($c['w'])){
                    $list[] = $k;
                    $wisa += $wc[$k]['miw'];
                    $wasa += $wc[$k]['maw'];
                }
            }
            if ($c['miw'] > $wis){
                if (!$wis){//Cac cot chua co kich thuoc => chia deu
                    for($k=$j;$k<$lc;$k++) $wc[$k]['miw'] = $c['miw']/$c['colspan'];
                }elseif (!count($list)){//Khong co cot nao co kich thuoc auto => chia deu phan du cho tat ca
                    $wi = $c['miw'] - $wis;
                    for($k=$j;$k<$lc;$k++)
                        $wc[$k]['miw'] += ($wc[$k]['miw']/$wis)*$wi;
                }else{//Co mot so cot co kich thuoc auto => chia deu phan du cho cac cot auto
                    $wi = $c['miw'] - $wis;
                    foreach ($list as $_z2=>$k)
                        $wc[$k]['miw'] += ($wc[$k]['miw']/$wisa)*$wi;
                }
            }
            if ($c['maw'] > $was){
                if (!$wis){//Cac cot chua co kich thuoc => chia deu
                    for($k=$j;$k<$lc;$k++) $wc[$k]['maw'] = $c['maw']/$c['colspan'];
                }elseif (!count($list)){//Khong co cot nao co kich thuoc auto => chia deu phan du cho tat ca
                    $wi = $c['maw'] - $was;
                    for($k=$j;$k<$lc;$k++)
                        $wc[$k]['maw'] += ($wc[$k]['maw']/$was)*$wi;
                }else{//Co mot so cot co kich thuoc auto => chia deu phan du cho cac cot auto
                    $wi = $c['maw'] - $was;
                    foreach ($list as $_z2=>$k)
                        $wc[$k]['maw'] += ($wc[$k]['maw']/$wasa)*$wi;
                }
            }
        }
    }

    function _tableWidth(&$table){
        $wc = &$table['wc'];
        $nc = $table['nc'];
        $a = 0;
        for ($i=0;$i<$nc;$i++){
            $a += isset($wc[$i]['w']) ? $wc[$i]['miw'] : $wc[$i]['maw'];
        }
        if ($a > $this->width) $table['w'] = $this->width;

        if (isset($table['w'])){
            $wis = $wisa = 0;
            $list = array();
            for ($i=0;$i<$nc;$i++){
                $wis += $wc[$i]['miw'];
                if (!isset($wc[$i]['w'])){ $list[] = $i;$wisa += $wc[$i]['miw'];}
            }
            if ($table['w'] > $wis){
                if (!count($list)){//Khong co cot nao co kich thuoc auto => chia deu phan du cho tat ca
                    //$wi = $table['w'] - $wis;
                    $wi = ($table['w'] - $wis)/$nc;
                    for($k=0;$k<$nc;$k++)
                        //$wc[$k]['miw'] += ($wc[$k]['miw']/$wis)*$wi;
                        $wc[$k]['miw'] += $wi;
                }else{//Co mot so cot co kich thuoc auto => chia deu phan du cho cac cot auto
                    //$wi = $table['w'] - $wis;
                    $wi = ($table['w'] - $wis)/count($list);
                    foreach ($list as $_z2=>$k)
                        //$wc[$k]['miw'] += ($wc[$k]['miw']/$wisa)*$wi;
                        $wc[$k]['miw'] += $wi;
                }
            }
            for ($i=0;$i<$nc;$i++){
                $a = $wc[$i]['miw'];
                unset($wc[$i]);
                $wc[$i] = $a;
            }
        }else{
            $table['w'] = $a;
            for ($i=0;$i<$nc;$i++){
                $a = isset($wc[$i]['w']) ? $wc[$i]['miw'] : $wc[$i]['maw'];
                unset($wc[$i]);
                $wc[$i] = $a;
            }
        }
        $table['w'] = array_sum($wc);
    }

    function _tableHeight(&$table){
        $cs = &$table['cells'];
        $nc = $table['nc'];
        $nr = $table['nr'];
        $listspan = array();
        for ($i=0;$i<$nr;$i++){
            $hr = &$table['hr'][$i];
            for ($j=0;$j<$nc;$j++){
                if (isset($cs[$i][$j]) && $cs[$i][$j]){
                    $c = &$cs[$i][$j];
                    list($x,$cw) = $this->_tableGetWCell($table, $i,$j);

                    $fontSize = (isset($c['fontSize']) && ($c['fontSize'] >0))? $c['fontSize']: 0;

                    $ch = $this->countLine($cw, implode("\n", $c['text'])) * $this->getLineHeight($fontSize);
                    $c['ch'] = $ch;

                    if (isset($c['h']) && $c['h'] > $ch) $ch = $c['h'];

                    if (isset($c['rowspan'])) $listspan[] = array($i,$j);
                    elseif ($hr < $ch)                $hr         = $ch;
                    $c['mih'] = $ch;
                }
            }
        }
        $hr = &$table['hr'];
        foreach ($listspan as $_z=>$span){
            list($i,$j) = $span;
            $c = &$cs[$i][$j];
            $lr = $i + $c['rowspan'];
            if ($lr > $nr) $lr = $nr;
            $hs = $hsa = 0;
            $list = array();
            for($k=$i;$k<$lr;$k++){
                $hs += $hr[$k];
                if (!isset($c['h'])){
                    $list[] = $k;
                    $hsa += $hr[$k];
                }
            }
            if ($c['mih'] > $hs){
                if (!$hs){//Cac dong chua co kich thuoc => chia deu
                    for($k=$i;$k<$lr;$k++) $hr[$k] = $c['mih']/$c['rowspan'];
                }elseif (!count($list)){//Khong co dong nao co kich thuoc auto => chia deu phan du cho tat ca
                    $hi = $c['mih'] - $hs;
                    for($k=$i;$k<$lr;$k++)
                        $hr[$k] += ($hr[$k]/$hs)*$hi;
                }else{//Co mot so dong co kich thuoc auto => chia deu phan du cho cac dong auto
                    $hi = $c['mih'] - $hsa;
                    foreach ($list as $_z2=>$k)
                        $hr[$k] += ($hr[$k]/$hsa)*$hi;
                }
            }
        }
        $table['repeatH'] = 0;
        if (count($table['repeat'])){
            foreach ($table['repeat'] as $_z=>$i) $table['repeatH'] += $hr[$i];
        }else $table['repeat'] = 0;
    }

    /**
     * @desc Xac dinh toa do va do rong cua mot cell
     */
    function _tableGetWCell(&$table, $i,$j){
        $c = &$table['cells'][$i][$j];
        if ($c){
            if (isset($c['x0'])) return array($c['x0'], $c['w0']);
            $x = 0;
            $wc = &$table['wc'];
            for ($k=0;$k<$j;$k++) $x += $wc[$k];
            $w = $wc[$j];
            if (isset($c['colspan'])){
                for ($k=$j+$c['colspan']-1;$k>$j;$k--)
                    $w += $wc[$k];
            }
            $c['x0'] = $x;
            $c['w0'] = $w;
            return array($x, $w);
        }
        return array(0,0);
    }

    function _tableGetHCell(&$table, $i,$j){
        $c = &$table['cells'][$i][$j];
        if ($c){
            if (isset($c['h0'])) return $c['h0'];
            $hr = &$table['hr'];
            $h = $hr[$i];
            if (isset($c['rowspan'])){
                for ($k=$i+$c['rowspan']-1;$k>$i;$k--)
                    $h += $hr[$k];
            }
            $c['h0'] = $h;
            return $h;
        }
        return 0;
    }

    function _tableRect($x, $y, $w, $h, $type=1){
        if (strlen($type)==4)
        {
            $x2 = $x + $w; $y2 = $y + $h;
            if (intval($type{0})) $this->Line($x , $y , $x2, $y );
            if (intval($type{1})) $this->Line($x2, $y , $x2, $y2);
            if (intval($type{2})) $this->Line($x , $y2, $x2, $y2);
            if (intval($type{3})) $this->Line($x , $y , $x , $y2);
        }
        elseif ((int)$type===1)
            $this->Rect($x, $y, $w, $h);
        elseif ((int)$type>1 && (int)$type<11)
        {
            $width = $this->LineWidth;
            $this->SetLineWidth($type * $this->LineWidth);
            $this->Rect($x, $y, $w, $h);
            $this->SetLineWidth($width);
        }
    }

function _tableDrawBorder(&$table){
    foreach ($table['listborder'] as $_z=>$c){
        list($x,$y,$w,$h,$s) = $c;
        $this->_tableRect($x,$y,$w,$h,$s);
        }
    $table['listborder'] = array();
    }

function _tableWriteRow(&$table,$i,$x0){
    $maxh = 0;
    for ($j=0;$j<$table['nc'];$j++){
        $h = $this->_tableGetHCell($table, $i, $j);
        if ($maxh < $h) $maxh = $h;
    }
    if ($table['lasty']+$maxh>$this->bottom && $table['multipage']){
        if ($maxh+$table['repeatH'] > $this->height){
            $msg = 'Height of this row is greater than page height!';
            $h = $this->countLine(0,$msg) * $this->getLineHeight();
            $this->SetFillColor(255,0,0);
            $this->Rect($this->x, $this->y=$table['lasty'], $table['w'], $h, 'F');
            $this->MultiCell($table['w'],$this->getLineHeight(),$msg);
            $table['lasty'] += $h;
            return ;
        }
        $this->_tableDrawBorder($table);
        $this->AddPage($this->CurOrientation);

        $table['lasty'] = $this->y;
        if ($table['repeat']){
            foreach ($table['repeat'] as $_z=>$r){
                if ($r==$i) continue;
                $this->_tableWriteRow($table,$r,$x0);
            }
        }
    }
    $y = $table['lasty'];
    for ($j=0;$j<$table['nc'];$j++){
        if (isset($table['cells'][$i][$j]) && $table['cells'][$i][$j]){
            $c = &$table['cells'][$i][$j];
            list($x,$w) = $this->_tableGetWCell($table, $i, $j);
            $h = $this->_tableGetHCell($table, $i, $j);
            $x += $x0;
            //Align
            $this->x = $x; $this->y = $y;
            $align = isset($c['a'])? $c['a'] : 'L';
            //Vertical align
            $verticalAlign = (!isset($c['va']))? 'T': $c['va'];
            $verticalAlign = (strpos('TMB', $verticalAlign)=== false)? 'T': $verticalAlign;

            if ($verticalAlign == 'M')       $this->y += ($c['mih']>$c['ch'])? ($h-$c['ch'])/2: ($h-$c['mih'])/2;
            elseif ($verticalAlign == 'B') $this->y += ($c['mih']>$c['ch'])? $h-$c['ch']: $h-$c['mih'];
            //Fill
            $fill = isset($c['bgcolor']) ? $c['bgcolor']
                : (isset($table['bgcolor'][$i]) ? $table['bgcolor'][$i]
                : (isset($table['bgcolor'][-1]) ? $table['bgcolor'][-1] : 0));
            if ($fill){
                $color = Color::HEX2RGB($fill);
                $this->SetFillColor($color[0],$color[1],$color[2]);
                $this->Rect($x, $y, $w, $h, 'F');
            };
            //Content
            $f = $this->_setFontText($c);

            if (isset($c['color']))
            {
                $color = Color::HEX2RGB($c['color']);
                $this->SetTextColor($color[0],$color[1],$color[2]);
            }else unset($color);

            $this->MultiCell($w,$this->getLineHeight($f),implode("\n",$c['text']),0,$align);

            if (isset($color))
                $this->SetTextColor(0);

            //Border
            if (isset($c['border'])){
                $table['listborder'][] = array($x,$y,$w,$h,$c['border']);
            }elseif (isset($table['border']) && $table['border'])
                $table['listborder'][] = array($x,$y,$w,$h,$table['border']);
        }
    }
    $table['lasty'] += $table['hr'][$i];
    $this->y = $table['lasty'];
}
function _setFontText(&$c){
    //$count = 0;

    if (isset($c['fontSize']) && ($c['fontSize'] >0)){
        $fontSize   = $c['fontSize'];
        //$count++;
    }else $fontSize   = $this->defaultFontSize;
    if (isset($c['fontFamily'])){
        $fontFamily = $c['fontFamily'];
        //$count++;
    }else $fontFamily = $this->defaultFontFamily;
    if (isset($c['fontStyle'])){
          $STYLE     = explode(",", $c['fontStyle']);
          $fontStyle = '';

          foreach($STYLE AS $si=>$style)  $fontStyle .= strtoupper(substr(trim($style), 0, 1));
         // $count++;
    }else $fontStyle  = $this->defaultFontStyle;

   $this->SetFont($fontFamily, $fontStyle, $fontSize);
   return $fontSize;
}
function _tableWrite(&$table){
    //if ($table['w']>$this->width+5)
    //debug($this->CurOrientation,$table['w'],$this->width);
    if ($this->CurOrientation == 'P' && $table['w']>$this->width+5) $this->AddPage('L');
    $x0 = $this->x;
    $y0 = $this->y;
    if (isset($table['a'])){
        if ($table['a']=='C'){
            $x0 += (($this->right-$x0) - $table['w'])/2;
        }elseif ($table['a']=='R'){
            $x0 = $this->right - $table['w'];
        }
    }
    $table['lasty'] = $y0;
    $table['listborder'] = array();
    for ($i=0;$i<$table['nr'];$i++) $this->_tableWriteRow($table, $i, $x0);
    $this->_tableDrawBorder($table);
    //echo "<pre>";print_r($table);
}

function htmltable(&$html,$multipage=1){
    $a = $this->AutoPageBreak;
    $this->SetAutoPageBreak(0,$this->bMargin);
    $HTML = explode("<table", $html);
    foreach ($HTML as $i=>$table)
    {
        if (strlen($table)<6) continue;
        $table = '<table' . $table;
        $table = $this->_tableParser($table);
        $table['multipage'] = $multipage;
        $this->_tableColumnWidth($table);
        $this->_tableWidth($table);
        $this->_tableHeight($table);
        $this->_tableWrite($table);
    }
    $this->SetAutoPageBreak($a,$this->bMargin);
}


function transhtmltable(&$html,$tableedge=0,$tablefill=0,$multipage=1){  // add by zx 2014-09-18
    $this->MaxY=0;
    $a = $this->AutoPageBreak;
    $this->SetAutoPageBreak(0,$this->bMargin);
    $HTML = explode("<table", $html);
    foreach ($HTML as $i=>$table)
    {
        if (strlen($table)<6) continue;
        $table = '<table' . $table;
        $table = $this->_tableParser($table);
        $table['multipage'] = $multipage;
        $this->_tableColumnWidth($table);
        $this->_tableWidth($table);
        $this->_tableHeight($table);
        //$this->_tableWrite($table);
        $this->_transtableWrite($table,$tableedge,$tablefill);
    }
    $this->SetAutoPageBreak($a,$this->bMargin);
}

function _transtableWrite(&$table,$tableedge,$tablefill){  // add by zx 2014-09-18
    //if ($table['w']>$this->width+5)
    //debug($this->CurOrientation,$table['w'],$this->width);
    if ($this->CurOrientation == 'P' && $table['w']>$this->width+5) $this->AddPage('L');
    $x0 = $this->x;
    $y0 = $this->y;
    if (isset($table['a'])){
        if ($table['a']=='C'){
            $x0 += (($this->right-$x0) - $table['w'])/2;
        }elseif ($table['a']=='R'){
            $x0 = $this->right - $table['w'];
        }
    }
    $table['lasty'] = $y0;
    $table['listborder'] = array();
    //for ($i=0;$i<$table['nr'];$i++) $this->_tableWriteRow($table, $i, $x0);
    for ($i=0;$i<$table['nr'];$i++) $this->_transtableWriteRow($table, $i, $x0,$tableedge,$tablefill);
    //$this->_tableDrawBorder($table);
    //echo "<pre>";print_r($table);
}

function _transtableWriteRow(&$table,$i,$x0,$tableedge,$tablefill){  // add by zx 2014-09-18
    $maxh = 0;
    for ($j=0;$j<$table['nc'];$j++){
        $h = $this->_tableGetHCell($table, $i, $j);
        if ($maxh < $h) $maxh = $h;
    }
    if ($table['lasty']+$maxh>$this->bottom && $table['multipage']){
        if ($maxh+$table['repeatH'] > $this->height){
            $msg = 'Height of this row is greater than page height!';
            $h = $this->countLine(0,$msg) * $this->getLineHeight();
            $this->SetFillColor(255,0,0);
            $this->Rect($this->x, $this->y=$table['lasty'], $table['w'], $h, 'F');
            $this->MultiCell($table['w'],$this->getLineHeight(),$msg);
            $table['lasty'] += $h;
            return ;
        }
        $this->_tableDrawBorder($table);
        $this->AddPage($this->CurOrientation);

        $table['lasty'] = $this->y;
        if ($table['repeat']){
            foreach ($table['repeat'] as $_z=>$r){
                if ($r==$i) continue;
                $this->_tableWriteRow($table,$r,$x0);
            }
        }
    }
    $y = $table['lasty'];
    for ($j=0;$j<$table['nc'];$j++){
        if (isset($table['cells'][$i][$j]) && $table['cells'][$i][$j]){
            $c = &$table['cells'][$i][$j];
            list($x,$w) = $this->_tableGetWCell($table, $i, $j);
            $h = $this->_tableGetHCell($table, $i, $j);
            $x += $x0;
            //Align
            $this->x = $x; $this->y = $y;
            $align = isset($c['a'])? $c['a'] : 'L';
            //Vertical align
            $verticalAlign = (!isset($c['va']))? 'T': $c['va'];
            $verticalAlign = (strpos('TMB', $verticalAlign)=== false)? 'T': $verticalAlign;

            if ($verticalAlign == 'M')       $this->y += ($c['mih']>$c['ch'])? ($h-$c['ch'])/2: ($h-$c['mih'])/2;
            elseif ($verticalAlign == 'B') $this->y += ($c['mih']>$c['ch'])? $h-$c['ch']: $h-$c['mih'];
            //Fill
            $fill = isset($c['bgcolor']) ? $c['bgcolor']
                : (isset($table['bgcolor'][$i]) ? $table['bgcolor'][$i]
                : (isset($table['bgcolor'][-1]) ? $table['bgcolor'][-1] : 0));
            if ($fill){
                $color = Color::HEX2RGB($fill);
                $this->SetFillColor($color[0],$color[1],$color[2]);
                if(($tableedge!=0) || ($tablefill!=0)){
                    $this->SetFillColor($color[0],$color[1],$color[2]);
                    if($tablefill!=0){
                        $this->Rect($x, $y, $w, $h, 'F');
                    }
                    else {
                        $this->Rect($x, $y, $w, $h, '');
                    }
                }
            };
            //Content
            $f = $this->_setFontText($c);

            if (isset($c['color']))
            {
                $color = Color::HEX2RGB($c['color']);
                $this->SetTextColor($color[0],$color[1],$color[2]);
            }else unset($color);

            $this->MultiCell($w,$this->getLineHeight($f),implode("\n",$c['text']),0,$align);

            if($this->MaxY==0){  //add by zx *********************************
                $this->MaxY=$this->y;
            }
            else{
                if($this->y>$this->MaxY){
                    $this->MaxY=$this->y;
                }
            }

            if (isset($color))
                $this->SetTextColor(0);

            //Border
            if (isset($c['border'])){
                $table['listborder'][] = array($x,$y,$w,$h,$c['border']);
            }elseif (isset($table['border']) && $table['border'])
                $table['listborder'][] = array($x,$y,$w,$h,$table['border']);
        }
    }
    $table['lasty'] += $table['hr'][$i];
    $this->y = $table['lasty'];
}


}
?>