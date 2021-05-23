<style type="text/css">
.moveLtoR{ filter:revealTrans(Transition=6,Duration=0.3)};
.moveRtoL{ filter:revealTrans(Transition=7,Duration=0.3)};
/* 为 DIV 加阴影 */
.out {position:relative;background:#006633;margin:10px auto;width:400px;}
.in {background:#FFFFE6;border:1px solid #555;padding:10px 5px;position:relative;top:-5px;left:-5px;}
/* 为 图片 加阴影 */
.imgShadow {position:relative;     background:#bbb;      margin:10px auto;     width:220px; }
.imgContainer {position:relative;      top:-5px;     left:-5px;     background:#fff;      border:1px solid #555;     padding:0; }
.imgContainer img {     display:block; }
.glow1 { filter:glow(color=#FF0000,strengh=2)}
</style>
<?php
//电信-zxq 2012-08-01
/*
$DataIn.ch4_freight_declaration
$DataIn.ch1_shipmain
$DataPublic.freightdata
二合一已更新
*/
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=30;
$tableMenuS=550;
ChangeWtitle("$SubCompany 中港报关费列表");
$funFrom="ch_freight_declaration";
$nowWebPage=$funFrom."_read";
$TypeId=$TypeId==""?1:$TypeId;
$PayTypeId=$PayTypeId==""?0:$PayTypeId;
$TypeName=$TypeId==1?"研砼Invoice":"研砼提货单";
$Th_Col="选项|40|序号|30|货运公司|80|提单号码|80|件数|40|重量|60|体积|60|车型|80|运费<br>(RMB)|60|搬运费<br>(RMB)|60|报关费<br>(RMB)|60|续页费<br>(RMB)|60|无缝清关<br>(RMB)|60|仓储费<br>(RMB)|60|登记费<br>(RMB)|60|停车费<br>(RMB)|60|快递费<br>(RMB)|60|其他费<br>(RMB)|60|合计<br>(RMB)|60|备注|40|状态|40|操作|50|物流对账时间|80|$TypeName|120|出货日期|80";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,14,2,3,4,7,8";
$sumCols="8,9,10,11,12,13,14,15,16,17,18";			//求和列,需处理
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
	//月份
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
	                   FROM $DataIn.ch4_freight_declaration F
	                   LEFT JOIN $DataIn.ch4_freight_invoice I ON I.Mid=F.Id
	                   LEFT JOIN $DataIn.$TempTable M ON M.Id=I.chId	
	                   WHERE 1 $SearchRows GROUP BY DATE_FORMAT(M.Date,'%Y-%m') ORDER BY M.Date DESC";}
	else {
	         $TempTable="ch1_deliverymain";
	         $SearchRows.=" AND F.TypeId='$TypeId'";
			 $DateSql="SELECT M.DeliveryDate AS Date
	                   FROM $DataIn.ch4_freight_declaration F
	                   LEFT JOIN $DataIn.ch4_freight_invoice I ON I.Mid=F.Id
	                   LEFT JOIN $DataIn.$TempTable M ON M.Id=I.chId	
	                   WHERE 1 $SearchRows GROUP BY DATE_FORMAT(M.DeliveryDate,'%Y-%m') ORDER BY M.DeliveryDate DESC";}
	//echo $DateSql;
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
	$clientResult = mysql_query("SELECT F.CompanyId,D.Forshort 
	FROM $DataIn.ch4_freight_declaration F
	LEFT JOIN $DataIn.ch4_freight_invoice I ON I.Mid=F.Id
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
	//费用来源
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
echo"<div id='Jp' style='position:absolute; left:341px; top:229px; width:420px; height:50px;z-index:1;visibility:hidden;' tabIndex=0><input name='ActionTableId' type='hidden' id='ActionTableId'><input name='ActionRowId' type='hidden' id='ActionRowId'><input name='ObjId' type='hidden' id='ObjId'>
		<div class='out'>
			<div class='in' id='infoShow'>
			</div>
		</div>
	</div>";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
if($From!="slist"){
     if($TypeId==1){
            $mySql="SELECT M.Date,M.InvoiceNO,M.InvoiceFile,F.Id,F.Termini,F.ExpressNO,F.BoxQty,F.mcWG,F.Price,F.PayType,
                    F.Amount,F.depotCharge,F.Remark,F.Estate,F.Locks,F.Date AS fDate,F.Operator,D.Forshort,I.Mid,W.forwardWG,
		            L.Date AS LogDate,F.declarationCharge,F.checkCharge,F.carryCharge,F.xyCharge,F.wfqgCharge,
		            F.ccCharge,F.djCharge,F.stopcarCharge,F.expressCharge,F.otherCharge,F.CarType,F.Volume
                    FROM $DataIn.ch4_freight_declaration F
                    LEFT JOIN $DataIn.ch4_freight_invoice I ON I.Mid=F.Id
                    LEFT JOIN $DataIn.$TempTable M ON M.Id=I.chId	
		            LEFT JOIN $DataIn.ch3_forward W ON W.chId=I.chId and W.TypeId = '1'
                    LEFT JOIN $DataIn.freightdata D ON D.CompanyId=F.CompanyId 
		            LEFT JOIN $DataIn.ch4_logistics_date L ON L.Mid=F.Id
                    WHERE 1 $SearchRows ORDER BY F.Id DESC";
                  }
          else {

              $mySql="SELECT M.DeliveryDate AS Date,M.DeliveryNumber AS InvoiceNO,
	                F.Id,F.Termini,F.ExpressNO,F.BoxQty,F.mcWG,F.Price,F.depotCharge,F.PayType,
		            F.Amount,F.Remark,F.Estate,F.Locks,F.Date AS fDate,F.Operator,D.Forshort,I.Mid,W.forwardWG,
		            L.Date AS LogDate,F.declarationCharge,F.checkCharge,F.carryCharge,F.xyCharge,F.wfqgCharge,
		            F.ccCharge,F.djCharge,F.stopcarCharge,F.expressCharge,F.otherCharge,F.CarType,F.Volume
                    FROM $DataIn.ch4_freight_declaration F
                     LEFT JOIN $DataIn.ch4_freight_invoice I ON I.Mid=F.Id
                     LEFT JOIN $DataIn.$TempTable M ON M.Id=I.chId	
		             LEFT JOIN $DataIn.ch3_forward W ON W.chId=I.chId and W.TypeId != '1'
                     LEFT JOIN $DataIn.freightdata D ON D.CompanyId=F.CompanyId 
		             LEFT JOIN $DataIn.ch4_logistics_date L ON L.Mid=F.Id
                     WHERE 1 $SearchRows ORDER BY F.Id DESC";
              //echo $mySql;
                  }
         }
else{
          $mySql=" SELECT * FROM (SELECT M.Date AS ShipDate,M.InvoiceNO,M.InvoiceFile,
                        F.Id,F.Termini,F.ExpressNO,F.BoxQty,F.mcWG,F.Price,F.depotCharge,O.Date AS LogDate,
                        F.Amount, F.declarationCharge,F.checkCharge,F.carryCharge,F.xyCharge,
                        F.wfqgCharge, F.ccCharge,F.djCharge,F.stopcarCharge,F.expressCharge,F.otherCharge,
                        F.Remark,F.Estate,F.Locks,F.Date AS  fDate,F.Operator,D.Forshort,W.forwardWG,I.Mid,F.CarType,F.Volume
                     
                        FROM $DataIn.ch4_freight_declaration F
                        LEFT JOIN $DataIn.ch4_freight_invoice I ON I.Mid=F.Id
                        LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=I.chId	
                        LEFT JOIN $DataIn.ch3_forward W ON W.chId=I.chId
                        LEFT JOIN $DataIn.freightdata D ON D.CompanyId=F.CompanyId
                        LEFT JOIN $DataIn.ch4_logistics_date O ON O.Mid=F.Id 
                       WHERE 1 $SearchRows
                       UNION ALL
                       SELECT M.DeliveryDate AS ShipDate,M.DeliveryNumber AS InvoiceNO,'' as InvoiceFile,
                       F.Id,F.Termini,F.ExpressNO,F.BoxQty,F.Amount,
                       F.mcWG,F.Price,F.depotCharge,O.Date AS LogDate,F.declarationCharge,F.checkCharge,
                       F.carryCharge,F.xyCharge,
                       F.wfqgCharge, F.ccCharge,F.djCharge,F.stopcarCharge,F.expressCharge,F.otherCharge,
                       F.Remark,F.Estate,F.Locks,
                       F.Date AS  fDate,F.Operator,D.Forshort,W.forwardWG,I.Mid,F.CarType,F.Volume
                       FROM $DataIn.ch4_freight_declaration F
                       LEFT JOIN $DataIn.ch4_freight_invoice I ON I.Mid=F.Id
                       LEFT JOIN $DataIn.ch1_deliverymain M ON M.Id=I.chId	
                       LEFT JOIN $DataIn.ch3_forward W ON W.chId=I.chId
                       LEFT JOIN $DataIn.freightdata D ON D.CompanyId=F.CompanyId
                       LEFT JOIN $DataIn.ch4_logistics_date O ON O.Mid=F.Id 
                       WHERE 1 $SearchRows ) A ORDER BY Id DESC";
         }
//echo $mySql;

$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
    $tbDefalut=0;
	$midDefault="";
	$d=anmaIn("download/invoice/",$SinkOrder,$motherSTR);//invoice
	$d1=anmaIn("download/expressbill/",$SinkOrder,$motherSTR);
	$d2=anmaIn("download/DeliveryNumber/",$SinkOrder,$motherSTR);//提货单
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Mid=$myRow["Mid"];
		$Date=$myRow["Date"];
		$PayType=$myRow["PayType"];
		if($PayType==0) $PayType="自付";
		else if($PayType==1) $PayType="代付";
		$InvoiceNO=$myRow["InvoiceNO"];
		$InvoiceFile=$myRow["InvoiceFile"];
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
		$Id=$myRow["Id"];
		$Termini=$myRow["Termini"]==""?"&nbsp;":$myRow["Termini"];
		$ExpressNO=$myRow["ExpressNO"];
		$runningNum=$ExpressNO;
		$f1=anmaIn($ExpressNO.".jpg",$SinkOrder,$motherSTR);
		$ExpressNO=$ExpressNO==""?"&nbsp;":"<span onClick='OpenOrLoad(\"$d1\",\"$f1\")' style='CURSOR: pointer;color:#FF6633'>$ExpressNO</span>";
		$BoxQty=$myRow["BoxQty"];
		$mcWG=$myRow["mcWG"];
		$forwardWG=$myRow["forwardWG"]==""?"&nbsp;":$myRow["forwardWG"];
		$Price=$myRow["Price"];
		$CarType=$myRow["CarType"]==""?"&nbsp;":$myRow["CarType"];
		$Volume=$myRow["Volume"];
		//$Amount=sprintf("%.2f",$mcWG*$Price);
		$Amount=$myRow["Amount"];
		$Amount0=sprintf("%.2f",$mcWG*$Price);
	   // if (abs($Amount-$Amount0)>1) echo "称重:$mcWG*单价:$Price=$Amount0 != $Amount";

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
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Locks=$myRow["Locks"];
		$Estate=$myRow["Estate"];
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
		//=========================
		$TableId="ListTable$i";
		$LogDate=$myRow["LogDate"]==""?"&nbsp;":$myRow["LogDate"];
		$SetLogDate="onclick='updateJq($TableId,$i,$Id,\"$runningNum\")'";
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
				$Choose.="<input type='hidden' id='TempTypeId' name='TempTypeId' value='$TypeId' >";
		if($tbDefalut==0 && $midDefault==""){//首行
				//并行列
				echo"<table width='$tableWidth' border='0' cellspacing='0'  id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'><tr bgcolor='$theDefaultColor' onmousedown='ClickKeyCheck(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' 
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
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$mcWG</td>";//重量
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				/*echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$forwardWG</td>";//上海称重)
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;*/
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
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center' $SetLogDate>$LogDate</td>";//物流对账时间
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td width='' class='A0101'>";
				$midDefault=$Mid;
				}
			if($midDefault!="" && $midDefault==$Mid){//同属于一个主ID，则依然输出明细表格
				$m=47;
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
			else{
				//新行开始
				echo"</td></tr></table>";//结束上一个表格
				//并行列
				echo"<table width='$tableWidth' border='0' cellspacing='0'  id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'><tr bgcolor='$theDefaultColor' onmousedown='ClickKeyCheck(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' 
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
				/*echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$forwardWG</td>";//上海称重)
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;*/
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Volume</td>";//体积
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$CarType</td>";//车型
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Amount</td>";//运费
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
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center' $SetLogDate>$LogDate</td>";//物流对账时间
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
<script  src='../model/IE_FOX_MASK.js' type=text/javascript></script>
<script language="JavaScript" type="text/JavaScript">
function updateJq(TableId,RowId,Id,runningNum){//
	var InfoSTR="";
	var buttonSTR="";
	var theDiv=document.getElementById("Jp");
	var tempTableId=document.form1.ActionTableId.value;
	theDiv.style.top=event.clientY + document.body.scrollTop+'px';
	if(theDiv.style.visibility=="hidden" || TableId!=tempTableId){
		document.form1.ActionTableId.value=TableId;//表格名称
		InfoSTR="<input name='runningNum' type='text' id='runningNum' value='提单号:"+runningNum+"' size='12' class='TM0000' readonly>的物流对账日期:<input name='LogDate' type='text' id='LogDate' size='10' maxlength='10' class='INPUT0100' onFocus='WdatePicker()' readonly>&nbsp;<input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='更新' onclick='aiaxUpdate("+RowId+","+Id+")'>&nbsp;<input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='取消' onclick='CloseDiv()'>";
		infoShow.innerHTML=InfoSTR;
		theDiv.className="moveRtoL";
		if (isIe()) {  //只有IE才能用   add by zx 加入庶影   20110323  IE_FOX_MASK.js
			theDiv.filters.revealTrans.apply();//防止错误
			theDiv.filters.revealTrans.play(); //播放
		}
		else{
			theDiv.style.opacity=0.9;
		}
		theDiv.style.visibility = "";
		theDiv.style.display="";
		}
	}

function CloseDiv(){
	var theDiv=document.getElementById("Jp");
	theDiv.className="moveLtoR";
	if (isIe()) {  //只有IE才能用 add by zx 加入庶影   20110323  IE_FOX_MASK.js
		theDiv.filters.revealTrans.apply();
		//theDiv.style.visibility = "hidden";
		theDiv.filters.revealTrans.play();
	}
	theDiv.style.visibility = "hidden";
	theDiv.filters.revealTrans.play();
	infoShow.innerHTML="";
	}

function aiaxUpdate(RowId,Id){
    var TableId="ListTable"+RowId;
	var tempTableId=document.getElementById(TableId);
	var tempLogDate=document.form1.LogDate.value;
	var url="../admin/subprogram/updated_model_ch.php?Id="+Id+"&LogDate="+tempLogDate;
	var ajax=InitAjax();
　	ajax.open("GET",url,true);
	ajax.onreadystatechange =function(){
	　　if(ajax.readyState==4){//&& ajax.status ==200
	       tempTableId.rows[0].cells[17].innerHTML=tempLogDate;
			}
		}
　	ajax.send(null);
    CloseDiv();
	}
</script>
