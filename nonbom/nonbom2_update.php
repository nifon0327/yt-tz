<?php 
//非BOM配件子分类  EWEN 2013-02-17 OK
include "../model/modelhead.php";
include "../model/livesearch/modellivesearch.php";//用于在文本框输入文字显示与之相关的内容
//步骤2：
ChangeWtitle("$SubCompany 更新总务用品分类");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData = mysql_fetch_array(mysql_query("SELECT A.cSign,A.SendFloor,A.BuyerId,A.TypeName,A.mainType,A.Remark,A.NameRule,B.Name ,A.GetSign,A.FirstId  
FROM $DataPublic.nonbom2_subtype A
LEFT JOIN $DataPublic.staffmain B ON B.Number=A.CheckerId
WHERE A.Id='$Id'",$link_id));
$TypeName=$upData["TypeName"];
$mainType=$upData["mainType"];
$Remark=$upData["Remark"];
$Name=$upData["Name"];
$NameRule=$upData["NameRule"];
$BuyerId=$upData["BuyerId"];
$cSign=$upData["cSign"];
$FirstId=$upData["FirstId"];
$SendFloor=$upData["SendFloor"];
$GetSign=$upData["GetSign"];
$GetStr="GetSign".$GetSign;
$$GetStr="checked";

//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
		<table width="760" border="0" align="center" cellspacing="5">
        <tr>
              <td height="23" align="right">送货楼层</td>
              <td><select name="SendFloor"  id="SendFloor" style="width: 380px;" msg="没有填写或超出字节的范围" >
                <option value="" selected="selected">请选择</option>
                <?php 
				$checkFloorResult = mysql_query("SELECT Id,Name,Remark FROM $DataIn.nonbom0_ck  WHERE Estate=1 AND TypeId IN (0,1) order by  Remark",$link_id);
				if($checkFloorRow = mysql_fetch_array($checkFloorResult)) {
					do{			
						$IdTemp=$checkFloorRow["Id"];
						$IdRemark=$checkFloorRow["Remark"];
						$IdName=$checkFloorRow["Name"];
						if($IdTemp==$SendFloor){
							echo"<option value='$IdTemp' selected>$IdName</option>";
							}
						else{
							echo"<option value='$IdTemp'>$IdName</option>";
							}
						}while($checkFloorRow = mysql_fetch_array($checkFloorResult));
					}
				?>
              </select></td>
          </tr>
         <!--
        <tr>
    			<td width="150" height="25" align="right" >公司标识</td>
    			<td >
				<?php 
     	 		//选择公司名称
        		$SharingShow="Y";
        		include "../model/subselect/cSign.php";
     			?>
				</td>
	    </tr>
	    -->
	     <tr>
            	<td width="150" height="26" align="right" scope="col">所属会计科目</td>
                <td>
                <?php 
                include "../model/subselect/acfirst_sType.php";
				?>
                </td>
			</tr>

      		<tr>
            	<td width="150" height="20" align="right" scope="col">所属主类</td>
                <td>
                <?php 
                include "../model/subselect/nonbom_mType.php";
				?>
                </td>
			</tr>
        	<tr>
          		<td width="150" height="22" align="right" scope="col">分类名称</td>
       		  <td scope="col"><input name="TypeName" type="text" id="TypeName" title="可输入2-30个字节(每1中文字占2个字节，每1英文字母占1个字节)" value="<?php  echo $TypeName?>" style="width: 380px;" maxlength="30" DataType="LimitB"  Max="30" Min="2" Msg="没有填写或字符不在2-30个字节内"></td>
        	</tr>
		<tr>
		  <td height="30" align="right" scope="col">款项是否收回</td>
		  <td scope="col"><input name="GetSign[]" type="checkbox" value="1"  onclick="checkSign(1)" <?php echo $GetSign1 ?>><span class="redB">是</span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input name="GetSign[]"  type="checkbox" value="0"  onclick="checkSign(0)"  <?php echo $GetSign0 ?>><span class="blueB">否</span>
	    </tr>

            <tr>
          		<td height="40" align="right" valign="top" scope="col">分类说明</td>
          		<td scope="col"><textarea name="Remark" rows="3" id="Remark" style="width: 380px;" dataType="Require" msg="未填写或格式不对"><?php  echo $Remark?></textarea></td>
        	</tr>
            <tr>
              <td height="40" align="right" valign="top">命名规则</td>
              <td><textarea name="NameRule" rows="3" id="NameRule" style="width: 380px;" dataType="Require" msg="未填写或格式不对"><?php echo $NameRule;?></textarea></td>
            </tr>

        <tr>
              <td height="23" align="right">采购负责人</td>
              <td><select name="BuyerId"  id="BuyerId" style="width: 380px;" dataType="Require" msg="没有选择" >
              <option value="" selected>请选择</option>
             <?php 
			 //采购选择
			$checkResult = mysql_query("SELECT A.BuyerId,B.Name FROM $DataIn.nonbom3_buyer A LEFT JOIN $DataIn.staffmain B ON B.Number=A.BuyerId ORDER BY B.Name",$link_id);
			if($checkRow = mysql_fetch_array($checkResult)) {
				do{			
					$BuyerIdTemp=$checkRow["BuyerId"];
					$BuyerIdName=$checkRow["Name"];
					if($BuyerId==$BuyerIdTemp){
						echo"<option value='$BuyerIdTemp' selected>$BuyerIdName</option>";
						}
					else{
						echo"<option value='$BuyerIdTemp'>$BuyerIdName</option>";
						}
					}while($checkRow = mysql_fetch_array($checkResult));
				}?>
               </select>
			 	</td>
            </tr>
            <tr>
              <td height="26" align="right">申购终审人</td>
              <td><input name="Name" type="text" id="Name" title="必填项,2-30个字节的范围"  style="width: 380px;" value="<?php echo $Name;?>" maxlength="30" datatype="LimitB" max="30" min="2" msg="没有填写或超出字节的范围" onkeyup="showResult(this.value,'Name','staffmain','1','Estate=1')" onblur="LoseFocus()" onchange="clearData()" autocomplete="off">
			 	</td>
            </tr>
            
      	</table>
        </td></tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>
<script>
function  checkSign(tempvalue){
     var getSign=document.getElementsByName("GetSign[]");
      if(tempvalue==0) getSign[0].checked="";
      if(tempvalue==1) getSign[1].checked="";

}
</script>