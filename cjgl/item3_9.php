<link href="css/keyboard.css" rel="stylesheet" type="text/css" />
<?php   
$Th_Col="配件|40|ID|30|客户|80|PO|80|中文名|210|Product Code|150|Price|55|Qty|50|Amount|70|采购|45|备料|45|组装|45|待出|45|交期|70|期限|50";
$Field=explode("|",$Th_Col);
$Count=count($Field);
$wField=$Field;
$widthArray=array();
for ($i=0;$i<$Count;$i++){
	$i=$i+1;
	$widthArray[]=$wField[$i];
	$tableWidth+=$wField[$i];
	}
if (isSafari6()==1){
   $tableWidth=$tableWidth+ceil($Count*1.5)+1; 
}
$SearchRows="";
$GysList="";
$nowInfo="当前:可备料订单";

    //显示客户名称
	$ClientResult= mysql_query("SELECT * FROM ( 
   SELECT M.CompanyId,C.Forshort,SUM(if(K.tStockQty>=(G.OrderQty-IFNULL(L.Qty,0)),(G.OrderQty-IFNULL(L.Qty,0)),0)) as K1,SUM(G.OrderQty-IFNULL(L.Qty,0)) AS K2 ,SUM(G.OrderQty) AS blQty,IFNULL(SUM(L.Qty),0) AS llQty
   FROM $DataIn.yw1_ordermain M 
   LEFT JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber 
   LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId 
   LEFT JOIN $DataIn.trade_object C ON M.CompanyId=C.CompanyId
   LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId 
   LEFT JOIN ( 
           SELECT L.StockId,SUM(L.Qty) AS Qty 
           FROM $DataIn.yw1_ordersheet S 
           LEFT JOIN $DataIn.cg1_stocksheet G ON S.POrderId=G.POrderId 
           LEFT JOIN $DataIn.ck5_llsheet L ON G.StockId=L.StockId 
           WHERE 1 AND S.scFrom>0 AND S.Estate=1 GROUP BY L.StockId
         ) L ON L.StockId=G.StockId 
   LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=G.StuffId 
   LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
   LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=D.TypeId 
   WHERE 1 and S.scFrom>0 AND S.Estate=1 AND ST.mainType<2 GROUP BY S.POrderId 
) A WHERE A.K1>=A.K2  AND A.blQty!=A.llQty Group by A.CompanyId",$link_id);
	if ($ClientRow = mysql_fetch_array($ClientResult)){
		echo"<select name='CompanyId' id='CompanyId' onchange='ResetPage(1,3)'>";
        echo"<option value='' selected>全部</option>";
		do{
			$theCompanyId=$ClientRow["CompanyId"];
            $theForshort=$ClientRow["Forshort"];
			if($CompanyId==$theCompanyId){
				echo"<option value='$theCompanyId' selected>$theForshort</option>";
				$SearchRows.="and M.CompanyId='$theCompanyId'";
				}
			else{
				echo"<option value='$theCompanyId'>$theForshort</option>";
				}
			}while($ClientRow = mysql_fetch_array($ClientResult));
		echo"</select>&nbsp;";
		}      

//步骤5：
echo"<table width='$tableWidth' border='0' cellspacing='0'  style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr>
	<td colspan='8' height='40px' class=''>$GysList $TypeList</td><td colspan='4' class=''  align='right'>$GysList1</td><td colspan='4' align='right' class=''><input name='NowInfo' type='text' id='NowInfo' value='$nowInfo' class='text' disabled></td></tr></table>";
	echo "<table width='$tableWidth' border='0' cellspacing='0'  style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'><tr>";
	//输出表格标题
	for($i=0;$i<$Count;$i=$i+2){
		$Class_Temp=$i==0?"A1111":"A1101";
		$j=$i;
		$k=$j+1;
		echo"<td width='$Field[$k]' class='' height='25px' style='background-color: #F0F5F8'> <div align='center'>$Field[$j]</div></td>";
		}
	echo"</tr>";
