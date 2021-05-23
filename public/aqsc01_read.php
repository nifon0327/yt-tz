<?php 
//2013-10-11 ewen
include "../model/modelhead.php";
//$sumCols="5";		//求和列
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=7;				
$tableMenuS=400;
ChangeWtitle("$SubCompany 安全生产汇编");
$funFrom="aqsc01";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|60|序号|50|分类|60|上级分类|200|分类名称|200|排序|40|状态|50|更新日期|80|操作|60";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,3,4,5,6,7,8";

//步骤3：
include "../model/subprogram/read_model_3.php";
if($From!="slist"){
	$SearchRows="";
	$checkResult = mysql_query("SELECT Grade FROM $DataPublic.aqsc01 GROUP BY Grade ORDER BY Grade",$link_id);
	if($checkRow = mysql_fetch_array($checkResult)) {
		echo"<select name='Grade' id='Grade' onchange='document.form1.submit()'>";
		do{
			$theGrade=$checkRow["Grade"];
			$Grade=$Grade==""?$theGrade:$Grade;
			$GradeName=$theGrade."级分类";
			if($Grade==$theGrade){
				echo "<option value='$theGrade' selected>$GradeName</option>";
				$SearchRows=" AND A.Grade='$theGrade'";
				}
			else{
				echo "<option value='$theGrade'>$GradeName</option>";					
				}
			}while($checkRow = mysql_fetch_array($checkResult));
		echo"</select>&nbsp;";
		}
	}
//步骤4：需处理-条件选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT A.Id,A.Name,A.Grade,A.Sort,A.Estate,A.Locks,A.Date,A.Operator,IFNULL(B.Name,'无') AS PreType FROM $DataPublic.aqsc01 A LEFT JOIN $DataPublic.aqsc01 B ON B.Id=A.PreItem WHERE 1 $SearchRows ORDER BY A.PreItem,A.Sort,A.Id";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do {
		$m=1;
		$SubTB="";
		$Id=$myRow["Id"];
		$PreType=$myRow["PreType"];
		$Name=$myRow["Name"];
		$Sort=$myRow["Sort"];
		$Sort="<input type='text' id='sortId' name='sortId' style='width:40px;text-align: right;' value='$Sort' onblur='updateSort(this,$Id,1,\"\")'>";
		$Grade=$myRow["Grade"];
		$Grade=$Grade."级分类";
		$Estate=$myRow["Estate"]==1?"<div class='greenB'>有效</div>":"<div class='redB'无效</div>";
		$Date=$myRow["Date"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Locks=$myRow["Locks"];
		//检查是否存在2级分类
		$checkSubItemSql=mysql_query("SELECT Id FROM $DataPublic.aqsc01 WHERE PreItem='$Id'",$link_id);
		if($checkSubItemRow=mysql_fetch_array($checkSubItemSql)){
			$showPurchaseorder="<img onClick='Model_ShowOrHide($i,Sub$i,Img_openORclose$i,$Id,0);' name='Img_openORclose$i' src='../images/showtable.gif' title='展开子分类' width='13' height='13' style='CURSOR: pointer'>";
				$SubTB="<table width='$tableWidth' border='0' cellspacing='0' id='Sub$i' style='display:none'><tr bgcolor='#B7B7B7'>
					<td class='A0111' height='30' valign='top' id='SubDiv$i'></td>
					</tr></table>
					";
			}
		else{//如果没有下级分类，则检查是否存在子文件
			$checkSubSql=mysql_query("SELECT Id FROM $DataPublic.aqsc02 WHERE TypeId='$Id'",$link_id);
			if($checkSubRow=mysql_fetch_array($checkSubSql)){
				$showPurchaseorder="<img onClick='Model_ShowOrHide($i,Sub$i,Img_openORclose$i,$Id,1);' name='Img_openORclose$i' src='../images/showtable.gif' title='展开资料' width='13' height='13' style='CURSOR: pointer'>";
				$SubTB="
					<table width='$tableWidth' border='0' cellspacing='0' id='Sub$i' style='display:none'>
					<tr bgcolor='#B7B7B7'>
					<td class='A0111' height='30' valign='top' id='SubDiv$i' align='right'></td></tr></table>";
				}
			else{
				$showPurchaseorder="<img name='Img_openORclose$i' src='../images/spacer.gif' width='13' height='13'>";
				}
			}
		$ValueArray=array(
			array(0=>$Grade),
			array(0=>$PreType),
			array(0=>$Name),
			array(0=>$Sort,1=>"align='center'"),
			array(0=>$Estate,1=>"align='center'"),
			array(0=>$Date,1=>"align='center'"),
			array(0=>$Operator,1=>"align='center'")
			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		echo $SubTB;
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
function Model_ShowOrHide(RowId,e,f,tempValue,Action){//e:隐藏的表格ID;f:展开与收起图片的名称;	tempId:
	TempRowId=RowId;
	e.style.display=(e.style.display=="none")?"":"none";
	var yy=f.src;
	if (yy.indexOf("showtable")==-1){//如果显示的是 收起图片
		f.src="../images/showtable.gif";
		f.title="展开";
		e.myProperty=true;
		}
	else{
		f.src="../images/hidetable.gif";
		f.title="收起";
		e.myProperty=false;
		if(tempValue!=""){
			var url="../public/aqsc01_ajax.php?tempValue="+tempValue+"&RowId="+RowId+"&Action="+Action;
			　	var ajax=InitAjax(); 
			　	ajax.open("GET",url,true);
				ajax.onreadystatechange =function(){
			　		if(ajax.readyState==4){
						eval("SubDiv"+RowId).innerHTML=ajax.responseText;
						}
					}
				ajax.send(null);
			}
		}
	}
function updateSort(e,Id,Action,DB){
     var SortId=e.value;
     var url="aqsc01_sort_ajax.php?Id="+Id+"&Action="+Action+"&SortId="+SortId+"&DB="+DB;
	 var ajax=InitAjax();
　  ajax.open("GET",url,true);
	 ajax.onreadystatechange =function(){
	　　if(ajax.readyState==4 && ajax.status ==200){
			var BackData=ajax.responseText;
             if(BackData=="N"){
                e.value="";
				}
			}
		}
　	ajax.send(null);
}
</script>