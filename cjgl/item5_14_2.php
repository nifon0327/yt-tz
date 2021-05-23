<link href="css/keyboard.css" rel="stylesheet" type="text/css" />
<?php
$Th_Col="序号|30|客户|80|交期|50|皮套半成品名|200|半成品名称|200|半成品ID|50|外发数|50|选项|30|序号|30|配件ID|45|供应商|65|原材料名称|250|仓库<br/>楼层|40|单位|30|在库|60|备料数量|55|已备数量|55|备料+|40";
$nowInfo="当前:需备料订单";
$Field=explode("|",$Th_Col);
$Count=count($Field);
$Cols = $Count/2;
$wField=$Field;
$widthArray=array();
for ($i=0;$i<$Count;$i++){
	$i=$i+1;
	$widthArray[]=$wField[$i];
	$tableWidth+=$wField[$i];
	}
//if (isSafari6()==1){
   $tableWidth=$tableWidth+ceil($Count*1.5)+1;
//}

$SearchRows=" AND SC.scFrom=1 AND SC.Estate>0 AND SC.WorkShopId=0 AND SC.ActionId = 105 ";

if (strlen($tempStuffCname)>1){
	$SearchRows.=" AND (D.StuffCname LIKE '%$tempStuffCname%' OR D.StuffId='$tempStuffCname') ";
	$GysList2="<span class='ButtonH_25' id='cancelQuery' value='取消查询'   onclick='ResetPage(4,5)'>取消查询</span>";
   }
