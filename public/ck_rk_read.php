<?php
//电信-zxq 2012-08-01
include "../model/modelhead.php";
$path = $_SERVER["DOCUMENT_ROOT"];
include_once($path.'/factoryCheck/checkSkip.php');
?>
<script>
function zhtj(obj){
	switch(obj){
		case "chooseDate"://改变采购
			if(document.all("CompanyId")!=null){
				document.forms["form1"].elements["CompanyId"].value="";
				}
		break;
		}
	document.form1.action="ck_rk_read.php";
	document.form1.submit();
}
</script>
<?php
//步骤2：需处理
$ColsNumber=12;
$tableMenuS=500;
ChangeWtitle("$SubCompany 入库记录列表");
$funFrom="ck_rk";
$From=$From==""?"read":$From;
$sumCols="7,8,9";			//求和列,需处理
$MergeRows=4;
$Th_Col="操作|50|日期|70|送货单|80|供应商|80|选项|60|序号|40|配件ID|50|配件名称|300|单位|40|品检<br>报告|40|采购总数|60|本次入库|60|出库数量|60|订单领料|60|请款<br>方式|30|采购单号|80|需求单流水号|110|存储位置|50";

//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 200;							//每页默认记录数量
$ActioToS="1,2,3,4,7,8";
$ActioToS="1,3,4,7,8";
//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-可选条件下拉框
if($From!="slist"){	//非查询：过滤采购、结付方式、供应商、月份
	$SearchRows="";
	$date_Result = mysql_query("SELECT Date FROM $DataIn.ck1_rkmain WHERE 1 GROUP BY DATE_FORMAT(Date,'%Y-%m') ORDER BY Date DESC",$link_id);
	if($dateRow = mysql_fetch_array($date_Result)) {
		echo"<select name='chooseDate' id='chooseDate' onchange='zhtj(this.name)'>";
		do{
			$dateValue=date("Y-m",strtotime($dateRow["Date"]));
			$StartDate=$dateValue."-01";
			$EndDate=date("Y-m-t",strtotime($dateRow["Date"]));
			$chooseDate=$chooseDate==""?$dateValue:$chooseDate;
			if($chooseDate==$dateValue){
				echo"<option value='$dateValue' selected>$dateValue</option>";
				$SearchRows.=" and ((M.Date>'$StartDate' and M.Date<'$EndDate') OR M.Date='$StartDate' OR M.Date='$EndDate')";
				}
			else{
				echo"<option value='$dateValue'>$dateValue</option>";
				}
			}while($dateRow = mysql_fetch_array($date_Result));
		echo"</select>&nbsp;";
		}
	$providerSql = mysql_query("SELECT M.CompanyId,P.Forshort,P.Letter 
			FROM $DataIn.ck1_rkmain M,$DataIn.trade_object P WHERE M.CompanyId=P.CompanyId $SearchRows GROUP BY M.CompanyId ORDER BY P.Letter",$link_id);
	if($providerRow = mysql_fetch_array($providerSql)){
		echo "<select name='CompanyId' id='CompanyId' onchange='zhtj(this.name)'>";
		echo"<option value='' selected>全部</option>";
		do{
			$Letter=$providerRow["Letter"];
			$Forshort=$providerRow["Forshort"];
			$Forshort=$Letter.'-'.$Forshort;
			$thisCompanyId=$providerRow["CompanyId"];
			if($CompanyId==$thisCompanyId){
				echo"<option value='$thisCompanyId' selected>$Forshort </option>";
				$SearchRows.=" and M.CompanyId='$thisCompanyId'";
				}
			else{
				echo"<option value='$thisCompanyId'>$Forshort</option>";
				}
			}while ($providerRow = mysql_fetch_array($providerSql));
		echo"</select>&nbsp;";
		}
	}
//检查进入者是否采购
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT M.BillNumber,M.Date,M.Remark,		  
    S.Id,S.Mid,S.StockId,S.StuffId,S.Qty,S.gys_Id,S.Locks,S.Type AS RkType,S.llQty AS outQty,S.llSign,
	D.StuffCname,D.TypeId,D.Picture,
	G.OrderQty,G.FactualQty+G.AddQty AS cgQty,G.POrderId,          
	Y.ProductId,Y.OrderPO,Y.Qty as PQty,Y.PackRemark,Y.sgRemark,Y.ShipType,
	PI.Leadtime,
	MP.Name AS Position,
	U.Name AS UnitName,
	P.Forshort,  
	C.Forshort AS Client,
	CP.cName,CP.TestStandard,
	GM.PurchaseID,GM.Id AS GMId, 
	IFNULL(F.AutoSign,-1) as AutoSign
FROM $DataIn.ck1_rksheet S
LEFT JOIN $DataIn.ck1_rkmain M ON S.Mid=M.Id 
LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId
LEFT JOIN $DataIn.stuffunit U ON U.Id=D.Unit 
LEFT JOIN $DataIn.base_mposition MP ON MP.Id=T.Position 
LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId 
LEFT JOIN $DataIn.cg1_stockmain GM ON GM.Id=G.Mid
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=G.POrderId 
LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=Y.Id
LEFT JOIN $DataIn.productdata CP ON CP.ProductId=Y.ProductId
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=CP.CompanyId
Left Join $DataIn.cw1_fkoutsheet F On F.StockId = S.StockId
WHERE 1 $SearchRows ORDER BY M.Date DESC,M.Id DESC";
//echo $mySql;
$hasExcute = '';
$skip = false;
$mainResult = mysql_query($mySql." $PageSTR",$link_id);
if($mainRows = mysql_fetch_array($mainResult)){
	$tbDefalut=0;
	$midDefault="";
	do{
		$m=1;
		//主单信息
		$LockRemark="";
		$Mid=$mainRows["Mid"];
		$Date=$mainRows["Date"];

        /******************验厂过滤********************/
        $groupLeaderSql = "SELECT GroupLeader From $DataIn.staffgroup WHERE GroupId = 701 ";
        $groupLeaderResult = mysql_query($groupLeaderSql);
        $groupLeaderRow = mysql_fetch_assoc($groupLeaderResult);
        $Leader = $groupLeaderRow['GroupLeader'];
        if($hasExcute == ''){
            $skip = skipData($Leader, $Date, $DataIn, $DataPublic, $link_id);
        }

        if($FactoryCheck == 'on' and $skip){
            continue;
        }else if($FactoryCheck == 'on'){
            $Date = substr($Date, 0, 10);
        }
        /***************************************/

		$BillNumber=$mainRows["BillNumber"];
		$gys_Id=$mainRows["gys_Id"];
		$Remark=$mainRows["Remark"]==""?"":"title='$Remark'";
		//检查是否存在文件
		if($BillNumber!=""){
				  $FilePath1="../download/deliverybill/$BillNumber.jpg";
				  if(file_exists($FilePath1)){
						 $BillNumber="<a href='$FilePath1' target='_blank'>$BillNumber</a>";
				}
			  else {
					if(is_numeric($BillNumber)){
						$GysSql=mysql_query("SELECT Id,GysNumber FROM $DataIn.gys_shmain 
						WHERE BillNumber=$BillNumber",$link_id);
						if(mysql_num_rows($GysSql) > 0)
						{
							$GysMid=mysql_result($GysSql,0,"Id");
							$GysNumber=mysql_result($GysSql,0,"GysNumber");
						}
						$MidSTR=anmaIn($GysMid,$SinkOrder,$motherSTR);
						$BillNumber="<a href='../supplier/shorder_view.php?f=$MidSTR' target='_blank'>$GysNumber</a>";

					 }

			   }
		  }else{
			  $BillNumber ="&nbsp;";
		  }
		$Forshort=$mainRows["Forshort"]==""?"&nbsp;":$mainRows["Forshort"];
		$upMian="<img location.href=\"#\"' style='CURSOR: pointer' onclick='upMainData(\"ck_rk_upmain\",$Mid)' src='../images/edit.gif' title='更新入库主单资料' width='13' height='13'>";
		//明细资料
		$StuffId=$mainRows["StuffId"];
		$RkType = $mainRows["RkType"];		 //入库类型

		if($StuffId!=""){

			$checkidValue=$mainRows["Id"];
			$StuffCname=$mainRows["StuffCname"];
			$UnitName=$mainRows["UnitName"]==""?"&nbsp;":$mainRows["UnitName"];
			$Qty=$mainRows["Qty"];
			$cgQty=$mainRows["cgQty"];
			$outQty=$mainRows["outQty"];
			$Locks=$mainRows["Locks"];
			$Picture=$mainRows["Picture"];
			$TypeId=$mainRows["TypeId"];
			$StockId=$mainRows["StockId"];

			if(strlen($StockId)>10 && $RkType == 1){

		    $checkComboxResult = mysql_query("SELECT (AddQty+FactualQty) AS OrderQty FROM $DataIn.cg1_stuffcombox WHERE StockId = $StockId",$link_id);
            if($checkComboxRow = mysql_fetch_array($checkComboxResult)){
                $cgQty  =  $checkComboxRow["OrderQty"];

            }


			if($outQty == $Qty){
				$outBgColor  ="class='greenB'";
				$LockRemark  = "数量全部出库，不能修改!";
			}else if($outQty>$Qty){
				$outBgColor  ="class='redB'";
			}else{
			    $outBgColor  ="class='yellowB'";
			}
			if($outQty ==0 )$outQty="&nbsp;";

				//请款方式
				$Autobgcolor="";
				$AutoSign=$mainRows["AutoSign"];
				switch($AutoSign)
				{
					case -1:
					    $AutoSign="&nbsp;";
						break;
					case 2:
				    	$AutoSign="<image src='../images/AutoCheckB.png' style='width:20px;height:20px;' title='人工请款自动通过'/>";
						$LockRemark="人工请款自动通过";
					break;
					case 4:
				    	$AutoSign="<image src='../images/AutoCheck.png' style='width:20px;height:20px;' title='系统请款自动通过'/>";
						$LockRemark="系统请款自动通过";
					//$Autobgcolor="bgcolor='##FF0000'";
					break;
					default:
					    $LockRemark="请款.....";
						$AutoSign="&nbsp;";
					break;

			    }

				$PurchaseID=$mainRows["PurchaseID"];

				$GMId=$mainRows["GMId"];
				$MidSTR=anmaIn($GMId,$SinkOrder,$motherSTR);
			    $PurchaseIDStr="<a href='../model/subprogram/purchaseorder_view.php?f=$MidSTR' target='_blank'>$PurchaseID</a>";
				$Position=$mainRows["Position"]==""?"未设置":$mainRows["Position"];
				$rkTemp=mysql_query("SELECT SUM(Qty) AS Qty FROM $DataIn.ck1_rksheet WHERE StockId='$StockId' order by Id",$link_id);
				$rkQty=mysql_result($rkTemp,0,"Qty");
				$rkQty=$rkQty==""?0:$rkQty;
				if($rkQty==$cgQty){
					$rkBgColor="class='greenB'";
					}
				else{
					$rkBgColor="class='redB'";
					}
				$ProductId=$mainRows["ProductId"];
                $POrderId=$mainRows["POrderId"];
                $OrderPO=$mainRows["OrderPO"];
                $PQty=$mainRows["PQty"];
		        $PackRemark=$mainRows["PackRemark"];
		        $sgRemark=$mainRows["sgRemark"];
		        $ShipType=$mainRows["ShipType"];
		        $Leadtime=$mainRows["Leadtime"];
                $cName=$mainRows["cName"];
	            $Client=$mainRows["Client"];
                $TestStandard=$mainRows["TestStandard"];
		        include "../admin/Productimage/getPOrderImage.php";
		        $OrderQty=$mainRows["OrderQty"];
		        $llQty="&nbsp;";$llBgColor="";
                if ($StockId>0){
	                $checkLLSql=mysql_fetch_array(mysql_query("SELECT SUM(Qty) AS Qty FROM $DataIn.ck5_llsheet WHERE StockId='$StockId'",$link_id));
	                $llQty=$checkLLSql["Qty"]==0?$llQty:$checkLLSql["Qty"];
	                $llBgColor=$llQty==$OrderQty?"class='greenB'":"";
                }
                //echo "SELECT SUM(Qty) AS Qty FROM $DataIn.ck5_llsheet WHERE StockId='$StockId'";




		    $showPurchaseorder="<img src='../images/showtable.gif' onClick='ShowOrHide(StuffList$i,showtable$i,StuffList$i,\"$POrderId\",$i,\"\");' name='showtable$i' title='显示或隐藏订单信息资料.' width='13' height='13' style='CURSOR: pointer'/>";
			$StuffListTB="<table width='$tableWidth' border='0' cellspacing='0' 
			id='StuffList$i' style='display:none'>
            <tr bgcolor='#B7B7B7'>
			<td class='A0111' ><br> &nbsp;<span class='redB'>订单PO：</span>$OrderPO&nbsp;&nbsp;<span class='redB'>业务单流水号：</span>$POrderId ($Client : $TestStandard)&nbsp;&nbsp;<span class='redB'>数量：</span>$PQty &nbsp; &nbsp;<span class='redB'>订单备注：</span>$PackRemark &nbsp;&nbsp;<span class='redB'>出货方式：</span>$ShipType &nbsp;&nbsp;<span class='redB'>生管备注：</span>$sgRemark &nbsp;&nbsp;<span class='redB'>PI交期：</span>$Leadtime</td>
				</tr>
				<tr bgcolor='#B7B7B7'>
				<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";
           }else{
              $OrderQty="&nbsp;";
	          $llQty="&nbsp;";
	          $llBgColor="";
	          $PurchaseIDStr = "&nbsp;";
	          $AutoSign = "&nbsp;";
	          if($RkType == 2){
		          $LockRemark = "备品转入入库，锁定操作";
	          }
           }


			//配件品检报告qualityReport
           // include "../model/subprogram/stuff_get_qualityreport.php";
            include "../public/stuff_quality_report_get.php";
            include "../model/subprogram/stuff_Property.php";//配件属性
            $d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
		    //检查是否有图片
		    include "../model/subprogram/stuffimg_model.php";




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
				if(($BuyerId==$Login_P_Number && ($Keys & mUPDATE || $Keys & mDELETE)) || $Keys & mLOCK){//有权限
					if($LockRemark!=""){
						$Choose="&nbsp;&nbsp;&nbsp;<img src='../images/lock.png' title='$LockRemark' width='15' height='15'/>";
						}
					else{
						$Choose="<input name='checkid[]' type='checkbox' id='checkid$i' value='$checkidValue' disabled><img src='../images/unlock.png' width='15' height='15'/>";
						}
					}
				else{//无权限
					$Choose="&nbsp;&nbsp;&nbsp;<img src='../images/lock.png' title='锁定操作!' width='15' height='15'/>";
					}
				}
			$Sid=anmaIn($StockId,$SinkOrder,$motherSTR);
			////////////////////////////////////////////////////
			if($tbDefalut==0 && $midDefault==""){//首行
				//并行列
				echo"<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'><tr>";
				echo"<td scope='col' class='A0111' width='$Field[$m]' align='center'>$upMian</td>";//更新
				$unitWidth=$tableWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Date</td>";//入库日期
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center' $Remark>$BillNumber</td>";		//下单备注
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Forshort</td>";
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				//echo"<td width='$unitWidth' class='A0101'>";
				echo"<td width='' class='A0101'>";
				$midDefault=$Mid;
				}
			if($midDefault!="" && $midDefault==$Mid){//同属于一个主ID，则依然输出明细表格
				$m=9;
				echo"<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
				echo"<tr onmousedown='ClickKeyCheck(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' 
					onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
					onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
				$unitFirst=$Field[$m]-1;

				echo"<td class='A0001' width='$unitFirst'  align='center' $tdBGCOLOR>$Choose  $showPurchaseorder</td>";//选项
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$j</td>";			//序号
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$StuffId</td>";	//配件ID
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]'>$StuffCname</td>";	//配件
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$UnitName</td>";	//单位
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$qualityReport</td>";	//品检报告
				$m=$m+2;
				$cgQty = $cgQty==""?"&nbsp;":$cgQty;
				echo"<td class='A0001' width='$Field[$m]' align='right'>$cgQty</td>";		//需求数量
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'><div $rkBgColor>$Qty</div></td>";		//入库数量
				$m=$m+2;//
				echo"<td class='A0001' width='$Field[$m]' align='right'><div $outBgColor>$outQty</div></td>";//出库数量
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'><div $llBgColor>$llQty</div></td>";		//领料数量

				$m=$m+2;
				//请款方式
				echo"<td class='A0001' width='$Field[$m]' align='center'>$AutoSign</td>";

				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$PurchaseIDStr</td>";		//采购单号
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='center'><a href='ck_rk_list.php?Sid=$Sid' target='_blank'>$StockId</div></td>";//需求流水号
				$m=$m+2;
				echo"<td  width=''  align='center'>$Position</td>";	//配件存储位置
				echo"</tr></table>";
				$i++;
				$j++;
				}
			else{
				//新行开始
				echo"</td></tr></table>";//结束上一个表格
				//并行列
				echo"<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'><tr>";
				echo"<td scope='col' class='A0111' width='$Field[$m]' align='center'>$upMian</td>";	//更新
				$unitWidth=$tableWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Date</td>";	//入库日期
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'  $Remark>$BillNumber</td>";		//下单备注
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Forshort</td>";
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				//并行宽
				//echo"<td width='$unitWidth' class='A0101'>";
				echo"<td width='' class='A0101'>";
				$midDefault=$Mid;
				echo"<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
				echo"<tr onmousedown='ClickKeyCheck(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' 
					onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
					onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
				$unitFirst=$Field[$m]-1;
				echo"<td class='A0001' width='$unitFirst'  align='center' $tdBGCOLOR>$Choose $showPurchaseorder</td>";//选项
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$j</td>";			//序号
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$StuffId</td>";	//配件ID
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]'>$StuffCname</td>";	//配件
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$UnitName</td>";	//单位
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$qualityReport</td>";	//品检报告
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'>$cgQty</td>";		//需求数量
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'><div $rkBgColor>$Qty</div></td>";		//入库数量
				$m=$m+2;


				echo"<td class='A0001' width='$Field[$m]' align='right'><div $outBgColor>$outQty</div></td>";	//出库数量
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'><div $llBgColor>$llQty</div></td>";		//领料数量
				//请款方式
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$AutoSign</td>";		//采购单号

				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$PurchaseIDStr</td>";		//采购单号
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='center'><a href='ck_rk_list.php?Sid=$Sid' target='_blank'>$StockId</div></td>";	//需求流水号
				$m=$m+2;
				echo"<td  width=''  align='center'>$Position</td>";	//配件存储位置
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
echo '</div>';
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>
