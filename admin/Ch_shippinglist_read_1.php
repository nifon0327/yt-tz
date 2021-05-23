<?php   
include "../model/modelhead.php";
$From=$From==""?"read":$From;
$tableMenuS=750;
ChangeWtitle("$SubCompany 已出订单列表");
$funFrom="ch_shippinglist";
$nowWebPage=$funFrom."_read";
$Pagination=$Pagination==""?0:$Pagination;
$ShipTypeFlag=$ShipTypeFlag==""?0:$ShipTypeFlag;
$Page_Size = 100;
if($DeliverySign==0 && $DeliverySign!=""){
	 $Th_Col="选项|60|序号|40|出货流水号|70|客户|90|销售模式|60|Invoice名称|110|Invoice|80|外箱标签|60|出货金额|70|出货数量|70|已出数量|70|出货日期|70|货运信息|120|How to Ship|80|报关公司|80|报关单|120|快递签收单|120|Forward签收单|120|发票单|120|出货分类|60|备注|140|操作员|50";
	 $ActioToS="1,3,36,26,28,34,35,7,8,59,99,100,101,102,112,114,124,153,180";
	 $sumCols="8";			//求和列,需处理
	 $ColsNumber=17;	
    }
else{
	 $Th_Col="选项|60|序号|40|出货流水号|70|客户|90|销售模式|60|Invoice名称|110|Invoice|80|外箱标签|60|出货金额|80|出货日期|70|货运信息|120|How to Ship|80|报关公司|80|报关单|120|快递签收单|120|Forward签收单|120|发票单|120|出货分类|60|备注|140|操作员|50";
	 $ActioToS="1,3,36,26,28,34,35,7,8,59,99,100,101,102,114,124,153,174,180";
	 $sumCols="8";			//求和列,需处理
	 $ColsNumber=17;	
    }
