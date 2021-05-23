<style type="text/css">
.list{position:relative;color:#FF0000;}
.list span img{ /*CSS for enlarged image*/
border-width: 0;
padding: 2px; width:100px;
}
.list span{ 
position: absolute;
padding: 3px;
border: 1px solid gray;
visibility: hidden;
background-color:#FFFFFF;
}
.list:hover{
background-color:transparent;
}
.list:hover span{
visibility: visible;
top:0; left:28px;
}
</style>
<?php 
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=38;
$tableMenuS=1500;
ChangeWtitle("$SubCompany 交易对象列表审核");
$funFrom="tradeobject";
$From=$From==""?"m":$From;
$Th_Col="选项|50|序号|35|交易属性|70|类型|40|编号|50|简称|140|Logo|50|货币|40|电话|120|传真|120|网址|40|联系人|100|手机|100|快递帐号|150|国家|130|出货数量|60|最后出货日期|80|我司联系人|60|Price Term|150|销售模式|60|中国信保|120|收款方式|150|付款方式|60|收款帐号|120|胶框图|50|提示图|50|评审|50|营业<br>执照|50|税务<br>登记证|50|开户<br>许可证|50|纳税人<br>认定书|50|付款<br>委托书|50|合作<br>协议|50|ROSH<br>报告|50|增值税率|50|加税率|50|备注|50|更新备注|180|退回原因|180|图片职责|120|状态|40|更新日期|80|操作员|50";
$Type=3;//供应商
//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 200;							//每页默认记录数量
$ActioToS="15,17";
$nowWebPage=$funFrom."_m";

$auditors = "10006,10231";
include "../model/subprogram/read_model_3.php";

$SearchRows = "";
if($From!="slist"){
   $buyerSql = mysql_query("SELECT M.Number,M.Name  FROM $DataIn.staffmain M  WHERE  M.Number IN ($auditors)",$link_id);
   if($buyerRow = mysql_fetch_array($buyerSql)){
		echo"<select name='Auditor' id='Auditor' onchange='document.form1.submit()'>";
		echo "<option value=''  selected>审核人</option>";
		do{
			$thisNumber=$buyerRow["Number"];
			$thisName=$buyerRow["Name"];
			
			if ($Auditor==$thisNumber || ($Auditor=='' && $thisNumber==$Login_P_Number)){
				echo "<option value='$thisNumber'  selected>$thisName</option>";
				   if ($thisNumber=='10006'){
					      $SearchRows= " AND A.ObjectSign!=2";
				   }else{
					      $SearchRows= " AND A.ObjectSign=2";
				   }
				}
			else{
				echo "<option value='$thisNumber' >$thisName </option>";
				}
			}while ($buyerRow = mysql_fetch_array($buyerSql));
		echo"</select>&nbsp;";
	  }
}		

//步骤5：
$TitlePre="<br>&nbsp;&nbsp;退回原因:<input type=\"text\" id=\"ReturnReasons\" name=\"ReturnReasons\" style=\"width:600\"><p>";
include "../model/subprogram/read_model_5.php";


$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
$mySql="SELECT 
A.Id,A.CompanyId,A.Letter,A.Forshort,A.Logo,A.ProviderType,A.Estate,A.Date,A.Operator,A.Locks,A.ExpNum,A.PayType,A.CompanySign,B.Tel,B.Fax,B.Website,B.Remark,B.Area,C.Symbol,A.Judge,A.PackFile,A.TipsFile,A.Prepayment,A.LimitTime,K.Title AS BankTitle,E.Name AS staff_Name ,A.PriceTerm,F.Name AS PayMode ,F.eName AS ePayMode,A.ObjectSign,A.UpdateReasons,A.ReturnReasons,A.ChinaSafeSign,A.ChinaSafe,
P.InvoiceTax,P.BusinessLicence,P.TaxCertificate,P.ProductionCertificate,P.BankPermit,P.SalesAgreement,P.PaymentOrder,P.TaxpayerIdentifi,T.Name AS AddValueTaxName,PA.Name AS GysPayMode,A.SaleMode 
FROM $DataIn.trade_object  A
LEFT JOIN $DataIn.companyinfo B ON B.CompanyId=A.CompanyId AND B.Type=8
LEFT JOIN $DataIn.currencydata C ON C.Id=A.Currency
LEFT JOIN $DataIn.staffmain E ON E.Number=A.Staff_Number
LEFT JOIN $DataIn.my2_bankinfo K ON K.Id=A.BankId 
LEFT JOIN $DataIn.clientpaymode F ON F.Id=A.PayMode
LEFT JOIN $DataIn.providersheet P ON P.CompanyId=A.CompanyId 
LEFT JOIN $DataIn.provider_addtax T ON T.Id = P.AddValueTax 
LEFT JOIN $DataIn.providerpaymode PA ON PA.Id = A.GysPayMode  
WHERE 1  $SearchRows  AND A.Estate=2 AND MOD(A.CompanySign,7)=0 ORDER BY A.Estate DESC ,A.Letter ";
//echo $SearchRows;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
       $d=anmaIn("download/providerfile/",$SinkOrder,$motherSTR);
	do{
		$m=1;
		$Id=$myRow["Id"];
		$CompanyId=$myRow["CompanyId"];   
		$logoName = $myRow["Logo"]; 
	    
	    if($logoName!=""){
	       $logoFilePath="../download/tradelogo/".$logoName;
	        $noStatue="onMouseOver=\"window.status='none';return true\"";
		    $logoFileSTR="<span class='list' >View<span><img src='$logoFilePath' $noStatue/></span>";     
		 }
        else{
            $logoFileSTR="&nbsp;";
        }
           
        $Judge=$myRow["Judge"];        
		//最后一次评审是否合格
        $ColbgColor="";
		$Results="";
        $viewRowSign=1;
        $ReviewResult=mysql_query("SELECT Results FROM $DataIn.providerreview WHERE CompanyId=$CompanyId ORDER BY Year DESC LIMIT 1",$link_id);
        if($Review_Row = mysql_fetch_array($ReviewResult)){
        	$Results=$Review_Row["Results"];
            }
		else{
        	$checkResults=mysql_fetch_array(mysql_query("SELECT Results FROM $DataIn.providersheet WHERE CompanyId='$CompanyId'  LIMIT 1",$link_id));
            $Results=$checkResults["Results"];
            }
		if($Results==4) $ColbgColor=" bgcolor='#FF0000' ";        
		if($ProviderTypeSign==97) {
			if ($Results==4) $viewRowSign=1; else $viewRowSign=0;
            }
		//加密
		if ($viewRowSign==1){
			$Idc=anmaIn("trade_object",$SinkOrder,$motherSTR);
			$Ids=anmaIn($Id,$SinkOrder,$motherSTR);		
			$Letter=$myRow["Letter"]==""?"":$myRow["Letter"]."-";
			//加密
			$Forshort="<a href='companyinfo_view.php?c=$Idc&d=$Ids' target='_blank'>$myRow[Forshort]</a>";
			$Symbol=$myRow["Symbol"];
            $EstateColor="";
			$checkEstate=mysql_query("SELECT Id FROM $DataIn.providerreview WHERE CompanyId='$CompanyId' AND Estate=2",$link_id);
			if($checkRow = mysql_fetch_array($checkEstate)){
				$EstateColor=" style='background:#EFE769' ";
                } 
			if($Judge!="")$Judge="<a href='providerdata_review_read.php?CompanyId=$CompanyId' target='_blank'>查看</a>";
			/*
			switch($myRow["ProviderType"]){
		    	case 0:$ProviderType="自购";break;
			  	case 1:$ProviderType="<span class='redB'>代购";break;
			  	case 2:$ProviderType="<span style='color:#FF33FF'>客供";break;
			  	case 99:$ProviderType="<span style='color:#AC516F'>禁用";break;
			     default: $ProviderType="&nbsp;";break;
				}
			*/
			switch($myRow["ProviderType"]){
			   case 99:
			         $ProviderType="<span style='color:#AC516F'>禁用</span>";
			         break;
			   default:
			         $ProviderTypeFrom=$myRow["ProviderType"];
			         include "../model/subselect/ProviderType.php";
			         $ProviderType=$ProviderType==""?"&nbsp;":$ProviderType;
			    break; 
			    
			}
			
			
			
		    $ObjectSignFrom=$myRow["ObjectSign"];
		    include "../model/subselect/TradeType.php";
		    
			$SaleMode	=$myRow["SaleMode"] ;
			$SaleModeStr="";
             switch($SaleMode){
              case "1": $SaleModeStr="<img src='../images/salein.jpg' width='18' height='18'>";break;
              case "2": $SaleModeStr="外销";break;
              case "2": $SaleModeStr="&nbsp;";break;
             }
			$UpdateReasons=$myRow["UpdateReasons"]==""?"&nbsp;":"<div style='color:#FF0000'>".$myRow["UpdateReasons"]. "</div>";
			$ReturnReasons=$myRow["ReturnReasons"]==""?"&nbsp;":"<div class='blueB'>".$myRow["ReturnReasons"]. "</div>";

			$LimitTime=$myRow["LimitTime"]==2?$myRow["LimitTime"]. "周":"<div style='color:#FF0000'>".$myRow["LimitTime"]. "周</div>";
			$Tel=$myRow["Tel"]==""?"&nbsp;":$myRow["Tel"];
			$Fax=$myRow["Fax"]==""?"&nbsp;":$myRow["Fax"];
			$Website=$myRow["Website"]==""?"&nbsp":"<a href='http://$myRow[Website]' target='_blank'>查看</a>";
            $ExpNum=trim($myRow["ExpNum"])==""?"&nbsp;":$myRow["ExpNum"];
		    $Area=$myRow["Area"]==""?"&nbsp":$myRow["Area"];
            $PriceTerm=trim($myRow["PriceTerm"])==""?"&nbsp":$myRow["PriceTerm"];
            $ChinaSafe=$myRow["ChinaSafe"]==""?"&nbsp":$myRow["ChinaSafe"];
		   $BankTitle=$myRow["Estate"]==1?$myRow["BankTitle"]:"&nbsp;";	
		   $OrderSignColor=$myRow["PayType"]==1?"bgcolor=\"#F00\"":"";
		   $staff_Name=$myRow["staff_Name"]==""?"&nbsp":$myRow["staff_Name"];	
		   $PayMode=$myRow["PayMode"];
		   $ePayMode=$myRow["ePayMode"];	
		   $GysPayMode=$myRow["GysPayMode"];
		   $GysPayModeStr=$myRow["Prepayment"]==1?"<div title='先付款' class='redB'>$GysPayMode</div>":$GysPayMode;

			$checkLinkman=mysql_fetch_array(mysql_query("SELECT Name,Mobile,Email FROM $DataIn.linkmandata WHERE CompanyId='$CompanyId' and Type=8 and Defaults=0 LIMIT 1",$link_id));
			$Name=$checkLinkman["Name"]==""?"&nbsp":$checkLinkman["Name"];
			$Mobile=$checkLinkman["Mobile"]==""?"&nbsp":$checkLinkman["Mobile"];
			$Linkman=$checkLinkman["Email"]==""?$Name:"<a href='mailto:$checkLinkman[Email]'>$Name</a>";
			
			
	if($ObjectSign!=2){
			$PackFile=$myRow["PackFile"];
			if ($PackFile==1){
			   $PackFileName="Pack_$CompanyId.png";
			   $f=anmaIn($PackFileName,$SinkOrder,$motherSTR);	
				$PackFile="<span onClick='OpenOrLoad(\"$d\",\"$f\")' style='CURSOR: pointer;color:#F00;'>查看</span>";
			}
			else{
				$PackFile="&nbsp;";
			}
			
			$TipsFile=$myRow["TipsFile"];
			if ($TipsFile==1){
			   $PackFileName="Tips_$CompanyId.png";
			   $f=anmaIn($PackFileName,$SinkOrder,$motherSTR);	
				$TipsFile="<span onClick='OpenOrLoad(\"$d\",\"$f\")' style='CURSOR: pointer;color:#F00;'>查看</span>";
			}
			else{
				$TipsFile="&nbsp;";
			}
			
			
			$BusinessLicence=$myRow["BusinessLicence"];
			if ($BusinessLicence==1){
			   $PackFileName="B".$CompanyId.".jpg";
			   $f=anmaIn($PackFileName,$SinkOrder,$motherSTR);	
			   $BusinessLicence="<span onClick='OpenOrLoad(\"$d\",\"$f\")' style='CURSOR: pointer;color:#F00;'>查看</span>";
			}
			else{
				$BusinessLicence="&nbsp;";
			}

			$TaxCertificate=$myRow["TaxCertificate"];
			if ($TaxCertificate==1){
			   $PackFileName="T".$CompanyId.".jpg";
			   $f=anmaIn($PackFileName,$SinkOrder,$motherSTR);	
				$TaxCertificate="<span onClick='OpenOrLoad(\"$d\",\"$f\")' style='CURSOR: pointer;color:#F00;'>查看</span>";
			}
			else{
				$TaxCertificate="&nbsp;";
			}
			
			
			$ProductionCertificate=$myRow["ProductionCertificate"];
			if ($ProductionCertificate==1){
			   $PackFileName="P".$CompanyId.".jpg";
			   $f=anmaIn($PackFileName,$SinkOrder,$motherSTR);	
				$ProductionCertificate="<span onClick='OpenOrLoad(\"$d\",\"$f\")' style='CURSOR: pointer;color:#F00;'>查看</span>";
			}
			else{
				$ProductionCertificate="&nbsp;";
			}	
			
			$BankPermit=$myRow["BankPermit"];
			if ($BankPermit==1){
			   $BankPermitFileName="K".$CompanyId.".jpg";
			   $f=anmaIn($BankPermitFileName,$SinkOrder,$motherSTR);	
			   $BankPermit="<span onClick='OpenOrLoad(\"$d\",\"$f\")' style='CURSOR: pointer;color:#F00;'>查看</span>";
			}
			else{
				$BankPermit="&nbsp;";
			}

			$SalesAgreement=$myRow["SalesAgreement"];
			if ($SalesAgreement==1){
			   $SalesAgreementFileName="S".$CompanyId.".pdf";
			   $f=anmaIn($SalesAgreementFileName,$SinkOrder,$motherSTR);	
			   $SalesAgreement="<span onClick='OpenOrLoad(\"$d\",\"$f\")' style='CURSOR: pointer;color:#F00;'>查看</span>";
			}
			else{
				$SalesAgreement="&nbsp;";
			}
			
			
			$PaymentOrder=$myRow["PaymentOrder"];
			if ($PaymentOrder==1){
			   $PaymentOrderFileName="O".$CompanyId.".jpg";
			   $f=anmaIn($PaymentOrderFileName,$SinkOrder,$motherSTR);	
			   $PaymentOrder="<span onClick='OpenOrLoad(\"$d\",\"$f\")' style='CURSOR: pointer;color:#F00;'>查看</span>";
			}
			else{
				$PaymentOrder="&nbsp;";
			}
			
			$TaxpayerIdentifi=$myRow["TaxpayerIdentifi"];
			if ($TaxpayerIdentifi==1){
			   $TaxpayerIdentifiFileName="TI".$CompanyId.".jpg";
			   $f=anmaIn($TaxpayerIdentifiFileName,$SinkOrder,$motherSTR);	
			   $TaxpayerIdentifi="<span onClick='OpenOrLoad(\"$d\",\"$f\")' style='CURSOR: pointer;color:#F00;'>查看</span>";
			}
			else{
				$TaxpayerIdentifi="&nbsp;";
			}
			
			$InvoiceTax = $myRow["InvoiceTax"];
			$InvoiceTax=$InvoiceTax==0?"&nbsp;":$InvoiceTax."%";
			$AddValueTaxName=$myRow["AddValueTaxName"]==""?"&nbsp;":$myRow["AddValueTaxName"];
					
			//add by zx 2014-03-27 抓图片职责，找到最多人的那个作为职责
			$PJobname="&nbsp;";
			$checkPNumber=mysql_query("SELECT A.PicNumber,A.MoreCS FROM (
										SELECT S.PicNumber,SUM(1) as MoreCS 
										FROM $DataIn.bps  B
										LEFT JOIN $DataIn.stuffdata S ON S.StuffId=B.StuffId 
										WHERE B.CompanyId='$CompanyId' AND S.Estate>0 AND S.Pjobid!=-1  group by S.PicNumber ) A order by A.MoreCS desc
								   ",$link_id);			
			if($PNumberRow=mysql_fetch_array($checkPNumber)){
				$PicNumber=$PNumberRow["PicNumber"];
				if ($PicNumber!=0 ){  //说明指定的人
					 $PicmySql="SELECT j.Id,j.Name,m.Number,m.Name as staffname FROM $DataPublic.jobdata  j
						  LEFT JOIN $DataPublic.staffmain M on J.Id=M.JobId
						  WHERE  M.Number='$PicNumber' ";
				   $result = mysql_query($PicmySql,$link_id);
				   if($myrow = mysql_fetch_array($result)){
					$jId=$myrow["Id"];
					$jobName=$myrow["Name"];
					$Number=$myrow["Number"];
					$staffname=$myrow["staffname"];
					$PJobname=$jobName."-$staffname";
				   }				
				}	
			}				
			$PJobname="<img location.href=\"#\"' style='CURSOR: pointer' onclick='upMainData(\"tradeobject_uppicNumber\",\"$Id\")' src='../images/edit.gif' title='更新上传图片职责人' width='13' height='13'>
			<span class='yellowB'>$PJobname</span>";
          }
else{
           $PJobname="&nbsp;";
        }


              $ShipQtySum="&nbsp";	$LastShipDate="&nbsp;";
			$Remark=$myRow["Remark"]==""?"&nbsp":"<img src='../images/remark.gif' title='$myRow[Remark]' width='16' height='16'>";	
           $Estate=$myRow["Estate"];
            switch($Estate){
                 case "1":$Estate="<div class='greenB'>√</div>";break;
                 case "0":$Estate="<div class='redB'>×</div>";break;
                 case "2":$Estate="<div class='yellowB'>×.</div>";break;
                 case "4":$Estate="<div class='blueB'>退回</div>";break;
             }
			$Date=$myRow["Date"];
			$Operator=$myRow["Operator"];
			include "../model/subprogram/staffname.php";
			$Locks=$myRow["Locks"];
            $CompanyIdStr="<a href='tradeobject_view.php?CompanyId=$CompanyId&funFrom=$funFrom&From=$From' target='_blank'>$CompanyId</a>";
            
			$ValueArray=array(
				array(0=>$ObjectStr,1=>"align='center'"),
				array(0=>$ProviderType,1=>"align='center'"),
				array(0=>$CompanyIdStr,1=>"align='center'"),
				array(0=>$Letter.$Forshort,2=>"onmousedown='window.event.cancelBubble=true;'"),
				array(0=>$logoFileSTR,1=>"align='center'"),
				
				array(0=>$Symbol,1=>"align='center'"),
				array(0=>$Tel),
				array(0=>$Fax),
				array(0=>$Website,1=>"align='center'",2=>"onmousedown='window.event.cancelBubble=true;'"),
				array(0=>$Linkman),
				array(0=>$Mobile,1=>"align='left'"),
				array(0=>$ExpNum,1=>"align='left'"),
				array(0=>$Area,1=>"align='left'"),
			    array(0=>$ShipQtySum,2=>"align='center'"),	
			    array(0=>$LastShipDate,2=>"align='center'"),	
				array(0=>$staff_Name,1=>"align='center'"),
				array(0=>$PriceTerm,1=>"align='left'"),
				array(0=>$SaleModeStr,1=>"align='center'"),
				array(0=>$ChinaSafe,1=>"align='left'"),
	 		    array(0=>$PayMode,1=>"title='$ePayMode' "),
			    array(0=>$GysPayModeStr,1=>"align='center' "),
				array(0=>$BankTitle,1=>"align='left'"),
				array(0=>$PackFile,1=>"align='center' "),
				array(0=>$TipsFile,1=>"align='center' "),
				array(0=>$Judge,1=>"align='center' $EstateColor "),
				array(0=>$BusinessLicence,1=>"align='center'"),
				array(0=>$TaxCertificate,1=>"align='center'"),
				array(0=>$BankPermit,1=>"align='center'"),
				array(0=>$TaxpayerIdentifi,1=>"align='center'"),
				array(0=>$PaymentOrder,1=>"align='center'"),
				array(0=>$SalesAgreement,1=>"align='center'"),
				array(0=>$ProductionCertificate,1=>"align='center'"),
				array(0=>$AddValueTaxName,1=>"align='center'"),
				array(0=>$InvoiceTax,1=>"align='center'"),
				array(0=>$Remark,1=>"align='center'"),
				array(0=>$UpdateReasons,1=>"align='center'"),
				array(0=>$ReturnReasons),
				array(0=>$PJobname),
				array(0=>$Estate,1=>"align='center'"),
				array(0=>$Date,1=>"align='center'"),
				array(0=>$Operator,1=>"align='center'")
				);
			
			$checkidValue=$Id;
			include "../model/subprogram/read_model_6.php";
            }
	    }while ($myRow = mysql_fetch_array($myResult));
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