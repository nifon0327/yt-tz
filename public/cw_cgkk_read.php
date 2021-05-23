<?php
//步骤1
include "../model/modelhead.php";
echo "<SCRIPT src='../model/publicfun.js' type=text/javascript></script>";
//步骤2：需处理
$ColsNumber=12;
$tableMenuS=700;
$sumCols="7,8";		//求和列
$From=$From==""?"read":$From;
ChangeWtitle("$SubCompany 采购单扣款列表");
$funFrom="cw_cgkk";
$Th_Col="操作|40|扣款单号|100|日期|65|供应商|100|总金额|60|备注|40|审核<br>状态|30|选项|60|序号|30|采购单号|70|采购单流水号|90|配件ID|50|配件名|250|单价|50|扣款数量|60|扣款金额|60|币种|30|扣款原因|150|扣款<br>状态|40|操作|40";

//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$ActioToS="1,2,3,4,113";	//15,34
$MergeRows=6;
//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
//非必选,过滤条件

if($From!="slist"&&$Kid==""){
			//***********************状态
    $SearchRows="";
	$Estate=$Estate==""?1:$Estate;
	$selStr="Estate".$Estate;
	$$selStr="selected";
	echo "<select id='Estate' name='Estate' onchange='document.form1.submit()'>";
	echo "<option value='1' $Estate1>未审核</option>";
	echo "<option value='0' $Estate0>已审核</option></select>&nbsp;";
	$SearchRows.=" AND M.Estate='$Estate'";
	//**********************月份
	$monthResult = mysql_query("SELECT M.Date FROM $DataIn.cw15_gyskkmain M WHERE 1 $SearchRows group by DATE_FORMAT(M.Date,'%Y-%m') order by M.Date DESC",$link_id);
	if($monthRow = mysql_fetch_array($monthResult)) {
		echo"<select name='chooseMonth' id='chooseMonth' onchange='document.form1.submit()'>";
		do{
			$dateValue=date("Y-m",strtotime($monthRow["Date"]));
			$dateText=date("Y年m月",strtotime($monthRow["Date"]));
			$chooseMonth=$chooseMonth==""?$dateValue:$chooseMonth;
			if($chooseMonth==$dateValue){
				echo"<option value='$dateValue' selected>$dateText</option>";
				$SearchRows.=" and DATE_FORMAT(M.Date,'%Y-%m')='$dateValue'";
				}
			else{
				echo"<option value='$dateValue'>$dateText</option>";
				}
			}while($monthRow = mysql_fetch_array($monthResult));
		echo"</select>&nbsp;";
		}

	//供应商
	$ProviderResult=mysql_query("SELECT P.CompanyId ,P.Letter,P.Forshort
	                FROM $DataIn.cw15_gyskkmain M
	                LEFT JOIN $DataIn.trade_object  P ON P.CompanyId=M.CompanyId
					WHERE 1 $SearchRows GROUP BY P.CompanyId ORDER BY P.Letter",$link_id);
	 if($ProviderRow=mysql_fetch_array($ProviderResult)){
	    echo"<select name='CompanyId' id='CompanyId' onchange='document.form1.submit()'>";
		echo "<option vaule='' selected>全部</option>";
		 do{
		    $ThisCompanyId=$ProviderRow["CompanyId"];
			$Letter=$ProviderRow["Letter"];
			$Forshort=$ProviderRow["Forshort"];
			if($ThisCompanyId==$CompanyId){
			   echo"<option value='$ThisCompanyId' selected>$Letter"."_"."$Forshort</option>";
			   $SearchRows.=" AND M.CompanyId='$ThisCompanyId'";
			    }
			else{
			    echo"<option value='$ThisCompanyId'>$Letter"."_"."$Forshort</option>";
			    }

		   }while($ProviderRow=mysql_fetch_array($ProviderResult));
	     }
		echo"</select>&nbsp;";

	}
include "cw_cgkk_auto.php";

if($Kid!="")$SearchRows.=" AND S.Kid='$Kid'";
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";

//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
$mySql="SELECT S.Id,S.PurchaseID,S.StockId,S.StuffId,S.Qty,S.Price,S.Amount,SM.Name AS CgName,S.StuffName,M.Picture,D.Picture AS StuffPicture,
         S.Mid,M.BillNumber,M.Date,M.TotalAmount,M.BillFile,M.Remark,M.Estate,M.Operator,M.Locks,P.Forshort,S.Remark AS SheetRemark,S.Kid,C.Symbol
         FROM $DataIn.cw15_gyskksheet S
		 LEFT JOIN $DataIn.cw15_gyskkmain M ON M.Id=S.Mid
         LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
		 LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId 
		 LEFT JOIN $DataPublic.staffmain SM ON SM.Number=G.BuyerId
         LEFT JOIN $DataIn.trade_object  P ON P.CompanyId=M.CompanyId
		 LEFT JOIN $DataPublic.currencydata  C ON C.Id=P.Currency
         WHERE 1 $SearchRows ORDER BY M.Date";
//echo $mySql;
$mainResult = mysql_query($mySql." $PageSTR",$link_id);
if($mainRows = mysql_fetch_array($mainResult)){
$tbDefalut=0;
$midDefault="";
$dc=anmaIn("download/cgkkbill/",$SinkOrder,$motherSTR);
	do{
		$m=1;
		//主单信息
		$Mid=$mainRows["Mid"];
		$Date=$mainRows["Date"];
		$BillFile=$mainRows["BillFile"];
		$BillNumber=$mainRows["BillNumber"];
		$FileName=$BillNumber.".pdf";
		$fc=anmaIn($FileName,$SinkOrder,$motherSTR);
		if($BillFile==1){
		 $BillNumber="<a href=\"../admin/openorload.php?d=$dc&f=$fc&Type=&Action=6\" target=\"download\">$BillNumber</a>";
		     }
        $PictureView=$mainRows["Picture"];
		if($PictureView==1){
		    $PictureFileName=$mainRows["BillNumber"].".jpg";
	        $fd=anmaIn($PictureFileName,$SinkOrder,$motherSTR);
		    $PictureView="<a href=\"../admin/openorload.php?d=$dc&f=$fd&Type=&Action=6\" target=\"download\">view</a>";
		     }
       else $PictureView="";

		$Remark=$mainRows["Remark"];
		$Remark=$Remark==""?"&nbsp;":"<img src='../images/remark.gif' title='$Remark' width='18' height='18'>";
		$TotalAmount=$mainRows["TotalAmount"];
		$Forshort=$mainRows["Forshort"];
        $Operator=$mainRows["Operator"];
		include "../model/subprogram/staffname.php";
		$Locks=$mainRows["Locks"];
		$Estate=$mainRows["Estate"];
		//echo "Estate:$Estate";
		if($Estate==1){
		     $Estate="<div class='redB' title='未审核'>×</div>";
			 $LockRemark="";
			 }
		else {
		     $Estate="<div class='greenB' title='已审核'>√</div>";
			 if($Login_P_Number!=10006  &&  $Login_P_Number!=10007  &&  $Login_P_Number!=10369  &&  $Login_P_Number!=10868   &&  $Login_P_Number!=10341 ){//刘英姿修改
			    $LockRemark="已审核不能修改!";}
			 }
		$CgName=$mainRows["CgName"]==""?"&nbsp;":$mainRows["CgName"];
		$upMian="<img location.href=\"#\"' style='CURSOR: pointer' onclick='upMainData(\"cw_cgkk_upmain\",$Mid)' src='../images/edit.gif' alt='更新扣款主单资料' width='13' height='13'>";
		//明细资料
		$StuffId=$mainRows["StuffId"];
		if($StuffId!=""){
		    $Kid=$mainRows["Kid"];
			if($Kid==0)$KidEstate="<div class='redB' title='未扣款'>×</div>";
			else $KidEstate="<div class='greenB' title='已扣款'>√</div>";
			$Symbol=$mainRows["Symbol"];
		    $StuffId=$StuffId==0?"&nbsp;":$StuffId;
			$checkidValue=$mainRows["Id"];
			$StuffCname=$mainRows["StuffName"];
			$Qty=$mainRows["Qty"];
			$Price=$mainRows["Price"];
			$Amount=$mainRows["Amount"];
			$StockId=$mainRows["StockId"]==0?"&nbsp;":$mainRows["StockId"];
			$PurchaseID=$mainRows["PurchaseID"]==0?"&nbsp;":$mainRows["PurchaseID"];
            $SheetRemark=$mainRows["SheetRemark"]==""?"&nbsp;":$mainRows["SheetRemark"];
			$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
			//检查是否有图片
          $Picture=$mainRows["StuffPicture"];
		  include "../model/subprogram/stuffimg_model.php";	//检查是否有图片
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
			$Sid=anmaIn($StockId,$SinkOrder,$motherSTR);
           if($StockId!=0){
                     $FromDir='public';
		             $URL="cw_cgkk_productturn.php";
                     $theParam="StuffId=$StuffId";
			         $showPurchaseorder="<img onClick='P_ShowOrHide(StuffList$i,showtable$i,StuffList$i,\"$URL\",\"$theParam\",$i,\"\",\"$FromDir\");' name='showtable$i' src='../images/showtable.gif' 
			          title='显示或隐藏配件采购明细资料.' width='13' height='13' style='CURSOR: pointer'>";
			          $XtableWidth=$tableWidth-500;
			          $StuffListTB="<table width='$XtableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>			
				                                    <tr bgcolor='#B7B7B7'>
				                                   <td class='A0111' height='30' align='left'><br><div id='showStuffTB$i' width='$XsubTableWidth'>&nbsp;</div><br></td></tr></table>";
                  }
            else {  $showPurchaseorder=""; $StuffListTB=""; }
			////////////////////////////////////////////////////
			if($tbDefalut==0 && $midDefault==""){//首行
				//并行列
				echo"<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'><tr>";
				echo"<td scope='col' class='A0111' width='$Field[$m]' align='center'>$upMian</td>";
				$unitWidth=$tableWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$BillNumber</td>";
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				//echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$PictureView</td>";
				//$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Date</td>";
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Forshort</td>";
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$TotalAmount</td>";
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Remark</td>";
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Estate</td>";
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td width='' class='A0101'>";
				$midDefault=$Mid;
				}
			if($midDefault!="" && $midDefault==$Mid){//同属于一个主ID，则依然输出明细表格
				$m=15;
				echo"<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
				echo"<tr onmousedown='ClickKeyCheck(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' 
					onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
					onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
				$unitFirst=$Field[$m]-1;
				echo"<td class='A0001' width='$unitFirst'  align='center' $tdBGCOLOR>$Choose&nbsp;$showPurchaseorder</td>";//选项
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$j</td>";
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$PurchaseID</td>";
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$StockId</td>";
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$StuffId</td>";
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' >$StuffCname</td>";
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'>$Price</td>";
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'>$Qty</td>";
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'>$Amount</td>";
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='Center'>$Symbol</td>";
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' >$SheetRemark</td>";
				$m=$m+2;
				echo"<td class='A0001' width='' align='center'>$KidEstate</td>";
			    $m=$m+2;
				echo"<td class='A0001' width='' align='center'>$Operator</td>";
				echo"</tr></table>";
				$i++;
				$j++;
				}
			else{
				//新行开始
				echo"</td></tr></table>";//结束上一个表格
				//并行列
				echo"<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'><tr>";
				echo"<td scope='col' class='A0111' width='$Field[$m]' align='center'>$upMian</td>";
				$unitWidth=$tableWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$BillNumber</td>";
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				//echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$PictureView</td>";
				//$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Date</td>";
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Forshort</td>";
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$TotalAmount</td>";
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Remark</td>";
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Estate</td>";
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td width='' class='A0101'>";
				$midDefault=$Mid;
				echo"<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
				echo"<tr onmousedown='ClickKeyCheck(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' 
					onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
					onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
				$unitFirst=$Field[$m]-1;
				echo"<td class='A0001' width='$unitFirst'  align='center' $tdBGCOLOR>$Choose&nbsp;$showPurchaseorder</td>";//选项
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$j</td>";
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$PurchaseID</td>";
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$StockId</td>";
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$StuffId</td>";
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' >$StuffCname</td>";
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'>$Price</td>";
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'>$Qty</td>";
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'>$Amount</td>";
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='Center'>$Symbol</td>";
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' >$SheetRemark</td>";
				$m=$m+2;
				echo"<td class='A0001' width='' align='center'>$KidEstate</td>";
			    $m=$m+2;
				echo"<td class='A0001' width='' align='center'>$Operator</td>";
				echo"</tr></table>";
				$i++;
				$j++;
				}
			echo $StuffListTB;
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
<script language="JavaScript">
function ViewChart(Pid,OpenType){
	document.form1.action="../public/productdata_chart.php?Pid="+Pid+"&Type="+OpenType;
	document.form1.target="_blank";
	document.form1.submit();
	document.form1.target="_self";
	document.form1.action="";
	}
</script>