//步骤3：
include "../model/subprogram/read_model_3.php";
//echo 'CompanyId：' . $CompanyId;
//步骤4：需处理-条件选项
if($From!="slist"){
	//月份
	$SearchRows="";	
	$SearchRows=" and M.Estate='0'";	
	$date_Result = mysql_query("SELECT M.Date FROM $DataIn.ch1_shipmain M WHERE 1 $SearchRows GROUP BY DATE_FORMAT(M.Date,'%Y-%m') ORDER BY M.Date DESC",$link_id);
	if($dateRow = mysql_fetch_array($date_Result)) {
		echo"<select name='chooseDate' id='chooseDate' onchange='RefreshPage(\"$nowWebPage\")'>";
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
	//客户
	$clientResult = mysql_query("SELECT M.CompanyId,C.Forshort 
	FROM $DataIn.ch1_shipmain M,$DataIn.trade_object C 
	WHERE 1 AND C.CompanyId=M.CompanyId $SearchRows GROUP BY M.CompanyId ORDER BY M.CompanyId",$link_id);
	if($clientRow = mysql_fetch_array($clientResult)) {
		echo"<select name='CompanyId' id='CompanyId' onchange='RefreshPage(\"$nowWebPage\")'>";
		echo"<option value='' selected>全部客户</option>";
		do{			
			$thisCompanyId=$clientRow["CompanyId"];
			$Forshort=$clientRow["Forshort"];
			if($CompanyId==$thisCompanyId){
				echo"<option value='$thisCompanyId' selected>$Forshort</option>";
				$SearchRows.=" and M.CompanyId='$thisCompanyId' ";
				}
			else{
				echo"<option value='$thisCompanyId'>$Forshort</option>";					
				}
			}while ($clientRow = mysql_fetch_array($clientResult));
		echo"</select>&nbsp;";
		}
		//订单类型
		echo"<select name='ShipTypeFlag' id='ShipTypeFlag' onchange='RefreshPage(\"$nowWebPage\")'>";
		switch ($ShipTypeFlag){
			 case '1':
			   $SearchRows.=" and M.ShipType='' ";
			   $TypeSel1="selected";
				break;		
			case '2':
			    $SearchRows.=" and M.ShipType='replen' ";
				$TypeSel2="selected";
				break;			
			case '3': 
			    $SearchRows.=" and M.ShipType='credit' ";
				$TypeSel3="selected";
				break;
			case '4':
			    $SearchRows.=" and M.ShipType='debit' ";
				$TypeSel4="selected";
				break;
		    default:
			   $TypeSel0="selected";
			    break;
		}		
		echo"<option value='0' $TypeSel0>出货分类</option>";
		echo"<option value='1' $TypeSel1>出&nbsp;&nbsp;货</option>";
		echo"<option value='2' $TypeSel2 style='background-color:#FEA085;'>补&nbsp;&nbsp;货</option>";
		echo"<option value='3' $TypeSel3 style='background-color:#FFFF93;'>扣&nbsp;&nbsp;款</option>";
		echo"<option value='4' $TypeSel4 style='background-color:#6ACFFF;'>其它收款</option>";
		echo"</select>&nbsp;";	
		
		//＝＝＝内销/外销分类
		$SaleMode=$SaleMode==""?0:$SaleMode;
		$StrSign='SaleModeSign'.$SaleMode;
		$$StrSign='selected';
		echo"<select name='SaleMode' id='SaleMode' onchange='RefreshPage(\"$nowWebPage\")'>";
		echo"<option value='0'  $SaleModeSign0>全部</option>";
		echo"<option value='1'  $SaleModeSign1>内销</option>";
		echo"<option value='2'  $SaleModeSign2>外销</option>";
		echo"</select>&nbsp;";
		
		if ($SaleMode>0){
			  $SearchRows.=" AND C.SaleMode='$SaleMode' ";
		}
		
		//==========发货状态
		$DeliverySign=$DeliverySign==""?1:$DeliverySign;
		$StrSign='StrSign'.$DeliverySign;
		$$StrSign='selected';
		echo"<select name='DeliverySign' id='DeliverySign' onchange='RefreshPage(\"$nowWebPage\")'>";
		echo"<option value='1'  $StrSign1>全部</option>";
		echo"<option value='2' $StrSign0>库存</option>";
		echo"</select>&nbsp;";
		//echo $DeliverySign;
		if($DeliverySign!=""){
		  switch($DeliverySign){
		   case 0:$SearchRows.=" AND M.Id IN (SELECT ShipId FROM $DataIn.ch1_shipout)";
		       break;
		   default:$SearchRows.="";
		      break;
		  }
		}
	}
	else{
		$SearchRows.=" and M.Estate='0'";	
	}
		   
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理

$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT 
M.Id,M.CompanyId,M.BankId,M.Number,M.InvoiceNO,M.InvoiceFile,M.Wise,M.Date,M.Estate,M.Locks,M.Sign,M.Remark,M.ShipType,M.Ship,M.Operator,C.Forshort,C.PayType,S.InvoiceModel,S.LabelModel,S.OutSign ,T.Type as incomeType,F.Forshort AS bgForshort,C.SaleMode,T.BgBillNum,T.Attached AS BgAttached 
FROM $DataIn.ch1_shipmain M
LEFT JOIN $DataIn.ch1_shiptypedata T ON T.Mid=M.Id 
LEFT JOIN $DataIn.freightdata  F ON F.CompanyId = T.CompanyId 
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
LEFT JOIN $DataIn.ch8_shipmodel S ON S.Id=M.ModelId 
WHERE 1 $SearchRows
ORDER BY M.Date DESC";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$d1=anmaIn("download/invoice/",$SinkOrder,$motherSTR);		
	do{
		$m=1;
		$LockRemark="";
		$OrderSignColor="";
		$theDefaultColor=$DefaultBgColor;
		$Id=$myRow["Id"];
		$CompanyId=$myRow["CompanyId"];
		$Number=$myRow["Number"];
		$Forshort=$myRow["Forshort"];
		
		$Forshort="<img location.href=\"#\"' style='CURSOR: hand' onclick='upMainData(\"ch_shippinglist_picture\",\"$Id\")' src='../images/edit.gif' title='更新附档' width='13' height='13'>
		<span class='yellowB'>$Forshort</span>";
		
		$InvoiceNO=$myRow["InvoiceNO"];
		$InvoiceFile=$myRow["InvoiceFile"];
		$BoxLable="<div class='redB'>未装箱</div>";
		//检查是否有装箱
		$LabelModel= $myRow["LabelModel"];
		$checkPacking=mysql_query("SELECT Id FROM $DataIn.ch2_packinglist WHERE Mid='$Id' LIMIT 1",$link_id);
		if($PackingRow=mysql_fetch_array($checkPacking)){
            $PId=$PackingRow["Id"];
			//加密参数
			$Parame1=anmaIn($Id,$SinkOrder,$motherSTR);
			$Parame2=anmaIn("Mid",$SinkOrder,$motherSTR);	
			if ($LabelModel==36  || $LabelModel==46 || $LabelModel==48 || $LabelModel==39 ) { 
				$BoxLable=$InvoiceFile==0?"&nbsp;":"<a href='ch_shippinglist_print.php?Parame1=$Parame1&Parame2=$Parame2&LablePos=1' target='_blank'>正</a>
				<a href='ch_shippinglist_print.php?Parame1=$Parame1&Parame2=$Parame2&LablePos=2' target='_blank'>侧</a>";
				
			}else {			
				$BoxLable=$InvoiceFile==0?"&nbsp;":"<a href='ch_shippinglist_print.php?Parame1=$Parame1&Parame2=$Parame2' target='_blank'>查看</a>";
			}
		}
        else $PId="";
		$Sign=$myRow["Sign"];//收支标记
		$f1=anmaIn($InvoiceNO,$SinkOrder,$motherSTR);
        if ($InvoiceFile==1){
             $dfname=urldecode($InvoiceNO);
	        $InvoiceFile=strlen($InvoiceNO)>20?"<a href=\"openorload.php?dfname=$dfname&Type=invoice\" target=\"download\">查看</a>":"<a href=\"openorload.php?d=$d1&f=$f1&Type=&Action=7\" target=\"download\" >查看</a>";
        }
        else{
	        $InvoiceFile="&nbsp;";
        }
		if($CompanyId==1001  && $Sign!=-1){
			$d2=anmaIn("download/invoice/mca/",$SinkOrder,$motherSTR);
			$InvoiceFile=$InvoiceFile==0?"&nbsp;":"<a href=\"openorload.php?d=$d2&f=$f1&Type=&Action=7\" target=\"download\">★</a>";
			}    
        $InvoiceModel=$myRow["InvoiceModel"];
		
		//if ($InvoiceModel==5){ //出MCA
		if ($InvoiceModel==5 || $CompanyId==1064){ //出MCA
                    $d2=anmaIn("download/invoice/mca/",$SinkOrder,$motherSTR);
                    $InvoiceFile.="&nbsp;&nbsp;<a href=\"openorload.php?d=$d2&f=$f1&Type=&Action=7\" target=\"download\">★</a>";
                }	
		 if($CompanyId==1079 && file_exists("../download/songbill/".$InvoiceNO.".pdf")){
              $d3=anmaIn("download/songbill/",$SinkOrder,$motherSTR);	
              $Forshort="<a href=\"openorload.php?d=$d3&f=$f1&Type=&Action=7\" target=\"download\">$Forshort</a>";
            }

        $OutSign= $myRow["OutSign"];
        $OutPdfFile='';
  
        if ($CompanyId==100426){
             $outfname=urldecode($InvoiceNO);
             $OutPdfFile= "&nbsp;&nbsp;&nbsp;&nbsp;<a href='Ch_shippinglist_shipinfo.php?Id=$Id&CheckSign=HK' target='_blank' >HK</a>";
        }else{
	        if ($OutSign==9){
	        $OutPdfFile= "&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"ch_shippinglist_tooldpdf.php?Id=$Id\" target=\"_blank\">旧版</a>";
            }
        }
        
       $SaleMode	=$myRow["SaleMode"] ;
	   $SaleModeStr="";
	   switch($SaleMode){
	      case "1": $SaleModeStr="<img src='../images/salein.jpg' width='18' height='18'>";break;
	      case "2": $SaleModeStr="外销";break;
	      case "2": $SaleModeStr="&nbsp;";break;
	     }
		$incomeType=$myRow["incomeType"]==1?"<span class='redB'>报关</span>":"&nbsp;";
		$bgForshort=$myRow["bgForshort"];
		$BgBillNum=$myRow["BgBillNum"];
		$BgAttached=$myRow["BgAttached"];
		$bgForshort="<img location.href=\"#\"' style='CURSOR: hand' onclick='upMainData(\"Ch_shippinglist_upshipType\",\"$Id\")' src='../images/edit.gif' title='更新报关公司' width='13' height='13'>
		<span class='yellowB'>$bgForshort</span>";	
		
		$BankId=$myRow["BankId"];


	   $CheckNumberResult=mysql_fetch_array(mysql_query("SELECT  * FROM $DataIn.ch1_shipfile WHERE ShipId=$Id",$link_id));
	   $expressBackNumber=$CheckNumberResult["ExpressNum"];
	   $ForwardBackNumber=$CheckNumberResult["ForwardNum"];
	   $BillNum=$CheckNumberResult["BillNum"];
	   if($expressBackNumber!=""){
	          $d5=anmaIn("download/expressreback/",$SinkOrder,$motherSTR);
			  $f5=anmaIn($Id."_".$expressBackNumber,$SinkOrder,$motherSTR);
	          $expressBackNumber="&nbsp;&nbsp;<a href=\"openorload.php?d=$d5&f=$f5&Type=&Action=7\" target=\"download\">$expressBackNumber</a>";
	      }
	   else {
	        $expressBackNumber="&nbsp;";
	      }
	   if($ForwardBackNumber!=""){
	          $d6=anmaIn("download/forwardreback/",$SinkOrder,$motherSTR);
			  $f6=anmaIn($Id."_".$ForwardBackNumber,$SinkOrder,$motherSTR);
	          $ForwardBackNumber="&nbsp;&nbsp;<a href=\"openorload.php?d=$d6&f=$f6&Type=&Action=7\" target=\"download\">$ForwardBackNumber</a>";
	      }
	   else {
	        $ForwardBackNumber="&nbsp;";
	      }
	      
	    if($BillNum!=""){
	          $d7=anmaIn("download/billback/",$SinkOrder,$motherSTR);
			  $f7=anmaIn($CompanyId."_".$BillNum,$SinkOrder,$motherSTR);
	          $BillNum="&nbsp;&nbsp;<a href=\"openorload.php?d=$d7&f=$f7&Type=&Action=7\" target=\"download\">$BillNum</a>";
	      }
	   else {
	        $BillNum="&nbsp;";
	      }
	   
	    $BgBillNum = $BgBillNum==""?$BgAttached:$BgBillNum;
	    if($BgBillNum!=""){
	         if(file_exists("../download/bgbillback/".$Id."_".$BgBillNum.".pdf")){
		         $d8=anmaIn("download/bgbillback/",$SinkOrder,$motherSTR);
			     $f8=anmaIn($Id."_".$BgBillNum,$SinkOrder,$motherSTR);
		         
	         }else{
		         $d8=anmaIn("download/shiptype/",$SinkOrder,$motherSTR);
			     $f8=anmaIn($Id,$SinkOrder,$motherSTR);
	         } 
	          $BgBillNum="&nbsp;&nbsp;<a href=\"openorload.php?d=$d8&f=$f8&Type=&Action=7\" target=\"download\">$BgBillNum</a>";
	      }
	      
	      
       $expressBack = "<img location.href=\"#\"' style='CURSOR: hand' onclick='upMainData(\"Ch_shippinglist_expressBack\",\"$Id\")' src='../images/edit.gif' title='上传快递签收单' width='13' height='13'><span class='yellowB'>$expressBackNumber</span>";	

       $ForwardBack = "<img location.href=\"#\"' style='CURSOR: hand' onclick='upMainData(\"Ch_shippinglist_ForwardBack\",\"$Id\")' src='../images/edit.gif' title='上传Forward签收单' width='13' height='13'><span class='yellowB'>$ForwardBackNumber</span>";	

       $BillNum = "<img location.href=\"#\"' style='CURSOR: hand' onclick='upMainData(\"Ch_shippinglist_billnum\",\"$Id\")' src='../images/edit.gif' title='上传发票' width='13' height='13'><span class='yellowB'>$BillNum</span>";	
       $BgBillNum = "<img location.href=\"#\"' style='CURSOR: hand' onclick='upMainData(\"Ch_shippinglist_upshipType\",\"$Id\")' src='../images/edit.gif' title='上传报关单' width='13' height='13'><span class='yellowB'>$BgBillNum</span>";	
		
		$BankId="<img location.href=\"#\"' style='CURSOR: hand' onclick='upMainData(\"Ch_shippinglist_upbankID\",\"$Id\")' src='../images/edit.gif' title='更新银行账号' width='13' height='13'>
		<span class='yellowB'>$BankId</span>";	
		
		$Ship=$myRow["Ship"];
		$shipTypeResult = mysql_query("SELECT Id,Name FROM $DataPublic.ch_shiptype WHERE Id='$Ship'",$link_id);
		if($shipTypeRow = mysql_fetch_array($shipTypeResult)){
		      $ShipName=$shipTypeRow["Name"];
              $Ship="<image src='../images/ship$Ship.png' style='width:20px;height:20px;' title='$ShipName'/>";
          }
          else{
	        $Ship="";  
          }

		$Ship=$Ship=""?"&nbsp;":$Ship;
		$Ship="<img location.href=\"#\"' style='CURSOR: hand' onclick='upMainData(\"Ch_shippinglist_upship\",\"$Id\")' src='../images/edit.gif' title='更新出货方式' width='13' height='13'>
		<span class='yellowB'>$Ship</span>";	
		
		$ShipType=$myRow["ShipType"];
		switch ($ShipType){
			case 'replen':
				$ShipType="补货"; 
				$shipColor=" bgcolor='#FEA085' ";
				break;			
			case 'credit': 
				$ShipType="扣款"; 
				$shipColor=" bgcolor='#FFFF93' ";
				break;
			case 'debit':
				$ShipType="其它收款"; 
				$shipColor=" bgcolor='#6ACFFF' ";
				break;
		    default:
			    $ShipType="出货"; 
				$shipColor="";
		}
		 $ColbgColor=$shipColor;//前面选项设置订单类型颜色
		$Remark=$myRow["Remark"]==""?"&nbsp;":$myRow["Remark"];
		$Remark="<img location.href=\"#\"' style='CURSOR: hand' onclick='upMainData(\"Ch_shippinglist_upmain\",\"$Id\")' src='../images/edit.gif' title='更新备注' width='13' height='13'>
		<span class='yellowB'>$Remark</span>";	
		if($myRow["Wise"]==""  && $PId!="")$theDefaultColor="#FFA6D2";//未填货运信息以及未装箱的显示粉红色
       
		$Wise=$myRow["Wise"]==""?"&nbsp;":$myRow["Wise"];
		$Date=$myRow["Date"];
		$Locks=$myRow["Locks"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		//出货金额
		
		$checkAmount=mysql_fetch_array(mysql_query("SELECT SUM(Qty*Price) AS Amount FROM $DataIn.ch1_shipsheet WHERE Mid='$Id'",$link_id));
		$Amount=$checkAmount["Amount"]*$Sign;
		$Amount=sprintf("%.2f",$Amount);
		if($Amount<0){
			$Amount="<div class='redB'>$Amount</div>";
			}
                  
		         
		$showPurchaseorder="<img onClick='sOrhOrder(StuffList$i,showtable$i,StuffList$i,\"$Id\",$i);' name='showtable$i' src='../images/showtable.gif' title='显示或隐藏出货订单明细.' width='13' height='13' style='CURSOR: hand'>";
		$StuffListTB="
			<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";
		//检查收款情况
		 $checkShipAmount=mysql_query("SELECT SUM(S.Amount) AS ShipAmount
		FROM $DataIn.cw6_orderinsheet S 
		LEFT JOIN $DataIn.cw6_orderinmain M ON M.Id=S.Mid
		WHERE S.chId='$Id' GROUP BY S.chId",$link_id);
		if(mysql_num_rows($checkShipAmount) > 0)
		{
			$ShipAmount=mysql_result($checkShipAmount,0,"ShipAmount");
			$LockRemark=$Login_P_Number==10868?"":"记录已经结付，强制锁定操作！";
			$ShipType="<a href='ch_shippinglist_toexcel.php?ActionId=114&Id=$Id' target='_blank'  title='导出invoice资料.'>$ShipType</a>";
		}
		$ShipAmount=$ShipAmount==""?0:round($ShipAmount,2);
		if(sprintf("%.2f",$Amount)-sprintf("%.2f",$ShipAmount)>0){//出货金额与收款金额一致，则为已收款
			 if($myRow["PayType"]==1){
				$BoxLable="<span class=\"redB\">未收款</span>";
				$OrderSignColor="bgColor='#F00'";
				}
			}
		
		if($Number==536 ){
			$LockRemark ="";
		}
		
		if($DeliverySign==0){
			//出货数量+提货数量
			 $ShipResult=mysql_query("SELECT SUM(Qty) AS ShipQty FROM $DataIn.ch1_shipsheet WHERE Mid='$Id'",$link_id);
			 $ShipQty=mysql_result($ShipResult,0,"ShipQty");
			 $DeliveryResult=mysql_query("SELECT SUM(DeliveryQty) AS DeliveryQty FROM $DataIn.ch1_deliverysheet WHERE ShipId='$Id'",$link_id);
			 $DeliveryQty=mysql_result($DeliveryResult,0,"DeliveryQty");
			 $DeliveryQty=$DeliveryQty==""?0:$DeliveryQty;
			 if($DeliveryQty<$ShipQty)
				$DeliveryQty="<div class='yellowB'>$DeliveryQty</div>";
			 else 
				$DeliveryQty="<div class='greenB'>$DeliveryQty</div>";
		     $ValueArray=array(
			   array(0=>$Number,1=>"align='center' $NumberColor"),
			   array(0=>$Forshort),
			   array(0=>$SaleModeStr,	1=>"align='center'"),
			   array(0=>$InvoiceNO),
			   array(0=>$InvoiceFile . $OutPdfFile,1=>"align='center'"),
			   array(0=>$BoxLable,1=>"align='center'"),
			   array(0=>$Amount,	1=>"align='right'"),
			   array(0=>$ShipQty,	1=>"align='right'"),
			   array(0=>$DeliveryQty,	1=>"align='right'"),
			   array(0=>$Date,1=>"align='center'"),
			   array(0=>$Wise),
			   	
			   array(0=>$Ship),	
			   array(0=>$bgForshort),
			   array(0=>$BgBillNum),
			   array(0=>$expressBack),	
			   array(0=>$ForwardBack),	
			   array(0=>$BillNum),	
			   array(0=>$ShipType,1=>"align='center'"),
			   array(0=>$Remark),
			   array(0=>$Operator,1=>"align='center'")
			  );
		     }
	    else{
		     $ValueArray=array(
			   array(0=>$Number,1=>"align='center' $NumberColor"),
			   array(0=>$Forshort),
			   array(0=>$SaleModeStr,	1=>"align='center'"),
			   array(0=>$InvoiceNO),
			   array(0=>$InvoiceFile . $OutPdfFile,1=>"align='center'"),
			   array(0=>$BoxLable,1=>"align='center'"),
			   array(0=>$Amount,	1=>"align='right'"),
			   array(0=>$Date,1=>"align='center'"),
			   array(0=>$Wise),
			   array(0=>$Ship),	
			   array(0=>$bgForshort),
			   array(0=>$BgBillNum),
			   array(0=>$expressBack),	
			   array(0=>$ForwardBack),	
			   array(0=>$BillNum),	
			   array(0=>$ShipType,1=>"align='center'"),
			   array(0=>$Remark),
			   array(0=>$Operator,1=>"align='center'")
			);
		  }
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		echo $StuffListTB;
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
  	}
//步骤7：//
List_Title($Th_Col,"0",0);
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>
