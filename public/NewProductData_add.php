<?php  
//电信-ZX  2012-08-01
/*
$DataIn.trade_object
$DataIn.producttype
$DataPublic.productunit
$DataPublic.packingunit
二合一已更新
*/
//步骤1
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增产品资料");//需处理
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
        <input id="PackingUnit" name="PackingUnit" type="hidden" value="1" />
        <table width="830" border="0" align="center" cellspacing="5" id="NoteTable">
		<!--
        <tr>
            <td align="right">隶属客户</td>
            <td><select name="CompanyId" id="CompanyId" size="1" style="width: 490px;" dataType="Require"  msg="未选择客户">
			<option value=''>请选择</option>
  			< 
			$result = mysql_query("SELECT CompanyId,Forshort FROM $DataIn.trade_object WHERE cSign=$Login_cSign AND Estate=1 order by Id",$link_id);
			if($myrow = mysql_fetch_array($result)){
				do{
					echo"<option value='$myrow[CompanyId]'>$myrow[Forshort]</option>";
					} while ($myrow = mysql_fetch_array($result));
				}
			  ?>			  
      		</select>
            </td>
          </tr>  -->
            <tr>
            <td align="right" scope="col">产品中文名</td>
            <td scope="col"><input name="cName" type="text" id="cName" size="91" dataType="LimitB" min="3" max="60"  msg="2-60个字节之内" title="必填项,2-60个字节内"></td>
            </tr>
            
            <tr>
            <td align="right">供应商</td>
            <td><select name="CompanyId" id="CompanyId" style="width: 490px;" dataType="Require"  msg="未选择供应商">
            <option value=''>-供应商列表-</option>
            <?php 
            //供应商
            $checkSql = "SELECT CompanyId,Forshort,Letter FROM $DataIn.trade_object WHERE (cSign>=0) AND Estate='1' order by Letter";
            $checkResult = mysql_query($checkSql); 
            while ( $checkRow = mysql_fetch_array($checkResult)){
                $CompanyId=$checkRow["CompanyId"];
                $Forshort=$checkRow["Letter"].'-'.$checkRow["Forshort"];
                echo "<option value='$CompanyId'>$Forshort</option>";
                } 
            ?>
            </select>
            </td>
            </tr>			
            <!--
            <tr>
            <td align="right" scope="col">英文代码<br>
              Product Code</td>
            <td scope="col"><input name="eCode" type="text" id="eCode" size="91"></td>
               
				</tr>
				<tr>
				  <td align="right" valign="top" scope="col">英文注释<br>
				  Description</td>
				  <td scope="col"><textarea name="Description" cols="58" rows="2" id="Description"></textarea>
			      </td>
		  </tr>
           -->
          <tr>
            <td width="123" align="right">成品类别</td>
            <td><select name="TypeId" id="TypeId" style="width: 490px;" dataType="Require"  msg="未选择分类">
			<option selected value="">请选择<option>
			<?php 
			$result = mysql_query("SELECT * FROM $DataIn.producttype WHERE Estate=1 order by Letter",$link_id);
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
            <td align="right">买价</td>
            <td><input name="Price" type="text" id="Price" size="91" dataType="Currency" msg="错误的价格"></td>
          </tr>
          <tr>
            <td align="right">单 位</td>
            <td><select name="Unit" id="Unit" style="width: 490px;" datatype="Require"  msg="未选择单位">
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
          <!--
          <tr>
            <td align="right">检验标准图</td>
            <td><input name="TestStandard" type="file" id="TestStandard" size="79" title="可选项,JPG格式" DataType="Filter" Accept="jpg" Msg="文件格式不对" Row="7" Cel="1"></td>
          </tr>
          -->
           <tr>
            <td align="right">高清标准图</td>
            <td><input name="Img_H" type="file" id="Img_H" size="79" title="可选项,JPG格式" DataType="Filter" Accept="jpg" Msg="文件格式不对" Row="7" Cel="1"></td>
          </tr>         
           <!--
          <tr>
            <td align="right">微缩标准图</td>
            <td><input name="Img_L" type="file" id="Img_L" size="79" title="可选项,JPG格式" DataType="Filter" Accept="jpg" Msg="文件格式不对" Row="7" Cel="1"></td>
          </tr>
                    
          <tr>
            <td align="right">包装说明<br>              </td>
            <td><input name="Remark" type="text" id="Remark" size="91">
            </td>
          </tr>
           -->
          <tr>
            <td align="right" valign="top">产品备注</td>
            <td><textarea name="pRemark" cols="58" rows="2" id="pRemark"></textarea></td>
          </tr>
          <!--
          <tr>
          
            <td align="right">标签装箱单位</td>
            <td>
              <select name="PackingUnit" id="PackingUnit" style="width: 490px;" dataType="Require"  msg="未选择装箱单位">
                <option value="" selected>请选择</option>
              <
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
            <td height="30" align="right">外箱标签条码</td>
            <td><input name="Code" type="text" id="Code" size="91" title="注:条码的英文注释与条码数字之间用&quot;|&quot;隔开,英文注释中需换行的地方输入&quot;&lt;br&gt;&quot;"></td>
          </tr>
          -->
		  <?php 
		  /*
          <tr>
            <td align="right" valign="top"><input name="CopyTo" type="checkbox" id="CopyTo" value="1" onclick="ChooseSet()"><label for="CopyTo">资料复制至</label></td>            
            <td><select name="CompanyIdCC[]" size="8" multiple id="CompanyIdCC" style="width: 490px;" disabled>
              <?php  
			$result = mysql_query("SELECT CompanyId,Forshort FROM $DataIn.trade_object WHERE cSign=$Login_cSign AND Estate=1 order by Id",$link_id);
			if($myrow = mysql_fetch_array($result)){
				do{
					echo"<option value='$myrow[CompanyId]'>$myrow[Forshort]</option>";
					} while ($myrow = mysql_fetch_array($result));
				}
			  ?>
                                    </select></td>
          </tr>
		  */?>
        </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>
<script>
function ChooseSet(){
	var The_Selectd = window.document.form1.CompanyIdCC;
	if(document.form1.CopyTo.checked==true){
		The_Selectd.disabled=false;
		}
	else{
		for (loop=The_Selectd.options.length-1;loop>=0;loop--){
			The_Selectd.options[loop].selected=false;
			}
		The_Selectd.disabled=true;
		}	
	}
</script>