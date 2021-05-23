<?php 
include "../model/subprogram/s1_model_1.php";
//步骤2：需处理
$Th_Col="选项|40|序号|40|配件编号|60|非bom配件名称|380|配件条码|100|配件分类|100|采购|60|默认供应商|100|单位|40|货币|30|单价|60|在库|60|采购库存|60|最低库存|60|备注|120|可用状态|50|更新日期|80|操作员|50";
$ColsNumber=16;
$tableMenuS=700;
$Page_Size = 100;							//每页默认记录数量
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
//步骤3：
$SearchRows="";
		$sSearch.=$uType==""?"":" AND A.TypeId=$uType";
switch($fSearchPage){
         case "stuff_die":
           $SearchRows="  AND  B.mainType=8";
          break;
         case "nonbom10":
           $sSearch.="  AND  C.wStockQty>0  AND C.oStockQty>0";
          break;
     }
     if ($GoodsName){
         $SearchRows .= " AND A.GoodsName LIKE '%$GoodsName%'";
     }
include "../model/subprogram/s1_model_3.php";
//步骤4：可选，其它预设选项
echo"<select name='Pagination' id='Pagination' onchange='RefreshPage(\"nonbom4_s1\")'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>$CencalSstr";
//步骤5：
include "../model/subprogram/s1_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);

$mySql="SELECT A.Id,A.GoodsId,A.GoodsName,A.BarCode,A.Price,A.Unit,A.ReturnReasons,A.Attached,A.Estate,A.Locks,A.Date,A.Operator,B.TypeName,C.wStockQty,C.oStockQty,C.mStockQty,D.Forshort,D.CompanyId,E.Name AS Buyer,F.Symbol,A.Remark,A.CkId 
FROM $DataPublic.nonbom4_goodsdata A
LEFT JOIN $DataPublic.nonbom2_subtype B  ON B.Id=A.TypeId
LEFT JOIN $DataPublic.nonbom5_goodsstock C ON C.GoodsId=A.GoodsId
LEFT JOIN $DataPublic.nonbom3_retailermain D ON D.CompanyId=C.CompanyId
LEFT JOIN $DataPublic.staffmain E ON E.Number=B.BuyerId
LEFT JOIN $DataPublic.currencydata F ON F.Id=D.Currency
WHERE 1 $SearchRows  $sSearch AND (B.cSign='0' OR B.cSign='$Login_cSign') AND  A.Estate>0 ORDER BY A.Estate DESC,A.Date DESC,A.GoodsId DESC";

$Keys=31;
//echo "$mySql";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$Dir=anmaIn("download/nonbom/",$SinkOrder,$motherSTR);
	do{
		$m=1;
		$Id=$myRow["Id"];
		$GoodsId=$myRow["GoodsId"];
		$GoodsName=$myRow["GoodsName"];
		$BarCode=$myRow["BarCode"]==""?"&nbsp;":$myRow["BarCode"];
		$TypeName=$myRow["TypeName"]==""?"&nbsp;":$myRow["TypeName"];
        $Remark=$myRow["Remark"]==""?"&nbsp;":$myRow["Remark"];
		$Unit=$myRow["Unit"];
            $PropertyResult=mysql_query("SELECT Id FROM $DataPublic.nonbom4_goodsproperty WHERE GoodsId=$GoodsId AND Property=7",$link_id);  
             if($PropertyRow=mysql_fetch_array($PropertyResult)){
                   $PropertySign=1;
               }
             else  $PropertySign=0;
		$wStockQty=del0($myRow["wStockQty"]);
		$oStockQty=del0($myRow["oStockQty"]);
		$mStockQty=del0($myRow["mStockQty"]);
			$CkId=$myRow["CkId"];		
		$Price=$myRow["Price"];
		$Buyer=$myRow["Buyer"];
		$Forshort=$myRow["Forshort"];
		switch($Action){
		case "6":
			$Bdata=$GoodsId."^^".$GoodsName;
			break;
		case "7":
			$Bdata=$GoodsId."^^".$GoodsName."^^".$Forshort."^^".$Price;
			break;
		case "8":
			$Bdata=$GoodsId."^^".$GoodsName."^^".$Unit."^^".$PropertySign."^^".$CkId;
			break;
		case "10":
			$Bdata=$GoodsId."^^".$GoodsName."^^".$Unit."^^".$PropertySign."^^".$wStockQty."^^".$oStockQty."^^".$mStockQty;
			break;
          }
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
			case 3:
				$ReturnReasons=$myRow["ReturnReasons"]==""?"未填写退回原因":$myRow["ReturnReasons"];
			    $Estate="<img src='../images/warn.gif' title='$ReturnReasons' width='18' height='18'>";
			    break;
			}
		$Date=$myRow["Date"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Attached=$myRow["Attached"];
		if($Attached==1){
			$Attached=$GoodsId.".jpg";
			$Attached=anmaIn($Attached,$SinkOrder,$motherSTR);
			$GoodsName="<span onClick='OpenOrLoad(\"$Dir\",\"$Attached\")' style='CURSOR: pointer;color:#FF6633'>$GoodsName</span>";
			}
		$Symbol=$myRow["Symbol"];
	     include"../model/subprogram/good_Property.php";//非BOM配件属性	
		$CompanyId=$myRow["CompanyId"];
		//加密
		$CompanyId=anmaIn($CompanyId,$SinkOrder,$motherSTR);		
		$Forshort="<a href='nonbom3_view.php?d=$CompanyId' target='_blank'>$Forshort</a>";
		//历史单价
		$Price="<a href='nonbom4_history.php?GoodsId=$GoodsId' target='_blank'>$Price</a>";
		//配件分析
		$GoodsId="<a href='nonbom4_report.php?GoodsId=$GoodsId' target='_blank'>$GoodsId</a>";
		$Locks=$myRow["Locks"];
		$ValueArray=array(
			array(0=>$GoodsId,1=>"align='center'"),
			array(0=>$GoodsName),
            array(0=>$BarCode),
			array(0=>$TypeName),
			array(0=>$Buyer),
			array(0=>$Forshort),
			array(0=>$Unit,1=>"align='center'"),
			array(0=>$Symbol,1=>"align='center'"),
			array(0=>$Price,1=>"align='right'"),
			array(0=>$wStockQty,1=>"align='right'"),
			array(0=>$oStockQty,1=>"align='right'"),
			array(0=>$mStockQty,1=>"align='right'"),
			array(0=>$Remark,1=>"align='center'"),
			array(0=>$Estate,1=>"align='center'"),
			array(0=>$Date,1=>"align='center'"),
			array(0=>$Operator,1=>"align='center'")
			);
		$checkidValue=$Bdata;
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