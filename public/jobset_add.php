<?php 
//电信-EWEN
//代码共享-EWEN 2012-08-14
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增职位");//需处理
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
		<table width="760" border="0" align="center" cellspacing="5">
			<!--<tr>
				<td width="150" height="29" align="right" >所属公司: </td>
				<td >
				<?php 
      			//选择公司名称
        		$SharingShow="Y";
        		include "../model/subselect/cSign.php";
     			?>
				</td>
  			</tr>-->
			<tr>
            	<td scope="col" width="150" height="30" align="right">职位名称:</td>
            	<td scope="col">
              	<input name="Name" type="text" id="Name" style="width:380px" maxlength="16" title="可输入2-16个字节(每1中文字占2个字节，每1英文字母占1个字节)" DataType="LimitB"  Max="16" Min="2" Msg="没有填写或字符不在2-16个字节内"> 
				</td>
			</tr>
			<tr>
		  		<td height="60" align="right" valign="top" scope="col">职责内容:</td>
		  		<td scope="col"><textarea name="WorkNote" style="width:380px" rows="3" id="WorkNote"></textarea></td>
	    	</tr>
			<tr>
		  		<td height="60" align="right" valign="top" scope="col">工作时间:</td>
		  		<td scope="col"><textarea name="WorkTime" style="width:380px" rows="3" id="WorkTime"></textarea></td>
	    	</tr>
  <tr>
			  <td height="60" align="right" valign="top" scope="col">职位负责人</td>
			  <td scope="col">
              <select name="LeaderNumber" id="LeaderNumber" style="width:380px" />
              <?php 
              //读取该部门固定薪员工
			$checkSql=mysql_query("SELECT A.Number,A.Name,B.Name AS Job 
				FROM $DataPublic.staffmain A
				LEFT JOIN $DataPublic.jobdata B ON B.Id=A.JobId
				WHERE A.cSign='$Login_cSign' AND A.Estate='1' AND A.KqSign='3' ORDER BY A.JobId,A.Name",$link_id);
			if($checkRow=mysql_fetch_array( $checkSql)){
						echo "<option value='' selected>请选择</option>";
				$i=1;
				do{
					$Number=$checkRow["Number"];
					$Name=$i." ".$checkRow["Job"]."-".$checkRow["Name"];
						echo "<option value='$Number' >$Name</option>";

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