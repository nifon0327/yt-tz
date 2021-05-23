<?php
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=8;
//$tableMenuS=1490;
ChangeWtitle("$SubCompany 损耗信息");
$funFrom="bom_loss";
$nowWebPage=$funFrom."_read";
//$sumCols="4";		//求和列
$Th_Col="选项|60|顺序号|50|构件类型|100|配件编码|100|配件名称|200|单位|50|本次标准|70|PC定额标准|80";
$Pagination=$Pagination==""?1:$Pagination;  //分页标志 0-不分页 1-分页
$Page_Size = 100; //每页数量
$ActioToS="8,9"; //功能:0查询,1新增,2更新,3删除,4可用,5禁用,6锁定,7解锁,8全选,9反选,10列印,11统计,12其它,13请款,14退回,15取消

//检索条件
// 0-未审核 1-已审核
$statusType = 0;
if (isset($_POST["statusType"])) {
    $statusType = $_POST["statusType"];
}

//项目ID
$proId = "";
if (isset($_GET["proId"])) {
    $proId = $_GET["proId"];
}
if (isset($_POST["proId"])) {
    $proId = $_POST["proId"];
}

if (isset($_GET["proId"]) || isset($_POST["proId"]) ) {
    $mySql = "select Estate from $DataIn.bom_object where TradeId = $proId";
    $myResult = mysql_query($mySql, $link_id);
    if($myResult  && $myRow = mysql_fetch_array($myResult)){

        $Estate = $myRow["Estate"];
        if ($Estate == 0 || $Estate == 1 ) {
            //未审核
            $statusType = 0;
        } else {
            $statusType = 1;
        }
    }
}

//项目数据检索
$mySql="SELECT a.Id, a.Forshort, b.Estate FROM $DataIn.trade_object a
inner join $DataIn.bom_object b on a.id = b.tradeid ";
if ($statusType == 0) {
   $mySql .= " AND b.Estate in (0, 1)";
} else if ($statusType == 1) {
   $mySql .= " AND b.Estate in (2, 3, 4)";
}
$mySql .= "where a.ObjectSign = 2 order by a.Date";
//echo $mySql;

$myResult = mysql_query($mySql, $link_id);

$tradeList = array();
if($myResult  && $myRow = mysql_fetch_array($myResult)){
    do{
        $tradeList[] = $myRow;

        if ($proId) {
            if ($proId == $myRow["Id"]) {
                $Estate = $myRow["Estate"];
            }
        } else {
            $proId = $myRow["Id"];
            $Estate = $myRow["Estate"];
        }

    }while ($myRow = mysql_fetch_array($myResult));
}

//物料名称
$name = "";
if ($_POST["name"]) {
    $name = $_POST["name"];
}

?>

<style type="text/css">
.input_radio1{
	vertical-align: top;
	margin-top: -1.5px;
	margin-left: 20px;
}
.select1{
    min-width: 100px;
	height: 25px;
	margin-right: 25px;
	border: 1px solid lightgray;
}
.btn_a1{
	width: 80px;
	height: 25px;
	font-size: 12px;
	color: #0099FF;
	text-align: center;
    line-height: 25px;
	box-sizing: border-box;
	border: 1px solid rgba(121, 121, 121, 1);
	border-radius: 5px;
	display: inline-block;
}
a.btn_a1:link,a.btn_a1:visited {
    color: #0099FF;
}
.input_btn2{
	width: 80px;
	height: 25px;
	color: #000;
	border: 1px solid #000;
	border-radius: 5px;
    margin-left: 10px;
	background-color: rgba(0, 153, 102, 1);
}
#mouldNoCon {
    width: 150px;
    height: 25px;
}
.tds1{
	height: 35px;
}
.lable_active{
	font-weight: bold;
}
</style>
<table border="0" cellspacing="0" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;width: 1000px;border: 1px solid #E2E8E8;border-radius:5px;-moz-box-shadow: 0px 0px 10px #c7c7c7; box-shadow: 0px 0px 10px #c7c7c7;margin:10px 10px 10px 0' bgcolor='#FFF' class="div-select">
	<tr>
		<td style="padding-left: 10px;height: 50px">
		<input name="statusTypeCon" type="radio" id="statusType0" class="input_radio1" value="0" <?php if ($statusType == 0) {echo "checked"; } else { echo "onClick='statusTypeChange(0)'";} ?> /><LABEL for="statusType0" <?php if ($statusType == 0) {echo "class='lable_active'";} ?> >未审核</LABEL>
		<input name="statusTypeCon" type="radio" id="statusType1" class="input_radio1" value="1" <?php if ($statusType == 1) {echo "checked"; } else { echo "onClick='statusTypeChange(1)'";} ?> /><LABEL for="statusType1" <?php if ($statusType == 1) {echo "class='lable_active'";} ?> >已审核</LABEL>
		</td>
		<td class="tds1"  style="padding-left: 10px;height: 50px">
		      <!-- 项目 -->
			<select name='proIdCon' id='proIdCon' onchange='proIdConChange()' class="select1">
			<?php
			foreach ($tradeList as $trade){
			    $Id=$trade["Id"];
			    $Forshort=$trade["Forshort"];
			    echo "<option value='$Id' ", $Id == $proId?"selected":"", ">$Forshort</option>";
			}
			?>
			</select>
        </td>
        <td style="height: 50px">
            <a href='bom_mould_read.php?proId=<?php echo $proId ?>' class="btn-confirm" style="display:inline-block">模具信息</a>
            <a href='bom_info_read.php?proId=<?php echo $proId ?>' class="btn-confirm" style="display:inline-block;width: auto">BOM信息</a>
        </td>
        <td style="height: 50px">
            <span type='button' name='button' class="btn-confirm" value='导出数据' onClick='toExportData()' style="display:inline-block">导出数据</span>
            <?php if ($Estate == 0 || $Estate == 3 || $Estate == 4 ) { ?>
                <span type='button' name='button' class="btn-confirm" value='导入数据' onClick='toImportData()' style="display:inline-block">导入数据</span>
                <span type='button' name='button' class="btn-confirm" value='删除' onClick='toDeleteData()' style="display:inline-block">删　除</span>
            <?php } ?>
        </td>
    </tr>
