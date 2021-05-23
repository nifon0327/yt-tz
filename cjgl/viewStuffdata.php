<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<META content='MSHTML 6.00.2900.2722' name=GENERATOR />
<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
<link rel='stylesheet' href='lightgreen/read_line.css'>
</head>
<body>
<form name='form1' id='form1' enctype='multipart/form-data' method='post' action=''>
<?php
//电信-zxq 2012-08-01
include "../basic/parameter.inc";
include "../model/modelfunction.php";
//OK
if ($Action=="15") $cListTemp="未补数量"; else $cListTemp="参考买价";
$Th_Col="选项|30|序号|40|分类|60|配件Id|45|配件名称|280|$cListTemp|60|默认供应商|100|送货<br/>楼层|40|采购|50|规格|30|备注|30|更新日期|70|操作|50";
$Field=explode("|",$Th_Col);
$Count=count($Field);
$wField=$Field;
$widthArray=array();
for ($i=0;$i<$Count;$i++){
	$i=$i+1;
	$widthArray[]=$wField[$i];
	$tableWidth+=$wField[$i];
	}

//查询条件
$Action=$Action==""?"6":$Action;
$selModel=$selModel=""?"1":$selModel;
$GysList="";
$nowInfo="当前:配件资料";
$SearchRows="";

$result = mysql_query("SELECT * FROM $DataIn.stufftype WHERE Estate=1 AND mainType<2 order by Letter",$link_id);
if($myrow = mysql_fetch_array($result)){
	    $GysList.="<select name='StuffType' id='StuffType' onchange='document.form1.submit();'><option value='' selected>--配件类型--</option>";
		do{
			$theTypeId=$myrow["TypeId"];
			$TypeName=$myrow["Letter"]."-".$myrow["TypeName"];
			//$StuffType=$StuffType==""?$theTypeId:$StuffType;
			if ($StuffType==$theTypeId){
				$GysList.= "<option value='$theTypeId' selected>$TypeName</option>";
				$SearchRows.=" AND S.TypeId='$theTypeId' ";
				}
			else{
				$GysList.= "<option value='$theTypeId'>$TypeName</option>";
				}
			}while ($myrow = mysql_fetch_array($result));
			$GysList.= "</select>&nbsp;";
		}

switch($Action){
   case "9":
   case "15":
	  $SearchRows.= " AND P.CompanyId='$searchSTR' ";
	  $searchSTR="";
	  break;
   default:
      $SearchRows.= " AND (S.StuffCname like '%$searchSTR%' OR S.StuffId like'%$searchSTR%' )";
	  break;
   }

    $GysList1.=" <input name='searchSTR' type='text' id='searchSTR' size='28' value='$searchSTR'>";
    $GysList1.=" <span name='qSearch' id='qSearch' onClick='document.form1.submit();'></span>";
//步骤5：
echo"<table  cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7'>
	<tr>
	<td colspan='4' height='40px' class=''>$GysList</td><td colspan='5' height='40px' class=''>$GysList1</td><td colspan='4' align='right' class=''><input name='NowInfo' type='text' id='NowInfo' value='$nowInfo' class='text' disabled></td></tr>";
	//输出表格标题
    echo "<tr>";
	for($i=0;$i<$Count;$i=$i+2){
		$Class_Temp=$i==0?"A1111":"A1101";
		$j=$i;
		$k=$j+1;
		echo"<td width='$Field[$k]' class='' height='25px' style='background-color: #F0F5F8'><div align='center'>$Field[$j]</div></td>";
		}
	echo"</tr>";

$i=1;
$mySql="SELECT  S.Id,S.StuffId,S.StuffCname,S.Picture,S.Estate,S.Price,M.Number,P.CompanyId,P.Forshort,P.Currency,M.Name,S.Spec,S.Remark,S.SendFloor,S.Date,S.Operator,S.Locks,T.TypeName
	FROM $DataIn.stuffdata S
	LEFT JOIN $DataIn.bps B ON B.StuffId=S.StuffId
	LEFT JOIN $DataIn.staffmain M ON M.Number=B.BuyerId
	LEFT JOIN $DataIn.trade_object P ON P.CompanyId=B.CompanyId
	LEFT JOIN $DataIn.stufftype T ON T.TypeId=S.TypeId 
	WHERE 1 AND S.Estate=1 $SearchRows order by Id DESC";
