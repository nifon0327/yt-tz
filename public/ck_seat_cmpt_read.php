<?php
defined('IN_COMMON') || include '../basic/common.php';

include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=13;
//$tableMenuS=1490;
ChangeWtitle("$SubCompany 库位构件明细");
$funFrom="ck_cpqkwt_read";
$nowWebPage=$funFrom."_read";
//$sumCols="4";     //求和列
$Th_Col="选项|60|序号|50|项目编号|80|项目名称|100|楼号|60|构件类别|60|楼层|60|构件编号|100|数量|60|构件方量|60|库位|60|入库时间|70|备注|70";
$Pagination=$Pagination==""?1:$Pagination;  //分页标志 0-不分页 1-分页
$Page_Size = 1000; //每页数量
$ActioToS="8,9"; //功能:0查询,1新增,2更新,3删除,4可用,5禁用,6锁定,7解锁,8全选,9反选,10列印,11统计,12其它,13请款,14退回,15取消


//$CheckDate=$CheckDate==""?"":$CheckDate;//默认日期

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
        width: 70px;
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
    #cmptNoCon {
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
        <td class="tds1" colspan="2" style="padding-left: 10px;height: 50px">
            <!-- 项目 -->
            <select name='CompanyIdCon' id='CompanyIdCon' class=" ">
                <option value='' selected>全部项目</option>
                <?php
                $mySql = "select a.Letter, a.Forshort, b.TradeNo, a.CompanyId
                    from $DataIn.trade_object a
                    INNER join $DataIn.trade_info b on a.id = b.TradeId
                    where a.ObjectSign = 2 order by a.Letter";

                $myResult = mysql_query($mySql, $link_id);

                if($myResult  && $myRow = mysql_fetch_array($myResult)){
                    do{

                        $Letter=$myRow["Letter"];
                        $Forshort=$myRow["Forshort"];
                        $Forshort = $Letter . '-' . $Forshort;
                        $ThisCompanyId = $myRow["CompanyId"];

                        echo "<option value='$ThisCompanyId' ", $ThisCompanyId == $CompanyId?"selected":"", ">$Forshort</option>";

                    }while ($myRow = mysql_fetch_array($myResult));
                }
                ?>
            </select>&nbsp;&nbsp;
            &nbsp;&nbsp;
            <!-- 栋号-->
            <select name='BuildingNoCon' id='BuildingNoCon' class=" " >
                <option value='' selected>全部楼栋</option>
                <?php
                $mySql="SELECT DISTINCT a.BuildingNo FROM $DataIn.trade_drawing a order by CAST(a.BuildingNo as SIGNED)";
                $myResult = mysql_query($mySql, $link_id);

                if($myResult  && $myRow = mysql_fetch_array($myResult)){
                    do{
                        $ThisBuildingNo = $myRow["BuildingNo"];

                        echo "<option value='$ThisBuildingNo' ", $ThisBuildingNo == $BuildingNo?"selected":"", ">$ThisBuildingNo 栋</option>";
                    }while ($myRow = mysql_fetch_array($myResult));
                }
                ?>
            </select>&nbsp;&nbsp;

            <!-- 楼层-->
            <select name='FloorNoCon' id='FloorNoCon' class=" " '>
                <option value='' selected>全部楼层</option>
                <?php
                $mySql="SELECT DISTINCT a.FloorNo FROM $DataIn.trade_drawing a order by CAST(a.FloorNo as SIGNED)";
                $myResult = mysql_query($mySql, $link_id);

                if($myResult  && $myRow = mysql_fetch_array($myResult)){
                    do{
                        $ThisFloorNo = $myRow["FloorNo"];

                        echo "<option value='$ThisFloorNo' ", $ThisFloorNo == $FloorNo?"selected":"", ">$ThisFloorNo 层</option>";
                    }while ($myRow = mysql_fetch_array($myResult));
                }
                ?>
            </select>&nbsp;&nbsp;


            <!-- 库位-->
            <select name='SeatIdCon' id='SeatIdCon' class=" " onchange='buildConChange()'>
                <option value='' selected>全部库位</option>
                <?php
                $mySql="SELECT DISTINCT a.SeatId FROM $DataIn.wms_seat a order by SeatId";
                $myResult = mysql_query($mySql, $link_id);

                if($myResult  && $myRow = mysql_fetch_array($myResult)){
                    do{
                        $ThisSeatId = $myRow["SeatId"];

                        echo "<option value='$ThisSeatId' ", $ThisSeatId == $SeatId?"selected":"", ">$ThisSeatId</option>";
                    }while ($myRow = mysql_fetch_array($myResult));
                }
                ?>
            </select>&nbsp;&nbsp;

            <input name='cmptNoCon' type='text' id='cmptNoCon' placeholder="输入构件编号" autocomplete='off' value='<?php echo $cmptNo ?>'/>&nbsp;&nbsp;

            <!-- 入库时间-->
            <?php
                echo "<input name='CheckDateCon' type='text' id='CheckDateCon' size='10' maxlength='10' value='$CheckDate' placeholder=\"入库时间\" onFocus='WdatePicker()' />";
            ?>&nbsp;&nbsp;

            <span type='button' name='Submit' value='查询' class="btn-confirm" onClick='toSearchResult()' > 查　询 </span>

        </td>
    </tr>
