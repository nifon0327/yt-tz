<?php
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 更新劳务员工资料");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION['nowWebPage']; 
//步骤3：//需处理
$mainRow=mysql_fetch_array(mysql_query("SELECT M.CompanyId,M.Id,M.WorkAdd,M.Number,M.IdNum,M.Name,M.Nickname,M.KqSign,M.BranchId,M.GroupId,
M.JobId,M.ComeIn,M.AttendanceFloor,M.Estate,M.Locks,M.Date,M.Operator,S.Sex,S.Nation,S.Rpr,S.Education,S.Married,
S.Birthday,S.Photo,S.IdcardPhoto,S.Idcard,S.eMail,S.Address,S.Tel,S.Mobile,S.Bank,S.HealthPhoto,M.Remark
FROM $DataIn.lw_staffmain M
LEFT JOIN $DataIn.lw_staffsheet S ON S.Number=M.Number
WHERE M.Id='$Id' LIMIT 1",$link_id));
$CompanyId=$mainRow["CompanyId"];
$WorkAdd=$mainRow["WorkAdd"];
$floorAdd = $mainRow["AttendanceFloor"];
$Number=$mainRow["Number"];
$IdNum=$mainRow["IdNum"];
$StaffName=$mainRow["StaffName"];
$Name=$mainRow["Name"];
$Nickname=$mainRow["Nickname"];
$Sex=$mainRow["Sex"];
$Nation=$mainRow["Nation"];
$Education=$mainRow["Education"];
$Married=$mainRow["Married"];
$Rpr=$mainRow["Rpr"];
$Birthday=$mainRow["Birthday"];
$Tel=$mainRow["Tel"];
$Address=$mainRow["Address"];
$Idcard=$mainRow["Idcard"];
$IdcardPhoto=$mainRow["IdcardPhoto"];
$HealthPhoto=$mainRow["HealthPhoto"];
$Photo=$mainRow["Photo"];
$Mobile=$mainRow["Mobile"];
$ComeIn=$mainRow["ComeIn"];
$eMail=$mainRow["eMail"];
$Weixin=$mainRow["Weixin"];
$Bank=$mainRow["Bank"];
$GroupId=$mainRow["GroupId"];
$BranchId=$mainRow["BranchId"];
$JobId=$mainRow["JobId"];
$KqSign=$mainRow["KqSign"];
$KqStr = "KqSign".$KqSign;
$$KqStr = "selected";
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../admin/subprogram/add_model_t.php";
 
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,Number,$Number";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
	<table width="750" border="0" align="center" cellspacing="5" id="NoteTable">
		<tr>
            <td height="16" scope="col" colspan="2">基本信息</td>
          </tr>

          <tr>
            <td align="right" scope="col">姓&nbsp;&nbsp;&nbsp;&nbsp;名</td>
            <td scope="col"><input name="Name" type="text" id="Name" value="<?php  echo $Name?>" style="width:380px;" maxlength="8" dataType="Chinese" msg="只允许中文">
            </td>
          </tr>      
         <tr>
            <td align="right" scope="col">性 &nbsp;&nbsp;&nbsp;别</td>
            <td scope="col">
			<select name="Sex" id="Sex" style="width:380px;">
			<?php 
			if($Sex==1){
				echo"<option value='1' selected>男</option>";
				echo"<option value='0'>女</option>";
				}
			else{
				echo"<option value='1'>男</option>";
				echo"<option value='0' selected>女</option>";
				}
			?>
            </select></td>
          </tr>
          <tr>
            <td align="right" scope="col">民&nbsp;&nbsp;&nbsp;&nbsp;族</td>
            <td scope="col">
			<select id=Nation  name=Nation style="width:380px;">
			 <?php 
			 $nResult = mysql_query("SELECT Id,Name FROM $DataPublic.nationdata WHERE Estate=1 order by Id",$link_id);
			 if($nRow = mysql_fetch_array($nResult)){
				do{
					if($nRow["Id"]==$Nation){
						echo"<option value='$nRow[Id]' selected>$nRow[Name]</option>";
						}
					else{
						echo"<option value='$nRow[Id]'>$nRow[Name]</option>";
						}
					}while ($nRow = mysql_fetch_array($nResult));
				}
			  ?>
            </select>
			</td>
          </tr>
          <tr>
            <td align="right" scope="col">户口原地</td>
            <td scope="col">
			<select name="Rpr"  class=select id=Rpr style="width:380px;">
			 <?php  
			 $rResult = mysql_query("SELECT Id,Name FROM $DataPublic.rprdata WHERE Estate=1 order by Id",$link_id);
			 if($rRow = mysql_fetch_array($rResult)){
				do{
					if($Rpr==$rRow["Id"]){
						echo"<option value='$rRow[Id]' selected>$rRow[Name]</option>";
						}
					else{
						echo"<option value='$rRow[Id]'>$rRow[Name]</option>";
						}
					}while ($rRow = mysql_fetch_array($rResult));
				}
			  ?>
		    </select>
			</td>
          </tr>

          <tr>
            <td align="right">婚姻状况</td>
            <td><select name="Married"  id="Married" style="width:380px;">
			<?php 
			if($Married==0){				
				echo"<option value='1'>未婚</option>";
				echo"<option value='0' selected>已婚</option>";
				}
			else{
				echo"<option value='1' selected>未婚</option>";
				echo"<option value='0'>已婚</option>";
				}
			?>
            </select></td>
          </tr>
          <tr>
            <td width="112" align="right">出生日期</td>
            <td><input name="Birthday" type="text" id="Birthday" value="<?php  echo $Birthday?>" style="width:380px;" maxlength="10" onfocus="WdatePicker()" dataType="Date" format="ymd" msg="生日日期不正确" readonly></td>
          </tr>

 
          <tr>
            <td align="right">家庭地址</td>
            <td><textarea name="Address" id="Address" rows="3" value="<?php  echo $Address?>" style="width:380px;"></textarea></td>
          </tr>
          <tr>
            <td align="right">身份证号</td>
            <td><input name="Idcard" type="text" id="Idcard" value="<?php  echo $Idcard?>" style="width:380px;" maxlength="18">
            </td>
          </tr>
          <tr>
            <td align="right">身 份 证</td>
            <td>
			<input name="IdcardPhoto" type="file" id="IdcardPhoto" style="width:380px;" dataType="Filter" msg="非法的文件格式" accept="jpg" Row="13" Cel="1">
			</td>
          </tr>
		 <?php 
		if($IdcardPhoto==1){
			echo"<tr><td>&nbsp;</td><td>
			<input type='checkbox' name='oldIphoto' id='oldIphoto' value='1'><LABEL for='oldIphoto'>删除已传身份证扫描档</LABEL></td></tr>";
			$RowNext=15;
			}
		else{
			$RowNext=14;
			}
		?>
          <tr>
            <td align="right">照 &nbsp;&nbsp;&nbsp;片</td>
            <td><input name="Photo" type="file" id="Photo" style="width:380px;" dataType="Filter" msg="非法的文件格式" accept="jpg" Row="<?php  echo $RowNext?>" Cel="1"></td></tr>
			<?php 
			if($Photo==1){
				echo"<tr><td>&nbsp;</td><td>
				<input type='checkbox' name='oldPhoto' id='oldPhoto' value='1'><LABEL for='oldPhoto'>删除已传照片</LABEL></a></td></tr>";
				$RowNext=$RowNext+2;
				}
			else{
				$RowNext++;
				}
			?>
			 <tr>
			 <tr>
            <td align="right">照片PNG</td>
            <td><input name="PngPhoto" type="file" id="PngPhoto" style="width:380px;" dataType="Filter" msg="非法的文件格式" accept="png" Row="13" Cel="1"></td></tr>
			 <tr>
            <td align="right">健康体检</td>
            <td>
			<input name="HealthPhoto" type="file" id="HealthPhoto" style="width:380px;" dataType="Filter" msg="非法的文件格式" accept="jpg" Row="13" Cel="1">
			</td>
          </tr>
		  <?php 
			if($HealthPhoto==1){
				echo"<tr><td>&nbsp;</td><td>
				<input type='checkbox' name='oldHPhoto' id='oldHPhoto' value='1'><LABEL for='oldHPhoto'>删除已传照健康体检证</LABEL></a></td></tr>";
				$RowNext=$RowNext+2;
				}
			else{
				$RowNext++;
				}
			?>
          <tr>
            <td height="22" colspan="2"><div align="left">公司信息</div></td>
          </tr>
           <tr>
			  <td align="right" scope="col">劳务公司</td>
	          <td scope="col"><select name="CompanyId" id="CompanyId" style="width:380px" dataType="Require"  msg="未选择籍贯">
	            <option value="" selected>--请选择--</option>
				  <?php 
				 $Result4 = mysql_query("SELECT CompanyId,Forshort FROM $DataPublic.lw_company WHERE Estate=1 order by Id",$link_id);
				 if($myRow4 = mysql_fetch_array($Result4)){
					do{
					    if($CompanyId==$myRow4["CompanyId"]){
						    echo" <option value='$myRow4[CompanyId]' selected>$myRow4[Forshort]</option>";
					    }else{
						    echo" <option value='$myRow4[CompanyId]'>$myRow4[Forshort]</option>";
					    }
						
						}while($myRow4 = mysql_fetch_array($Result4));
					}
				 ?>
              </select>
	          </td>
          </tr>
		   <tr>
		     <td align="right">工作地点</td>
		     <td><?php 
             include "../model/subselect/WorkAdd.php";
			 ?></td>
        </tr>
        
        <tr>
        	<td align="right">考勤楼层</td>
        	<td>
	        	<?php
		        	include "../model/subselect/FloorAdd.php";
	        	?>
        	</td>
        </tr>
         <tr>
            <td align="right">部&nbsp;&nbsp;&nbsp;&nbsp;门</td>
            <td>
			<?php 
             include"../model/subselect/BranchId.php";
            ?>
            </select>              
            </td>
          </tr>
          <tr>
            <td align="right">小&nbsp;&nbsp;&nbsp;&nbsp;组</td>
            <td>
              <?php 
			     include"../model/subselect/GroupIdType.php";
			?>
            </select></td>
          </tr>
           <tr>
            <td align="right">职&nbsp;&nbsp;&nbsp;&nbsp;位</td>
            <td>
			<?php 
             include"../model/subselect/JobIdType.php";
            ?>          
            </td>
          </tr>
          
           <tr>
            <td align="right">考勤状态</td>
            <td><select name="KqSign"  id="KqSign" style="width:380px" dataType="Require"  msg="未选择婚姻状况">
              <option value="">--请选择--</option>
              <option value="1" <?php echo $KqSign1?>>考勤</option>
              <option value="2" <?php echo $KqSign2?>>考勤参考</option>
              <option value="3" <?php echo $KqSign3?>>无须考勤</option>
            </select>
            </td>
          </tr>     
          <tr>
            <td align="right">ID 卡 号</td>
            <td><input name="IdNum" type="text" id="IdNum" value="<?php  echo $IdNum?>" style="width:380px;" maxlength="10"></td>
          </tr>
          <tr>
            <td align="right">移动电话</td>
            <td><input name="Mobile" type="text" id="Mobile" value="<?php  echo $Mobile?>" style="width:380px;" maxlength="16"></td>
          </tr>
  
          <tr>
          <td align="right">微信</td>
            <td><input name="Weixin" type="text" id="Weixin" style="width:380px"   value="<?php  echo $Weixin?>" ></td>
          </tr>
              
          <tr>
            <td align="right">入职时间</td>
            <td><input name="ComeIn" type="text" id="ComeIn" value="<?php  echo $ComeIn?>" style="width:380px;" maxlength="10"  dataType="Date" format="ymd" msg="入职日期不正确" ></td>
          </tr>
          <tr>
            <td align="right">电子邮件</td>
            <td><input name="eMail" type="text" id="eMail" value="<?php  echo $eMail?>" style="width:380px;" maxlength="50" require="false" dataType="Email" msg="信箱格式不正确"></td>
          </tr>
        <tr>
            <td align="right" valign="top">备&nbsp;&nbsp;&nbsp;&nbsp;注</td>
            <td><textarea name="Remark" style="width:380px" rows="4" id="Remark"><?php  echo $Remark?></textarea></td>
          </tr>
          
   </table>
</td></tr></table>
<?php 
//步骤5：
include "../admin/subprogram/add_model_b.php";
?>

