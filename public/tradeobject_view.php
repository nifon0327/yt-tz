<?php 
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 显示交易对象资料");//需处理
$fromWebPage=$funFrom."_".$From;		
$nowWebPage =$funFrom."_view";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$upResult = mysql_query("SELECT A.Id,A.CompanyId,A.Letter,A.Forshort,A.ProviderType,A.PayMode,A.GysPayMode,A.Estate,A.Date,A.Operator,A.Locks,A.ExpNum,A.ObjectSign,A.Currency,A.FscNo,
A.PayType,A.BankId,A.Staff_Number,B.Tel,B.Fax,B.Website,B.Remark,B.Area,B.Company,B.ZIP,B.Address,B.Bank,C.Symbol,A.Judge,A.PackFile,A.TipsFile,A.Prepayment,A.LimitTime,K.Title AS BankTitle,E.Name AS staff_Name ,A.PriceTerm,A.UpdateReasons
FROM $DataIn.trade_object  A
LEFT JOIN $DataIn.companyinfo B ON B.CompanyId=A.CompanyId AND B.Type=8
LEFT JOIN $DataPublic.currencydata C ON C.Id=A.Currency
LEFT JOIN $DataPublic.staffmain E ON E.Number=A.Staff_Number
LEFT JOIN $DataPublic.my2_bankinfo K ON K.Id=A.BankId 
WHERE  A.CompanyId='$CompanyId'",$link_id); 
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
	$ExpNum=$upRow["ExpNum"];
	$UpdateReasons=$upRow["UpdateReasons"];
	$BankId=$upRow["BankId"];
	$PayType=$upRow["PayType"];
	$ObjectSign=$upRow["ObjectSign"];
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
	$BankId=$upRow["BankId"];

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
             switch($ObjectSign){
              case "1": $ObjectStr="客户/供应商";break;
              case "2": $ObjectStr="客户";break;
             case "3": $ObjectStr="供应商";break;
             }
//步骤4：
$tableWidth=950;$tableMenuS=650;
$SaveSTR="NO";
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Orderby,$Orderby,Pagination,$Pagination,Page,$Page,LinkId,$LinkId,CompanyId,$CompanyId,ObjectSign,$ObjectSign";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
<tr><td class="A0011">
        <table width="800" border="0" align="center" cellspacing="5">
		 <tr>
		   	<td align="right">交易对象类型</td>
		   	<td colspan="3" scope="col"><input id="ObjectSign" name="ObjectSign" readonly type="text"  value="<?php echo  $ObjectStr?>"  style="width:470px"></td>
	      </tr> 
  <tr>
    <td scope="col" align="right">结付货币</td>
    <td colspan="3" scope="col">
		<?php 
		$CurrencyResult = mysql_query("SELECT A.Id,A.Name FROM $DataPublic.currencydata A WHERE A.Estate=1 AND A.ID='$Currency' ORDER BY A.Id",$link_id);
		$CurrencyName =mysql_result($CurrencyResult,0,"Name");	
		//币别不能乱改，因为会影响以前结付的货款 modify by zx 2013-11-21
		//include "../model/subselect/Currency.php";
		?>
        <input name="CurrencyName" type="text" id="CurrencyName" value="<?php  echo $CurrencyName?>" style="width:470px"  readonly="readonly" >
        <input name="Currency" type="hidden" id="Currency" value="<?php  echo $Currency ?>"/>       
  </td>
  </tr>

		 <tr>
		   	<td align="right">国家地区</td>
		   	<td colspan="3" scope="col">
              <input name="Area" type="text" id="Area" style="width:470px;"  value="<?php  echo $AreaStr?>" dataType="LimitB" max="50" min="2" msg="2-50个字节之内"></td>
	      </tr> 
          <tr>
            <td  align="right">公司名称</td>
            <td colspan="3"><input name="Company" type="text" id="Company"  value="<?php  echo $Company?>" style="width:470px;" dataType="LimitB" max="50" min="2" msg="必须在2-50个字节之内"></td>
          </tr>
          <tr>
            <td align="right">公司简称</td>
            <td colspan="3"><input name="Forshort" type="text" id="Forshort"  value="<?php  echo $Forshort?>" style="width:470px;" dataType="LimitB" max="12" min="2" msg="必须在2-12个字节之内"></td>
          </tr>
          <tr>
            <td align="right">公司电话</td>
            <td width="164"><input name="Tel" type="text" id="Tel" value="<?php  echo $Tel?>" style="width:190px;"></td>
            <td align="right">公司传真</td>
            <td width="410"><input name="Fax" type="text" id="Fax" value="<?php  echo $Fax?>" style="width:190px;" require="false"></td>
          </tr>

         <tr>
            <td align="right">网&nbsp;&nbsp;&nbsp;&nbsp;址</td>
            <td width="164"><input name="Website" type="text" id="Website" value="<?php  echo $Website?>" style="width:190px;"></td>
            <td align="right">邮政编码</td>
            <td width="410"><input name="ZIP" type="text" id="ZIP" style="width:190px;" value="<?php  echo $ZIP?>" require="false" dataType="Custom" regexp="^[1-9]\d{5}$" msg="邮政编码不存在"></td>
          </tr>
          <tr>
            <td align="right">通信地址</td>
            <td colspan="3"><input name="Address" type="text" require="false" id="Address" value="<?php  echo $Address?>" style="width:470px;" ataType="Limit" max="50" msg="必须在50个字之内"></td>
          </tr>
          <tr>
            <td valign="top" align="right">快递帐户</td>
            <td colspan="3"><textarea name="ExpNum" style="width:470px" id="ExpNum"><?php  echo $ExpNum?></textarea></td>
          </tr>
          <tr>
            <td valign="top" align="right">银行帐户</td>
            <td colspan="3"><textarea name="Bank" style="width:470px" id="Bank"><?php  echo $Bank?></textarea></td>
          </tr>
         <tr>
            <td ></td>
            <td colspan="3" align="left"><span class="redB">客户信息</span></td>
          </tr>
         <tr>
            <td align="right">收款性质</td>
            <td width="164"><select name="PayType" id="PayType" style="width:192px" datatype="Require" msg="未选择">
              <option value="" >请选择</option>
					<option value="0" <?php  echo $PayTypeSTR0?>>无</option>
					<option value="1" <?php  echo $PayTypeSTR1?>>款到发货</option>
                    <option value="2" <?php  echo $PayTypeSTR2?>>货到付款</option>
            </select></td>
            <td align="right">收款方式</td>
            <td width="410"><select name="PayMode" id="PayMode" style="width:192px" datatype="Require" msg="未选择">
              <option value="" selected>请选择</option>
              <?php 
			$PayModeResult = mysql_query("SELECT Id,Name FROM $DataPublic.clientpaymode WHERE Estate=1 order by Id",$link_id);
			if($PayModeRow = mysql_fetch_array($PayModeResult)){
				$i=1;
                  if($PayMode==0){
						echo"<option value='0' selected>无</option>";
                            }
				do{
					$Id=$PayModeRow["Id"];
					$Name=$PayModeRow["Name"];
					if($PayMode==$Id){
						echo"<option value='$Id' selected>$Name</option>";
						}
					else{
						echo"<option value='$Id'>$Name</option>";
						}
					}while ($PayModeRow = mysql_fetch_array($PayModeResult));
				}
			?>
            </select></td>
          </tr>

          <tr>
            <td scope="col" align="right">收款帐号</td>
            <td colspan="3" scope="col"><select name="BankId" id="BankId" style="width:470px" datatype="Require" msg="未选择">
              <option value="" selected>请选择</option>
              <?php 
			$PayBankResult = mysql_query("SELECT * FROM (
            SELECT Id,Title FROM $DataPublic.my2_bankinfo WHERE cSign='$Login_cSign' AND Estate=1
            UNION 
             SELECT 0 AS Id ,'无'  AS Title
            ) A  ORDER BY Id",$link_id);
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
          <tr>
            <td valign="top" align="right">PriceTerm</td>
            <td colspan="3"><input type="text" name="PriceTerm" style="width:470px" id="PriceTerm" value="<?php  echo $PriceTerm?>"></td>
          </tr>
          <tr>
            <td valign="top" align="right">备&nbsp;&nbsp;&nbsp;&nbsp;注</td>
            <td colspan="3"><textarea name="Remark" style="width:470px" id="Remark"><?php  echo $Remark?></textarea></td>
          </tr>
          <tr>
            <td ></td>
            <td colspan="3" align="left"><span class="redB">供应商信息</span></td>
          </tr>
       <tr>
         <td width="90" align="right" scope="col">供应商类型</td>
         <td colspan="3" scope="col">
		 <select name="ProviderType" id="ProviderType" style="width:470px" dataType="Require" msg="未选择">
		 <option value="">请选择</option>
		 <option value="-1" <?php  echo $ProviderTypeSTR9?>>无</option>
		 <option value="0" <?php  echo $ProviderTypeSTR0?>>自购供应商</option>
		 <option value="1" <?php  echo $ProviderTypeSTR1?>>代购供应商</option>
		 <option value="2" <?php  echo $ProviderTypeSTR2?>>客供供应商</option>
         </select></td>
       </tr>
		<tr>
            <td  align="right">付款方式</td>
            <td colspan="3" scope="col">
				<select name="GysPayMode" id="GysPayMode" style="width:470px" dataType="Require" msg="未选">
				<option value="">请选择</option>
                <option value="99" <?php echo $GysPayMode99?>>无</option>
                <option value="1" <?php echo $GysPayMode1?>>现金</option>
				<option value="0" <?php echo $GysPayMode0?>>30天</option>
                <option value="3" <?php echo $GysPayMode3?>>45天</option>
                <option value="2" <?php echo $GysPayMode2?>>60天</option>
				</select> &nbsp;&nbsp;<input type="checkbox" id="Prepayment" name="Prepayment" <?php  echo $PrepaymentSTR?>>先付款
	 <input type="hidden" id="Prepay" name="Prepay" value="<?php  echo $Prepayment?>"></td>
          </tr>
          <tr>
            <td align="right">限交货期</td>
            <td colspan="3"><input name="LimitTime" type="text" id="LimitTime" style="width:80px;" value='2' dataType="Number" msg="只能输入数字">周 &nbsp;&nbsp;&nbsp;&nbsp;<span style='color:#0000FF'>说明:0 不限制;1 可交货至本周;2 可交货至下周;如此类推。</span></td>
          </tr>
        <tr>
            <td align="right">企业法人</td>
            <td width="164"><input name="LegalPerson" type="text" id="LegalPerson" style="width:190px;" value="<?php  echo $LegalPerson?>" ></td>
            <td align="right">营业执照</td>
            <td width="410"><input name="BusinessLicence " type="file" id="BusinessLicence" style="width:190px" dataType="Filter" msg="非法的文件格式" accept="jpg" Row="9" Cel="1"></td>
          </tr>

     <tr>
            <td align="right">FSC证书编号</td>
            <td width="164"><input name="FscNo" type="text" id="FscNo" style="width:190px;"  value="<?php  echo $FscNo?>"/></td>
            <td align="right">税务登记证</td>
            <td width="410"><input name="TaxCertificate" type="file" id="TaxCertificate" style="width:190px" dataType="Filter" msg="非法的文件格式" accept="jpg" Row="10" Cel="1"></td>
          </tr>

     <tr>
            <td align="right"></td>
            <td width="164">&nbsp;</td>
            <td align="right">生产许可证</td>
            <td width="410"><input name="ProductionCertificate" type="file" id="ProductionCertificate" style="width:190px" dataType="Filter" msg="非法的文件格式" accept="jpg" Row="10" Cel="1"></td>
          </tr>  

          <tr>
            <td valign="top" align="right">公司简介</td>
            <td colspan="3"><textarea name="Description" type="text" id="Description" style="width:470px;" rows='2' ><?php  echo $Description?></textarea></td>
          </tr>
           <tr>
            <td valign="top" align="right">已获资质</td>
            <td colspan="3"><textarea name="Aptitudes" type="text" id="Aptitudes" style="width:470px;"><?php  echo $Aptitudes?></textarea></td>
          </tr>
           <tr>
            <td valign="top" align="right">质量能力</td>
            <td colspan="3"><textarea name="EAQF" type="text" id="EAQF" style="width:470px;"><?php  echo $EAQF?></textarea></td>
          </tr>
          <tr>
            <td valign="top" align="right">备&nbsp;&nbsp;&nbsp;&nbsp;注</td>
            <td colspan="3"><textarea name="Remark" style="width:470px;" id="Remark"><?php  echo $Remark?></textarea></td>
          </tr>

          <tr>
            <td ></td>
            <td colspan="3" align="left"><span class="redB">默认联系人信息</span></td>
          </tr>


          <tr>
            <td align="right">联 系 人</td>
            <td><input name="Linkman" type="text" id="Linkman" style="width:190px" value="<?php  echo $lmanRow["Name"]?>" dataType="Limit" max="20" min="2" msg="必须在2-20个字之内"></td>
            <td width="54" align="right">性&nbsp;&nbsp;&nbsp;&nbsp;别</td>
            <td>
              <select name="Sex" id="Sex" style="width:190px" dataType="Require" msg="未选择">
			  <option value="">请选择</option>
			  <option value="0" <?php  echo $SexSTR0?>>女</option>
              <option value="1" <?php  echo $SexSTR1?>>男</option>
              </select></td>
          </tr>
          <tr>
            <td align="right">职&nbsp;&nbsp;&nbsp;&nbsp;务</td>
            <td width="164"><input name="Headship" type="text" id="Headship" style="width:190px"  value="<?php  echo $lmanRow["Headship"]?>" maxlength="20"></td>
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
            <td width="89" scope="col" align="right">我司联络人</td>
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
            <td colspan="3"><textarea name="UpdateReasons" style="width:470px" id="UpdateReasons" dataType="Require" msg="请填写更新的内容" rows="3"><?php  echo $UpdateReasons?></textarea></td>
          </tr>

  </table></td></tr></table>


<?php 
//步骤6：表尾
include "../model/subprogram/add_model_b.php";
?>