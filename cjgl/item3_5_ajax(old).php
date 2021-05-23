<?php
//电信-zxq 2012-08-01
include "cj_chksession.php";

$path = $_SERVER["DOCUMENT_ROOT"];
include_once("$path/public/kqClass/Kq_dailyItem.php");

header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
include "../basic/parameter.inc";
$Th_Col="序号|35|订单PO|60|订单流水号|60|产品名称|250|加工<br>类型|35|加工<br>数量|40|已经<br>登记|45|本次<br>登记|45|登记人|45";
$Field=explode("|",$Th_Col);
$Count=count($Field);
$Cells=$Count/2;
//提示行
echo"<table border='0' cellspacing='0' cellpadding='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#D9D9D9'>
<tr >";
//输出表格标题
for($i=0;$i<$Count;$i=$i+2){
	$Class_Temp=$i==0?"A0111":"A0101";
	$j=$i;
	$k=$j+1;
	echo"<td width='$Field[$k]' class='' height='25px'><div align='center'>$Field[$j]</div></td>";
	}
echo"</tr>";
$i=1;
$TypeStr=$TypeId==0?"":" AND D.TypeId='$TypeId'";
$SearchRows=$GroupId==0?"":" AND D.GroupId='$GroupId' ";
$mySql="SELECT S.OrderPO,P.TestStandard,S.ProductId,P.cName,D.POrderId,D.Qty,D.TypeId
,M.Name,T.TypeName
FROM $DataIn.sc1_cjtj D
LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=D.POrderId
LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
LEFT JOIN $DataPublic.staffmain M ON M.Number=D.Leader
LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId
WHERE 1 $SearchRows  $TypeStr  AND DATE_FORMAT(D.Date,'%Y-%m-%d')='$checkDay' ORDER BY D.Date DESC";
//echo $mySql;

$skipItem = "N";
if($checkDay >= "2014-03" && $factoryCheck == "on")
		{
			$dailyItem = new KqDailyItem("", "", $checkDay);
			$dailyItem->setupDateType($checkDay, "10200",$DataIn, $DataPublic, $link_id);
			//echo $dailyItem->dateType."<br>";
			if($dailyItem->dateType == "X" || $dailyItem->dateType == "Y" || $dailyItem->dateType == "F")
			{
				$skipItem = "Y";
			}
		}


$sumOrderQty=0;
$sumcjtjOverQty=0;
$sumQty=0;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult) && $skipItem == "N"){
	do{
		$m=1;
		$UpdateStr=0;
		$Id=$myRow["Id"];
		$OrderPO=$myRow["OrderPO"];
		$POrderId=$myRow["POrderId"];
		$ProductId=$myRow["ProductId"];
		$TestStandard=$myRow["TestStandard"];
		$cName=$myRow["cName"];
		//include "../admin/Productimage/getPOrderImage.php";//直接调用出问题
       if($TestStandard==1){
	    //输出标准图
         $TestStandard="<span onClick='viewImage(\"$POrderId\",2,1)' style='CURSOR: pointer;color:#FF6633;' title='$TestRemark'>$cName</span>";

	     }
     else{
	        if($TestStandard==2){
		        $TestStandard="<div class='blueB' title='标准图审核中'>$cName</div>";
		        }
	        else{
		        $TestStandard=$cName;
		        }
         }
		$TypeName=substr($myRow["TypeName"],0,6);
		$Qty=$myRow["Qty"];
		$Name=$myRow["Name"];
		$TypeId=$myRow["TypeId"];
		//本类订单总数
		$checkOrderSql=mysql_fetch_array(mysql_query("SELECT SUM(G.OrderQty) AS OrderQty FROM $DataIn.cg1_stocksheet G LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId WHERE 1 AND G.POrderId='$POrderId' AND D.TypeId='$TypeId'",$link_id));
		$OrderQty=$checkOrderSql["OrderQty"];
		//本类登记总数
		$cjtjOverSql=mysql_fetch_array(mysql_query("SELECT SUM(Qty) AS cjtjOverQty FROM $DataIn.sc1_cjtj WHERE 1 AND TypeId='$TypeId' AND POrderId='$POrderId'",$link_id));
		$cjtjOverQty=$cjtjOverSql["cjtjOverQty"];
		if($cjtjOverQty==$OrderQty){
			$OrderQtyStr="<span class='greenB'>$OrderQty</span>";
			}
		else{
			if($cjtjOverQty<$OrderQty){
				$OrderQtyStr="<span class='yellowB'>$OrderQty</span>";
				}
			else{
				$OrderQtyStr="<span class='redB'>◆$OrderQty</span>";
				$UpdateStr=1;
				}
			}
		$POrderId="<a href='item1_ajax?POrderId=$POrderId'   target='_blank'>$POrderId</a>";
		echo"<tr id='Row$i'>";
		echo"<td align='center' height='25' class='A0111'>$i</td>";
		echo"<td align='center' class='A0101'>$OrderPO</td>";
		echo"<td align='center' class='A0101'>$POrderId</td>";
		echo"<td class='A0101'>$TestStandard</td>";
		echo"<td align='center' class='A0101'>$TypeName</td>";
		echo"<td align='right' class='A0101'>$OrderQtyStr</td>";
		echo"<td align='right' class='A0101'>$cjtjOverQty</td>";
		//权限
		$checkPower=mysql_fetch_array(mysql_query("SELECT Action FROM $DataIn.sc4_upopedom WHERE UserId='$Login_Id' AND ModuleId='$fromModuleId'",$link_id));
		$SubAction=$checkPower["Action"];
		$Disabled="disabled";
		if($SubAction==31 && $UpdateStr==1){
			$Disabled="";
			}
		echo"<td class='A0101'><input name='Qty$i' type='text' id='Qty$i' value='$Qty' size='5' onChange='Correct(this,$Id,$OrderQty)' onFocus=toTempValue(this.value) $Disabled></td>";
		echo"<td align='center' class='A0101'>$Name</td>";

		echo"</tr>";
		$i++;
		$sumOrderQty=$sumOrderQty+$OrderQty;
		$sumcjtjOverQty=$sumcjtjOverQty+$cjtjOverQty;
		$sumQty=$sumQty+$Qty;
		}while ($myRow = mysql_fetch_array($myResult));

		echo"<tr id='Row$i'>";
		echo"<td align='center' height='25' class='A0111'>&nbsp;</td>";
		echo"<td align='center' class='A0101'>总计</td>";
		echo"<td align='center' class='A0101'>&nbsp;</td>";
		echo"<td class='A0101'>&nbsp;</td>";
		echo"<td align='center' class='A0101'>&nbsp;</td>";
		echo"<td align='right' class='A0101'>$sumOrderQty</td>";
		echo"<td align='right' class='A0101'>$sumcjtjOverQty</td>";
		echo"<td align='right' class='A0101'>$sumQty</td>";
		echo"<td align='center' class='A0101'>&nbsp;</td>";
	}
else{
	echo"<tr><td colspan='$Cells' align='center' height='30' class='A0111'><div class='redB'>该小组没有生产记录.</div></td></tr>";
	}
echo "</table>";
	?>
