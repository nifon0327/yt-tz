<?php
/*
二合一已更新
*/
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=30;
$tableMenuS=600;
$sumCols="8,9,10,11,12,13,14,15,16,17,18";			//求和列
$From=$From==""?"m":$From;
ChangeWtitle("$SubCompany 中港报关费用待审核列表");
$funFrom="ch_freight_declaration";
$TypeId=$TypeId==""?1:$TypeId;
$PayTypeId=$PayTypeId==""?2:$PayTypeId;
$TypeName=$TypeId==1?"研砼Invoice":"研砼提货单";
$Th_Col="选项|40|序号|30|货运公司|80|提单号码|80|件数|40|重量|60|体积|60|车型|80|运费<br>(RMB)|60|搬运费<br>(RMB)|60|报关费<br>(RMB)|60|续页费<br>(RMB)|60|无缝清关<br>(RMB)|60|仓储费<br>(RMB)|60|登记费<br>(RMB)|60|停车费<br>(RMB)|60|快递费<br>(RMB)|60|其他费<br>(RMB)|60|合计<br>(RMB)|60|备注|40|状态|40|操作|50|物流对账日期|80|$TypeName|120|出货日期|80";
//必选，分页默认值
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$ActioToS="17,15";
$nowWebPage=$funFrom."_m";
include "../model/subprogram/read_model_3.php";
if($From!="slist"){
    $SearchRows.="";
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
		}
	else {
	         $TempTable="ch1_deliverymain";
	         $SearchRows.=" AND F.TypeId='$TypeId'";
		}

    //货运公司
	$clientResult = mysql_query("SELECT F.CompanyId,D.Forshort 
	FROM $DataIn.ch4_freight_declaration F
	LEFT JOIN $DataIn.ch4_freight_invoice I ON I.Mid=F.Id
	LEFT JOIN $DataIn.$TempTable M ON M.Id=I.chId	
	LEFT JOIN $DataPublic.freightdata D ON D.CompanyId=F.CompanyId 
	WHERE 1 and F.Estate='2' $SearchRows GROUP BY F.CompanyId ORDER BY F.CompanyId",$link_id);
	if($clientRow = mysql_fetch_array($clientResult)) {
		echo"<select name='CompanyId' id='CompanyId' onchange='RefreshPage(\"$nowWebPage\")'>";
		do{
			$thisCompanyId=$clientRow["CompanyId"];
			$Forshort=$clientRow["Forshort"];
			$CompanyId=$CompanyId==""?$thisCompanyId:$CompanyId;
			if($CompanyId==$thisCompanyId){
				echo"<option value='$thisCompanyId' selected>$Forshort</option>";
				$SearchRows.=" and F.CompanyId='$thisCompanyId' ";
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
	echo "<option value='2' $strPay2>全部</option>
		<option value='0' $strPay0>自付</option>
	      <option value='1' $strPay1>代付</option>
		  </select>&nbsp;";
	if($PayTypeId==0 || $PayTypeId==1)
		$SearchRows.=" AND F.PayType='$PayTypeId'";
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>";
//步骤4：
include "../model/subprogram/read_model_5.php";
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
if($TypeId==1){
$mySql="SELECT 
	M.Date,M.InvoiceNO,M.InvoiceFile,
	F.Id,F.Termini,F.ExpressNO,F.BoxQty,F.mcWG,F.Price,F.Amount,L.Date AS LogDate,F.declarationCharge,F.checkCharge,F.PayType,
	F.depotCharge,F.carryCharge,F.xyCharge,F.wfqgCharge,
    F.ccCharge,F.djCharge,F.stopcarCharge,F.expressCharge,F.otherCharge,
    F.Remark,F.Estate,F.Locks,F.Date AS fDate,F.Operator,D.Forshort,I.Mid,W.forwardWG,F.CarType,F.Volume
	FROM $DataIn.ch4_freight_declaration F
	LEFT JOIN $DataIn.ch4_freight_invoice I ON I.Mid=F.Id
    LEFT JOIN $DataIn.$TempTable M ON M.Id=I.chId	
    LEFT JOIN $DataIn.ch3_forward W ON W.chId=I.chId
	LEFT JOIN $DataPublic.freightdata D ON D.CompanyId=F.CompanyId 
	LEFT JOIN $DataIn.ch4_logistics_date L ON L.Mid=F.Id
	WHERE 1 AND F.Estate='2' $SearchRows ORDER BY F.Id DESC";
	}
else{
   $mySql="SELECT 
	M.DeliveryDate AS Date,M.DeliveryNumber AS InvoiceNO,
	F.Id,F.Termini,F.ExpressNO,F.BoxQty,F.mcWG,F.Price,F.Amount,L.Date AS LogDate,F.declarationCharge,F.checkCharge,F.PayType,
	F.depotCharge,F.carryCharge,F.xyCharge,F.wfqgCharge,
    F.ccCharge,F.djCharge,F.stopcarCharge,F.expressCharge,F.otherCharge,
    F.Remark,F.Estate,F.Locks,F.Date AS fDate,F.Operator,D.Forshort,I.Mid,W.forwardWG,F.CarType,F.Volume
	FROM $DataIn.ch4_freight_declaration F
	LEFT JOIN $DataIn.ch4_freight_invoice I ON I.Mid=F.Id
    LEFT JOIN $DataIn.$TempTable M ON M.Id=I.chId	
    LEFT JOIN $DataIn.ch3_forward W ON W.chId=I.chId
	LEFT JOIN $DataPublic.freightdata D ON D.CompanyId=F.CompanyId 
	LEFT JOIN $DataIn.ch4_logistics_date L ON L.Mid=F.Id
	WHERE 1 AND F.Estate='2' $SearchRows ORDER BY F.Id DESC";
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
		$Date=$myRow["Date"];
		$InvoiceNO=$myRow["InvoiceNO"];
		$InvoiceFile=$myRow["InvoiceFile"];
		//加密参数
		//echo $InvoiceNO.".pdf";
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
		$PayType=$myRow["PayType"];
		if($PayType==0) $PayType="自付";
		else if($PayType==1) $PayType="代付";
		$Forshort=$myRow["Forshort"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Id=$myRow["Id"];
		$Termini=$myRow["Termini"]==""?"&nbsp;":$myRow["Termini"];
		$ExpressNO=$myRow["ExpressNO"];
		$CarType=$myRow["CarType"]==""?"&nbsp;":$myRow["CarType"];
		$Volume=$myRow["Volume"];

		$f1=anmaIn($ExpressNO.".jpg",$SinkOrder,$motherSTR);
		$ExpressNO=$ExpressNO==""?"&nbsp;":"<span onClick='OpenOrLoad(\"$d1\",\"$f1\")' style='CURSOR: pointer;color:#FF6633'>$ExpressNO</span>";

		$BoxQty=$myRow["BoxQty"];
		$mcWG=$myRow["mcWG"];$forwardWG=$myRow["forwardWG"]==""?"&nbsp;":$myRow["forwardWG"];
		$Price=$myRow["Price"];
		//$Amount=sprintf("%.2f",$mcWG*$Price);

		$Amount=$myRow["Amount"];
		$Amount0=sprintf("%.2f",$mcWG*$Price);
	    //if (abs($Amount-$Amount0)>1) echo "称重:$mcWG*单价:$Price=$Amount0 != $Amount";

		$totalCharge = 0.00;
		//$depotCharge=$myRow["depotCharge"];
		$declarationCharge=$myRow["declarationCharge"];
		//$checkCharge=$myRow["checkCharge"];
		$carryCharge=$myRow["carryCharge"];
		$xyCharge=$myRow["xyCharge"];
		$wfqgCharge=$myRow["wfqgCharge"];
		$ccCharge=$myRow["ccCharge"];
		$djCharge=$myRow["djCharge"];
		$stopcarCharge=$myRow["stopcarCharge"];
		$expressCharge=$myRow["expressCharge"];
		$otherCharge=$myRow["otherCharge"];
		$totalCharge = $Amount+$declarationCharge+$carryCharge+$xyCharge+$wfqgCharge+$ccCharge+$djCharge+$stopcarCharge+$expressCharge+$otherCharge;
		$totalCharge = sprintf("%.2f", $totalCharge);

		$Remark=$myRow["Remark"]==""?"&nbsp;":"<img src='../images/remark.gif' title='$myrow[Remark]' width='18' height='18'>";
		$Locks=$myRow["Locks"];
		$Estate=$myRow["Estate"];
		$LogDate=substr($myRow["LogDate"],0,7)==""?"&nbsp;":substr($myRow["LogDate"],0,7);
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
		$Remark=$myRow["Remark"]==""?"&nbsp;":"<img src='../images/remark.gif' title='$myRow[Remark]' width='18' height='18'>";
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
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Forshort</td>";//货运公司
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;

				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$ExpressNO</td>";//提单号码
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$BoxQty</td>";//件数
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$mcWG</td>";//研砼称重
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Volume</td>";//体积
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$CarType</td>";//车型
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Amount</td>";//运费<br>(RMB)
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$carryCharge</td>"; //搬运费
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$declarationCharge</td>";//报关费
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$xyCharge</td>"; //续页费
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$wfqgCharge</td>";//无缝清关
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$ccCharge</td>";//仓储费
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$djCharge</td>";//登记费
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$stopcarCharge</td>";//停车费
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$expressCharge</td>";//快递费
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$otherCharge</td>";//其它费用
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$totalCharge</td>";//总计费用
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;


				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Remark</td>";//备注
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Estate</td>";//状态
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Operator</td>";//操作
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$LogDate</td>";//对账日期
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td width='' class='A0101'>";
				$midDefault=$Mid;
				}
			if($midDefault!="" && $midDefault==$Mid){//同属于一个主ID，则依然输出明细表格
				$m=49;
				echo"<table width='100%' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'>";
				echo"<tr>";
				$unitFirst=$Field[$m]-1;

				echo"<td class='A0001' width='$Field[$m]'align='center'>$InvoiceFile</td>";//InvoiceNO
				$m=$m+2;
			    echo"<td class='A0001' width='$Field[$m]'align='center'>$Date</td>";//出货日期
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
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Forshort</td>";//货运公司
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;

				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$ExpressNO</td>";//提单号码
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$BoxQty</td>";//件数
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$mcWG</td>";//研砼称重
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Volume</td>";//体积
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$CarType</td>";//车型
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Amount</td>";//运费<br>(RMB)
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$carryCharge</td>"; //搬运费
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$declarationCharge</td>";//报关费
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$xyCharge</td>"; //续页费
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$wfqgCharge</td>";//无缝清关
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$ccCharge</td>";//仓储费
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$djCharge</td>";//登记费
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$stopcarCharge</td>";//停车费
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$expressCharge</td>";//快递费
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$otherCharge</td>";//其它费用
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$totalCharge</td>";//总计费用
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;

				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Remark</td>";//备注
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Estate</td>";//状态
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Operator</td>";//操作
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$LogDate</td>";//对账日期
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
			    echo"<td class='A0001' width='$Field[$m]'align='center'>$Date</td>";//出货日期
				echo"</tr></table>";
				$i++;
				$j++;
				}
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