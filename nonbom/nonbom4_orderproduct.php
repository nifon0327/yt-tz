<?php
include "../basic/chksession.php";
include "../basic/parameter.inc";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>非BOM模具类配件订单使用明细</title>
<style type='text/css' >
body{
text-align:center;
}
.purpleB{color: #FF00CC;font-weight: bold;}
.redB{color: #FF0000;font-weight: bold;}
.greenB{color: #009900;font-weight: bold;}
.yellowB{color: #FF6633;font-weight: bold;}
.blueB{color: #0000CC;font-weight: bold;}
.orangeB{color: #FF6600;font-weight: bold;}

#container{
width:920px;
height: 1200px;
margin:0px auto;
}
#NoteTable{
   width: 100%;
  border-collapse:collapse;
}
h1{
 margin-top: 80px;
 font-size: 20px;
 font-weight: bold;
}
.Bantr{
 height: 30px;
  background-color: #33CCCC;
}


.A0100{
  border-bottom: 1px solid #CCC;
  font-size: 12px;
  height: 25px;
  background-color: #F0E68C;
}

#mcColor{
      width: 20px;
       height: 20px;
       background-color: #F0E68C;
}

.B0100{
  border-bottom: 1px solid #CCC;
  font-size: 12px;
  height: 25px;
  background-color: #EED5D2;
}

#ptColor{
       width: 20px;
       height: 20px;
       background-color: #EED5D2;
}
.mctextColor{
   width: 80px;
   font-size: 12px;
   text-align: left;
}
.pttextColor{
   font-size: 12px;
   text-align: left;
}
</style>
</head>
<?php
$CheckGoodsResult=mysql_fetch_array(mysql_query("SELECT GoodsName  FROM $DataPublic.nonbom4_goodsdata WHERE  GoodsId=$GoodsId",$link_id));
$GoodsName=$CheckGoodsResult["GoodsName"];
?>
<body >
<div id="container">
<h1><?php  echo $GoodsName;?></h1>
<table width="100%" cellpadding="0" cellspacing="0" border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;' >
<tr><td id="mcColor">&nbsp;</td><td class="mctextColor">包装订单</td><td id="ptColor">&nbsp;</td><td class="pttextColor">皮套订单</td></tr>
</table>
<table  id="NoteTable">
<tr class="Bantr">
<td width="100" align="center">客户</td>
<td width="100" align="center">订单流水号</td>
<td  width="120" align="center">PO#</td>
<td align="center">产品名称</td>
<td width="100" align="center">订单数量</td>
<td width="80" align="center">出货状态</td>
<td width="100" align="center">下单日期</td>
</tr>
<?php
//主系统
$SumQty=0;
if($DataIn=="d7"){
   $mySql="SELECT Y.Qty ,Y.POrderId,Y.OrderPO,P.cName,P.TestStandard,M.OrderDate,Y.Estate,T.Forshort
          FROM $DataIn.cut_die  D   
         LEFT JOIN $DataIn.yw1_ordersheet  Y ON Y.ProductId=D.ProductId
         LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=Y.OrderNumber
         LEFT JOIN $DataIn.trade_object   T ON T.CompanyId=M.CompanyId AND T.ObjectSign IN (2,3)
         LEFT JOIN $DataIn.productdata P  ON P.ProductId=Y.ProductId 
         WHERE  D.GoodsId=$GoodsId ";

}
else{
   $mySql="SELECT Y.Qty ,Y.POrderId,Y.OrderPO,P.cName,P.TestStandard,M.OrderDate,Y.Estate,T.Forshort
          FROM $DataOut.cut_die  D   
         LEFT JOIN $DataOut.yw1_ordersheet  Y ON Y.ProductId=D.ProductId
         LEFT JOIN $DataOut.yw1_ordermain M ON M.OrderNumber=Y.OrderNumber
         LEFT JOIN $DataOut.trade_object   T ON T.CompanyId=M.CompanyId AND T.ObjectSign IN (2,3)
         LEFT JOIN $DataOut.productdata P  ON P.ProductId=Y.ProductId 
         WHERE  D.GoodsId=$GoodsId ";
}
$CheckDataInResult=mysql_query($mySql,$link_id);
 while($CheckDataInRow=mysql_fetch_array($CheckDataInResult)){
         $thisPOrderId=$CheckDataInRow["POrderId"];
         $thisQty=$CheckDataInRow["Qty"];
         $thisForshort=$CheckDataInRow["Forshort"];
         $thisOrderPO=$CheckDataInRow["OrderPO"];
         $cName=$CheckDataInRow["cName"];
         $TestStandard=$CheckDataInRow["TestStandard"];
		 include "../admin/Productimage/getPOrderImage.php";
         $thisOrderDate=$CheckDataInRow["OrderDate"];
         $thisEstate=$CheckDataInRow["Estate"];
         switch($thisEstate){
               case 0:$thisEstate="<span class='greenB'>已出</span>";break;
               case 1:$thisEstate="<span class='redB'>未出</span>";break;
               case 2:$thisEstate="<span class='yellowB'>待出</span>";break;
               case 4:$thisEstate="<span class='blueB'>当前出货</span>";break;
            }
        $SumQty+=$thisQty;
       echo "<tr>
         <td class='A0100'>$thisForshort</td>
         <td class='A0100'>$thisPOrderId</td>
         <td class='A0100'>$thisOrderPO</td>
         <td class='A0100' align='left'>$TestStandard</td>
         <td class='A0100'>$thisQty</td>
         <td class='A0100'>$thisEstate</td>
         <td class='A0100'>$thisOrderDate</td>
        </tr>";
}

//子系统
if($DataIn=="d7"){
    $mySql2="SELECT Y.Qty ,Y.POrderId,Y.OrderPO,Y.Date,Y.Estate,S.StuffCname
          FROM $DataOut.cut_die  D   
         LEFT JOIN $DataOut.yw1_ordersheet  Y ON Y.ProductId=D.ProductId
         LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=Y.OrderNumber
         LEFT JOIN $DataIn.stuffdata S ON S.StuffId=G.StuffId
         WHERE  D.GoodsId=$GoodsId ORDER BY Y.Date Desc ";
}
else{
    $mySql2="SELECT Y.Qty ,Y.POrderId,Y.OrderPO,Y.Date,Y.Estate,S.StuffCname
          FROM $DataIn.cut_die  D   
         LEFT JOIN $DataIn.yw1_ordersheet  Y ON Y.ProductId=D.ProductId
         LEFT JOIN $DataOut.cg1_stocksheet G ON G.StockId=Y.OrderNumber
         LEFT JOIN $DataOut.stuffdata S ON S.StuffId=G.StuffId
         WHERE  D.GoodsId=$GoodsId ORDER BY Y.Date Desc ";
}
 $CheckDataOutResult=mysql_query($mySql2,$link_id);
 while($CheckDataOutRow=mysql_fetch_array($CheckDataOutResult)){
         $OutPOrderId=$CheckDataOutRow["POrderId"];
         $OutQty=$CheckDataOutRow["Qty"];
         $SumQty+=$OutQty;
         $OutForshort=$CheckDataOutRow["Forshort"];
         $OutOrderPO=$CheckDataOutRow["OrderPO"];
         $OutcName=$CheckDataOutRow["StuffCname"];
         $OutOrderDate=$CheckDataOutRow["Date"];
         $OutEstate=$CheckDataInRow["Estate"];
         $OutForshort="研砼包装";
         switch($OutEstate){
               case 0:$OutEstate="<span class='greenB'>已出</span>";break;
               case 1:$OutEstate="<span class='redB'>未出</span>";break;
               case 2:$OutEstate="<span class='yellowB'>待出</span>";break;
               case 4:$OutEstate="<span class='blueB'>当前出货</span>";break;
            }
       echo "<tr>
         <td class='B0100'>$OutForshort</td>
         <td class='B0100'>$OutPOrderId</td>
         <td class='B0100'>$OutOrderPO</td>
         <td class='B0100' align='left'>$OutcName</td>
         <td class='B0100'>$OutQty</td>
         <td class='B0100'>$OutEstate</td>
         <td class='B0100'>$OutOrderDate</td>
        </tr>";
}
?>
<tr bgcolor="#33CCCC"><td colspan="4" height="25" style="font-size:18px;font-weight:bold;" >总计</td><td><?php echo $SumQty?></td><td colspan="2">&nbsp;</td></tr>
</table>
</div>
</body>
</html>