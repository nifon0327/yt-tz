<?php 
//电信-EWEN
include "../model/modelhead.php";
//步骤2：//需处理
ChangeWtitle("$SubCompany 更新行政费用");
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upResult = mysql_query("SELECT * FROM $DataIn.hzqksheet Where Id=$Id",$link_id);
if($upRow = mysql_fetch_array($upResult)){
	$Id=$upRow["Id"];
	$Content=$upRow["Content"];
	$cSign = $upRow["cSign"];
	$Amount=$upRow["Amount"];
	$Currency=$upRow["Currency"];
	$Date=$upRow["Date"];
	$TypeId=$upRow["TypeId"];
	$Bill=$upRow["Bill"];
	$Estate=$upRow["Estate"];
	$OtherId=$upRow["OtherId"];
    $Property=$upRow["Property"];
     switch($Property){
         case "1": $PropertyStr1="checked";break;
         case "2": $PropertyStr2="checked";break;
         case "3": $PropertyStr3="checked";break;
       }
	}
$OtherCheck=mysql_fetch_array(mysql_query("SELECT  S.getmoneyNO FROM $DataIn.cw4_otherinsheet S WHERE S.Id=$OtherId",$link_id));
$OtherName=$OtherCheck["getmoneyNO"];
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Estate,$Estate,Pagination,$Pagination,Page,$Page";
if($Estate==0){//已结付时，只可以修改分类和单据
	?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
<tr><td class="A0011">
      <table width="750" border="0" cellspacing="5" id="NoteTable">
		<tr>
          <td height="25" width="150" scope="col"><div align="right">编&nbsp;&nbsp;&nbsp;&nbsp;号</div></td>
          <td scope="col">&nbsp;<?php  echo $Id?></td>
		</tr>
		<tr>
          <td height="25" width="150" scope="col"><div align="right">费用分类</div></td>
          <td scope="col">
		  <select name="TypeId" id="TypeId" style="width:380px">
          <?php 
			$result = mysql_query("SELECT * FROM $DataPublic.adminitype WHERE Estate=1 order by Letter,Id",$link_id);
			if($myrow = mysql_fetch_array($result)){
				do{
					if($TypeId==$myrow["TypeId"]){
						echo"<option value='$myrow[TypeId]|$myrow[Amount]' selected>$myrow[Letter] - $myrow[Name]</option>";
						}
					else{
						echo"<option value='$myrow[TypeId]|$myrow[Amount]'>$myrow[Letter] - $myrow[Name]</option>";
						}
					} while ($myrow = mysql_fetch_array($result));
				}
			?>
          </select></td>
        </tr>
        <tr>
          <td height="29" scope="col"><div align="right">登记日期</div></td>
          <td scope="col"><?php  echo $Date?></td>
        </tr>
        <tr>
          <td height="30" scope="col"><div align="right">金&nbsp;&nbsp;&nbsp;&nbsp;额</div></td>
          <td scope="col"><?php  echo $Amount?></td>
        </tr>
        <tr>
          <td height="24" scope="col"><div align="right">货&nbsp;&nbsp;&nbsp;&nbsp;币</div></td>
          <td scope="col">
              <?php 
		   	$cResult = mysql_query("SELECT Name,Id FROM $DataPublic.currencydata WHERE Estate=1 AND Id='$Currency' order by Id",$link_id);
		    if($cRow = mysql_fetch_array($cResult)){
						echo $cRow["Name"];
				}
          	?>
         </td>
        </tr>
        <tr>
          <td height="13" valign="top" scope="col"><div align="right">说&nbsp;&nbsp;&nbsp;&nbsp;明</div></td>
          <td scope="col"><textarea name="Content" cols="50" rows="3" id="Content" dataType="Require" Msg="未填写说明"><?php  echo $Content?></textarea></td>
        </tr>
        <tr>
          <td height="13" valign="top" scope="col"><div align="right">单 &nbsp;&nbsp;&nbsp;据</div></td>
          <td scope="col"><input name="Attached" type="file" id="Attached" size="65" DataType="Filter" Accept="jpg" Msg="文件格式不对,请重选" Row="6" Cel="1"></td>
        </tr>
		<?php 
		if($Bill==1){
			echo"<tr><td height='13' scope='col'>&nbsp;</td>
			  <td scope='col'><input name='oldAttached' type='checkbox' id='oldAttached' value='1'><LABEL for='oldAttached'> 删除已传单据</LABEL></td></tr>";
			}?>
		<tr>
		  <td height="30" align="right" scope="col">款项是否收回</td>
		  <td scope="col"><input type="hidden" id="OtherId" name="OtherId" value="<?php echo $OtherId?>"><input name="OtherName" type="text" id="OtherName" size="59" onclick="SearchRecord(this)" readonly value="<?php echo $OtherName?>"><input  type="button" id="clearButton" name="clearButton" value="清空" onclick="clearHear()"></td>
	    </tr>
      </table>
</td></tr></table>
<?php 
	}
