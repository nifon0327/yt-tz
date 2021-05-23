<?php 
//电信-EWEN
include "../model/modelhead.php";
include "../basic/downloadFileIP.php";  //取得下载文档的IP
if ($donwloadFileIP=="") {
	$donwloadFileIP="..";    //无IP，则用原来的方式
	$donwloadFileaddress="$donwloadFileIP/admin/openorload.php";
}
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=8;				
$tableMenuS=550;
ChangeWtitle("$SubCompany QC检验标准图");
$funFrom="Qcstandard";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|60|序号|30|类别|80|标准说明|300|连接产品数量|80|内容(JPG)|50|图档下载|60|状态|60|更新日期|80|操作员|50";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,3,4,5,6,7,8,82,94";
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
	$SearchRows="";
	$result = mysql_query("SELECT TypeId,TypeName FROM $DataIn.producttype order by Id",$link_id);
	if($myrow = mysql_fetch_array($result)){
	echo"<select name='Type' id='Type' onchange='ResetPage(this.name)'>";
	  echo "<option value=''>全 部</option>";
		do{
			$theTypeId=$myrow["TypeId"];
			$TypeName=$myrow["TypeName"];
			//$Type=$Type==""?$theTypeId:$Type;
			if ($Type==$theTypeId){
				echo "<option value='$theTypeId' selected>$TypeName</option>";
				$SearchRows=" AND Q.TypeId='$theTypeId'";
				}
			else{
				echo "<option value='$theTypeId'>$TypeName</option>";
				}
			}while ($myrow = mysql_fetch_array($result));
			echo "</select>&nbsp;";
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
$mySql="SELECT Q.Id,Q.TypeId,Q.Title,Q.Picture,Q.IsType,Q.Estate,Q.Date,Q.Operator,T.TypeName 
FROM $DataIn.qcstandarddata Q
LEFT JOIN $DataIn.producttype T ON T.TypeId=Q.TypeId 
WHERE 1 $SearchRows ORDER BY Q.Estate Desc,Q.Date DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$d=anmaIn("download/standarddrawing/",$SinkOrder,$motherSTR);
	do{
		$m=1;
		$Id=$myRow["Id"];
		$TypeName=$myRow["TypeName"];
		$Title=$myRow["Title"]==""?"&nbsp":$myRow["Title"];	
		$Date=substr($myRow["Date"],0,10);
		$TypeId=$myRow["TypeId"];
		$IsType=$myRow["IsType"];
		$Estate=$myRow["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
		if($Estate==2){
			$EstateSTR="审核中";$ClassColor="blueB";}
		else{
			$EstateSTR="View";$ClassColor="yellowB";}
		$FileName=$myRow["Picture"];
		$File=anmaIn($FileName,$SinkOrder,$motherSTR);
		$Dir="download/QCstandard/";
		$Dir=anmaIn($Dir,$SinkOrder,$motherSTR);			
		$Picture="<span onClick='OpenOrLoad(\"$Dir\",\"$File\")' style='CURSOR: hand;' class='$ClassColor'>$EstateSTR</span>";
		
		$FileName="&nbsp;";
		$Fileresult = mysql_query("SELECT FileName FROM $DataIn.doc_standarddrawing where FileType=2 AND  FileRemark='$Title' order by Id desc limit 1",$link_id);
		if($myrow = mysql_fetch_array($Fileresult)){
			$FileName=$myrow["FileName"];
			$f=anmaIn($FileName,$SinkOrder,$motherSTR);
			$FileName="<a href=\"$donwloadFileaddress?d=$d&f=$f&Type=&Action=6\" target=\"download\"><img src='../images/down.gif' style='$style' width='18' height='18'></a>";
			
		}
		

		
		if ($IsType==1){
			$LinkQty="【类】图";
			}
		else{
			$QtyResult =mysql_fetch_array(mysql_query("SELECT count(*) as Qty FROM $DataIn.qcstandardimg WHERE QcId='$Id'",$link_id));
		    $LinkQty=$QtyResult["Qty"];
		}
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		//动态读取
		$showPurchaseorder="<img onClick='ShowQC(QCList$i,showtable$i,QCList$i,\"$Id|$TypeId|$IsType\",$i);' name='showtable$i' src='../images/showtable.gif' alt='显示此QC标准图的产品.' width='13' height='13' style='CURSOR: hand'>";
		$QCTB="
			<table width='1100' border='0' cellspacing='0' id='QCList$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' height='30'><br><div id='showQCTB$i' width='1100'>&nbsp;</div><br></td></tr></table>";
			
				
		$ValueArray=array(
			array(0=>$TypeName,1=>"align='center'"),
			array(0=>$Title),
			array(0=>$LinkQty,1=>"align='center'"),
			array(0=>$Picture, 1=>"align='center'"),
			array(0=>$FileName,	1=>"align='center'"),
			array(0=>$Estate,1=>"align='center'"),
			array(0=>$Date,					 1=>"align='center'"),
			array(0=>$Operator,1=>"align='center'")
			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		echo $QCTB;
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
  	}
//步骤7：
List_Title($Th_Col,"0",0);
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>
<script>
function ShowQC(e,f,Order_Rows,QCId,RowId){
	e.style.display=(e.style.display=="none")?"":"none";
	var yy=f.src;
	if (yy.indexOf("showtable")==-1){
		f.src="../images/showtable.gif";
		Order_Rows.myProperty=true;
		}
	else{
		f.src="../images/hidetable.gif";
		Order_Rows.myProperty=false;
		//动态加入采购明细
		if(QCId!=""){			
			var url="../public/qcstandard_ajax.php?QCId="+QCId+"&RowId="+RowId; 
		　	var show=eval("showQCTB"+RowId);
		　	var ajax=InitAjax(); 
		　	ajax.open("GET",url,true);
			ajax.onreadystatechange=function(){
		　		if(ajax.readyState==4){// && ajax.status ==200
					var BackData=ajax.responseText;					
					show.innerHTML=BackData;
					}
				}
			ajax.send(null); 
			}
		}
	}
</script>
