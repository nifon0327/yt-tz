<?php
//电信-zxq 2012-08-01
include "../model/modelhead.php";
?>
<script>
function zhtj(obj){
	document.form1.action="ck_bl_read.php";
	document.form1.submit();
}
</script>
<?php
//步骤2：需处理
$ColsNumber=14;
$tableMenuS=500;
ChangeWtitle("$SubCompany 仓库备料记录列表");
$funFrom="ck_bl";
$From=$From==""?"read":$From;
$sumCols="12,13";			//求和列,需处理
$MergeRows=4;
$Th_Col="序号|30|生产日期|70|订单PO|60|产品名称|200|订单数量|60|序号|30|采  购|40|供应商|70|配件名称|250|仓库<br/>楼层|30|在库|60|备料数量|55|已备数量|55|备料+|40";
//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 500;							//每页默认记录数量2,3,4,
//$ActioToS="1";
//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-可选条件下拉框
if($From!="slist"){	//非查询：过滤采购、结付方式、供应商、月份
	$SearchRows="";
	$checkDay=$checkDay==""?"1":$checkDay;
	//日期可选
	$selStr="selFlag" . $checkDay;
	$$selStr="selected";
	$GysList="<select name='checkDay' id='checkDay' onchange='ResetPage(2,5)'>";
	$GysList.="<option value='1' $selFlag1>今天</option>";
	$GysList.="<option value='2' $selFlag2>明天</option>";
	$GysList.="<option value='0' $selFlag0>全部</option>";
	$GysList.="</select>";
    $curDate=date("Y-m-d");
	switch($checkDay*1){
		case 1:
		   $SearchRows=" AND B.blDate='$curDate'";
		   break;
		case 2:
		   $curDate=date("Y-m-d",strtotime("$curDate  +1   day"));
		   $SearchRows=" AND B.blDate='$curDate'";
		   break;
		case 0:
		   $SearchRows="";
		   break;
	}
	if ($checkDay==0) $SearchRows.=" AND B.Estate>0 ";//全部只显示未备料的订单

    //显示客户名称
	$ClientResult= mysql_query("SELECT M.CompanyId,C.Forshort 
					 FROM $DataIn.yw9_blsheet B 
					 LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=B.POrderId  
					 LEFT JOIN $DataIn.yw1_ordermain M  ON M.OrderNumber=S.OrderNumber 
					 LEFT JOIN $DataIn.trade_object C ON M.CompanyId=C.CompanyId
	                 LEFT JOIN $DataPublic.currencydata R ON R.Id=C.Currency 
					 WHERE 1 $SearchRows GROUP BY M.CompanyId order by M.CompanyId",$link_id);
	if ($ClientRow = mysql_fetch_array($ClientResult)){
		$GysList.="<select name='CompanyId' id='CompanyId' onchange='ResetPage(2,5)'>";
		do{
			$theCompanyId=$ClientRow["CompanyId"];
			$theForshort=$ClientRow["Forshort"];
			$CompanyId=$CompanyId==""?$theCompanyId:$CompanyId;
			if($CompanyId==$theCompanyId){
				$GysList.="<option value='$theCompanyId' selected>$theForshort</option>";
				$SearchRows.="and M.CompanyId='$theCompanyId'";
				$DefaultClient=$theForshort;
				}
			else{
				$GysList.="<option value='$theCompanyId'>$theForshort</option>";
				}
			}while($ClientRow = mysql_fetch_array($ClientResult));
		    $GysList.="</select>&nbsp;";
		}
	  echo $GysList;
	}
//检查进入者是否采购
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT B.POrderId,B.Id,B.blDate,B.Estate,A.Name AS Operator,S.OrderPO,S.Qty,P.cName,P.TestStandard,P.ProductId  
        FROM $DataIn.yw9_blsheet B 
        LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=B.POrderId 
		LEFT JOIN $DataIn.yw1_ordermain M  ON M.OrderNumber=S.OrderNumber 
		LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId 
		LEFT JOIN $DataPublic.staffmain A ON A.Number=B.Operator 
        WHERE 1 $SearchRows  AND B.Estate<2  ORDER BY B.Id DESC";
$mainResult = mysql_query($mySql." $PageSTR",$link_id);
if($mainRows = mysql_fetch_array($mainResult)){
	$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
	do{
		$m=1;
		//备料主单信息
		$blId=$mainRows["Id"];
		$POrderId=$mainRows["POrderId"];
		$ProductId=$mainRows["ProductId"];
		$blDate=$mainRows["blDate"];
		$OrderPO=$mainRows["OrderPO"];
		$blOperator=$mainRows["Operator"];
		$Qty=$mainRows["Qty"];
		$Estate=$mainRows["Estate"];
		$cName=$mainRows["cName"];
		$TestStandard=$mainRows["TestStandard"];
		include "../admin/Productimage/getPOrderImage.php";
		$bgIdColor=$Estate==2?"bgcolor='#EE7466'":"";
		if($Keys==31 && $Estate==1){//有权限
		    // $checkAllclick="onclick=\"checkAll(this,$i);\" ";
			 $checkAllclick="";
			 $checkDisabled="";
		    }
		else{
			$checkAllclick="";
			$checkDisabled="disabled";
		  }

		//输出订单信息
		       echo"<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'><tr>";
				//echo"<td scope='col' class='A0111' width='$Field[$m]' align='center'><input name='checkAll$i' type='checkbox' id='checkAll$i' $checkAllclick></td>";//备料编号blId
				echo"<td scope='col' class='A0111' width='$Field[$m]' align='center'>$i</td>";
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$blDate<br />$blOperator</td>";	//备料日期
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center' $bgIdColor>$OrderPO</td>";		//订单PO
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]'>$TestStandard</td>";		//产品名称
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Qty</td>";		//订单数量
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				//echo"<td width='$unitWidth' class='A0101'>";
				echo"<td width='' class='A0101'>";
		//订单产品对应的配件信息
		$checkStockSql=mysql_query("SELECT G.OrderQty,G.StockId,K.tStockQty,D.StuffId,D.StuffCname,D.Picture,F.Remark,M.Name,P.Forshort 
				FROM $DataIn.cg1_stocksheet G 
				LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=G.StuffId 
				LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
				LEFT JOIN $DataPublic.staffmain M ON M.Number=G.BuyerId 
				LEFT JOIN $DataIn.trade_object P ON P.CompanyId=G.CompanyId 
				LEFT JOIN $DataIn.base_mposition F ON F.Id=D.SendFloor
				LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId
				WHERE G.POrderId='$POrderId' AND T.mainType<2 ORDER BY D.SendFloor",$link_id);
          if($checkStockRow=mysql_fetch_array($checkStockSql)){
			  echo"<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
			  $j=1;$llCount=0;
			  do{
					 $Name=$checkStockRow["Name"];
				     $Forshort=$checkStockRow["Forshort"];
					 $StockId=$checkStockRow["StockId"];
					 $StuffId=$checkStockRow["StuffId"];
					 $StuffCname=$checkStockRow["StuffCname"];
					 $Picture=$checkStockRow["Picture"];
					 $tStockQty=$checkStockRow["tStockQty"];
					 $OrderQty=$checkStockRow["OrderQty"];
					 $Remark=$checkStockRow["Remark"];
					//检查是否有图片
					 $d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
		        	include "../model/subprogram/stuffimg_model.php";
			    		 //检查已领料数据
					 $checkllQty=mysql_query("SELECT SUM(Qty) AS llQty FROM $DataIn.ck5_llsheet WHERE StockId='$StockId'",$link_id);
		              $llQty=mysql_result($checkllQty,0,"llQty");
		              $llQty=$llQty==""?0:$llQty;
		              $llQtyTemp=$OrderQty-$llQty;
					  $checkDisabled="";
					   if ($llQtyTemp<=0){//是否已领料
							 $llIMG="";
							 $llonClick="bgcolor='#96FF2D'";
							 $llCount+=1;//已领料数据
							 $checkDisabled="disabled";
					         }
						 else{
					        if ($tStockQty<=0){//判断在库量是否可进行领料
						       $llIMG="<img src='../images/registerNo.png' width='30' height='30'>";
						        $llonClick="";
								$checkDisabled="disabled";
					          }
						  else{
							//检查权限
			/*			    if($Keys==31){//有权限  && $Estate==1
							   $temQty=$llQtyTemp>$tStockQty?$tStockQty:$llQtyTemp;
						       $llIMG="<img src='../images/register.png' width='30' height='30'>";
							   $llonClick=" onclick='showKeyboard(this,$i,$j,$temQty, $temQty,$StockId)'";
							   }
							else{
								$llonClick="";
					            $llIMG="<img src='../images/registerNo.png' width='30' height='30'>";
								$checkDisabled="disabled";
							}*/
						 }
					  }
		             //库存量是否充足
					 $bgColor=($OrderQty-$llQty>$tStockQty && $llQtyTemp>0)?"bgcolor='#FFCC66'":"";
					// $checkIdclick="onclick=\"checkId(this,$i,$j);\" ";
					  echo "<tr height='30'>";
					  $unitFirst=$Field[$m]-1;//$checkName="checkId$i" . "[]";
					 // echo "<td class='A0101' width='$unitFirst' align='center'>
					 // <input name='checkId$i' type='checkbox' id='checkId$i' value='$StockId' $checkIdclick $checkDisabled></td>";
					 // $m=$m+2;
					  echo"<td class='A0101' width='$unitFirst' align='center' $bgColor>$j</td>";//序号
						$m=$m+2;
						echo"<td class='A0101' width='$Field[$m]' align='center'>$Name</td>";			//采购
						$m=$m+2;
						echo"<td class='A0101' width='$Field[$m]'>$Forshort</td>";		//供应商
						$m=$m+2;
						echo"<td class='A0101' width='$Field[$m]'>$StuffCname</td>";	//配件名称
						$m=$m+2;
						echo"<td class='A0101' width='$Field[$m]' align='center'>$Remark</td>";  //仓库楼层
						$m=$m+2;
						echo"<td class='A0101' width='$Field[$m]' align='right'>$tStockQty</td>";		//库存数量
						$m=$m+2;
						echo"<td class='A0101' width='$Field[$m]' align='right'>$OrderQty</td>";		//备料数量
						$m=$m+2;
						echo"<td class='A0101' width='$Field[$m]' align='right'>$llQty</td>";		//已领料数量
						$m=$m+2;
						  echo"<td class='A0100' align='center' $llonClick>$llQtyTemp</td>";
						//echo"<td class='A0100' align='center' $llonClick>$llIMG</td>";	//领料
						echo"</tr>";
						$j++;
				}while($checkStockRow=mysql_fetch_array($checkStockSql));
				echo"</table>";
				echo " <input name='checkId$i' type='checkbox' id='checkId$i'  disabled style='display:none;'>";//防止只有一条配件记录而产生错误
				$i++;
		    }
		   echo"</td></tr></table>";
		   //检查是否领料完成,更新备领单状态
		/*   $blupFlag=0;
		   if ($llCount==$j-1){
			    $blupFlag=1;include "item5_blUpdate.php";
		      }
			   else{
				  if ($Estate==0){
					  $blupFlag=2;include "item5_blUpdate.php";
				  }
			   }*/
		}while($mainRows = mysql_fetch_array($mainResult));
		$i=$i-1;
		echo " <input name='cIdNumber' type='hidden' id='cIdNumber' value='$i'/>";
  }
else{
	noRowInfo($tableWidth);
	}
echo '</div>';
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
//include "../model/subprogram/read_model_menu.php";
?>