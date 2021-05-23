<?php   
//电信-zxq 2012-08-01
/*
$DataIn.sgsdata
$DataIn.trade_object
$DataIn.sgsfile
二珍味一已更新
*/
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=8;				
$tableMenuS=550;
ChangeWtitle("$SubCompany SGS TEST REPORT");
$funFrom="sgs";
$nowWebPage=$funFrom."_read";
$Th_Col="&nbsp;|40|Item|30|SGS NO|120|SGS test project|520|Picture|100|PDF document|90|Date|80";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="2,3,4,7,8";
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);


if ($myCompanyId==1004 || $myCompanyId==1059  || $myCompanyId==1072){  //CEL-A OR CEL-B OR CEL-C
    $SearchRows="and (D.CompanyId='1004' OR D.CompanyId='1059'  OR D.CompanyId='1072') ";
   }
else{
	if ($myCompanyId==1081 || $myCompanyId==1002 || $myCompanyId==1080 || $myCompanyId==1065 ) {
		$SearchRows="and D.CompanyId in ('1081','1002','1080','1065')";
	}
	else {
    	$SearchRows="and D.CompanyId='$myCompanyId'";
	}
}

$mySql="SELECT * FROM $DataIn.sgsdata D WHERE 1 $SearchRows ORDER BY D.Date DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$SgsId=$myRow["SgsId"];
		$SgsNo=$myRow["SgsNo"]."/".$myRow["Type"];;
		$ItemE=$myRow["ItemE"];			
		$Date=$myRow["Date"];		
		$PdfFile=$myRow["PdfFile"];
		if($PdfFile!=""){			
			$d=anmaIn("download/sgsreport/",$SinkOrder,$motherSTR);			
			$f=anmaIn($PdfFile,$SinkOrder,$motherSTR);
			$PdfFile="<span onClick='OpenOrLoad(\"$d\",\"$f\",6)' style='CURSOR: pointer;color:#FF6633'>download</span>";
			}
		else{
			$PdfFile="-";
			}			
		$Locks=$myRow["Locks"];
		//检查有没有上传相关图片
		$SgsFile_Result = mysql_query("SELECT * FROM $DataIn.sgsfile WHERE SgsId=$SgsId",$link_id);
		$number=@mysql_num_rows($SgsFile_Result);
		if ($number>0){
			$Content="<a href='sgs_view.php?SgsId=$SgsId' target='_blank'>View($number pages)</a>";}
		else{
			$Content="&nbsp;";}
		$ValueArray=array(
			array(0=>$SgsNo),
			array(0=>$ItemE),
			array(0=>$Content,	1=>"align='center'"),
			array(0=>$PdfFile,	1=>"align='center'"),
			array(0=>$Date,		1=>"align='center'")
			);
		$checkidValue=$Id;
		include "../admin/subprogram/read_model_6.php";
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
  	}
//步骤7：
echo '</div>';
?>
