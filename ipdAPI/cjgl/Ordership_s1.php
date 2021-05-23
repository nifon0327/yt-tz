<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transition al.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head>
<META content='MSHTML 6.00.2900.2722' name=GENERATOR />
<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
<link rel='stylesheet' href='lightgreen/read_line.css'>
<style type="text/css" media="screen">
	table
	{
		border-collapse: collapse;
	}
	
	tr
	{
		font-size: 14px;
		border: 1px solid black;
	}
</style>
</head>
<body>
<form name='form1' id='form1' enctype='multipart/form-data' method='post' action=''>
<?php 
//权限
include "../../basic/parameter.inc";
include "../../model/modelfunction.php";
//OK
$Th_Col="选项|40|序号|40|PO#|80|订单流水号|80|中文名|280|售价|60|订单数量|60|金额|60|订单日期|70";
$Field=explode("|",$Th_Col);
$Count=count($Field);
$wField=$Field;
$tableWidth=0;
for ($i=0;$i<$Count;$i++){
	$i=$i+1;
	$tableWidth+=$wField[$i];
	}
//查询条件
$SearchRows1=" and S.Estate='2' AND M.CompanyId='$CompanyId'";
$SearchRows2=" and S.Estate='1' AND S.CompanyId='$CompanyId'";
$nowInfo="当前:待出订单资料";
//步骤5：
echo"<table border='1' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>
	<tr bgcolor='#D9D9D9'>
	<td colspan='6' height='40px' class='A1010'>$GysList</td><td colspan='4' align='right' class='A1001'><input name='NowInfo' type='text' id='NowInfo' value='$nowInfo' class='text' disabled></td></tr>";
	//输出表格标题
    echo "<tr>";
	for($i=0;$i<$Count;$i=$i+2){
		$Class_Temp=$i==0?"A1111":"A1101";
		$j=$i;
		$k=$j+1;
		echo"<td width='$Field[$k]' Class='$Class_Temp' height='25px'><div align='center'>$Field[$j]</div></td>";
		}
	echo"</tr>";

if($ShipSign==-1){//待扣项目
	$mySql="SELECT '' AS OrderNumber,S.CompanyId,PO AS OrderPO,S.Date AS OrderDate,'3' AS Type,S.Id,S.Number AS POrderId,'' AS ProductId,S.Qty,S.Price,'' AS PackRemark,S.Description AS cName,S.Description AS eCode 
	FROM $DataIn.ch6_creditnote S WHERE 1 $SearchRows2";
	}
else{	//待出订单和随货样品
	$mySql="
		SELECT M.OrderNumber,M.CompanyId,M.OrderDate,'1' AS Type,S.Id,
		S.OrderPO,S.POrderId,S.ProductId,S.Qty,S.Price,S.PackRemark,P.cName,P.eCode,P.TestStandard 
		FROM $DataIn.yw1_ordersheet S 
		LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
		LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId WHERE 1 $SearchRows1	
	UNION ALL 
		SELECT '' AS OrderNumber,S.CompanyId,S.Date AS OrderDate,'2' AS Type,S.Id,'' AS OrderPO,
		S.SampId AS POrderId,'' AS ProductId,S.Qty,S.Price,'' AS PackRemark,S.SampName AS cName,
		S.Description AS eCode ,'' AS TestStandard 
		FROM $DataIn.ch5_sampsheet S WHERE 1 $SearchRows2";
	}
