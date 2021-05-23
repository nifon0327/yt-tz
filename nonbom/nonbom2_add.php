<?php 
//非BOM配件子分类  EWEN 2013-02-17 OK
include "../model/modelhead.php";
include "../model/livesearch/modellivesearch.php";//用于在文本框输入文字显示与之相关的内容
//步骤2：
ChangeWtitle("$SubCompany 新增非BOM配件子分类");//需处理
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
		<table width="760" border="0" align="center" cellspacing="5" id="NoteTable">
        <tr>
        <!--
    			<td width="150" height="30" align="right" >公司标识</td>
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
            	<td width="150" height="26" align="right" scope="col">所属主类</td>
                <td>
                <?php 
                include "../model/subselect/nonbom_mType.php";
				?>
                </td>
			</tr>
            <tr>
            	<td width="150" height="36" align="right">分类名称</td>
                <td><input name="TypeName" type="text" id="TypeName" style="width: 380px;" maxlength="30" title="可输入2-30个字节(每1中文字占2个字节，每1英文字母占1个字节)" DataType="LimitB"  Max="30" Min="2" Msg="没有填写或字符不在2-30个字节内"></td>
            </tr>

		<tr>
		  <td height="30" align="right" scope="col">款项是否收回</td>
		  <td scope="col"><input name="GetSign[]" type="checkbox" value="1"  onclick="checkSign(1)" ><span class="redB">是</span>
   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="GetSign[]"  type="checkbox" value="0"  onclick="checkSign(0)" checked ><span class="blueB">否</span>
	    </tr>


            <tr>
              <td height="40" align="right" valign="top">分类说明</td>
              <td><textarea name="Remark" rows="3" id="Remark" style="width: 380px;" msg="未填写或格式不对"></textarea></td>
            </tr>
            <tr>
              <td height="40" align="right" valign="top">命名规则</td>
              <td><textarea name="NameRule" rows="3" id="NameRule" style="width: 380px;" msg="未填写或格式不对"></textarea></td>
            </tr>
            <tr>
              <td height="29" align="right">送货楼层</td>
              <td><select name="SendFloor"  id="SendFloor" style="width: 380px;" msg="没有填写或超出字节的范围" >
                <option value="" selected="selected">请选择</option>
                <?php 
				$checkResult = mysql_query("SELECT Id,Name,Remark FROM $DataPublic.nonbom0_ck  WHERE Estate=1 AND TypeId IN (0,1) order by  Remark",$link_id);
				if($checkRow = mysql_fetch_array($checkResult)) {
					do{			
						$IdTemp=$checkRow["Id"];
						$IdRemark=$checkRow["Remark"];
						$IdName=$checkRow["Name"];
						echo"<option value='$IdTemp'>$IdName</option>";
						}while($checkRow = mysql_fetch_array($checkResult));
					}
				?>
              </select></td>
            </tr>
            <tr>
              <td height="29" align="right">采购负责人</td>
              <td><select name="BuyerId"  id="BuyerId" style="width: 380px;" msg="没有填写或超出字节的范围" >
              <option value="" selected>请选择</option>
             <?php 
			 //采购选择
			$checkResult = mysql_query("SELECT A.BuyerId,B.Name FROM $DataPublic.nonbom3_buyer A LEFT JOIN $DataPublic.staffmain B ON B.Number=A.BuyerId ORDER BY B.Name",$link_id);
			if($checkRow = mysql_fetch_array($checkResult)) {
				do{			
					$BuyerIdTemp=$checkRow["BuyerId"];
					$BuyerIdName=$checkRow["Name"];
					echo"<option value='$BuyerIdTemp'>$BuyerIdName</option>";
					}while($checkRow = mysql_fetch_array($checkResult));
				}?>
               </select>
			 	</td>
            </tr>
            <tr>
              <td height="34" align="right">申购终审人</td>
              <td><input name="Name" type="text" id="Name" title="必填项,2-30个字节的范围"  style="width: 380px;" maxlength="30" datatype="LimitB" max="30" min="2" msg="没有填写或超出字节的范围" onkeyup="showResult(this.value,'Name','staffmain','1','Estate=1')" onblur="LoseFocus()" onchange="clearData()" autocomplete="off">
			 	</td>
            </tr>
          </table>
  </td></tr></table>
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