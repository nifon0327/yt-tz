<?php 
//电信-EWEN
//代码、数据表共享-EWEN
include "../model/modelhead.php";
$From=$From==""?"read":$From;
$ChooseType=$ChooseType==""?1:$ChooseType;
//需处理参数
$ColsNumber=7;				
$tableMenuS=600;
ChangeWtitle("$SubCompany 未处理传真列表");
$funFrom="fax";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|40|序号|40|传真接收时间|130|传真文档名|170|传真主题|450";
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
	$SearchRows="AND A.Sign=1 AND A.Claimer='' AND A.ClaimDate='0000-00-00 00:00:00'";
	$otherAction="<span onClick='Claim(1)' $onClickCSS>分配</span>&nbsp;&nbsp;&nbsp;";
	//有帐号的员工：本公司员工或分公司员工
	$checkStaff ="SELECT A.Number,A.Name FROM $DataPublic.staffmain A,$DataIn.usertable B WHERE B.Number=A.Number AND A.Estate=1 ORDER BY A.cSign,A.BranchId,A.JobId,A.Number";
	$staffResult = mysql_query($checkStaff); 
	if($staffRow = mysql_fetch_array($staffResult)){
		echo"<select name='Number' id='Number'>";
		echo "<option value='' selected>请选择传真接收员工</option>";
		do{
			$pNumber=$staffRow["Number"];
			$PName=$staffRow["Name"];					
			echo "<option value='$pNumber'>$pNumber-$PName</option>";
			}while ($staffRow = mysql_fetch_array($staffResult));
		echo"</select>";
		}
	}
echo $CencalSstr;
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$Keys=4;$Locks=1;
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT A.Id,A.InDateTime,A.FileName,A.Sign,A.Title FROM $DataPublic.faxdata A WHERE 1 $SearchRows ORDER BY A.Sign DESC,A.InDateTime DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$InDateTime=$myRow["InDateTime"];
		$FileName="<a href='../download/faxfile/$myRow[FileName]' target='_blank'>$myRow[FileName]</a>";
		$Title=$myRow["Title"];
		$TitleText="<input name='Title$i' type='text' id='Title$i' size='83' class='A0000' value='$Title' onChange='ToSaveTitle(this,$Id)'>";
		$ValueArray=array(
			array(0=>$InDateTime, 	1=>"align='center'"),
			array(0=>$FileName,		1=>"align='center'",2=>"onmousedown='window.event.cancelBubble=true;'"),
			array(0=>$TitleText,2=>"onmousedown='window.event.cancelBubble=true;'")
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
echo"<input name='Title' type='hidden' id='Title'>";
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
echo"注意：如果是多页传真，当直接打开看不到其它页时，请下载后转PDF文件即可浏览";
?>
<script>
function Claim(Action){
	//检查是否有选取项目
	var Message="";
	UpdataIdX=0;
	for (var i=0;i<form1.elements.length;i++){
			var e=form1.elements[i];
			if (e.type=="checkbox"){
				e.disabled=false;
				if(e.checked){
					UpdataIdX=UpdataIdX+1;
					} 
				}
			}
	//如果没有选记录
	if(UpdataIdX==0){
		Message="没有选取记录!";
		}
	var NumberValue=document.getElementById('Number').value;	
	if(NumberValue==""){
		Message="没有选取传真接收员工!";
		}
	if(Message!=""){
		alert(Message);
		for (var i=0;i<form1.elements.length;i++){
			var e=form1.elements[i];
			if (e.type=="checkbox"){
				e.disabled=true;
				}
			}
		return false;
		}
	else{
		document.form1.action="fax_updated.php?Action=Claim";
		document.form1.submit();
		}
	}
function ToSaveTitle(thisTemp,IdTemp){
	var TitleTemp=encodeURIComponent(thisTemp.value);
	var url_1="fax_updated.php?TitleTemp="+TitleTemp+"&Id="+IdTemp;
　	var ajax1=InitAjax(); 
　	ajax1.open("GET",url_1,true);
	ajax1.onreadystatechange =function(){
	　　if(ajax1.readyState==4 && ajax1.status ==200 && ajax1.responseText!="" ){
			
			}
		}
	ajax1.send(null);
	}
</script>
