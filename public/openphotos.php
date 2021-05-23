<?php 
//$DataIn.$FileData 二合一已更新
//电信-joseph
include "../basic/chksession.php";
include "../model/loadtype.php";
include "../basic/parameter.inc";
$Download=new download('php,exe,html',false);
function anmaOut($RuleStr,$EncryptStr,$Type){
	$SinkOrder="xacdefghijklmbnopqrstuvwyz";
	$RuleLen = strlen($RuleStr);					//渗透码长度，隔1取1
	for($i=1;$i<$RuleLen;$i++){				
		$inChar=substr($RuleStr,$i,1);				//取出渗透码字符
		$inNum=strpos($SinkOrder,$inChar);			//将 渗透码字母 转为数字
		$oldStr.=substr($EncryptStr,$inNum,1);		//从加密码中读取原文字符
		$i++;
		}
	return $oldStr;
	}
$fArray=explode("|",$f);
$dArray=explode("|",$d);
$sArray=explode("|",$s);

$RuleStr1=$fArray[0];
$EncryptStr1=$fArray[1];
$FileId=anmaOut($RuleStr1,$EncryptStr1,"f");

$RuleStr2=$dArray[0];
$EncryptStr2=$dArray[1];
$FileDir=anmaOut($RuleStr2,$EncryptStr2,"d");

$RuleStr3=$sArray[0];
$EncryptStr3=$sArray[1];
$FileData=anmaOut($RuleStr3,$EncryptStr3,"s");

$FilePath="../$FileDir/";
$checkPicture=mysql_query("SELECT Picture FROM $DataIn.$FileData WHERE Mid='$FileId' ORDER BY Id",$link_id);
if($PictureRow=mysql_fetch_array($checkPicture)){
	echo"<SCRIPT src='../model/pagefun.js' type=text/javascript></script>";
	echo "<body onkeydown='unUseKey()' oncontextmenu='event.returnValue=false' onhelp='return false;'>";
	do{
		$ImgFile=$FilePath.$PictureRow["Picture"];
		echo"<img src='$ImgFile'>";
		}while($PictureRow=mysql_fetch_array($checkPicture));
	echo"</body>";
	}
?>
