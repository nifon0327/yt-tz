<?php 
/*电信---yang 20120801
$DataIn.development
$DataIn.trade_object
$DataPublic.staffmain
二合一已更新
*/
//步骤1
include "../model/subprogram/s1_model_1.php";
//步骤2：需处理
$Th_Col="选项|40|序号|40|客户|60|项目编号|60|项目名称|400|内容/图片|60|起始日期|70|结束日期|70|开发|60";
$ColsNumber=9;
$tableMenuS=500;
$Page_Size = 100;							//每页默认记录数量
//非必选,过滤条件
$isPage=0;//是否分页

//步骤3：
include "../model/subprogram/s1_model_3.php";
//步骤4：可选，其它预设选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>";
echo $CencalSstr;
//步骤5：
include "../model/subprogram/s1_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT D.Id,D.ItemId,D.Attached,D.ItemName,D.Content,D.Plan,D.StartDate,D.EndDate,D.Locks,D.Operator,C.Forshort,P.Name 
FROM $DataIn.development D 
LEFT JOIN $DataIn.trade_object C ON D.CompanyId=C.CompanyId 
LEFT JOIN $DataPublic.staffmain P ON P.Number=D.Operator 
WHERE 1 AND (D.Operator=$Login_P_Number OR D.Developer=$Login_P_Number) $sSearch ORDER BY D.ItemId desc";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$ItemId=$myRow["ItemId"];
		$Attached=$ItemId.".jpg";
		$ItemName=$myRow["ItemName"];
		$checkidValue=$ItemId."~".$ItemName;
		//加密字串
		$ItemId=anmaIn($Attached,$SinkOrder,$motherSTR);
		$Dir=anmaIn("f01",$SinkOrder,$motherSTR);
		$Content=trim($myRow["Content"])==""?"&nbsp;":"<img src='../images/remark.gif' alt='$myRow[Content]' width='16' height='16'>";
		$Attached=$myRow["Attached"]==0?"&nbsp;":"<span onClick='OpenOrLoad(\"$Dir\",\"$ItemId\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
		$Plan=$myRow["Plan"]==""?"$myRow[ItemId]":"<span title='开发进度：$myRow[Plan]' style='CURSOR: pointer;color:#FF6633'>$myRow[ItemId]</span>";
		$Locks=1;
		$ValueArray=array(
			0=>array(0=>$myRow["Forshort"],
					 1=>"align='center'"),
			1=>array(0=>$Plan,
					 1=>"align='center'",
					 2=>"onmousedown='window.event.cancelBubble=true;'"),
			2=>array(0=>$ItemName),
			3=>array(0=>"&nbsp;$Content&nbsp;$Attached",
					 2=>"onmousedown='window.event.cancelBubble=true;'"),
			4=>array(0=>$myRow["StartDate"],
					 1=>"align='center'"),
			5=>array(0=>$myRow["EndDate"]=="0000-00-00"?"&nbsp;":$myRow["EndDate"],
					 1=>"align='center'"),
			6=>array(0=>$myRow["Name"],
					 1=>"align='center'")
			);
		include "../model/subprogram/s1_model_6.php";
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