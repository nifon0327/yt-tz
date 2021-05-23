<?php 
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 更新交易对象资料");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$fromcSign = $fromcSign == ""?7:$fromcSign;
$upResult = mysql_query("SELECT A.Id,A.CompanyId,A.Letter,A.Forshort,A.ProviderType,A.PayMode,A.GysPayMode,A.Date
,A.ExpNum,A.ObjectSign,A.Currency,A.FscNo,A.CompanySign,A.PayType,A.BankId,A.Staff_Number,B.Tel,B.Fax,B.Website,
B.Remark,B.Area,B.Company,B.ZIP,B.Address,B.Bank,B.BankUID,B.BankAccounts,B.IBAN,C.Symbol,A.Judge,A.PackFile,A.TipsFile,
A.Prepayment,A.LimitTime,K.Title AS BankTitle,E.Name AS staff_Name ,A.PriceTerm,A.UpdateReasons,A.ChinaSafe,A.ChinaSafeSign,
P.InvoiceTax,P.BusinessLicence,P.TaxCertificate,P.ProductionCertificate,P.BankPermit,P.SalesAgreement,P.PaymentOrder,
P.TaxpayerIdentifi,P.AddValueTax,A.SaleMode,P.BulidTime,P.ValidTime,P.Capital,P.CompanySize,P.StaffNum,P.CompanyNature,P.CompanyCategory,P.CompanyPicture,P.MainBusiness,P.DealRange,

T.TradeNo,T.Proofreader,(select Name from $DataPublic.staffmain S1 where S1.Number = T.Proofreader)AS ProofreaderName,
T.Proofreader1,(select Name from $DataPublic.staffmain S1 where S1.Number = T.Proofreader1)AS Proofreader1Name,
T.Checker,(select Name from $DataPublic.staffmain S1 where S1.Number = T.Checker)AS CheckerName,T.Members,
T.Producer,(select Name from $DataPublic.staffmain S1 where S1.Number = T.Producer)AS ProducerName,T.CmptTotal
  
FROM $DataIn.trade_object  A
LEFT JOIN $DataIn.companyinfo B ON B.CompanyId=A.CompanyId AND B.Type=8
LEFT JOIN $DataIn.providersheet P ON P.CompanyId=A.CompanyId 
LEFT JOIN $DataPublic.currencydata C ON C.Id=A.Currency
LEFT JOIN $DataPublic.staffmain E ON E.Number=A.Staff_Number
LEFT JOIN $DataPublic.my2_bankinfo K ON K.Id=A.BankId 

LEFT JOIN $DataIn.trade_info T ON T.TradeId=A.Id

WHERE  A.Id='$Id' order by A.Id DESC",$link_id); 
if($upRow = mysql_fetch_array($upResult)){
	$CompanyId=$upRow["CompanyId"];
	$FscNo=$upRow["FscNo"];
	$Forshort=$upRow["Forshort"];
	$Currency=$upRow["Currency"];
	$LimitTime=$upRow["LimitTime"];
	$PayMode=$upRow["PayMode"];	$TempPay="PayModelSTR".strval($PayMode);$$TempPay="selected";
    $ProviderType=$upRow["ProviderType"];
    $ProviderType=$ProviderType=="-1"?"9":$ProviderType;
	$TempProviderType="ProviderTypeSTR".strval($ProviderType);
    $$TempProviderType="selected";
    $GysPayMode=$upRow["GysPayMode"];
    $GysPayModeStr="GysPayMode".strval($GysPayMode);
    $$GysPayModeStr="selected";
	$Prepayment=$upRow["Prepayment"];
	$PrepaymentSTR=$GysPayMode==0?"disabled":"";
	$PrepaymentSTR.=($GysPayMode==1 && $Prepayment==1)?" checked ":"";
     $Staff_Number=$upRow["Staff_Number"];
	$Staff_Name=$upRow["Staff_Name"];
	$PriceTerm=$upRow["PriceTerm"];
    $ChinaSafe=$upRow["ChinaSafe"];
    $ChinaSafeSign=$upRow["ChinaSafeSign"];
    $ChinaSafeSignSTR="ChinaSafeSignSTR".strval($ChinaSafeSign);
    $$ChinaSafeSignSTR="selected";
    
    $SaleMode=$upRow["SaleMode"];
    $SaleModeSTR="SaleMode".strval($SaleMode);
    $$SaleModeSTR ="selected";
	
	$ExpNum=$upRow["ExpNum"];
	$UpdateReasons=$upRow["UpdateReasons"];
	$BankId=$upRow["BankId"];
	$BankUID=$upRow["BankUID"];
	$BankAccounts=$upRow["BankAccounts"];
	$IBAN=$upRow["IBAN"];
	$PayType=$upRow["PayType"];
	$ObjectSign=$upRow["ObjectSign"];
	$CompanySign=$upRow["CompanySign"];
	$Company=$upRow["Company"];
	$Tel=$upRow["Tel"];
	$Judge=$upRow["Judge"];
	$Fax=$upRow["Fax"];
	$AreaStr=$upRow["Area"];
	$ZIP=$upRow["ZIP"];
     $Website=$upRow["Website"];
	$Address=$upRow["Address"];
	$Bank=$upRow["Bank"];
	$Remark=$upRow["Remark"];
	$BusinessLicence=$upRow["BusinessLicence"];
	$TaxCertificate=$upRow["TaxCertificate"];
	$ProductionCertificate=$upRow["ProductionCertificate"];
	$TaxpayerIdentifi = $upRow["TaxpayerIdentifi"];
	$BankPermit = $upRow["BankPermit"];
	$SalesAgreement = $upRow["SalesAgreement"];
	$BulidTime = $upRow["BulidTime"];
	$ValidTime = $upRow["ValidTime"];
	$Capital = $upRow["Capital"];
	$CompanySize = $upRow["CompanySize"];
	$StaffNum = $upRow["StaffNum"];
	$CompanyNature = $upRow["CompanyNature"];
	$CompanyCategory = $upRow["CompanyCategory"];
	$CompanyPicture = $upRow["CompanyPicture"];
	$MainBusiness = $upRow["MainBusiness"];
	$DealRange = $upRow["DealRange"];
	
	//
	$TradeNo = $upRow['TradeNo'];
	$Proofreader = $upRow['Proofreader'];
	$ProofreaderName = $upRow['ProofreaderName'];
	$Proofreader1 = $upRow['Proofreader1'];
	$Proofreader1Name= $upRow['Proofreader1Name'];
	$Checker = $upRow['Checker'];
	$CheckerName = $upRow['CheckerName'];
	$Members = $upRow['Members'];
	$Producer = $upRow['Producer'];
	$ProducerName = $upRow['ProducerName'];
	$CmptTotal = $upRow['CmptTotal'];
	//

	$BankId=$upRow["BankId"];
	$InvoiceTax=$upRow["InvoiceTax"];
	$AddValueTax=$upRow["AddValueTax"];
	$lmanResult = mysql_query("SELECT * FROM $DataIn.linkmandata WHERE CompanyId=$CompanyId AND Type=8 AND Defaults=0 order by CompanyId DESC LIMIT 1",$link_id);
	$lmanRow = mysql_fetch_array($lmanResult);
	$LinkId=$lmanRow["Id"];
	$TempSex="SexSTR".strval($lmanRow["Sex"]);$$TempSex="selected";
    $TempPayType="PayTypeSTR".strval($PayType);$$TempPayType="selected";
    $sheetResult=mysql_query("SELECT * FROM $DataIn.providersheet WHERE CompanyId=$CompanyId  LIMIT 1",$link_id);
	if ($sheetRow = mysql_fetch_array($sheetResult)){
            $LegalPerson=$sheetRow["LegalPerson"];
            $Description=$sheetRow["Description"];
            $Aptitudes=$sheetRow["Aptitudes"];
            $EAQF=$sheetRow["EAQF"];
            $BLdate=$sheetRow["BLdate"]=="0000-00-00"?"":$sheetRow["BLdate"];
            $TRCdate=$sheetRow["TRCdate"]=="0000-00-00"?"":$sheetRow["TRCdate"];
            $PLdate=$sheetRow["PLdate"]=="0000-00-00"?"":$sheetRow["PLdate"];
	}
}
$ObjectStr="ObjectSign".$ObjectSign;
$$ObjectStr="selected";
//步骤4：
$tableWidth=950;$tableMenuS=650;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Orderby,$Orderby,Pagination,$Pagination,Page,$Page,LinkId,$LinkId,CompanyId,$CompanyId,OldObjectSign,$ObjectSign";


$EssentialSign="<span style='color:#F00;'>*</span>";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
<tr><td class="A0011">
        <table width="800" border="0" align="center" cellspacing="5">
		 <tr>
		   	<td align="right"><?php echo $EssentialSign?>交易对象类型</td>
		   	<td colspan="3" scope="col">
           <?php 
            $TypeIdWidth="470px";
            $onChangeFunction="ChangeInfo()";
			include "../model/subselect/TradeType.php";
			?>
         </td></td>
	      </tr> 
  <tr>
    <td scope="col" align="right"><?php echo $EssentialSign?>结付货币</td>
    <td colspan="3" scope="col">
		<?php 
		$CurrencyResult = mysql_query("SELECT A.Id,A.Name FROM $DataPublic.currencydata A WHERE A.Estate=1 AND A.ID='$Currency' ORDER BY A.Id",$link_id);
		$CurrencyName =mysql_result($CurrencyResult,0,"Name");	
		//币别不能乱改，因为会影响以前结付的货款 
		//include "../model/subselect/Currency.php";
		?>
        <input name="CurrencyName" type="text" id="CurrencyName" value="<?php  echo $CurrencyName?>" style="width:470px"  readonly="readonly" >
        <input name="Currency" type="hidden" id="Currency" value="<?php  echo $Currency ?>"/>       
  </td>
  </tr>
  

         <tr>
            <td align="right">所属公司</td>
            <td colspan="3">
            
             <?php 
               $x=0;
			    $checkResult = mysql_query("SELECT * FROM $DataIn.companys_group  WHERE Estate=1 ORDER BY Id",$link_id);
                while($checkRow = mysql_fetch_array($checkResult)){
                    $thiscSign=$checkRow["cSign"];
                    $CShortName=$checkRow["CShortName"];
                    $ColorValue=$checkRow["ColorValue"];
                    $checked = ""; $readonly ="";
                    if($thiscSign ==$fromcSign){
	                    $checked = "checked";
	                    $readonly = " onclick='return false;'";
                    }
                    if($CompanySign%$thiscSign==0){
	                    $checked = "checked";
                    }
                    echo "<input name='TempCompanySign[]' type='checkbox' value='$thiscSign' $checked $readonly /><span style='color:$ColorValue;'>$CShortName</span>&nbsp;&nbsp;&nbsp;&nbsp;";
                    $x++;
                    }
			?>
            </td>
          </tr>
  

          <tr>
            <td  align="right"><?php echo $EssentialSign?>公司名称</td>
            <td colspan="3"><input name="Company" type="text" id="Company"  value="<?php  echo $Company?>" style="width:470px;" dataType="LimitB" max="50" min="2" msg="必须在2-50个字节之内"></td>
          </tr>
          <tr>
            <td align="right"><?php echo $EssentialSign?>公司简称</td>
            <td colspan="3"><input name="Forshort" type="text" id="Forshort"  value="<?php  echo $Forshort?>" style="width:470px;" DPNameCheck="trade_object" DPNameCheckExcept=<?php echo $Id;?> dataType="LimitB" max="20" min="2" msg="必须在2-20个字节之内"></td>
          </tr>
          <tr>
             <td align="right">国家地区</td>
		   	<td width="210" scope="col">
              <input name="Area" type="text" id="Area" style="width:190px;"  value="<?php  echo $AreaStr?>" dataType="LimitB" max="50" min="2" msg="2-50个字节之内"></td>
            <td align="right">LOGO</td>
            <td width="410"><input name="Logo" type="file" id="Logo" style="width:190px"  accept="png" Row="3" Cel="1" ></td>
          </tr>
          <tr>
            <td align="right"><?php echo $EssentialSign?>公司电话</td>
            <td width="210"><input name="Tel" type="text" id="Tel" value="<?php  echo $Tel?>" style="width:190px;"></td>
            <td align="right">公司传真</td>
            <td width="410"><input name="Fax" type="text" id="Fax" value="<?php  echo $Fax?>" style="width:190px;" require="false"></td>
          </tr>

         <tr>
            <td align="right">网&nbsp;&nbsp;&nbsp;&nbsp;址</td>
            <td width="210"><input name="Website" type="text" id="Website" value="<?php  echo $Website?>" style="width:190px;"></td>
            <td align="right">邮政编码</td>
            <td width="410"><input name="ZIP" type="text" id="ZIP" style="width:190px;" value="<?php  echo $ZIP?>" ></td>
          </tr>
          <tr>
            <td align="right"><?php echo $EssentialSign?>通信地址</td>
            <td colspan="3"><input name="Address" type="text" require="false" id="Address" value="<?php  echo $Address?>" style="width:470px;" ataType="Limit" min="2" max="50" msg="必须在50个字之内"></td>
          </tr>
          <tr>
            <td valign="top" align="right">快递帐户</td>
            <td colspan="3"><textarea name="ExpNum" style="width:470px" id="ExpNum"><?php  echo $ExpNum?></textarea></td>
          </tr>

          <tr id="Tr_IBAN" name="Tr_IBAN" disabled="disabled" hidden="hidden">
            <td valign="top" align="right">IBAN</td>
            <td colspan="3"><input name="IBAN" type="text"  style="width:470px" id="IBAN" value="<?php  echo $IBAN?>"></td>
          </tr>
          <tr>
            <td valign="top" align="right">备&nbsp;&nbsp;&nbsp;&nbsp;注</td>
            <td colspan="3"><textarea name="Remark" style="width:470px" id="Remark"><?php  echo $Remark?></textarea></td>
          </tr>

        </table>
       </td></tr> 
           
        <tr id="ClientTable" name="ClientTable" disabled="disabled" hidden="hidden">
        <td class="A0011">
        
         <table width="800" border="0" align="center" cellspacing="5" >
          <tr>
            <td width="90">&nbsp;</td>
            <td colspan="3" scope="col" align="left"><span class="redB">客户信息</span></td>
          </tr>
         <tr>
            <td align="right" width="90"><?php echo $EssentialSign?>收款性质</td>
            <td width="210"><select name="PayType" id="PayType" style="width:150px" datatype="Require" msg="未选">
              <option value="">请选择</option>
					<option value="1" <?php  echo $PayTypeSTR1?>>款到发货</option>
                    <option value="2" <?php  echo $PayTypeSTR2?>>货到付款</option>
            </select></td>
            <td align="right" width="90"><?php echo $EssentialSign?>收款方式</td>
            <td value="410"><select name="PayMode" id="PayMode" style="width:156px" datatype="Require" msg="未选">
              <option value="" selected>请选择</option>
              <?php 
			$PayModeResult = mysql_query("SELECT Id,Name,eName FROM $DataPublic.clientpaymode WHERE Estate=1 order by Id",$link_id);
			if($PayModeRow = mysql_fetch_array($PayModeResult)){
				$i=1;
				do{
					$Id=$PayModeRow["Id"];
					$Name=$PayModeRow["Name"];
					$eName=$PayModeRow["eName"];
					if($PayMode==$Id){
						echo"<option value='$Id' selected>$Name($eName)</option>";
					}
					else{
						echo"<option value='$Id'>$Name($eName)</option>";
					}
					$i++;
					}while ($PayModeRow = mysql_fetch_array($PayModeResult));
				}
			?>
            </select></td>
          </tr>

          <tr>
            <td scope="col" align="right"><?php echo $EssentialSign?>收款帐号</td>
            <td colspan="3" scope="col"><select name="BankId" id="BankId" style="width:470px" datatype="Require" msg="未选">
              <option value="" selected>请选择</option>
              <?php 
			$PayBankResult = mysql_query("SELECT Id,Title FROM $DataPublic.my2_bankinfo WHERE  Estate=1 order by Id",$link_id);
			if($PayBankRow = mysql_fetch_array($PayBankResult)){
				$i=1;
				do{
					$Id=$PayBankRow["Id"];
					$Title=$PayBankRow["Title"];
					$SelectedSTR=$Id==$BankId?" selected":"";
					echo"<option value='$Id' $SelectedSTR>$Title</option>";
					$i++;
					}while ($PayBankRow = mysql_fetch_array($PayBankResult));
				}
			?>
            </select></td>
          </tr>
          
          <tr><td scope="col" align="right"><?php echo $EssentialSign?>销售模式</td>
          <td colspan="3" scope="col"><select name="SaleMode" id="SaleMode" style="width:470px" datatype="Require" msg="未选">
              <option value="" selected>请选择</option>
              <option value="1" <?php  echo $SaleMode1?>>内销</option>
              <option value="2" <?php  echo $SaleMode2?>>外销</option>
              </select></td>
          </tr>
          <tr>
            <td valign="top" align="right">PriceTerm</td>
            <td colspan="3"><input type="text" name="PriceTerm" style="width:470px" id="PriceTerm"  value="<?php  echo $PriceTerm?>"></td>
          </tr>
          
           <tr>
            <td align="right"><?php echo $EssentialSign?>中国信保</td>
            <td ><select name="ChinaSafeSign" id="ChinaSafeSign" style="width:150px" datatype="Require" msg="未选" onchange="ChinaSafeSignChange()">
              <option value="" selected>请选择</option>
					<option value="0" <?php  echo $ChinaSafeSignSTR0?>>否</option>
					<option value="1" <?php  echo $ChinaSafeSignSTR1?>>是</option>
            </select></td>
            <td align="right">限&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;额</td>
            <td ><input type="text" name="ChinaSafe" style="width:156px" id="ChinaSafe" value="$<?php  echo $ChinaSafe?>" disabled="disabled"></td>
          </tr>
         </table>
         </td></tr> 
        
        <tr id="SupplierTable" name="SupplierTable" disabled="disabled" hidden="hidden">
        <td class="A0011">
              <table width="800" border="0" align="center" cellspacing="5" id="NoteTable" name="NoteTable">
          <tr>
            <td >&nbsp;</td>
            <td colspan="3" align="left"><span class="redB">供应商信息</span></td>
          </tr>
       <tr>
         <td width="90" align="right" scope="col"><?php echo $EssentialSign?>供应商类型</td>
         <td colspan="3" scope="col">
          <?php 
                $TypeIdWidth="470px";
                $TypeFrom="";
			    include "../model/subselect/ProviderType.php";
			?>
         </td>
       </tr>
		<tr>
            <td  align="right"><?php echo $EssentialSign?>付款方式</td>
            <td colspan="3" scope="col">
				<select name="GysPayMode" id="GysPayMode" style="width:470px" dataType="Require" msg="未选">
				<option value="" selected>请选择</option>
                <?php 
				$PayModeResult = mysql_query("SELECT Id,Name
				FROM $DataIn.providerpaymode WHERE Estate=1 order by SortId",$link_id);
				if($PayModeRow = mysql_fetch_array($PayModeResult)){
					$i=1;
					do{
					    $_Id=$PayModeRow["Id"];
					    $_Name=$PayModeRow["Name"];
						if($GysPayMode==$_Id){
						     echo"<option value='$_Id' selected>$_Name</option>";
						}
						else{
							 echo"<option value='$_Id'>$_Name</option>";
						}
						$i++;
					}while ($PayModeRow = mysql_fetch_array($PayModeResult));
				}
			 ?>
				</select></td>
          </tr>
            <tr>
            <td valign="top" align="right"><?php echo $EssentialSign?>银行名称</td>
            <td colspan="3"><input name="Bank" type='text' style="width:470px" id="Bank" dataType="Require" value="<?php  echo $Bank?>"/></td>
          </tr>
          <tr>
            <td valign="top" align="right"><?php echo $EssentialSign?>户&nbsp;&nbsp;&nbsp;&nbsp;名</td>
            <td colspan="3"><input name="BankUID" type='text' style="width:470px" id="BankUID" dataType="Require" value="<?php  echo $BankUID?>"></td>
          </tr>
          <tr>
            <td valign="top" align="right"><?php echo $EssentialSign?>帐&nbsp;&nbsp;&nbsp;&nbsp;号</td>
            <td colspan="3"><input name="BankAccounts" type='text' style="width:470px" id="BankAccounts" dataType="Require" value="<?php  echo $BankAccounts?>"></td>
          </tr>

           <tr>
            
            <td align="right" width="90">增值税率</td>
            <td width="210"><select name="AddValueTax" id="AddValueTax" style="width:170px">
				<option value="" selected>请选择</option>
              <?php 
			   $AddTaxResult = mysql_query("SELECT Id, Name,Value FROM $DataIn.provider_addtax WHERE  Estate=1 order by Id",$link_id);
			if($AddTaxRow = mysql_fetch_array($AddTaxResult)){
				$i=1;
				do{
				    $SelectedSTR ="";
				    $AddTaxId=$AddTaxRow["Id"];
					$AddTaxName=$AddTaxRow["Name"];
					$AddTaxValue=$AddTaxRow["Value"];
					if($AddValueTax == $AddTaxId){
						$SelectedSTR=" selected";
					}
					
					
					echo"<option value='$AddTaxId' $SelectedSTR>$AddTaxName</option>";
					$i++;
					}while ($AddTaxRow = mysql_fetch_array($AddTaxResult));
				}
			?>
				</select></td>
				<td align="right">加税点</td>
            <td width="410"><input name="InvoiceTax" type="text" id="InvoiceTax" style="width:170px;" dataType="Currency" msg="输入错误"  value="<?php  echo $InvoiceTax?>" >％ &nbsp;&nbsp;</td>
          </tr>
        <tr>
 
        <tr>
            <td align="right" width="90"><?php echo $EssentialSign?>企业法人</td>
            <td width="210"><input name="LegalPerson" type="text" id="LegalPerson" style="width:170px;" value="<?php  echo $LegalPerson?>" datatype="Require" msg="未填"></td>
             <td align="right" width="90"><?php echo $EssentialSign?>成立时间</td>
            <td width="420"><input name="BulidTime" type="text" id="BulidTime" style="width:160px;" maxlength="10" value="<?php  echo $BulidTime?>" onfocus="WdatePicker()"  dataType="Date" format="ymd" msg="日期不正确" readonly/>
            </td>
          </tr>
               <tr>
            <td align="right" width="90"><?php echo $EssentialSign?>有效期</td>
            <td width="210"><input name="ValidTime" type="text" id="ValidTime" style="width:150px;" value="<?php  echo $ValidTime?>" datatype="Require" msg="未填"></td>
 
            <td align="right" width="90"><?php echo $EssentialSign?>注册资本</td>
            <td width="420"><input name="Capital" type="text" id="Capital" style="width:160px;" value="<?php  echo $Capital?>" datatype="Require" msg="未填"/></td>
          </tr>
          
        <tr>
            <td align="right" width="90"><?php echo $EssentialSign?>规&nbsp;&nbsp;&nbsp;&nbsp;模</td>
            <td width="210"><input name="CompanySize" type="text" id="CompanySize" style="width:150px;"  value="<?php  echo $CompanySize?>"datatype="Require" msg="未填"></td>
 
            <td align="right" width="90"><?php echo $EssentialSign?>员工人数</td>
            <td width="420"><input name="StaffNum" type="text" id="StaffNum" style="width:160px;" value="<?php  echo $StaffNum?>" datatype="Require" msg="未填" /></td>
          </tr>
        <?php 
            $d=anmaIn("download/providerfile/",$SinkOrder,$motherSTR);
	        if ($BusinessLicence==1){
		       $BusinessFileName="B".$CompanyId.".jpg";
			   $f=anmaIn($BusinessFileName,$SinkOrder,$motherSTR);	
			   $BusinessLicence="<span onClick='OpenOrLoad(\"$d\",\"$f\")' style='CURSOR: pointer;color:#F00;'>查看</span>";
	        }
	        else{
		       $BusinessLicence=""; 
	        }
	         if ($TaxCertificate==1){
		       $TaxFileName="T".$CompanyId.".jpg";
			   $f=anmaIn($TaxFileName,$SinkOrder,$motherSTR);	
			   $TaxCertificate="<span onClick='OpenOrLoad(\"$d\",\"$f\")' style='CURSOR: pointer;color:#F00;'>查看</span>";
	        }
	        else{
		       $TaxCertificate="";
	        }
	        if ($BankPermit==1){
		       $BankPermitFileName="K".$CompanyId.".jpg";
			   $f=anmaIn($BankPermitFileName,$SinkOrder,$motherSTR);	
			   $BankPermit="<span onClick='OpenOrLoad(\"$d\",\"$f\")' style='CURSOR: pointer;color:#F00;'>查看</span>";
	        }
	        else{
		        $BankPermit="";
	        }
	        if ($TaxpayerIdentifi==1){
		       $TaxpayerIdentifiFileName="TI".$CompanyId.".jpg";
			   $f=anmaIn($TaxpayerIdentifiFileName,$SinkOrder,$motherSTR);	
			   $TaxpayerIdentifi="<span onClick='OpenOrLoad(\"$d\",\"$f\")' style='CURSOR: pointer;color:#F00;'>查看</span>";
	        }
	        else{
		        $TaxpayerIdentifi="";
	        }
	         if ($PaymentOrder==1){
		       $PaymentOrderFileName="O".$CompanyId.".jpg";
			   $f=anmaIn($PaymentOrderFileName,$SinkOrder,$motherSTR);	
			   $PaymentOrder="<span onClick='OpenOrLoad(\"$d\",\"$f\")' style='CURSOR: pointer;color:#F00;'>查看</span>";
	        }
	        else{
		        $PaymentOrder="";
	        }
	         if ($SalesAgreement==1){
		       $SalesAgreementFileName="S".$CompanyId.".jpg";
			   $f=anmaIn($SalesAgreementFileName,$SinkOrder,$motherSTR);	
			   $SalesAgreement="<span onClick='OpenOrLoad(\"$d\",\"$f\")' style='CURSOR: pointer;color:#F00;'>查看</span>";
	        }
	        else{
		        $SalesAgreement="";
	        }
	         if ($ProductionCertificate==1){
		       $ProductionCertificateFileName="O".$CompanyId.".jpg";
			   $f=anmaIn($ProductionCertificateFileName,$SinkOrder,$motherSTR);	
			   $ProductionCertificate="<span onClick='OpenOrLoad(\"$d\",\"$f\")' style='CURSOR: pointer;color:#F00;'>查看</span>";
	        }
	        else{
		       $ProductionCertificate=""; 
	        }
        ?>
           <tr>
            <td align="right">营业执照</td>
            <td width="210"><input name="BusinessLicence" type="file" id="BusinessLicence" style="width:190px" dataType="Filter" msg="jpg的文件格式" accept="jpg" Row="8" Cel="1" ><?php  echo $BusinessLicence?></td>
            <td align="right">税务登记证</td>
            <td width="410"><input name="TaxCertificate" type="file" id="TaxCertificate" style="width:190px" dataType="Filter" msg="jpg的文件格式" accept="jpg" Row="9" Cel="1" ><?php  echo $TaxCertificate?></td>
            </tr>
           <tr>
            <td align="right">开户许可</td>
            <td width="210"><input name="BankPermit" type="file" id="BankPermit" style="width:190px" dataType="Filter" msg="jpg的文件格式" accept="jpg" Row="10" Cel="1"><?php  echo $BankPermit?></td>
            <td align="right">纳税人认定书</td>
            <td width="410"><input name="TaxpayerIdentifi" type="file" id="TaxpayerIdentifi" style="width:190px" dataType="Filter" msg="jpg的文件格式" accept="jpg" Row="10" Cel="1"><?php  echo $TaxpayerIdentifi?></td>
           </tr>
           <tr>
            <td align="right">付款委托书</td>
            <td width="210"><input name="PaymentOrder" type="file" id="PaymentOrder" style="width:190px" dataType="Filter" msg="pdf的文件格式" accept="pdf" Row="11" Cel="1"><?php  echo $PaymentOrder?></td>
             <td align="right">合作协议</td>
            <td width="410"><input name="SalesAgreement" type="file" id="SalesAgreement" style="width:190px" dataType="Filter" msg="pdf的文件格式" accept="pdf" Row="12" Cel="1"><?php  echo $SalesAgreement?></td>
           </tr>
           <tr>  
            <td align="right">ROSH报告</td>
            <td colspan="3"><input name="ProductionCertificate" type="file" id="ProductionCertificate" style="width:190px" dataType="Filter" msg="pdf的文件格式" accept="pdf" Row="13" Cel="1"><?php  echo $ProductionCertificate?></td>
          </tr>
          <tr>
            <td scope="col" align="right"><?php echo $EssentialSign?>公司性质</td>
            <td colspan="3" scope="col"><select name="CompanyNature" id="CompanyNature" style="width:470px" datatype="Require" msg="未选">
              <option value="" selected>请选择</option>
              <?php 
			$NatureResult = mysql_query("SELECT Id,name FROM $DataPublic.trade_nature WHERE  Estate=1 order by Id",$link_id);
			if($NatureRow = mysql_fetch_array($NatureResult)){
				$i=1;
				do{
					$thisNatureId=$NatureRow["Id"];
					$thisNatureName=$NatureRow["name"];
					$SelectedSTR  = $CompanyNature == $thisNatureId ?"selected":"";
					echo"<option value='$thisNatureId' $SelectedSTR>$thisNatureName</option>";
					$i++;
					}while ($NatureRow = mysql_fetch_array($NatureResult));
				}
			?>
            </select></td>
          </tr>
          
          <tr>
            <td scope="col" align="right"><?php echo $EssentialSign?>公司类别</td>
            <td colspan="3" scope="col"><select name="CompanyCategory" id="CompanyCategory" style="width:470px" datatype="Require" msg="未选">
              <option value="" selected>请选择</option>
              <?php 
              
			$CategoryResult = mysql_query("SELECT Id,name FROM $DataPublic.trade_category WHERE  Estate=1 order by Id",$link_id);
			if($CategoryRow = mysql_fetch_array($CategoryResult)){
				$i=1;
				do{
					$thisCategoryId=$CategoryRow["Id"];
					$thisCategoryName=$CategoryRow["name"];
					$SelectedSTR  = $CompanyCategory == $thisCategoryId ?"selected":"";
					echo"<option value='$thisCategoryId' $SelectedSTR>$thisCategoryName</option>";
					$i++;
					}while ($CategoryRow = mysql_fetch_array($CategoryResult));
				}
			?>
            </select></td>
          </tr>
          
          <tr>
            <td valign="top" align="right">经营范围</td>
            <td colspan="3"><textarea name="DealRange" type="text" id="DealRange" style="width:470px;"><?php echo $DealRange?></textarea></td>
          </tr>
           <tr>
            <td valign="top" align="right">主营业务</td>
            <td colspan="3"><textarea name="MainBusiness" type="text" id="MainBusiness" style="width:470px;"><?php echo $MainBusiness?></textarea></td>
          </tr>
          <tr>
            <td valign="top" align="right">公司简介</td>
            <td colspan="3"><textarea name="Description" type="text" id="Description" style="width:470px;" rows='2' ><?php  echo $Description?></textarea></td>
          </tr>
           <tr>
            <td valign="top" align="right">工厂实拍</td>
            <td colspan="3"><input name="CompanyPicture" type="file" id="CompanyPicture" style="width:190px" dataType="Filter" msg="jpg的文件格式" accept="jpg" Row="14" Cel="1"></td></td>
          </tr>
          </table>
         </td></tr> 
        
		
		<!--增加项目信息相关 by ckt 2017-12-27-->
        <tr id="TradeTable" name="TradeTable" disabled="disabled" hidden="">
            <td class="A0011">
                <table width="800" border="0" align="center" cellspacing="5">
                    <tbody>
                    <tr>
                        <td ></td>
                        <td colspan="3" align="left"><span class="redB">项目信息</span></td>
                    </tr>
                    <tr>
                        <td align="right" width="88" ><?php echo $EssentialSign?>项目编号</td>
                        <td width="202"><input name="TradeNo" value="<?php echo $TradeNo; ?>" type="text" id="TradeNo" style="width:150px" maxlength="15" datatype="Require" msg="未填"></td>
                        <td align="right" width="89"><?php echo $EssentialSign?>校对者</td>
                        <td>
                            <input name="ProofreaderName" value="<?php echo $ProofreaderName; ?>" onclick="searchStaffId(22,1,'ProofreaderName','Proofreader');" type="text" id="ProofreaderName" style="width:165px" datatype="Require" msg="未填" readonly="readonly">
                            <input name="Proofreader" value="<?php echo $Proofreader; ?>" id="Proofreader" type="text" hidden="hidden" />
                        </td>
                    </tr>
                    <tr>
                        <td align="right" ><?php echo $EssentialSign?>复校者 </td>
                        <td>
                            <input name="Proofreader1Name" value="<?php echo $Proofreader1Name; ?>" onclick="searchStaffId(22,1,'Proofreader1Name','Proofreader1');" type="text" id="Proofreader1Name" style="width:150px" readonly="readonly" datatype="Require" msg="未填">
                            <input name="Proofreader1" value="<?php echo $Proofreader1; ?>" id="Proofreader1" type="text" hidden="hidden" />
                        </td>
                        <td align="right" ><?php echo $EssentialSign?>审核人</td>
                        <td>
                            <input name="CheckerName" value="<?php echo $CheckerName; ?>" onclick="searchStaffId(22,1,'CheckerName','Checker');" type="text" id="CheckerName" style="width:165px" readonly="readonly" datatype="Require" msg="未填">
                            <input name="Checker" value="<?php echo $Checker; ?>" id="Checker" type="text" hidden="hidden" />
                        </td>
                    </tr>
                    <tr>
                        <td align="right" ><?php echo $EssentialSign?>项目人员 </td>
                        <td colspan="3"><textarea name="Members" onclick="searchStaffId(22,'','Members','')" type="text" id="Members" style="width:470px;" readonly="readonly" datatype="Require" msg="未填"><?php echo $Members;?></textarea></td>
                    </tr>
                    <tr>
                        <td align="right" ><?php echo $EssentialSign?>生产负责人</td>
                        <td>
                            <input name="ProducerName" value="<?php echo $ProducerName; ?>" onclick="searchStaffId(22,1,'ProducerName','Producer');" type="text" id="ProducerName" style="width:150px" readonly="readonly" datatype="Require" msg="未填">
                            <input name="Producer" value="<?php echo $Producer; ?>" id="Producer" type="text" hidden="hidden" />
                        </td>
                        <td align="right" ><?php echo $EssentialSign?>构件总数量</td>
                        <td><input name="CmptTotal" value="<?php echo $CmptTotal; ?>" type="text" id="CmptTotal" style="width:165px" datatype="Number" msg="应为数字"></td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
		
		
		
        <tr><td class="A0011">
              <table width="800" border="0" align="center" cellspacing="5"> 
          <tr>
            <td ></td>
            <td colspan="3" align="left"><span class="redB">默认联系人信息</span></td>
          </tr>
          <tr>
            <td align="right"><?php echo $EssentialSign?>联 系 人</td>
            <td><input name="Linkman" type="text" id="Linkman" style="width:190px" value="<?php  echo $lmanRow["Name"]?>" dataType="Limit" max="20" min="2" msg="必须在2-20个字之内"></td>
            <td width="54" align="right"><?php echo $EssentialSign?>性&nbsp;&nbsp;&nbsp;&nbsp;别</td>
            <td>
              <select name="Sex" id="Sex" style="width:190px" dataType="Require" msg="未选择">
			  <option value="">请选择</option>
			  <option value="0" <?php  echo $SexSTR0?>>女</option>
              <option value="1" <?php  echo $SexSTR1?>>男</option>
              </select></td>
          </tr>
          <tr>
            <td align="right"><?php echo $EssentialSign?>职&nbsp;&nbsp;&nbsp;&nbsp;务</td>
            <td width="164"><input name="Headship" type="text" id="Headship" style="width:190px"  value="<?php  echo $lmanRow["Headship"]?>" maxlength="20" dataType="Require" msg="未填写"></td>
            <td align="right">昵&nbsp;&nbsp;&nbsp;&nbsp;称</td>
            <td width="410"><input name="Nickname" type="text" id="Nickname" style="width:190px" value="<?php  echo $lmanRow["Nickname"]?>" maxlength="20"></td>
          </tr>
          <tr>
            <td align="right">移动电话</td>
            <td><input name="Mobile" type="text" id="Mobile" style="width:190px" value="<?php  echo $lmanRow["Mobile"]?>" require="false"></td>
            <td align="right">固定电话</td>
            <td><input name="Tel2" type="text" id="Tel2" style="width:190px" require="false" value="<?php  echo $lmanRow["Tel"]?>"></td>
          </tr>
          <tr>
            <td align="right">MSN</td>
            <td colspan="3"><input name="MSN" type="text" id="MSN" style="width:470px;"  value="<?php  echo $lmanRow["MSN"]?>"></td>
          </tr>
          <tr>
            <td align="right">SKYPE</td>
            <td colspan="3"><input name="SKYPE" type="text" id="SKYPE" style="width:470px;" value="<?php  echo $lmanRow["SKYPE"]?>"></td>
          </tr>
          <tr>
            <td align="right">邮件地址</td>
            <td colspan="3"><input name="Email" type="text" id="Email" style="width:470px;" value="<?php  echo $lmanRow["Email"]?>" require="false" dataType="Email" msg="信箱格式不正确"></td>
          </tr>
          
			 <tr>
            <td width="89" scope="col" align="right"><?php echo $EssentialSign?>我司联络人</td>
            <td colspan="3" scope="col">
			<select name="Staff_Number" id="Staff_Number" style="width:470px" dataType="Require" msg="未选择">
			<option value="">请选择</option>
			<?php 
			$Staff_Result = mysql_query("SELECT M.Number,M.Name,B.Name AS Branch FROM $DataPublic.staffmain  M
										LEFT JOIN $DataPublic.branchdata B ON B.Id=M.BranchId
										WHERE M.Estate=1 AND M.BranchId IN (3,4) ORDER BY M.BranchId,M.Id",$link_id);
			if($staff_Row = mysql_fetch_array($Staff_Result)){
				do{
					$Number=$staff_Row["Number"];
					$Name=$staff_Row["Name"];
					$Branch=$staff_Row["Branch"];
					if($Number==$Staff_Number){
						echo"<option value='$Number' selected>$Branch - $Name</option>";
					}
					else{	
						echo"<option value='$Number'>$Branch - $Name</option>";
					}			
					}while ($staff_Row = mysql_fetch_array($Staff_Result));
				}
			?>
            </select> 
			</td></tr>
 
             
          
          <tr>
            <td align="right">说&nbsp;&nbsp;&nbsp;&nbsp;明</td>
            <td colspan="3"><textarea name="Remark2" style="width:470px" id="Remark2"><?php  echo $lmanRow["Remark"]?></textarea></td>
          </tr>

          <tr>
            <td align="right">更新原因</td>
            <td colspan="3"><textarea name="UpdateReasons" style="width:470px" id="UpdateReasons" dataType="Require" msg="请填写更新的内容" rows="3"></textarea></td>
          </tr>

  </table></td></tr></table>


<?php 
//步骤6：表尾
include "../model/subprogram/add_model_b.php";
?>
<script>
window.onload=function(){
    ChangeInfo();
    
}

function ChangeInfo(){
    var ObjectSignValue=document.getElementById("ObjectSign").value*1;
    switch(ObjectSignValue){
	    case 2://客户
	      setElementHidden("ClientTable",false);
	      setClientTableElement(false);
	      setElementHidden("SupplierTable",true);
	      setSupplierTableElement(true);
		  setElementHidden("TradeTable",false);
          setTradeTableElement(false);
	      break;
	   case 3://供应商
	      setElementHidden("ClientTable",true);
	      setClientTableElement(true);
	      setElementHidden("SupplierTable",false);
	      setSupplierTableElement(false);
	      setElementHidden("TradeTable",true);
		  setTradeTableElement(true);
	      break;
	  case 1://客户/供应商
	      setElementHidden("ClientTable",false);
	      setElementHidden("SupplierTable",false);
	      setClientTableElement(false);
	      setSupplierTableElement(false);
		  setElementHidden("TradeTable",false);
          setTradeTableElement(false);
	      break;
	  default:
	      setElementHidden("ClientTable",true);
	      setElementHidden("SupplierTable",true);
		  setElementHidden("TradeTable",true);
		  setTradeTableElement(true);
	      setClientTableElement(true);
	      setSupplierTableElement(true);
		  
	      break;
    }
     
     ChangeStatus();

}

//add by ckt 2017-12-28
function setTradeTableElement(hidden){
    setElementHidden("TradeNo",hidden);
    setElementHidden("ProofreaderName",hidden);
    setElementHidden("Proofreader1Name",hidden);
    setElementHidden("CheckerName",hidden);
    setElementHidden("Members",hidden);
    setElementHidden("ProducerName",hidden);
    setElementHidden("CmptTotal",hidden);
    if (hidden){
        document.getElementById("TradeNo").value='';
        document.getElementById("ProofreaderName").value='';
        document.getElementById("Proofreader1Name").value='';
        document.getElementById("CheckerName").value='';
        document.getElementById("Members").value='';
        document.getElementById("ProducerName").value="";
        document.getElementById("CmptTotal").value="";
    }
}

function setClientTableElement(hidden){
	setElementHidden("PayType",hidden);
	setElementHidden("PayMode",hidden);
	setElementHidden("BankId",hidden);
	setElementHidden("SaleMode",hidden);
	setElementHidden("ChinaSafeSign",hidden);
	setElementHidden("PriceTerm",hidden);
}

function setSupplierTableElement(hidden){
	setElementHidden("ProviderType",hidden);
	setElementHidden("GysPayMode",hidden);
	setElementHidden("Bank",hidden);
	setElementHidden("BankUID",hidden);
	setElementHidden("BankAccounts",hidden);
	setElementHidden("InvoiceTax",hidden);
	setElementHidden("LegalPerson",hidden);
	setElementHidden("BulidTime",hidden);
	setElementHidden("ValidTime",hidden);
	setElementHidden("Capital",hidden);
	setElementHidden("CompanySize",hidden);
	setElementHidden("StaffNum",hidden);
	setElementHidden("CompanyNature",hidden);
	setElementHidden("CompanyCategory",hidden);
	setElementHidden("MainBusiness",hidden);
	setElementHidden("CompanyPicture",hidden);
	setElementHidden("DealRange",hidden);
	setElementHidden("BusinessLicence",hidden);
	setElementHidden("TaxCertificate",hidden);
	setElementHidden("BankPermit",hidden);
	setElementHidden("TaxpayerIdentifi",hidden);
	setElementHidden("SalesAgreement",hidden);
	setElementHidden("PaymentOrder",hidden);
	setElementHidden("ProductionCertificate",hidden);
}



function ChangeStatus(){
	var ObjectSignValue=document.getElementById("ObjectSign").value;
    var CurrencyValue=  document.getElementById("Currency").value;
    
    if(CurrencyValue==5 && (ObjectSignValue==1 ||ObjectSignValue==3)){
       setElementHidden("Tr_IBAN",false);
    }
    else{
	   setElementHidden("Tr_IBAN",true); 
    }
}

function setElementHidden(elementName,hidden){
	if (hidden){
		document.getElementById(elementName).disabled="disabled";
        document.getElementById(elementName).hidden="hidden";
	}
	else{
		document.getElementById(elementName).disabled="";
        document.getElementById(elementName).hidden="";
	}
}

function ChinaSafeSignChange(){
	var ChinaSafeSign=document.getElementById("ChinaSafeSign");
	var ChinaSafe=document.getElementById("ChinaSafe");
	if (ChinaSafeSign.value==1){
		ChinaSafe.disabled="";
	}
	else{
		ChinaSafe.disabled="disabled";
	}
}
//内部员工选择函数 by ckt 2017-12-28
function searchStaffId(Action, SearchNum,BackName,BackId) {
    var SafariReturnValue = document.getElementById('SafariReturnValue');
    if (!arguments[4]) {
        var num = Math.random();
        SafariReturnValue.value = "";
        SafariReturnValue.callback = 'searchStaffId("","'+SearchNum+'","'+BackName+'","'+BackId+'",true)';
        var url = "/public/staff_s1.php?r=" + num +"&uType=1&tSearchPage=staff&fSearchPage=tradeobject&Action=" + Action+"&SearchNum="+SearchNum;
        openFrame(url, 696, 650);//url需为绝对路径
        return false;
    }
    if (SafariReturnValue.value) {
        if(SearchNum==1){//单选
            var FieldArray = SafariReturnValue.value.split("^^");
            document.getElementById(BackId).value = FieldArray[0];
            document.getElementById(BackName).value = FieldArray[1];
        }else{//多选
            var FieldArray = SafariReturnValue.value.split("``");
            var ReturnValue = '';
            var iVal;
            FieldArray.forEach(function(Val){
                iVal = Val.split("^^");
                ReturnValue += iVal[1]+',';
            });
            document.getElementById(BackName).value = ReturnValue.substr(0, ReturnValue.length-1);
        }
        SafariReturnValue.value = "";
        SafariReturnValue.callback = "";
    }
}
</script>