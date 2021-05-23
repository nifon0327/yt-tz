<?php 
/*电信---yang 20120801
$DataIn.sgsdata
$DataIn.trade_object
$DataIn.sgsfile
二珍味一已更新
*/
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=6;				
$tableMenuS=550;
ChangeWtitle("$SubCompany Intertek TEST REPORT");
$funFrom="sgs";
$nowWebPage=$funFrom."_read";
$Th_Col="...|40|Item|30|Intertek NO|120|Intertek test project|520|Picture|100|PDF document|90|Date|80";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="2,3,4,7,8";
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
	$SearchRows="";
	$CheckCompanySql=mysql_query("SELECT D.CompanyId,C.Forshort 
	FROM $DataIn.sgsdata D 
	LEFT JOIN $DataIn.trade_object C ON C.CompanyId=D.CompanyId WHERE 1 AND ObjectSign IN (1,2) GROUP BY D.CompanyId",$link_id);
	if($CheckCompanyRow=mysql_fetch_array($CheckCompanySql)){
		echo"<select name='CompanyId' id='CompanyId' onchange='RefreshPage(\"$nowWebPage\")'>";
		do{
			$theCompanyId=$CheckCompanyRow["CompanyId"];
			$theForshort=$CheckCompanyRow["Forshort"];
			$CompanyId=$CompanyId==""?$theCompanyId:$CompanyId;
			if($CompanyId==$theCompanyId){
				echo"<option value='$theCompanyId' selected>$theForshort</option>";
				$SearchRows.=" AND D.CompanyId='$CompanyId'";
				}
			else{
				echo"<option value='$theCompanyId'>$theForshort</option>";
				}
			}while($CheckCompanyRow=mysql_fetch_array($CheckCompanySql));
		echo"</select>&nbsp;";
		}
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
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
			//$PdfFile="<span onClick='OpenOrLoad(\"$d\",\"$f\",6)' style='CURSOR: pointer;color:#FF6633'>download</span>";
			$PdfFile="<a href=\"openorload.php?d=$d&f=$f&Type=&Action=6\" target=\"download\"  style='CURSOR: pointer;color:#FF6633'>download</a>";
			}
		else{
			$PdfFile="-";
			}			
		$Locks=$myRow["Locks"];
		//检查有没有上传相关图片
		$SgsFile_Result = mysql_query("SELECT * FROM $DataIn.sgsfile WHERE SgsId=$SgsId",$link_id);
		$number=@mysql_num_rows($SgsFile_Result);
		if($SgsFileRow = mysql_fetch_array($SgsFile_Result))
		{
			$Picture=$SgsFileRow["FileName"];
		}
		if ($number>0 && $Picture!=""){
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
		include "../model/subprogram/read_model_6.php";
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
  	}
//步骤7：
echo '</div>';
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>
