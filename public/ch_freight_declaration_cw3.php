<?php
//电信-zxq 2012-08-01
/*
$DataIn.ch4_freight
$DataIn.ch1_shipmain
$DataPublic.freightdata
*/
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
			 $DateSql="SELECT F.Date 
	         FROM $DataIn.ch4_freight_declaration F 
	         LEFT JOIN $DataIn.ch4_freight_invoice I ON I.Mid=F.Id
	         LEFT JOIN $DataIn.$TempTable M ON M.Id=I.chId	
	         WHERE 1 $SearchRows group by DATE_FORMAT(F.Date,'%Y-%m') order by F.Date DESC";}
	else {
	         $TempTable="ch1_deliverymain";
	         $SearchRows.=" AND F.TypeId='$TypeId'";
			 $DateSql="SELECT M.DeliveryDate AS Date
	         FROM $DataIn.ch4_freight_declaration F 
	         LEFT JOIN $DataIn.ch4_freight_invoice I ON I.Mid=F.Id
	         LEFT JOIN $DataIn.$TempTable M ON M.Id=I.chId	
	         WHERE 1 $SearchRows group by DATE_FORMAT(M.DeliveryDate,'%Y-%m') order by M.DeliveryDate DESC";
			}

	$monthResult = mysql_query($DateSql,$link_id);
	if ($dateRow = mysql_fetch_array($monthResult)) {
		echo"<select name='chooseDate' id='chooseDate' onchange='document.form1.submit()'>";
		do{
			$dateValue=date("Y-m",strtotime($dateRow["Date"]));
			$StartDate=$dateValue."-01";
			$EndDate=date("Y-m-t",strtotime($dateRow["Date"]));
			$chooseDate=$chooseDate==""?$dateValue:$chooseDate;
			if($chooseDate==$dateValue){
				echo"<option value='$dateValue' selected>$dateValue</option>";
				if($TypeId==1){
				     $SearchRows.=" and DATE_FORMAT(F.Date,'%Y-%m')='$dateValue'";
				    }
				else{
				     $SearchRows.=" and DATE_FORMAT(M.DeliveryDate,'%Y-%m')='$dateValue'";
				    }
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
	$clientResult = mysql_query("SELECT F.CompanyId,D.Forshort 
	FROM $DataIn.ch4_freight_declaration F
	LEFT JOIN $DataIn.ch4_freight_invoice I ON I.Mid=F.Id
	LEFT JOIN $DataIn.$TempTable M ON M.Id=I.chId	
	LEFT JOIN $DataPublic.freightdata D ON D.CompanyId=F.CompanyId 
	WHERE 1 $SearchRows GROUP BY F.CompanyId ORDER BY F.CompanyId",$link_id);
	if($clientRow = mysql_fetch_array($clientResult)) {
		echo"<select name='CompanyId' id='CompanyId' onchange='RefreshPage(\"$nowWebPage\")'>";
		//echo "<option value='' selected>货运公司</option>";
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
	//费用来源
	$PayStr="strPay".$PayType;
	$$PayStr="selected";
	echo "<select name='PayType' id='PayType' onchange='RefreshPage(\"$nowWebPage\")'>";
	echo "<option value='k' $strPayk>全部</option>
             <option value='0' $strPay0>自付</option>
	      <option value='1' $strPay1>代付</option>
		  </select>&nbsp;";
		  if ($PayType!="k")
		  {
			    $SearchRows.=" AND F.PayType='$PayType'";
		  }
	}
else{
	echo"<select name='Estate' id='Estate' onchange='ResetPage(this.name)'>
	<option value='3' $EstateSTR3>未结付</option>
	</select>&nbsp;";
	}

include "../model/selectbank1.php";
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>";
//  echo "$CencalSstr";

//非必选,过滤条件
include "../model/subprogram/read_model_5.php";
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
if($From!="slist"){
  if($TypeId==1){
    $mySql="SELECT M.Date AS ShipDate,M.InvoiceNO,M.InvoiceFile,
       F.Id,F.Termini,F.ExpressNO,F.BoxQty,F.mcWG,F.Price,F.Amount,
       F.depotCharge,O.Date AS LogDate,F.declarationCharge,F.checkCharge,F.Remark,F.Estate,F.Locks,F.Date AS        fDate,F.Operator,D.Forshort,W.forwardWG,I.Mid,F.carryCharge,F.xyCharge,F.wfqgCharge,
		            F.ccCharge,F.djCharge,F.stopcarCharge,F.expressCharge,F.otherCharge
       FROM $DataIn.ch4_freight_declaration F
       LEFT JOIN $DataIn.ch4_freight_invoice I ON I.Mid=F.Id
       LEFT JOIN $DataIn.$TempTable M ON M.Id=I.chId	
       LEFT JOIN $DataIn.ch3_forward W ON W.chId=I.chId
       LEFT JOIN $DataPublic.freightdata D ON D.CompanyId=F.CompanyId
       LEFT JOIN $DataIn.ch4_logistics_date O ON O.Mid=F.Id 
       WHERE 1 $SearchRows ORDER BY F.Id DESC";
	   }
  else{
      $mySql="SELECT M.DeliveryDate AS ShipDate,M.DeliveryNumber AS InvoiceNO,
       F.Id,F.Termini,F.ExpressNO,F.BoxQty,F.mcWG,F.Price,F.Amount,
       F.depotCharge,O.Date AS LogDate,F.declarationCharge,F.checkCharge,F.Remark,F.Estate,F.Locks,F.Date AS        fDate,F.Operator,D.Forshort,W.forwardWG,I.Mid,F.carryCharge,F.xyCharge,F.wfqgCharge,
		            F.ccCharge,F.djCharge,F.stopcarCharge,F.expressCharge,F.otherCharge
       FROM $DataIn.ch4_freight_declaration F
       LEFT JOIN $DataIn.ch4_freight_invoice I ON I.Mid=F.Id
       LEFT JOIN $DataIn.$TempTable M ON M.Id=I.chId	
       LEFT JOIN $DataIn.ch3_forward W ON W.chId=I.chId
       LEFT JOIN $DataPublic.freightdata D ON D.CompanyId=F.CompanyId
       LEFT JOIN $DataIn.ch4_logistics_date O ON O.Mid=F.Id 
       WHERE 1 $SearchRows ORDER BY F.Id DESC";
       }
}
else{
    $mySql="SELECT M.Date AS ShipDate,M.InvoiceNO,M.InvoiceFile,
       F.Id,F.Termini,F.ExpressNO,F.BoxQty,F.mcWG,F.Price,F.Amount,
       F.depotCharge,O.Date AS LogDate,F.declarationCharge,F.checkCharge,F.Remark,F.Estate,F.Locks,F.Date AS        fDate,F.Operator,D.Forshort,W.forwardWG,I.Mid,F.carryCharge,F.xyCharge,F.wfqgCharge,
		            F.ccCharge,F.djCharge,F.stopcarCharge,F.expressCharge,F.otherCharge
       FROM $DataIn.ch4_freight_declaration F
       LEFT JOIN $DataIn.ch4_freight_invoice I ON I.Mid=F.Id
       LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=I.chId	
       LEFT JOIN $DataIn.ch3_forward W ON W.chId=I.chId
       LEFT JOIN $DataPublic.freightdata D ON D.CompanyId=F.CompanyId
       LEFT JOIN $DataIn.ch4_logistics_date O ON O.Mid=F.Id 
       WHERE 1 $SearchRows AND I.TypeId=1
       UNION ALL
       SELECT M.DeliveryDate AS ShipDate,M.DeliveryNumber AS InvoiceNO,'' as InvoiceFile,
       F.Id,F.Termini,F.ExpressNO,F.BoxQty,F.mcWG,F.Price,F.Amount,
       F.depotCharge,O.Date AS LogDate,F.declarationCharge,F.checkCharge,F.Remark,F.Estate,F.Locks,F.Date AS        fDate,F.Operator,D.Forshort,W.forwardWG,I.Mid,F.carryCharge,F.xyCharge,F.wfqgCharge,
		            F.ccCharge,F.djCharge,F.stopcarCharge,F.expressCharge,F.otherCharge
       FROM $DataIn.ch4_freight_declaration F
       LEFT JOIN $DataIn.ch4_freight_invoice I ON I.Mid=F.Id
       LEFT JOIN $DataIn.ch1_deliverymain M ON M.Id=I.chId	
       LEFT JOIN $DataIn.ch3_forward W ON W.chId=I.chId
       LEFT JOIN $DataPublic.freightdata D ON D.CompanyId=F.CompanyId
       LEFT JOIN $DataIn.ch4_logistics_date O ON O.Mid=F.Id 
       WHERE 1 $SearchRows AND I.TypeId=2";
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
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Id=$myRow["Id"];
		$Termini=$myRow["Termini"]==""?"&nbsp;":$myRow["Termini"];
		$ExpressNO=$myRow["ExpressNO"];
		$f1=anmaIn($ExpressNO.".jpg",$SinkOrder,$motherSTR);
		$ExpressNO=$ExpressNO==""?"&nbsp;":"<span onClick='OpenOrLoad(\"$d1\",\"$f1\")' style='CURSOR: pointer;color:#FF6633'>$ExpressNO</span>";

		$BoxQty=$myRow["BoxQty"];
		$mcWG=$myRow["mcWG"];
		$forwardWG=$myRow["forwardWG"]==""?"&nbsp;":$myRow["forwardWG"];
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
		$Locks=1;
		$LogDate=substr($myRow["LogDate"],0,7)==""?"&nbsp;":substr($myRow["LogDate"],0,7);
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
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Termini</td>";//目的地
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
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$forwardWG</td>";//上海称重)
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Price</td>";//单价
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Amount</td>";//运费<br>
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
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Estate</td>";//状态
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Remark</td>";//备注
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
				$i++;
				}
			if($midDefault!="" && $midDefault==$Mid){//同属于一个主ID，则依然输出明细表格
			   echo $m;
				$m=49;
				echo"<table width='100%' border='0' cellspacing='0'  style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
				echo"<tr>";
				$unitFirst=$Field[$m]-1;
				echo"<td class='A0001' width='$Field[$m]'align='center'>$InvoiceFile</td>";//InvoiceNO
				$m=$m+2;
			    echo"<td class='A0001' width='$Field[$m]'align='center'>$ShipDate</td>";//出货日期
				echo"</tr></table>";
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
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Termini</td>";//目的地
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
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$forwardWG</td>";//上海称重)
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Price</td>";//单价
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Amount</td>";//运费<br>
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
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Estate</td>";//状态
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Remark</td>";//备注
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
				echo"<table width='100%' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'>";
				echo"<tr>";
				$unitFirst=$Field[$m]-1;
				echo"<td class='A0001' width='$Field[$m]'align='center'>$InvoiceFile</td>";//InvoiceNO
				$m=$m+2;
			    echo"<td class='A0001' width='$Field[$m]'align='center'>$ShipDate</td>";//出货日期
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