else{
	$Gys_Result = mysql_query("SELECT T.CompanyId, T.Forshort 
	  FROM (
	    SELECT CG.CompanyId,SC.sPOrderId 
	    FROM $DataIn.yw1_scsheet SC
	    INNER JOIN $DataIn.cg1_semifinished  SM ON SM.mStockId = SC.mStockId
	    INNER JOIN $DataIn.cg1_stocksheet    CG ON CG.StockId = SC.mStockId
	    WHERE 1 $SearchRows AND CG.Mid=0 
	 )S 
	 INNER JOIN $DataIn.trade_object  T ON  T.CompanyId = S.CompanyId
	 WHERE  getCanStock(S.sPOrderId,1)=1    GROUP BY  S.CompanyId",$link_id);
	if ($GysRow = mysql_fetch_array($Gys_Result)) {
		$GysList="<select name='CompanyId' id='CompanyId' onChange='ResetPage(14,5)'>";
		do{
			$thisCompanyId=$GysRow["CompanyId"];
			$thisForshort=$GysRow["Forshort"];
			$CompanyId=$CompanyId==""?$thisCompanyId:$CompanyId;
			if($CompanyId==$thisCompanyId){
				$GysList.="<option value='$thisCompanyId' selected>$thisForshort</option>";
				$SearchRows.=" AND  CG.CompanyId='$thisCompanyId'";
				}
			else{
				$GysList.= "<option value='$thisCompanyId'>$thisForshort</option>";
				}
			}while($GysRow = mysql_fetch_array($Gys_Result));
		 $GysList.="</select>&nbsp;";
	 }
      $GysList2="<input name='StuffCname' type='text' id='StuffCname' size='16' value='配件名称或Id'   oninput='CnameChanged(this)' onfocus=\"this.value=this.value=='配件名称或Id'?'' : this.value;\"  onblur= \"this.value=this.value=='' ? '配件名称或Id' : this.value;\" style='color:#DDD;'><input class='ButtonH_25' type='button'  id='stuffQuery' value='查询' onclick=\" document.getElementById('tempStuffCname').value=document.getElementById('StuffCname').value;ResetPage(14,5);\" disabled/><input name='tempStuffCname' type='hidden' id='tempStuffCname'/>";
}


$GysList1="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class='ButtonH_25' id='saveBtn' onclick='saveQty(2)' disabled>保存</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class='ButtonH_25' type='button'  id='cancelBtn' value='取消' onclick='ArrayClear()' disabled/>";
echo"<table width='$tableWidth' border='0' cellspacing='0'  style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
<tr >
<td colspan='".($Cols-8)."' height='40px' class=''> $OutTypeList $GysList $GysList2</td><td colspan='4' class=''  align='right'> $GysList1</td><td colspan='4' align='right' class=''><input name='NowInfo' type='text' id='NowInfo' value='$nowInfo' class='text' disabled></td></tr></table>";
echo "<table width='$tableWidth' border='0' cellspacing='0'  style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'><tr>";
//输出表格标题
for($i=0;$i<$Count;$i=$i+2){
	$Class_Temp=$i==0?"A1111":"A1101";
	$j=$i;
	$k=$j+1;
	echo"<td width='$Field[$k]' class='' height='25px' style='background-color: #F0F5F8'><div align='center'>$Field[$j]</div></td>";
	}
echo"</tr></table>";
$DefaultBgColor=$theDefaultColor;
$i=1;

$checkBlSign=1;//可备料标识
$mySql="SELECT A.* FROM (
	SELECT  SC.POrderId,O.Forshort,SC.sPOrderId,SC.Qty,M.PurchaseID,M.Remark,SC.mStockId,SC.ActionId,
	(CG.addQty+CG.FactualQty) AS xdQty,
	D.StuffId,D.StuffCname,D.Price,D.Picture,D.Gfile,D.Gstate,
	IF(CG.DeliveryWeek>0,CG.DeliveryDate,'2099-12-31') AS DeliveryDate,CG.DeliveryWeek
	FROM  $DataIn.yw1_scsheet SC
    LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId = SC.POrderId
    LEFT JOIN $DataIn.yw1_ordermain OM ON OM.OrderNumber = Y.OrderNumber
    LEFT JOIN $DataIn.trade_object O ON O.CompanyId = OM.CompanyId
	INNER  JOIN $DataIn.cg1_semifinished  SM ON SM.mStockId = SC.mStockId
	INNER  JOIN $DataIn.cg1_stocksheet    CG ON CG.StockId = SC.mStockId
	INNER  JOIN $DataIn.stuffdata         D  ON D.StuffId = CG.StuffId 
	LEFT  JOIN $DataIn.yw1_ordersheet    S  ON S.POrderId = SC.POrderId 
	LEFT  JOIN $DataIn.workshopdata      W  ON W.Id = SC.WorkShopId
	LEFT  JOIN $DataIn.cg1_stockmain     M  ON M.Id = CG.Mid 
	WHERE  1 $SearchRows AND CG.DeliveryWeek>0 GROUP BY SC.sPOrderId
	) A  
	WHERE getCanStock(A.sPOrderId,$checkBlSign)=$checkBlSign  ORDER BY DeliveryDate,sPOrderId";
