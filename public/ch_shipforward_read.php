<?php
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=35;
$tableMenuS=550;
ChangeWtitle("$SubCompany Forward杂费列表");
$funFrom="ch_shipforward";
$nowWebPage=$funFrom."_read";
$TypeId=$TypeId==""?1:$TypeId;
$PayTypeId=$PayTypeId==""?0:$PayTypeId;
$TypeName=$TypeId==1?"研砼Invoice":"研砼提货单";
$Th_Col="选项|40|序号|35|Forward Invoice|80|Forward公司|80|入仓号|100|件数|35|研砼<br>称重|50|上海<br>称重|50|研砼<br>体积|50|上海<br>体积|50|研砼<br>体积重|50|上海<br>体积重|50|CFS费|60|THC费|60|文件费|60|手续费|60|ENS费|60|保险费|60|过桥费|60|电放费|60|提单费|60|其它费用|60|金额(HKD)|60|发票日期|70|费用结付|70|ETD/ETA|80|状态|30|备注|30|操作|50|$TypeName|110|出货日期|70";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$unColorCol=1;
$ActioToS="1,14,2,3,4,7,8";
$sumCols="5,6,7,8,9,10,11,12,13,14,15,16,17,18";		//求和列,需处理
//步骤3：
include "../model/subprogram/read_model_3.php";
$TempEstateSTR="EstateSTR".strval($Estate); $$TempEstateSTR="selected";
	$SearchRows.=$Estate==""?"":" AND F.Estate='$Estate'";
	$selectedStr="strType".$TypeId;
	if($TypeId==1){$SearchRows.=" AND M.Estate='0'";}
    $$selectedStr="selected";
	echo "<select name='TypeId' id='TypeId' onchange='RefreshPage(\"$nowWebPage\")'>";
	echo "<option value='1' $strType1>invoice</option>
	      <option value='2' $strType2>提货单</option>
		  </select>&nbsp;";
	if($TypeId==1){
	         $TempTable="ch1_shipmain";
	         $SearchRows.=" AND F.TypeId='$TypeId'";
			 $DateSql="SELECT M.Date 
			 FROM $DataIn.ch3_forward F 
	         LEFT JOIN $DataIn.ch3_forward_invoice I ON I.Mid=F.Id
	         LEFT JOIN $DataIn.$TempTable M ON M.Id=I.chId	
	         WHERE 1 $SearchRows GROUP BY DATE_FORMAT(M.Date,'%Y-%m') ORDER BY M.Date DESC";
			 }
	else    {
	         $TempTable="ch1_deliverymain";
	         $SearchRows.=" AND F.TypeId='$TypeId'";
			 $DateSql="SELECT M.DeliveryDate AS Date 
			 FROM $DataIn.ch3_forward F 
	         LEFT JOIN $DataIn.ch3_forward_invoice I ON I.Mid=F.Id
	         LEFT JOIN $DataIn.$TempTable M ON M.Id=I.chId	
	         WHERE 1 $SearchRows GROUP BY DATE_FORMAT(M.DeliveryDate,'%Y-%m') ORDER BY M.DeliveryDate DESC";
	        }