//echo $mySql;
$i=0;$j=1;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$newMid="";
	do{
		$m=1;
		$Id=$myRow["Id"];	
		$OrderPO=$myRow["OrderPO"]==""?"&nbsp;":$myRow["OrderPO"];
		$OrderDate=$myRow["OrderDate"];
		$POrderId=$myRow["POrderId"];
		$ProductId=$myRow["ProductId"]==""?"&nbsp;":$myRow["ProductId"];
		$Qty=$myRow["Qty"];
		$Price=$myRow["Price"];	
		$Amount=sprintf("%.2f",$Qty*$Price);
		$PackRemark=$myRow["PackRemark"]; 
		$cName=$myRow["cName"]; 
		$eCode=$myRow["eCode"]; 
		$TestStandard=$myRow["TestStandard"];
		$Description=$myRow["Description"];
		$Type=$myRow["Type"];		
		$OrderPO=$Type==2?"随货项目":$OrderPO;
		$Locks=1;
		$LockRemark="";
		if($Type==1){//如果是订单：检查生产数量与需求数量是否一致，如果不一致，不允许选择
			//工序总数
			$CheckgxQty=mysql_fetch_array(mysql_query("SELECT SUM(G.OrderQty) AS gxQty 
				FROM $DataIn.cg1_stocksheet G
				LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
				LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
				WHERE G.POrderId='$POrderId' AND T.mainType=3",$link_id));
			$gxQty=$CheckgxQty["gxQty"];
			//已完成的工序数量
			$CheckscQty=mysql_fetch_array(mysql_query("SELECT SUM(C.Qty) AS scQty FROM $DataIn.sc1_cjtj C 
			LEFT JOIN $DataIn.stufftype T ON C.TypeId=T.TypeId
			WHERE C.POrderId='$POrderId' AND T.Estate=1",$link_id));
			$scQty=$CheckscQty["scQty"];
			if($gxQty!=$scQty){//生产完毕
				$LockRemark="生产登记异常！$gxQty<>$scQty";
				}
			//检查领料记录 备料总数与领料总数比较
			$CheckblQty=mysql_fetch_array(mysql_query("SELECT SUM(G.OrderQty) AS blQty 
				FROM $DataIn.cg1_stocksheet G
				LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
				LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
				WHERE G.POrderId='$POrderId' AND T.mainType=1",$link_id));
			$blQty=$CheckblQty["blQty"];
			$CheckllQty=mysql_fetch_array(mysql_query("SELECT SUM(K.Qty) AS llQty 
				FROM $DataIn.cg1_stocksheet G 										
				LEFT JOIN $DataIn.ck5_llsheet K ON K.StockId = G.StockId 
				LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
				WHERE G.POrderId='$POrderId' AND K.Estate=0",$link_id));
			$llQty=$CheckllQty["llQty"];
			if($blQty!=$llQty){//领料完毕
				$LockRemark.="领料异常！$blQty<>$llQty";
				}
			}
			$disable="";
		    $LockRemark.=$TestStandard==0?"标准图未上传或未通过,禁止出货":"";
		    if($LockRemark!=""){
		    $disable="disabled";
		       }
	       $ValueSTR="$Type^^$Id^^$OrderPO^^ " . str_replace(","," ",$cName) . "^^$eCode^^$Price^^$Qty";
		   $chooseStr="<input name='checkid[$j]' type='checkbox' id='checkid$j' value='$ValueSTR' $disable>";
			//输出明细
				echo"<tr>";
				//$m=$m+2;
				echo"<td class='A0111' height='25'  align='center' width='$Field[$m]'>$chooseStr</td>";
				$m=$m+2;
				echo"<td class='A0101' align='center' width='$Field[$m]'>$j</td>";
				$m=$m+2;
				echo"<td class='A0101' align='center' width='$Field[$m]'>$OrderPO</td>";
				$m=$m+2;
				echo"<td class='A0101' align='center' width='$Field[$m]'>$POrderId</td>";
				$m=$m+2;
				echo"<td class='A0101' width='$Field[$m]' >$cName</td>";
				$m=$m+2;
				echo"<td class='A0101' width='$Field[$m]' align='center'>$Price</td>";
				$m=$m+2;
				echo"<td class='A0101' width='$Field[$m]' align='center'>$Qty</td>";
				$m=$m+2;
				echo"<td  class='A0101' width='$Field[$m]' align='center'>$Amount</td>";	
				$m=$m+2;
				echo"<td  class='A0101' width='$Field[$m]' align='center'>$OrderDate</td>";					
				echo"</tr>";
			    $j++;
		}while($myRow = mysql_fetch_array($myResult));
	    echo"</table>";
	}
if ($j==1){
	echo"<tr><td colspan='11' align='center' height='30' class='A0111' style='background-color: #ffffff'><div class='redB' style='background-color: #ffffff'>没有记录!</div></td></tr>";
	}
echo "</table>";
	?>
</form>
</body>
</html>