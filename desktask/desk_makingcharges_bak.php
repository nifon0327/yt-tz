<?php
/*
已更新电信---yang 20120801
*/
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 人工/FOB费用月统计");
echo "<SCRIPT src='../model/js/cg_stuffqty_read.js' type=text/javascript></script>";
$tableWidth=800;
$bgcolor1="bgcolor='#CCCCCC'";
$bgcolor2="bgcolor='#FFCCFF'";
$bgcolor3="bgcolor='#33CCFF'";


?><form name="form1" method="post" action="">
<table border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>
  <tr>
    <td align="center" height="15">人工/FOB费用月统计</td>
  <tr>
    <td height="15">
      <select name="Year" id="Year" onChange="javascript:document.form1.submit()">
	  <?php
	  $checkY=mysql_query("SELECT DATE_FORMAT(Date,'%Y')  AS Date FROM $DataIn.ch1_shipmain GROUP BY DATE_FORMAT(Date,'%Y') ORDER BY Date DESC",$link_id);
	  if($checkR=mysql_fetch_array($checkY)){
	  	do{
			$theYaer=$checkR["Date"];
			$Year=$Year==""?$theYaer:$Year;
			if($Year==$theYaer){
				echo"<option value='$theYaer' selected>$theYaer 年</option>";
				}
			else{
				echo"<option value='$theYaer'>$theYaer 年</option>";
				}
			}while ($checkR=mysql_fetch_array($checkY));
		}
	  ?>
      </select>
      </td>
  </table>
</form>
<?php
$MonthArray=array();
for($i=0;$i<=12;$i++){
	array_push($MonthArray,array("R_I"=>0,"R_O"=>0,"FOB_I"=>0,"FOB_O"=>0,"X_I"=>0,"X_O"=>0,"RG_O"=>0,"RG_1"=>0,"RG_2"=>0,"RG_3"=>0,"Rens_0"=>0));
	}

