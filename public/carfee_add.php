<?php 
include "../model/modelhead.php";
//步骤2：//需处理
ChangeWtitle("$SubCompany 新增车辆费用");
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="funFrom,$funFrom,From,$From,Estate,$Estate,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理;
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
	<table width="750" border="0" cellspacing="5" id="NoteTable">
		<tr>
            <td width="180" height="25" align="right" scope="col">费用分类</td>
            <td scope="col">
			<select name="TypeId" id="TypeId" style="width:380px" dataType="Require" msg="未选择分类">
			<option value="" selected>请选择</option>
			<?php 
			$result = mysql_query("SELECT * FROM $DataPublic.carfee_type WHERE Estate=1 order by Id",$link_id);
			if($myrow = mysql_fetch_array($result)){
				do{
					echo"<option value='$myrow[Id]'>$myrow[Name]</option>";//
					} while ($myrow = mysql_fetch_array($result));
				}
			?>		
		 	</select></td></tr>
		 	<tr>
            <td scope="col" align="right">所属公司</td>
            <td scope="col">
              <?php 
              include "../model/subselect/cSign.php";
			  ?>
			</td></tr>
		    <tr>
              <td height="29" scope="col" align="right">所属会计科目</td>
                <td>
                <?php 
                $RowFromSTR="admintype";
                include "../model/subselect/acfirst_sType.php";
				?>
                </td>
		   </tr>
		 	
		<tr>
		  <td height="29" align="right" scope="col">登记日期</td>
		  <td scope="col"><input name="theDate" type="text" id="theDate" onfocus="WdatePicker()" value="<?php  echo date("Y-m-d");?>" size="49" maxlength="10" dataType="Date" format="ymd" Msg="未选日期或格式不对" readonly></td>
	    </tr>
		<tr>
		  	<td height="24" align="right" scope="col">货&nbsp;&nbsp;&nbsp;&nbsp;币</td>
		  	<td scope="col">
			<select name="Currency" id="Currency" style="width:380px" dataType="Require"  msg="未选择货币">
			<option value="" selected>请选择</option>
		  	<?php 
		   	$cResult = mysql_query("SELECT Name,Id FROM $DataPublic.currencydata WHERE Estate=1 order by Id",$link_id);
		    if($cRow = mysql_fetch_array($cResult)){
				do{
					echo"<option value='$cRow[Id]'>$cRow[Name]</option>";
					}while ($cRow = mysql_fetch_array($cResult));
				}
          	?>
		  	</select></td>
	    </tr>


		<tr>
		  <td height="30" align="right" scope="col">指定车辆</td>
		  <td scope="col"><input type="hidden" id="CarId" name="CarId"><input name="CarName" type="text" id="CarName" size="49" onclick="SearchRecord(this)" readonly></td>
	    </tr>

		<tr>
		  <td height="30" align="right" scope="col">金&nbsp;&nbsp;&nbsp;&nbsp;额</td>
		  <td scope="col"><input name="Amount" type="text" id="Amount" size="49" dataType="Double" Msg="未填写或格式不对"></td>
	    </tr>

      <tr>
        <td align="right">起始里程</td>
        <td><input name="sCourses" type="text" id="sCourses" value="0" style="width:380px" require="false" maxlength="10" dataType="Number"  msg="未填写或格式不对"></td>
      </tr>
      <tr>
        <td align="right">结束里程</td>
        <td><input name="eCourses" type="text" id="eCourses" value="0" style="width:380px" require="false" maxlength="10" dataType="Number"  msg="未填写或格式不对"></td>
      </tr>

		<tr>
		  <td height="13" align="right" valign="top" scope="col">说&nbsp;&nbsp;&nbsp;&nbsp;明</td>
		  <td scope="col"><textarea name="Content" cols="51" rows="3" id="Content" dataType="Require" Msg="未填写说明"></textarea></td>
		</tr>
		<tr>
		  <td height="13" align="right" valign="top" scope="col">单 &nbsp;&nbsp;&nbsp;据</td>
		  <td scope="col"><input name="Attached" type="file" id="Attached" size="52" DataType="Filter" Accept="jpg" Msg="文件格式不对,请重选" Row="5" Cel="1"></td>
	    </tr>

        </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>
<script>
function SearchRecord(e){
	var r=Math.random();
	 var BackData=window.showModalDialog("car_info_s1.php?r="+r+"&tSearchPage=car_info&fSearchPage=carfee&SearchNum=1&Action=2","BackData","dialogHeight =500px;dialogWidth=930px;center=yes;scroll=yes");

	if(BackData){
		var CL=BackData.split("^^");
		document.form1.CarId.value=CL[0];//记录产品ID
		e.value=CL[1];	//文本框显示车辆名称
		}
	}

</script>