//echo $mySql;
$myResult = mysql_query($mySql,$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$StuffId=$myRow["StuffId"];
		$StuffCname=$myRow["StuffCname"];
		$Forshort=$myRow["Forshort"];
		$TypeName=$myRow["TypeName"];
		$Buyer=$myRow["Name"];
		$Price=$myRow["Price"];
		$ListFlag=1;
		switch($Action){
		case "2"://多配件操作,如清除BOM配件
			$Bdata=$StuffId;
			break;
		case "3"://产品配件关系设定
			$Currency=$myRow["Currency"];
			$checkRate=mysql_fetch_array(mysql_query("SELECT Rate FROM $DataPublic.currencydata WHERE Id='$Currency'",$link_id));
			$Rate=$checkRate["Rate"];
			$Amount=sprintf("%.4f",$Price*$Rate);
			$Bdata=$TypeName."^^".$StuffId."^^".$StuffCname."^^".$Buyer."^^".$Forshort."^^".$Amount."^^".$Currency;
			break;
		case "4"://需求单配件置换
			$Number=$myRow["Number"];
			$CompanyId=$myRow["CompanyId"];
			$Bdata=$StuffId."^^".$StuffCname."^^".$Price."^^".$Number."^^".$Buyer."^^".$CompanyId."^^".$Forshort;
			break;
		case "5":
			$CompanyId=$myRow["CompanyId"];
			$Bdata=$StuffId."^^".$StuffCname."^^".$Price."^^".$CompanyId."^^".$Forshort;
			break;
		case "6"://选择配件以便进行操作:其它操作
			$Bdata=$StuffId."^^".$StuffCname;

			break;
		case "7"://客户订单》配件需求单异动
			$checkStock=mysql_query("SELECT oStockQty FROM $DataIn.ck9_stocksheet WHERE StuffId='$StuffId' LIMIT 1",$link_id);
			$oStockQty=mysql_result($checkStock,0,"oStockQty");
			$oStockQty=$oStockQty==""?0:$oStockQty;
			$Bdata=$StuffId."^^".$StuffCname."^^".$Price."^^".$oStockQty."^^".$Buyer."^^".$Forshort;
			break;
		case "8"://报废
			$checkStock=mysql_query("SELECT oStockQty,tStockQty FROM $DataIn.ck9_stocksheet WHERE StuffId='$StuffId' LIMIT 1",$link_id);
			$oStockQty=mysql_result($checkStock,0,"oStockQty");
			$oStockQty=$oStockQty==""?0:$oStockQty;
			$tStockQty=mysql_result($checkStock,0,"tStockQty");
			$tStockQty=$tStockQty==""?0:$tStockQty;

			$checkbfStock=mysql_query("SELECT SUM(Qty) AS bfQty FROM $DataIn.ck8_bfsheet  WHERE StuffId='$StuffId' AND Estate=1",$link_id);
			$bfQty=mysql_result($checkbfStock,0,"bfQty");
			if ($bfQty>0) $oStockQty=$oStockQty-$bfQty;

			if ($oStockQty<=0) $ListFlag=0;
			$Bdata=$StuffId."^^".$StuffCname."^^".$oStockQty;
			break;
		 case "9"://退换
		    $checkStock=mysql_query("SELECT tStockQty FROM $DataIn.ck9_stocksheet WHERE StuffId='$StuffId' LIMIT 1",$link_id);
			$tStockQty=mysql_result($checkStock,0,"tStockQty");
			//echo $tStockQty;
			$tStockQty=$tStockQty==""?0:$tStockQty;
			if ($tStockQty<=0) $ListFlag=0;
		    $Bdata=$StuffId."^^".$StuffCname."^^".$tStockQty;
			break;
		case "15"://补仓
		    $bcsql="SELECT (ifnull(B.thQty,0)-ifnull(A.bcQty,0)) AS unQty 
		        FROM (SELECT StuffId,SUM(Qty) AS thQty FROM $DataIn.ck2_thsheet WHERE StuffId='$StuffId' AND Estate = 0  GROUP BY StuffId)B 
	            LEFT JOIN (SELECT StuffId,SUM(Qty) AS bcQty FROM $DataIn.ck3_bcsheet WHERE StuffId='$StuffId' GROUP BY StuffId) A ON A.StuffId=B.StuffId";
			 $checkStock=mysql_fetch_array(mysql_query($bcsql,$link_id));
			 $unQty=$checkStock["unQty"];
			 if ($unQty<=0) $ListFlag=0;
			 $Bdata=$StuffId."^^".$StuffCname."^^".$unQty;
		    break;
			}
		$Spec=$myRow["Spec"]==""?"&nbsp;":"<img src='../images/remark.gif' alt='$myRow[Spec]' width='18' height='18'>";
		$Remark=$myRow["Remark"]==""?"&nbsp;":"<img src='../images/remark.gif' title='$myRow[Remark]' width='18' height='18'>";

		$Picture=$myRow["Picture"];
		if($Picture==1){
			$Picture=$StuffId.".jpg";
			$File=anmaIn($Picture,$SinkOrder,$motherSTR);
			$Dir="stufffile";
			$Dir=anmaIn($Dir,$SinkOrder,$motherSTR);
			$Picture="<span onClick='OpenOrLoad(\"$Dir\",\"$File\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
			}
		else{
			$Picture="&nbsp";
			}
		$SendFloor=$myRow["SendFloor"];
		include "../model/subprogram/stuff_GetFloor.php";
		$SendFloor=$SendFloor=""?"&nbsp":$SendFloor;
		$StuffCname=$myRow["StuffCname"];
		$Estate=$myRow["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
		$Date=substr($myRow["Date"],0,10);
		$Operator=$myRow["Operator"];
		include "../admin/subprogram/staffname.php";
		$Locks=1;
		$LockRemark="";
	    $checkidValue=$Bdata;
		if ($ListFlag==1){
			echo"<tr onclick='if(checkid$i.checked)checkid$i.checked=false;else checkid$i.checked=true;'>
			<td class='A0111' align='center' id='theCel$i' height='25' >
			<input name='checkid[]' type='checkbox' id='checkid$i' value='$checkidValue'></td>
				 <td class='A0101' align='center'>$i</td>";
			echo"<td class='A0101' align='center'>$TypeName</td>";
			echo"<td class='A0101' align='center'>$StuffId</td>";
			echo"<td class='A0101'>$StuffCname</td>";
			if ($Action=="15"){
				  echo"<td class='A0101' align='right'><font color='#FF0000'>$unQty</font></td>";
			  }else{
			      echo"<td class='A0101' align='right'>$Price</td>";
			  }
			echo"<td class='A0101'>$Forshort</td>";
			echo"<td class='A0101' align='center'>$SendFloor</td>";
			echo"<td class='A0101' align='center'>$Buyer</td>";
			echo"<td class='A0101'>$Spec</td>";
			echo"<td class='A0101'>$Remark</td>";
			echo"<td class='A0101' align='center'>$Date</td>";
			echo"<td class='A0101' align='center'>$Operator</td>";
			echo"</tr>";
			$i++;
		}
		}while ($myRow = mysql_fetch_array($myResult));
	  if ($i==1){
		echo"<tr><td colspan='13' align='center' height='30' class='A0111'><div class='redB' style='background-color: #ffffff'>没有记录!</div></td></tr>";
	  }
	}
else{
	echo"<tr><td colspan='13' align='center' height='30' class='A0111'><div class='redB' style='background-color: #ffffff'>没有记录!</div></td></tr>";
	}
echo "</table>";
	?>
</form>
</body>
</html>