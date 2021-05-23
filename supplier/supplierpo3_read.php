<?php
//电信-zxq 2012-08-01
/*
$DataIn.cg1_stocksheet
$DataIn.cg1_stockmain
$DataIn.stuffdata
$DataPublic.currencydata
$DataIn.trade_object
$DataIn.ck1_rksheet
$DataIn.cw1_fkoutsheet
分开已更新
*/
 session_start();
$Login_WebStyle="default";
include "../model/modelhead.php";
echo"<link rel='stylesheet' href='../model/mask.css'>";
//步骤2：需处理

if ($Estate=="" && $From!="slist"){
	echo"<meta http-equiv=\"Refresh\" content='0;url=Gys_sh_read.php?Estate='>";
}

$tableMenuS=550;
ChangeWtitle("$SubCompany 采购单列表(结付分列)");
$funFrom="supplierpo3";
$From=$From==""?"read":$From;


//echo "S_IsPrice:$S_IsPrice <Br>";

$Estate=$Estate==""?1:$Estate;
if($S_IsPrice==1){
	$ColsNumber=16;
	$sumCols="7,8,9,12,13,14,15";			//求和列,需处理
	$MergeRows=2;
	$Th_Col="采购单号|60|备注|30|选项|35|行号|30|配件ID|40|配件名称|200|图档|30|QC图|30|品检<br>报告|40|需求数|45|增购数|45|实购数|45|含税价|45|单位|30|金额|60|金额(RMB)|60|收货数|45|欠数/末补|65|请款<br>方式|30|货款|30|采购流水号|100";
    $ActioToS="1,11,38";
}
else
{
	$ColsNumber=13;
	$sumCols="5,6,7,8,9";			//求和列,需处理
	$MergeRows=2;
	$Th_Col="采购单号|60|备注|30|选项|35|行号|30|配件ID|40|配件名称|200|图档|30|QC图|30|品检<br>报告|40|需求数|45|增购数|45|实购数|45|收货数|45|欠数|45|请款<br>方式|30|货款|30|采购流水号|100";
	$ActioToS="1";
}

//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 200;							//每页默认记录数量