//echo $mySql;
/*
$mySql="SELECT * FROM (
SELECT  SC.POrderId,SC.sPOrderId,SC.Qty,SC.mStockId,SC.ActionId,
D.StuffId,D.StuffCname,D.Price,D.Picture,D.Gfile,D.Gstate,CG.DeliveryDate,CG.DeliveryWeek,
SUM(if(K.tStockQty>=(G.OrderQty-IFNULL(L.Qty,0)),(G.OrderQty-IFNULL(L.Qty,0)),0)) as K1,
SUM(G.OrderQty-IFNULL(L.Qty,0)) AS K2,
IFNULL(SUM(G.OrderQty),0)  AS blQty,
IFNULL(SUM(L.Qty),0) AS llQty
FROM  $DataIn.yw1_scsheet SC
LEFT  JOIN $DataIn.yw1_stocksheet    G  ON G.sPOrderId = SC.sPOrderId
LEFT  JOIN $DataIn.cg1_stocksheet    SG ON SG.StockId = G.StockId
LEFT  JOIN $DataIn.workshopdata      W  ON W.Id = SC.WorkShopId
LEFT  JOIN $DataIn.cg1_stocksheet    CG  ON CG.StockId = SC.mStockId
LEFT  JOIN $DataIn.stuffdata         D  ON D.StuffId = CG.StuffId
LEFT  JOIN $DataIn.stufftype         T  ON T.TypeId = D.TypeId
LEFT  JOIN $DataIn.ck9_stocksheet    K ON K.StuffId=G.StuffId
LEFT  JOIN (
             SELECT L.StockId,SUM(L.Qty) AS Qty
             FROM $DataIn.yw1_scsheet SC
             LEFT JOIN $DataIn.yw1_stocksheet  G ON G.sPOrderId=SC.sPOrderId
             LEFT JOIN $DataIn.ck5_llsheet L ON G.StockId=L.StockId  AND L.sPOrderId = SC.sPOrderId
             WHERE 1  AND SC.scFrom =1 AND SC.Estate>0 AND SC.Level != 1
             GROUP BY L.StockId
		    ) L ON L.StockId=G.StockId
WHERE  1 $SearchRows AND SG.blsign = 1  GROUP BY SC.sPOrderId ORDER BY SC.Id) A  WHERE 1 AND  A.K1>=A.K2 AND A.blQty!=A.llQty";
*/
//echo $mySql; //AND  A.K1>=A.K2 AND A.blQty!=A.llQty
$myResult = mysql_query($mySql,$link_id);
if($myRow = mysql_fetch_array($myResult)){
$i=1;
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Qty=$myRow["Qty"];
		$xdQty=$myRow["xdQty"];
		$Relation=$Qty/$xdQty;
		$Forshort=$myRow['Forshort'];
        $POrderId=$myRow["POrderId"];
        $sPOrderId=$myRow["sPOrderId"];
        $mStockId=$myRow["mStockId"];
        $ActionId=$myRow["ActionId"];
		$StuffId=$myRow["StuffId"];
		$StuffCname=$myRow["StuffCname"];
		$Picture=$myRow["Picture"];
        include "../model/subprogram/stuffimg_model.php";
		include"../model/subprogram/stuff_Property.php";//配件属性

        $DeliveryDate = $myRow["DeliveryWeek"]>0?$myRow["DeliveryDate"]:"";
        $DeliveryWeek = $myRow["DeliveryWeek"];//本配件的交期
        include "../model/subprogram/deliveryweek_toweek.php";

		if($SubAction==31){//有权限
		    $checkAllclick="onclick=\"checkAll(this,$i);\" ";
			$checkDisabled="";
		    }
		else{
			$checkAllclick="";
			$checkDisabled="disabled";
		  }

		  //检查是否未确定产品，是则锁定并标底色
		$LockRemark='';
		$CheckSignSql=mysql_query("SELECT Id FROM $DataIn.yw2_orderexpress WHERE POrderId ='$POrderId' AND Type='2' LIMIT 1",$link_id);
		if($CheckSignRow=mysql_fetch_array($CheckSignSql)){
			$LockRemark="业务锁定,未确定产品";
			$OrderSignColor="bgcolor='#FF0000'";
		}

		//检查是否锁定
		$CheckSignSql=mysql_query("SELECT Id FROM $DataIn.cg1_lockstock WHERE StockId ='$mStockId' AND Locks=0 LIMIT 1",$link_id);
		if($CheckSignRow=mysql_fetch_array($CheckSignSql)){
			  $LockRemark="采购锁定,未确定产品";
			  $OrderSignColor="bgcolor='#FF0000'";
		}
		else{
			$LocksResult=mysql_fetch_array(mysql_query("SELECT getStockIdLock('$mStockId') AS Locks",$link_id));
			$mLocks = $LocksResult['Locks'];

			if ($mLocks>0){
				 $LockRemark="半成品锁定";
				 $OrderSignColor="bgcolor='#FF0000'";
			}
	  }

        if ($LockRemark!=""){
	        continue;
        }


        //取最上层半成品名称

		$semiRow=mysql_fetch_array(mysql_query("SELECT D.StuffCname 
		FROM $DataIn.cg1_semifinished  S   
		LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId = S.mStockId
		LEFT JOIN $DataIn.stuffdata D ON D.StuffId = G.StuffId
		WHERE S.POrderId='$POrderId' AND G.Level = 1 LIMIT 1",$link_id));
        $semiStuffCname = $semiRow["StuffCname"];


        echo"<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'><tr>";
		echo"<td scope='col' class='A0111' width='$Field[$m]' align='center'><input name='checkAll$i' type='checkbox' id='checkAll$i' $checkAllclick></td>";
		$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
        echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Forshort</td>";
        $unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
		echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$DeliveryWeek</td>";
		$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
		echo"<td scope='col' class='A0101' width='$Field[$m]'>$semiStuffCname</td>";
		$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;

		echo"<td scope='col' class='A0101' width='$Field[$m]'>$StuffCname</td>";
		$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
		echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$StuffId</td>";
		$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
		echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Qty</td>";
		$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
		echo"<td width='' class='A0101'>";

		//订单产品对应的配件信息
	  //echo $Relation;
		$checkStockSql=mysql_query("SELECT ROUND(A.OrderQty*$Relation,1) AS OrderQty,A.StockId,
		        K.tStockQty,D.StuffId,D.StuffCname,D.Picture,F.Remark,M.Name,
		        P.Forshort,U.Name AS UnitName,U.Decimals 
				FROM  $DataIn.cg1_semifinished   A 
                INNER JOIN $DataIn.cg1_stocksheet G  ON G.StockId = A.StockId
				INNER JOIN $DataIn.ck9_stocksheet K ON K.StuffId=A.StuffId 
				INNER JOIN $DataIn.stuffdata D ON D.StuffId=A.StuffId 
				INNER JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId
				INNER JOIN $DataIn.stuffmaintype MT ON MT.Id=T.mainType
				INNER JOIN $DataIn.stuffunit U ON U.Id=D.Unit
				LEFT JOIN  $DataIn.staffmain M ON M.Number=G.BuyerId 
				LEFT JOIN  $DataIn.trade_object P ON P.CompanyId=G.CompanyId 
				LEFT JOIN  $DataIn.base_mposition F ON F.Id=D.SendFloor
				WHERE  A.POrderId='$POrderId' AND A.mStockId='$mStockId' AND G.blSign=1 ORDER BY D.SendFloor",$link_id);

	            if($checkStockRow=mysql_fetch_array($checkStockSql)){
				  echo"<table width='100%' border='0' cellspacing='0' id='ListTable$i' 
				       style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
				  $j=1;$llCount=0;
				  $passValue ="";
				  do{
						 $Name=$checkStockRow["Name"];
					     $Forshort=$checkStockRow["Forshort"];
						 $StockId=$checkStockRow["StockId"];

						 $Decimals=$checkStockRow["Decimals"];
						 $StuffId=$checkStockRow["StuffId"];
						 $StuffCname=$checkStockRow["StuffCname"];
						 $UnitName=$checkStockRow["UnitName"];
						 $Picture=$checkStockRow["Picture"];
						 $tStockQty=round($checkStockRow["tStockQty"],$Decimals);
						 $OrderQty= round($checkStockRow["OrderQty"],$Decimals);
						 $Remark=$checkStockRow["Remark"];
						 //检查是否有图片
						 $d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
			        	 include "../model/subprogram/stuffimg_model.php";
	                     include"../model/subprogram/stuff_Property.php";//配件属性
				    		 //检查已领料数据
						 $checkllQty=mysql_query("SELECT SUM(Qty) AS llQty FROM $DataIn.ck5_llsheet WHERE sPOrderId = $sPOrderId AND  StockId='$StockId'",$link_id);
			             $llQty=mysql_result($checkllQty,0,"llQty");
			             $llQty=$llQty==""?0:$llQty;
			             $llQtyTemp=$OrderQty-$llQty;
						 $checkDisabled="";
						 $passValue = "$POrderId@$sPOrderId@$StockId@$mStockId@$WorkShopId@$ActionId@$StuffId";
					     if ($llQtyTemp<=0){//是否已领料
							 $llIMG="";
							 $llonClick="bgcolor='#96FF2D'";
							 $llCount+=1;//已领料数据
							 $checkDisabled="disabled";
					         }
						 else{
					        if ($tStockQty<=0 || $LockRemark!=''){//判断在库量是否可进行领料
						       $llIMG="<img src='../images/registerNo.png' width='30' height='30' title='$LockRemark'>";
						        $llonClick="";
								$checkDisabled="disabled";
					          }
						    else{
								//检查权限
							    if($SubAction==31){//有权限  && $Estate==1
								   $temQty=$llQtyTemp>$tStockQty?$tStockQty:$llQtyTemp;

							       $llIMG="<img src='../images/register.png' width='30' height='30'>";
								   $llonClick=" onclick='showKeyboard(this,$i,$j,$temQty, $temQty,\"$passValue\")'";
								   }
								else{
									$llonClick="";
						            $llIMG="<img src='../images/registerNo.png' width='30' height='30'>";
									$checkDisabled="disabled";
								   }
						       }
					    }

			            //库存量是否充足
						$bgColor=($OrderQty-$llQty>$tStockQty && $llQtyTemp>0)?"bgcolor='#FFCC66'":"";
						$checkIdclick="onclick=\"checkId(this,$i,$j);\" ";
						echo "<tr height='30'>";
						$unitFirst=$Field[$m]-1;
						echo "<td class='A0101' width='$unitFirst' align='center'>
						  <input name='checkId$i' type='checkbox' id='checkId$i' value='$passValue' $checkIdclick $checkDisabled></td>";
						$m=$m+2;
						echo"<td class='A0101' width='$unitFirst' align='center' $bgColor>$j</td>";//序号
						$m=$m+2;
						echo"<td class='A0101' width='$Field[$m]' align='center'>$StuffId</td>";	//采购
						$m=$m+2;
						echo"<td class='A0101' width='$Field[$m]'  $PrintClick>$Forshort </td>";//供应商
						$m=$m+2;
						echo"<td class='A0101' width='$Field[$m]'>$StuffCname</td>";	//配件名称
						$m=$m+2;
						echo"<td class='A0101' width='$Field[$m]' align='center'>$Remark</td>"; //仓库楼层
						$m=$m+2;
						echo"<td class='A0101' width='$Field[$m]' align='center'>$UnitName</td>";  //单位
						$m=$m+2;
						echo"<td class='A0101' width='$Field[$m]' align='right'>$tStockQty</td>";//库存数量
						$m=$m+2;
						echo"<td class='A0101' width='$Field[$m]' align='right'>$OrderQty</td>";//备料数量
						$m=$m+2;
						echo"<td class='A0101' width='$Field[$m]' align='right'>$llQty</td>";//已领料数量
						$m=$m+2;
						echo"<td class='A0100' align='center' style='color:#FF0000;' $llonClick>$llIMG</td>";	//领料
						echo"</tr>";
						$j++;
					}while($checkStockRow=mysql_fetch_array($checkStockSql));
					echo"</table>";
					echo " <input name='checkId$i' type='checkbox' id='checkId$i'  disabled style='display:none;'>";//防止只有一条配件记录而产生错误
					$i++;
			    }
			echo"</td></tr></table>";

        }while ($myRow = mysql_fetch_array($myResult));
	    $i=$i-1;
	    echo " <input name='cIdNumber' type='hidden' id='cIdNumber' value='$i'/>";
	    echo " <input name='fromPage' type='hidden' id='fromPage' value='3'/>";
    }
else{
	echo"<table width='$tableWidth' border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'><tr><td colspan='".$Cols."' align='center' height='30' class='A0111' style='background-color: #ffffff'><div class='redB' style='background-color: #ffffff'>没有记录!</div></td></tr></table>";
	}
?>

</form>
</body>
</html>
