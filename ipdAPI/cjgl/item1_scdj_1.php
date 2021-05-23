<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" href="sharedStyle.css" type="text/css" charset="utf-8">
	<script type="text/javascript" src="jquery-1.7.2.js" charset="utf-8"></script>
	<script type="text/javascript" src="cj_function.js" charset="utf-8"></script>
</head>
<body>
<?php
//OK
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
//读取产品资料
include "../../basic/parameter.inc";
include "../../model/modelfunction.php";

$CheckSql=mysql_query("SELECT P.CompanyId,C.Forshort,S.OrderPO,
S.ProductId,P.cName,P.eCode,S.POrderId,S.Qty
FROM $DataIn.yw1_ordersheet S
LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
WHERE 1 AND S.POrderId=$POrderId",$link_id);

if($CheckRow=mysql_fetch_array($CheckSql)){
	$CompanyId=$CheckRow["CompanyId"];
	$Forshort=$CheckRow["Forshort"];
	$OrderPO=$CheckRow["OrderPO"];
	$ProductId=$CheckRow["ProductId"];
	$cName=$CheckRow["cName"];
	$eCode=$CheckRow["eCode"];
	$Qty=$CheckRow["Qty"];		//订单数量，即加工数量

	//已完成的工序数量
	$CheckCfQty=mysql_fetch_array(mysql_query("SELECT SUM(C.Qty) AS cfQty 
	FROM $DataIn.sc1_cjtj C 
	WHERE C.POrderId='$POrderId' AND C.TypeId='$TypeId'",$link_id));
	$OverPQty=$CheckCfQty["cfQty"]==""?0:$CheckCfQty["cfQty"];
	//未完成订单数
	$UnPQty=$Qty-$OverPQty;
	}
?>

  	<table width="100%" height="100" border="1" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
    	<tr align="center" bgcolor="#d6efb5">
	    	<td width="142" height="50" class="A1111">客户</td>
	    	<td width="116" class="A1101">PO</td>
	    	<td width="117" class="A1101">产品ID</td>
	    	<td width="390" class="A1101">产品名称</td>
	    	<td width="338" class="A1101">Product Code </td>
	    	<td width="195" class="A1101">订单流水号</td>
		    <input name="POrderId" type="hidden" id="POrderId" value="<?php  echo $POrderId?>">
		    <input name="TypeId" type="hidden" id="TypeId" value="<?php  echo $TypeId ?>"
	   </tr>
	   <tr align="center">
		   <td height="50" class="A0111"><?php  echo $Forshort?></td>
		   <td class="A0101"><?php  echo $OrderPO?></td>
		   <td class="A0101"><?php  echo $ProductId?></td>
		   <td class="A0101"><?php  echo $cName?></td>
		   <td class="A0101"><?php  echo $eCode?></td>
		   <td class="A0101"><?php  echo $POrderId?></td>
     </tr>
  </table>
  <br>
<table width="100%" height="221" border="1" cellpadding="0" cellspacing="0">
	<tr align="center" bgcolor="#d6efb5">
		<td height="60" colspan="5" class="A0000">登记生产数量</td>
  </tr>

	<tr align="center" bgcolor="#d6efb5">
		<td width="259" height="50" class="A1111">&nbsp;</td>
		<td width="259" class="A1101">订单</td>
		<td width="260" class="A1101">已生产</td>
		<td width="260" class="A1101">未生产</td>
		<td width="260" class="A1101">本次登记</td>
	</tr>
  <tr align="center">
    <td height="50" bgcolor="#d6efb5" class="A0111">数量</td>
  	<td class="A0101" bgcolor="#FFFFFF"><?php  echo $Qty?></td>
    <td class="A0101" bgcolor="#FFFFFF"><?php  echo $OverPQty?></td>
    <td class="A0101" bgcolor="#FFFFFF"><input name="UnPQty" type="text" class="I0000C" id="UnPQty" value="<?php  echo $UnPQty?>" size="13" readonly></td>
    <td class="A0101" bgcolor="#FFFFFF"><input name="Qty" type="number" class="I0000C" id="Qty" value="这里输入生产数量" size="15" onfocus="ClearStr()"></td>
  </tr>
</table>
</body>
</html>

<script type="text/javascript">

	function ToSaveDj(TypeId,operator)
	{
		var Qty=document.getElementById("Qty").value;
		var CheckSTR=fucCheckNUM(Qty,"");
		if(CheckSTR==0)
		{
			document.getElementById("InfoBack").innerHTML="不是规范的数字！";
			document.form1.Qty.value="";
			return false;
		}
		else
		{
		//检查数字是否合法
			var MaxValue=document.getElementById("UnPQty").value;
			thisValue=Number(Qty);
			MaxValue=Number(MaxValue);
			if((thisValue>MaxValue) || thisValue==0)
			{
				document.getElementById("InfoBack").innerHTML="超出范围！";
				document.getElementById("Qty").value="";
				return false;
			}
			else
			{
				document.getElementById("InfoBack").innerHTML="&nbsp;";
			}
			var POrderId=document.getElementById("POrderId").value;
			var url="../../cjgl/item1_scdj_ajax.php?TypeId="+TypeId+"&POrderId="+POrderId+"&Qty="+Qty+"&Operator="+operator+"&ipadTag=yes";
	　		var ajax=InitAjax();
	　		ajax.open("GET",url,true);
			ajax.onreadystatechange =function(){
		　　if(ajax.readyState==4 && ajax.status ==200)
			   {
			   	   var BackData=ajax.responseText;
				   if(BackData=="Y")
				   {
				       var d = new Date();
				       var timTag = d.getFullYear() + "" +(d.getMonth()+1) + "" + d.getDate() + "" + d.getHours() + "" + d.getMinutes() + "" + d.getSeconds();
				       var url = "?"+timTag+"#saved";
				   }
			   }
			}
			ajax.send(null);
		}
	}


</script>
