<?php 
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新客户寄送记录");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT * FROM $DataIn.ch10_samplemail WHERE 1 AND Id='$Id' LIMIT 1",$link_id));
$DataType=$upData["DataType"];
$TempSTR="DataTypeSTR".strval($DataType);
$$TempSTR="selected";	
$theCompanyId=$upData["CompanyId"];
$LinkMan=$upData["LinkMan"];
$ExpressNO=$upData["ExpressNO"];
$Pieces=$upData["Pieces"];
$Weight=$upData["Weight"];
$Qty=$upData["Qty"];
$Price=$upData["Price"];
$Amount=$upData["Amount"];
$cSign=$upData["cSign"];
$PayType=$upData["PayType"];
$PayTypeSTR="PayTypeSTR".strval($PayType); 
$$PayTypeSTR="selected";	

$ServiceType=$upData["ServiceType"];
$ServiceTypeSTR="ServiceTypeSTR".strval($ServiceType); 
$$ServiceTypeSTR="selected";	

$HandledBy=$upData["HandledBy"];
$Description=$upData["Description"];
$Remark=$upData["Remark"];
$Schedule=$upData["Schedule"];
$SendDate=$upData["SendDate"];
$ReceiveDate=$upData["ReceiveDate"];
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,CompanyId,$CompanyId,Estate,$Estate,chooseDate,$chooseDate";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
        <table width="800" border="0" align="center" cellspacing="0" id="NoteTable">
		<tr>
            <td width="130" scope="col" align="right">模&nbsp;&nbsp;&nbsp;&nbsp;板</td>
            <td scope="col">
				<select name="DataType" id="DataType" style='width: 380px;' dataType="Require"  msg="未选择">
                  <option value="1" <?php  echo $DataTypeSTR1?>>中文资料</option>
                  <option value="2" <?php  echo $DataTypeSTR2?>>英文资料</option>
              </select></td>
	      </tr>
        <tr>
        <td scope="col" align="right">所属公司</td>
        <td scope="col">
          <?php 
          include "../model/subselect/cSign.php";
		  ?>
		</td></tr>
	      
		<tr>
          <td height="1" scope="col" align="right">收&nbsp;件&nbsp;人</td>
          <td scope="col"><select name="LinkMan" id="LinkMan" style='width: 380px;' dataType="Require" msg="未选择">
            <?php 
			$LinkMan_Result= mysql_query("SELECT A.Id,A.LinkMan,C.Forshort FROM $DataIn.ch10_mailaddress A 
			,$DataIn.trade_object C WHERE C.CompanyId=A.CompanyId
			ORDER BY C.OrderBy DESC,A.Id",$link_id);
			if($LinkManRow = mysql_fetch_array($LinkMan_Result)){
				do{
					if($LinkMan==$LinkManRow["Id"]){
						echo"<option value='$LinkManRow[Id]' selected>$LinkManRow[Forshort] - $LinkManRow[LinkMan]</option>";
						}
					else{
						echo"<option value='$LinkManRow[Id]'>$LinkManRow[Forshort] - $LinkManRow[LinkMan]</option>";
						}
					} while($LinkManRow = mysql_fetch_array($LinkMan_Result));
				}
			?>
          </select></td>
          </tr>
		<tr>
		  <td scope="col" align="right">快递公司</td>
		  <td scope="col"><select name='theCompanyId' id='theCompanyId' size='1' style='width: 380px;' dataType="Require" msg="未选择">
            <?php 
		$fResult = mysql_query("SELECT * FROM $DataPublic.freightdata WHERE Estate=1 order by Id",$link_id);
		if($fRow = mysql_fetch_array($fResult)){
			do{
				if($theCompanyId==$fRow["CompanyId"]){
					echo"<option value='$fRow[CompanyId]' selected>$fRow[Forshort]</option>";
					}
				else{
					echo"<option value='$fRow[CompanyId]'>$fRow[Forshort]</option>";
					}
				} while ($fRow = mysql_fetch_array($fResult));
			}
		?>
          </select></td>
		  </tr>
		<tr>
		  <td scope="col" align="right">寄件日期</td>
		  <td scope="col"><input name="theDate" type="text" id="theDate" style='width: 380px;' value="<?php  echo $SendDate?>" dataType="Date" format="ymd" msg="未选择或格式不对" onfocus="WdatePicker()" readonly></td>
		  </tr>
		<tr>
		  <td align="right" scope="col">提单号码</td>
		  <td scope="col"><input name="ExpressNO" type="text" id="ExpressNO" style='width: 380px;' value="<?php  echo $ExpressNO?>" dataType="Require" msg="未填写"></td>
		  </tr>
		<tr>
		  <td align="right" scope="col">件&nbsp;&nbsp;&nbsp;&nbsp;数</td>
		  <td scope="col"><input name="Pieces" type="text" id="Pieces" style='width: 380px;' value="<?php  echo $Pieces?>" dataType="Number" msg="未填写或格式不对"></td>
		  </tr>
		<tr>
		  <td scope="col" align="right">重&nbsp;&nbsp;&nbsp;&nbsp;量</td>
		  <td scope="col"><input name="Weight" type="text" id="Weight" style='width: 380px;' value="<?php  echo $Weight?>" dataType="Currency" msg="未填写或格式不对"></td>
		  </tr>
		<tr>
		  <td scope="col" align="right">单&nbsp;&nbsp;&nbsp;&nbsp;价</td>
		  <td scope="col"><input name="Price" type="text" id="Price" style='width: 380px;' value="<?php  echo $Price?>" dataType="Currency" msg="未填写或格式不对"></td>
		  </tr>
		<tr>
		  <td align="right" scope="col">费&nbsp;&nbsp;&nbsp;&nbsp;用</td>
		  <td scope="col"><input name="Amount" type="text" id="Amount" style='width: 380px;' value="<?php  echo $Amount?>" dataType="Currency" msg="未填写或格式不对"></td>
		  </tr>
		<tr>
		  <td scope="col" align="right">付款方式</td>
		  <td scope="col"><select name="PayType" id="PayType" style='width: 380px;' dataType="Require"  msg="未选择">
		    <option value="1" <?php  echo $PayTypeSTR1?>>CASH 现付 <?php  echo $PayTypeSTR1?></option>
		    <option value="2" <?php  echo $PayTypeSTR2?>>A/C 月结 <?php  echo $PayTypeSTY2?></option>
		    <option value="3" <?php  echo $PayTypeSTR3?>>PP 预付</option>
		    <option value="4" <?php  echo $PayTypeSTR4?>>CC 到付</option>
		    </select></td>
		  </tr>
		<tr>
		  <td align="right" scope="col">服务类型</td>
		  <td scope="col"><select name="ServiceType" id="ServiceType" style='width: 380px;' dataType="Require"  msg="未选择">
		    <option value="1" <?php  echo $ServiceTypeSTR1?>>PARCEL 包裹</option>
            <option value="2" <?php  echo $ServiceTypeSTR2?>>DOCUMENT 文件</option>
            <option value="3" <?php  echo $ServiceTypeSTR3?>>OTHERS 其它</option>
            </select></td>
		  </tr>
		<tr>
		  <td scope="col" align="right">物品名称</td>
		  <td scope="col"><input name="Description" type="text" id="Description" style='width: 380px;' value="<?php  echo $Description?>" dataType="Require" msg="未填写"></td>
		</tr>
		<tr>
		  <td align="right" scope="col">数&nbsp;&nbsp;&nbsp;&nbsp;量</td>
		  <td scope="col"><input name="Qty" type="text" id="Qty" style='width: 380px;' value="<?php  echo $Qty?>" dataType="Number" msg="未填写或格式不对"></td>
		  </tr>
		<tr>
		  <td valign="top" scope="col" align="right">备&nbsp;&nbsp;&nbsp;&nbsp;注</td>
		  <td scope="col"><textarea name="Remark" cols="50" rows="2" id="Remark" dataType="Require"  msg="未填写"><?php  echo $Remark?></textarea></td>
		</tr>
		<tr>
		  <td valign="top" scope="col" align="right">经 手 人</td>
		  <td scope="col">
		  <select name="HandledBy" id="HandledBy" style='width: 380px;' dataType="Require"  msg="未填写">
		  <?php 
			$result = mysql_query("SELECT U.Number,M.Name 
			FROM $DataIn.usertable U,$DataPublic.staffmain M WHERE M.Number>10001 AND M.Number=U.Number AND M.Estate='1' ORDER BY M.BranchId,M.JobId,M.Number",$link_id);
			if($myrow = mysql_fetch_array($result)){
				do{
					$Number=$myrow["Number"];
					$Name=$myrow["Name"];
					if($Number==$HandledBy){
						echo "<option value='$Number' selected>$Name</option>";
						}
					else{
						echo "<option value='$Number'>$Name</option>";
						}
					}while ($myrow = mysql_fetch_array($result));
				} 
			?>
          </select></td>
		  </tr>
		<tr>
		  <td scope="col" align="right">签收日期</td>
		  <td scope="col"><input name="ReceiveDate" type="text" id="ReceiveDate" size="78" value="<?php  echo $ReceiveDate?>"></td>
		  </tr>
		<tr>
		  <td scope="col" align="right">进度图片</td>
		  <td scope="col"><input name="Schedule" type="file" id="Schedule" size="66" DataType="Filter" Accept="jpg" Msg="格式不对,请重选" Row="16" Cel="1"></td>
		  </tr>
		  <?php 
		  if($Schedule==1){
			echo"<tr><td >&nbsp;</td><td scope='col'><input name='oldSchedule' type='checkbox' id='oldSchedule' value='0'><LABEL for='oldSchedule'>删除已传进度图片</LABEL></td></tr>";}
		  ?>
        </table>
		</td>
	</tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>