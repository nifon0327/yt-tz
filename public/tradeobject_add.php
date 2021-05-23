<?php
include "../model/modelhead.php";
echo"<SCRIPT src='../model/tradeobject.js' type=text/javascript></script>";
ChangeWtitle("$SubCompany 新增交易对象资料");//需处理
$nowWebPage =$funFrom."_add";
$toWebPage  =$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Estate,$Estate,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=950;$tableMenuS=650;
include "../model/subprogram/add_model_t.php";
$EssentialSign="<span style='color:#F00;'>*</span>";
$fromcSign = $fromcSign == ""?7:$fromcSign;
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
<tr><td class="A0011">
        <table width="800" border="0" align="center" cellspacing="5">

       <tr>
         <td width="90" align="right" scope="col"><?php echo $EssentialSign?>交易对象类型</td>
         <td colspan="3" scope="col">
		  <?php
			 $typeResult = mysql_query("SELECT Id,Name FROM $DataIn.trade_type WHERE Estate=1 order by OrderBy",$link_id);
			 while ($typeRow = mysql_fetch_array($typeResult))
			 {
					$Id=$typeRow["Id"];
					$Name=$typeRow["Name"];
					echo"<option value='$Id'>$Name</option>";
				}
			?>
	    <?php
	        $ObjectSign="";
            $TypeIdWidth="470px";
            $onChangeFunction="ChangeInfo()";
			include "../model/subselect/TradeType.php";
			?>

         </td>
       </tr>

		 <tr>
            <td width="90" scope="col" align="right"><?php echo $EssentialSign?>结付货币</td>
            <td colspan="3" scope="col">
			<?php
            $TypeIdWidth="470px";
            $onChangeFunction="ChangeStatus()";
			include "../model/subselect/Currency.php";
			?>
        </td></tr>

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
                    echo "<input name='TempCompanySign[]' type='checkbox' value='$thiscSign' $checked $readonly /><span style='color:$ColorValue;'>$CShortName</span>&nbsp;&nbsp;&nbsp;&nbsp;";
                    $x++;
                    }
			?>
            </td>
          </tr>


          <tr id="Tr_IBAN" name="Tr_IBAN" disabled="disabled" hidden="hidden">
            <td valign="top" align="right">IBAN</td>
            <td colspan="3"><input name="IBAN" type="text"  style="width:470px" id="IBAN"></td>
          </tr>

          <tr>
            <td  align="right"><?php echo $EssentialSign?>公司名称</td>
            <td colspan="3"><input name="Company" type="text" id="Company" style="width:470px;" dataType="LimitB" max="50" min="2" msg="必须在2-50个字节之内"></td>
          </tr>
          <tr>
            <td align="right"><?php echo $EssentialSign?>公司简称</td>
            <td colspan="3"><input name="Forshort" type="text" id="Forshort" style="width:470px;" DPNameCheck="trade_object" dataType="LimitB" max="20" min="2" msg="必须在2-20个字节之内"></td>
          </tr>
          <tr>
            <td align="right"><?php echo $EssentialSign?>国家地区</td>
		   	<td width="210" scope="col">
              <input name="Area" type="text" id="Area" style="width:190px;" dataType="LimitB" max="50" min="2" msg="2-50个字节之内"></td>
            <td align="right">LOGO</td>
            <td width="410"><input name="Logo" type="file" id="Logo" style="width:190px"  accept="png" Row="3" Cel="1" ></td>
          </tr>
          <tr>
            <td align="right"><?php echo $EssentialSign?>电&nbsp;&nbsp;&nbsp;&nbsp;话</td>
            <td ><input name="Tel" type="text" id="Tel" style="width:190px;"  dataType="Require" msg="未填"></td>
            <td align="right">传&nbsp;&nbsp;&nbsp;&nbsp;真</td>
            <td ><input name="Fax" type="text" id="Fax" style="width:190px;" require="false"></td>
          </tr>

         <tr>
            <td align="right">网&nbsp;&nbsp;&nbsp;&nbsp;址</td>
            <td width="150"><input name="Website" type="text" id="Website" style="width:190px;"></td>
            <td align="right">邮政编码</td>
            <td  width="410"><input name="ZIP" type="text" id="ZIP" style="width:190px;" require="false" dataType="Custom" regexp="^[1-9]\d{5}$" msg="邮政编码不存在"></td>
          </tr>
          <tr>
            <td align="right"><?php echo $EssentialSign?>地&nbsp;&nbsp;&nbsp;&nbsp;址</td>
            <td colspan="3"><input name="Address" type="text" require="false" id="Address" style="width:470px;" dataType="LimitB" max="100" min="2" msg="必须在100个字之内"></td>
          </tr>
          <tr>
            <td valign="top" align="right">快递帐户</td>
            <td colspan="3"><textarea name="ExpNum" style="width:470px" id="ExpNum"></textarea></td>
          </tr>
          <tr>
            <td valign="top" align="right">备&nbsp;&nbsp;&nbsp;&nbsp;注</td>
            <td colspan="3"><textarea name="Remark" style="width:470px" id="Remark"></textarea></td>
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
              <option value="" selected>请选择</option>
					<option value="1">款到发货</option>
                    <option value="2">货到付款</option>
            </select></td>
            <td align="right" width="90"><?php echo $EssentialSign?>收款方式</td>
            <td width="410"><select name="PayMode" id="PayMode" style="width:156px" datatype="Require" msg="未选">
              <option value="" selected>请选择</option>
              <?php
			$PayModeResult = mysql_query("SELECT Id,Name,eName 
			FROM $DataIn.clientpaymode WHERE Estate=1 order by Id",$link_id);
			if($PayModeRow = mysql_fetch_array($PayModeResult)){
				$i=1;
				do{
					$Id=$PayModeRow["Id"];
					$Name=$PayModeRow["Name"];
					$eName=$PayModeRow["eName"];
					echo"<option value='$Id'>$i $Name($eName)</option>";
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
					$SelectedSTR=$Id==5?" selected":"";
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
            <td valign="top" align="right"><?php echo $EssentialSign?>PriceTerm</td>
            <td colspan="3"><input type="text" name="PriceTerm" style="width:470px" id="PriceTerm"></td>
          </tr>

           <tr>
            <td align="right"><?php echo $EssentialSign?>中国信保</td>
            <td ><select name="ChinaSafeSign" id="ChinaSafeSign" style="width:150px" datatype="Require" msg="未选" onchange="ChinaSafeSignChange()">
              <option value="" selected>请选择</option>
					<option value="0">否</option>
					<option value="1">是</option>
            </select></td>
            <td align="right">限&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;额</td>
            <td ><input type="text" name="ChinaSafe" style="width:156px" id="ChinaSafe" value="$" disabled="disabled"></td>
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
                $TypeFrom="";$ProviderType="";
                $onChangeFunction="ProviderTypeChange()";
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
					echo"<option value='$_Id'>$_Name</option>";
					$i++;
					}while ($PayModeRow = mysql_fetch_array($PayModeResult));
				}
			 ?>
				</select></td>
          </tr>
            <tr>
            <td align="right"><?php echo $EssentialSign?>银行名称</td>
            <td colspan="3"><input name="Bank" type='text' style="width:470px" id="Bank" dataType="Require" msg="未填" /></td>
          </tr>
          <tr>
            <td align="right"><?php echo $EssentialSign?>户&nbsp;&nbsp;&nbsp;&nbsp;名</td>
            <td colspan="3"><input name="BankUID" type='text' style="width:470px" id="BankUID" dataType="Require" msg="未填"></td>
          </tr>
          <tr>
            <td align="right"><?php echo $EssentialSign?>帐&nbsp;&nbsp;&nbsp;&nbsp;号</td>
            <td colspan="3"><input name="BankAccounts" type='text' style="width:470px" id="BankAccounts" dataType="Require" msg="未填"></td>
          </tr>

           <tr>
            <td align="right" ><?php echo $EssentialSign?>增值税率</td>
            <td width="210"><select name="AddValueTax" id="AddValueTax" style="width:150px" dataType="Require" msg="未选">
				<option value="" selected>请选择</option>
              <?php
			   $AddTaxResult = mysql_query("SELECT Id, Name,Value FROM $DataIn.provider_addtax WHERE  Estate=1 order by Id",$link_id);
			if($AddTaxRow = mysql_fetch_array($AddTaxResult)){
				$i=1;
				do{
				    $AddTaxId=$AddTaxRow["Id"];
					$AddTaxName=$AddTaxRow["Name"];
					$AddTaxValue=$AddTaxRow["Value"];
					echo"<option value='$AddTaxId' >$AddTaxName</option>";
					$i++;
					}while ($AddTaxRow = mysql_fetch_array($AddTaxResult));
				}
			?>
				</select></td>
				<td align="right">加税点</td>
            <td width="420"><input name="InvoiceTax" type="text" id="InvoiceTax" style="width:150px;" dataType="Currency" msg="输入错误" value="0.0" > ％&nbsp;&nbsp;</td>
          </tr>

        <tr>
            <td align="right" width="90"><?php echo $EssentialSign?>法&nbsp;&nbsp;&nbsp;&nbsp;人</td>
            <td width="210"><input name="LegalPerson" type="text" id="LegalPerson" style="width:150px;" datatype="Require" msg="未填" ></td>

            <td align="right" width="90"><?php echo $EssentialSign?>成立时间</td>
            <td width="420"><input name="BulidTime" type="text" id="BulidTime" style="width:160px;" maxlength="10" value="0000-00-00" onfocus="WdatePicker()"  dataType="Date" format="ymd" msg="日期不正确" readonly/></td>
          </tr>
            <tr>
            <td align="right" width="90"><?php echo $EssentialSign?>有效期</td>
            <td width="210"><input name="ValidTime" type="text" id="ValidTime" style="width:150px;" maxlength="10" value="0000-00-00" onfocus="WdatePicker()"  dataType="Date" format="ymd" msg="日期不正确" readonly/></td>

            <td align="right" width="90"><?php echo $EssentialSign?>注册资本</td>
            <td width="420"><input name="Capital" type="text" id="Capital" style="width:160px;" datatype="Require" msg="未填"/></td>
          </tr>

        <tr>
            <td align="right" width="90"><?php echo $EssentialSign?>规&nbsp;&nbsp;&nbsp;&nbsp;模</td>
            <td width="210"><input name="CompanySize" type="text" id="CompanySize" style="width:150px;" datatype="Require" msg="未填"></td>

            <td align="right" width="90"><?php echo $EssentialSign?>员工人数</td>
            <td width="420"><input name="StaffNum" type="text" id="StaffNum" style="width:160px;" datatype="Require" msg="未填" /></td>
          </tr>


           <tr>
            <td align="right">营业执照</td>
            <td width="210"><input name="BusinessLicence" type="file" id="BusinessLicence" style="width:190px" accept="jpg" Row="8" Cel="1" ></td>
            <td align="right">税务登记证</td>
            <td width="420"><input name="TaxCertificate" type="file" id="TaxCertificate" style="width:190px"  accept="jpg" Row="9" Cel="1" ></td>
            </tr>



           <tr>
            <td align="right">开户许可</td>
            <td width="210"><input name="BankPermit" type="file" id="BankPermit" style="width:190px" accept="jpg" Row="10" Cel="1"></td>
            <td align="right">纳税人认定书</td>
            <td width="420"><input name="TaxpayerIdentifi" type="file" id="TaxpayerIdentifi" style="width:190px" accept="jpg" Row="11" Cel="1"></td>
            </tr>
            <tr>
            <td align="right">付款委托书</td>
            <td width="210"><input name="PaymentOrder" type="file" id="PaymentOrder" style="width:190px"  accept="jpg" Row="12" Cel="1"></td><td align="right">合作协议</td>
            <td width="420"><input name="SalesAgreement" type="file" id="SalesAgreement" style="width:190px"  accept="jpg" Row="13" Cel="1"></td>
          </tr>

           <tr>
            <td align="right">ROSH报告</td>
            <td colspan="3"><input name="ProductionCertificate" type="file" id="ProductionCertificate" style="width:190px" dataType="Filter" msg="jpg的文件格式" accept="jpg" Row="14" Cel="1"></td>
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
					echo"<option value='$thisNatureId' >$thisNatureName</option>";
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
					echo"<option value='$thisCategoryId' >$thisCategoryName</option>";
					$i++;
					}while ($CategoryRow = mysql_fetch_array($CategoryResult));
				}
			?>
            </select></td>
          </tr>

          <tr>
            <td valign="top" align="right">经营范围</td>
            <td colspan="3"><textarea name="DealRange" type="text" id="DealRange" style="width:470px;"></textarea></td>
          </tr>
           <tr>
            <td valign="top" align="right">主营业务</td>
            <td colspan="3"><textarea name="MainBusiness" type="text" id="MainBusiness" style="width:470px;"></textarea></td>
          </tr>
            <tr>
            <td valign="top" align="right">公司简介</td>
            <td colspan="3"><textarea name="Description" type="text" id="Description" style="width:470px;"></textarea></td>
          </tr>
           <tr>
            <td valign="top" align="right">工厂实拍</td>
            <td colspan="3"><input name="CompanyPicture" type="file" id="CompanyPicture" style="width:190px" dataType="Filter" msg="jpg的文件格式" accept="jpg" Row="14" Cel="1"></td></td>
          </tr>
          </table>
         </td></tr>
        <!--增加项目信息相关 by ckt 2017-12-25-->
        <tr id="TradeTable" name="TradeTable" disabled="disabled" hidden="hidden">
            <td class="A0011">
                <table width="800" border="0" align="center" cellspacing="5">
                    <tbody>
                    <tr>
                        <td ></td>
                        <td colspan="3" align="left"><span class="redB">项目信息</span></td>
                    </tr>
                    <tr>
                        <td align="right" width="88" ><?php echo $EssentialSign?>项目编号</td>
                        <td width="202"><input name="TradeNo" type="text" id="TradeNo" style="width:150px" maxlength="15" datatype="Require" msg="未填"></td>
                        <td align="right" width="89"><?php echo $EssentialSign?>校对者</td>
                        <td>
                            <input name="ProofreaderName" onclick="searchStaffId(22,1,'ProofreaderName','Proofreader');" type="text" id="ProofreaderName" style="width:165px" datatype="Require" msg="未填" readonly="readonly">
                            <input name="Proofreader" id="Proofreader" type="text" hidden="hidden" />
                        </td>
                    </tr>
                    <tr>
                        <td align="right" ><?php echo $EssentialSign?>复校者 </td>
                        <td>
                            <input name="Proofreader1Name" onclick="searchStaffId(22,1,'Proofreader1Name','Proofreader1');" type="text" id="Proofreader1Name" style="width:150px" readonly="readonly" datatype="Require" msg="未填">
                            <input name="Proofreader1" id="Proofreader1" type="text" hidden="hidden" />
                        </td>
                        <td align="right" ><?php echo $EssentialSign?>审核人</td>
                        <td>
                            <input name="CheckerName" onclick="searchStaffId(22,1,'CheckerName','Checker');" type="text" id="CheckerName" style="width:165px" readonly="readonly" datatype="Require" msg="未填">
                            <input name="Checker" id="Checker" type="text" hidden="hidden" />
                        </td>
                    </tr>
                    <tr>
                        <td align="right" ><?php echo $EssentialSign?>项目人员 </td>
                        <td colspan="3"><textarea name="Members" onclick="searchStaffId(22,'','Members','')" type="text" id="Members" style="width:470px;" readonly="readonly" datatype="Require" msg="未填"></textarea></td>
                    </tr>
                    <tr>
                        <td align="right" ><?php echo $EssentialSign?>生产负责人</td>
                        <td>
                            <input name="ProducerName" onclick="searchStaffId(22,1,'ProducerName','Producer');" type="text" id="ProducerName" style="width:150px" readonly="readonly" datatype="Require" msg="未填">
                            <input name="Producer" id="Producer" type="text" hidden="hidden" />
                        </td>
                        <td align="right" ><?php echo $EssentialSign?>构件总数量</td>
                        <td><input name="CmptTotal" type="text" id="CmptTotal" style="width:165px" datatype="Number" msg="应为数字"></td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <!---->
        <tr><td class="A0011">
              <table width="800" border="0" align="center" cellspacing="5">
          <tr>
            <td ></td>
            <td colspan="3" align="left"><span class="redB">默认联系人信息</span></td>
          </tr>
          <tr>
            <td align="right" width="90"><?php echo $EssentialSign?>联 系 人</td>
            <td width="210"><input name="Linkman" type="text" id="Linkman" style="width:150px" dataType="Limit" max="20" min="2" msg="必须在2-20个字之内"></td>
            <td width="90" align="right"><?php echo $EssentialSign?>性&nbsp;&nbsp;&nbsp;&nbsp;别</td>
            <td width="410">
              <select name="Sex" id="Sex" style="width:165px" dataType="Require" msg="未选">
			  <option value="">请选择</option>
			  <option value="0">女</option>
              <option value="1">男</option>
              </select></td>
          </tr>
          <tr>
            <td align="right"><?php echo $EssentialSign?>职&nbsp;&nbsp;&nbsp;&nbsp;务</td>
            <td ><input name="Headship" type="text" id="Headship" style="width:150px" maxlength="20"  dataType="Require" msg="未填"></td>
            <td align="right">昵&nbsp;&nbsp;&nbsp;&nbsp;称</td>
            <td ><input name="Nickname" type="text" id="Nickname" style="width:165px" maxlength="20"></td>
          </tr>
          <tr>
            <td align="right">移动电话</td>
            <td><input name="Mobile" type="text" id="Mobile" style="width:150px" require="false"></td>
            <td align="right">固定电话</td>
            <td><input name="Tel2" type="text" id="Tel2" style="width:165px" require="false"></td>
          </tr>
          <tr>
            <td align="right">MSN</td>
            <td colspan="3"><input name="MSN" type="text" id="MSN" style="width:470px;" ></td>
          </tr>
          <tr>
            <td align="right">SKYPE</td>
            <td colspan="3"><input name="SKYPE" type="text" id="SKYPE" style="width:470px;"></td>
          </tr>
          <tr>
            <td align="right">邮件地址</td>
            <td colspan="3"><input name="Email" type="text" id="Email" style="width:470px;" require="false" dataType="Email" msg="信箱格式不正确"></td>
          </tr>

			 <tr>
            <td width="89" scope="col" align="right"><?php echo $EssentialSign?>我司联络人</td>
            <td colspan="3" scope="col">
			<select name="Staff_Number" id="Staff_Number" style="width:470px" dataType="Require" msg="未选">

			<option value="">请选择</option>
			<?php

			$Staff_Result = mysql_query("SELECT M.Number,M.Name,B.Name AS Branch FROM $DataPublic.staffmain  M
										LEFT JOIN $DataPublic.branchdata B ON B.Id=M.BranchId
										WHERE M.Estate=1 AND M.BranchId IN (3,4,110)  ORDER BY M.BranchId,M.Id",$link_id);
			if($staff_Row = mysql_fetch_array($Staff_Result)){
				do{
					$Number=$staff_Row["Number"];
					$Name=$staff_Row["Name"];
					$Branch=$staff_Row["Branch"];
					echo"<option value='$Number'>$Branch - $Name</option>";
					}while ($staff_Row = mysql_fetch_array($Staff_Result));
				}
			?>
            </select>
			</td></tr>



          <tr>
            <td align="right">说&nbsp;&nbsp;&nbsp;&nbsp;明</td>
            <td colspan="3"><textarea name="Remark2" style="width:470px" id="Remark2"></textarea></td>
          </tr>
  </table></td></tr></table>
<?php
//步骤5：
include "../model/subprogram/add_model_b.php";
?>