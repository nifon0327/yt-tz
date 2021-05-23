<?php 
// 二合一已更新电信---yang 20120801
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=9;				
$tableMenuS=600;
ChangeWtitle("$SubCompany 扫描文档");
$funFrom="branch";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|50|序号|60|扫描时间|200|文档名称|250";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="";
$NowDay=$NowDay==""?date("Y-m-d"):$NowDay;
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
echo"扫描日期:<INPUT name='NowDay' class=textfield id='NowDay' size='12' value='$NowDay' onchange='document.form1.submit()'>";
//步骤5：
include "../model/subprogram/read_model_5.php";

/*
$DataPublic.faxdata	传真列表
*/
$Faxdir="../mcscan/";
$handle=opendir($Faxdir);
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
while($File=readdir($handle)) {
	$FileType=substr($File,-4,4);
	if (($File!=".")and($File!="..")and($File!="Thumbs.db")) {
		$InDateTime=date("Y-m-d H:i:s",filemtime($Faxdir."".$File));
		$ToDay=date("Y-m-d",strtotime($InDateTime));
		if($ToDay==$NowDay){
			$m=1;
			$File="<a href='../mcscan/$File' target='_blank'>$File</a>";
			$ValueArray=array(
				array(0=>$InDateTime, 	1=>"align='center'"),
				array(0=>$File,			1=>"align='center'")
				);
			include "../model/subprogram/read_model_6.php";
			//$i++;
			}
   		}
	}
closedir($handle);
if($i==1){
	noRowInfo($tableWidth);
	}
List_Title($Th_Col,"0",0);
pBottom($i-1,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>