</table>

<table border="0" cellspacing="0" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;width: 1000px;border: 1px solid #E2E8E8;border-radius:5px;-moz-box-shadow: 0px 0px 10px #c7c7c7; box-shadow: 0px 0px 10px #c7c7c7;margin:10px 10px 10px 0' bgcolor='#FFF' class="div-select">
    <tr>
        <td class="tds1" style="height: 50px;">&nbsp;&nbsp;
            <span type='button' name='button' class="btn-confirm" value='导出数据' onClick='toExportData()' >导出数据</span>
            <a href="../admin/openorload.php?d=<?php echo $d ?>&f=<?php echo $f ?>&Type=&Action=6"target="download" style="color: #169BD5;margin-left: 5px;margin-right: 5px;display: none">下载模板</a>
        </td>
        <td>
            <span type='button' name='Submit' value='查询' class="btn-confirm" onClick='backToqkwt()' > 返　回 </span>
        </td>
    </tr>
</table>
<?php
//步骤3：
include "../model/subprogram/read_model_3.php";

//检索条件隐藏值
echo " <input type='hidden' name='CompanyId' id='CompanyId' value='$CompanyId' />";
echo " <input type='hidden' name='BuildingNo' id='BuildingNo' value='$BuildingNo' />";
echo " <input type='hidden' name='FloorNo' id='FloorNo' value='$FloorNo' />";
echo " <input type='hidden' name='SeatId' id='SeatId' value='$SeatId' />";
echo " <input type='hidden' name='cmptNo' id='cmptNo' value='$cmptNo' />";
echo " <input type='hidden' name='CheckDate' id='CheckDate' value='$CheckDate' />";

//步骤5：
// 菜单
include "../model/subprogram/read_model_5.php";

List_Title($Th_Col,"1",0);
//步骤6：需处理数据记录处理
$SearchRows = "where 1=1 ";

if ($CompanyId) {
    $SearchRows .= " and c.CompanyId = '$CompanyId' ";
}

if ($BuildingNo) {
    $SearchRows .= " and a.BuildingNo = '$BuildingNo' ";
}

if ($FloorNo) {
    $SearchRows .= " and a.FloorNo = '$FloorNo' ";
}

if ($SeatId) {
    $SearchRows .= " and e.SeatId = '$SeatId' ";
}

if ($cmptNo) {
    $SearchRows .= " and a.CmptNo like '%$cmptNo%' ";
}

if ($CheckDate) {
    $SearchRows .= " and DATE_FORMAT(e.PutawayDate,'%Y-%m-%d') = '$CheckDate' ";
}