</table>
<?php
//步骤3：
include "../model/subprogram/read_model_3.php";

//检索条件隐藏值
echo " <input type='hidden' name='statusType' id='statusType' value='$statusType' />";
echo " <input type='hidden' name='proId' id='proId' value='$proId' />";
echo " <input type='hidden' name='name' id='name' value='$name' />";
echo " <input type='hidden' name='Estate' id='Estate' value='$Estate' />";

$SearchRows = " AND a.TradeId= $proId";

if ($name) {
    $SearchRows .= " AND a.MaterName like '%$name%'";
}

$Orderby = "order by a.Id ";

//步骤5：
// 菜单
include "../model/subprogram/read_model_5.php";

//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
//A.Estate 不用,使用项目表状态
$mySql="select a.Id, a.CmptType, a.StuffType,
u.Name, a.ThisStd, a.PcStd,
b.TradeNo, c.Forshort, d.Estate,s.StuffCname
from $DataIn.stuff_loss a
LEFT JOIN $DataIn.Stuffdata s on s.StuffEname = a.StuffType
LEFT JOIN $DataIn.stuffunit u on u.id = s.Unit
LEFT JOIN $DataIn.trade_info b on a.TradeId = b.TradeId
LEFT JOIN $DataIn.trade_object c on c.id = a.TradeId
INNER JOIN $DataIn.bom_object d on d.TradeId = a.TradeId
where 1 $SearchRows $Orderby";

//echo $mySql;

$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myResult  && $myRow = mysql_fetch_array($myResult)){
    do{
        $m=1;  //必须
        $ValueArray=array(
                array(0=>$myRow["CmptType"],    1=>"align='center'"),
                array(0=>$myRow["StuffType"],    1=>"align='center'"),
                array(0=>$myRow["StuffCname"],        1=>"align='center'"),
                array(0=>$myRow["Name"],        1=>"align='center'"),
                array(0=>$myRow["ThisStd"],     1=>"align='center'"),
                array(0=>$myRow["PcStd"],       1=>"align='center'")
        );

        $checkidValue=$myRow["Id"];
        $ChooseOut = "N";
        $myOpration = "<input name='checkid[]' type='checkbox' id='checkid$i' value='$checkidValue' disabled>";
        include "../model/subprogram/read_model_6.php";
    }while ($myRow = mysql_fetch_array($myResult));
}
else{
    noRowInfo($tableWidth);
}

//步骤7：
echo '</div>';
$myResult = mysql_query($mySql,$link_id);
if ($myResult ) $RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>
<script >

topFloat = 150;

//查询
function toSearchResult(){

	jQuery("#statusType").val(jQuery("input[name='statusTypeCon']:checked").val());
	jQuery("#proId").val(jQuery("#proIdCon").val());
	jQuery("#name").val(jQuery("#nameCon").val());

	document.form1.action="bom_loss_read.php";
	document.form1.target = "_self";
	document.form1.submit();
}

//删除
function toDeleteData() {
	var choosedRow=0;
	var Ids;

	jQuery('input[name^="checkid"]:checkbox').each(function() {
        if (jQuery(this).prop('checked') ==true) {
			choosedRow=choosedRow+1;
			if (choosedRow == 1) {
				Ids = jQuery(this).val();
			} else {
				Ids = Ids + "," + jQuery(this).val();
			}
        }
	});

	if (choosedRow == 0) {
		alert("该操作要求选定记录！");
		return;
	}

	var message=confirm("确定要进行此操作吗？");
	if(message==false){
		return;
	}

	var proId = jQuery("#proId").val();

	document.form1.action="bom_loss_del.php?Ids="+Ids+"&proId="+proId;
	document.form1.target = "_self";
	document.form1.submit();
}

//导出数据
function toExportData() {
	document.form1.action="bom_data_export.php?type=2";
	document.form1.target = "_self";
	document.form1.submit();
}

function toImportData() {
	document.form1.action="bom_data_add.php?type=2";
	document.form1.target = "_self";
	document.form1.submit();
}

function proIdConChange() {
	jQuery("#proId").val(jQuery("#proIdCon").val());
	jQuery("#type").val("");
	jQuery("#status").val("");
	jQuery("#mouldNo").val("");

	RefreshPage("bom_loss_read");
}

function statusTypeChange(statusType) {
	jQuery("#statusType").val(statusType);
	jQuery("#proId").val("");
	jQuery("#type").val("");
	jQuery("#status").val("");
	jQuery("#mouldNo").val("");

	RefreshPage("bom_loss_read");
}
</script>
