<?php 
/*
已更新电信---yang 20120801
*/
//步骤1
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 产品BOM表加入");//需处理
$nowWebPage =$funFrom."_bom";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"] = $nowWebPage;
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,ActionId,95,Id,$Id";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$companyRow=mysql_fetch_array(mysql_query("SELECT C.Forshort ,D.ItemName FROM  $DataIn.development D
                        LEFT JOIN $DataIn.trade_object C ON C.CompanyId=D.CompanyId
						WHERE D.Id='$Id'",$link_id));
$Forshort=$companyRow["Forshort"];
$ItemName=$companyRow["ItemName"];			
						
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
        <table width="830" border="0" align="center" cellspacing="5" id="NoteTable">
		<tr>
            <td align="right">客&nbsp;&nbsp;&nbsp;&nbsp;户：</td>
            <td scope="col"><?php  echo $Forshort?></td>
          </tr>		
		  <tr>
            <td align="right" scope="col">产品中文名</td>
            <td scope="col"><input name="cName" type="text" id="cName" value="<?php  echo $ItemName?>" style="width: 380px;" dataType="LimitB" min="3" max="60"  msg="2-60个字节之内" title="必填项,2-60个字节内"></td></tr>
				<tr>
            <td align="right" scope="col">英文代码<br>
              Product Code</td>
            <td scope="col"><input name="eCode" type="text" id="eCode" style="width: 380px;"></td>
               
				</tr>
				<tr>
				  <td align="right" valign="top" scope="col">英文注释<br>
				  Description</td>
				  <td scope="col"><textarea name="Description" style="width: 380px;" rows="2" id="Description"></textarea>
			      </td>
		  </tr>
          
          <tr>
            <td width="123" align="right">成品类别</td>
            <td><select name="TypeId" id="TypeId" style="width: 380px;" dataType="Require"  msg="未选择分类">
			<option selected value="">请选择</option>
			<?php 
			$result = mysql_query("SELECT * FROM $DataIn.producttype order by Letter",$link_id);
			while ($myrow = mysql_fetch_array($result)){
				$Letter=$myrow["Letter"];
				$TypeId=$myrow["TypeId"];
				$TypeName=$myrow["TypeName"];
				echo "<option value='$TypeId'>$Letter-$TypeName</option>";
				} 
			?>
           </select>
		   </td>
          </tr>
          <tr>
            <td align="right">参考售价</td>
            <td><input name="Price" type="text" id="Price" style="width: 380px;" dataType="Currency"  msg="错误的价格"></td>
          </tr>
          <tr>
            <td align="right">单品重量(g)</td>
            <td><input name="Weight" type="text" id="Weight" style="width: 380px;" value="0" dataType="Currency" msg="错误的重量"></td>
          </tr>
          <tr>
            <td align="right">单 位</td>
            <td><select name="Unit" id="Unit" style="width: 380px;" datatype="Require"  msg="未选择单位">
              <option value="" selected>请选择</option>
        		<?php 
			   $ptResult = mysql_query("SELECT * FROM $DataPublic.productunit WHERE Estate=1 order by Id",$link_id);
				while ($ptRow = mysql_fetch_array($ptResult)){
					$ptId=$ptRow["Id"];
					$ptName=$ptRow["Name"];
					echo"<option value='$ptId'>$ptName</option>";
					} 
				?>
            </select></td>
          </tr>
          <tr>
            <td align="right">包装说明<br>              </td>
            <td><input name="Remark" type="text" id="Remark" style="width: 380px;">
            </td>
          </tr>
          <tr>
            <td align="right" valign="top">产品备注</td>
            <td><textarea name="pRemark" style="width: 380px;" rows="2" id="pRemark"></textarea></td>
          </tr>
          <tr>
            <td align="right">标签装箱单位</td>
            <td>
              <select name="PackingUnit" id="PackingUnit" style="width: 380px;" dataType="Require"  msg="未选择装箱单位">
                <option value="" selected>请选择</option>
              <?php 
			   $puResult = mysql_query("SELECT * FROM $DataPublic.packingunit WHERE Estate=1 order by Id",$link_id);
				while ($puRow = mysql_fetch_array($puResult)){
					$puId=$puRow["Id"];
					$puName=$puRow["Name"];
					echo"<option value='$puId'>$puName</option>";
					} 
				?>
              </select>
            </td>
          </tr>
          <tr>
            <td align="right">外箱标签条码</td>
            <td><input name="Code" type="text" id="Code" style="width: 380px;" title="注:条码的英文注释与条码数字之间用&quot;|&quot;隔开,英文注释中需换行的地方输入&quot;&lt;br&gt;&quot;"></td>
          </tr>
          <tr>
            <td align="right" valign="top">报价规则</td>
            <td><textarea name="bjRemark" style="width: 380px;" id="bjRemark"></textarea></td>
          </tr>
        </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>