<?php 
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
switch($ActionId){
case "1":
$TableId="ListTB".$RowId;
$subTableWidth=700;
echo"<table id='$TableId' width='$subTableWidth'  cellspacing='1' border='1' align='center'><tr bgcolor='#CCCCCC'>
		<td width='20' height='20'></td>
		<td width='60' align='center'>餐厅</td>
		<td width='80' align='center'>菜式分类</td>
		<td width='150' align='center'>菜式名称</td>		
		<td width='60' align='center'>价格</td>
		<td width='60' align='center'>点餐数量</td>
		<td width='60' align='center'>点餐金额</td>
		<td width='70' align='center'>点餐日期</td>
		<td width='30' align='center'>状态</td>
</tr>";

$sListResult = mysql_query("SELECT A.Id,A.Price,A.Qty,A.Amount,A.Estate,A.Locks,A.Date,A.Operator,B.Name AS MenuName,C.Name AS CTName,D.Name AS MenuType 
FROM $DataPublic.ct_myorder A
LEFT JOIN $DataPublic.ct_menu B ON B.Id=A.MenuId
LEFT JOIN $DataPublic.ct_data C ON C.Id=B.CtId
LEFT JOIN $DataPublic.ct_type D ON D.Id=B.mType
WHERE 1 AND A.Operator='$Number' AND DATE_FORMAT(A.Date,'%Y-%m')='$Month'  ORDER BY A.Estate DESC,A.Id DESC",$link_id);

$i=1;
if ($StockRows = mysql_fetch_array($sListResult)) {
	do{
		$m=1;
		$Id=$StockRows["Id"];
		$CTName=$StockRows["CTName"];	
		$MenuType=$StockRows["MenuType"];	
		$MenuName=$StockRows["MenuName"];	
		$Price=$StockRows["Price"];	
		$Qty=$StockRows["Qty"];	
		$Amount=$StockRows["Amount"];	
        $Estate=$StockRows["Estate"];	
         switch($Estate){
             case "0":$Estate="<div class='greenB'>√</div>";
                 break;
             case "1":$Estate="<div class='redB'>×</div>";
                 break;
              }
		//$Estate=$myRow["Locks"]==0?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
		$Date=$StockRows["Date"];
	
	echo"<tr bgcolor='$theDefaultColor'>
		<td bgcolor='$Sbgcolor' align='right' height='20'>$i</td>";//
		echo"<td  align='center' >$CTName</td>";	
		echo"<td  align='center'>$MenuType</td>";//
		echo"<td  align='center' >$MenuName</td>";		
		echo"<td  align='right'>$Price</td>";
		echo"<td  align='right'>$Qty</td>";
		echo"<td  align='right'>$Amount</td>";
		echo"<td  align='center'>$Date</td>";
		echo"<td  align='center'>$Estate</td>";
		echo"</tr>";
		$i=$i+1;
	}while ($StockRows = mysql_fetch_array($sListResult));
}
else{
	echo"<tr><td height='30' colspan='6'>无相关的记录.</td></tr>";
	}
echo"</table>"."";
break;
case "2":
          $DelSql="DELETE  FROM $DataPublic.ct_monthamount WHERE Number='$tempNumber' AND Month='$chooseDate'";
          $DelResult=@mysql_query($DelSql);
          $In_Sql="INSERT INTO $DataPublic.ct_monthamount(`Id`, `Month`, `Number`, `Amount`, `Operator`)VALUES(NULL,'$chooseDate','$tempNumber','$SumAmount','$Login_P_Number')";
           $In_Result=@mysql_query($In_Sql);
            if($In_Result&& mysql_affected_rows()>0){
                echo "Y";
                 }
     break;
}

?>