$SUM_I=0;
$SUM_O=0;
$scSubTo="desk_makingcharges_a";
$checkMonth=mysql_query("SELECT Date FROM $DataIn.ch1_shipmain WHERE DATE_FORMAT(Date,'%Y')=$Year GROUP BY DATE_FORMAT(Date,'%Y-%m') ORDER BY Date DESC",$link_id);
if($checkRow=mysql_fetch_array($checkMonth)){
	$i=1;
	do{
		$MonthTemp=date("Y-m",strtotime($checkRow["Date"]));
		$MNumber=date("m",strtotime($checkRow["Date"]))*1;
		$validM=$validM==0?$MNumber:$validM;
		$SUM_thisI=0;
		$SUM_thisO=0;
		//1111111111111111111111
		//读取收数据
		$Value1=0;
		$Value2=0;
		$I1=0;$I2=0;$O1=0;$O2=0;
		//I1：人工-需求单统计：当月出货订单的需求单中,配件分类少8000的配件需求单总额
		//I2：FOB-需求单统计：当月出货订单的需求单中,配件分类为9080FOB费用的需求单总额
		$checkDataISql=mysql_query("
			SELECT SUM(G.Price*G.OrderQty) AS Amount,'I1' AS Type FROM $DataIn.ch1_shipsheet S 
				LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
				LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
				LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
				LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
				WHERE T.mainType=3 AND DATE_FORMAT(M.Date,'%Y-%m')='$MonthTemp'
		UNION ALL
			SELECT SUM(G.Price*G.OrderQty) AS Amount,'I2' AS Type 
				FROM $DataIn.ch1_shipsheet S 
				LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
				LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
				LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
				WHERE  D.TypeId='8000' AND DATE_FORMAT(M.Date,'%Y-%m')='$MonthTemp'
		",$link_id);
		if($checkDataIRow=mysql_fetch_array($checkDataISql)){
			do{
				$Type=$checkDataIRow["Type"];
				$Amount=$checkDataIRow["Amount"]==""?0:$checkDataIRow["Amount"];
				$$Type=$Amount;
				}while($checkDataIRow=mysql_fetch_array($checkDataISql));
			}

		/*O1:人工实际支出
		1.生产员工薪资(实发+借支+员工出社保费)
		2.生产员工假日加班费
		3.生产员工社保费(公司出的那部分)
		4.试用期薪资AND X.Estate IN(0,3)SELECT SUM(Amount) AS Amount FROM()A
		*/
		$checkDataOSql=mysql_query("
			SELECT SUM(X.Amount+X.Jz+X.Sb) AS Amount,'0' AS Sign,Sum(1) as Rens 
			FROM $DataIn.cwxzsheet X WHERE X.Month='$MonthTemp' AND X.BranchId=5 
			UNION ALL
			SELECT SUM(H.Amount) AS Amount,'1' AS Sign,Sum(1) as Rens 
			FROM $DataIn.hdjbsheet H WHERE H.Month='$MonthTemp' AND H.BranchId=5 
			UNION ALL
			SELECT SUM(B.cAmount) AS Amount,'2' AS Sign,Sum(1) as Rens 
			FROM $DataIn.sbpaysheet B WHERE B.Month='$MonthTemp' AND B.BranchId=5 
			UNION ALL
			SELECT SUM(Amount) AS Amount,'3' AS Sign,Sum(Rens) as Rens  FROM(
			SELECT SUM(L.Amount) AS Amount,Sum(1) as Rens  
			FROM $DataIn.hzqksheet L WHERE DATE_FORMAT(L.Date,'%Y-%m')='$MonthTemp' AND L.TypeId='635' 
			UNION ALL
			SELECT SUM(L.Amount) AS Amount,Sum(1) as Rens  
			FROM $DataIn.cwxztempsheet L WHERE L.Month='$MonthTemp') A
			
		",$link_id);
		if($checkDataORow=mysql_fetch_array($checkDataOSql)){
			do{
				$SjSign=$checkDataORow["Sign"];
				$SjAmount=$checkDataORow["Amount"];
				$SjRens=$checkDataORow["Rens"];  //人数
				$O1+=$SjAmount;
				$TempA="SjItem".strval($SjSign);
				$$TempA=$SjAmount;
				$TempB="SjRens".strval($SjSign);   //人数,SjRens0为正式工的，
				$$TempB=$SjRens;
				}while ($checkDataORow=mysql_fetch_array($checkDataOSql));
			//$O1=$checkDataORow["Amount"]==""?0:$checkDataORow["Amount"];
			}

		/*O2:FOB实际
		当月出货所产生的Forward杂费和中港运费、入仓费、报关费
		*/
		$checkDataO2Sql=mysql_query("
			SELECT SUM(Amount) AS Amount FROM (
				SELECT SUM(W.Amount*C.Rate) AS Amount 
				FROM $DataIn.ch1_shipmain M
				LEFT JOIN $DataIn.ch3_forward W ON W.chId=M.Id
				LEFT JOIN $DataPublic.currencydata C ON C.Symbol='HKD'
				WHERE DATE_FORMAT(M.Date,'%Y-%m')='$MonthTemp'
			UNION ALL 
				SELECT SUM(F.mcWG*F.Price+F.depotCharge*C.Rate) AS Amount 
				FROM $DataIn.ch1_shipmain M
				LEFT JOIN $DataIn.ch4_freight F ON F.chId=M.Id
				LEFT JOIN $DataPublic.currencydata C ON C.Symbol='HKD'
				WHERE DATE_FORMAT(M.Date,'%Y-%m')='$MonthTemp'
			UNION ALL 
				SELECT SUM(F.declarationCharge+F.checkCharge) AS Amount 
				FROM $DataIn.ch1_shipmain M
				LEFT JOIN $DataIn.ch12_declaration F ON F.chId=M.Id
				WHERE DATE_FORMAT(M.Date,'%Y-%m')='$MonthTemp'				
			)A
		",$link_id);
		//	UNION ALL 			SELECT SUM(X.declarationCharge+X.checkCharge) AS Amount FROM $DataIn.ch12_declaration X WHERE 1 AND DATE_FORMAT(X.Date,'%Y-%m')='$MonthTemp' $EstateSTR
		if($checkDataO2Row=mysql_fetch_array($checkDataO2Sql)){
			$O2=$checkDataO2Row["Amount"]==""?0:$checkDataO2Row["Amount"];
			}

		/*I3：行政费用需求单统计：当月出货总额的7%*/
		$checkDataI3Sql=mysql_query("SELECT SUM(S.Qty*S.Price*B.Rate*M.Sign) AS CB
			FROM $DataIn.ch1_shipsheet S 
			LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
			LEFT JOIN $DataIn.trade_object G ON G.CompanyId=M.CompanyId
			LEFT JOIN $DataPublic.currencydata B ON B.Id=G.Currency
			WHERE 1 AND M.Estate=0 AND DATE_FORMAT(M.Date,'%Y-%m')='$MonthTemp' ORDER BY M.Date DESC",$link_id);

		if($checkDataI3Row=mysql_fetch_array($checkDataI3Sql)){
			//采购美金部分
			$checkDataI3SqlUSD=mysql_query("SELECT SUM(G.OrderQty*G.Price*B.Rate*M.Sign) AS CB
			FROM $DataIn.ch1_shipsheet S 
			LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
			LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
			LEFT JOIN $DataIn.trade_object P ON P.CompanyId=G.CompanyId
			LEFT JOIN $DataPublic.currencydata B ON B.Id=P.Currency
			WHERE 1 AND M.Estate=0 AND DATE_FORMAT(M.Date,'%Y-%m')='$MonthTemp' AND P.Currency=2 ORDER BY M.Date DESC",$link_id);
			if($checkDataI3RowUSD=mysql_fetch_array($checkDataI3SqlUSD)){
				$I3USD=$checkDataI3RowUSD["CB"]==""?0:$checkDataI3RowUSD["CB"];
				}
			$I3=$checkDataI3Row["CB"]==""?0:$checkDataI3Row["CB"];
			include "../model/subprogram/sys_parameters.php";
			$I3=($I3-$I3USD)*$HzRate;
			}
		/*
		1.采购、业务、行政、开发人工(实发+借支+个人出社保)
		2.采购、业务、行政、开发假日加班费
		3.采购、业务、行政、开发社保(公司出的部分)
		4.总务采购费
		5.行政费用：厂房租金601,厂房水电费602,厂区管理费603,员工网络费606,电话费607,办公耗材608,车辆支出费用609,其它总务费用610,差旅费613,报刊费614,交际费615,银行手续费617,非月结快递费618,税款624,机模费626,模具费627,样品费630,运费632
		6.打样费用
		7.船务快递费(月结快递费)
		8.船务寄样费
		9.报关费用 add my zx 2010-12-01
		*/
		$JobIdTemp=" AND X.BranchId<5";//非生产的员工
		//审核状态
		$EstateSTR=" AND (X.Estate=3 OR X.Estate=0)";
		//		5.行政费用：厂房租金601,厂房水电费602,厂区管理费603,员工网络费606,电话费607,办公耗材608,车辆支出费用609,其它总务费用610,差旅费613,报刊费614,交际费615,银行手续费617,非月结快递费618,税款624,机模费626,模具费627,样品费630,运费632,购买手机费用649
		$checkO3Sql = mysql_query("
			SELECT SUM(Amount) AS Amount FROM(
		SELECT SUM(X.Amount+X.Jz+X.Sb) AS Amount FROM $DataIn.cwxzsheet X WHERE X.Month='$MonthTemp'  $JobIdTemp $EstateSTR
					UNION ALL
					SELECT SUM(X.Amount) AS Amount FROM $DataIn.hdjbsheet X WHERE X.Month='$MonthTemp'  $JobIdTemp $EstateSTR
					UNION ALL
					SELECT SUM(X.cAmount) AS Amount FROM $DataIn.sbpaysheet X WHERE X.Month='$MonthTemp'  $JobIdTemp $EstateSTR
					UNION ALL
					SELECT SUM(X.Qty*X.Price) AS Amount FROM $DataIn.zw3_purchases X WHERE 1  AND DATE_FORMAT(X.qkDate,'%Y-%m')='$MonthTemp' $EstateSTR
					UNION ALL
					SELECT SUM(X.Amount*C.Rate) AS Amount FROM $DataIn.hzqksheet X,$DataPublic.currencydata C WHERE C.Id=X.Currency AND DATE_FORMAT(X.Date,'%Y-%m')='$MonthTemp' $EstateSTR AND X.TypeId IN (601,602,603,606,607,608,609,610,613,614,615,617,618,624,626,627,630,632,643,649)
					UNION ALL
					SELECT SUM(X.Amount) AS Amount FROM $DataIn.cwdyfsheet X WHERE 1 AND DATE_FORMAT(X.Date,'%Y-%m')='$MonthTemp'  $EstateSTR
					UNION ALL
					SELECT SUM(X.Amount) AS Amount FROM $DataIn.ch9_expsheet X WHERE 1 AND DATE_FORMAT(X.Date,'%Y-%m')='$MonthTemp' $EstateSTR
					UNION ALL
					SELECT SUM(X.Amount) AS Amount FROM $DataIn.ch10_samplemail X WHERE 1 AND DATE_FORMAT(X.SendDate,'%Y-%m')='$MonthTemp' $EstateSTR
		
					)A 
				",$link_id);
		if($checkO3Row = mysql_fetch_array($checkO3Sql)){
			$O3=sprintf("%.0f",$checkO3Row["Amount"]);
			}
		$MonthArray[$MNumber]["R_I"]=$I1;
		$MonthArray[$MNumber]["R_O"]=$O1;
		$MonthArray[$MNumber]["FOB_I"]=$I2;
		$MonthArray[$MNumber]["FOB_O"]=$O2;
		$MonthArray[$MNumber]["X_I"]=$I3;
		$MonthArray[$MNumber]["X_O"]=$O3;
		$MonthArray[$MNumber]["RG_0"]=$SjItem0;//生产员工工资
		$MonthArray[$MNumber]["RG_1"]=$SjItem1;//假日加班费
		$MonthArray[$MNumber]["RG_2"]=$Sjitem2;//社保
		$MonthArray[$MNumber]["RG_3"]=$Sjitem3;//试用期工资
		$MonthArray[$MNumber]["Rens_0"]=$SjRens0;//生产员工人数
		$i++;
		//1111111111111111111111
		}while($checkRow=mysql_fetch_array($checkMonth));
	}
?>
<table border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>
  <tr align="center" class="">

    <td class="A1111" colspan="2" width="198">项目</td>
    <?php
    for($i=1;$i<=$validM;$i++){
		echo "<td width='80' class='A1101'>".$i."月</td>";
		}
	?>
	<td width="80" class="A1101" >全年累计</td>
  	<td width="80" class="A1101">月平均</td>
  </tr>

  <tr align="center">
    <td width="70" class="A0111" rowspan="5" >人工</td>
    <td width="128" class="A0101" bgcolor="#CCCCCC" align="right">需求单统计</td>
	<?php
	$SUM_I=0;
	//$validM=0;  //计算，有多少个月算多少个月的平均
	for($i=0;$i<$validM;$i++){
		$MNumber=$i+1;
		$I1=$MonthArray[$MNumber]["R_I"];
		if ($I1>0){
			$validM=$validM;
		}
    	echo"<td class='A0101' align='right' $bgcolor1>".number_format($I1,0)."</td>";
		$SUM_I=$SUM_I+$I1;
		}
	echo"<td class='A0101' align='right' $bgcolor1 >".number_format($SUM_I,0)."</td>";
	$AV_SUM=$SUM_I/$validM;
	echo"<td class='A0101' align='right' $bgcolor1 >".number_format($AV_SUM,0)."</td>";

	?>
  </tr>
   <tr align="center">
    <td class="A0101" bgcolor="#FFCCFF" align="right"><img onClick="View('RG_sheet',this)" id="ThisImg_1" name="ThisImg_1" src="../images/showtable.gif" width="13" height="13" style="CURSOR: pointer" title="展开或关闭明细项目">实际支出统计</td>
	<?php
	$SUM_I=0;
	//$validM=0;  //计算，有多少个月算多少个月的平均
	for($i=0;$i<$validM;$i++){
		$MNumber=$i+1;
		$I1=$MonthArray[$MNumber]["R_O"];
		if ($I1>0){
			$validM=$validM;
		}
    	echo"<td class='A0101' align='right' $bgcolor2>".number_format($I1,0)."</td>";
		$SUM_I=$SUM_I+$I1;
		}
	echo"<td class='A0101' align='right' $bgcolor2>".number_format($SUM_I,0)."</td>";
	$AV_SUM=$SUM_I/$validM;
	echo"<td class='A0101' align='right' $bgcolor2>".number_format($AV_SUM,0)."</td>";
	?>
  </tr>
   <tr align="center" style='display:none' id="RG_sheet">
    <td class="A0101" bgcolor="#FFCCFF" align="right">①生产员工薪资<br>②生产员工加班费<br>③生产员工社保<br>④试用期薪资<br>生产员平均工资</td>
	<?php
	$SUM0=0;$SUM1=0;$SUM2=0;$SUM3=0;$SUMRens0=0;
	//$validM=0;  //计算，有多少个月算多少个月的平均
	for($i=0;$i<$validM;$i++){
		$SUMMonthRens0=0;  //当月统计
		$MNumber=$i+1;
		$I0=$MonthArray[$MNumber]["RG_0"];$SUM0+=$I0;$SUMMonthRens0=$SUMMonthRens0+$I0;$I0=number_format($I0,0);
		$I1=$MonthArray[$MNumber]["RG_1"];$SUM1+=$I1;$SUMMonthRens0=$SUMMonthRens0+$I1;$I1=number_format($I1,0);
		$Rens0=$MonthArray[$MNumber]["Rens_0"];
		if($Rens0>0){
			$AV_MonthRens0=$SUMMonthRens0/$Rens0;
			$SUMRens0=$SUMRens0+$AV_MonthRens0;
			$AV_MonthRens0=number_format($AV_MonthRens0);

		}
		$I2=$MonthArray[$MNumber]["RG_2"];$SUM2+=$I2;$I2=number_format($I2,0);
		$I3=$MonthArray[$MNumber]["RG_3"];$SUM3+=$I3;$I3=number_format($I3,0);
		if ($I0>0){
			$validM=$validM;
			}
    	echo"<td class='A0101' align='right' $bgcolor2>$I0<br>$I1<br>$I2<br>$I3<br>$AV_MonthRens0</td>";
		}
		$AV_SUM0=number_format($SUM0/$validM,0);
		$AV_SUM1=number_format($SUM1/$validM,0);
		$AV_SUM2=number_format($SUM2/$validM,0);
		$AV_SUM3=number_format($SUM3/$validM,0);
		$AV_SUMRens0=number_format($SUMRens0/$validM,0);   //全部平均工资
		$SUM0=number_format($SUM0,0);
		$SUM1=number_format($SUM1,0);
		$SUM2=number_format($SUM2,0);
		$SUM3=number_format($SUM3,0);
	echo"<td class='A0101' align='right' $bgcolor2>$SUM0<br>$SUM1<br>$SUM2<br>$SUM3<br>--</td>";
	echo"<td class='A0101' align='right' $bgcolor2>$AV_SUM0<br>$AV_SUM1<br>$AV_SUM2<br>$AV_SUM3<br>$AV_SUMRens0</td>";
	?>
  </tr>



   <tr align="center">
    <td class="A0101" align="right">差额</td>
 	<?php
	$SUM_I=0;
	$SUM_O=0;
	//$validM=0;  //计算，有多少个月算多少个月的平均
	for($i=0;$i<$validM;$i++){
		$MNumber=$i+1;
		$I1=$MonthArray[$MNumber]["R_I"];
		$O1=$MonthArray[$MNumber]["R_O"];
		$Value1=$I1-$O1;
		if ($I1>0){
			$validM=$validM;
		}
		$Value1=$Value1<0?"<div class='redB'>".number_format($Value1,0)."</div>":"<div class='greenB'>".number_format($Value1,0)."</div>";
    	echo"<td class='A0101' align='right'>".$Value1."</td>";
		$SUM_I=$SUM_I+$I1;
		$SUM_O=$SUM_O+$O1;
		}
	echo"<td class='A0101' align='right'>".number_format(($SUM_I-$SUM_O),0)."</td>";
	$AV_SUM=($SUM_I-$SUM_O)/$validM;
	$AV_SUM=$Value1<0?"<div class='redB'>".number_format($AV_SUM,0)."&nbsp;</div>":"<div class='greenB'>".number_format($AV_SUM,0)."</div>";
	echo"<td class='A0101' align='right'>".$AV_SUM."</td>";
	?>
  </tr>

   <tr align="center">
    <td class="A0101" align="right">差异</td>
 	<?php
	$SUM_I=0;
	$SUM_O=0;
	//$validM=0;  //计算，有多少个月算多少个月的平均
	for($i=0;$i<$validM;$i++){
		$MNumber=$i+1;
		$I1=$MonthArray[$MNumber]["R_I"];
		$O1=$MonthArray[$MNumber]["R_O"];
		$Value1=$I1-$O1;
		if ($I1>0){
			$validM=$validM;
		}
		$Percent_RG=round(number_format(($O1-$I1)/$O1,2)*100);
    	echo"<td class='A0101' align='center'>".$Percent_RG."%</td>";
		$SUM_I=$SUM_I+$I1;
		$SUM_O=$SUM_O+$O1;
		}
	echo"<td class='A0101' align='right'>&nbsp;</td>";
	$AV_SUM=round(number_format(($SUM_O-$SUM_I)/$SUM_O,2)*100);
	echo"<td align='center' class='A0101' >$AV_SUM%</td>";
	?>
   </tr>

  <!-- FOB 统计-->
   <tr align="center">
    <td width="70" class="A0111" rowspan="4" >FOB</td>
    <td class="A0101" bgcolor="#CCCCCC" align="right">需求单统计</td>
	<?php
	$SUM_I=0;
	//$validM=0;  //计算，有多少个月算多少个月的平均
	for($i=0;$i<$validM;$i++){
		$MNumber=$i+1;
		$I1=$MonthArray[$MNumber]["FOB_I"];
		if ($I1>0){
			$validM=$validM;
		}
    	echo"<td class='A0101' align='right' $bgcolor1>".number_format($I1,0)."</td>";
		$SUM_I=$SUM_I+$I1;
		}
	echo"<td class='A0101' align='right' $bgcolor1>".number_format($SUM_I,0)."</td>";
	$AV_SUM=$SUM_I/$validM;
	echo"<td class='A0101' align='right' $bgcolor1>".number_format($AV_SUM,0)."</td>";

	?>
  </tr>
   <tr align="center">
    <td class="A0101" bgcolor="#FFCCFF" align="right">实际支出统计</td>
	<?php
	$SUM_I=0;
	//$validM=0;  //计算，有多少个月算多少个月的平均
	for($i=0;$i<$validM;$i++){
		$MNumber=$i+1;
		$I1=$MonthArray[$MNumber]["FOB_O"];
		if ($I1>0){
			$validM=$validM;
		}
    	echo"<td class='A0101' align='right' $bgcolor0>".number_format($I1,0)."</td>";
		$SUM_I=$SUM_I+$I1;
		}
	echo"<td class='A0101' align='right' $bgcolor0>".number_format($SUM_I,0)."</td>";
	$AV_SUM=$SUM_I/$validM;
	echo"<td class='A0101' align='right' $bgcolor0>".number_format($AV_SUM,0)."</td>";
	?>

  </tr>

   <tr align="center">
    <td class="A0101" align="right">差额</td>
 	<?php
	$SUM_I=0;
	$SUM_O=0;
	//$validM=0;  //计算，有多少个月算多少个月的平均
	for($i=0;$i<$validM;$i++){
		$MNumber=$i+1;
		$I1=$MonthArray[$MNumber]["FOB_I"];
		$O1=$MonthArray[$MNumber]["FOB_O"];
		$Value1=$I1-$O1;
		if ($I1>0){
			$validM=$validM;
		}
		$Value1=$Value1<0?"<div class='redB'>".number_format($Value1,0)."</div>":"<div class='greenB'>".number_format($Value1,0)."</div>";
    	echo"<td class='A0101' align='right'>".$Value1."</td>";
		$SUM_I=$SUM_I+$I1;
		$SUM_O=$SUM_O+$O1;
		}
	echo"<td class='A0101' align='right'>".number_format(($SUM_I-$SUM_O),0)."</td>";
	$AV_SUM=($SUM_I-$SUM_O)/$validM;
	$AV_SUM=$Value1<0?"<div class='redB'>".number_format($AV_SUM,0)."&nbsp;</div>":"<div class='greenB'>".number_format($AV_SUM,0)."</div>";
	echo"<td class='A0101' align='right'>".$AV_SUM."</td>";
	?>

   </tr>
   <tr align="center">
    <td class="A0101" align="right">差异</td>
 	<?php
	$SUM_I=0;
	$SUM_O=0;
	//$validM=0;  //计算，有多少个月算多少个月的平均
	for($i=0;$i<$validM;$i++){
		$MNumber=$i+1;
		$I1=$MonthArray[$MNumber]["FOB_I"];
		$O1=$MonthArray[$MNumber]["FOB_O"];
		$Value1=$I1-$O1;
		if ($I1>0){
			$validM=$validM;
		}
		$Percent_RG=round(number_format(($O1-$I1)/$O1,2)*100);
    	echo"<td class='A0101' align='center'>".$Percent_RG."%</td>";
		$SUM_I=$SUM_I+$I1;
		$SUM_O=$SUM_O+$O1;
		}
	echo"<td class='A0101' align='right'>&nbsp;</td>";
	$AV_SUM=round(number_format(($SUM_O-$SUM_I)/$SUM_O,2)*100);
	echo"<td align='center' class='A0101' >$AV_SUM%</td>";
	?>
   </tr>


   <!-- 行政统计-->
   <tr align="center">
    <td width="70" class="A0111" rowspan="4" >行政费用</td>
    <td class="A0101" bgcolor="#CCCCCC" align="right">需求单统计</td>
	<?php
	$SUM_I=0;
	//$validM=0;  //计算，有多少个月算多少个月的平均
	for($i=0;$i<$validM;$i++){
		$MNumber=$i+1;
		$I1=$MonthArray[$MNumber]["X_I"];
		if ($I1>0){
			$validM=$validM;
		}
    	echo"<td class='A0101' align='right' $bgcolor1>".number_format($I1,0)."</td>";
		$SUM_I=$SUM_I+$I1;
		}
	echo"<td class='A0101' align='right' $bgcolor1>".number_format($SUM_I,0)."</td>";
	$AV_SUM=$SUM_I/$validM;
	echo"<td class='A0101' align='right' $bgcolor1>".number_format($AV_SUM,0)."</td>";

	?>
  </tr>
   <tr align="center">
    <td class="A0101" bgcolor="#FFCCFF" align="right">实际支出统计</td>
	<?php
	$SUM_I=0;
	//$validM=0;  //计算，有多少个月算多少个月的平均
	for($i=0;$i<$validM;$i++){
		$MNumber=$i+1;
		$I1=$MonthArray[$MNumber]["X_O"];
		if ($I1>0){
			$validM=$validM;
		}
    	echo"<td class='A0101' align='right' $bgcolor2>".number_format($I1,0)."</td>";
		$SUM_I=$SUM_I+$I1;
		}
	echo"<td class='A0101' align='right' $bgcolor2>".number_format($SUM_I,0)."</td>";
	$AV_SUM=$SUM_I/$validM;
	echo"<td class='A0101' align='right' $bgcolor2>".number_format($AV_SUM,0)."</td>";
	?>

  </tr>
   </tr>
   <tr align="center">
    <td class="A0101" align="right">差额</td>
 	<?php
	$SUM_I=0;
	$SUM_O=0;
	//$validM=0;  //计算，有多少个月算多少个月的平均
	for($i=0;$i<$validM;$i++){
		$MNumber=$i+1;
		$I1=$MonthArray[$MNumber]["X_I"];
		$O1=$MonthArray[$MNumber]["X_O"];
		$Value1=$I1-$O1;
		if ($I1>0){
			$validM=$validM;
		}
		$Value1=$Value1<0?"<div class='redB'>".number_format($Value1,0)."</div>":"<div class='greenB'>".number_format($Value1,0)."</div>";
    	echo"<td class='A0101' align='right'>".$Value1."</td>";
		$SUM_I=$SUM_I+$I1;
		$SUM_O=$SUM_O+$O1;
		}
	echo"<td class='A0101' align='right'>".number_format(($SUM_I-$SUM_O),0)."</td>";
	$AV_SUM=($SUM_I-$SUM_O)/$validM;
	$AV_SUM=$Value1<0?"<div class='redB'>".number_format($AV_SUM,0)."&nbsp;</div>":"<div class='greenB'>".number_format($AV_SUM,0)."</div>";
	echo"<td class='A0101' align='right'>".$AV_SUM."</td>";
	?>

   </tr>
   <tr align="center">
    <td class="A0101" align="right">差异</td>
 	<?php
	$SUM_I=0;
	$SUM_O=0;
	//$validM=0;  //计算，有多少个月算多少个月的平均
	for($i=0;$i<$validM;$i++){
		$MNumber=$i+1;
		$I1=$MonthArray[$MNumber]["X_I"];
		$O1=$MonthArray[$MNumber]["X_O"];
		$Value1=$I1-$O1;
		if ($I1>0){
			$validM=$validM;
		}
		$Percent_RG=round(number_format(($O1-$I1)/$O1,2)*100);
    	echo"<td class='A0101' align='center'>".$Percent_RG."%</td>";
		$SUM_I=$SUM_I+$I1;
		$SUM_O=$SUM_O+$O1;
		}
	echo"<td class='A0101' align='right'>&nbsp;</td>";
	$AV_SUM=round(number_format(($SUM_O-$SUM_I)/$SUM_O,2)*100);
	echo"<td align='center' class='A0101' >$AV_SUM%</td>";
	?>
   </tr>


   <!-- 合计-->
   <tr align="center"   >
    <td width="70" class="A0111" rowspan="4" <?php    echo  "$bgcolor3" ;?> >合计</td>
    <td class="A0101" <?php    echo  "$bgcolor3" ;?>  align="right">需求单统计</td>
	<?php
	$SUM_I=0;
	$SUM_thisI=0;
	//$validM=0;  //计算，有多少个月算多少个月的平均
	for($i=0;$i<$validM;$i++){
		$MNumber=$i+1;
		$I1=$MonthArray[$MNumber]["R_I"];
		$I2=$MonthArray[$MNumber]["FOB_I"];
		$I3=$MonthArray[$MNumber]["X_I"];

		if ($I1>0 ){
			$validM=$validM;
		}
		$SUM_thisI=$I1+$I2+$I3;
    	echo"<td class='A0101' align='right' $bgcolor1 >".number_format($SUM_thisI,0)."</td>";
		$SUM_I=$SUM_I+$SUM_thisI;
		}
	echo"<td class='A0101' align='right' $bgcolor1>".number_format($SUM_I,0)."</td>";
	$AV_SUM=$SUM_I/$validM;
	echo"<td class='A0101' align='right' $bgcolor1>".number_format($AV_SUM,0)."</td>";

	?>
  </tr>
   <tr align="center">
    <td class="A0101" <?php    echo  "$bgcolor3" ;?>  align="right">实际支出统计</td>
	<?php
	$SUM_I=0;
	$SUM_thisI=0;
	//$validM=0;  //计算，有多少个月算多少个月的平均
	for($i=0;$i<$validM;$i++){
		$MNumber=$i+1;
		$I1=$MonthArray[$MNumber]["R_O"];
		$I2=$MonthArray[$MNumber]["FOB_O"];
		$I3=$MonthArray[$MNumber]["X_O"];

		if ($I1>0 ){
			$validM=$validM;
		}
		$SUM_thisI=$I1+$I2+$I3;
    	echo"<td class='A0101' align='right' $bgcolor2>".number_format($SUM_thisI,0)."</td>";
		$SUM_I=$SUM_I+$SUM_thisI;
		}
	echo"<td class='A0101' align='right' $bgcolor2>".number_format($SUM_I,0)."</td>";
	$AV_SUM=$SUM_I/$validM;
	echo"<td class='A0101' align='right' $bgcolor2>".number_format($AV_SUM,0)."</td>";
	?>

  </tr>

   <tr align="center">
    <td class="A0101" <?php    echo  "$bgcolor3" ;?>  align="right">差额</td>
 	<?php
	$SUM_I=0;
	$SUM_O=0;
	$SUM_thisI=0;
	$SUM_thisO=0;
	//$validM=0;  //计算，有多少个月算多少个月的平均
	for($i=0;$i<$validM;$i++){
		$MNumber=$i+1;
		$I1=$MonthArray[$MNumber]["R_I"];
		$I2=$MonthArray[$MNumber]["FOB_I"];
		$I3=$MonthArray[$MNumber]["X_I"];

		$O1=$MonthArray[$MNumber]["R_O"];
		$O2=$MonthArray[$MNumber]["FOB_O"];
		$O3=$MonthArray[$MNumber]["X_O"];

		$SUM_thisI=$I1+$I2+$I3;
		$SUM_thisO=$O1+$O2+$O3;
		$Value1=$SUM_thisI-$SUM_thisO;
		if ($I1>0){
			$validM=$validM;
		}
		$Value1=$Value1<0?"<div class='redB'>".number_format($Value1,0)."</div>":"<div class='greenB'>".number_format($Value1,0)."</div>";
    	echo"<td class='A0101' align='right'>".$Value1."</td>";
		$SUM_I=$SUM_I+$SUM_thisI;
		$SUM_O=$SUM_O+$SUM_thisO;
		}
	echo"<td class='A0101' align='right'>".number_format(($SUM_I-$SUM_O),0)."</td>";
	$AV_SUM=($SUM_I-$SUM_O)/$validM;
	$AV_SUM=$Value1<0?"<div class='redB'>".number_format($AV_SUM,0)."&nbsp;</div>":"<div class='greenB'>".number_format($AV_SUM,0)."</div>";
	echo"<td class='A0101' align='right'>".$AV_SUM."</td>";
	?>
  </tr>
   </tr>
   <tr align="center">
    <td class="A0101" <?php    echo  "$bgcolor3" ;?>  align="right">差异</td>
 	<?php
	$SUM_I=0;
	$SUM_O=0;
	$SUM_thisI=0;
	$SUM_thisO=0;
	//$validM=0;  //计算，有多少个月算多少个月的平均
	for($i=0;$i<$validM;$i++){
		$MNumber=$i+1;
		$I1=$MonthArray[$MNumber]["R_I"];
		$I2=$MonthArray[$MNumber]["FOB_I"];
		$I3=$MonthArray[$MNumber]["X_I"];

		$O1=$MonthArray[$MNumber]["R_O"];
		$O2=$MonthArray[$MNumber]["FOB_O"];
		$O3=$MonthArray[$MNumber]["X_O"];

		$SUM_thisI=$I1+$I2+$I3;
		$SUM_thisO=$O1+$O2+$O3;
		$Value1=$SUM_thisI-$SUM_thisO;
		if ($I1>0){
			$validM=$validM;
		}
		$Percent_RG=round(number_format(($SUM_thisO-$SUM_thisI)/$SUM_thisO,2)*100);
    	echo"<td class='A0101' align='center'>".$Percent_RG."%</td>";
		$SUM_I=$SUM_I+$SUM_thisI;
		$SUM_O=$SUM_O+$SUM_thisO;
		}
	echo"<td class='A0101' align='right'>&nbsp;</td>";
	$AV_SUM=round(number_format(($SUM_O-$SUM_I)/$SUM_O,2)*100);
	echo"<td align='center' class='A0101' >$AV_SUM%</td>";
	?>
   </tr>



</table>

<p>&nbsp;</p>
<table width="1056" border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>
  <tr align="center"  class="">
    <td width="103" class="A1111" height="25">项目</td>
    <td width="300" class="A1101">需求单统计</td>
    <td width="500" class="A1101">实际支出</td>
  </tr>
  <tr>
    <td class="A0111">1-人工</td>
    <td class="A0101">当月出货订单的需求单中,配件分类属于7001-7100人工类的配件需求单总额</td>
    <td class="A0101">1.生产员工薪资=实发+借支+员工出社保费(与薪资表生产员工的实付+社保+借支总和一致)<br>
      2.生产员工加班费（与假日加班费统计页面的生产员工加班费总和一致）<br>
      3.生产员工社保=公司付费部分（与社保缴费记录中生产员工的公司缴费总和一致）<br>
    4.试用期薪资+行政635</td>
  </tr>
  <tr>
    <td class="A0111">2-FOB</td>
    <td class="A0101">当月出货订单的需求单中,配件分类为9080FOB费用的需求单总额(与已出明细中的预估FOB值一致)</td>
    <td class="A0101">当月出货所产生的Forward杂费和中港运费、入仓费（与已出明细中的实际FOB一致）、报关费和商检费</td>
  </tr>
  <tr>
    <td class="A0111">3-行政费用</td>
    <td class="A0101">当月(非代购)出货总额的 <?php    echo $HzRate*100?>%</td>
    <td class="A0101">注意：以下项目均为请款通过项目<br>
      1.采购、业务、行政、开发人工(实发+借支+个人出社保)<br>
      2.采购、业务、行政、开发假日加班费<br>
      3.采购、业务、行政、开发社保(公司出的部分)<br>
      4.总务采购费<br>
      5.行政费用：厂房租金601,厂房水电费602,厂区管理费603,电话费607,办公耗材608,车辆支出费用609,其它总务费用610,差旅费613,交际费615,银行手续费617,非月结快递费618,税款624,模具费627,样品费630,运费632,开办费用643,购买手机费用649<br>
      6.打样开发费用<br>
      7.船务快递费(月结快递费)<br>
      8.船务寄样费</td>
  </tr>
  <tr>
    <td colspan="3" class="A0111" height="25"><p>另：差异百分比=((实际支出统计-需求单统计)/实际支出统计)*100% ；行政比率自动随系统参数更新</p>
    <p>1、</p></td>
  </tr>
</table>
</body>
</html>
<script>
function View(RowTemp,f){
	var e=eval(RowTemp);
	e.style.display=(e.style.display=="none")?"":"none";
	var yy=f.src;
	if (yy.indexOf("showtable")==-1){
		f.src="../images/showtable.gif";
		e.myProperty=true;
		}
	else{
		f.src="../images/hidetable.gif";
		e.myProperty=false;
		}
	}

</script>