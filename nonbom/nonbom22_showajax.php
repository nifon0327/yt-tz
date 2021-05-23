<?php 
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
echo "<link rel='stylesheet' href='../cjgl/lightgreen/read_line.css'>";
echo "<SCRIPT src='../model/pagefun.js' type=text/javascript></script>";
echo "<center>";
$GoodsResult=mysql_query("SELECT GoodsName,Attached  FROM $DataPublic.nonbom4_goodsdata  WHERE  GoodsId='$GoodsId'",$link_id); 
if($GoodsRow=mysql_fetch_array($GoodsResult)){
  $GoodsName=$GoodsRow["GoodsName"];
  $Attached=$GoodsRow["Attached"];
		if($Attached==1){
			$Attached=$GoodsId.".jpg";
			$Attached=anmaIn($Attached,$SinkOrder,$motherSTR);
			$GoodsName="<span onClick='OpenOrLoad(\"$Dir\",\"$Attached\")' style='CURSOR: pointer;color:#FF6633'>$GoodsName</span>";
			}
	   include"../model/subprogram/good_Property.php";//非BOM配件属性	
}
	   
echo "<table width='680' border='0' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF' >
      <tr>
      <td height='80' colspan='2' align='center'  style='Font-size:18px;'>&nbsp;</td>
      </tr>
	   <tr>
       <td height='22' width='60'>配件名称:</td> <td width='370'>$GoodsName</td> 
       <td width='60'>报废数量:</td> <td width='170'>$bfQty</td> 
       </tr></table>";
echo"
<table width='660' cellspacing='0' border='3' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;' align='center'>
<tr  bgcolor='#33CCCC'>
<td class='A0101' width='50' align='center' >NO.</td>
<td class='A0101' width='80' align='center' >单号</td>
<td class='A0101' width='250' align='center'>报废原因</td>
<td class='A0101' width='80' align='center' >报废数量</td>
<td class='A0101' width='80' align='center'>报废时间</td>
<td class='A0101' width='60' align='center'>报废人</td>
<td class='A0101' width='60' align='center'>状态</td>
</tr>";
$ListResult = mysql_query("SELECT   B.Qty,B.Remark,B.Picture,B.Estate,B.Date ,M.Name,B.BillNumber
FROM $DataIn.nonbom8_bf  B  
LEFT JOIN $DataPublic.staffmain M ON M.Number=B.bfNumber
WHERE B.GoodsId=$GoodsId",$link_id);
      if($ListRow=mysql_fetch_array($ListResult)){
	  $i=1;
	    do{
		   $Qty=$ListRow["Qty"]; 
		   $Remark=$ListRow["Remark"]; 
		   $Estate=$ListRow["Estate"];
		   $Date=$ListRow["Date"];
           $Name=$ListRow["Name"];
            $BillNumber=$ListRow["BillNumber"];
           $EstateStr=$Estate==1?"<span class='redB'>未审核</span>":"<span class='greenB'>已审核</span>";
			echo "<tr bgcolor='#FFFFFF'>
			<td height='30' align='center' class='A0101'>$i</td>
			<td align='center'  class='A0101'>$BillNumber</td>
			<td align='left'  class='A0101'>$Remark</td>
			<td align='center' class='A0101'>$Qty</td>
			<td align='center' class='A0101'>$Date</td>
			<td align='center' class='A0101'>$Name</td>
			<td align='center' class='A0100'>$EstateStr</td>
			</tr>";
			$i++;
		}while($ListRow=mysql_fetch_array($ListResult));
	}
echo"</table>";


$FixedResult=mysql_query("SELECT  F.BarCode,C.GoodsNum,B.BillNumber
FROM $DataIn.nonbom8_bffixed  F 
LEFT JOIN $DataIn.nonbom8_bf B  ON B.Id=F.BfId
LEFT JOIN $DataIn.nonbom7_code C ON C.BarCode=F.BarCode
WHERE 1 ORDER BY B.BillNumber",$link_id);
if($FixedRow=mysql_fetch_array($FixedResult)){
       echo"<br><br><br>
       <table width='660' cellspacing='0' border='3' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;' align='center'>
       <tr  bgcolor='#33CCCC'>
       <td class='A0101' width='170' align='center' >单号</td>
       <td class='A0101' width='250' align='center'>资产条码</td>
       <td class='A0101' width='250' align='center' >资产编号</td>
       </tr>";
       $TempBillNumber="";
       do{
                 $BillNumber  =$FixedRow["BillNumber"];   
                 $BarCode  =$FixedRow["BarCode"];   
                $GoodsNum  =$FixedRow["GoodsNum"];   
                 
                 $NumsResult=mysql_fetch_array(mysql_query("SELECT COUNT(*) AS Nums FROM $DataIn.nonbom8_bffixed F  LEFT JOIN $DataIn.nonbom8_bf B  ON B.Id=F.BfId WHERE BillNumber=$BillNumber ",$link_id));
                 $Nums=$NumsResult["Nums"];
                 echo "<tr >";
                 if($TempBillNumber!=$BillNumber)echo "<td class='A0101' align='center' rowspan='$Nums' >$BillNumber</td>";    
                 echo "<td class='A0101' align='center' height='30' >$BarCode</td>";    
                 echo "<td class='A0101' align='center' >$GoodsNum</td></tr>";    
                 $TempBillNumber=$BillNumber;
         }while($FixedRow=mysql_fetch_array($FixedResult));
      echo"</table>";
}
?>