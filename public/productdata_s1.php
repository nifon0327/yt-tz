<?php 
//MC代码不同：BOM表回传参数加入售价电信---yang 20120801
include "../model/subprogram/s1_model_1.php";
//步骤2：需处理
$Th_Col="选项|40|序号|30|客户|50|产品ID|50|中文名|230|Product Code|250|产品属性|80|QC标准图|70|授权书档案|80|产品备注|200|描述|30|参考<br>售价|60|装箱<br>单位|30|外箱条码|150|所属分类|90";
$ColsNumber=17;
$tableMenuS=600;
$Page_Size = 100;							//每页默认记录数量
$isPage=1;//是否分页
//非必选,过滤条件
$Parameter.=",Bid,$Bid";


switch($Action){
	case "2"://来自新增订单
		if($From!=slist){$CompanyIdSTR=" and P.CompanyId=$Bid";}
		$CompanyIdSTR.=" AND P.Estate=1 AND P.ProductId IN(SELECT ProductId FROM $DataIn.pands GROUP BY ProductId ORDER BY ProductId)";
	break;

	case "6"://产品其它操作功能
	  $clearProduct=" AND P.Estate>=1";
	break;
	case "61"://QC标准图 
	     $clearProduct=" AND P.Estate=1";
	break;	
	case "7"://来自BOM设定
		$joinProduct="LEFT JOIN $DataIn.pands S ON S.ProductId=P.ProductId";
		$clearProduct=" AND P.Estate>0 AND S.ProductId IS NULL";
	break;
        case "10"://来自生产工序BOM表
             $CompanyIdSTR.=" AND P.ProductId NOT IN (SELECT ProductId FROM $DataIn.process_bom GROUP BY ProductId ORDER BY ProductId)";
	break;
	} 
	
switch($fSearchPage){
	case "ch_customscode": 
	    $clearProduct.=" AND P.ProductId NOT IN (SELECT ProductId FROM $DataIn.customscode)";
	break;
}  
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

//echo "SearchRows: $SearchRows";
//echo "oldresearch:$oldresearch";

/* 搜索结束 End */

//步骤5：
include "../model/subprogram/s1_model_5.php";
//步骤6：需处理数据记录处理

$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
$mySql="SELECT P.Id,P.ProductId,P.cName,P.eCode,P.Remark,P.pRemark,P.Description,P.Price,P.TestStandard,P.Code,P.Estate,P.PackingUnit,
	P.Unit,P.Date,P.Locks,P.Operator,C.CompanyId,C.Forshort,C.Currency,T.TypeName,D.Attached,P.buySign
	FROM $DataIn.productdata P
	LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId AND  C.ObjectSign IN (1,2)
	LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId
	LEFT JOIN  $DataIn.yw7_clientproduct A ON A.ProductId=P.ProductId
	LEFT JOIN  $DataIn.yw7_clientproxy D  ON D.Id=A.cId
	$joinProduct
	where 1 and C.Estate=1 $uTypeSTR $sSearch $clearProduct $CompanyIdSTR $SearchRows  order by Id DESC";//客户在使用中，记录可用中
