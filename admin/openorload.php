<?php
defined('IN_COMMON') || include '../basic/common.php';

//电信-joseph
include "../model/loadtype.php";
//include "../model/modelfunction.php";
include "../admin/subprogram/down_Large_File.php";	//下载大文件
//include "../admin/subprogram/down_SLarge_File.php";	//下载大文件

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

function GetIP(){
	if(getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
		$ip = getenv("HTTP_CLIENT_IP");
	else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
			$ip = getenv("HTTP_X_FORWARDED_FOR");
		else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
				$ip = getenv("REMOTE_ADDR");
			else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
					$ip = $_SERVER['REMOTE_ADDR'];
				else
					$ip = "unknown";
					return($ip);
	}

	if (!isset($f)) {
	    $f=$_GET['f'];
	}
	if (!isset($d)) {
	    $d=$_GET['d'];
	}
	if (!isset($Action)) {
	    $Action=$_GET['Action'];
	}
$fArray=explode("|",$f);
$dArray=explode("|",$d);
$RuleStr1=$fArray[0];
$EncryptStr1=$fArray[1];
$FileName=anmaOut($RuleStr1,$EncryptStr1,"f");
$RuleStr2=$dArray[0];
$EncryptStr2=$dArray[1];
$FileDir=anmaOut($RuleStr2,$EncryptStr2,"d");

$TempFile="../$FileDir".$FileName;

if ($arrays!=""){    // add by zx 2011-01-27
	//echo "$arrays";
	$Login_IP=GetIP();
	$webIp="116.6.107.228";
	if(preg_match('/^192\.168/',$Login_IP)){//IP地址为192开头则为内部登录
		$uFrom=1;
		}
	else{
		if($Login_IP==$webIp){				//如果IP地址与公司域名一致，则为内部域名登录
			$uFrom=2;
			}
		else{								//否则为外网登录
			$uFrom=3;
			}
		}
	//echo "uFrom:$uFrom";
	if($uFrom==3){  //如果外部IP则需要写入
		$temparrays=explode("|",$arrays);
		$tempCount=count($temparrays);
		$temparray0=$temparrays[0];
		switch($temparray0){   //来自客户，则需要插入到数据表。
			case "Supplier":  // add by zx 2011-04
				$Date=date("Y-m-d H:i:s");
				$StuffId=$temparrays[1];
				$CompanyId=$temparrays[2];
				$Operator=$temparrays[3];
				include "../basic/parameter.inc";
				$inRecode="INSERT INTO $DataIn.stuffprovider (Id,StuffId,CompanyId,Date,Estate,Operator) VALUES 
			(NULL,'$StuffId','$CompanyId','$Date','1','$Operator')";
				$inAction=mysql_query($inRecode,$link_id);
				//echo "$inRecode";
			break;
		}
	}
}
switch($Type){
    case "invoice":
        $dfname=urldecode($dfname);
        $dfname="../download/invoice/$dfname.pdf";
        downFile($dfname);
        break;
    case "LongFile":
        $dfname=urldecode($dfname);
        $dfname="../$FileDir/$dfname";
		//$dfname="../download/DeliveryNumber/$dfname";
        downFile($dfname);
        break;
	case "pdf":
	    $FilePath="../$FileDir".$FileName;
	    echo  "<script language='javascript'> location.href='$FilePath'</script>  ";
	   break;
	case "ch":
	case "zip":
		$FilePath=$FileDir."/".$FileName;
		downFile($FilePath);
		break;
	case "stuff":	//输出配件图片
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

	 case "cut": //输出配件切割样板图
	     $FilePath="../" . $FileDir."/".$FileName;
		 downFile($FilePath);
	    break;
	case "Limit":
	      $FilePath="../" . $FileDir.$FileName;
	       $img_info = getimagesize($FilePath);
           $imgWidth=$img_info[0];
           $imgWidth=$imgWidth>1024?"width='1024'":"";
	      echo"<img src='$FilePath'  $imgWidth><br>";
	   break;
	   case "qcbad"://输出品检报告不良图片
                   // header("Content-Type:text/html;charset=utf-8");
                    include "../basic/parameter.inc";
                    echo"<style type='text/css'>
                            <!--
                            body {
                                    background-color: #CCCCCC;}
                            -->
                            </style>";
	                echo "<body onkeydown='unUseKey()'  onhelp='return false;'>";//oncontextmenu='event.returnValue=false'
                    $i=1;
                    $checkCaseSql=mysql_query("SELECT B.Id,B.Picture  FROM $DataIn.qc_badrecordsheet B  WHERE B.Id='$Action'  AND B.Picture=1 LIMIT 1",$link_id);
                    if($checkCaseRow=mysql_fetch_array($checkCaseSql)){
                           $Bid=$checkCaseRow["Id"];
                           $FilePath="../download/qcbadpicture/Q".$Bid.".jpg";
                            //限制图片大小宽度为1500px；
                            $img_info = getimagesize($FilePath);
                            $imgWidth=$img_info[0];
                            $imgWidth=$imgWidth>1380?"width='1380'":"";
                            echo"<div><img src='$FilePath' $imgWidth></div><br>";
                            $i++;
                    }

                    $checkPicSql=mysql_query("SELECT F.Picture  FROM $DataIn.qc_badrecordfile F  WHERE F.Mid='$Action' ",$link_id);
                      if($checkPicRow=mysql_fetch_array($checkPicSql)){
                            do{
                                    $FilePath="../download/qcbadpicture/".$checkPicRow["Picture"];
                                     //限制图片大小宽度为1500px；
                                     $img_info = getimagesize($FilePath);
                                     $imgWidth=$img_info[0];
                                     $imgWidth=$imgWidth>1380?"width='1380'":"";
                                     echo"<div height='50px' style='font-size: 26px;color:#FFFFFF;' >$i</div>";
                                     echo"<div><img src='$FilePath' $imgWidth></div><br>";
                                    $i++;
                                    }while($checkPicRow=mysql_fetch_array($checkPicSql));
                     }

                    break;
     case "bjproof":
                 $FilePath="../$FileDir".$FileName;
                  if(!file_exists($FilePath)){
	                  $FilePath="http://113.105.80.227/$FileDir".$FileName;
                  }
                   echo"<div><img src='$FilePath'></div><br>";
                   break;
	default:
		$FilePath="../$FileDir".$FileName;
		if($Action!=6 && $Action!=7){
			echo"<style type='text/css'>
				<!--
				body {
					background-color: #CCCCCC;}
				-->
				</style>";
			echo "<body onkeydown='unUseKey()'  onhelp='return false;'>
			<table border='0' cellpadding='0' cellspacing='0'>";//oncontextmenu='event.returnValue=false'
			if ($Type=="change")  //表示需要更换的标准图 add by zx 2011-01-26
			{
				echo"<tr><td bgcolor='#0000FF' height='50' align='center'><span style='font-size: 36px;color:#0000FF'><img src='../images/changestandard.gif'></span></td></tr>";
			}
			if($Action>1000){
				echo"<tr><td bgcolor='#FF9933' height='50' align='center'><span style='font-size: 36px;color:#FFFFFF'>1</span></td></tr>";
				}
			include "../basic/parameter.inc";//检查检讨报告
			$checkCaseSql=mysql_query("SELECT E.Picture,E.Title FROM $DataIn.casetoproduct C LEFT JOIN $DataIn.errorcasedata  E ON E.Id=C.cId WHERE C.ProductId='$Action'",$link_id);
			if($checkCaseRow=mysql_fetch_array($checkCaseSql)){
				echo"<tr><td><img src='$FilePath'></td></tr>";
				$i=2;
				do{
					$Picture="../download/errorcase/".$checkCaseRow["Picture"];
					echo "<tr><td bgcolor='#FF9933' height='50' align='center'><span style='font-size: 36px;color:#FFFFFF'>$i</span></td></tr><tr><td><img src='$Picture'></td></tr>";
					$i++;
					}while($checkCaseRow=mysql_fetch_array($checkCaseSql));
				}
			else{
				echo"<tr><td><img href='$FilePath' ></td></tr>";
				header("Location: $FilePath"); //pdf预览
				exit();
				}
			echo"</table></body>";
			}
		else{//下载
			if($Action==7){
				$FilePath="../$FileDir".$FileName.".pdf";
				}
			downFile($FilePath);
		}
	break;
	}
 /*
 echo  "<script language='javascript'> window.open( '$FilePath', '_blank ') </script>  ";
 */
?>
