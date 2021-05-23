<?php
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=32;
$tableMenuS=600;
$sumCols="5,6,7,8";		//求和列
$From=$From==""?"m":$From;
ChangeWtitle("$SubCompany Forward杂费待审核列表");
$funFrom="ch_shipforward";
$TypeId=$TypeId==""?1:$TypeId;
$PayTypeId=$PayTypeId==""?2:$PayTypeId;
$TypeName=$TypeId==1?"研砼Invoice":"研砼提货单";
$Th_Col="选项|40|序号|35|Forward公司|80|入仓号|100|Forward Invoice|80|件数|35|研砼<br>称重|50|上海<br>称重|50|研砼<br>体积|50|上海<br>体积|50|研砼<br>体积重|50|上海<br>体积重|50|CFS费|60|THC费|60|文件费|60|手续费|60|ENS费|60|保险费|60|过桥费|60|电放费|60|提单费|60|其它费用|60|金额(HKD)|60|费用结付|70|发票日期|70|ETD/ETA|80|状态|30|备注|30|操作|50|$TypeName|110|出货日期|70";
//必选，分页默认值
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$ActioToS="17,15";
//步骤3：
$nowWebPage=$funFrom."_m";
include "../model/subprogram/read_model_3.php";
if($From!="slist"){

    $selectedStr="strType".$TypeId;
    $$selectedStr="selected";
	echo "<select name='TypeId' id='TypeId' onchange='RefreshPage(\"$nowWebPage\")'>";
	echo "<option value='1' $strType1>invoice</option>
	      <option value='2' $strType2>提货单</option>
		  </select>&nbsp;";
	   $pResult = mysql_query("SELECT P.CompanyId,P.Forshort  
		FROM $DataIn.ch3_forward F  
		LEFT JOIN $DataPublic.freightdata P ON P.CompanyId=F.CompanyId WHERE 1 AND F.Estate='2' GROUP BY P.CompanyId ORDER BY P.CompanyId",$link_id);
		if($pRow = mysql_fetch_array($pResult)){
			echo"<select name='CompanyId' id='CompanyId' onchange='document.form1.submit()'>";
			echo"<option value='' selected>全部</option>";
			do{
				$Forshort=$pRow["Forshort"];
				$thisCompanyId=$pRow["CompanyId"];
				if($CompanyId==$thisCompanyId){
					echo"<option value='$thisCompanyId' selected>$Forshort </option>";
					$SearchRows.=" and F.CompanyId='$thisCompanyId'";
					}
				else{
					echo"<option value='$thisCompanyId'>$Forshort</option>";
					}
				}while($pRow = mysql_fetch_array($pResult));
			echo"</select>&nbsp;";
			}
		}

		//费用结付
	$PayStr="strPay".$PayTypeId;
	$$PayStr="selected";
	echo "<select name='PayTypeId' id='PayTypeId' onchange='RefreshPage(\"$nowWebPage\")'>";
	echo "<option value='2' $strPay2>全部</option>
		  <option value='0' $strPay0>自付</option>
	      <option value='1' $strPay1>代付</option>
		  </select>&nbsp;";
	if($PayTypeId==0 || $PayTypeId==1)
		$SearchRows.=" AND F.PayType='$PayTypeId'";

echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>";
//步骤4：
$TitlePre="<br>&nbsp;&nbsp;退回原因:<input type=\"text\" id=\"ReturnReasons\" name=\"ReturnReasons\" style=\"width:600\"><p>";
include "../model/subprogram/read_model_5.php";

$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
if($TypeId==1){
    $TempTable="ch1_shipmain";
	$SearchRows.=" AND F.TypeId='$TypeId'";
    $mySql="SELECT M.Number,M.InvoiceNO,M.Date AS ShipDate,M.InvoiceFile,
    F.Id,F.chId,F.HoldNO,F.ForwardNO,F.BoxQty,F.mcWG,F.forwardWG,F.Volume,F.HKVolume,F.VolumeKG,F.HKVolumeKG,F.Amount,
    F.InvoiceDate,F.PayType,F.ETD,F.Remark,F.Estate,F.Locks,F.Date,F.Operator,F.ShipType,D.Forshort,I.Mid,
    F.CFSCharge,F.THCCharge,F.WJCharge,F.SXCharge,F.ENSCharge,F.BXCharge,F.GQCharge,F.DFCharge,F.TDCharge,F.OtherCharge 
    FROM $DataIn.ch3_forward F
    LEFT JOIN $DataIn.ch3_forward_invoice I ON I.Mid=F.Id
    LEFT JOIN $DataIn.$TempTable M ON M.Id=I.chId
    LEFT JOIN $DataPublic.freightdata D ON D.CompanyId=F.CompanyId 
    WHERE 1 $SearchRows AND F.Estate='2' AND M.Number>0 ORDER BY F.Id DESC";
    }
else{
    $TempTable="ch1_deliverymain";
	$SearchRows.=" AND F.TypeId='$TypeId'";
    $mySql="SELECT M.DeliveryNumber AS InvoiceNO,M.DeliveryDate AS ShipDate,
    F.Id,F.chId,F.HoldNO,F.ForwardNO,F.BoxQty,F.mcWG,F.forwardWG,F.Volume,F.HKVolume,F.VolumeKG,F.HKVolumeKG,F.Amount,
    F.InvoiceDate,F.PayType,F.ETD,F.Remark,F.Estate,F.Locks,F.Date,F.Operator,F.ShipType,D.Forshort,I.Mid,
    F.CFSCharge,F.THCCharge,F.WJCharge,F.SXCharge,F.ENSCharge,F.BXCharge,F.GQCharge,F.DFCharge,F.TDCharge,F.OtherCharge 
    FROM $DataIn.ch3_forward F
    LEFT JOIN $DataIn.ch3_forward_invoice I ON I.Mid=F.Id
    LEFT JOIN $DataIn.$TempTable M ON M.Id=I.chId
    LEFT JOIN $DataPublic.freightdata D ON D.CompanyId=F.CompanyId 
    WHERE 1 $SearchRows AND F.Estate='2' ORDER BY F.Id DESC";
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
		$PayType=$myRow["PayType"];
		if($PayType==0) $PayType="自付";
		else if($PayType==1) $PayType="代付";
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
		$ShipType=$myRow["ShipType"];
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
		$ETD=$myRow["ETD"]==""?"&nbsp;":$myRow["ETD"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Remark=$myRow["Remark"]==""?"&nbsp;":"<img src='../images/remark.gif' title='$myRow[Remark]' width='18' height='18'>";
		$Locks=$myRow["Locks"];
		$Estate=$myRow["Estate"];
		switch($Estate){
			case "1":
				$Estate="<div align='center' class='redB' title='未处理'>×</div>";
				break;
			case "2":
				$Estate="<div align='center' class='yellowB' title='请款中...'>×.</div>";
				break;
			case "3":
				$Estate="<div align='center' class='yellowB' title='请款通过,等候结付!'>√.</div>";
				break;
			case "0":
				$Estate="<div align='center' class='greenB' title='已结付'>√</div>";
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
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$PayType</td>";//费用结付
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$InvoiceDate</td>";//发票日期
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
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$PayType</td>";//费用结付
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$InvoiceDate</td>";//发票日期
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
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>