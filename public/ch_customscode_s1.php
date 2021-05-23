<?php
include "../model/subprogram/s1_model_1.php";
$Th_Col="选项|50|序号|40|客户|80|产品类型|120|产品ID|60|中文名|320|eCode|150|海关编码|120|商品名称|120|材质|80|用途|80|品牌授权书|70|备注|60|状态|50|更新日期|80|操作人|60";
$ColsNumber=17;
$tableMenuS=600;
$Page_Size = 100;							//每页默认记录数量
$isPage=1;//是否分页
//非必选,过滤条件
$Parameter.=",Bid,$Bid";
 
//已传入参数：目的查询页面，来源页面，可选记录数，动作，类别uType
$uTypeSTR=$uType==""?"":"and P.TypeId=$uType";
//步骤3：
include "../model/subprogram/s1_model_3.php";
//步骤4：可选，其它预设选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>";

echo $CencalSstr;

/* 搜索开始 Bend*/
$From=$From==""?"s1":$From;
echo "<input name='From' type='hidden' id='From' value='$From'>";
$oldresearch=$oldresearch==""?"$uTypeSTR $sSearch $clearProduct $CompanyIdSTR":$oldresearch;  //把第一次载放时的条件存起来
echo "<input name='oldresearch' type='hidden' id='oldresearch' value='$oldresearch'>";
$searchtable="productdata|P|cName|0|0"; //快速搜索的表名，字段名. 表名|别名|字段|1  1表示带Estate字段,其它值无
include "../model/subprogram/QuickSearch.php";

if ($FromSearch=="FromSearch") {  //来自快速搜索
		$Arraysearch=explode("|",$searchtable);
		$TAsName=$Arraysearch[1];
		$TField=$Arraysearch[2];
		$SearchRows="  AND $TAsName.$TField like '$search%'  ";
		$SearchRows=$oldresearch.$SearchRows;
	}


//步骤5：
include "../model/subprogram/s1_model_5.php";
//步骤6：需处理数据记录处理

$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
$mySql="SELECT H.Id,C.Forshort,T.TypeName,H.HSCode,H.Remark,H.Date,H.Estate,H.Locks,H.Operator,
       P.ProductId,P.cName,P.eCode,P.TestStandard,H.GoodsName,M.Name AS MaterialQ,W.Name AS UseWay
       FROM $DataIn.customscode H
       LEFT JOIN $DataIn.productdata P ON P.ProductId = H.ProductId 
       LEFT JOIN $DataIn.productmq M ON M.Id = P.MaterialQ
       LEFT JOIN $DataIn.productuseway W ON W.Id = P.UseWay
	   LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
       LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId
	   WHERE 1  AND H.GoodsName='' $uTypeSTR $sSearch  $SearchRows  order by Id DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		 $m=1;	
	     $Id=$myRow["Id"];
		 $ProductId=$myRow["ProductId"];
		 $cName=$myRow["cName"];
		 $eCode=$myRow["eCode"]==""?"&nbsp;":$myRow["eCode"];
		 $GoodsName=$myRow["GoodsName"]==""?"&nbsp;":$myRow["GoodsName"];
		 $MaterialQ=$myRow["MaterialQ"]==""?"&nbsp;":$myRow["MaterialQ"];
		 $UseWay=$myRow["UseWay"]==""?"&nbsp;":$myRow["UseWay"];
		 include "../model/subprogram/product_clientproxy.php";//客户授权书
		 $TestStandard=$myRow["TestStandard"];
		 include "../admin/Productimage/getProductImage.php";
		 $TypeName=$myRow["TypeName"];
		 $Forshort=$myRow["Forshort"];
		 $HSCode=$myRow["HSCode"];
		 $Remark=$myRow["Remark"];
		 $Bdata=$ProductId."^^".$cName;
		 $Remark=$Remark==""?"&nbsp;":"<img src='../images/remark.gif' title='$Remark' width='18' height='18'>";
		 $Estate=$myRow["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
		 $Date=$myRow["Date"];
		 $Locks=$myRow["Locks"];
		 $Operator=$myRow["Operator"];
		 include "../model/subprogram/staffname.php";
		 $ValueArray=array(
			array(0=>$Forshort,1=>"align='center'"),
			array(0=>$TypeName,1=>"align='center'"),
			array(0=>$ProductId,1=>"align='center'"),
			array(0=>$TestStandard,1=>"align='left'"),
			array(0=>$eCode,1=>"align='left'"),
			array(0=>$HSCode,1=>"align='center'"),
			array(0=>$GoodsName,1=>"align='center'"),
			array(0=>$MaterialQ,1=>"align='center'"),
			array(0=>$UseWay,1=>"align='center'"),
			array(0=>$clientproxy,1=>"align='center'"),
			array(0=>$Remark,1=>"align='center'"),
			array(0=>$Estate,1=>"align='center'"),
			array(0=>$Date,1=>"align='center'"),
			array(0=>$Operator,1=>"align='center'")
			);
	
		$checkidValue=$Bdata;
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