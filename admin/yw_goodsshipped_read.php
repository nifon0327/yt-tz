<?php
//电信-EWEN
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=24;
$tableMenuS=750;
ChangeWtitle("$SubCompany 已出订单列表");
$funFrom="yw_goodsshipped";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|60|序号|40|收款<br>账号|60|报关<br>方式|30|快递签收单|80|Forward签收单|80|出货日期|50|Invoice|120|Invoice备注|100|标签|40|出货数量|60|出货金额<br>(USD)|80|其它收款<br>(USD)|60|金额合计<br>(USD)|80|应收<br>(RMB)|80|已收款|80|客户退款<br>(USD)|80|TT备注|40|件数|50|上海<br>称重|60|公司<br>称重|60|重量差<br>(差率)|60|中港<br>运费|60|入仓费<br>(HKD)|60|杂费<br>(HKD)|60|实际FOB|60|预估FOB|60|FOB差<br>(差率)|60";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;

$ActioToS="38,152";//0,8,9

if ($Login_BranchId==$APP_CONFIG['FINANCE_BRANCHID'] || in_array($Login_GroupId, $APP_CONFIG['IT_DEVELOP_GROUPID'])){
	$ActioToS="38,152,175";
}
$sumCols="8,9,10,11,12,13,14,16,17";			//求和列,需处理
//步骤3：
include "../model/subprogram/business_authority.php";//看客户权限
include "../model/subprogram/read_model_3.php";
$Keys=31;
//步骤4：需处理-条件选项
if($From!="slist"){
	//月份
	$SearchRows="";
	$SearchRows=" and M.Estate='0'";
	$date_Result = mysql_query("SELECT M.Date FROM $DataIn.ch1_shipmain M WHERE 1 $SearchRows GROUP BY DATE_FORMAT(M.Date,'%Y-%m') ORDER BY M.Date DESC",$link_id);
	if($dateRow = mysql_fetch_array($date_Result)){
		echo"<select name='chooseDate' id='chooseDate' onchange='RefreshPage(\"$nowWebPage\")'>";
		do{
			$dateValue=date("Y-m",strtotime($dateRow["Date"]));
			$StartDate=$dateValue."-01";
			$EndDate=date("Y-m-t",strtotime($dateRow["Date"]));
			$chooseDate=$chooseDate==""?$dateValue:$chooseDate;
			if($chooseDate==$dateValue){
				echo"<option value='$dateValue' selected>$dateValue</option>";
				//$SearchRows.=" and ((M.Date>'$StartDate' and M.Date<'$EndDate') OR M.Date='$StartDate' OR M.Date='$EndDate')";
                   $SearchRows.=" AND DATE_FORMAT(M.Date,'%Y-%m')='$dateValue'";
				}
			else{
				echo"<option value='$dateValue'>$dateValue</option>";
				}
			}while($dateRow = mysql_fetch_array($date_Result));
		echo"</select>&nbsp;";
		}
	//客户：计算金额并排序
	$clientResult = mysql_query("
		SELECT Amount,CompanyId,Forshort FROM (
			SELECT SUM( S.Price*S.Qty*M.Sign*D.Rate) AS Amount,M.CompanyId,C.Forshort
			FROM $DataIn.ch1_shipmain M
			LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid = M.Id
			LEFT JOIN $DataIn.trade_object C ON C.CompanyId = M.CompanyId
			LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency
			WHERE 1 $SearchRows  $ClientStr  GROUP BY M.CompanyId
		)A ORDER BY Amount DESC",$link_id);

	if($clientRow = mysql_fetch_array($clientResult)) {
		echo"<select name='CompanyId' id='CompanyId' onchange='RefreshPage(\"$nowWebPage\")'>";
		echo"<option value='' selected>全部客户</option>";
		$j=1;
		do{
			$thisCompanyId=$clientRow["CompanyId"];
			$Forshort=$clientRow["Forshort"];
			if($CompanyId==$thisCompanyId){
				echo"<option value='$thisCompanyId' selected>$j - $Forshort</option>";
				$SearchRows.=" and M.CompanyId='$thisCompanyId' ";
				}
			else{
				echo"<option value='$thisCompanyId'>$j - $Forshort</option>";
				}
			$j++;
			}while ($clientRow = mysql_fetch_array($clientResult));
		echo"</select>&nbsp;";
		}

		//==========发货状态
		$DeliverySign=$DeliverySign==""?1:$DeliverySign;
		$StrSign='StrSign'.$DeliverySign;
		$$StrSign='selected';
		echo"<select name='DeliverySign' id='DeliverySign' onchange='RefreshPage(\"$nowWebPage\")'>";
		echo"<option value='1' $StrSign1>全部发货</option>";
		echo"<option value='0' $StrSign0>库存</option>";
		echo"</select>&nbsp;";
		//echo $DeliverySign;
		if($DeliverySign!=""){
		  switch($DeliverySign){
		   case 0:$SearchRows.=" AND M.Id IN (SELECT ShipId FROM $DataIn.ch1_shipout)";
		       break;
		   case 1:$SearchRows.="";
		      break;
		  }
		}

		//==========扣款分类
		$ShipTypeSign=$ShipTypeSign==""?"":$ShipTypeSign;
		$StrSign='ShipType'.$ShipTypeSign;
		$$StrSign='selected';
		echo"<select name='ShipTypeSign' id='ShipTypeSign' onchange='RefreshPage(\"$nowWebPage\")'>";
		echo"<option value=''  $ShipType>全部款</option>";
		echo"<option value='1' $ShipType1>credit</option>";
		echo"<option value='2' $ShipType2>debit</option>";
		echo"</select>&nbsp;";
		//echo $DeliverySign;
		if($ShipTypeSign!=""){
		  switch($ShipTypeSign){
		   case 1:$SearchRows.=" AND M.ShipType='credit' ";
		       break;
		   case 2:$SearchRows.=" AND M.ShipType='debit' ";
		      break;
		  }
		}

	}
	//未收货款
	$ShipResult = mysql_query("SELECT SUM( S.Price*S.Qty*M.Sign) AS Amount,
	        M.CompanyId,C.Forshort,C.Currency,D.Rate
            FROM $DataIn.ch1_shipmain M
            LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid = M.Id
            LEFT JOIN $DataIn.trade_object C ON C.CompanyId = M.CompanyId
            LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency
            WHERE M.Estate =0 AND M.cwSign IN (1,2) GROUP BY M.CompanyId ORDER BY Amount DESC",$link_id);
   $Total=0;$Total_1=0;$Total_2=0;$Total_3=0;
   if($ShipRow = mysql_fetch_array($ShipResult)) {
	$i=1;
	do{
		$FK_1=0;$FK_2=0;$FK_3=0;$FK_JY=0;
		$CompanyId=$ShipRow["CompanyId"];
		//计算部分结付单的金额
		$CheckPart=mysql_fetch_array(mysql_query("SELECT SUM(-P.Amount*M.Sign) AS GatheringSUM 
		FROM $DataIn.cw6_orderinsheet P
		LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=P.chId 
		LEFT JOIN $DataIn.trade_object C ON C.CompanyId = M.CompanyId
		LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency
		WHERE M.cwSign='2' AND M.CompanyId='$CompanyId'",$link_id));
		$PayedAmount=$CheckPart["GatheringSUM"];
		$Forshort=$ShipRow["Forshort"];
		$Rate=$ShipRow["Rate"];
		$Currency=$ShipRow["Currency"];
		$TempFKSTR="FK_".strval($Currency);
		//预收货款
		$CheckPreJY=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(Amount),0) FK_JY FROM $DataIn.cw6_advancesreceived WHERE CompanyId='$CompanyId' AND Mid='0'",$link_id));
		$FK_JY=$CheckPreJY["FK_JY"];
		$Yshk+=$FK_JY*$Rate;
		$$TempFKSTR=sprintf("%.2f",($ShipRow["Amount"]-$FK_JY+$PayedAmount));
		$TempFKSUM="Total_".strval($Currency);$$TempFKSUM=$$TempFKSUM+$$TempFKSTR;
		$TempRMB=$$TempFKSTR*$Rate;
		$Total=$Total+$TempRMB;
        $i++;
		}while ($ShipRow = mysql_fetch_array($ShipResult));
	}
$Total=number_format($Total);
$wsfkout="<a href='../desktask/desk_clientfkcount_read.php' target='_blank' style='CURSOR: pointer;color:red;'>未收货款($Total)</a>";

$JobResult = mysql_query("SELECT JobId FROM $DataPublic.staffmain WHERE Number='$Login_P_Number' and JobId='5' ",$link_id);
if (mysql_num_rows($JobResult)>0 || $Login_P_Number==10868 || $Login_P_Number==12204){
	$toSaveExcel="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href='yw_goodsshipped_saveExcel.php' target='_blank'>Save Excel(财务专用)</a>";
}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr &nbsp;&nbsp;$wsfkout &nbsp;&nbsp;$toSaveExcel";

//步骤5：
include "../model/subprogram/read_model_5.php";
include "../model/subprogram/CurrencyList.php";

//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
$mySql="SELECT M.Id,M.CompanyId,M.BankId,M.Number,M.InvoiceNO,M.InvoiceFile,M.Wise,M.Date,M.Estate,M.Locks,M.Sign,M.ShipType,M.Remark,M.Operator,T.Type as incomeType,C.Forshort,C.PayType,D.Rate,D.Symbol,M.Ship,T.Attached
FROM $DataIn.ch1_shipmain M 
LEFT JOIN $DataIn.ch1_shiptypedata T ON T.Mid=M.Id 
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency 
WHERE 1 $SearchRows $ClientStr  ORDER BY M.Date DESC";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
//合计初始化
$chQtySUM=0;
$usdAmountSUM=0;
$otherAmountSUM=0;
$totalAmountSUM=0;
$rmbAmountSUM=0;
$ReturnAmountSUM=0;
$BoxQtySUM=0;
$mcWGSUM=0;
$depotChargeSUM=0;
$FreightSUM=0;
$ForwardSUM=0;
$cwAmountSUM=0;
$forwardWGSUM=0;
$freightMid="";
$forwardMid="";
	do{
		$OrderSignColor="";
		$m=1;
		$Id=$myRow["Id"];
		$CompanyId=$myRow["CompanyId"];
		//$BankId=$myRow["BankId"]==4?"<div class='redB'>报关</div>":"&nbsp;";
		$BankId=$myRow["BankId"];
		switch($BankId){
			case 4:
				$BankId="<span class='redB'>研砼国内</span>"; break;
			case 5:
				$BankId="<span class='BlueB'>研砼上海</span>"; break;
			case 6:
				$BankId="<span class='redB'>鼠宝上海</span>"; break;
			case 7:
				$BankId="<span class='redB'>鼠宝国内</span>"; break;
			default:
				$BankId="&nbsp;"; break;  //其它旧的不显示
		}
		$Ship=$myRow["Ship"];
		$shipTypeResult = mysql_query("SELECT Id,Name FROM $DataPublic.ch_shiptype WHERE Id='$Ship'",$link_id);
		if($shipTypeRow = mysql_fetch_array($shipTypeResult)){
              $ShipTitle=$shipTypeRow["Name"];
		       $ShipInfo="<image src='../images/ship$Ship.png'  title='$ShipTitle' style='width:20px;height:20px;'/>";
          }
          else{
	        $ShipInfo="";
          }
		/*
		switch ($Ship){
			case '-1':
				$ShipInfo=""; break;
			case '0':
				$ShipInfo="<img src='../images/air.png' title='空运' width='20' height='20'>";break;
			case '1':
				$ShipInfo="<img src='../images/boat.png' title='船运' width='20' height='20'>";break;
			case '7':
				$ShipInfo="<img src='../images/car.png' title='陆运' width='20' height='20'>";break;
			case '8':
				$ShipInfo="<img src='../images/home.png' title='库存' width='20' height='20'>";break;
			case '9':
				$ShipInfo="<img src='../images/ups.png' title='UPS' width='20' height='20'>";break;
			case '10':
				$ShipInfo="<img src='../images/dhl.png' title='DHL' width='20' height='20'>";break;
			case '11':
				$ShipInfo="<img src='../images/sf.png' title='SF' width='20' height='20'>";break;
			case '12':
				$ShipInfo="<img src='../images/fedx.png' title='FedEx' width='30' height='12'>";break;
		}
		*/
		switch($myRow["ShipType"]){
			case "credit":
			   $ShipInfo="<img src='../images/Credit note.png' title='SF' width='20' height='20'>";break;
			   break;
			   case "debit":
			   $ShipInfo="<img src='../images/Debit note.png' title='SF' width='20' height='20'>";break;
			   break;
		}
		$Rate=$myRow["Rate"];
		$Symbol=$myRow["Symbol"];
		$Number=$myRow["Number"];
		$Forshort=$myRow["Forshort"];
		$InvoiceNO=$myRow["InvoiceNO"];
		$InvoiceFile=$myRow["InvoiceFile"];
		$BoxLable="<div class='redB'>未装箱</div>";
		//检查是否有装箱
		$checkPacking=mysql_query("SELECT Id FROM $DataIn.ch2_packinglist WHERE Mid='$Id' LIMIT 1",$link_id);
		if($PackingRow=mysql_fetch_array($checkPacking)){
			//加密参数
			$Parame1=anmaIn($Id,$SinkOrder,$motherSTR);
			$Parame2=anmaIn("Mid",$SinkOrder,$motherSTR);
			$BoxLable=$InvoiceFile==0?"&nbsp;":"<a href='ch_shippinglist_print.php?Parame1=$Parame1&Parame2=$Parame2' target='_blank'>查看</a>";
			}
		//Invoice查看
		//加密参数
		$f1=anmaIn($InvoiceNO,$SinkOrder,$motherSTR);
		$d1=anmaIn("download/invoice/",$SinkOrder,$motherSTR);
		$InvoiceFile=$InvoiceFile==0?"&nbsp;":"<a href=\"openorload.php?d=$d1&f=$f1&Type=&Action=7\" target=\"download\"  style='CURSOR: pointer;color:#FF6633'>$InvoiceNO $ShipInfo</a>";
		$Wise=$myRow["Wise"]==""?"&nbsp;":$myRow["Wise"];
		$Date=$myRow["Date"];
		$Days=CountDays($Date,0)==0?"今天":CountDays($Date,0);
		$Date=date("d",strtotime($Date))."日<br><span style=\"color:#CCC\">".$Days."</span>";
		$Locks=$myRow["Locks"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Sign=$myRow["Sign"];
        $ShipType=$myRow["ShipType"];
        $Attached=$myRow["Attached"];
        $incomeType=$myRow["incomeType"]==1?"<span class='redB'>报关</span>":"&nbsp;";
        if($Attached!="" && $Attached!=0 &&$myRow["incomeType"]==1){
		  $f2=anmaIn($Attached,$SinkOrder,$motherSTR);
		  $d2=anmaIn("download/shiptype/",$SinkOrder,$motherSTR);
          $incomeType="<a href=\"openorload.php?d=$d2&f=$f2&Type=&Action=6\" target=\"download\"  style='CURSOR: pointer;color:#339900'>报关</a>";
          }
		$MainRemark=$myRow["Remark"]==""?"&nbsp;":$myRow["Remark"];
		$MainRemark="<span class='yellowB'>$MainRemark</span>";
		//$MainRemark="<img src='../images/remark.gif' title='$MainRemark' width='18' height='18'>";
		//订单出货金额
		$checkAmount=mysql_fetch_array(mysql_query("SELECT SUM(Qty*Price) AS Amount,SUM(Qty) AS chQty  FROM $DataIn.ch1_shipsheet WHERE Mid='$Id' AND (Type=1 OR Type=3)",$link_id));
		//$checkAmount=mysql_fetch_array(mysql_query("SELECT SUM(substr(Qty*Price,1,position('.' in Qty*Price)+2)) AS Amount,SUM(Qty) AS chQty  FROM $DataIn.ch1_shipsheet WHERE Mid='$Id' AND (Type=1 OR Type=3)",$link_id));

		$Amount=round(($checkAmount["Amount"])*$Sign,2);//出货金额取值方式要与已收款的取值方式一致，不能一个使用四舍五入一个不使用，以免两个数值对比时产生错误判断
		$chQty=$checkAmount["chQty"];
		$chQtySUM+=$chQty;
		if($Symbol!="USD"){
			$usdAmount=sprintf("%.2f",$Amount/$USDRate);//转为USD
			}
		else{
			$usdAmount=sprintf("%.2f",$Amount);//转为USD
			}

                 //其它金额
               $checkAmount2=mysql_fetch_array(mysql_query("SELECT SUM(Qty*Price) AS Amount FROM $DataIn.ch1_shipsheet WHERE Mid='$Id' AND Type=2",$link_id));
               $Amount2=round(($checkAmount2["Amount"])*$Sign,2);//出货金额取值方式要与已收款的取值方式一致，不能一个使用四舍五入一个不使用，以免两个数值对比时产生错误判断
		if($Symbol!="USD"){
			$otherAmount=sprintf("%.2f",$Amount2/$USDRate);//转为USD
			}
		else{
			$otherAmount=sprintf("%.2f",$Amount2);//转为USD
			}

		//求和
		$usdAmountSUM=sprintf("%.2f",$usdAmountSUM+$usdAmount);
                $otherAmountSUM=sprintf("%.2f",$otherAmountSUM+$otherAmount);
                $totalAmount=sprintf("%.2f",$usdAmount+$otherAmount);

                $usdAmount=$usdAmount==0?"&nbsp;":$usdAmount;
                $otherAmount=$otherAmount==0?"&nbsp;":$otherAmount;
                $Amount=$Amount+$Amount2;
		$rmbAmount=sprintf("%.2f",$Rate*$Amount);
		$rmbAmountSUM=sprintf("%.2f",$rmbAmountSUM+$rmbAmount);
		$showPurchaseorder="<img onClick='sOrhOrder(StuffList$i,showtable$i,StuffList$i,\"$Id\",$i);' name='showtable$i' src='../images/showtable.gif' title='显示或隐藏出货订单明细.' width='13' height='13' style='CURSOR: pointer'>";
		$StuffListTB="<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'><tr bgcolor='#B7B7B7'><td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";
		//中港运费处理:运费和入仓费


		//单一invoice箱数
		$PackingResult = mysql_query("SELECT SUM(L.BoxRow*L.BoxQty) AS BoxPcs , SUM(L.WG*L.BoxQty) as WG
									  FROM $DataIn.ch2_packinglist L 
									  WHERE L.Mid='$Id' ORDER BY L.Id",$link_id);
		if($PackingRow = mysql_fetch_array($PackingResult))
		{
			$BoxPcs=$PackingRow["BoxPcs"];		//装箱数量
			$WG = $PackingRow["WG"];
		}

		$BoxQty="&nbsp;";$mcWG="&nbsp;";$depotCharge="&nbsp;";$Freight="&nbsp;";$Forward="&nbsp;";$forwardWG="&nbsp;";$DifferenceWG="&nbsp;";
		//$depotCharge_1="&nbsp;";$Freight_1="&nbsp;";$Forward_1="&nbsp;";
		$checkFreight=mysql_query("SELECT F.BoxQty,F.mcWG,F.depotCharge,F.mcWG*Price AS Amount, F.Price,I.Mid
		                           FROM $DataIn.ch4_freight_declaration F
								   LEFT JOIN $DataIn.ch4_freight_invoice I ON I.Mid=F.Id
		                           WHERE I.chId='$Id' LIMIT 1",$link_id);

		if($FreightRow=mysql_fetch_array($checkFreight))
		{
		    $sMid=$FreightRow["Mid"];

			$BoxQty=$FreightRow["BoxQty"];
			$mcWG=$FreightRow["mcWG"];
			$depotCharge=$FreightRow["depotCharge"];
			$Freight=sprintf("%.2f",$FreightRow["Amount"]);
			$BoxQtySUM+=$BoxPcs;

			$price = $FreightRow["Price"];
			$depotCharge_1=$FreightRow["depotCharge"];
			$Freight_1=sprintf("%.2f",$FreightRow["Amount"]);

			if($freightMid!=$sMid)
			{
				$mcWGSUM+=$mcWG;
				$depotChargeSUM+=$depotCharge;
				$FreightSUM+=$Freight;
			}
			$freightMid=$sMid;
			$Freight = round($price * $WG, 2);
			$depotCharge = round(($WG/$mcWG) * $depotCharge, 2);
		}

		//杂费

			$checkForward=mysql_query("SELECT F.Amount,F.forwardWG,I.Mid FROM $DataIn.ch3_forward F
		                          	   LEFT JOIN $DataIn.ch3_forward_invoice I ON I.Mid=F.Id
								  	   WHERE I.chId='$Id' LIMIT 1",$link_id);
			if($ForwardRow=mysql_fetch_array($checkForward))
			{

		    	$cMid=$ForwardRow["Mid"];

		    	//装箱数
				$forwardBoxQty = mysql_query("SELECT SUM(BoxQty) as Qty FROM $DataIn.ch3_forward F
		                          	   LEFT JOIN $DataIn.ch3_forward_invoice I ON I.Mid=F.Id
								  	   WHERE I.chId='$Id' LIMIT 1");

			    $forwardBoxRow = mysql_fetch_assoc($forwardBoxQty);
			    $forwardTotleBox = $forwardBoxRow["Qty"];
				//echo "box:$BoxPcs----forwardTotleQty:$forwardTotleBox";
			    $Forward=sprintf("%.2f",$ForwardRow["Amount"]);
			    $forwardWG=sprintf("%.2f",$ForwardRow["forwardWG"]);
                $mcWG=$mcWG==0?$WG:$mcWG;
			    $DifferenceWG=sprintf("%.2f",$mcWG-$forwardWG);
			    if($forwardMid!=$cMid)
			    {
			    	$ForwardSUM+=$Forward;
				 	$forwardWGSUM+=$forwardWG;
				}
				$forwardMid=$cMid;
				$Forward_1=sprintf("%.2f",$ForwardRow["Amount"]);
				if($forwardTotleBox == "" || $forwardTotleBox == 0)
				{
					$Forward = $Forward;
				}
				else
				{
					$Forward = round(($BoxPcs/$forwardTotleBox) * $Forward, 2);
				}
			}



		//实际FOB
		$SjFOB=0;
		$GjFOB=0;
		$CeFOB=0;
		$ClFOB=0;
		$SjFOB=sprintf("%.2f",$depotCharge*$HKDRate+$Freight+$Forward*$HKDRate);
		//$SjFOB=sprintf("%.2f",$depotCharge_1*$HKDRate+$Freight_1+$Forward_1*$HKDRate);
			//估计FOB
			$GjRow=mysql_fetch_array(mysql_query("SELECT SUM(G.OrderQty*G.Price) AS GjFob
			FROM $DataIn.ch1_shipsheet S
			LEFT JOIN $DataIn.cg1_stocksheet G ON  G.POrderId=S.POrderId
			LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
			WHERE S.Mid=$Id AND D.TypeId=8000
			",$link_id));
			$GjFOB=sprintf("%.2f",$GjRow["GjFob"]);
			$CeFOB=sprintf("%.2f",$GjFOB-$SjFOB);
			if ($SjFOB!=0) $ClFOB=sprintf("%.0f",($CeFOB*100)/$SjFOB); else $ClFOB=0;
		//已收款
		$chId=$mainRows["chId"];
		$checkShipAmount=mysql_query("SELECT SUM(S.Amount) AS ShipAmount,concat(M.Remark) AS Remark 
		FROM $DataIn.cw6_orderinsheet S 
		LEFT JOIN $DataIn.cw6_orderinmain M ON M.Id=S.Mid
		WHERE S.chId='$Id' GROUP BY S.chId",$link_id);

		if (mysql_num_rows($checkShipAmount)>0){
			  $Remark=mysql_result($checkShipAmount,0,"Remark");
		      $ShipAmount=mysql_result($checkShipAmount,0,"ShipAmount");
		      $Remark="<img src='../images/remark.gif' title='$Remark' width='18' height='18'>";
		}
		else{
			$Remark="&nbsp;";
		}
		//$Remark=$Remark==""?"&nbsp;":"<img src='../images/remark.gif' title='$Remark' width='18' height='18'>";
		$ShipAmount=$ShipAmount==""?"&nbsp;":round($ShipAmount,2);
		if($ShipAmount!="&nbsp;" || $Amount==0){
			$cwAmountSUM=sprintf("%.2f",$cwAmountSUM+$ShipAmount);

			if(sprintf("%.2f",$Amount)-sprintf("%.2f",$ShipAmount)==0){
				$ShipAmount="<span class='greenB'>$ShipAmount</span>";
				$OrderSignColor="bgColor='#339900'";
				}
			else{
				$ShipAmount="<span class='yellowB' title=\"$Amount==$ShipAmount\">$ShipAmount</span>";
				$OrderSignColor="bgColor='#FFCC00'";
				}
			}
                //客户退款
                 $checkReturnAmount=mysql_query("SELECT SUM(S.Qty*G.Price) AS ReturnAmount
			FROM $DataIn.ch1_shipsheet S 
			LEFT JOIN $DataIn.cg1_stocksheet G ON  G.POrderId=S.POrderId 
			LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
			WHERE S.Mid=$Id AND D.TypeId=9104",$link_id);
                 $ReturnAmount=sprintf("%.2f",mysql_result($checkReturnAmount,0,"ReturnAmount"));
                 $ReturnAmountSUM+=$ReturnAmount;
                 $ReturnAmount=zerotospace($ReturnAmount);

		//扣款处理
		if($Sign<0){
			$usdAmount="<div class='redB'>$usdAmount</div>";
                        //$otherAmount="<div class='redB'>$otherAmount</div>";
			$rmbAmount="<div class='redB'>$rmbAmount</div>";
			}
		$Sum_SjFOB+=$SjFOB;
		$Sum_GjFOB+=$GjFOB;
		$Sum_CeFOB+=$CeFOB;
		$SjFOB=zerotospace($SjFOB);
		$GjFOB=zerotospace($GjFOB);
		$CeFOB=zerotospace($CeFOB);
		if($ClFOB!=0){
			if($ClFOB<0){
				$CeFOB=$CeFOB."<br><span class='redB'>(".zerotospace($ClFOB)."%)</span>";
				}
			else{
				$CeFOB=$CeFOB."<br><span class='blueB'>(".zerotospace($ClFOB)."%)</span>";
				}
			}
		//重量差比率
		if ($mcWG!=0)  $ClWG=zerotospace(sprintf("%.2f",($DifferenceWG/$mcWG))*100); else $ClWG=0;
		if($DifferenceWG!=0 && $mcWG>0 ){
			if($DifferenceWG<0){
				$ClWG=$ClWG>-1?"":"<br><span class='redB'>(".$ClWG."%)</span>";
				$DifferenceWG=$DifferenceWG.$ClWG;
				}
			else{
				$ClWG=$ClWG<1?"":"<br><span class='blueB'>(".$ClWG."%)</span>";
				$DifferenceWG=$DifferenceWG.$ClWG;
				}
			}
		//如果货款性质为1，则检查是否已收款，如果没收，则不显示标签
		if($myRow["PayType"]==1 && $OrderSignColor!="bgColor='#339900'"){
			$BoxLable="<span class=\"redB\">未收款</span>";
			$OrderSignColor="bgColor='#F00'";
			}

   $CheckNumberResult=mysql_fetch_array(mysql_query("SELECT  * FROM $DataIn.ch1_shipfile WHERE ShipId=$Id",$link_id));
  $expressBackNumber=$CheckNumberResult["ExpressNum"];
  $ForwardBackNumber=$CheckNumberResult["ForwardNum"];
   if($expressBackNumber!=""){
          $d5=anmaIn("download/expressreback/",$SinkOrder,$motherSTR);
		 $f5=anmaIn($Id."_".$expressBackNumber,$SinkOrder,$motherSTR);
          $expressBackNumber="&nbsp;&nbsp;<a href=\"openorload.php?d=$d5&f=$f5&Type=&Action=7\" target=\"download\">$expressBackNumber</a>";
      }
   else {
        $expressBackNumber="&nbsp;";
      }
   if($ForwardBackNumber!=""){
          $d6=anmaIn("download/forwardreback/",$SinkOrder,$motherSTR);
		 $f6=anmaIn($Id."_".$ForwardBackNumber,$SinkOrder,$motherSTR);
          $ForwardBackNumber="&nbsp;&nbsp;<a href=\"openorload.php?d=$d6&f=$f6&Type=&Action=7\" target=\"download\">$ForwardBackNumber</a>";
      }
   else {
        $ForwardBackNumber="&nbsp;";
      }
      $expressBack = $expressBackNumber;
      $ForwardBack =$ForwardBackNumber;


		$ValueArray=array(
			array(0=>$BankId,			1=>"align='center'"),
			array(0=>$incomeType,			1=>"align='center'"),
			   array(0=>$expressBack,			1=>"align='center'"),
			   array(0=>$ForwardBack,			1=>"align='center'"),
			array(0=>$Date,			1=>"align='center'"),
			array(0=>$InvoiceFile),
			array(0=>$MainRemark),
			array(0=>$BoxLable,		1=>"align='center'"),
			array(0=>$chQty,		1=>"align='right'"),
			array(0=>$usdAmount, 	1=>"align='right'"),
            array(0=>$otherAmount, 	1=>"align='right'"),
            array(0=>$totalAmount, 	1=>"align='right'"),
			array(0=>$rmbAmount, 	1=>"align='right'"),
			array(0=>$ShipAmount,	1=>"align='right'"),
            array(0=>$ReturnAmount,	1=>"align='right'"),
			array(0=>$Remark, 		1=>"align='center'"),
			array(0=>$BoxPcs, 		1=>"align='center'"),
			array(0=>$forwardWG, 	1=>"align='right'"),
			array(0=>$WG,	 		1=>"align='right'"),
			array(0=>$DifferenceWG, 1=>"align='right'"),
			array(0=>$Freight, 		1=>"align='center'"),
			array(0=>$depotCharge,	1=>"align='right'"),
			array(0=>$Forward,		1=>"align='right'"),
			array(0=>$SjFOB, 1=>"align='right'"),
			array(0=>$GjFOB, 1=>"align='right'"),
			array(0=>$CeFOB, 1=>"align='right'"),
			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		echo $StuffListTB;
		}while ($myRow = mysql_fetch_array($myResult));
	//合计
	$SjFOBSum=round($depotChargeSUM*$HKDRate+$FreightSUM+$ForwardSUM);
	$Sum_SjFOB=round($Sum_SjFOB);
	$Sum_CeFOB=round($Sum_CeFOB);
	$Sum_GjFOB=round($Sum_GjFOB);
	$Sum_ClFOB=$Sum_SjFOB==0?0:sprintf("%.0f",($Sum_GjFOB-$Sum_SjFOB)*100/$Sum_SjFOB);
	if($Sum_ClFOB<0){
		$Sum_CeFOB="<span class='redB'>$Sum_CeFOB<br>(".$Sum_ClFOB."%)</span>";
		}
	else{
		$Sum_CeFOB="<span class='blueB'>$Sum_CeFOB<br>(".$Sum_ClFOB."%)</span>";
		}
        $totalAmountSUM=$usdAmountSUM+$otherAmountSUM;
        $ReturnAmountSUM=sprintf("%.2f",$ReturnAmountSUM);
	echo"<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'>
			<tr>
				<td colspan='6' height='25' class='A0111' align='center'>合计</td>
				<td class='A0101' align='right' width='60'>$chQtySUM</td>
				<td class='A0101' align='right' width='80'>$usdAmountSUM</td>
                                <td class='A0101' align='right' width='60'>$otherAmountSUM</td>
                                <td class='A0101' align='right' width='80'>$totalAmountSUM</td>
				<td class='A0101' align='right' width='80'>$rmbAmountSUM</td>
                                <td class='A0101' align='right' width='80'>$cwAmountSUM</td>
				<td class='A0101' align='right' width='80'>$ReturnAmountSUM</td>
				<td class='A0101' align='right' width='40'>&nbsp;</td>
				<td class='A0101' align='center' width='50'>$BoxQtySUM</td>
				<td class='A0101' align='right' width='60'>$forwardWGSUM</td>
				<td class='A0101' align='right' width='60'>$mcWGSUM</td>
				<td class='A0101' align='right' width='60'>&nbsp;</td>	
				<td class='A0101' align='right' width='60'>$FreightSUM</td>
				<td class='A0101' align='right' width='60'>$depotChargeSUM</td>	
				<td class='A0101' align='right' width='60'>$ForwardSUM</td>
				<td class='A0101' align='right' width='60'><span class=\"rmbB\">$Sum_SjFOB</span></td>
				<td class='A0101' align='right' width='60'><span class=\"rmbB\">$Sum_GjFOB</span></td>
				<td class='A0101' align='right' width='60'>$Sum_CeFOB</td>
			</tr></table>";
	}
else{
	noRowInfo($tableWidth);
  	}
//步骤7：
echo '</div>';
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
$Keys=31;
include "../model/subprogram/read_model_menu.php";
?>