else{//未结付前修改
?>

<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
<tr><td class="A0011">
      <table width="750" border="0" cellspacing="5" id="NoteTable">
		<tr>
          <td height="25" width="150" scope="col"><div align="right">编&nbsp;&nbsp;&nbsp;&nbsp;号</div></td>
          <td scope="col">&nbsp;<?php  echo $Id?></td>
		</tr>
		<tr>
            <td scope="col" align="right">所属公司</td>
            <td scope="col">
              <?php 
              include "../model/subselect/cSign.php";
			  ?>
			</td></tr>
		<tr>
          <td height="25" width="150" scope="col"><div align="right">费用分类</div></td>
          <td scope="col">
		  <select name="TypeId" id="TypeId" style="width:380px">
          <?php 
			$result = mysql_query("SELECT * FROM $DataPublic.adminitype WHERE Estate=1 order by Letter,Id",$link_id);
			if($myrow = mysql_fetch_array($result)){
				do{
					if($TypeId==$myrow["TypeId"]){
						echo"<option value='$myrow[TypeId]|$myrow[Amount]' selected>$myrow[Letter] - $myrow[Name]</option>";
						}
					else{
						echo"<option value='$myrow[TypeId]|$myrow[Amount]'>$myrow[Letter] - $myrow[Name]</option>";
						}
					} while ($myrow = mysql_fetch_array($result));
				}
			?>
          </select></td>
        </tr>
        <tr>
          <td height="29" scope="col"><div align="right">登记日期</div></td>
          <td scope="col"><input name="theDate" type="text" id="theDate" onfocus="WdatePicker()" value="<?php  echo $Date?>" style="width:380px" maxlength="10" datatype="Date" format="ymd" msg="未选日期或格式不对" readonly></td>
        </tr>
        <tr>
          <td height="30" scope="col"><div align="right">金&nbsp;&nbsp;&nbsp;&nbsp;额</div></td>
          <td scope="col"><input name="Amount" type="text" id="Amount" style="width:380px" value="<?php  echo $Amount?>" dataType="Double" Msg="未填写或格式不对"></td>
        </tr>
        <tr>
          <td height="24" scope="col"><div align="right">货&nbsp;&nbsp;&nbsp;&nbsp;币</div></td>
          <td scope="col"><select name="Currency" id="Currency" style="width:380px" dataType="Require"  msg="未选择货币">
              <option value="" selected>请选择</option>
              <?php 
		   	$cResult = mysql_query("SELECT Name,Id FROM $DataPublic.currencydata WHERE Estate=1 order by Id",$link_id);
		    if($cRow = mysql_fetch_array($cResult)){
				do{
					if($Currency==$cRow["Id"]){
						echo"<option value='$cRow[Id]' selected>$cRow[Name]</option>";
						}
					else{
						echo"<option value='$cRow[Id]'>$cRow[Name]</option>";
						}
					}while ($cRow = mysql_fetch_array($cResult));
				}
          	?>
          </select></td>
        </tr>
        <tr>
          <td height="13" valign="top" scope="col"><div align="right">说&nbsp;&nbsp;&nbsp;&nbsp;明</div></td>
          <td scope="col"><textarea name="Content" cols="50" rows="3" id="Content" dataType="Require" Msg="未填写说明"><?php  echo $Content?></textarea></td>
        </tr>

        <tr>
          <td height="13" valign="top" scope="col"><div align="right">单 &nbsp;&nbsp;&nbsp;据</div></td>
          <td scope="col"><input name="Attached" type="file" id="Attached" size="65" DataType="Filter" Accept="jpg" Msg="文件格式不对,请重选" Row="6" Cel="1"></td>
        </tr>
		<?php 
		if($Bill==1){
			echo"<tr><td height='13' scope='col'>&nbsp;</td>
			  <td scope='col'><input name='oldAttached' type='checkbox' id='oldAttached' value='1'><LABEL for='oldAttached'> 删除已传单据</LABEL></td></tr>";
			}?>
		<tr>
		  <td height="30" align="right" scope="col">款项收回类型</td>
		  <td scope="col"><input name="Property" type="radio" value="1" <?php echo $PropertyStr1?>><span class="redB">其他收入</span>
                      <input name="Property"  type="radio" value="2"  <?php echo $PropertyStr2?>><span class="blueB">Invoice</span>
                      <input name="Property" type="radio" value="3"  <?php echo $PropertyStr3?>><span class="yellowB">薪资</span>
	    </tr>
		<tr>
		  <td height="13" valign="top" scope="col"><div align="right">款项是否收回</div></td>
		  <td scope="col"><input type="hidden" id="OtherId" name="OtherId" value="<?php echo $OtherId?>"><input name="OtherName" type="text" id="OtherName" style="width:380px" onclick="SearchRecord(this)" value="<?php echo $OtherName?>" readonly ><input  type="button" id="clearButton" name="clearButton" value="清空" onclick="clearHear()"></td>
	    </tr>
      </table>
</td></tr></table>
<?php 
}
//步骤6：表尾
include "../model/subprogram/add_model_b.php";
?>
<script>
function SearchRecord(e){
	var r=Math.random();
    var tempK=0;
    var temp=document.getElementsByName("Property");
         for (i=0;i<temp.length;i++){
              if(temp[i].checked){
                    tempK=temp[i].value;
                 }
            }
  switch(tempK){
           case "1":
	            var BackData=window.showModalDialog("cw_otherin_s1.php?r="+r+"&tSearchPage=cw_otherin&fSearchPage=cw_adminicost&SearchNum=1&Action=1","BackData","dialogHeight =500px;dialogWidth=930px;center=yes;scroll=yes");
               break;
          case "2":
	            var BackData=window.showModalDialog("cw_orderin_s1.php?r="+r+"&tSearchPage=cw_otherin&fSearchPage=cw_adminicost&SearchNum=1&Action=6","BackData","dialogHeight =500px;dialogWidth=930px;center=yes;scroll=yes");
               break;
          case "3":
               break;
            default:
               alert("请选择款项收回类型!");return false;
               break;
          }
	if(BackData){
		var CL=BackData.split("^^");
		document.form1.OtherId.value=CL[0];//记录产品ID
		e.value=CL[1];	//文本框显示产品名称
		}
	}
function clearHear(){
    document.getElementById("OtherId").value="";
    document.getElementById("OtherName").value="";
}
</script>