//echo "$mySql";	
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$ProductId=$myRow["ProductId"];
		$cName=$myRow["cName"];
		$eCode=$myRow["eCode"]==""?"&nbsp;":$myRow["eCode"];
		$pRemark=$myRow["pRemark"]==""?"&nbsp;":"<span class='redB'>".$myRow["pRemark"]."</span>";
		$Price=$myRow["Price"];
		$buySign=$myRow["buySign"];
        switch($buySign){
               case "0":  $buySign="&nbsp;";break;
               case "1":  $buySign="<span >自购</span>";$buySigntag = '自购';break;
               case "2":  $buySign="<span class='redB'>代购</span>";$buySigntag = '代购';break;
               case "3":  $buySign="<span class='greenB'>客供</span>";$buySigntag = '客供';break;
               default :  $buySigntag = '';
        }
		
		switch($Action){
		case "1"://选择产品以便进行操作
			$Bdata=$ProductId;
			break;
		case"2"://来自新增订单
			$Bdata=$ProductId."^^".$cName."^^".$eCode."^^".$Price;	
			break;
	    case "3"://来自新需求单产品匹配
		case "6"://多选框
        case "10":
		case "61":
			$Bdata=$ProductId."^^".$cName.' '.$buySigntag;
			break;
		case "7"://选择产品以便进行BOM操作
			//读取汇率
			$Currency=$myRow["Currency"];
			$checkRate=mysql_fetch_array(mysql_query("SELECT Rate FROM $DataPublic.currencydata WHERE Id='$Currency'",$link_id));
			$Rate=$checkRate["Rate"];
			$Amount=sprintf("%.4f",$Price*$Rate);
			$Bdata=$ProductId."^^".$Amount."^^".$cName;
			break;
			}		
		$Client=$myRow["Forshort"];
		$Remark=trim($myRow["Remark"])==""?"&nbsp;":"<img src='../images/remark.gif' alt='$myRow[Remark]' width='18' height='18'>";
		$Description=$myRow["Description"]==""?"&nbsp;":"<img src='../images/remark.gif' alt='$myRow[Description]' width='18' height='18'>";
		$Price=$myRow["Price"];
		$Moq=$myRow["Moq"]==0?"&nbsp;":$myRow["Moq"];
		$Currency=$myRow["Currency"];
		$TestStandard=$myRow["TestStandard"];
		
		if($TestStandard==1){
			include "../model/subprogram/teststandard_y.php";
			}
		else{
			$TestStandard=$cName;
			}
		$Code=$myRow["Code"]==""?"&nbsp;":$myRow["Code"];
		$Estate=$myRow["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
		$PackingUnit=$myRow["PackingUnit"];
		$uResult = mysql_query("SELECT Name FROM $DataPublic.packingunit WHERE Id=$PackingUnit order by Id Limit 1",$link_id);
		if($uRow = mysql_fetch_array($uResult)){
			$PackingUnit=$uRow["Name"];
			}			
		$Unit=$myRow["Unit"];
		$Date=$myRow["Date"];
		$Locks=$myRow["Locks"];
		//产品QC检验标准图
         $QCImage="";
         include "../admin/subprogram/product_qcfile.php";
         $QCImage=$QCImage==""?"&nbsp;":$QCImage;
         
		$Attached=$myRow["Attached"];
		$d=anmaIn("download/clientproxy/",$SinkOrder,$motherSTR);		
		if($Attached!=""){
			$f=anmaIn($Attached,$SinkOrder,$motherSTR);
			//$Attached="<span onClick='OpenOrLoad(\"$d\",\"$f\",6)' style='CURSOR: pointer;color:#FF6633'>View</span>";
			$Attached="<a href=\"openorload.php?d=$d&f=$f&Type=&Action=6\" target=\"download\"  style='CURSOR: pointer;color:#FF6633'>View</a>";
			}
		else{
			$Attached="-";
			}		 
		 
		//操作员姓名
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$thisCId=$myRow["CompanyId"];
		$Client=$myRow["Forshort"];
		$TypeName=$myRow["TypeName"];
			$ValueArray=array(
				array(0=>$Client),
				array(0=>$ProductId,			1=>"align='center'"),
				array(0=>$TestStandard,		3=>"..."),
				array(0=>$eCode,				3=>"..."),
				array(0=>$buySign,			1=>"align='center'"),
				array(0=>$QCImage,		1=>"align='center'"),
				array(0=>$Attached,		1=>"align='center'"),
				array(0=>$pRemark),
				array(0=>$Description,		1=>"align='center'"),
				array(0=>$Price."&nbsp;", 	1=>"align='right'"),
				array(0=>$PackingUnit,		1=>"align='center'"),
				array(0=>$Code),
				array(0=>$TypeName,			1=>"align='center'")
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
<script type="text/javascript">   
     //alert("Here12");
	 document.body.scrollTop=0;
	 //window.scrollTo(0,0);
	//window.scrollBy(-(window.screen.height),0);

	 //alert("??");
</script> 