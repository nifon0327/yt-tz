<?php 
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增样品寄送资料");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
        <table width="800" border="0" align="center" cellspacing="0">
		<tr>
            <td width="130" scope="col" align="right">模&nbsp;&nbsp;&nbsp;&nbsp;板</td>
            <td scope="col">
				<select name="DataType" id="DataType" style='width: 380px;' dataType="Require"  msg="未选择">
                  <option value="" selected>请选择</option>
                  <option value="1">中文资料</option>
                  <option value="2">英文资料</option>
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
            <option value="" selected>请选择</option>
            <?php 
			$LinkMan_Result= mysql_query("SELECT A.Id,A.LinkMan,C.Forshort FROM $DataIn.trade_object C ,$DataIn.ch10_mailaddress A
			WHERE  A.CompanyId=C.CompanyId
			ORDER BY C.OrderBy DESC,A.Id",$link_id);//C.cSign=$Login_cSign AND
			if($LinkManRow = mysql_fetch_array($LinkMan_Result)){
				do{
					echo"<option value='$LinkManRow[Id]'>$LinkManRow[Forshort] - $LinkManRow[LinkMan]</option>";
					} while($LinkManRow = mysql_fetch_array($LinkMan_Result));
				}
			?>
          </select></td>
          </tr>
		<tr>
		  <td scope="col" align="right">快递公司</td>
		  <td scope="col"><select name='theCompanyId' id='theCompanyId' size='1' style='width: 380px;' dataType="Require" msg="未选择">
		    <option value="" selected>请选择</option>
            <?php 
		$forward_result = mysql_query("SELECT * FROM $DataPublic.freightdata WHERE Estate=1 order by Id",$link_id);
		if($forward_myrow = mysql_fetch_array($forward_result)){
			do{
				echo"<option value='$forward_myrow[CompanyId]'>$forward_myrow[Forshort]</option>";
				} while ($forward_myrow = mysql_fetch_array($forward_result));
			}
		?>
          </select></td>
		  </tr>
		<tr>
		  <td scope="col" align="right">寄件日期</td>
		  <td scope="col"><input name="theDate" type="text" id="theDate" style='width: 380px;' value="<?php  echo date("Y-m-d");?>" dataType="Date" format="ymd" msg="未选择或格式不对" onfocus="WdatePicker()" readonly></td>
		  </tr>
		<tr>
		  <td align="right" scope="col">提单号码</td>
		  <td scope="col"><input name="ExpressNO" type="text" id="ExpressNO" style='width: 380px;' dataType="Require" msg="未填写"></td>
		  </tr>
		<tr>
		  <td align="right" scope="col">件&nbsp;&nbsp;&nbsp;&nbsp;数</td>
		  <td scope="col"><input name="Pieces" type="text" id="Pieces" style='width: 380px;' dataType="Number" msg="未填写或格式不对"></td>
		  </tr>
		<tr>
		  <td scope="col" align="right">重&nbsp;&nbsp;&nbsp;&nbsp;量</td>
		  <td scope="col"><input name="Weight" type="text" id="Weight" style='width: 380px;' dataType="Currency" msg="未填写或格式不对"></td>
		  </tr>
		<tr>
		  <td scope="col" align="right">单&nbsp;&nbsp;&nbsp;&nbsp;价</td>
		  <td scope="col"><input name="Price" type="text" id="Price" style='width: 380px;' dataType="Currency" msg="未填写或格式不对"></td>
		  </tr>
		<tr>
		  <td align="right" scope="col">费&nbsp;&nbsp;&nbsp;&nbsp;用</td>
		  <td scope="col"><input name="Amount" type="text" id="Amount" style='width: 380px;' dataType="Currency" msg="未填写或格式不对"></td>
		  </tr>
		<tr>
		  <td scope="col" align="right">付款方式</td>
		  <td scope="col"><select name="PayType" id="PayType" style='width: 380px;' dataType="Require"  msg="未选择">
		    <option value="" selected>请选择</option>
		    <option value="1">CASH 现付</option>
		    <option value="2">A/C 月结</option>
		    <option value="3">PP 预付</option>
		    <option value="4">CC 到付</option>
		    </select></td>
		  </tr>
		<tr>
		  <td align="right" scope="col">服务类型</td>
		  <td scope="col"><select name="ServiceType" id="ServiceType" style='width: 380px;' dataType="Require"  msg="未选择">
		    <option value="" selected>请选择</option>
		    <option value="1">PARCEL 包裹</option>
            <option value="2">DOCUMENT 文件</option>
            <option value="3">OTHERS 其它</option>
            </select></td>
		  </tr>
		<tr>
		  <td scope="col" align="right">物品名称</td>
		  <td scope="col"><input name="Description" type="text" id="Description" style='width: 380px;' dataType="Require" msg="未填写"></td>
		</tr>
		<tr>
		  <td align="right" scope="col">数&nbsp;&nbsp;&nbsp;&nbsp;量</td>
		  <td scope="col"><input name="Qty" type="text" id="Qty" style='width: 380px;' dataType="Number" msg="未填写或格式不对"></td>
		  </tr>
		<tr>
		  <td valign="top" scope="col" align="right">备&nbsp;&nbsp;&nbsp;&nbsp;注</td>
		  <td scope="col"><textarea name="Remark" cols="50" rows="2" id="Remark" dataType="Require"  msg="未填写"></textarea></td>
		</tr>
		<tr>
		  <td valign="top" scope="col" align="right">经 手 人</td>
		  <td scope="col">
		  <select name="HandledBy" id="HandledBy" style='width: 380px;' dataType="Require"  msg="未填写">
		  <?php 
			$result = mysql_query("SELECT U.Number,M.Name FROM $DataIn.usertable U LEFT JOIN $DataPublic.staffmain M ON M.Number=U.Number WHERE M.Number>10001 and M.Estate='1' ORDER BY M.BranchId,M.JobId,M.Number",$link_id);
			if($myrow = mysql_fetch_array($result)){
				echo "<option value=''>请选择</option>";
				do{
					$Number=$myrow["Number"];
					$Name=$myrow["Name"];
					if($Number==10039){
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
        </table>
		</td>
	</tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>