$DefaultBgColor=$theDefaultColor;
$i=1;
$mySql="SELECT * FROM (
SELECT M.CompanyId,O.Forshort,S.OrderPO,M.OrderDate,M.ClientOrder,M.Operator,S.Id,S.POrderId,S.ProductId,S.Qty,S.Price,S.PackRemark,S.cgRemark,S.sgRemark
,S.DeliveryDate,S.Estate,S.Locks,P.cName,P.eCode,P.Weight,P.TestStandard,P.pRemark,P.bjRemark,PI.PI,PI.Leadtime,SUM(if(K.tStockQty>=(G.OrderQty-IFNULL(L.Qty,0)),(G.OrderQty-IFNULL(L.Qty,0)),0)) as K1,SUM(G.OrderQty-IFNULL(L.Qty,0)) AS K2,SUM(G.OrderQty) AS blQty,IFNULL(SUM(L.Qty),0) AS llQty
FROM $DataIn.yw1_ordermain M
LEFT JOIN $DataIn.trade_object O ON M.CompanyId = O.CompanyId
LEFT JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber
LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id
LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
LEFT JOIN (
             SELECT * FROM  (SELECT L.StockId,SUM(L.Qty) AS Qty FROM $DataIn.yw1_ordersheet S 
             LEFT JOIN $DataIn.cg1_stocksheet G ON S.POrderId=G.POrderId
             LEFT JOIN $DataIn.ck5_llsheet L ON G.StockId=L.StockId 
             WHERE 1  AND S.Estate>0 GROUP BY L.StockId)A where A.StockId IS NOT NULL
         ) L ON L.StockId=G.StockId
LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=G.StuffId
LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=D.TypeId
WHERE 1 and S.Estate>0 AND ST.mainType<2  $SearchRows  GROUP BY S.POrderId ) A 
WHERE A.K1>=A.K2 AND A.blQty!=A.llQty ORDER BY A.OrderDate ASC,A.Id DESC";
$mainResult = mysql_query($mySql,$link_id);
if($myRow = mysql_fetch_array($mainResult)){
$SumQty=0;
	do{
		$m=1;
        $AskDay="";
		$Id=$myRow["Id"];
		$OrderSignColor="bgColor='#FFFFFF'";
		$theDefaultColor=$DefaultBgColor;
		$OrderPO=$myRow["OrderPO"];
		$POrderId=$myRow["POrderId"];
        $OrderDate=$myRow["OrderDate"];
        include "../admin/order_date.php";
        if($blSign==1){ continue;}

		$ProductId=$myRow["ProductId"];
		$cName=$myRow["cName"];
		$eCode=$myRow["eCode"];		
		$Weight=$myRow["Weight"];$MainWeight=$myRow["MainWeight"]==0?"&nbsp;":$myRow["MainWeight"];
		$TestStandard=$myRow["TestStandard"];
		include "../admin/Productimage/getPOrderImage.php";
		$Unit=$myRow["Unit"];
		$Qty=$myRow["Qty"];
      
		$Price=sprintf("%.3f",$myRow["Price"]);
       $thisSaleAmount=sprintf("%.2f",$Qty*$Price);//本订单卖出金额
		$PackRemark=$myRow["PackRemark"];
		$sgRemark=$myRow["sgRemark"];
		$DeliveryDate=$myRow["DeliveryDate"]=="0000-00-00"?"":$myRow["DeliveryDate"];
		$Leadtime=$myRow["Leadtime"]==""?"&nbsp;":$myRow["Leadtime"];
		//读取操作员姓名
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		//如果超过30天
		$AskDay=AskDay($OrderDate);
		$BackImg=$AskDay==""?"":"background='../images/$AskDay'";
		$OrderDate=CountDays($OrderDate,0);
		$Estate=$myRow["Estate"];
		$LockRemark=$Estate==4?"已生成出货单.":"";
		$Locks=$myRow["Locks"];	
		
		 //加急订单锁定操作，整单锁和单个配件锁都不能备料
         $Lock_Result=mysql_fetch_array(mysql_query("
                                  SELECT POrderId FROM $DataIn.yw2_orderexpress   WHERE POrderId='$POrderId' AND Type='2'
                                  UNION ALL
                                  SELECT POrderId FROM (SELECT LEFT(GL.StockId,12) AS POrderId,GL.Locks 
                                  FROM $DataIn.cg1_lockstock GL,$DataIn.cg1_stocksheet G 
                                  WHERE GL.Locks=0 AND GL.StockId=G.StockId GROUP BY POrderId) K WHERE K.POrderId='$POrderId'",$link_id));
       $newPOrderId=$Lock_Result["POrderId"];
       $Locks=$newPOrderId==""?1:0;
       $LockRemarks="";
       $LockRemarks=$Locks==1?"":"<span class='redB'>订单已锁</span>";
       $LockRemarks=$TestStandardSign==1?"":"<span class='redB'>订单已锁</span>";
       if ($LockRemarks!="" || $Locks==0) continue;
       
         $SumQty=$SumQty+$Qty;
			//订单状态色：有未下采购单，则为白色
			$checkColor=mysql_query("SELECT G.Id,G.StockId FROM $DataIn.cg1_stocksheet G 
			LEFT JOIN $DataIn.stuffdata D ON G.StuffId=D.StuffId
			LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
			WHERE 1 AND T.mainType<2 AND G.Mid='0' and (G.FactualQty>'0' OR G.AddQty>'0' ) and G.PorderId='$POrderId'",$link_id);
		if($checkColorRow = mysql_fetch_array($checkColor)){
					$OrderSignColor="bgColor='#FFFFFF'";//有未下需求单
				  do{   
					 $StockId=$checkColorRow["StockId"];
					 $CheckStockSql=mysql_query("SELECT * FROM $DataIn.cg1_lockstock WHERE StockId ='$StockId' AND Locks=0 LIMIT 1",$link_id);
					 if($CheckStockRow=mysql_fetch_array($CheckStockSql)) 
						{      
							   $OrderSignColor="bgColor='#0099FF'";	 break; //找到一个跳出当前循环  
						}
					}while($checkColorRow = mysql_fetch_array($checkColor));
			
				}
			else{//已全部下单	
				$OrderSignColor="bgColor='#339900'";	//设默认绿色
				//生产数量与工序数量不等时，黄色//工序总数
				$CheckgxQty=mysql_fetch_array(mysql_query("SELECT SUM(G.OrderQty) AS gxQty 
				FROM $DataIn.cg1_stocksheet G
				LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
				LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
				WHERE G.POrderId='$POrderId' AND T.mainType=3",$link_id));
				$gxQty=$CheckgxQty["gxQty"];
				//已完成的工序数量
				$CheckscQty=mysql_fetch_array(mysql_query("SELECT SUM(C.Qty) AS scQty FROM $DataIn.sc1_cjtj C WHERE C.POrderId='$POrderId'",$link_id));
				$scQty=$CheckscQty["scQty"];
	
				if($gxQty!=$scQty){
					$OrderSignColor="bgColor='#FFCC00'";
					}
               }

				//动态读取 $thisTOrmbINo
		$showPurchaseorder="[ + ]";
		$ListRow="<tr bgcolor='#D9D9D9' id='ListRow$i' style='display:none'><td class='A0111' height='30' colspan='$Count'><br><div id='ShowDiv$i'>&nbsp;</div><br></td></tr>";
            echo"<tr><td class='A0111' align='center' id='theCel$i' height='25' valign='middle' onClick='ShowOrHide(ListRow$i,theCel$i,$i,$POrderId);' >$showPurchaseorder</td>";
            echo "<td class='A0101' align='center' $OrderSignColor>$i</td>";
            echo "<td class='A0101' align='center'>".$myRow['Forshort']."</td>";
			echo "<td class='A0101' align='center'>$OrderPO</td>";
			echo"<td class='A0101' align='left'>$TestStandard</td>";
			echo"<td class='A0101' align='left'>$eCode</td>";
			echo"<td class='A0101' align='right'>$Price</td>";	
			echo"<td class='A0101' align='right'>$Qty</td>";
			echo"<td class='A0101' align='right'>$thisSaleAmount</td>";
            echo"<td class='A0101' align='center'>$wl_cycle</td>";
            echo"<td class='A0101' align='center'>$bl_cycle</td>";
            echo"<td class='A0101' align='center'>$sc_cycle</td>";
            echo"<td class='A0101' align='center'>$sctj_date</td>";
            echo"<td class='A0101' align='center'>$Leadtime</td>";
            echo"<td class='A0101' align='center' $BackImg>$OrderDate</td>";
			echo"</tr>";  
           echo $ListRow;
			$i++;
		 }while($myRow = mysql_fetch_array($mainResult));
          echo"<tr><td class='A0111' align='center' valign='middle' height='25'  colspan=7'>总    计</td>";
		  echo"<td class='A0101' align='right'>$SumQty</td>";
		  echo"<td class='A0101' align='center' colspan='7'>&nbsp;</td></tr>";
		  echo "</table>";
       }	
else{
	echo"<tr><td colspan='18' align='center' height='30' class='A0111'><div class='redB' style='background-color: #ffffff'>没有记录!</div></td></tr>";
	   }
echo "</table>";
	?>
</form>
</body>
</html>