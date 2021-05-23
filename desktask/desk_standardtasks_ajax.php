<?php   
//电信---yang 20120801
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
echo"<br><table cellspacing='1' border='1' align='center'>
<tr bgcolor='#FFFFFF'>
		<td width='50' height='25' align='center'>序号</td>
		<td width='350' align='center'>产品名称</td>
		<td width='90' align='center'>产品ID</td>
		<td width='120' align='center'>产品分类</td>
		<td width='120' align='center'>客户</td>
		<td width='80' align='center'>操作人</td>
		</tr>";
switch($Action){
	case 1://已出货的订单
		$checkSql=mysql_query("
					 SELECT A.ProductId,B.cName,B.TestStandard,C.Forshort,D.TypeName
					  FROM $DataIn.yw1_ordersheet A
					  LEFT JOIN $DataIn.productdata B ON B.ProductId=A.ProductId
					  LEFT JOIN $DataIn.trade_object C ON C.CompanyId=B.CompanyId
					  LEFT JOIN $DataIn.producttype D ON D.TypeId=B.TypeId
					  WHERE A.scFrom=0 AND A.Estate=0 AND B.Estate=1 AND B.TestStandard='$TestStandard' GROUP BY B.ProductId
					  ",$link_id);
	break;
	case 2://已生产待出货的订单
	$checkSql=mysql_query("
					 SELECT A.ProductId,B.cName,B.TestStandard,C.Forshort,D.TypeName
					  FROM $DataIn.yw1_ordersheet A
					  LEFT JOIN $DataIn.productdata B ON B.ProductId=A.ProductId
					  LEFT JOIN $DataIn.trade_object C ON C.CompanyId=B.CompanyId
					  LEFT JOIN $DataIn.producttype D ON D.TypeId=B.TypeId
					  WHERE A.scFrom=0 AND A.Estate=2 AND B.Estate=1 AND B.TestStandard='$TestStandard' GROUP BY B.ProductId
					  ",$link_id);
	break;
	case 3://正在生产的订单
	$checkSql=mysql_query("
					 SELECT A.ProductId,B.cName,B.TestStandard,C.Forshort,D.TypeName
					  FROM $DataIn.yw1_ordersheet A
					  LEFT JOIN $DataIn.productdata B ON B.ProductId=A.ProductId
					  LEFT JOIN $DataIn.trade_object C ON C.CompanyId=B.CompanyId
					  LEFT JOIN $DataIn.producttype D ON D.TypeId=B.TypeId
					  WHERE A.scFrom=2 AND A.Estate=1 AND B.Estate=1 AND B.TestStandard='$TestStandard' GROUP BY B.ProductId
					  ",$link_id);
	break;
	case 4://未生产的订单
	$checkSql=mysql_query("
					 SELECT A.ProductId,B.cName,B.TestStandard,C.Forshort,D.TypeName
					  FROM $DataIn.yw1_ordersheet A
					  LEFT JOIN $DataIn.productdata B ON B.ProductId=A.ProductId
					  LEFT JOIN $DataIn.trade_object C ON C.CompanyId=B.CompanyId
					  LEFT JOIN $DataIn.producttype D ON D.TypeId=B.TypeId
					  WHERE A.scFrom=1 AND A.Estate=1 AND B.Estate=1 AND B.TestStandard='$TestStandard' GROUP BY B.ProductId
					   ",$link_id);
	break;
	default://全部产品
	$checkSql=mysql_query("
					 SELECT B.ProductId,B.cName,B.TestStandard,C.Forshort,D.TypeName
					  FROM  $DataIn.productdata B
					  LEFT JOIN $DataIn.trade_object C ON C.CompanyId=B.CompanyId
					  LEFT JOIN $DataIn.producttype D ON D.TypeId=B.TypeId
					  WHERE 1 AND B.Estate=1 AND B.TestStandard='$TestStandard' GROUP BY B.ProductId
					   ",$link_id);
	break;
	}
$i=1;
if($checkRow=mysql_fetch_array($checkSql)){
	do{
		$ProductId=$checkRow["ProductId"];
		$cName=$checkRow["cName"];
		$TestStandard=$checkRow["TestStandard"];
		$PersonResult=mysql_fetch_array(mysql_query("SELECT M.Name FROM $DataIn.productstandimg P
		  LEFT JOIN $DataPublic.staffmain M ON M.Number=P.Operator WHERE P.ProductId='$ProductId'",$link_id));
		 $Operator=$PersonResult["Name"]==""?"&nbsp;":$PersonResult["Name"];
		
		//如果TestStandard》0显示连接
		include "../admin/Productimage/getOnlyProductImage.php";
		echo"
		<tr bgcolor='#FFFFFF'>
		<td align='center' height='25'>$i</td>
		<td>$TestStandard</td>
		<td>$ProductId</td>
		<td>$checkRow[TypeName]</td>
		<td>$checkRow[Forshort]</td>
		<td align='center'>$Operator</td>
		</tr>
		";
		$i++;
		}while ($checkRow=mysql_fetch_array($checkSql));
	}
echo"</table><br>";
?>