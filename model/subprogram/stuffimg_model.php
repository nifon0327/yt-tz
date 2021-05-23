<?php 
include $_SERVER['DOCUMENT_ROOT'] . "/basic/downloadFileIP.php";  //取得下载文档的IP

if ($donwloadFileIP=="") {
	$donwloadFileIP="..";    //无IP，则用原来的方式
	$donwloadFileaddress="$donwloadFileIP/admin/openorload.php";
}

//已更新///电信---yang 20120801
$Picture=$Picture==""?4:$Picture;
if ($ComeFrom=="Supplier"){  //如果来自供应商，则强行显示只是状态为1有
	if(($Picture!=1) && ($Picture!=7)){
		$Picture=0;
  		}
	}
//已更新

switch ($Picture){
	case 0: break;
	case 2:   //新图重新要上传的PDF
		$file=$StuffId;
		$f=anmaIn($file,$SinkOrder,$motherSTR);
		//$StuffCname="<span onClick='OpenOrLoad(\"$d\",\"$f\",7)' style='CURSOR: pointer; color:#F0F; font-weight:bold' title='图片未审核'>$StuffCname</span>";
		$StuffCname="<a href=\"$donwloadFileaddress?d=$d&f=$f&Type=&Action=7\" target=\"download\" style='CURSOR: pointer; color:#F0F; font-weight:bold' title='图片未审核'>$StuffCname</a>";
	    break;
    case 4:  //	JPG
		$checkImgSql=mysql_query("SELECT Id FROM $DataIn.stuffimg WHERE StuffId='$StuffId'",$link_id);
		if($checkImgRow=mysql_fetch_array($checkImgSql)){
			$f=anmaIn($StuffId,$SinkOrder,$motherSTR);
			$StuffCname="<span onClick='OpenOrLoad(\"$d\",\"$f\",\"\",\"stuff\")' style='CURSOR: pointer; background:#FFFF00;  font-weight:bold' title='请重新上传图片' >$StuffCname</span>";
			}
		else{
			if($StuffId==101528){//退款配件
				$StuffCname="<span style='color:FF0000;font-weight: bold;'>$StuffCname</span>";
				}
			else{
				if($mainType>1){//统计和加工分类
					$StuffCname=$mainType==3?"<span style='color:008000;font-weight: bold;'>$StuffCname</span>":"<span style='color:0000CC;font-weight: bold;'>$StuffCname</span>";
					}
				}
			}
		break;
    case 7://	PDF	
		$f=anmaIn($StuffId,$SinkOrder,$motherSTR);
		//$StuffCname="<span onClick='OpenOrLoad(\"$d\",\"$f\",7)' style='CURSOR: pointer;color:#0033FF' title='请重新上传图片'>$StuffCname</span>";  //新图全部是PDF	
		$StuffCname="<a href=\"$donwloadFileaddress?d=$d&f=$f&Type=&Action=7\" target=\"download\" style='CURSOR: pointer; color:#0033FF' title='请重新上传图片'>$StuffCname</a>";
		if($StuffId==101528){//退款配件
			$StuffCname="<span style='color:FF0000;font-weight: bold;'>$StuffCname</span>";
			}
		else{
			if($mainType>1){//统计和加工分类
				$StuffCname=$mainType==3?"<span style='color:008000;font-weight: bold;'>$StuffCname</span>":"<span style='color:0000CC;font-weight: bold;'>$StuffCname</span>";
				}
			}
      break;			
	default:
		$f=anmaIn($StuffId,$SinkOrder,$motherSTR);
		//$StuffCname="<span onClick='OpenOrLoad(\"$d\",\"$f\",7)' style='CURSOR: pointer;color:#FF6633'>$StuffCname</span>";  //新图全部是PDF
		$StuffCname="<a href=\"$donwloadFileaddress?d=$d&f=$f&Type=&Action=7\" target=\"download\" style='CURSOR: pointer; color:#FF6633'>$StuffCname</a>";	
		if($StuffId==101528){//退款配件
			$StuffCname="<span style='color:FF0000;font-weight: bold;'>$StuffCname</span>";
			}
		else{
			if($mainType>1){//统计和加工分类
				$StuffCname=$mainType==3?"<span style='color:008000;font-weight: bold;'>$StuffCname</span>":"<span style='color:0000CC;font-weight: bold;'>$StuffCname</span>";
				}
			}
      break;	
	}
?>