<?php
//电信-zxq 2012-08-01
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;										//每页默认记录数量
$mainData="$DataIn.cw10_samplemail";
$sheetData="$DataIn.ch10_samplemail";
//步骤1：
include "../model/subprogram/cw0_model1.php";
$i=1;
$j=($Page-1)*$Page_Size+1;
$mRow=1;
//$SearchRows="and S.Estate='$Estate'";
List_Title($Th_Col,"1",1);
$mySql="SELECT 
	M.PayDate,M.PayAmount,M.Payee,M.Receipt,M.CheckSheet,M.Remark AS PayRemark,M.Locks AS MLocks,
	S.Id,S.Mid,S.DataType,S.LinkMan,S.ExpressNO,S.Pieces,S.Weight,S.Qty,S.Price,S.Amount,
S.PayType,S.ServiceType,S.Description,S.Remark,S.Schedule,S.SendDate,S.ReceiveDate,S.Estate,S.Locks,S.Operator,
	P.Name AS HandledBy,D.Forshort,C.Forshort AS Client,A.Termini ,B.Title,M.cSign
 	FROM $mainData M
	LEFT JOIN $sheetData S ON S.Mid=M.Id
	LEFT JOIN $DataPublic.freightdata D ON D.CompanyId=S.CompanyId 
	LEFT JOIN $DataIn.ch10_mailaddress A ON A.Id=S.LinkMan
	LEFT JOIN $DataIn.trade_object C ON C.CompanyId=A.CompanyId 
	LEFT JOIN $DataPublic.staffmain P ON P.Number=S.Operator
	LEFT JOIN $DataPublic.my2_bankinfo B ON B.Id=M.BankId
	WHERE 1 $SearchRows order by M.Id DESC,S.SendDate DESC";
