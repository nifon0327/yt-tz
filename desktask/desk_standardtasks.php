<?php   
//电信---yang 20120801
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 标准图上传审核任务统计");//需处理
?>
<table border="0" cellpadding="0" cellspacing="0" >
<tr align="center" bgcolor="#CCCCCC">
<td width="50" height="30" class="A1111">序号</td>
<td width="400" class="A1101">产品来源</td>
<td width="100" class="A1101">未上传标准图</td>
<td width="100" class="A1101">未审核标准图</td>
<td width="100" class="A1101">需更新标准图</td>
<td width="100" class="A1101">审核退回修改</td>
</tr>
<?php   
//0未上传标准图；1已通过标准图；2审核中标准图；3需更新的标准图
//已出货
/*
$i=1;
$A0=$A2=$A3=$A4=0;
$AOnclick0=$AOnclick2=$AOnclick3=$AOnclick4="";
$checkSql=mysql_query("SELECT count(*) AS Num,C.TestStandard FROM (
					  SELECT A.ProductId,B.TestStandard
					  FROM $DataIn.yw1_ordersheet A
					  LEFT JOIN $DataIn.productdata B ON B.ProductId=A.ProductId
					  WHERE A.scFrom=0 AND A.Estate=0 AND B.Estate=1 AND B.TestStandard!=1 GROUP BY B.ProductId) C GROUP BY C.TestStandard",$link_id);
if($checkRow=mysql_fetch_array($checkSql)){
	do{
		$Num=$checkRow["Num"];
		if($Num>0){
			$TestStandard=$checkRow["TestStandard"];
			$TempEstateSTR="A".strval($TestStandard); 
			$$TempEstateSTR=$Num;	
			$TempOnclick="AOnclick".strval($TestStandard); 
			$$TempOnclick="style=\"CURSOR: pointer;width:80px\" onClick=ShowSheet('TrShow$i','DivShow$i','$TestStandard',$i)";
			}
		}while ($checkRow=mysql_fetch_array($checkSql));
	}
$A0=SpaceValue0($A0);
$A2=SpaceValue0($A2);
$A3=SpaceValue0($A3);
$A4=SpaceValue0($A4);
*/
?>
<!--
<tr align="center">
  <td height="30" class="A0111"><?php    echo $i?></td>
  <td align="left" class="A0101">已出货订单中的产品</td>
  <td class="A0101"  <?php    echo $AOnclick0?>><?php    echo $A0?>&nbsp;</td>
  <td class="A0101"  <?php    echo $AOnclick2?>><?php    echo $A2?>&nbsp;</td>
  <td class="A0101" <?php    echo $AOnclick3?>><?php    echo $A3?>&nbsp;</td>
  <td class="A0101" <?php    echo $AOnclick4?>><?php    echo $A4?>&nbsp;</td>
