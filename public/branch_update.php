<?php 
//电信-joseph
//代码共享、数据库共享-EWEN 2012-08-14
include "../model/modelhead.php";
echo "<link rel='stylesheet' href='../model/inputSuggest.css'>
      <script type='text/javascript' src='../model/inputSuggest1.0c.js'></script>";
//步骤2：
ChangeWtitle("$SubCompany 更新部门资料");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData = mysql_fetch_array(mysql_query("SELECT * FROM $DataPublic.branchdata WHERE Id='$Id'",$link_id));
$Name=$upData["Name"];
$TypeId=$upData["TypeId"];
$SortId=$upData["SortId"];
$cSign=$upData["cSign"];
$SortId=$SortId==99?0:$SortId;
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
		<table width="760" border="0" align="center" cellspacing="5">
    		<!--<tr>
    			<td width="150" height="40" align="right" >公司标识</td>
    			<td >
				<?php 
     	 		//选择公司名称
        		$SharingShow="Y";
        		include "../model/subselect/cSign.php";
     			?>
				</td>
  			</tr>-->
         	<tr>
            	<td align="right">部门类别</td>
            	<td>
				<?php 
				include "../model/subselect/BranchType.php";
				?>
            	</td>
          	</tr>
			<tr>
            	<td width="150" height="40" align="right" scope="col">部门名称</td>
            	<td scope="col"><input name="Name" type="text" id="Name" style="width:380px" value="<?php  echo $Name?>" maxlength="16" title="可输入2-16个字节(每1中文字占2个字节，每1英文字母占1个字节)" DataType="LimitB"  Max="16" Min="2" Msg="没有填写或字符不在2-16个字节内"></td>
			</tr>
            <tr>
			  <td height="40" align="right" scope="col">排序号码</td>
			  <td scope="col"><input name="SortId" type="text" id="SortId" style="width:380px" value="<?php  echo $SortId?>" maxlength="16" datatype="Number" msg="没有填写或格式不对" /></td>
		  </tr>
  <tr>
			  <td height="40" align="right" scope="col">部门负责人</td>
			  <td scope="col">
              <select name="Manager" id="Manager" style="width:380px" />
              <?php 
              //读取该部门固定薪员工
			$checkSql=mysql_query("SELECT A.Number,A.Name,B.Name AS Branch 
				FROM $DataPublic.staffmain A
				LEFT JOIN $DataPublic.branchdata B ON B.Id=A.BranchId
				WHERE A.cSign='$Login_cSign' AND A.Estate='1' AND A.KqSign!='1' ORDER BY A.BranchId,A.Name",$link_id);
			if($checkRow=mysql_fetch_array( $checkSql)){
						echo "<option value='' selected>请选择</option>";
				$i=1;
				do{
					$Number=$checkRow["Number"];
					$Name=$i." ".$checkRow["Branch"]."-".$checkRow["Name"];

					echo "<option value='$Number'>$Name</option>";
					$i++;
					}while($checkRow=mysql_fetch_array( $checkSql));
				}
			  ?>
              </select>
              </td>
		  </tr>

      </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>