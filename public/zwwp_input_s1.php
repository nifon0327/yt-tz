<?php 
//
include "../model/subprogram/s1_model_1.php";
//步骤2：需处理
$Th_Col="选项|40|序号|40|申购日期|75|申购人|50|申购物品名称|200|分类|80|数量|50|单位|50|申购说明|300|申购状态|60|推荐供应商|100";

$ColsNumber=16;
$tableMenuS=600;
$Page_Size = 100;							//每页默认记录数量
$Parameter.=",Bid,$Bid,Jid,$Jid";
//步骤3：
include "../model/subprogram/s1_model_3.php";
//步骤4：可选，其它预设选项
$sSearch=$From!="slist"?"":$sSearch;
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>";
echo $CencalSstr;
//步骤5：
include "../model/subprogram/s1_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT A.Id,A.GoodsId,A.Qty,A.Remark,A.Date,A.Estate,A.Locks,A.Purchaser,B.GoodsName,B.Attached,B.Unit,B.Id AS TId,C.TypeName,D.Forshort,D.Linkman,D.Tel  
	FROM $DataIn.zwwp4_purchase A 
	LEFT JOIN $DataPublic.zwwp3_data B ON B.Id=A.GoodsId 
	LEFT JOIN $DataPublic.zwwp2_subtype C ON C.Id=B.TypeId
	LEFT JOIN $DataPublic.zwwp0_retailer D ON D.CompanyId=B.CompanyId
	WHERE 1 $SearchRows AND A.Estate=3 ORDER BY A.Date DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$Dir=anmaIn("download/zwwp/",$SinkOrder,$motherSTR);
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Date=$myRow["Date"];
		$Operator=$myRow["Purchaser"];
		include "../model/subprogram/staffname.php";
		$GoodsName=$myRow["GoodsName"];
		$TypeName=$myRow["TypeName"];
		$Qty=$myRow["Qty"];
		$Unit=$myRow["Unit"]==""?"<span class='redB'>未设置</span>":$myRow["Unit"];
		$Remark=$myRow["Remark"];
		$LockRemark="";
		
		$Attached=$myRow["Attached"];
		if($Attached==1){
			$Attached="Z".$myRow["TId"].".jpg";
			$Attached=anmaIn($Attached,$SinkOrder,$motherSTR);
			$GoodsName="<span onClick='OpenOrLoad(\"$Dir\",\"$Attached\")' style='CURSOR: pointer;color:#FF6633'>$GoodsName</span>";
			}
		$Locks=1;
		$Estate="<div class='yellowB'>待采购</div>";
		$Forshort=$myRow["Forshort"];
        if($Forshort!=""){
        	$Forshort="<span title='联系人:" . $myRow["Linkman"] . "&#10 联系电话:" . $myRow["Tel"] . "'>$Forshort</span>";
            }
		else{
        	$Forshort="<span class='redB'>未设置</sapn>";
            }
		$ValueArray=array(
			array(0=>$Date,1=>"align='center'"),
			array(0=>$Operator, 1=>"align='center'"),
			array(0=>$GoodsName),
			array(0=>$TypeName),
			array(0=>$Qty,1=>"align='right'"),
			array(0=>$Unit,1=>"align='center'"),
			array(0=>$Remark),
			array(0=>$Estate, 1=>"align='center'"),
            array(0=>$Forshort),
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
?>