</tr>
-->
<?php   
//0未上传标准图；1已通过标准图；2审核中标准图；3需更新的标准图
//已出货
/*
echo"</tr><tr id='TrShow$i' style='display:none;background:#ccc;'><td colspan='6' style=\"height:50px\" valign=\"top\" class=\"A0111\"><div id='DivShow$i' style='display:none;'></div></td></tr>";//隐藏的行
$i++;
*/
$i=2;
$A0=$A2=$A3=$A4=0;
$AOnclick0=$AOnclick2=$AOnclick3=$AOnclick4="";
$checkSql=mysql_query("
					  SELECT count(*) AS Num,C.TestStandard FROM (
					  SELECT A.ProductId,B.TestStandard
					  FROM $DataIn.yw1_ordersheet A
					  LEFT JOIN $DataIn.productdata B ON B.ProductId=A.ProductId
					  WHERE A.scFrom=0 AND A.Estate=2 AND B.Estate=1 AND B.TestStandard!=1 GROUP BY B.ProductId) C GROUP BY C.TestStandard",$link_id);
if($checkRow=mysql_fetch_array($checkSql)){
	do{
		$Num=$checkRow["Num"];
		if($Num>0){
			$TestStandard=$checkRow["TestStandard"];
			$TempEstateSTR="A".strval($TestStandard); 
			$$TempEstateSTR=$Num;	
			$TempOnclick="AOnclick".strval($TestStandard); 
			$$TempOnclick="style=\"CURSOR: pointer;width:80px\" onClick=ShowSheet('TrShow$i','DivShow$i','$TestStandard',$i)";
			}
		}while ($checkRow=mysql_fetch_array($checkSql));
	}
$A0=SpaceValue0($A0);
$A2=SpaceValue0($A2);
$A3=SpaceValue0($A3);
$A4=SpaceValue0($A4);
?>
<tr align="center">
  <td height="30" class="A0111"><?php    echo $i-1?></td>
  <td align="left" class="A0101">已生产待出货订单中的产品</td>
  <td class="A0101"  <?php    echo $AOnclick0?>><?php    echo $A0?>&nbsp;</td>
  <td class="A0101"  <?php    echo $AOnclick2?>><?php    echo $A2?>&nbsp;</td>
  <td class="A0101" <?php    echo $AOnclick3?>><?php    echo $A3?>&nbsp;</td>
  <td class="A0101" <?php    echo $AOnclick4?>><?php    echo $A4?>&nbsp;</td>
</tr>
<?php   
echo"</tr><tr id='TrShow$i' style='display:none;background:#ccc;'><td colspan='6' style=\"height:50px\" valign=\"top\" class=\"A0111\"><div id='DivShow$i' style='display:none;'></div></td></tr>";//隐藏的行
$i++;
$A0=$A2=$A3=$A4=0;
$AOnclick0=$AOnclick2=$AOnclick3=$AOnclick4="";
$checkSql=mysql_query("
					  SELECT count(*) AS Num,C.TestStandard FROM (
					  SELECT A.ProductId,B.TestStandard
					  FROM $DataIn.yw1_ordersheet A
					  LEFT JOIN $DataIn.productdata B ON B.ProductId=A.ProductId
					  WHERE A.scFrom=2 AND A.Estate=1 AND B.Estate=1 AND B.TestStandard!=1 GROUP BY B.ProductId) C GROUP BY C.TestStandard",$link_id);
if($checkRow=mysql_fetch_array($checkSql)){
	do{
		$Num=$checkRow["Num"];
		if($Num>0){
			$TestStandard=$checkRow["TestStandard"];
			$TempEstateSTR="A".strval($TestStandard); 
			$$TempEstateSTR=$Num;	
			$TempOnclick="AOnclick".strval($TestStandard); 
			$$TempOnclick="style=\"CURSOR: pointer;width:80px\" onClick=ShowSheet('TrShow$i','DivShow$i','$TestStandard',$i)";
			}
		}while ($checkRow=mysql_fetch_array($checkSql));
	}
$A0=SpaceValue0($A0);
$A2=SpaceValue0($A2);
$A3=SpaceValue0($A3);
$A4=SpaceValue0($A4);
?>
<tr align="center">
  <td height="30" class="A0111"><?php    echo $i-1?></td>
  <td align="left" class="A0101">正在生产订单中的产品</td>
  <td class="A0101"  <?php    echo $AOnclick0?>><?php    echo $A0?>&nbsp;</td>
  <td class="A0101"  <?php    echo $AOnclick2?>><?php    echo $A2?>&nbsp;</td>
  <td class="A0101" <?php    echo $AOnclick3?>><?php    echo $A3?>&nbsp;</td>
  <td class="A0101" <?php    echo $AOnclick4?>><?php    echo $A4?>&nbsp;</td>
</tr>
<?php   
echo"</tr><tr id='TrShow$i' style='display:none;background:#ccc;'><td colspan='6' style=\"height:50px\" valign=\"top\" class=\"A0111\"><div id='DivShow$i' style='display:none;'></div></td></tr>";//隐藏的行
$i++;
$A0=$A2=$A3=$A4=0;
$AOnclick0=$AOnclick2=$AOnclick3=$AOnclick4="";
$checkSql=mysql_query("
					  SELECT count(*) AS Num,C.TestStandard FROM (
					  SELECT A.ProductId,B.TestStandard
					  FROM $DataIn.yw1_ordersheet A
					  LEFT JOIN $DataIn.productdata B ON B.ProductId=A.ProductId
					  WHERE A.scFrom=1 AND A.Estate=1 AND B.Estate=1 AND B.TestStandard!=1 GROUP BY B.ProductId) C GROUP BY C.TestStandard",$link_id);
if($checkRow=mysql_fetch_array($checkSql)){
	do{
		$Num=$checkRow["Num"];
		if($Num>0){
			$TestStandard=$checkRow["TestStandard"];
			$TempEstateSTR="A".strval($TestStandard); 
			$$TempEstateSTR=$Num;	
			$TempOnclick="AOnclick".strval($TestStandard); 
			$$TempOnclick="style=\"CURSOR: pointer;width:80px\" onClick=ShowSheet('TrShow$i','DivShow$i','$TestStandard',$i)";
			}
		}while ($checkRow=mysql_fetch_array($checkSql));
	}
$A0=SpaceValue0($A0);
$A2=SpaceValue0($A2);
$A3=SpaceValue0($A3);
$A4=SpaceValue0($A4);
?>
<tr align="center">
  <td height="30" class="A0111"><?php    echo $i-1?></td>
  <td align="left" class="A0101">未生产订单中的产品</td>
  <td class="A0101"  <?php    echo $AOnclick0?>><?php    echo $A0?>&nbsp;</td>
  <td class="A0101"  <?php    echo $AOnclick2?>><?php    echo $A2?>&nbsp;</td>
  <td class="A0101" <?php    echo $AOnclick3?>><?php    echo $A3?>&nbsp;</td>
  <td class="A0101" <?php    echo $AOnclick4?>><?php    echo $A4?>&nbsp;</td>
</tr>
<?php   
echo"</tr><tr id='TrShow$i' style='display:none;background:#ccc;'><td colspan='6' style=\"height:50px\" valign=\"top\" class=\"A0111\"><div id='DivShow$i' style='display:none;'></div></td></tr>";//隐藏的行
$i++;
$A0=$A2=$A3=$A4=0;
$AOnclick0=$AOnclick2=$AOnclick3=$AOnclick4="";
$checkSql=mysql_query("SELECT count(*) AS Num,TestStandard FROM $DataIn.productdata WHERE TestStandard!=1 AND Estate=1 GROUP BY TestStandard",$link_id);
if($checkRow=mysql_fetch_array($checkSql)){
	do{
		$Num=$checkRow["Num"];
		if($Num>0){
			$TestStandard=$checkRow["TestStandard"];
			$TempEstateSTR="A".strval($TestStandard); 
			$$TempEstateSTR=$Num;	
			$TempOnclick="AOnclick".strval($TestStandard); 
			$$TempOnclick="style=\"CURSOR: pointer;width:80px\" onClick=ShowSheet('TrShow$i','DivShow$i','$TestStandard',$i)";
			}
		}while ($checkRow=mysql_fetch_array($checkSql));
	}
$A0=SpaceValue0($A0);
$A2=SpaceValue0($A2);
$A3=SpaceValue0($A3);
$A4=SpaceValue0($A4);
?>
<tr align="center">
<td height="30" class="A0111"><?php    echo $i-1?></td>
  <td height="30" class="A0101">全部在使用的产品情况统计</td>
  <td class="A0101"  <?php    echo $AOnclick0?>><?php    echo $A0?>&nbsp;</td>
  <td class="A0101"  <?php    echo $AOnclick2?>><?php    echo $A2?>&nbsp;</td>
  <td class="A0101" <?php    echo $AOnclick3?>><?php    echo $A3?>&nbsp;</td>
    <td class="A0101" <?php    echo $AOnclick4?>><?php    echo $A4?>&nbsp;</td>
</tr>
<?php   
echo"</tr><tr id='TrShow$i' style='display:none;background:#ccc;'><td colspan='6' style=\"height:50px\" valign=\"top\" class=\"A0111\"><div id='DivShow$i' style='display:none;'></div></td></tr>";//隐藏的行
?>
</table>
<script language="JavaScript" type="text/JavaScript">
function ShowSheet(TrId,DivId,TestStandard,Action){//隐藏行ID,隐藏行DIV,全部数据还是月份数据,项目ID
 ShowDiv=eval(DivId);
 ShowTr=eval(TrId);
 ShowTr.style.display=(ShowTr.style.display=="none")?"":"none";
 ShowDiv.style.display=(ShowDiv.style.display=="none")?"":"none";
var url="desk_standardtasks_ajax.php?Action="+Action+"&TestStandard="+TestStandard;
 var ajax=InitAjax();
 ajax.open("GET",url,true);
 ajax.onreadystatechange =function(){
 　　if(ajax.readyState==4 && ajax.status ==200 && ajax.responseText!=""){
 　　　 var BackData=ajax.responseText;
   ShowDiv.innerHTML=BackData;
   }
  }
 ajax.send(null); 
 }
 </script>