<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transition al.dtd"> 
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
$Th_Col="采购单号|60|采购|50|供应商|80|选项|40|序号|40|需求流水号|90|配件名称|190|需求数|60|增购数|60|实购数|60|未收数|60";
$Field=explode("|",$Th_Col);
$Count=count($Field);
$wField=$Field;
$tableWidth=0;
for ($i=0;$i<$Count;$i++){
	$i=$i+1;
	$tableWidth+=$wField[$i];
	}
//查询条件
$Action=$Action==""?"1":$Action;
$selModel=$selModel=""?"2":$selModel;
$nowInfo="当前:需求单资料";
$SearchRows="";
switch($Action){
   case "1":
   case "2":
   default:
      $SearchRows=$searchSTR==""?"":" and S.CompanyId='$searchSTR'";
	  break;
 }
   

//步骤5：
echo"<table cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7'>
	<tr>
	<td colspan='7' height='40px' class=''>$GysList</td><td colspan='4' align='right' class=''><input name='NowInfo' type='text' id='NowInfo' value='$nowInfo' class='text' disabled></td></tr>";
	//输出表格标题
    echo "<tr>";
	for($i=0;$i<$Count;$i=$i+2){
		$Class_Temp=$i==0?"A1111":"A1101";
		$j=$i;
		$k=$j+1;
		echo"<td width='$Field[$k]' class='' height='25px' style='background-color: #F0F5F8'><div align='center'>$Field[$j]</div></td>";
		}
	echo"</tr>";

$i=0;$j=1;
/*$mySql="SELECT M.PurchaseID,M.Date,M.Id,S.StuffId,S.StockId,S.AddQty,S.FactualQty,P.Forshort,B.Name,D.StuffCname
	FROM $DataIn.cg1_stockmain M
	LEFT JOIN $DataIn.cg1_stocksheet S ON M.Id=S.Mid
	LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
	LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
	LEFT JOIN $DataPublic.staffmain B ON M.BuyerId=B.Number
	WHERE 1 and S.rkSign>0 and S.Mid>0 $SearchRows ORDER BY M.Id";*/
$mySql="SELECT M.PurchaseID,M.Date,M.Id,S.StuffId,S.StockId,S.AddQty,S.FactualQty,P.Forshort,B.Name,D.StuffCname
	FROM $DataIn.cg1_stockmain M
	LEFT JOIN $DataIn.cg1_stocksheet S ON M.Id=S.Mid
	LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
	LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
	LEFT JOIN $DataPublic.staffmain B ON M.BuyerId=B.Number
	WHERE 1 and S.rkSign>0 and S.Mid>0 $SearchRows  and NOT EXISTS(SELECT T.Property FROM $DataIn.stuffproperty T WHERE  T.StuffId=S.StuffId AND T.Property=9)
   UNION ALL 
   SELECT   '客供' AS PurchaseID,'0000-00-00' AS Date ,'-1' AS Id,S.StuffId,S.StockId,S.AddQty,S.FactualQty,P.Forshort,B.Name,D.StuffCname
   FROM $DataIn.cg1_stocksheet S 
   LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
	LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId 
	LEFT JOIN $DataPublic.staffmain B ON S.BuyerId=B.Number
    INNER JOIN  $DataIn.stuffproperty  OP  ON OP.StuffId=S.StuffId AND OP.Property=2		
    WHERE 1  and S.rkSign>0 AND S.Mid=0  AND  OP.Property=2 AND (S.FactualQty >0  OR S.AddQty >0) $SearchRows 
     and NOT EXISTS(SELECT T.Property FROM $DataIn.stuffproperty T WHERE  T.StuffId=S.StuffId AND T.Property=9)
    UNION ALL
    SELECT M.PurchaseID,M.Date,M.Id,G.StuffId,G.StockId,G.AddQty,G.FactualQty,P.Forshort,B.Name,D.StuffCname
	FROM $DataIn.cg1_stocksheet S 
	LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid
    INNER JOIN $DataIn.cg1_stuffcombox G ON G.mStockId=S.StockId 
	LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
	LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
	LEFT JOIN $DataIn.staffmain B ON M.BuyerId=B.Number
	WHERE 1 and S.rkSign>0 and S.Mid>0 $SearchRows 
	UNION ALL
    SELECT '客供' AS PurchaseID,'0000-00-00' AS Date,'-1' AS Id,G.StuffId,G.StockId,G.AddQty,G.FactualQty,P.Forshort,B.Name,D.StuffCname
	FROM $DataIn.cg1_stocksheet S 
	LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid
    INNER JOIN $DataIn.cg1_stuffcombox G ON G.mStockId=S.StockId 
	LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
	LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
	LEFT JOIN $DataIn.staffmain B ON M.BuyerId=B.Number
	INNER JOIN  $DataIn.stuffproperty  OP  ON OP.StuffId=S.StuffId AND OP.Property=2	
	WHERE 1 and S.rkSign>0 and S.Mid=0 AND OP.Property=2 AND (S.FactualQty >0 OR S.AddQty >0)  $SearchRows 
    ";
