<?php 
/*电信---yang 20120801
$DataPublic.currencydata
$DataIn.pands
$DataIn.productdata
$DataIn.trade_object
$DataIn.bps
$DataIn.stuffdata
$DataPublic.staffmain
$DataIn.trade_object
分开已更新
*/
//步骤1
//毛利行政费用百分比

//步骤2：需处理
$mySql= "SELECT P.Id, P.ProductId, P.cName, P.eCode, P.Code, D.StuffCname
		FROM $DataIn.productdata P
		LEFT JOIN $DataIn.pands A ON A.ProductId = P.ProductId
		LEFT JOIN $DataIn.stuffdata D ON D.StuffId = A.StuffId
		WHERE 1 AND (P.Code = '' OR P.Code IS NULL) AND  D.TypeId =9124 ORDER BY P.Id DESC ";  //未存在的条码D.StuffCname LIKE '条码%' AND D.TypeId =9033 ORDER BY P.Id DESC ";
//echo "$mySql <br>";	
$myResult = mysql_query($mySql,$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		
		$Id=$myRow["Id"];		
		$ProductId=$myRow["ProductId"];
		$eCode=$myRow["eCode"];
		$StuffCname=$myRow["StuffCname"];
                
                $TmpRes=explode("-",$StuffCname);
                $TmpCount=count($TmpRes);

                if($TmpCount<2){
                   $TmpRes=explode("码",$StuffCname); 
                   $TmpCount=count($TmpRes);
                }
                $UpdateFlag=0;
                if($TmpCount>1){
                  $tmpCode=preg_replace( '/[^\d]/ ', '',$TmpRes[$TmpCount-1]);
                  $tmpLen=strlen($tmpCode);
                    if ($ProductId==94233) echo $tmpCode . "  /  " . strlen($tmpCode) . "  / $tmpLen  /  ";
                  if ($tmpLen>11){
                     if ($tmpLen>13){
                         $tmpPos=strpos($tmpCode,'(');
                         if ($tmpPos>0) {$tmpCode=substr($tmpCode,0,$tmpPos-1);$tmpLen=strlen($tmpCode);}
                         $tmpPos=strpos($tmpCode,'码');
                         if ($tmpPos>0) {$TmpRes=explode("码",$tmpCode);$tmpCode=$TmpRes[1];$tmpLen=strlen($tmpCode);}
                     }
                     
                     $tmpCode1=preg_replace( '/[^\d]/ ', '',$tmpCode);
                     $tmpLen1=strlen($tmpCode1);
                      if ($ProductId==94233) echo $tmpCode . "  /  " .  $tmpCode1 . "  /  $tmpLen1<br>";
                     if (strlen($tmpCode1)==$tmpLen && $tmpLen>11) $UpdateFlag=1;
                  }
                }
                if ($UpdateFlag==1){
                   // echo $StuffCname . "-->" . $tmpCode . "</br>";
                    $Code=$eCode.'|'.$tmpCode;
		    $sql = "UPDATE $DataIn.productdata SET Code='$Code' WHERE Id='$Id'  LIMIT 1";
		    $result = mysql_query($sql,$link_id);  //执行，不管是否成功！
                    
                }
		/*$TmpRes=explode("码",$StuffCname);
		//echo "$TmpRes[0] <br>";
		//echo "$TmpRes[1]<br>";
		if(count($TmpRes)==2)
		{
			$StuffCname=$TmpRes[1];
   			$a=ereg('['.chr(0xa1).'-'.chr(0xff).']', $StuffCname);
    		$b=ereg('[0-9]', $StuffCname);
    		$c=ereg('[a-zA-Z]', $StuffCname);

			//$StuffCname=str_replace("条码","",$StuffCname);
			//$StuffCname=str_replace("-","",$StuffCname);
			//echo "S:$StuffCname <br>";
			if(strlen($StuffCname)>=13  && (!$a && $b && !$c) )
			{
				$Code=$eCode.'|'.$StuffCname;
				$sql = "UPDATE $DataIn.productdata SET Code='$Code' WHERE Id='$Id'  LIMIT 1";
				//echo "$sql <br>";
				$result = mysql_query($sql,$link_id);  //执行，不管是否成功！
			}
		}*/
		
	}  while ($myRow = mysql_fetch_array($myResult));
}  
$mySql="";
$myResult="";
$myRow="";
$sql="";

/*
function checkStr($str){
    $output='';
    $a=ereg('['.chr(0xa1).'-'.chr(0xff).']', $str);
    $b=ereg('[0-9]', $str);
    $c=ereg('[a-zA-Z]', $str);
    if($a && $b && $c){ $output='汉字数字英文的混合字符串';}
    elseif($a && $b && !$c){ $output='汉字数字的混合字符串';}
    elseif($a && !$b && $c){ $output='汉字英文的混合字符串';}
    elseif(!$a && $b && $c){ $output='数字英文的混合字符串';}
    elseif($a && !$b && !$c){ $output='纯汉字';}
    elseif(!$a && $b && !$c){ $output='纯数字';}
    elseif(!$a && !$b && $c){ $output='纯英文';}
    return $output;
}

*/


?>
