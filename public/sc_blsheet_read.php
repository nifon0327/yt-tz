<?php 
//电信---yang 20120801
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=14;
$tableMenuS=500;
ChangeWtitle("$SubCompany 备料单记录列表");
$funFrom="ck_ll";
$From=$From==""?"read":$From;
$sumCols="4,5";			//求和列,需处理
$MergeRows=4;
$Th_Col="编号|30|备料单日期|70|订单PO|70|产品名称|200|订单数量|60|序号|40|采购|50|供应商|70|配件名称|200|备料数量|60|仓库楼层|60";
//必选，分页默认值
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 500;							//每页默认记录数量2,3,4,
$ActioToS="1,7,8,11";
//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-可选条件下拉框
if($From!="slist"){	//非查询：过滤采购、结付方式、供应商、月份
	$SearchRows="";
	$checkDay=$checkDay==""?date("Y-m-d"):$checkDay;
	//日期可选
	echo"<input name='checkDay' type='text' id='checkDay' style='width:70px' maxlength='10' value='$checkDay' onchange='document.form1.submit()'  onFocus='WdatePicker()'/>";
	$SearchRows=" AND B.Date='$checkDay'";
	}
//检查进入者是否采购
echo $CencalSstr;
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT B.Num,B.Date
FROM $DataIn.yw9_blsheet B
WHERE 1 $SearchRows GROUP BY B.Num ORDER BY B.Date DESC,B.Id DESC";
$mainResult = mysql_query($mySql." $PageSTR",$link_id);
if($mainRows = mysql_fetch_array($mainResult)){
	echo"<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'>";
	do{
	////////////////////////////////
		$m=1;
		//主单信息
		$Num=$mainRows["Num"];
		$Date=$mainRows["Date"];
		//检查记录总数
		$checkRecordRow=mysql_fetch_array(mysql_query("SELECT count(*) AS Row1Sum 
		FROM $DataIn.yw9_blsheet B
		LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=B.POrderId
		LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
		LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId
		WHERE B.Num='$Num' AND T.mainType<2",$link_id));
		$Row1Sum=$checkRecordRow["Row1Sum"];
		echo "<tr>
		<td scope='col' class='A0111' width='$Field[$m]' align='center' rowspan='$Row1Sum' valign='top'>$Num <p><a href='sc_blsheet_print.php?Num=$Num' target='_blank'><img src='../images/printer.gif' title='打印此备料单'></a></td>";		//一级项目:编号
		$m=$m+2;
		echo"<td scope='col' class='A0101' width='$Field[$m]' align='center' rowspan='$Row1Sum' valign='top'>$Date</td>";//一级项目:备料日期
		$m=$m+2;
		//检查2级项目明细
		$checkOrderSql=mysql_query("
		SELECT B.POrderId,Y.OrderPO,Y.Qty,P.cName,P.TestStandard
		FROM $DataIn.yw9_blsheet B
		LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=B.POrderId
		LEFT JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId
		WHERE B.Num='$Num' ORDER BY B.Id
		",$link_id);
		if($checkOrderRow=mysql_fetch_array($checkOrderSql)){
			$TempB=1;
			do{
				$POrderId=$checkOrderRow["POrderId"];
				$OrderPO=$checkOrderRow["OrderPO"];
				$Qty=$checkOrderRow["Qty"];
				$cName=$checkOrderRow["cName"];
				$TestStandard=$checkOrderRow["TestStandard"];
				include "../admin/Productimage/getPOrderImage.php";
				if($TempB!=1){//非二级首行时
					echo"<tr>";
					}
				$checkStockSql=mysql_query("SELECT G.OrderQty,D.StuffCname,M.Name,P.Forshort,B.Remark,D.Gfile,D.Gstate
				FROM $DataIn.cg1_stocksheet G
				LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
				LEFT JOIN $DataPublic.staffmain M ON M.Number=G.BuyerId
				LEFT JOIN $DataIn.trade_object P ON P.CompanyId=G.CompanyId
				LEFT JOIN $DataIn.base_mposition B ON B.Id=D.SendFloor
				LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId
				WHERE POrderId='$POrderId' AND T.mainType<2 ORDER BY D.SendFloor",$link_id);
				$RecordSum=mysql_num_rows($checkStockSql);
				echo"<td class='A0101' width='$Field[$m]' height='20' align='center' rowspan='$RecordSum' valign='top'>$OrderPO</td>";//PO
				$m=$m+2;
				echo"<td class='A0101' width='$Field[$m]' rowspan='$RecordSum' valign='top'><a href='sc_blsheet_print.php?Num=$Num&POrderId=$POrderId' target='_blank'><img src='../images/printer.gif' title='打印此订单的备料单'></a>$POrderId<br>$TestStandard</td>";		//产品名称
				$m=$m+2;
				echo"<td class='A0101' width='$Field[$m]' align='right' rowspan='$RecordSum' valign='top'>$Qty&nbsp;</td>";		//订单数量
				$m=$m+2;
				//检查3级项目:包括需求明细表、配件资料表，仓库分区表、人事表、供应商资料表
				
				if($checkStockRow=mysql_fetch_array($checkStockSql)){
					$TempC=1;
					do{
						$Name=$checkStockRow["Name"];
						$Forshort=$checkStockRow["Forshort"];
						$StuffCname=$checkStockRow["StuffCname"];
						$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
		                $Gfile=$checkStockRow["Gfile"];
		                $Gstate=$checkStockRow["Gstate"];
						include "../model/subprogram/stuffimg_model.php";
		                include "../model/subprogram/stuffimg_Gfile.php";	//图档显示	
						$OrderQty=$checkStockRow["OrderQty"];
						$Remark=$checkStockRow["Remark"];
						if($TempC!=1){
							echo "<tr>";
							}
						echo"<td class='A0101' width='$Field[$m]' align='center'>$TempC</td>";		//序号
						$m=$m+2;
						echo"<td class='A0101' width='$Field[$m]'>$Name</td>";			//采购
						$m=$m+2;
						echo"<td class='A0101' width='$Field[$m]'>$Forshort</td>";		//供应商
						$m=$m+2;
						echo"<td class='A0101' width='$Field[$m]'>$StuffCname</td>";	//配件名称
						$m=$m+2;
						echo"<td class='A0101' width='$Field[$m]'>$OrderQty</td>";		//备料数量
						$m=$m+2;
						echo"<td class='A0101' align='center'>$Remark</td>";					//仓库楼层
						echo"</tr>";
						$TempC++;
						}while($checkStockRow=mysql_fetch_array($checkStockSql));
					}//3级完成
				$TempB++;
				}while($checkOrderRow=mysql_fetch_array($checkOrderSql));
			}//2级完成

	///////////////////////////////
		}while($mainRows = mysql_fetch_array($mainResult));
	echo"</table>";
	}
else{
	noRowInfo($tableWidth);
	}
List_Title($Th_Col,"0",1);
//$myResult = mysql_query($mySql,$link_id);
//$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
//include "../model/subprogram/read_model_menu.php";
?>