$mySql="select  SP.Qty,e.POrderId, e.SeatId,e.PutawayDate, b.TradeNo, c.Forshort, a.BuildingNo, a.FloorNo, a.CmptType, a.CmptNo, a.CVol 
from $DataIn.ch1_shipsplit SP
INNER JOIN $DataIn.yw1_ordersheet e ON e.POrderId = SP.POrderId
inner join $DataIn.yw1_ordermain d on e.OrderNumber = d.OrderNumber  
INNER JOIN $DataIn.trade_object c on d.CompanyId = c.CompanyId
inner join $DataIn.trade_info b on c.id = b.TradeId
inner join $DataIn.productdata dr on dr.ProductId = e.ProductId
inner join $DataIn.trade_drawing a on dr.eCode = concat_ws(\"-\",a.BuildingNo,a.FloorNo,a.CmptNo,a.SN)
$SearchRows AND e.Estate > 0 AND SP.Estate = 1 
order by b.TradeNo, a.BuildingNo, a.FloorNo, a.CmptType, a.CmptNo";
// echo $mySql;

$i = 1;
$j=($Page-1)*$Page_Size+1;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myResult  && $myRow = mysql_fetch_array($myResult)){
    do{
        $m=1;  //必须

        $POrderId = $myRow["POrderId"];
        $shipQty = $myRow["Qty"];

        // $checkShipRow = mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(Qty),0) AS Qty FROM $DataIn.ch1_shipsheet 
        //     WHERE POrderId='$POrderId'", $link_id));
        // $shipQty = $checkShipRow["Qty"];

        $ValueArray = array(
            array(0 => $myRow["TradeNo"], 1 => "align='center'"),
            array(0 => $myRow["Forshort"], 1 => "align='center'"),
            array(0 => $myRow["BuildingNo"], 1 => "align='center'"),
            array(0 => $myRow["CmptType"], 1 => "align='center'"),
            array(0 => $myRow["FloorNo"], 1 => "align='center'"),
            array(0 => $myRow["CmptNo"], 1 => "align='center'"),
            array(0 => $shipQty, 1 => "align='center'"),
            array(0 => $myRow["CVol"], 1 => "align='center'"),
            array(0 => $myRow["SeatId"], 1 => "align='center'"),
            array(0 => $myRow["PutawayDate"], 1 => "align='center'"),
            array(0 => "", 1 => "align='center'"),

//            array(0 => "<a href='trade_embedded_read.php?proId=$Id'>预埋件信息</a>", 1 => "align='center'"),
        );
        $checkidValue = $Id;
        $ChooseOut = "N";

        $Estate = $myRow["Estate"];
        $myOpration = "<input name='checkid[]' type='checkbox' id='checkid$i' data-state='$Estate' value='$checkidValue' disabled>";
        include "../model/subprogram/read_model_6.php";
    }while ($myRow = mysql_fetch_array($myResult));
}
else{
    noRowInfo($tableWidth);
}

//步骤7：
List_Title($Th_Col,"0",0);
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>
<script >
    topFloat = 150;

    //查询
    function toSearchResult(){
        jQuery("#CompanyId").val(jQuery("#CompanyIdCon").val());
        jQuery("#BuildingNo").val(jQuery("#BuildingNoCon").val());
        jQuery("#FloorNo").val(jQuery("#FloorNoCon").val());
        jQuery("#SeatId").val(jQuery("#SeatIdCon").val());
        jQuery("#cmptNo").val(jQuery("#cmptNoCon").val());
        jQuery("#CheckDate").val(jQuery("#CheckDateCon").val());

        document.form1.action="ck_seat_cmpt_read.php";
        document.form1.target = "_self";
        document.form1.submit();
    }

    //返回
    function backToqkwt() {
        document.form1.action="ck_cpqkwt_read.php";
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

        document.form1.action="trade_drawing_del.php?Ids="+Ids+"&proId="+proId;
        document.form1.target = "_self";
        document.form1.submit();
    }

    //导出数据
    function toExportData() {
        document.form1.action="ck_seat_cmpt_export.php";
        document.form1.target = "_blank";
        document.form1.submit();
    }
</script>