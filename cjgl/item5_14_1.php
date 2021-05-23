<?php
$Th_Col="序号|30|客户|80|交期|50|采购流水号|95|外发配件名称|240|外发数量|50|选项|30|序号|30|配件ID|45|供应商|65|关联原材料名|260|仓库<br/>楼层|40|单位|30|在库|60|备料数量|55|已备数量|55|备料+|40";
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

$SearchRows="";
if (strlen($tempStuffCname)>1){
	$SearchRows.=" AND (D.StuffCname LIKE '%$tempStuffCname%' OR D.StuffId='$tempStuffCname') ";
	$GysList2="<span class='ButtonH_25'  id='cancelQuery' value='取消查询'   onclick='ResetPage(4,5)'>取消查询</span>";
   }
else{
	$Gys_Result = mysql_query("SELECT G.CompanyId, T.Forshort 
	FROM  cg1_stuffunite U
	INNER JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId = U.POrderId 
	INNER JOIN $DataIn.cg1_stocksheet G  ON G.POrderId = Y.POrderId AND U.StuffId = G.StuffId
	INNER JOIN $DataIn.cg1_stocksheet GU ON U.uStuffId = GU.StuffId AND U.POrderId=GU.POrderId
	INNER JOIN $DataIn.stuffdata D ON D.StuffId = G.StuffId
	LEFT JOIN $DataIn.trade_object  T ON  T.CompanyId = G.CompanyId
	WHERE Y.Estate>0  AND G.CompanyId >0  AND G.StockId>0  AND G.Mid>0
	AND G.CompanyId NOT IN (".$APP_CONFIG['ASH_IN_SUPPLIER'].") GROUP BY G.CompanyId ",$link_id);
	if ($GysRow = mysql_fetch_array($Gys_Result)) {
		$GysList="<select name='GysCompanyId' id='GysCompanyId' onChange='ResetPage(14,5)'>";
		do{
			$thisCompanyId=$GysRow["CompanyId"];
			$thisForshort=$GysRow["Forshort"];
			$GysCompanyId=$GysCompanyId==""?$thisCompanyId:$GysCompanyId;
			if($GysCompanyId==$thisCompanyId){
				$GysList.="<option value='$thisCompanyId' selected>$thisForshort</option>";
				$SearchRows.=" AND  G.CompanyId='$thisCompanyId'";
				}
			else{
				$GysList.= "<option value='$thisCompanyId'>$thisForshort</option>";
				}
			}while($GysRow = mysql_fetch_array($Gys_Result));
		 $GysList.="</select>&nbsp;";
	 }
      $GysList2="<input name='StuffCname' type='text' id='StuffCname' size='16' value='配件名称或Id'   oninput='CnameChanged(this)' onfocus=\"this.value=this.value=='配件名称或Id'?'' : this.value;\"  onblur= \"this.value=this.value=='' ? '配件名称或Id' : this.value;\" style='color:#DDD;'><input class='ButtonH_25' type='button'  id='stuffQuery' value='查询' onclick=\" document.getElementById('tempStuffCname').value=document.getElementById('StuffCname').value;ResetPage(14,5);\" disabled/><input name='tempStuffCname' type='hidden' id='tempStuffCname'/>";
}


$GysList1="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class='ButtonH_25' id='saveBtn' onclick='saveQty(1)' disabled>保存</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class='ButtonH_25' type='button'  id='cancelBtn' value='取消' onclick='ArrayClear()' disabled/>";
echo"<table width='$tableWidth' border='0' cellspacing='0'  style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
<tr>
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

$mySql="SELECT Y.POrderId,O.Forshort,G.StockId,G.StuffId,(G.FactualQty + G.AddQty) AS cgQty, D.StuffCname,D.Picture,PI.Leadweek,IFNULL(PI.Leadtime,PL.Leadtime) AS Leadtime
FROM  cg1_stuffunite U
INNER JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId = U.POrderId
LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber = Y.OrderNumber
LEFT JOIN $DataIn.trade_object O ON O.CompanyId = M.CompanyId
INNER JOIN $DataIn.cg1_stocksheet G ON G.POrderId = U.POrderId AND U.StuffId = G.StuffId
INNER JOIN $DataIn.cg1_stocksheet GU ON U.uStuffId = GU.StuffId AND U.POrderId=GU.POrderId
INNER JOIN $DataIn.stuffdata D ON D.StuffId = G.StuffId
LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=Y.Id
LEFT JOIN $DataIn.yw3_pileadtime PL ON PL.POrderId=Y.POrderId  
WHERE 1 $SearchRows AND Y.Estate>0  AND G.StockId>0 AND G.Mid>0
AND G.CompanyId NOT IN (".$APP_CONFIG['ASH_IN_SUPPLIER'].") GROUP BY G.StockId";
//echo $mySql;
$myResult = mysql_query($mySql,$link_id);
if($myRow = mysql_fetch_array($myResult)){
$i=1;
	do{
		$m=1;
		$mStockId = $myRow["StockId"];
		$Forshort=$myRow['Forshort'];
		$StuffId = $myRow["StuffId"];
		$Picture = $myRow["Picture"];
		$POrderId = $myRow["POrderId"];
		$cgQty= $myRow["cgQty"];
		$StuffCname = $myRow["StuffCname"];
		include "../model/subprogram/stuffimg_model.php";
		include"../model/subprogram/stuff_Property.php";//配件属性
		$Leadtime=$mainRows["Leadtime"];
            $Leadweek=$myRow["Leadweek"];
	        include "../model/subprogram/PI_Leadweek.php";
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

	//echo $mStockId . "/" . $LockRemark;

	    echo"<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'><tr>";
		echo"<td scope='col' class='A0111' width='$Field[$m]' align='center'><input name='checkAll$i' type='checkbox' id='checkAll$i' $checkAllclick></td>";
		$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
        echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Forshort</td>";
        $unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
		echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Leadweek</td>";
		$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
		echo"<td scope='col' class='A0101' width='$Field[$m]' align='center' >$mStockId</td>";
		$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
		echo"<td scope='col' class='A0101' width='$Field[$m]' >$StuffCname</td>";
		$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
		echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$cgQty</td>";
		$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
		echo"<td width='' class='A0101'>";
		$checkStockSql  = "SELECT G.StockId, G.OrderQty,D.StuffId,D.StuffCname,D.Picture,F.Remark,
		        P.Forshort,U.Name AS UnitName,K.tStockQty
				FROM  $DataIn.cg1_stuffunite PU
				INNER JOIN $DataIn.cg1_stocksheet G ON PU.uStuffId = G.StuffId AND PU.POrderId=G.POrderId
				INNER JOIN $DataIn.ck9_stocksheet K ON K.StuffId=G.StuffId 
				INNER JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
				INNER JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId
				INNER JOIN $DataIn.stuffmaintype MT ON MT.Id=T.mainType
				INNER JOIN $DataIn.stuffunit U ON U.Id=D.Unit
				LEFT JOIN  $DataIn.trade_object P ON P.CompanyId=G.CompanyId 
				LEFT JOIN  $DataIn.base_mposition F ON F.Id=D.SendFloor
				WHERE  PU.POrderId='$POrderId' AND PU.StuffId = '$StuffId' ORDER BY D.SendFloor";
		//echo $checkStockSql;
		$checkStockResult=mysql_query($checkStockSql,$link_id);
	            if($checkStockRow=mysql_fetch_array($checkStockResult)){
				  echo"<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
				  $j=1;$llCount=0;
				  $passValue ="";
				  do{
					     $Forshort=$checkStockRow["Forshort"];
						 $StockId=$checkStockRow["StockId"];

						 $StuffId=$checkStockRow["StuffId"];
						 $StuffCname=$checkStockRow["StuffCname"];
						 $UnitName=$checkStockRow["UnitName"];
						 $Picture=$checkStockRow["Picture"];
						 $tStockQty=$checkStockRow["tStockQty"];
						 $OrderQty=$checkStockRow["OrderQty"];
						 $Remark=$checkStockRow["Remark"];
						 //检查是否有图片
						 $d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
			        	 include "../model/subprogram/stuffimg_model.php";
	                     include"../model/subprogram/stuff_Property.php";//配件属性
				    		 //检查已领料数据
						 $checkllQty=mysql_query("SELECT SUM(Qty) AS llQty FROM $DataIn.ck5_llsheet WHERE POrderId = $POrderId AND  StockId='$StockId'",$link_id);
			             $llQty=mysql_result($checkllQty,0,"llQty");
			             $llQty=$llQty==""?0:$llQty;
			             $llQtyTemp=$OrderQty-$llQty;
						 $checkDisabled="";
						 $passValue = "$POrderId@$StockId@$mStockId@$StuffId";
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
					}while($checkStockRow=mysql_fetch_array($checkStockResult));
					echo"</table>";
					echo " <input name='checkId$i' type='checkbox' id='checkId$i'  disabled style='display:none;'>";//防止只有一条配件记录而产生错误
					$i++;
			    }




		echo"</td></tr></table>";

       }while ($myRow = mysql_fetch_array($myResult));
	    $i=$i-1;
	    echo " <input name='cIdNumber' type='hidden' id='cIdNumber' value='$i'/>";
	    echo " <input name='fromPage' type='hidden' id='fromPage' value='4'/>";
    }
else{
	echo"<table width='$tableWidth' border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'><tr><td colspan='".$Cols."' align='center' height='30' class='A0111' style='background-color: #ffffff'><div class='redB' style='background-color: #ffffff'>没有记录!</div></td></tr></table>";
	}


?>

</form>
</body>
</html>