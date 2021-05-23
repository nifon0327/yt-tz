<?php
/*
$mainData
$sheetData
$DataPublic.adminitype
$DataPublic.currencydata
二合一已更新
电信-joseph
*/
//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$mainData="$DataIn.zw3_purchasem";
$sheetData="$DataIn.zw3_purchases";

include "../model/subprogram/read_model_3.php";
//步骤1：
if($From!="slist"){
	$SearchRows="";
	echo"<select name='Estate' id='Estate' onchange='ResetPage(this.name)'>
	<option value='3' $EstateSTR3>未结付</option>
	<option value='0' $EstateSTR0>已结付</option>
	</select>&nbsp;";

	   $monthResult = mysql_query("SELECT PayDate FROM $mainData WHERE 1  group by DATE_FORMAT(PayDate,'%Y-%m') order by PayDate DESC",$link_id);
      if ($monthResult ){
		if ($monthRow = mysql_fetch_array($monthResult)) {
			$MonthSelect.="<select name='chooseMonth' id='chooseMonth' onchange='document.form1.submit()'>";
			do{
				$dateValue=date("Y-m",strtotime($monthRow["PayDate"]));
				$FirstValue=$FirstValue==""?$dateValue:$FirstValue;
				$dateText=date("Y年m月",strtotime($monthRow["PayDate"]));
				if($chooseMonth==$dateValue){
					$MonthSelect.="<option value='$dateValue' selected>$dateText</option>";
					$SearchRows.="and  DATE_FORMAT(M.PayDate,'%Y-%m')='$dateValue'";
					}
				else{
					$MonthSelect.="<option value='$dateValue'>$dateText</option>";
					}
				}while($monthRow = mysql_fetch_array($monthResult));
			$SearchRows=$SearchRows==""?"and  DATE_FORMAT(M.PayDate,'%Y-%m')='$FirstValue'":$SearchRows;
			$MonthSelect.="</select>&nbsp;";
			}

	$checkType =mysql_query("SELECT G.Id,G.Name FROM $DataPublic.zw_goodstype  G
	LEFT JOIN $DataIn.zw3_purchaset T ON T.TypeId=G.Id
	LEFT JOIN $DataIn.zw3_purchases S ON S.TypeId=T.Id
	WHERE G.Estate=1  GROUP BY G.Id",$link_id);
    if($checkTypeRow = mysql_fetch_array($checkType)){
    echo"<select name='GoodsType' id='GoodsType'  onchange='document.form1.submit()'>";
    echo"<option value='' selected>物品类别</option>";
        do{
           $TypeId=$checkTypeRow["Id"];
	       $Name=$checkTypeRow["Name"];
        if ($TypeId==$GoodsType){
	           echo"<option value='$TypeId' selected>$Name</option>";
               $SearchRows.=" and T.TypeId='$TypeId'";
             }
	    else{
              echo "<option value='$TypeId'>$Name</option>";
            }
         }while ( $checkTypeRow = mysql_fetch_array($checkType));
       echo"</select>";
       }
    	//月份
  	   echo $MonthSelect;
	  }
	  $SearchRows.=" and S.Estate=0";
}

echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：可用功能输出
include "../model/subprogram/read_model_5.php";

//include "../model/subprogram/cw0_model1.php";
$i=1;
$j=($Page-1)*$Page_Size+1;
$mRow=1;
List_Title($Th_Col,"1",1);
$mySql="SELECT 
	M.PayDate,M.PayAmount,M.Payee,M.Receipt,M.CheckSheet,M.Remark AS PayRemark,M.Locks AS MLocks,
	S.Id,S.Mid,S.Unit,S.Price,S.Qty,T.TypeName,S.cgSign,S.Remark,S.Estate,P.Name AS Buyer,S.Bill,S.Locks,S.qkDate,S.Operator,T.Id AS TId,T.Attached,B.Title
 	FROM $mainData M
	LEFT JOIN $DataIn.zw3_purchases S ON S.Mid=M.Id
	LEFT JOIN $DataIn.zw3_purchaset T ON T.Id=S.TypeId
	LEFT JOIN $DataPublic.staffmain P ON P.Number=S.BuyerId
	LEFT JOIN $DataPublic.my2_bankinfo B ON B.Id=M.BankId
	WHERE 1 $SearchRows order by M.Id DESC,S.Date DESC";
	//echo $mySql;
$mainResult = mysql_query($mySql." $PageSTR",$link_id);
if($mainRows = mysql_fetch_array($mainResult)){
	$tbDefalut=0;
	$midDefault="";
	do{
		$m=1;
		//结付主表数据
		$Mid=$mainRows["Mid"];
		$PayDate=$mainRows["PayDate"];
		$PayAmount=$mainRows["PayAmount"];
		$BankName=$mainRows["Title"];
			$ImgDir="download/zwbuy/";
			$Checksheet=$mainRows["Checksheet"];
			$Payee=$mainRows["Payee"];
			$Receipt=$mainRows["Receipt"];
			include "../model/subprogram/cw0_imgview.php";
		$PayRemark=$mainRows["PayRemark"]==""?"&nbsp":"<img src='../images/remark.gif' title='$mainRows[PayRemark]' width='16' height='16'>";
		$MLocks=$mainRows["MLocks"];

		//结付明细数据
		$Id=$mainRows["Id"];
		$Qty=$mainRows["Qty"];
		$Unit=$mainRows["Unit"];
		$Price=$mainRows["Price"];
		$Amount=sprintf("%.2f",$Qty*$Price);
		$TypeName=$mainRows["TypeName"];
		$Remark=trim($mainRows["Remark"])==""?"&nbsp;":trim($mainRows["Remark"]);
		$qkDate=$mainRows["qkDate"];
		$Operator=$mainRows["Operator"];
		include "../model/subprogram/staffname.php";
		$Buyer=$mainRows["Buyer"];
		$Bill=$mainRows["Bill"];
		$Dir=anmaIn("download/zwbuy/",$SinkOrder,$motherSTR);
		if($Bill==1){
			$Bill="Z".$Id.".jpg";
			$Bill=anmaIn($Bill,$SinkOrder,$motherSTR);
			$Bill="<span onClick='OpenOrLoad(\"$Dir\",\"$Bill\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
			}
		else{
			$Bill="&nbsp;";
			}
		$TId=$mainRows["TId"];
		$Attached=$mainRows["Attached"];
		$Dir=anmaIn("download/zwwp/",$SinkOrder,$motherSTR);
		if($Attached==1){
			$Attached="Z".$TId.".jpg";
			$Attached=anmaIn($Attached,$SinkOrder,$motherSTR);
			$Attached="<span onClick='OpenOrLoad(\"$Dir\",\"$Attached\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
			}
		else{
			$Attached="-";
			}
			if(floor($Qty)==$Qty) { $Qty=floor($Qty); }
		//输出
		if($Keys & mUPDATE || $Keys & mLOCK){
			$upMian="<img location.href=\"#\"' style='CURSOR: pointer' onclick='upMainData(\"zw_purchasesqk_upmain\",$Mid)' src='../images/edit.gif' title='行号:$mRow 编号:$Mid 更新结付单资料!' width='13' height='13'>";
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
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='center'>$PayRemark</td>";		//对帐单
			$mRow++;
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='center'>$Checksheet</td>";		//结付备注
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='center'>$PayAmount</td>";		//结付总额
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='center'>$BankName</td>";		//结付银行
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
			echo"<td class='A0001' width='$Field[$m]' align='center'>$qkDate</td>";
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$TypeName</td>";
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Attached</td>";//图片
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Qty</td>";//金额
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Unit</td>";
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$Price</td>";
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]'>&nbsp;$Amount</td>";
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$Remark</td>";
			$m=$m+2;
			echo"<td  class='A0001' width='' align='center'>$Bill</td>";
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
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='center'>$PayRemark</td>";		//对帐单
			$mRow++;
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='center'>$Checksheet</td>";		//结付备注
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='center'>$PayAmount</td>";		//结付总额
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='center'>$BankName</td>";		//结付银行
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
			echo"<td class='A0001' width='$Field[$m]'><div align='center'>$i</div></td>";//序号
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$qkDate</td>";
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$TypeName</td>";
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Attached</td>";//图片
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Qty</td>";//金额
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Unit</td>";
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$Price</td>";
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]'>&nbsp;$Amount</td>";
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$Remark</td>";
			$m=$m+2;
			echo"<td  class='A0001' width='' align='center'>$Bill</td>";
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