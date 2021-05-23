<?php
//步骤1
include "../model/modelhead.php";
//步骤2：
$nowWebPage ="bom_data_export";
$_SESSION["nowWebPage"]=$nowWebPage;
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,type,$type,proId,$proId";
$type = $_GET["type"];
ChangeWtitle("$SubCompany 数据导出");

//步骤3：
$tableWidth=850;$tableMenuS=500;

?>
<style type="text/css">
.input_radio2{
	vertical-align: top;
	margin-top: -1.5px;
}
.select1{
    min-width: 100px;
	height: 25px;
	margin-right: 25px;
	border: 1px solid lightgray;
}
.table_td{
	height: 50px;
	border-bottom: 1px solid lightgray;
}
.td1{
	width: 130px;
	text-align: center;
}
.td2{
	padding-top: 6px;
}
</style>
<body ><form name="form1" id="checkFrom" enctype="multipart/form-data" action="" method="post" >
    <div class="div-select div-mcmain" style='width:<?php echo $tableWidth ?>'>


<table border="0" width="<?php echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#F2F3F5" id='NoteTable'>
	<tr>
		<td class="A0011">
      		<table width="760" border="0" align="center" cellspacing="0">
				<tr>
					<td scope="col" class="table_td td1" style="font-weight: bold;">项目名称</td>
					<td scope="col" class="table_td">
    					<select name='tradeChoose' id='tradeChoose' class="select1">
            			<?php
            			//项目数据检索
            			$mySql="SELECT a.Id, a.Forshort, b.TradeNo FROM $DataIn.trade_object a
            			INNER JOIN $DataIn.trade_info b on a.Id = b.TradeId where a.ObjectSign = 2 order by a.Date";
            			
            			$myResult = mysql_query($mySql, $link_id);
            			if($myResult  && $myRow = mysql_fetch_array($myResult)){
            			    do{
            			        $Id = $myRow["Id"];
            			        $Forshort = $myRow["Forshort"];
            			        //$TradeNo = $myRow["TradeNo"];
            			        echo "<option value='$Id' ", $Id == $proId?"selected":"", ">$Forshort</option>";
            			    }while ($myRow = mysql_fetch_array($myResult));
            			}
            			?>
            			</select>
					</td>
				</tr>
				<tr>
					<td scope="col" style="font-weight: bold;height: 50px;text-align: center">文件信息</td>
					<td scope="col" >
						<input type="checkbox" name="infoChk" id="infoChk" class="input_radio2" value="1" <?php if ($type==null || $type == 1) echo "checked" ?> /><LABEL for="infoChk">BOM信息</LABEL>&nbsp;&nbsp;
				    	<input type="checkbox" name="lossChk" id="lossChk" class="input_radio2" value="2" <?php if ($type==null || $type == 2) echo "checked" ?> /><LABEL for="lossChk">模具</LABEL>&nbsp;&nbsp;
				    	<input type="checkbox" name="mouldChk" id="mouldChk" class="input_radio2" value="3" <?php if ($type==null || $type == 3) echo "checked" ?> /><LABEL for="mouldChk">损耗</LABEL>
					</td>
				</tr>
      		</table>
		</td>
	</tr>
</table>
<?php  //二合一已更新?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#F2F3F5">
  <tr>
   <td  id="menuB1" width="<?php  echo $tableMenuS?>">&nbsp;</td>
   <td width="150" id="menuT2" align="center" height="40">
					<?php 
					echo"<span onClick='toExport()' class='btn-confirm'>导出</span>&nbsp;";
					if ($type) {
					    echo"&nbsp;<span onClick='javascript:ReOpen(\"$fromWebPage\");' class='btn-confirm'>返回</span>";
					}
					?>	
   </td>
   </tr>
   	<?php 
   	$SearchRows="";
	if($Parameter!=""){
		PassParameter($Parameter);
		}
	?>
</table>
    </div>
  </form>
<script >
function toExport() {
	//alert(jQuery('#tradeChoose').val());
	if(jQuery('#infoChk').is(':checked') || 
			jQuery('#lossChk').is(':checked') || 
			jQuery('#mouldChk').is(':checked') ) {

		document.form1.action="bom_data_download.php";
		document.form1.target = "_blank";	
		document.form1.submit();
	} else {
		alert("请选择导出信息");
	}
}

</script>

</body>
</html>
