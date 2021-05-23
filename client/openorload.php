<?php   
//电信-zxq 2012-08-01
//$DataIn.stuffimg 二合一已更新 未验证
include "../model/loadtype.php";
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
$RuleStr1=$fArray[0];
$EncryptStr1=$fArray[1];
$FileName=anmaOut($RuleStr1,$EncryptStr1,"f");
$RuleStr2=$dArray[0];
$EncryptStr2=$dArray[1];
$FileDir=anmaOut($RuleStr2,$EncryptStr2,"d");
switch($Type){
	case "stuff":
		session_start();//注意不能放在顶部，不然下载文件出问题
		include "../basic/parameter.inc";
		$checkImgSql=mysql_query("SELECT Picture FROM $DataIn.stuffimg WHERE StuffId='$FileName' ORDER BY Picture",$link_id);
		if($checkImgRow=mysql_fetch_array($checkImgSql)){
			echo"<SCRIPT src='../model/pagefun.js' type=text/javascript></script>";
			do{
				$FilePath="../$FileDir".$checkImgRow["Picture"];
				echo"<img src='$FilePath'><br>";
				}while ($checkImgRow=mysql_fetch_array($checkImgSql));
			}
		break;
		case "product":	//输出产品高清图
		$FilePath="../".$FileDir.$FileName;
		if(!$Download->downloadfile($FilePath)){ 
			echo $Download->geterrormsg();
			}
		break;

	default:
		$FilePath="../$FileDir".$FileName;
		echo $FilePath;
		if($Action!=6){
			echo"<SCRIPT src='../model/pagefun.js' type=text/javascript></script>";
			echo "<body onkeydown='unUseKey()' oncontextmenu='event.returnValue=false' onhelp='return false;'><img src='$FilePath'></body>";
			}
		else{//下载
			if(!$Download->downloadfile($FilePath)){ 
				echo "DOWNLOAD ERROR!";
				}
		}
	break;
	}
?>
