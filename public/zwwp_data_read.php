<?php 
//代码数据共享-EWEN 2012-11-25
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=14;
ChangeWtitle("$SubCompany 总务用品资料");
$funFrom="zwwp_data";
$From=$From==""?"read":$From;
$Th_Col="选项|50|序号|50|物品标识码|100|物品名称|300|物品类别|120|物品图片|60|当前申购|60|已购总数|60|入库总数|60|出库总数|60|在库|60|实际库存|60|可用状态|50|更新日期|80|操作员|60";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
//$ActioToS="1,2,3,4,5,6,7,8";
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>更新中... $CencalSstr";
include "../model/subprogram/read_model_5.php";
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT A.Id,A.GoodsName,A.Attached,A.Estate,A.Locks,A.Date,A.Operator,B.Name AS mainType 
FROM $DataPublic.zwwp3_data A
LEFT JOIN $DataPublic.zwwp2_subtype B  ON B.Id=A.TypeId 
WHERE 1 $SearchRows ORDER BY A.Estate DESC,A.Date DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$GoodsName=$myRow["GoodsName"];
		$mainType=$myRow["mainType"]==""?"&nbsp;":$myRow["mainType"];
		switch($myRow["Estate"]){
		    case 0:
		        $Estate= "<div class='redB'>×</div>";
		         break;
			case 1:
			    $Estate= "<div class='greenB'>√</div>";
			    break;
			case 2:
			    $Estate="<div class='redB'>未审核</div>";
			    break;
		}
		//$Estate=$myRow["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
		$Date=$myRow["Date"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Attached=$myRow["Attached"];
		$Dir=anmaIn("download/zwwp/",$SinkOrder,$motherSTR);
		if($Attached==1){
			$Attached="Z".$Id.".jpg";
			$Attached=anmaIn($Attached,$SinkOrder,$motherSTR);
			$Attached="<span onClick='OpenOrLoad(\"$Dir\",\"$Attached\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
			}
		else{
			$Attached="-";
			}
		$Locks=$myRow["Locks"];
		$ValueArray=array(
			array(0=>"&nbsp;",1=>"align='center'"),
			array(0=>$GoodsName),
            array(0=>$mainType),
			array(0=>$Attached,1=>"align='center'"),
			array(0=>"&nbsp;",1=>"align='center'"),
			array(0=>"&nbsp;",1=>"align='center'"),
			array(0=>"&nbsp;",1=>"align='center'"),
			array(0=>"&nbsp;",1=>"align='center'"),
			array(0=>"&nbsp;",1=>"align='center'"),
			array(0=>"&nbsp;",1=>"align='center'"),
			array(0=>$Estate,1=>"align='center'"),
			array(0=>$Date,1=>"align='center'"),
			array(0=>$Operator,1=>"align='center'")
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