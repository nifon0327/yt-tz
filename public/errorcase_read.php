<?php 
//电信-ZX
//已更新
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
ChangeWtitle("$SubCompany 检讨报告");
$funFrom="errorcase";
$nowWebPage=$funFrom."_read";
//$Th_Col="选项|60|序号|30|类别|80|检讨主题|340|连接产品/配件数量|120|下载|60|状态|40|更新日期|80|操作员|50";
$Th_Col="选项|60|序号|30|类别|80|检讨主题|340|连接产品/配件数量|120|状态|40|更新日期|80|操作员|50";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,3,4,7,8,82";
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
	$SearchRows="";
	$result = mysql_query("SELECT * FROM $DataPublic.errorcasetype order by Id",$link_id);
	if($myrow = mysql_fetch_array($result)){
	echo"<select name='Type' id='Type' onchange='ResetPage(this.name)'>";
	echo "<option value='' selected>全部</option>";
		do{
			$theTypeId=$myrow["Id"];
			$TypeName=$myrow["Name"];
			//$Type=$Type==""?$theTypeId:$Type;
			if ($Type==$theTypeId){
				echo "<option value='$theTypeId' selected>$TypeName</option>";
				$SearchRows=" AND E.Type='$theTypeId'";
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
if($From=="mcmain"){
	$SearchRows.=" AND E.Estate=1 ";
	}
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT E.Id,E.Title,E.Picture,E.Owner,E.Date,E.Operator,E.Estate,E.Locks,M.Name,T.Name AS TypeName
FROM $DataIn.errorcasedata E 
LEFT JOIN $DataPublic.errorcasetype T ON T.Id=E.Type
LEFT JOIN $DataPublic.staffmain M ON M.Number=E.Operator WHERE 1 $SearchRows ORDER BY E.Date DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$d=anmaIn("download/standarddrawing/",$SinkOrder,$motherSTR);
	do{
		$m=1;
		$Id=$myRow["Id"];
		$TypeName=$myRow["TypeName"];
		$Title=$myRow["Title"];
		$Date=substr($myRow["Date"],0,10);
		$Estate=$myRow["Estate"];
		if($Estate==2){
			$EstateSTR="<span class='yellowB'>审核中</span>";}
		else{
			$EstateSTR="<span class='greenB'>√</span>";}
		$FileName=$myRow["Picture"];
		
		$OFileName="&nbsp;";
		$OFileresult = mysql_query("SELECT FileName FROM $DataIn.doc_standarddrawing where FileType=5 AND  FileRemark=\"$Title\" order by Id desc limit 1",$link_id);
		//echo "SELECT FileName FROM $DataIn.doc_standarddrawing where FileType=5 AND  FileRemark='$Title' order by Id desc limit 1";
		if($Omyrow = mysql_fetch_array($OFileresult)){
			$OFileName=$Omyrow["FileName"];
			$f=anmaIn($OFileName,$SinkOrder,$motherSTR);
			$OFileName="<a href=\"$donwloadFileaddress?d=$d&f=$f&Type=&Action=6\" target=\"download\"><img src='../images/down.gif' style='$style' width='18' height='18'></a>";
			
		}		
		
		//统计连接数量
		$sumResult1=mysql_fetch_array(mysql_query("SELECT count(*) as Qty1 FROM $DataIn.casetoproduct WHERE cId='$Id'",$link_id));
		$sumResult2=mysql_fetch_array(mysql_query("SELECT count(*) as Qty2 FROM $DataIn.casetostuff WHERE cId='$Id'",$link_id));
		$Sum1=$sumResult1["Qty1"];
		$Sum2=$sumResult2["Qty2"];
		$Sum=$Sum1."/".$Sum2;
		$File=anmaIn($FileName,$SinkOrder,$motherSTR);
		$Dir="download/errorcase/";
		$Dir=anmaIn($Dir,$SinkOrder,$motherSTR);			
		//$Title="<span onClick='OpenOrLoad(\"$Dir\",\"$File\")' style='CURSOR: pointer;' class='yellowB'>$Title</span>";
        $Title="<span onClick='viewMistakeImage(\"$Id\",2,1)' style='CURSOR: pointer;' class='yellowN'>$Title</span>";
		$Owner=$myRow["Owner"]==""?"&nbsp":$myRow["Owner"];	
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Locks=$myRow["Locks"];		
		$showPurchaseorder="<img onClick='Show_links(StuffList$i,showtable$i,StuffList$i,\"$Id\",$i);' name='showtable$i' src='../images/showtable.gif' alt='显示或隐藏连接产品和配件明细.' width='13' height='13' style='CURSOR: pointer'>";
		$StuffListTB="
			<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;
			</div><br></td></tr></table>";
		$ValueArray=array(
			array(0=>$TypeName),
			array(0=>$Title),
			//array(0=>$Owner),
			array(0=>$Sum,1=>"align='center'"),
			//array(0=>$Picture,1=>"align='center'"),
			//array(0=>$OFileName,	1=>"align='center'"),
			array(0=>$EstateSTR,1=>"align='center'"),
			array(0=>$Date,					1=>"align='center'"),
			array(0=>$Operator,1=>"align='center'")
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
<script>
function Show_links(e,f,Order_Rows,Id,RowId){
	e.style.display=(e.style.display=="none")?"":"none";
	var yy=f.src;
	if (yy.indexOf("showtable")==-1){
		f.src="../images/showtable.gif";
		Order_Rows.myProperty=true;
		}
	else{
		f.src="../images/hidetable.gif";
		Order_Rows.myProperty=false;
		if(Id!=""){			
			var url="../public/errorcase_linkpro.php?Id="+Id+"&RowId="+RowId; 
		　	var show=eval("showStuffTB"+RowId);
		　	var ajax=InitAjax(); 
		　	ajax.open("GET",url,true);
			ajax.onreadystatechange =function(){
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