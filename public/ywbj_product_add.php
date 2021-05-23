<?php 
//电信-zxq 2012-08-01
//步骤1
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增报价产品资料");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
        <table width="830" border="0" align="center" cellspacing="5" id="NoteTable">
          <tr>
            <td align="right">客&nbsp;&nbsp;&nbsp;&nbsp;户</td>
            <td><select name="CompanyId" id="CompanyId" size="1" style="width: 490px;" dataType="Require"  msg="未选择客户">
                <option value="" selected>请选择</option>
                <?php  
			$result = mysql_query("SELECT CompanyId,Forshort FROM $DataIn.trade_object WHERE cSign=$Login_cSign AND Estate=1 order by Id",$link_id);
			if($myrow = mysql_fetch_array($result)){
				do{
					echo"<option value='$myrow[CompanyId]'>$myrow[Forshort]</option>";
					} while ($myrow = mysql_fetch_array($result));
				}
			  ?>
              </select>
            </td>
          </tr>
          <tr>
            <td align="right">产&nbsp;&nbsp;&nbsp;&nbsp;品</td>
            <td><select name="TypeId" id="TypeId" style="width: 490px;" dataType="Require"  msg="未选择分类">
                <?php 
			$result = mysql_query("SELECT * FROM $DataIn.producttype WHERE Estate=1 ORDER BY Letter",$link_id);
			if($myrow = mysql_fetch_array($result)){
                  echo "<option value='' selected>请选择</option>";
				do{
					   $Letter=$myrow["Letter"];
					   $TypeId=$myrow["TypeId"];
					   $TypeName=$myrow["TypeName"];
					   echo "<option value='$TypeId'>$Letter-$TypeName</option>";
					} while ($myrow = mysql_fetch_array($result));
				}
			?>
                 </select>
            </td>
          </tr>
          <tr>
            <td align="right" valign="top">备&nbsp;&nbsp;&nbsp;&nbsp;注</td>
            <td><textarea name="Remark" cols="58" rows="5" id="Remark"></textarea></td>
          </tr>
        </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>