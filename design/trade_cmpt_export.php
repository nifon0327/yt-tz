<?php
//步骤1
include "../model/modelhead.php";
//步骤2：
$nowWebPage ="trade_cmpt_export";
$_SESSION["nowWebPage"]=$nowWebPage;
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,type,$type,proId,$proId";
$type = $_GET["type"];
ChangeWtitle("$SubCompany 数据导出");

//步骤3：
$tableWidth=850;$tableMenuS=500;

/* 栋号 by.lwh 20180403 */

$proId = "";
if ($_GET["tradeChoose"]) {
    $proId = $_GET["tradeChoose"];
}
if ($_POST["tradeChoose"]) {
    $proId = $_POST["tradeChoose"];
}


$build = "";
if ($_GET["build"]) {
    $build = $_GET["build"];
}
if ($_POST["build"]) {
    $build = $_POST["build"];
}

if (!$proId) {
    $proIdSql = mysql_query("select TradeId from $DataIn.trade_info order by TradeId DESC limit 1", $link_id);
    $proId = mysql_fetch_array($proIdSql);
    $proId = $proId[0];
    }
    if ($proId){
    $drawing = mysql_fetch_array(mysql_query("select count(*) from(SELECT  count(*) FROM $DataIn.trade_drawing a where a.TradeId = $proId GROUP BY a.BuildingNo) D"));  //图纸
    $embedded = mysql_fetch_array(mysql_query("select count(*) from(SELECT  count(*) FROM $DataIn.trade_embedded a where a.TradeId = $proId GROUP BY a.BuildingNo) E"));  //预埋件
    $steel = mysql_fetch_array(mysql_query("select count(*) from(SELECT  count(*) FROM $DataIn.trade_steel a where a.TradeId = $proId GROUP BY a.BuildingNo) S"));  //钢筋

    if ($drawing==$embedded && $drawing==$steel && $embedded==$steel){
        $mySql = "SELECT DISTINCT a.BuildingNo FROM $DataIn.trade_steel a where a.TradeId = $proId order by a.BuildingNo";
        $myResult = mysql_query($mySql, $link_id);
        if ($myResult && $myRow = mysql_fetch_array($myResult)) {
            do {
                $buildList[] = $myRow;
                if ($build) {
                    if ($build == $myRow["BuildingNo"]) {

                    }
                } else {
                    $build = $myRow["BuildingNo"];
                }
            } while ($myRow = mysql_fetch_array($myResult));
        }
    }
}

echo " <input type='hidden' name='proId' id='proId' value='$proId' />";
echo " <input type='hidden' name='build' id='build' value='$build' />";
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
    					<select name='tradeChoose' id='tradeChoose' onchange='proIdConChange()' class="select1">
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
                        <!-- 栋号-->
                        <select name='buildChoose' id='buildChoose' class="select1" '>
                            <?php
                            if (!$buildList){
                                echo "<option value='' selected>不支持分栋导出</option>";
                            }else{
                                foreach ($buildList as $buildData){
                                    $BuildingNo=$buildData["BuildingNo"];
                                    echo "<option value='$BuildingNo' ", $BuildingNo == $build?"selected":"", ">$BuildingNo</option>";
                                }
                            }
                            ?>
                        </select>
					</td>
				</tr>
				<tr>
					<td scope="col" class="table_td td1">文件信息</td>
					<td scope="col" class="table_td td2">
						<input type="checkbox" name="drawingChk" id="drawingChk" class="input_radio2" value="1"/><LABEL for="drawingChk">图纸</LABEL>&nbsp;&nbsp;
				    	<input type="checkbox" name="steelChk" id="steelChk" class="input_radio2" value="2"/><LABEL for="steelChk">钢筋</LABEL>&nbsp;&nbsp;
				    	<input type="checkbox" name="embeddedChk" id="embeddedChk" class="input_radio2" value="3"/><LABEL for="embeddedChk">预埋件</LABEL>
					</td>
				</tr>
				<tr>
		  			<td class="table_td td1" scope="col">图纸信息</td>
		  			<td scope="col" class="table_td td2">
						<input type="checkbox" name="PordDrawingChk" id="PordDrawingChk" class="input_radio2" value="1"/><LABEL for="drawingChk">成品图纸</LABEL>&nbsp;&nbsp;
				    	<input type="checkbox" name="SteelDrawingChk" id="SteelDrawingChk" class="input_radio2" value="2"/><LABEL for="steelChk">钢筋图纸</LABEL>&nbsp;&nbsp;
				    	<input type="checkbox" name="MouldDrawingChk" id="MouldDrawingChk" class="input_radio2" value="3"/><LABEL for="embeddedChk">模具图纸</LABEL>&nbsp;&nbsp;
		  				<input type="checkbox" name="EmbeddedDrawingChk" id="EmbeddedDrawingChk" class="input_radio2" value="4"/><LABEL for="embeddedChk">预埋件图纸</LABEL>
		  			</td>
	    		</tr>
      </table>
	</td></tr>
</table>
<?php  //二合一已更新?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#F2F3F5">
 <tr><td height="5" colspan="6" class="A0011">&nbsp;</td></tr>
  <tr>
   <td  id="menuB1" width="<?php  echo $tableMenuS?>">&nbsp;</td>
   <td width="150" id="menuT2" align="center" height="50px">
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
	if(jQuery('#drawingChk').is(':checked') ||
			jQuery('#steelChk').is(':checked') ||
			jQuery('#embeddedChk').is(':checked') ||
			jQuery('#PordDrawingChk').is(':checked') ||
			jQuery('#SteelDrawingChk').is(':checked') ||
			jQuery('#MouldDrawingChk').is(':checked') ||
			jQuery('#EmbeddedDrawingChk').is(':checked')) {

		document.form1.action="trade_cmpt_download.php";
		document.form1.target = "_blank";
		document.form1.submit();
	} else {
		alert("请选择导出信息");
	}
}

function proIdConChange() {
    jQuery("#proId").val(jQuery("#tradeChoose").val());
    jQuery("#build").val();

    RefreshPage("trade_cmpt_export");
}

</script>

</body>
</html>
