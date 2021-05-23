<?php 
//ewen 2013-03-20 OK
include "../model/modelhead.php";
echo "<script src='../model/palette.js' type=text/javascript></script>";
ChangeWtitle("$SubCompany 生产车间设置");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$upData = mysql_fetch_array(mysql_query("SELECT * FROM $DataPublic.workshopdata WHERE Id='$Id'",$link_id));
$Name=$upData["Name"];
$WorkAdd=$upData["WorkAddId"];
$Floor=$upData["Floor"];
$Remark=$upData["Remark"];

$tableWidth=850;
$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
?>

<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
      <table width="760" border="0" align="left" cellspacing="5">
		<tr>
            <td width="120" height="40" align="right" scope="col">车间名称</td>
            <td scope="col" ><input name="Name" type="text" id="Name" style="width:380px;" value="<?php echo $Name;?>"  maxlength="20" title="可输入2-20个字节(每1中文字占2个字节，每1英文字母占1个字节)" DataType="LimitB"  Max="20" Min="2" Msg="没有填写或字符不在2-20个字节内"></td>
		</tr>
        <tr>
		     <td  width="120" height="40" align="right" scope="col">地点</td>
		     <td><?php 
                include "../model/subselect/WorkAdd.php";
			 ?></td>
        </tr>
		<tr>
            <td width="120" height="40" align="right" scope="col">楼层</td>
            <td scope="col" ><input name="Floor" type="text" id="Floor" style="width:380px;" value="<?php echo $Floor;?>"   DataType="LimitB"  Max="20" Min="2" Msg="没有填写或字符不在2-20个字节内"></td>
		</tr>
		 <tr>
			  <td height="40" align="right" scope="col">负责人</td>
			  <td scope="col">
              <select name="LeaderNumber" id="LeaderNumber" style="width:380px" />
              <?php 
              //读取该部门固定薪员工
			$checkSql=mysql_query("SELECT A.Number,A.Name,G.GroupName 
				FROM $DataPublic.staffmain A
				LEFT JOIN $DataPublic.branchdata B ON B.Id=A.BranchId
				LEFT JOIN $DataPublic.staffgroup G ON G.GroupId=A.GroupId 
				WHERE A.Estate='1' AND B.TypeId=2 ORDER BY A.GroupId,A.Name",$link_id);//AND A.KqSign!='1' 
			if($checkRow=mysql_fetch_array( $checkSql)){
				echo "<option value='' selected>请选择</option>";
				$i=1;
				do{
					$Number=$checkRow["Number"];
					$Name=$checkRow["GroupName"]."-".$checkRow["Name"];
                    if ($LeaderNumber==$Number){
	                    echo "<option value='$Number' selected>$Name</option>";
                    }
                    else{
	                    echo "<option value='$Number'>$Name</option>";
                    }
				  }while($checkRow=mysql_fetch_array( $checkSql));
				}
			  ?>
              </select>
              </td>
		  </tr>

        <tr>
            <td width="120" height="40" align="right" valign="top" scope="col">备注</td>
            <td scope="col" ><textarea name="Remark" rows="4" id="Remark" style="width:380px;" tdatatype="Require" msg="没有填写"><?php echo $Remark;?></textarea></td>
		</tr>
      </table>
</td></tr></table>


<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>