$mainResult = mysql_query($mySql." $PageSTR",$link_id);
if($mainRows = mysql_fetch_array($mainResult)){
	$tbDefalut=0;
	$midDefault="";
	$d=anmaIn("download/samplemail/",$SinkOrder,$motherSTR);//寄样图片、进度图片目录
	$d2=anmaIn("download/expressbill/",$SinkOrder,$motherSTR);
	do{
		$m=1;
		//结付主表数据
		$Mid=$mainRows["Mid"];
		$PayDate=$mainRows["PayDate"];
		$ShipDate=$mainRows["ShipDate"];
		$PayAmount=$mainRows["PayAmount"];
		$BankName=$mainRows["Title"];
		$ImgDir="download/samplemail/";
		$Checksheet=$mainRows["Checksheet"];
		$Payee=$mainRows["Payee"];
		$Receipt=$mainRows["Receipt"];
		include "../model/subprogram/cw0_imgview.php";
		$cSignFrom=$mainRows["cSign"];
		include"../model/subselect/cSign.php";
		$PayRemark=$mainRows["PayRemark"]==""?"&nbsp":"<img src='../images/remark.gif' title='$mainRows[PayRemark]' width='16' height='16'>";
		$MLocks=$mainRows["MLocks"];

		//结付明细数据
		$Id=$mainRows["Id"];
		$SendDate=$mainRows["SendDate"];
		$Forshort=$mainRows["Forshort"];
		$Client=$mainRows["Client"];
		$Termini=$mainRows["Termini"];
		$ExpressNO=$mainRows["ExpressNO"];
		//提单
		$Lading="../download/expressbill/".$ExpressNO.".jpg";
		if(file_exists($Lading)){
			$f1=anmaIn($ExpressNO.".jpg",$SinkOrder,$motherSTR);
			$ExpressNO="<span onClick='OpenOrLoad(\"$d2\",\"$f1\")' style='CURSOR: pointer;color:#FF6633'>$ExpressNO</span>";
			}
		//发票
		$Invoice="<a href='ch_invoicemodel.php?I=$Id' target='_black'>View</a>";
		//照片
		$SamplePicture="&nbsp;";
		$checkPicture=mysql_fetch_array(mysql_query("SELECT Id FROM $DataIn.ch10_samplepicture WHERE Mid='$Id'",$link_id));
		if($checkPicture["Id"]!=""){
			$f2=anmaIn($Id,$SinkOrder,$motherSTR);
			$t=anmaIn("ch10_samplepicture",$SinkOrder,$motherSTR);
			$SamplePicture="<span onClick='OpenPhotos(\"$d\",\"$f2\",\"$t\")' style='CURSOR: pointer;color:#FF6633'>view</span>";
			}
		//进度
		$Schedule=$myRow["Schedule"]==0?"&nbsp;":$myRow["Schedule"];
		if($Schedule==1){
			$f3=anmaIn("Schedule".$Id.".jpg",$SinkOrder,$motherSTR);
			$Schedule="<span onClick='OpenOrLoad(\"$d\",\"$f3\")' style='CURSOR: pointer;color:#FF6633'>view</span>";
			}
		$Pieces=$mainRows["Pieces"];
		$Weight=$mainRows["Weight"];
		$Price=$mainRows["Price"];
		$Amount=$mainRows["Amount"];
		$HandledBy=$mainRows["HandledBy"];
		$ReceiveDate=$mainRows["ReceiveDate"]==""?"&nbsp;":$mainRows["ReceiveDate"];
		$Remark=$mainRows["Remark"]==""?"&nbsp;":"<img src='../images/remark.gif' title='$mainRows[Remark]' width='18' height='18'>";
		//输出
		if($Keys & mUPDATE || $Keys & mLOCK){
			$upMian="<img location.href=\"#\"' style='CURSOR: pointer' onclick='upMainData(\"ch_samplemailing_upmain\",$Mid)' src='../images/edit.gif' title='行号:$mRow 编号:$Mid 更新结付单资料!' width='13' height='13'>";
			}
		else{
			$upMian="&nbsp;";
			}
		if($MLocks==0){
			$Choose="<img src='../images/lock.png' title='主记录已锁定!' width='15' height='15'>";
			}
		else{
			if($Keys & mUPDATE){
				$Choose="<input name='checkid[]' type='checkbox' id='checkid$i' value='$Id' disabled>";
				}
			else{
				$Choose="<img src='../images/lock.png' title='没有操作权限!' width='15' height='15'>";
				}
			}

		if($tbDefalut==0 && $midDefault==""){//首行
			//并行列
			echo"<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'><tr>";
			echo"<td scope='col' class='A0111' width='$Field[$m]' align='center'>$upMian</td>";//更新
			$unitWidth=$tableWidth-$Field[$m];
			$m=$m+2;
			echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$PayDate</td>";//结付日期
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Payee</td>";		//凭证
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='center'>$Receipt</td>";		//回执
			$mRow++;
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='center'>$PayRemark</td>";		//结付备注
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='center'>$PayAmount</td>";		//结付总额
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='center'>$BankName</td>";		//结付银行
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='center'>$cSign</td>";
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			//并行宽
			//echo"<td width='$unitWidth' class='A0101'>";
			echo"<td width='' class='A0101'>";
			$midDefault=$Mid;
			}
		if($midDefault!="" && $midDefault==$Mid){//同属于一个主ID，则依然输出明细表格
			$m=17;
			echo"<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
			echo"<tr onmousedown='ClickKeyCheck(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' 
				onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
				onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
			$unitFirst=$Field[$m]-1;
			echo"<td class='A0001' width='$unitFirst' height='20' align='center' $tdBGCOLOR>$Choose</td>";//选项
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$i</td>";//序号
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$SendDate</td>";//寄件日期
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Forshort</td>";//快递公司
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Client</td>";//客户
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Termini</td>";//目的地
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]'>&nbsp;$ExpressNO</td>";//提单号码
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$Invoice</td>";//发票
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$SamplePicture</td>";//样品照片
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$Schedule</td>";//寄样进度
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$Pieces</td>";//件数
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$Weight</td>";//重量
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$Price</td>";//单价
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$Amount</td>";//金额
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$HandledBy</td>";//经手人
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$ReceiveDate</td>";//签收日期
			$m=$m+2;
			echo"<td  class='A0001' width='' align='center'>$Remark</td>";//备注
			echo"</tr></table>";
			$i++;
			}
		else{
			//新行开始
			echo"</td></tr></table>";//结束上一个表格
			//并行列
			echo"<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'><tr>";
			echo"<td scope='col' class='A0111' width='$Field[$m]' align='center'>$upMian</td>";//更新
			$unitWidth=$tableWidth-$Field[$m];
			$m=$m+2;
			echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$PayDate</td>";//结付日期
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Payee</td>";		//凭证
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='center'>$Receipt</td>";		//回执
			$mRow++;
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='center'>$PayRemark</td>";		//结付备注
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='center'>$PayAmount</td>";		//结付总额
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='center'>$BankName</td>";		//结付银行
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='center'>$cSign</td>";
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			//并行宽
			//echo"<td width='$unitWidth' class='A0101'>";
			echo"<td width='' class='A0101'>";
			$midDefault=$Mid;
			echo"<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
			echo"<tr onmousedown='ClickKeyCheck(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' 
				onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
				onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
			$unitFirst=$Field[$m]-1;
			echo"<td class='A0001' width='$unitFirst' height='20' align='center' $tdBGCOLOR>$Choose</td>";//选项
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$i</td>";//序号
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$SendDate</td>";//寄件日期
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Forshort</td>";//快递公司
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Client</td>";//客户
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Termini</td>";//目的地
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]'>&nbsp;$ExpressNO</td>";//提单号码
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$Invoice</td>";//发票
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$SamplePicture</td>";//样品照片
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$Schedule</td>";//寄样进度
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$Pieces</td>";//件数
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$Weight</td>";//重量
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$Price</td>";//单价
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$Amount</td>";//金额
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$HandledBy</td>";//经手人
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$ReceiveDate</td>";//签收日期
			$m=$m+2;
			echo"<td  class='A0001' width='' align='center'>$Remark</td>";//备注
			echo"</tr></table>";
			$i++;
			}
		}while($mainRows = mysql_fetch_array($mainResult));
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