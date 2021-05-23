<style type="text/css">
<!--
.moveLtoR{ filter:revealTrans(Transition=6,Duration=0.3)};
.moveRtoL{ filter:revealTrans(Transition=7,Duration=0.3)};
/* 为 DIV 加阴影 */ 
.out {position:relative;background:#006633;margin:10px auto;width:400px;}
.in {background:#FFFFE6;border:1px solid #555;padding:10px 5px;position:relative;top:-5px;left:-5px;}  
/* 为 图片 加阴影 */ 
.imgShadow {position:relative;     background:#bbb;      margin:10px auto;     width:220px; } 
.imgContainer {position:relative;      top:-5px;     left:-5px;     background:#fff;      border:1px solid #555;     padding:0; } 
.imgContainer img {     display:block;} 
.glow1 { filter:glow(color=#FF0000,strengh=2)}
.INPUT0000{FONT-SIZE:12px;height:20px;line-height:20px;text-align:center;} 
-->
</style>

<?php 
//步骤1$DataIn.电信---yang 20120801
include "../model/modelhead.php";
echo"<html><head><META content='MSHTML 6.00.2900.2722' name=GENERATOR>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
<link rel='stylesheet' href='../model/css/read_line.css'>
<link rel='stylesheet' href='../model/css/sharing.css'>
<link rel='stylesheet' href='../model/keyright.css'>
<script src='../model/pagefun_Sc.js' type=text/javascript></script>
<script src='../model/checkform.js' type=text/javascript></script>
<script language='javascript' defer='true' type='text/javascript' src='../model/DatePicker/WdatePicker.js'></script></head>";


//步骤2：需处理
$get_JobId=mysql_fetch_array(mysql_query("SELECT JobId FROM $DataPublic.staffmain M WHERE  M.Number=$Login_P_Number",$link_id));
$JobType=$get_JobId[0];
if ($Login_P_Number=='10868') $JobType=3;
$SearchRowsA="";
$SearchRowsA=" AND S.Picture in (0,4,7) AND S.JobId='$JobType' AND S.Estate>0";

$ColsNumber=13;
$tableMenuS=600;
ChangeWtitle("$SubCompany 未传图档配件列表");
$funFrom="stuffdata";
$From=$From==""?"noimg_read":$From;

$Th_Col="选项|55|序号|40|配件Id|45|配件名称|280|图档|30|状态|30|参考买价|60|默认供应商|100|采购|50|规格|120|备注|30|更新日期|70|传图职责|60|操作|50";
//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 200;							//每页默认记录数量
$ActioToS="0,1";
//步骤3：
$nowWebPage=$funFrom."_noimg_read";
include "../model/subprogram/read_model_3.php";

//步骤4：需处理-条件选

if($From!="slist"){
	$SearchRows="";
	$result = mysql_query("SELECT * FROM $DataIn.stufftype WHERE Estate=1 order by Letter",$link_id);
	if($myrow = mysql_fetch_array($result)){
	echo"<select name='StuffType' id='StuffType' onchange='ResetPage(this.name)'><option value='' selected>--配件类型--</option>";
		do{
			$theTypeId=$myrow["TypeId"];
			$TypeName=$myrow["Letter"]."-".$myrow["TypeName"];
			if ($StuffType==$theTypeId){
				echo "<option value='$theTypeId' selected>$TypeName</option>";
				$SearchRows.=" AND S.TypeId='$theTypeId' ";
				}
			else{
				echo "<option value='$theTypeId'>$TypeName</option>";
				}
			}while ($myrow = mysql_fetch_array($result));
			echo "</select>&nbsp;";
		}
	//职位/标记选择
	echo"<select name='JobType' id='JobType' onchange='ResetPage(this.name)' disabled=true>";
	echo"<option value='' >--全部--</option>";
	$mySql="SELECT Id,Name FROM $DataPublic.jobdata  
	         WHERE Estate=1 AND Id in(3,4,6,7) order by Id,Name";
	$result = mysql_query($mySql,$link_id);
	if($myrow = mysql_fetch_array($result)){
		do{
			$jobId=$myrow["Id"];
			$jobName=$myrow["Name"];
			if ($jobId==$JobType){
				echo "<option value='$jobId' selected>$jobName</option>";
				  if ($SearchRowsA==""){
				    $SearchRows.=" AND S.JobId='$jobId' AND S.Estate>0";
				  }
				}
			else{
				echo "<option value='$jobId'>$jobName</option>";
				}
			}while ($myrow = mysql_fetch_array($result));
		}
    echo "</select>&nbsp;";
	//过滤	
	}

  echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页   </option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr	";
 //增加快带查询Search按钮
  $searchtable="stuffdata|S|StuffCname|0"; //快速搜索的表名，字段名. 表名|别名|字段|1  1表示带Estate字段,其它值无
  include "../model/subprogram/QuickSearch.php";
//}	
   echo "<input name='JobIdFlag' type='hidden' id='JobIdFlag' value='$JobType'>";
   echo "<input name='backValue' type='hidden' id='backValue' value=''>";
//步骤5：

include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理

$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
$mySql="SELECT 
	S.Id,S.StuffId,S.StuffCname,S.StuffEname,S.Gfile,S.Gstate,S.Picture,J.Name as Jobname,S.Gremark,S.Estate,S.Price,P.Forshort,M.Name,S.Spec,S.Remark,S.Date,S.Operator,S.Locks	
	FROM $DataIn.stuffdata S 
	LEFT JOIN $DataIn.bps B ON B.StuffId=S.StuffId 
	LEFT JOIN $DataPublic.staffmain M ON M.Number=B.BuyerId 
	LEFT JOIN $DataIn.trade_object P ON P.CompanyId=B.CompanyId 
	LEFT JOIN $DataPublic.jobdata J ON J.Id=S.Jobid 
	 LEFT JOIN $DataIn.stufftype T ON T.TypeId=S.TypeId  
	WHERE 1 $SearchRows $SearchRowsA  AND T.mainType<2 order by S.Estate DESC,S.Id DESC";
// echo "$mySql";	
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);		
	do{
		$m=1;
		$Id=$myRow["Id"];
		$StuffId=$myRow["StuffId"];
		$StuffCname=$myRow["StuffCname"];
		$StuffEname=$myrow["StuffEname"]==""?"&nbsp;":$myrow["StuffEname"];
		$Price=$myRow["Price"];
		//$Spec=$myRow["Spec"]==""?"&nbsp;":"<img src='../images/remark.gif' alt='$myRow[Spec]' width='18' height='18'>";
		$Spec=$myRow["Spec"]==""?"&nbsp;":$myRow["Spec"];
		$Remark=$myRow["Remark"]==""?"&nbsp;":"<img src='../images/remark.gif' alt='$myRow[Remark]' width='18' height='18'>";
		$StuffCname=$myRow["StuffCname"];
		$Picture=$myRow["Picture"];
		switch($Picture){
			 case 1://已上传
			  $Picstyle="style='color:#F63;'";
			  break;
			case 2://图片审核中
				$Picstyle="style='color:#F0F;'";
				break;
			case 4:
			case 7://图片要重新上传
				$Picstyle="style='color:#06C;'";
			break;
		    default:
			  $Picstyle="";
			}
		//$Gfile=$myRow["Gfile"];
		$Gfile="<img src='../images/upFile.jpg' alt='点击上传' width='18' height='18'>";
		$Gstate=$myRow["Gstate"];  //状态
		$Gremark=$myRow["Gremark"];
		//include "../model/subprogram/stuffimg_Gfile.php";	//图档显示			
		include "../model/subprogram/stuffimg_model.php";	//检查是否有图片
		$Estate=$myRow["Estate"];
		switch($Estate){
			case 0:
				$Estate="<div class='redB'>×</div>";
				break;
			case 1:
				$Estate="<div class='greenB'>√</div>";
				break;
			case 2://配件名称审核中
				$Estate="<div class='yellowB' title='配件名称审核中'>√.</div>";
				break;
			}
		
		$Date=substr($myRow["Date"],0,10);
		$Jobname=$myRow["Jobname"];		
		$Jobname=$Jobname==""?"&nbsp;":$Jobname;
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Locks=$myRow["Locks"];
		$Forshort=$myRow["Forshort"];
		$Buyer=$myRow["Name"];


		$URL="Stuffdata_Gfile_ajax.php";
        $theParam="StuffId=$StuffId";
		//echo "$theParam";
		$showPurchaseorder="<img onClick='PubblicShowOrHide(StuffList$i,showtable$i,StuffList$i,\"$URL\",\"$theParam\",$i,\"\");' name='showtable$i' src='../images/showtable.gif' 
		alt='显示或隐藏产品关联的情况.' width='13' height='13' style='CURSOR: pointer'>";
		//echo "PubblicShowOrHide(StuffList$i,showtable$i,StuffList$i,\"$URL\",\"$theParam\",$i,\"\")";
		$StuffListTB="
			<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";
		$ValueArray=array(
			array(0=>$StuffId, 		1=>"align='center'"),
			array(0=>$StuffCname),
			array(0=>$Gfile, 		1=>"align='center'", 2=>"onmousedown='window.event.cancelBubble=true;' onclick='showUpFile($StuffId,$i,4,3)' style='CURSOR: pointer'"),
			array(0=>$Estate,		1=>"align='center'"),
			array(0=>$Price,		1=>"align='center'"),
			array(0=>$Forshort),
			array(0=>$Buyer, 		1=>"align='center'"),
			array(0=>$Spec),
			array(0=>$Remark, 		1=>"align='center'"),
			array(0=>$Date, 		1=>"align='center'"),
			array(0=>$Jobname, 		1=>"align='center' $Picstyle"),
			array(0=>$Operator,		1=>"align='center'")
			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";

			
		echo $StuffListTB;
		
		
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
<script  src='../model/IE_FOX_MASK.js' type=text/javascript></script>   
<script language="JavaScript" type="text/JavaScript">
function showUpFile(StuffId,TableId,RowId,FileRowId){
	var backValId=document.getElementById("backValue");
	backValId.value="";
	var r=Math.random();
	var b=window.showModalDialog("stuffdata_upFile.php?r="+r+"&StuffId="+StuffId+"&ActionId=101",window,"dialogHeight =220px;dialogWidth=400px;center=yes;scroll=no");
	BackValue=backValId.value;
    if (BackValue!=""){
	   var Backdata=BackValue.split("@");
	   switch(Backdata[0]){
		case "Y":
		   var showText="<img src='../images/down.gif' style='background:#F00' alt='已上传,图档未审核' width='18' height='18'>";
		   eval("ListTable"+TableId).rows[0].cells[RowId].innerHTML="<DIV STYLE='overflow: hidden; text-overflow:ellipsis'><NOBR>"+showText+"</NOBR></DIV>";	
		    showText=eval("ListTable"+TableId).rows[0].cells[FileRowId].innerText;
		    showText="<A href='#' onClick=OpenOrLoad('"+Backdata[1]+"','"+Backdata[2]+"',6)  style='color:#F0F;'>"+showText+"</A>";
		    eval("ListTable"+TableId).rows[0].cells[FileRowId].innerHTML=showText;
		   break;
		 case "D":
		   showText="<img src='../images/upFile.jpg' alt='点击上传' width='18' height='18'>";
		   eval("ListTable"+TableId).rows[0].cells[RowId].innerHTML="<DIV STYLE='overflow: hidden; text-overflow:ellipsis'><NOBR>"+showText+"</NOBR></DIV>";	
		   showText=eval("ListTable"+TableId).rows[0].cells[FileRowId].innerText;
		   eval("ListTable"+TableId).rows[0].cells[FileRowId].innerHTML=showText;
		  break;
		default:
		  break;	   
	   }
	}
}
</script>