$Keys=1;
$otherAction=($Estate==1 || $Estate==5)?"<span onclick='javascript:showMaskDiv()' $onClickCSS>请款</span><input name='CompanyId' type='hidden' id='CompanyId' value='$myCompanyId'>":"";
//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
$SearchRows.=" AND M.CompanyId='$myCompanyId' ";
//步骤4：需处理-可选条件下拉框
if($From!="slist"){	//非查询：过滤采购、结付方式、供应商、月份
	$SearchRows=" AND M.CompanyId='$myCompanyId'";
	$TempEstateSTR="EstateSTR".strval($Estate);
	$$TempEstateSTR="selected";
//结付状态
	echo"<select name='Estate' id='Estate' onchange='document.form1.submit()'>";
	echo"<option value='' $EstateSTR>已出</option>";
	echo"<option value='1' $EstateSTR1>未请款</option>";
	if ($myCompanyId==2029) {
	    echo"<option value='5' $EstateSTR5>未请款(FSC)</option>";
	}
	echo"<option value='2' $EstateSTR2>请款中</option>";
	echo"<option value='3' $EstateSTR3>请款通过</option>";
	echo"<option value='4' $EstateSTR4>已结付</option>";
	echo"</select>&nbsp;";
	switch($Estate){
	    case 1:
	        if ($myCompanyId==2029) {
	           $SearchRows.=" and F.Estate IS NULL and not (A.StuffCname like '%fsc%' )";
	       }
	       else{
		      $SearchRows.=" and F.Estate IS NULL";
	       }

	       break;
	    case 4:
	       $SearchRows.=" and F.Estate='0'";
	       break;
	   case 5:
	        $SearchRows.=" and F.Estate IS NULL and A.StuffCname like '%fsc%' ";
	        break;
	    default:
	        $SearchRows.=" and F.Estate='$Estate'";
	       break;
	}
//月份
		$date_Result = mysql_query("SELECT M.Date 
		FROM $DataIn.cg1_stocksheet S 
		LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid
		LEFT JOIN $DataIn.cw1_fkoutsheet F ON F.StockId=S.StockId	
		LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId 	
		WHERE 1 $SearchRows group by DATE_FORMAT(M.Date,'%Y-%m') order by M.Id DESC",$link_id);
		if ($dateRow = mysql_fetch_array($date_Result)) {
			echo"<select name='chooseDate' id='chooseDate' onchange='document.form1.submit()'>";
			$chooseDateSign=0;
			do{
				$dateValue=date("Y-m",strtotime($dateRow["Date"]));
				$StartDate=$dateValue."-01";
				$EndDate=date("Y-m-t",strtotime($dateRow["Date"]));
				//$dateText=date("Y年m月",strtotime($dateRow["Date"]));
				$chooseDate=$chooseDate==""?$dateValue:$chooseDate;
				$chooseDateSign=$chooseDateSign==0?" and ((M.Date>'$StartDate' and M.Date<'$EndDate') OR M.Date='$StartDate' OR M.Date='$EndDate')":$chooseDateSign;
				if($chooseDate==$dateValue){
					echo"<option value='$dateValue' selected>$dateValue</option>";
					//$SearchRows.=" and  DATE_FORMAT(M.Date,'%Y-%m')='$dateValue'";
					$chooseDateSign=1;
					$SearchRows.=" and ((M.Date>'$StartDate' and M.Date<'$EndDate') OR M.Date='$StartDate' OR M.Date='$EndDate')";
					}
				else{
					echo"<option value='$dateValue'>$dateValue</option>";
					}
				}while($dateRow = mysql_fetch_array($date_Result));
				if ($chooseDateSign!=1)  $SearchRows.=$chooseDateSign;
			echo"</select>&nbsp;";
			}
		else{
			//无月份记录
			$SearchRows.=" and M.Date=''";
			}
		}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$StuffIdArray=array();
$mySql="SELECT M.Date,M.PurchaseID,M.Remark,
S.Id,S.Mid,S.StockId,S.POrderId,S.StuffId,S.Price,S.OrderQty,S.StockQty,S.AddQty,S.FactualQty,
S.CompanyId,S.BuyerId,S.DeliveryDate,S.StockRemark,S.AddRemark,S.Estate,S.Locks,
A.StuffCname,A.Picture,A.Gfile,A.Gstate,A.Gremark,A.GfileDate,A.TypeId ,U.Name AS UnitName,F.Estate as cwestate,F.AutoSign 
FROM $DataIn.cg1_stocksheet S
LEFT JOIN $DataIn.cg1_stockmain M ON S.Mid=M.Id 
LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId 
LEFT JOIN $DataPublic.stuffunit U ON U.Id=A.Unit
LEFT JOIN $DataIn.cw1_fkoutsheet F ON F.StockId=S.StockId
WHERE 1 $SearchRows ORDER BY M.PurchaseID DESC";
//echo $mySql;
$mainResult = mysql_query($mySql." $PageSTR",$link_id);
if($mainRows = mysql_fetch_array($mainResult)){
	$tbDefalut=0;
	$midDefault="";
	$webQty=0;
        //请款限制日期
        $LimitDate=date("Y-m") . "-25";
	do{
		$m=1;
		$Mid=$mainRows["Mid"];
		$Date=$mainRows["Date"];
		//echo "Date:$Date";
		if($Dates!="") {
			$Dates=$Date."：".CountDays($Date);
		}
		$PurchaseID=$mainRows["PurchaseID"];
		$Remark=$mainRows["Remark"]==""?"&nbsp":"<img src='../images/remark.gif' alt='$mainRows[Remark]' width='16' height='16'>";
		//加密
		$MidJM=anmaIn($Mid,$SinkOrder,$motherSTR);
		if($S_IsPrice==1){
			$PurchaseIDStr="<a href='supplierpo_view.php?V=$MidJM' target='_blank'>$PurchaseID</a>";
			}
		else{
			$PurchaseIDStr="<a href='supplierpo_NoPriceView.php?V=$MidJM' target='_blank'>$PurchaseID</a>";
			}
		$upMian="&nbsp;";
		//明细资料
		$StuffId=$mainRows["StuffId"];
		if($StuffId!=""){
			$checkidValue=$mainRows["Id"];
			$StuffCname=$mainRows["StuffCname"];
			$FactualQty=$mainRows["FactualQty"];
			$AddQty=$mainRows["AddQty"];
			$Qty=$FactualQty+$AddQty;
			$Price=$mainRows["Price"];
			$UnitName=$mainRows["UnitName"];
			$Amount=sprintf("%.2f",$Qty*$Price);
			$StockId=$mainRows["StockId"];
			$Estate=$mainRows["Estate"];
			$Locks=$mainRows["Locks"];
			$BuyerId=$mainRows["BuyerId"];
			$CompanyId=$mainRows["CompanyId"];
			$tdBGCOLOR=$mainRows["POrderId"]==""?"bgcolor='#FFCC99'":"";
			$Picture=$mainRows["Picture"];
            $TypeId=$mainRows["TypeId"];

		    //1表示手动请款审核,2.表示手动请款自动通过,3表示自动请款审核,4表示自动请款自动通过
		    $Autobgcolor="";
			$AutoSign=$mainRows["AutoSign"];
			switch($AutoSign){
				case 2:
					$AutoSign="<image src='../images/AutoCheckB.png' style='width:20px;height:20px;' title='人工请款自动通过'/>";
					break;
				case 4:
					$AutoSign="<image src='../images/AutoCheck.png' style='width:20px;height:20px;' title='系统请款自动通过'/>";
					//$Autobgcolor="bgcolor='##FF0000'";
					break;
				default:
					$AutoSign="&nbsp;";
					break;

			}

			$Gfile=$mainRows["Gfile"];
			$Gstate=$mainRows["Gstate"];
			$Gremark=$mainRows["Gremark"];
			$GfileDate=$mainRows["GfileDate"]==""?"&nbsp;":substr($mainRows["GfileDate"],0,10);
			$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
			$ComeFrom="Supplier"; //说明来自供应商，则只已审核的图片.
			include "../model/subprogram/stuffimg_model.php";
			include "../model/subprogram/stuffimg_Gfile.php";	//图档显示
			 //配件QC检验标准图
             include "../model/subprogram/stuffimg_qcfile.php";
             //配件品检报告qualityReport
             include "../model/subprogram/stuff_get_qualityreport.php";
			//供应商结付货币的汇率
			$Rate=1;
			$currency_Temp = mysql_query("SELECT C.Rate FROM $DataPublic.currencydata C,$DataIn.trade_object P WHERE P.CompanyId='$CompanyId' and P.Currency=C.Id ORDER BY C.Id LIMIT 1",$link_id);
	     if ($currency_Temp ){
			if($RowTemp = mysql_fetch_array($currency_Temp)){
				$Rate=$RowTemp["Rate"];//汇率
				}
	    	}
			$rmbAmount=sprintf("%.2f",$Amount*$Rate);
			///仓库情况////////////////////////////////////////

			//收货情况
			$rkTemp=mysql_query("SELECT IFNULL(SUM(Qty),0) AS Qty FROM $DataIn.ck1_rksheet WHERE StockId='$StockId' order by Id",$link_id);
			if (mysql_num_rows($rkTemp)>0){
		  	      $rkQty=mysql_result($rkTemp,0,"Qty");
			}
			else{
				$rkQty=0;
			}
			//结付情况/**/
			$LockRemark="";
			$checkPay=mysql_query("SELECT Estate,Month FROM $DataIn.cw1_fkoutsheet WHERE StockId='$StockId' ORDER BY Id DESC LIMIT 1",$link_id);
			if($checkPayRow=mysql_fetch_array($checkPay)){
				$cwEstate=$checkPayRow["Estate"];
				$AskMonth=$checkPayRow["Month"];
				switch($cwEstate){
					case 0://已结付
						$cwEstate="<div class='greenB' title='已结付...货款月份:$AskMonth'>√</div>";
						$LockRemark="已结付，锁定操作";
					break;
					case 2:	//请款中
						$cwEstate="<div class='yellowB' title='请款中...货款月份:$AskMonth'>×.</div>";
						$LockRemark="已请款，锁定操作";
					break;
					case 3://请款通过
						$cwEstate="<div class='yellowB' title='等候结付...货款月份:$AskMonth'>√.</div>";
						$LockRemark="已请款通过，锁定操作";
					break;
					}
				}
			else{
				$cwEstate="<div class='redB'>×</div>";
			}
              /*
             //最后一次的收货日期
            if (date("d")>=25 && $LockRemark==""){
                $shTimeRow=mysql_fetch_array(mysql_query("SELECT MAX(M.Date) AS MaxDate FROM $DataIn.ck1_rksheet S
                        LEFT JOIN $DataIn.ck1_rkmain M ON M.Id=S.Mid WHERE S.StockId='$StockId'",$link_id));
                    $MaxDate=$shTimeRow["MaxDate"];
                    if ($MaxDate!="")
                    {
                        if ($MaxDate>=$LimitDate){
                           $LockRemark.="送货日期($MaxDate)在25日后当月不能请款!";
                       }
                    }
            }
              */
			//尾数
			$Mantissa=$Qty-$rkQty;$Sid=anmaIn($StockId,$SinkOrder,$motherSTR);
			$Locks=0;
			if($Mantissa<=0){//尾数少于或等于0时
				$Locks=1;
				$BGcolor="class='greenB'";$StockId="<a href='../public/ck_rk_list.php?Sid=$Sid' target='_blank' title='点击查看收货记录'>$StockId</a>";
				if($Mantissa<0){
					$BGcolor="class='redB'";$Locks=0;
					$Mantissa="错误";
					}
				}
			else{
				if($Mantissa==$Qty){
					$BGcolor="class='redB'";
					}
				else{
					$BGcolor="class='yellowB'";$StockId="<a href='../public/ck_rk_list.php?Sid=$Sid' target='_blank' title='点击查看收货记录'>$StockId</a>";
					}
			}
			//////////////////////////////////////////////////
			///权限///////////////////////////////////////////
			if($Estate==1){
				$LockRemark="未审核";
				}
				///add by zx 2012-10-25 查是否有未补的货
			//退货的总数量 add by zx 2011-04-27
			if ($StuffIdArray["$StuffId"]=='' && $LockRemark=="" ) {
				//if($thStuffId!=$StuffId && $LockRemark=="") {  //相同配什时第一条取出总数
					$thSql=mysql_query("SELECT SUM( S.Qty ) AS thQty  FROM $DataIn.ck2_thmain M  
												   LEFT JOIN $DataIn.ck2_thsheet S ON S.Mid = M.Id
												   WHERE M.CompanyId = '$myCompanyId' AND S.StuffId = '$StuffId' ",$link_id);
					$thQty=mysql_result($thSql,0,"thQty");
					$thQty=$thQty==""?0:$thQty;
					//补货的数量 add by zx 2011-04-27
					$bcSql=mysql_query("SELECT SUM( S.Qty ) AS bcQty  FROM $DataIn.ck3_bcmain M 
												   LEFT JOIN $DataIn.ck3_bcsheet S ON S.Mid = M.Id
												   WHERE M.CompanyId = '$myCompanyId' AND S.StuffId = '$StuffId' ",$link_id);
					$bcQty=mysql_result($bcSql,0,"bcQty");
					$bcQty=$bcQty==""?0:$bcQty;

					$webQty=$thQty-$bcQty; //未补数量
					$thStuffId=$StuffId;
					$StuffIdArray["$StuffId"]=$webQty;
					//echo "$StuffId:".$StuffIdArray["$StuffId"]."<br>";
					//echo "LockRemark:$LockRemark <br>";

				//}
			}

			$cwestate=$mainRows["cwestate"];

			//查找大于当前送货时间StuffId配件送货请款记录

			$checkRkResult=mysql_query("SELECT SUM(IFNULL(R.Qty,0)) as Qty 
					FROM $DataIn.cg1_stocksheet S 
					LEFT JOIN $DataIn.cg1_stockmain M ON S.Mid=M.Id 
					LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId 
					LEFT JOIN $DataIn.ck1_rksheet R ON R.StockId=S.StockId 
					WHERE S.StuffId='$StuffId'  AND  M.CompanyId='$myCompanyId' AND M.Date>'$Date' AND S.Id<>'$checkidValue' 
					AND NOT EXISTS(SELECT F.StockId FROM $DataIn.cw1_fkoutsheet F WHERE F.StockId=S.StockId)  ",$link_id);
            $noqkQty=mysql_result($checkRkResult,0,"Qty");

			//echo "cwestate:$cwestate";
			//if($cwestate=='' && $StuffIdArray["$StuffId"]>0  && $LockRemark=="" && $Mantissa==0  ) {  //相同配件按最新日期递减补货，如果存在未补数量，则不能结款
			if($cwestate=='' && $noqkQty<$webQty  && $LockRemark=="" && $Mantissa==0  ) {  //相同配件按最新日期递减补货，如果存在未补数量，则不能结款
				$LockRemark="有未补数量，不能请款";
				$Mantissa=$Mantissa.'/'.$StuffIdArray["$StuffId"];
				if ($Mantissa==0) { //
					$StuffIdArray["$StuffId"]=$StuffIdArray["$StuffId"]-$Qty;
				}
			}


			if($Locks==1){//有权限
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


			////////////////////////////////////////////////////
			if($tbDefalut==0 && $midDefault==""){//首行
				//并行列
				echo"<table width='$tableWidth' border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'  bgcolor='#FFFFFF'><tr>";
				echo"<td scope='col' class='A0111' width='$Field[$m]' align='center'>$PurchaseIDStr</td>";//下单日期
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Remark</td>";		//下单备注
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				//并行宽
				//echo"<td width='$unitWidth' class='A0101'>";
				echo"<td width='' class='A0101'>";
				$midDefault=$Mid;
				}
			if($midDefault!="" && $midDefault==$Mid){//同属于一个主ID，则依然输出明细表格
				$m=5;
				echo"<table width='100%' border='0' cellspacing='0' id='ListTable$i'  style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
				echo"<tr onmousedown='ClickKeyCheck(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' 
					onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
					onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
				$unitFirst=$Field[$m]-1;
				echo"<td class='A0001' width='$unitFirst'  align='center' $tdBGCOLOR>$Choose</td>";//选项
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$i</td>";			//序号
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$StuffId</td>";	//配件ID
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]'>$StuffCname</td>";		//配件名称
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$Gfile</td>";		//图档
				$m=$m+2;
                                echo"<td class='A0001' width='$Field[$m]' align='center'>$QCImage</td>";  //QC标准图
                                $m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$qualityReport</td>"; //品检报告
                                $m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'>$FactualQty</td>";		//需求数量
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'>$AddQty</td>";	//增购数量
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='right'>$Qty</td>";	//实购数量
				if($S_IsPrice==1){
					$m=$m+2;
					echo"<td  class='A0001' width='$Field[$m]' align='right'>$Price</td>";				//单价
					$m=$m+2;
					echo"<td  class='A0001' width='$Field[$m]' align='center'>$UnitName</td>";		//单位
					$m=$m+2;
					echo"<td  class='A0001' width='$Field[$m]' align='right'>$Amount</td>";		//金额
					$m=$m+2;
					echo"<td  class='A0001' width='$Field[$m]' align='right'>$rmbAmount</td>";		//金额RMB
				}
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='right'>$rkQty</td>";		//收货数量
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='right'><div $BGcolor>$Mantissa</div></td>";		//欠数数量
                $m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='center'>$AutoSign</td>";//结付状态
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='center'>$cwEstate</td>";//结付状态
				$m=$m+2;
				echo"<td  class='A0001' width='' align='center'>$StockId</td>";//需求流水号
				echo"</tr></table>";
				$i++;
				}
			else{
				//新行开始
				echo"</td></tr></table>";//结束上一个表格
				//并行列
				echo"<table width='$tableWidth' border='0' cellspacing='0'  style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'><tr>";
				echo"<td scope='col' class='A0111' width='$Field[$m]' align='center'>$PurchaseIDStr</td>";//下单日期
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Remark</td>";		//下单备注
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				//并行宽
				//echo"<td width='$unitWidth' class='A0101'>";
				echo"<td width='' class='A0101'>";
				$midDefault=$Mid;
				echo"<table width='100%' border='0' cellspacing='0' id='ListTable$i'  style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
				echo"<tr onmousedown='ClickKeyCheck(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' 
					onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
					onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
				$unitFirst=$Field[$m]-1;
				echo"<td class='A0001' width='$unitFirst' align='center' $tdBGCOLOR>$Choose</td>";//选项
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$i</td>";			//序号
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$StuffId</td>";	//配件ID
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]'>$StuffCname</td>";		//配件名称
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$Gfile</td>";		//图档
				$m=$m+2;
                                echo"<td class='A0001' width='$Field[$m]' align='center'>$QCImage</td>";  //QC标准图
                                $m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$qualityReport</td>"; //品检报告
                                $m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'>$FactualQty</td>";		//需求数量
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'>$AddQty</td>";		//增购数量
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='right'>$Qty</td>";		//实购数量
				if($S_IsPrice==1){
					$m=$m+2;
					echo"<td  class='A0001' width='$Field[$m]' align='right'>$Price</td>";		//单价
					$m=$m+2;
					echo"<td  class='A0001' width='$Field[$m]' align='center'>$UnitName</td>";		//单位
					$m=$m+2;
					echo"<td  class='A0001' width='$Field[$m]' align='right'>$Amount</td>";		//金额
					$m=$m+2;
					echo"<td  class='A0001' width='$Field[$m]' align='right'>$rmbAmount</td>";	//金额RMB
				}
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='right'>$rkQty</td>";		//收货数量
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='right'><div $BGcolor>$Mantissa</div></td>";	//欠数数量
                $m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='center'>$AutoSign</td>";//结付状态
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='center'>$cwEstate</td>";		//结付状态
				$m=$m+2;

				echo"<td  class='A0001' width='' align='center'>$StockId</td>";	//需求流水号
				echo"</tr></table>";
				$i++;
				}
			}
		}while($mainRows = mysql_fetch_array($mainResult));
	echo"</tr></table>";
	}
else{
	noRowInfo($tableWidth);
	}
//步骤7：
echo '</div>';?>
<div id='divShadow' class="divShadow" style="display:none;">
	<div class='divInfo' id='divInfo'>
	<table width="300">
		<tr><td align="left">请输入请款月份</td></tr>
		<tr><td align="center"><input name="Month" type="text" id="Month" size="30" maxlength="7" value="" onFocus="WdatePicker({dateFmt:'yyyy-MM',minDate:'%y-%M-{%d+1}'})" class="Wdate" ></td></tr>
		<tr><td align="right"><a href="javascript:ckeckForm()">确定</a> &nbsp;&nbsp; <a href="javascript:closeMaskDiv()">取消</a></td></tr>
	</table>
	</div>
</div>
<div id="divPageMask" class="divPageMask" style="display:none;">
	<iframe scrolling="no" height="100%" width="100%" marginwidth="0" marginheight="0" src="../model/MaskBgColor.htm"></iframe>
</div>

<?php
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>
<script>
function showMaskDiv(){	//显示遮罩对话框
	//检查是否有选取记录
	UpdataIdX=0;
	for (var i=0;i<form1.elements.length;i++){
			var e=form1.elements[i];
			if (e.type=="checkbox"){
				var NameTemp=e.name;
				var Name=NameTemp.search("checkid") ;//防止有其它参数用到checkbox，所以要过滤
				if(e.checked && Name!=-1){
					UpdataIdX=UpdataIdX+1;
					break;
					}
				}
			}
	//如果没有选记录
	if(UpdataIdX==0){
		alert("没有选取记录!");
		}
	else{
		document.form1.Month.value="";
		document.getElementById('divShadow').style.display='block';
		divPageMask.style.width = document.body.scrollWidth;
		divPageMask.style.height = document.body.scrollHeight>document.body.clientHeight?document.body.scrollHeight:document.body.clientHeight;
		document.getElementById('divPageMask').style.display='block';
		}
	}

function closeMaskDiv(){	//隐藏遮罩对话框
	document.getElementById('divShadow').style.display='none';
	document.getElementById('divPageMask').style.display='none';
	}
function ckeckForm(){
	//检查月份
    /*//已修改为送货日期为当月25日之后不能请款。
    mydate=new Date();
    myday= mydate.getDate();
    if(myday>25){alert("请在每月25号前请款,谢谢!");return false;}
    */
	var checkMonth=yyyymmCheck(document.form1.Month.value);
	if(checkMonth && document.form1.Month.value!=""){
		for (var i=0;i<form1.elements.length;i++){
			var e=form1.elements[i];
			var NameTemp=e.name;
			var Name=NameTemp.search("checkid") ;//防止有其它参数用到checkbox，所以要过滤
			if (e.type=="checkbox" && Name!=-1){
				e.disabled=false;
				}
			}
		document.form1.action="../public/cg_cgdmainP_updated.php?ActionId=14";
		document.form1.submit();
		}
	else{
		alert("格式不对(YYYY-MM)");
		}
	}
</script>