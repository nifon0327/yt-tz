<?php 
/**
 * Su dung cho cac chuc nang lien quan den tieng Viet
 */

/**
 * @desc Code for convert UTF to VNI
 */
$chars_NCR_VNI = array(
	273=>'?, 272=>'?, 225=>'a?, 224=>'a?, 7843=>'a?, 227=>'a?, 7841=>'a?, 259=>'a?, 7855=>'a?,
	7857=>'a?, 7859=>'a?, 7861=>'a?, 7863=>'a?, 226=>'a?, 7845=>'a?, 7847=>'a?, 7849=>'a?, 7851=>'a?,
	7853=>'a?, 233=>'e?, 232=>'e?, 7867=>'e?, 7869=>'e?, 7865=>'e?, 234=>'e?, 7871=>'e?, 7873=>'e?,
	7875=>'e?, 7877=>'e?, 7879=>'e?, 237=>'?, 236=>'?, 7881=>'?, 297=>'?, 7883=>'?, 243=>'o?,
	242=>'o?, 7887=>'o?, 245=>'o?, 7885=>'o?, 7899=>'豉', 7901=>'豇', 7903=>'酐', 7905=>'趱', 7907=>'麸',
	417=>'?, 244=>'o?, 7889=>'o?, 7891=>'o?, 7893=>'o?, 7895=>'o?, 7897=>'o?, 250=>'u?, 249=>'u?,
	7911=>'u?, 361=>'u?, 7909=>'u?, 7913=>'鳄', 7915=>'鲽', 7917=>'鳆', 7919=>'鲺', 7921=>'鲲', 432=>'?,
	253=>'y?, 7923=>'y?, 7927=>'y?, 7929=>'y?, 7925=>'?, 193=>'A?, 192=>'A?, 7842=>'A?, 195=>'A?,
	7840=>'A?, 258=>'A?, 7854=>'A?, 7856=>'A?, 7858=>'A?, 7860=>'A?, 7862=>'A?, 194=>'A?, 7844=>'A?,
	7846=>'A?, 7848=>'A?, 7850=>'A?, 7852=>'A?, 201=>'E?, 200=>'E?, 7866=>'E?, 7868=>'E?, 7864=>'E?,
	202=>'E?, 7870=>'E?, 7872=>'E?, 7874=>'E?, 7876=>'E?, 7878=>'E?, 205=>'?, 204=>'?, 7880=>'?,
	296=>'?, 7882=>'?, 211=>'O?, 210=>'O?, 7886=>'O?, 213=>'O?, 7884=>'O?, 7898=>'再', 7900=>'载',
	7902=>'咱', 7904=>'哉', 7906=>'韵', 416=>'?, 212=>'O?, 7888=>'O?, 7890=>'O?, 7892=>'O?, 7894=>'O?,
	7896=>'O?, 218=>'U?, 217=>'U?, 7910=>'U?, 360=>'U?, 7908=>'U?, 431=>'?, 7912=>'仲', 7914=>'重',
	7916=>'舟', 7918=>'终', 7920=>'窒', 221=>'Y?, 7922=>'Y?, 7926=>'Y?, 7928=>'Y?, 7924=>'?);
/**
 * @desc Code for convert UTF to VNO (tieng Viet ko dau)
 */
$chars_NCR_VN0 = array(
	273=>'d', 272=>'D', 225=>'a', 224=>'a', 7843=>'a', 227=>'a', 7841=>'a', 259=>'a', 7855=>'a',
	7857=>'a', 7859=>'a', 7861=>'a', 7863=>'a', 226=>'a', 7845=>'a', 7847=>'a', 7849=>'a', 7851=>'a',
	7853=>'a', 233=>'e', 232=>'e', 7867=>'e', 7869=>'e', 7865=>'e', 234=>'e', 7871=>'e', 7873=>'e',
	7875=>'e', 7877=>'e', 7879=>'e', 237=>'i', 236=>'i', 7881=>'i', 297=>'i', 7883=>'i', 243=>'o',
	242=>'o', 7887=>'o', 245=>'o', 7885=>'o', 7899=>'o', 7901=>'o', 7903=>'o', 7905=>'o', 7907=>'o',
	417=>'o', 244=>'o', 7889=>'o', 7891=>'o', 7893=>'o', 7895=>'o', 7897=>'o', 250=>'u', 249=>'u',
	7911=>'u', 361=>'u', 7909=>'u', 7913=>'u', 7915=>'u', 7917=>'u', 7919=>'u', 7921=>'u', 432=>'u',
	253=>'y', 7923=>'y', 7927=>'y', 7929=>'y', 7925=>'y', 193=>'A', 192=>'A', 7842=>'A', 195=>'A',
	7840=>'A', 258=>'A', 7854=>'A', 7856=>'A', 7858=>'A', 7860=>'A', 7862=>'A', 194=>'A', 7844=>'A',
	7846=>'A', 7848=>'A', 7850=>'A', 7852=>'A', 201=>'E', 200=>'E', 7866=>'E', 7868=>'E', 7864=>'E',
	202=>'E', 7870=>'E', 7872=>'E', 7874=>'E', 7876=>'E', 7878=>'E', 205=>'I', 204=>'I', 7880=>'I',
	296=>'I', 7882=>'I', 211=>'O', 210=>'O', 7886=>'O', 213=>'O', 7884=>'O', 7898=>'O', 7900=>'O',
	7902=>'O', 7904=>'O', 7906=>'O', 416=>'O', 212=>'O', 7888=>'O', 7890=>'O', 7892=>'O', 7894=>'O',
	7896=>'O', 218=>'U', 217=>'U', 7910=>'U', 360=>'U', 7908=>'U', 431=>'U', 7912=>'U', 7914=>'U',
	7916=>'U', 7918=>'U', 7920=>'U', 221=>'Y', 7922=>'Y', 7926=>'Y', 7928=>'Y', 7924=>'Y');

class VNCode {
	/**
	 *@return int
	 *@desc Tra ve phan tu str[index] va tang index len 1
	 */
	function _nextCode($str, &$index){
		if ($index >= strlen($str)) return 0;
		return ord($str[$index++]);
	}
	
	/**
	 * @return string
	 * @desc Doi chuoi tu dang UTF-8 sang dang Decimal
	 */
	function UTF8_NCR($str){
		$result = '';
		$len = strlen($str);
		for($i=0;$i<$len;){
			$code = VNCode::_nextCode($str,$i);
			if (($code & 0xF0) == 0xF0){//11110000, 4 byte
				$b1 = $code & 0x07; //111
				$code = VNCode::_nextCode($str,$i);
				$b2 = $code & 0x3F; //111111
				$code = VNCode::_nextCode($str,$i);
				$b3 = $code & 0x3F; //111111
				$code = VNCode::_nextCode($str,$i);
				$b4 = $code & 0x3F; //111111
				$code = ((((($b1 << 6) | $b2) << 6) | $b3) << 6) | $b4;
				$result .= '&#'.$code.';';
			}elseif (($code & 0xE0) == 0xE0){//1110000, 3 byte
				$b1 = $code & 0x0F; //1111
				$code = VNCode::_nextCode($str,$i);
				$b2 = $code & 0x3F; //111111
				$code = VNCode::_nextCode($str,$i);
				$b3 = $code & 0x3F; //111111
				$code = ((($b1 << 6) | $b2) << 6) | $b3;
				$result .= '&#'.$code.';';
			}elseif (($code & 0xC0) == 0xC0){//1100000, 2 byte
				$b1 = $code & 0x1F; //11111
				$code = VNCode::_nextCode($str,$i);
				$b2 = $code & 0x3F; //111111
				$code = ($b1 << 6) | $b2;
				$result .= '&#'.$code.';';
			}else{
				$result .= chr($code);
			}
		}
		return $result;
	}
	
	/**
	 * @return string
	 * @desc Doi chuoi tu dang UTF8 sang dang VNI
	 */
	function UTF8VNI($str){
		global $chars_NCR_VNI;
		$result = '';
		$len = strlen($str);
		for($i=0;$i<$len;){
			$code = VNCode::_nextCode($str,$i);
			if (($code & 0xF0) == 0xF0){//11110000, 4 byte
				$b1 = $code & 0x07; //111
				$code = VNCode::_nextCode($str,$i);
				$b2 = $code & 0x3F; //111111
				$code = VNCode::_nextCode($str,$i);
				$b3 = $code & 0x3F; //111111
				$code = VNCode::_nextCode($str,$i);
				$b4 = $code & 0x3F; //111111
				$code = ((((($b1 << 6) | $b2) << 6) | $b3) << 6) | $b4;
				$result .= isset($chars_NCR_VNI[$code]) ? $chars_NCR_VNI[$code] : chr($code);
			}elseif (($code & 0xE0) == 0xE0){//1110000, 3 byte
				$b1 = $code & 0x0F; //1111
				$code = VNCode::_nextCode($str,$i);
				$b2 = $code & 0x3F; //111111
				$code = VNCode::_nextCode($str,$i);
				$b3 = $code & 0x3F; //111111
				$code = ((($b1 << 6) | $b2) << 6) | $b3;
				$result .= isset($chars_NCR_VNI[$code]) ? $chars_NCR_VNI[$code] : chr($code);
			}elseif (($code & 0xC0) == 0xC0){//1100000, 2 byte
				$b1 = $code & 0x1F; //11111
				$code = VNCode::_nextCode($str,$i);
				$b2 = $code & 0x3F; //111111
				$code = ($b1 << 6) | $b2;
				$result .= isset($chars_NCR_VNI[$code]) ? $chars_NCR_VNI[$code] : chr($code);
			}else{
				$result .= chr($code);
			}
		}
		return $result;
	}
	
	/**
	 * Convert UTF to VNO (tieng Viet ko dau)
	 */
	function UTF8VN0($str){
		global $chars_NCR_VN0;
		$result = '';
		$len = strlen($str);
		for($i=0;$i<$len;){
			$code = VNCode::_nextCode($str,$i);
			if (($code & 0xF0) == 0xF0){//11110000, 4 byte
				$b1 = $code & 0x07; //111
				$code = VNCode::_nextCode($str,$i);
				$b2 = $code & 0x3F; //111111
				$code = VNCode::_nextCode($str,$i);
				$b3 = $code & 0x3F; //111111
				$code = VNCode::_nextCode($str,$i);
				$b4 = $code & 0x3F; //111111
				$code = ((((($b1 << 6) | $b2) << 6) | $b3) << 6) | $b4;
				$result .= isset($chars_NCR_VN0[$code]) ? $chars_NCR_VN0[$code] : '?';
			}elseif (($code & 0xE0) == 0xE0){//1110000, 3 byte
				$b1 = $code & 0x0F; //1111
				$code = VNCode::_nextCode($str,$i);
				$b2 = $code & 0x3F; //111111
				$code = VNCode::_nextCode($str,$i);
				$b3 = $code & 0x3F; //111111
				$code = ((($b1 << 6) | $b2) << 6) | $b3;
				$result .= isset($chars_NCR_VN0[$code]) ? $chars_NCR_VN0[$code] : '?';
			}elseif (($code & 0xC0) == 0xC0){//1100000, 2 byte
				$b1 = $code & 0x1F; //11111
				$code = VNCode::_nextCode($str,$i);
				$b2 = $code & 0x3F; //111111
				$code = ($b1 << 6) | $b2;
				$result .= isset($chars_NCR_VN0[$code]) ? $chars_NCR_VN0[$code] : '?';
			}else{
				$result .= chr($code);
			}
		}
		return $result;
	}

	/**
	 * @return string
	 * @desc Doi chuoi tu dang Decimal sang dang UTF-8
	 */
	function NCR_VNI($str){
		global $chars_NCR_VNI;
		$str = trim($str);
		$len = strlen($str);
		$result = '';
		for($i=0;$i<$len;$i++){
			$n = '';
			if ($str[$i] == '&'){
				$k = $i+1;
				if ($k < $len && $str[$k] == '#'){
					$k++;
					while ($k < $len && is_numeric($str[$k]))
						$n .= $str[$k++];
					if ($k < $len && $str[$k]==';')
						$i = $k;
				}
			}
			if ($n!=''){
				$n = intval($n);
				$result .= isset($chars_NCR_VNI[$n]) ? $chars_NCR_VNI[$n] : '?';
			}else
			{
				$result .= $str[$i];
			}
		}
		return $result;
	}

	/**
	 * @return string
	 * @desc Doi chuoi tu dang Decimal sang dang UTF-8
	 */
	function NCR_UTF8($str){
		$len = strlen($str);
		$result = '';
		for($i=0;$i<$len;$i++){
			$n = '';
			if ($str[$i] == '&'){
				$k = $i+1;
				if ($k < $len && $str[$k] == '#'){
					$k++;
					while ($k < $len && is_numeric($str[$k]))
						$n .= $str[$k++];
					if ($k < $len && $str[$k]==';')
						$i = $k;
				}
			}
			if ($n!=''){
				$n = intval($n);
				$s = '';
				$first = 0;
				$mask = 0x80;
				while ($n>0){
					$byte = $n & 0x3F; //00111111
					$n = $n >> 6;
					if ($n) $s = chr($byte | 0x80).$s;
					$first = $first | $mask;
					$mask  = $mask >> 1;
				}
				$s = chr($first | $byte).$s;
				$result .= $s;
			}else
				$result .= $str[$i];
		}
		return $result;
	}

	/**
	 * @return int
	 * @desc Tra ve vi tri that cua chu cai thu index trong chuoi unicode UTF-8
	 */
	function indexUTF8($data, $index){
		$len = strlen($data);
		if ($len==0) return 0;
		for($i=0, $j=0;$i<$len && $j<$index;$j++){
			$code = ord($data{$i++});
			if (($code & 0xF0) == 0xF0){//11110000, 4 byte
				$i += 3;
			}elseif (($code & 0xE0) == 0xE0){//1110000, 3 byte
				$i += 2;
			}elseif (($code & 0xC0) == 0xC0){//1100000, 2 byte
				$i += 1;
			}
		}
		return ($i ? $i-1 : 0);
	}
	
	/**
	 * @return string
	 * @desc Correct a string UTF-8 (error cause by some char is cuted at the end)
	 */
	function correctUTF8($data){
		$len = strlen($data);
		if ($len>=3){
			$code = ord($data{$len-3});
			if (($code & 0xF0) == 0xF0) return substr($data,0,$len-3);
			$code = ord($data{$len-2});
			if (($code & 0xE0) == 0xE0) return substr($data,0,$len-2);
			$code = ord($data{$len-1});
			if (($code & 0xC0) == 0xC0) return substr($data,0,$len-1);
		}
		return $data;
	}
	
	/**
	 * @return string
	 * @desc Correct a string NCR (error cause by some char code >127 and < 255 isnot converted)
	 */
	function correctNCR($data){
		for ($i=128;$i<256;$i++)
			$data = str_replace(chr($i),"&#$i;",$data);
		return $data;
	}
	
	/**
	 * @return string
	 * @desc Cat mot string ngan lai de vua mot cot nho
	 */
	function trunstrword($str, $trunsize=0){
		$size = ($trunsize)? $trunsize : 20;
		if (strlen($str)<=$size) return $str;
		$len = strlen($str);
		for ($r=VNCode::indexUTF8($str,$size); $r<$len && $str[$r]!=' ' && $r<$size+10;$r++);
	
		$s2 = substr($str, 0, $r);
		if (strlen($s2)<strlen($str))
			$s2.='...';
		return $s2;
	}
	
	/**
	 * @return int
	 * @desc Tra ve vi tri that cua chu cai thu index trong chuoi unicode NCR
	 */
	function indexNCR($data, $index){
		$p = 0;
		$len = strlen($data);
		for($i=0;$i<$len && $p<$index;$i++){
			$p++;
			if ($data[$i] == '&'){
				$k = $i+1;
				if ($k<$len && $data[$k] == '#'){
					$k++;
					while ($k<$len && is_numeric($data[$k])) $k++;
					if ($k<$len && $data[$k]==';') $i = $k;
				}
			}
		}
		return $i;
	}
	
	/**
	 * @return int
	 * @desc Tra ve do dai chuoi unicode NCR
	 */
	function strlenNCR($data){
		$p = 0;
		$len = strlen($data);
		for($i=0;$i<$len;$i++){
			$p++;
			if ($data[$i] == '&'){
				$k = $i+1;
				if ($k<$len && $data[$k] == '#'){
					$k++;
					while ($k<$len && is_numeric($data[$k])) $k++;
					if ($k<$len && $data[$k]==';') $i = $k;
				}
			}
		}
		return $p;
	}
	/**
	 * Convert string in NCRx to UTF-8
	 * (NCRx has format --xxx; is an character which has ASCCI code is xxx)
	 */
	function NCRx_UTF8($str){
		$len = strlen($str);
		$result = '';
		$ln=0;
		for($i=0;$i<$len;$i++){
			$n = '';
			if ($str[$i] == '-'){
				$k = $i+1;
				if ($k < $len && $str[$k] == '-'){
					$k++;
					while ($k < $len && is_numeric($str[$k]))
						$n .= $str[$k++];
					if ($k < $len && $str[$k]==';')
						$i = $k;
				}
			}
			if ($n!=''){
				$n = intval($n);
				if ($n==13 || $n==10){
					$result .= $ln?'':'<br>';
					$ln = 1;
				}elseif ($n<128){
					$result .= chr($n);
					$ln = 0;
				}else{
					$s = '';
					$first = 0;
					$mask = 0x80;
					while ($n>0){
						$byte = $n & 0x3F; //00111111
						$n = $n >> 6;
						if ($n) $s = chr($byte | 0x80).$s;
						$first = $first | $mask;
						$mask  = $mask >> 1;
					}
					$s = chr($first | $byte).$s;
					$result .= $s;
					$ln = 0;
				}
			}else{
				$result .= $str[$i];
				$ln = 0;
			}
		}
		return $result;
	}
}
?>