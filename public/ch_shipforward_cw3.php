<?php
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
//步骤3：
include "../model/subprogram/read_model_3.php";
if($From!="slist"){
	//划分权限:如果没有最高权限，则只显示自己的记录
	$SearchRows="";
	$TempEstateSTR="EstateSTR".strval($Estate);
	$$TempEstateSTR="selected";
	$SearchRows=" and F.Estate='$Estate'";
	echo"<select name='Estate' id='Estate' onchange='ResetPage(this.name)'><option value='3' $EstateSTR3>未结付</option><option value='0' $EstateSTR0>已结付</option></select>&nbsp;";
	$selectedStr="strType".$TypeId;
	$$selectedStr="selected";
	echo "<select name='TypeId' id='TypeId' onchange='RefreshPage(\"$nowWebPage\")'>";
	echo "<option value='1' $strType1>invoice</option>
	      <option value='2' $strType2>提货单</option>
		  </select>&nbsp;";
	if($TypeId==1){
	        $TempTable="ch1_shipmain";
	        $SearchRows.=" AND F.TypeId='$TypeId'";
			}
	    else{
		    $TempTable="ch1_deliverymain";
	        $SearchRows.=" AND F.TypeId='$TypeId'";
		    }
	$monthResult = mysql_query("SELECT F.Date FROM $DataIn.ch3_forward F WHERE 1 $SearchRows group by DATE_FORMAT(F.Date,'%Y-%m') order by F.Date DESC",$link_id);
	if ($monthResult  && $dateRow = mysql_fetch_array($monthResult)) {
		echo"<select name='chooseDate' id='chooseDate' onchange='document.form1.submit()'>";
		do{
			$dateValue=date("Y-m",strtotime($dateRow["Date"]));
			$StartDate=$dateValue."-01";
			$EndDate=date("Y-m-t",strtotime($dateRow["Date"]));
			$chooseDate=$chooseDate==""?$dateValue:$chooseDate;
			if($chooseDate==$dateValue){
				echo"<option value='$dateValue' selected>$dateValue</option>";
				$SearchRows.=" and ((F.Date>'$StartDate' and F.Date<'$EndDate') OR F.Date='$StartDate' OR F.Date='$EndDate')";
				}
			else{
				echo"<option value='$dateValue'>$dateValue</option>";
				}
			}while($dateRow = mysql_fetch_array($monthResult));
		echo"</select>&nbsp;";
		}
	else{
		//无月份记录
		$SearchRows.=" and F.Date=''";
		}
	//货运公司
	$goodsResult = mysql_query("SELECT F.CompanyId,D.Forshort 
	FROM $DataIn.ch3_forward F
    LEFT JOIN $DataIn.ch3_forward_invoice I ON I.Mid=F.Id
	LEFT JOIN $DataIn.$TempTable M ON M.Id=I.chId
	LEFT JOIN $DataPublic.freightdata D ON D.CompanyId=F.CompanyId 
	WHERE 1 $SearchRows GROUP BY F.CompanyId ORDER BY F.CompanyId",$link_id);
	if($goodsResult  && $goodsRow = mysql_fetch_array($goodsResult)) {
		echo"<select name='CompanyId' id='CompanyId' onchange='RefreshPage(\"$nowWebPage\")'>";
		do{
			$thisCompanyId=$goodsRow["CompanyId"];
			$Forshort=$goodsRow["Forshort"];
			$CompanyId=$CompanyId==""?$thisCompanyId:$CompanyId;
			if($CompanyId==$thisCompanyId){
				echo"<option value='$thisCompanyId' selected>$Forshort</option>";
				$SearchRows.=" and F.CompanyId='$thisCompanyId' ";
				}
			else{
				echo"<option value='$thisCompanyId'>$Forshort</option>";
				}
			}while ($goodsRow = mysql_fetch_array($goodsResult));
		echo"</select>&nbsp;";
		}
	//费用来源
	$PayStr="strPay".$PayType;
	$$PayStr="selected";
	echo "<select name='PayType' id='PayType' onchange='RefreshPage(\"$nowWebPage\")'>";
	echo "<option value='0' $strPay0>自付</option>
	      <option value='1' $strPay1>代付</option>
		  </select>&nbsp;";
	$SearchRows.=" AND F.PayType='$PayType'";
		//******************客户
	/*$clientResult = mysql_query("SELECT * FROM (
	SELECT C.Forshort AS ClientId,SUBSTRING( C.Forshort, 1, 3 ) AS ClientForshort
	FROM $DataIn.ch3_forward F
    LEFT JOIN $DataIn.ch3_forward_invoice I ON I.Mid=F.Id
	LEFT JOIN $DataIn.$TempTable M ON M.Id=I.chId
	LEFT JOIN $DataPublic.freightdata D ON D.CompanyId=F.CompanyId
    LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId
	WHERE 1 $SearchRows  ) A GROUP BY ClientForshort",$link_id);
	if($clientRow = mysql_fetch_array($clientResult)) {
		echo"<select name='ClientId' id='ClientId' onchange='RefreshPage(\"$nowWebPage\")'>";
		echo"<option value='' selected>全部客户</option>";
		do{
			$thisClientId=$clientRow["ClientId"];
			$ClientForshort=$clientRow["ClientForshort"];
			 //$ClientId=$ClientId==""?$thisClientId:$ClientId;
			if($ClientId==$thisClientId){
				echo"<option value='$thisClientId' selected>$ClientForshort</option>";
				$SearchRows.=" and C.Forshort  like '$thisClientId%' ";
				}
			else{
				echo"<option value='$thisClientId'>$ClientForshort</option>";
				}
			}while ($clientRow = mysql_fetch_array($clientResult));
		echo"</select>&nbsp;";
		}*/

	}
else{
	echo"<select name='Estate' id='Estate' onchange='ResetPage(this.name)'>
	<option value='3' $EstateSTR3>未结付</option>
	</select>&nbsp;";
	$SearchRows.=" and F.Estate='$Estate'";
	    if($TypeId==1){
	        $TempTable="ch1_shipmain";
	        $SearchRows.=" AND F.TypeId='$TypeId'";
			}
	    else{
		    $TempTable="ch1_deliverymain";
	        $SearchRows.=" AND F.TypeId='$TypeId'";
		    }
	}

//结付的银行
include "../model/selectbank1.php";
echo"$CencalSstr";
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>";

//非必选,过滤条件
include "../model/subprogram/read_model_5.php";
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
if($TypeId==1){
    $mySql="SELECT M.Number,M.InvoiceNO,M.Date AS ShipDate,M.InvoiceFile,
    F.Id,F.chId,F.HoldNO,F.ForwardNO,F.BoxQty,F.mcWG,F.forwardWG,F.Volume,F.HKVolume,F.VolumeKG,F.HKVolumeKG,
    F.Amount,F.InvoiceDate,F.PayType,
    F.ETD,F.Remark,F.Estate,F.Locks,F.Date,P.Name AS Operator,D.Forshort,I.Mid,
    F.CFSCharge,F.THCCharge,F.WJCharge,F.SXCharge,F.ENSCharge,F.BXCharge,F.GQCharge,F.DFCharge,F.TDCharge,F.OtherCharge  
    FROM $DataIn.ch3_forward F
    LEFT JOIN $DataIn.ch3_forward_invoice I ON I.Mid=F.Id
    LEFT JOIN $DataIn.$TempTable M ON M.Id=I.chId
    LEFT JOIN $DataPublic.freightdata D ON D.CompanyId=F.CompanyId 
    LEFT JOIN $DataPublic.staffmain P ON P.Number=F.Operator
    WHERE 1 $SearchRows ORDER BY F.Id DESC";
	}
else{
   $mySql="SELECT M.DeliveryNumber AS InvoiceNO,M.DeliveryDate AS ShipDate,
   F.Id,F.chId,F.HoldNO,F.ForwardNO,F.BoxQty,F.mcWG,F.forwardWG,F.Volume,F.HKVolume,F.VolumeKG,F.HKVolumeKG,
   F.Amount,F.InvoiceDate,F.PayType,
   F.ETD,F.Remark,F.Estate,F.Locks,F.Date,P.Name AS Operator,D.Forshort,I.Mid,
   F.CFSCharge,F.THCCharge,F.WJCharge,F.SXCharge,F.ENSCharge,F.BXCharge,F.GQCharge,F.DFCharge,F.TDCharge,F.OtherCharge  
   FROM $DataIn.ch3_forward F
   LEFT JOIN $DataIn.ch3_forward_invoice I ON I.Mid=F.Id
   LEFT JOIN $DataIn.$TempTable M ON M.Id=I.chId
   LEFT JOIN $DataPublic.freightdata D ON D.CompanyId=F.CompanyId 
   LEFT JOIN $DataPublic.staffmain P ON P.Number=F.Operator
   WHERE 1 $SearchRows ORDER BY F.Id DESC";
   }
 // echo  $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myResult && $myRow = mysql_fetch_array($myResult)){
    $tbDefalut=0;
	$midDefault="";
	$d=anmaIn("download/invoice/",$SinkOrder,$motherSTR);
	$d1=anmaIn("download/expressbill/",$SinkOrder,$motherSTR);
	$d2=anmaIn("download/DeliveryNumber/",$SinkOrder,$motherSTR);//提货单
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Mid=$myRow["Mid"];
		$ShipDate=$myRow["ShipDate"];
		$InvoiceNO=$myRow["InvoiceNO"];
		$InvoiceFile=$myRow["InvoiceFile"];
		//加密参数
		$f=anmaIn($InvoiceNO.".pdf",$SinkOrder,$motherSTR);
		if($TypeId==1){//invoice
		    $InvoiceFile=$InvoiceFile==0?"&nbsp;":"<a href=\"openorload.php?d=$d&f=$f&Type=&Action=6\" target=\"download\">$InvoiceNO</a>";
			 }
		else {
		    $filename="../download/DeliveryNumber/$InvoiceNO.pdf";
		    if(file_exists($filename)){
	           $InvoiceFile="<a href=\"openorload.php?d=$d2&f=$f&Type=&Action=6\" target=\"download\">$InvoiceNO</a>";}
			 else $InvoiceFile=$InvoiceNO;
			 }
		$Forshort=$myRow["Forshort"];
		$HoldNO=$myRow["HoldNO"]==""?"&nbsp;":$myRow["HoldNO"];

		$ForwardNO=$myRow["ForwardNO"];
		//提单
		$Lading="../download/expressbill/".$ForwardNO.".jpg";
		if(file_exists($Lading)){
			$f1=anmaIn($ForwardNO.".jpg",$SinkOrder,$motherSTR);
			$ForwardNO="<span onClick='OpenOrLoad(\"$d1\",\"$f1\")' style='CURSOR: pointer;color:#FF6633'>$ForwardNO</span>";
			//$ForwardNO="<a href=\"openorload.php?d=$d1&f=$f1&Type=&Action=\" target=\"download\">$ForwardNO</a>";
			}
		$BoxQty=$myRow["BoxQty"];
		$mcWG=$myRow["mcWG"];
		$forwardWG=$myRow["forwardWG"];
		$Volume=$myRow["Volume"];
		$HKVolume=$myRow["HKVolume"];
		$VolumeKG=$myRow["VolumeKG"];
		$HKVolumeKG=$myRow["HKVolumeKG"];
		$Amount=$myRow["Amount"];
		$CFSCharge=$myRow["CFSCharge"];
		$THCCharge=$myRow["THCCharge"];
		$WJCharge=$myRow["WJCharge"];
		$SXCharge=$myRow["SXCharge"];
		$ENSCharge=$myRow["ENSCharge"];
		$BXCharge=$myRow["BXCharge"];
		$GQCharge=$myRow["GQCharge"];
		$DFCharge=$myRow["DFCharge"];
		$TDCharge=$myRow["TDCharge"];
		$OtherCharge=$myRow["OtherCharge"];
		$InvoiceDate=$myRow["InvoiceDate"];
		$ETD=$myRow["ETD"]==""?"&nbsp;":$myRow["ETD"];
		$Operator=$myRow["Operator"];
		$Remark=$myRow["Remark"]==""?"&nbsp;":"<img src='../images/remark.gif' title='$myRow[Remark]' width='18' height='18'>";
		$Locks=1;
		$Estate="<div align='center' class='yellowB' title='请款通过,等候结付!'>√.</div>";
		$checkidValue=$Id;
		if($Locks==0){//锁定状态:A一种是可以操作记录（分权限）；B一种是不可操作记录（不分权限）
				if($Keys & mLOCK){
					if($LockRemark!=""){//财务强制锁定
					$Choose="&nbsp;&nbsp;&nbsp;<img src='../images/lock.png' title='$LockRemark' width='15' height='15'>";
						}
					else{
						$Choose="<input name='checkid[]' type='checkbox' id='checkid$i' value='$checkidValue' disabled><img src='../images/lock.png' width='15' height='15'>";
						}
					}
				else{		//A2：无权限对锁定记录操作
					$Choose="&nbsp;&nbsp;&nbsp;<img src='../images/lock.png' title='锁定操作!' width='15' height='15'>";
					}
				}
			else{
				if($Keys & mUPDATE || $Keys & mDELETE || $Keys & mLOCK){//有权限
					if($LockRemark!=""){
					$Choose="&nbsp;&nbsp;&nbsp;<img src='../images/lock.png' title='$LockRemark' width='15' height='15'>";
						}
					else{
						$Choose="<input name='checkid[]' type='checkbox' id='checkid$i' value='$checkidValue' disabled><img src='../images/unlock.png' width='15' height='15'>";
						}
					}
				else{//无权限
					$Choose="&nbsp;&nbsp;&nbsp;<img src='../images/lock.png' title='锁定操作!' width='15' height='15'>";
					}
				}
		if($tbDefalut==0 && $midDefault==""){//首行
				//并行列
				echo"<table width='$tableWidth' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'><tr bgcolor='$theDefaultColor' onmousedown='ClickKeyCheck(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' 
					onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
					onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
				echo"<td scope='col' class='A0111' width='$Field[$m]' align='center'>$Choose</td>";//选项
				$unitWidth=$tableWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$i</td>";//序号
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$InvoiceDate</td>";//发票日期
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Forshort</td>";//Froward公司
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$HoldNO</td>";//入仓号
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$ForwardNO</td>";//Forward Invoice
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$BoxQty</td>";//件数
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$mcWG</td>";//研砼称重
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$forwardWG</td>";//上海称重
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Volume</td>";
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$HKVolume</td>";
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$VolumeKG</td>";
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$HKVolumeKG</td>";
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$CFSCharge</td>";
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$THCCharge</td>";
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$WJCharge</td>";
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$SXCharge</td>";
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$ENSCharge</td>";
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$BXCharge</td>";
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$GQCharge</td>";
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$DFCharge</td>";
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$TDCharge</td>";
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$OtherCharge</td>";
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Amount</td>";//金额(HKD)
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$ETD</td>";//ETD/ETA
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Estate</td>";//状态
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Remark</td>";//备注
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Operator</td>";//操作
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td width='' class='A0101'>";
				$midDefault=$Mid;
				}
			if($midDefault!="" && $midDefault==$Mid){//同属于一个主ID，则依然输出明细表格
				$m=57;
				echo"<table width='100%' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'>";
				echo"<tr>";
				$unitFirst=$Field[$m]-1;
				//echo"<td class='A0001' width='$unitFirst' height='20' align='center'>$j</td>";			//序号
				//$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]'align='center'>$InvoiceFile</td>";//InvoiceNO
				$m=$m+2;
			    echo"<td class='A0001' width='$Field[$m]'align='center'>$ShipDate</td>";//出货日期
				echo"</tr></table>";
				$i++;
				$j++;
				}
			else{
				//新行开始
				echo"</td></tr></table>";//结束上一个表格
				//并行列
				echo"<table width='$tableWidth' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'><tr bgcolor='$theDefaultColor' onmousedown='ClickKeyCheck(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' 
					onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
					onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
				echo"<td scope='col' class='A0111' width='$Field[$m]' align='center'>$Choose</td>";//选项
				$unitWidth=$tableWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$i</td>";//序号
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$InvoiceDate</td>";//发票日期
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Forshort</td>";//Froward公司
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$HoldNO</td>";//入仓号
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$ForwardNO</td>";//Forward Invoice
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$BoxQty</td>";//件数
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$mcWG</td>";//研砼称重
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$forwardWG</td>";//上海称重
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Volume</td>";
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$HKVolume</td>";
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$VolumeKG</td>";
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$HKVolumeKG</td>";
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$CFSCharge</td>";
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$THCCharge</td>";
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$WJCharge</td>";
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$SXCharge</td>";
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$ENSCharge</td>";
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$BXCharge</td>";
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$GQCharge</td>";
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$DFCharge</td>";
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$TDCharge</td>";
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$OtherCharge</td>";
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Amount</td>";//金额(HKD)
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$ETD</td>";//ETD/ETA
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Estate</td>";//状态
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Remark</td>";//备注
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Operator</td>";//操作
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				//echo"<td width='$unitWidth' class='A0101'>";
				echo"<td width='' class='A0101'>";
				$midDefault=$Mid;
				echo"<table width='100%' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'>";
				echo"<tr>";
				$unitFirst=$Field[$m]-1;
				//echo"<td class='A0001' width='$unitFirst' height='20' align='center'>$j</td>";			//序号
				//$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]'align='center'>$InvoiceFile</td>";//InvoiceNO
				$m=$m+2;
			    echo"<td class='A0001' width='$Field[$m]'align='center'>$ShipDate</td>";//出货日期
				echo"</tr></table>";
				$i++;
				$j++;
				}
		    //include "../model/subprogram/read_model_6.php";
		  }while ($myRow = mysql_fetch_array($myResult));
		echo"</tr></table>";
	}
else{
	noRowInfo($tableWidth);
  	}
//步骤7：
echo '</div>';
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= $myResult ==""?0:mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>