if($From!="slist"){
	$date_Result = mysql_query($DateSql,$link_id);
	if($dateRow = mysql_fetch_array($date_Result)) {
		echo"<select name='chooseDate' id='chooseDate' onchange='RefreshPage(\"$nowWebPage\")'>";
		do{
			$dateValue=date("Y-m",strtotime($dateRow["Date"]));
			$StartDate=$dateValue."-01";
			$EndDate=date("Y-m-t",strtotime($dateRow["Date"]));
			$chooseDate=$chooseDate==""?$dateValue:$chooseDate;
			if($chooseDate==$dateValue){
				echo"<option value='$dateValue' selected>$dateValue</option>";
				if($TypeId==1){
				   $SearchRows.=" and ((M.Date>'$StartDate' and M.Date<'$EndDate') OR M.Date='$StartDate' OR M.Date='$EndDate')";}
				else{
				   $SearchRows.=" and ((M.DeliveryDate>'$StartDate' and M.DeliveryDate<'$EndDate') OR M.DeliveryDate='$StartDate' OR M.DeliveryDate='$EndDate')";
				    }
				}
			else{
				echo"<option value='$dateValue'>$dateValue</option>";
				}
			}while($dateRow = mysql_fetch_array($date_Result));
		echo"</select>&nbsp;";
		}
	//客户
	$clientResult = mysql_query("SELECT F.CompanyId,D.Forshort FROM $DataIn.ch3_forward F
	        LEFT JOIN $DataIn.ch3_forward_invoice I ON I.Mid=F.Id
	        LEFT JOIN $DataIn.$TempTable M ON M.Id=I.chId
	        LEFT JOIN $DataPublic.freightdata D ON D.CompanyId=F.CompanyId 
	        WHERE 1 $SearchRows GROUP BY F.CompanyId ORDER BY F.CompanyId",$link_id);
	if($clientRow = mysql_fetch_array($clientResult)) {
		echo"<select name='CompanyId' id='CompanyId' onchange='RefreshPage(\"$nowWebPage\")'>";
		echo"<option value='' selected>全部</option>";
		do{
			$thisCompanyId=$clientRow["CompanyId"];
			$Forshort=$clientRow["Forshort"];
			if($CompanyId==$thisCompanyId){
				echo"<option value='$thisCompanyId' selected>$Forshort</option>";
				$SearchRows.=" and D.CompanyId='$thisCompanyId' ";
				}
			else{
				echo"<option value='$thisCompanyId'>$Forshort</option>";
				}
			}while ($clientRow = mysql_fetch_array($clientResult));
		echo"</select>&nbsp;";
		}
	//费用结付
	$PayStr="strPay".$PayTypeId;
	$$PayStr="selected";
	echo "<select name='PayTypeId' id='PayTypeId' onchange='RefreshPage(\"$nowWebPage\")'>";
	echo "<option value='0' $strPay0>自付</option>
	      <option value='1' $strPay1>代付</option>
		  </select>&nbsp;";
	$SearchRows.=" AND F.PayType='$PayTypeId'";
	//结付状态
	echo"<select name='Estate' id='Estate' onchange='ResetPage(this.name)'>
	<option value='' $EstateSTR>全  部</option>
	<option value='1' $EstateSTR1>未处理</option>
	<option value='2' $EstateSTR2>请款中</option>
	<option value='3' $EstateSTR3>请款通过</option>
	<option value='0' $EstateSTR0>已结付</option>
	</select>&nbsp;";
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
if($TypeId==1){
    $mySql="SELECT M.Number,M.InvoiceNO,M.Date AS ShipDate,M.InvoiceFile,
            F.Id,F.chId,F.HoldNO,F.ForwardNO,F.BoxQty,F.mcWG,F.forwardWG,F.Volume,F.HKVolume,F.VolumeKG,F.HKVolumeKG,F.Amount,
            F.InvoiceDate,F.PayType,F.ReturnReasons,F.ShipType,
            F.ETD,F.Remark,F.Estate,F.Locks,F.Date,F.Operator,D.Forshort,I.Mid,
            F.CFSCharge,F.THCCharge,F.WJCharge,F.SXCharge,F.ENSCharge,F.BXCharge,F.GQCharge,F.DFCharge,F.TDCharge,F.OtherCharge   
            FROM $DataIn.ch3_forward F
            LEFT JOIN $DataIn.ch3_forward_invoice I ON I.Mid=F.Id
	        LEFT JOIN $DataIn.$TempTable M ON M.Id=I.chId
            LEFT JOIN $DataPublic.freightdata D ON D.CompanyId=F.CompanyId 
            WHERE 1 $SearchRows ORDER BY F.Id DESC";
		}
    else{
	$mySql="SELECT M.DeliveryNumber AS InvoiceNO,M.DeliveryDate AS ShipDate,
            F.Id,F.chId,F.HoldNO,F.ForwardNO,F.BoxQty,F.mcWG,F.forwardWG,F.Volume,F.HKVolume,F.VolumeKG,F.HKVolumeKG,F.Amount,
            F.InvoiceDate,F.PayType,F.ReturnReasons,F.ShipType,
            F.ETD,F.Remark,F.Estate,F.Locks,F.Date,F.Operator,D.Forshort,I.Mid,
            F.CFSCharge,F.THCCharge,F.WJCharge,F.SXCharge,F.ENSCharge,F.BXCharge,F.GQCharge,F.DFCharge,F.TDCharge,F.OtherCharge  
            FROM $DataIn.ch3_forward F
            LEFT JOIN $DataIn.ch3_forward_invoice I ON I.Mid=F.Id
	        LEFT JOIN $DataIn.$TempTable M ON M.Id=I.chId
            LEFT JOIN $DataPublic.freightdata D ON D.CompanyId=F.CompanyId 
            WHERE 1 $SearchRows ORDER BY F.Id DESC";
	    }
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
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
		$ShipType=$myRow["ShipType"];
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
		//
		$CFSChargeTitle = $THCChargeTitle = $forwardWGTitle = $HKVolumeKGTitle = $HKVolumeTitle = $VolumeTitle="";
		if($ShipType==1){
			if($forwardWG>$HKVolumeKG){
			    if($forwardWG>$mcWG) $tempdiff = ($forwardWG/$mcWG * 100);
		        else  $tempdiff = ($mcWG/$forwardWG*100);
		        $forwardWGTitle = "class='redB'";
			}else{
				if($HKVolumeKG>$VolumeKG) $tempdiff = ($HKVolumeKG/$VolumeKG * 100);
		        else  $tempdiff = ($VolumeKG/$HKVolumeKG*100);
		        $HKVolumeKGTitle = "class='redB'";
			}

			if($tempdiff>=10){
			    $tempdiff = $tempdiff."%";
				$CFSChargeTitle = "class='redB'";
				$THCChargeTitle = "class='redB'";
			}else{
				$tempdiff = $tempdiff."%";
				$CFSChargeTitle = "class='yellowB'";
				$THCChargeTitle = "class='yellowB'";
			}

		}else if ($ShipType ==2){
			if($HKVolume>$Volume) {
			     $tempdiff = ($HKVolume/$Volume * 100);
			     $HKVolumeTitle = "class='redB'";
			   }
		    else  {
		          $tempdiff = ($Volume/$HKVolume*100);
		           $VolumeTitle = "class='redB'";
		          }
		    if($tempdiff>=10){
			    $tempdiff = $tempdiff."%";
				$CFSChargeTitle = "class='redB'";
			}else{
				$tempdiff = $tempdiff."%";
				$CFSChargeTitle = "class='yellowB'";
			}
		}



		$InvoiceDate=$myRow["InvoiceDate"];
		$PayType=$myRow["PayType"];
		if($PayType==0) $PayType="自付";
		else if($PayType==1) $PayType="代付";
		$ETD=$myRow["ETD"]==""?"&nbsp;":$myRow["ETD"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Remark=$myRow["Remark"]==""?"&nbsp;":"<img src='../images/remark.gif' title='$myRow[Remark]' width='18' height='18'>";
		$Locks=$myRow["Locks"];
		$Estate=$myRow["Estate"];

		$ReturnReasons=($myRow["ReturnReasons"]!="" && $Estate==1)?" title='" . $myRow["ReturnReasons"] . "'":"";

		switch($Estate){
			case "1":
				$Estate="<div align='center' class='redB' title='未处理'>×</div>";
				$LockRemark="";
				break;
			case "2":
				$Estate="<div align='center' class='yellowB' title='请款中...'>×.</div>";
				$LockRemark="记录已经请款，强制锁定操作！修改需退回。";
				$Locks=0;
				break;
			case "3":
				$Estate="<div align='center' class='yellowB' title='请款通过,等候结付!'>√.</div>";
				$LockRemark="记录已经请款通过，强制锁定操作！修改需退回。";
				$Locks=0;
				break;
			case "0":
				$Estate="<div align='center' class='greenB' title='已结付'>√</div>";
				$LockRemark="记录已经结付，强制锁定！修改需取消结付。";
				$Locks=0;
				break;
			}
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
		$Choose.="<input type='hidden' id='TempTypeId' name='TempTypeId' value='$TypeId' >";
		if($tbDefalut==0 && $midDefault==""){//首行
				//并行列
				echo"<table width='$tableWidth' border='0' cellspacing='0'  id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'><tr bgcolor='$theDefaultColor' onmousedown='ClickKeyCheck(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' 
					onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
					onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
				echo"<td scope='col' class='A0111' width='$Field[$m]' align='center'>$Choose</td>";//选项
				$unitWidth=$tableWidth-$Field[$m];
				$m=$m+2;
				$Ti=$ReturnReasons==""?$i:"<div class='redB'>退回</div>";
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center' $ReturnReasons>$Ti</td>";//序号
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$ForwardNO</td>";//Forward Invoice
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Forshort</td>";//Froward公司
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$HoldNO</td>";//入仓号
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$BoxQty</td>";//件数
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$mcWG</td>";//研砼称重
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'><span $forwardWGTitle>$forwardWG</span></td>";
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'><span $VolumeTitle>$Volume</span></td>";
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'><span $HKVolumeTitle>$HKVolume</span></td>";
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$VolumeKG</td>";
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'><span $HKVolumeKGTitle>$HKVolumeKG</span></td>";
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'><span $CFSChargeTitle>$CFSCharge</span></td>";
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'><span $THCChargeTitle>$THCCharge</span></td>";
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
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$InvoiceDate</td>";//发票日期
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$PayType</td>";//费用结付
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
				$m=59;
				echo"<table width='100%' border='0' cellspacing='0'  style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
				echo"<tr>";
				$unitFirst=$Field[$m]-1;
				//echo"<td class='A0001' width='$unitFirst' height='20' align='center'>$j</td>";			//序号
				//$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$InvoiceFile</td>";//InvoiceNO
				$m=$m+2;
			    echo"<td width=''align='center'>$ShipDate</td>";//出货日期
				echo"</tr>
				</table>";
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
				$Ti=$ReturnReasons==""?$i:"<div class='redB'>退回</div>";
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center' $ReturnBgColor $ReturnReasons>$Ti</td>";//序号
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$ForwardNO</td>";//Forward Invoice
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Forshort</td>";//Froward公司
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$HoldNO</td>";//入仓号
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$BoxQty</td>";//件数
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$mcWG</td>";//研砼称重
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'><span $forwardWGTitle>$forwardWG</span></td>";
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'><span $VolumeTitle>$Volume</span></td>";
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'><span $HKVolumeTitle>$HKVolume</span></td>";
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$VolumeKG</td>";
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'><span $HKVolumeKGTitle>$HKVolumeKG</span></td>";
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;

				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'><span $CFSChargeTitle>$CFSCharge</span></td>";
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'><span $THCChargeTitle>$THCCharge</span></td>";
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
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$InvoiceDate</td>";//发票日期
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$PayType</td>";//费用结付
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
				echo"<td class='A0001' width='$Field[$m]' align='center'>$InvoiceFile</td>";//InvoiceNO
				$m=$m+2;
			    echo"<td  width='' align='center'>$ShipDate</td>";//出货日期
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
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>