//echo $mySql;
$mainResult = mysql_query($mySql,$link_id);
if($mainRows = mysql_fetch_array($mainResult)){
	$newMid="";
	do{
		$m=1;
		$Mid=$mainRows["Id"];
		$Date=$mainRows["Date"];
		$PurchaseID=$mainRows["PurchaseID"];
		$Forshort=$mainRows["Forshort"];
		$Buyer=$mainRows["Name"];
		//明细资料
		$StuffId=$mainRows["StuffId"];
		if($StuffId!=""){			
			$StuffCname=$mainRows["StuffCname"];
			$StockId=$mainRows["StockId"];
			$FactualQty=$mainRows["FactualQty"];
			$AddQty=$mainRows["AddQty"];			
			$CountQty=$FactualQty+$AddQty;
			
			//收货数量计算
			$ReQty_Temp=mysql_query("SELECT SUM(Qty) AS a1 FROM $DataIn.ck1_rksheet WHERE StockId='$StockId'",$link_id);
			$ReQty=mysql_result($ReQty_Temp,0,"a1");
			$Unreceive=$CountQty-$ReQty;
		if ($Unreceive>0){	
			//收货数量计算
			if ($newMid!=$Mid){
			    $newMid=$Mid;
				if ($i>0) {echo"</table></td></tr>";}
				//输出并行列
				echo"<tr>";
				echo"<td class='A0111' width='$Field[$m]' align='center'>$PurchaseID</td>";
				$unitWidth=$tableWidth-$Field[$m];
				$m=$m+2;
				echo"<td class='A0101' width='$Field[$m]' align='center'>$Buyer</td>";
				$unitWidth=$tableWidth-$Field[$m];
				$m=$m+2;
				echo"<td class='A0101' width='$Field[$m]'>$Forshort</td>";
				$unitWidth=$tableWidth-$Field[$m];
				$m=$m+2;
				echo"<td colspan='8' class='A0101'>";
				$i++;
				echo"<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
				}
			else{
				$m=7;
			}
			$ValueSTR="$StockId^^$StuffId^^ " . str_replace(","," ",$StuffCname) . "^^$FactualQty^^$AddQty^^$CountQty^^$Unreceive^^$BuyerId";
			$chooseStr="<input name='checkid[$j]' type='checkbox' id='checkid$j' value='$ValueSTR'>";
			
			include"../model/subprogram/stuff_Property.php";//配件属性
			//输出明细
				echo"<tr>";
				//$m=$m+2;
				echo"<td class='A0001' align='center' width='$Field[$m]'>$chooseStr</td>";
				$m=$m+2;
				echo"<td class='A0001' align='center' width='$Field[$m]'>$j</td>";
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]'>$StockId</td>";
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]'>$StuffCname</td>";
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'>$FactualQty</td>";
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'>$AddQty</td>";
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'>$CountQty</td>";
				$m=$m+2;
				echo"<td  width='$Field[$m]' align='right'>$Unreceive</td>";							
				echo"</tr>";
			    $j++;
		     }
		  }
		}while($mainRows = mysql_fetch_array($mainResult));
	    echo"</table></td></tr>";
	}
if ($j==1){
	echo"<tr><td colspan='11' align='center' height='30' class='A0111' style='background-color: #ffffff'><div class='redB' style='background-color: #ffffff'>没有记录!</div></td></tr>";
	}
echo "</table>";
	?>
</form>
</body>
</html>