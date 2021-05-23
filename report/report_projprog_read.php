<?php
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=13;
$tableMenuS=500;
ChangeWtitle("$SubCompany 报表");
$funFrom="report_projprog";
$From=$From==""?"read":$From;
$sumCols="5,6,7,8,9,10";//合计列数
$Th_Col='选项|40|序号|40|项目编号|100|项目名称|100|构件类型|60|吊装总层数|60|施工楼层|60|总方量（m³）|100|构件总数量|60|生产完成方量（m³）|100|发货方量（m³）|100|首批构件供货时间|120|构件生产结束时间|120|跟新时间|120|确认|60';
//分页
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 20;							//每页默认记录数量
$nowWebPage=$funFrom."_read";
$rightShow=true;//显示系统右键菜单
$FilterSql = '';
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
    .input_btn3{
        margin-left: 20px;
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
    .firsttr{
        border-bottom: 1px solid #000;
    }
</style>
<table border="0" cellspacing="0" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;margin-bottom: 5px;border: 1px solid #000;width: 1075px;'>
    <tr>
        <td class="tds1 firsttr" style="padding-left: 10px;" >
            <?php
            //项目编号
            $TradeNoSql= mysql_query("select DISTINCT TradeNo from $DataIn.rp_projectprogress",$link_id);
            if($TradeNoRow = mysql_fetch_array($TradeNoSql)){
                echo "<select name='TradeNo' id='TradeNo' class='select1' onchange='FilterDate(this)'>";
                echo "<option value='' selected>项目编号</option>";
                do{
                    $ThisTradeNo=$TradeNoRow["TradeNo"];

                    if($TradeNo==$ThisTradeNo){
                        echo"<option value='$ThisTradeNo' selected>$ThisTradeNo</option>";
                        $FilterSql.=" and TradeNo = '$ThisTradeNo' ";
                    }
                    else{
                        echo"<option value='$ThisTradeNo'>$ThisTradeNo</option>";
                    }
                }while ($TradeNoRow = mysql_fetch_array($TradeNoSql));
                echo"</select>";
            }

            //项目名称
            $TradeNameSql= mysql_query("select DISTINCT TradeName from $DataIn.rp_projectprogress",$link_id);
            if($TradeNameRow = mysql_fetch_array($TradeNameSql)){
                echo "<select name='TradeName' id='TradeName'class='select1' onchange='FilterDate(this)'>";
                echo "<option value='' selected>项目名称</option>";
                do{
                    $ThisTradeName=$TradeNameRow["TradeName"];

                    if($TradeName==$ThisTradeName){
                        echo"<option value='$ThisTradeName' selected>$ThisTradeName</option>";
                        $FilterSql.=" and TradeName = '$ThisTradeName' ";
                    }
                    else{
                        echo"<option value='$ThisTradeName'>$ThisTradeName</option>";
                    }
                }while ($TradeNameRow = mysql_fetch_array($TradeNameSql));
                echo"</select>";
            }

            //状态
            echo "<select name='Confirm' id='Confirm' class='select1' onchange='FilterDate(this)'>";
            echo "<option value='' selected>状态选择</option>";
            if($Confirm==='1'){
                echo "<option value='0'>未确定</option>";
                echo "<option value='1' selected>已确定</option>";
                $FilterSql .= " and confirm=1";
            }else if($Confirm==='0'){
                echo "<option value='0' selected>未确定</option>";
                echo "<option value='1'>已确定</option>";
                $FilterSql .= " and confirm=0";
            }else{
                echo "<option value='0'>未确定</option>";
                echo "<option value='1'>已确定</option>";
            }
            echo"</select>";
            ?>
        </td>
    </tr>
    <tr>
        <td align="left" class="tds1" >
            <?php
            echo "<input type='button' class='input_btn2' name='Submit' value='全选' onclick=\"All_elects('$theDefaultColor', '$thePointerColor', '$theMarkColor', $ColsNumber)\">";
            echo "<input type='button' class='input_btn2' name='Submit' value='反选' onclick=\"Instead_elects('$theDefaultColor', '$theDefaultColor', '$theMarkColor', $ColsNumber)\">";
            echo "<input type='button' class='input_btn2 input_btn3' name='Submit' value='确定' onClick='ConfirmUd()'>";
            echo "<input type='button' class='input_btn2' name='Submit' value='删除' onclick='DeleteUd()'>";
            echo "<input type='button' class='input_btn2 input_btn3' name='Submit' value='导入数据' onClick='ToImportData()'>";
            echo "<input type='button' class='input_btn2' name='Submit' value='导出数据' onClick='ToExportData()'>";
            $d=anmaIn("report/phpExcelReader/",$SinkOrder,$motherSTR);
            $f=anmaIn("report_projprog_sample.xls",$SinkOrder,$motherSTR);
            echo "<a href=\"../admin/openorload.php?d=$d&f=$f&Type=&Action=6\" target='download' style='color: #169BD5;margin: 0 5px;'>下载模板</a>";
            ?>
        </td>
    </tr>
</table>
<?php
include "../report/subprogram/read_model_3.php";
//检索条件隐藏值
echo " <input type='hidden' name='TradeNo' id='TradeNo' value='$TradeNo' />";
echo " <input type='hidden' name='TradeName' id='TradeName' value='$TradeName' />";
echo " <input type='hidden' name='Confirm' id='Confirm' value='$Confirm' />";

//步骤5：
//$helpFile=1;
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);

//$dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(NOW(),1) AS CurWeek",$link_id));
//$curWeeks=$dateResult["CurWeek"];

$mySql = "select Id, TradeNo, TradeName, CmptType, HoistFloors, WorkFloor, TotalCube, CmptTotal, FinishedCube, DeliveredCube,
FCmptDTime, CmptETime, UpdatedTime, confirm
from $DataIn.rp_projectprogress
WHERE 1=1 $SearchRows $FilterSql 
GROUP BY Id  ORDER BY Id ";
//echo $mySql;

//$tempStuffId="";
$DefaultBgColor=$theDefaultColor;
if($myResult = mysql_query($mySql.$PageSTR,$link_id)){
    while($myRow = mysql_fetch_array($myResult)){
        $m=1;
        $TradeNo = $myRow['TradeNo'];
        $TradeName = $myRow['TradeName'];
        $CmptType = $myRow['CmptType'];
        $HoistFloors = $myRow['HoistFloors'];
        $WorkFloor = $myRow['WorkFloor'];
        $TotalCube = $myRow['TotalCube'];
        $CmptTotal = $myRow['CmptTotal'];
        $FinishedCube = $myRow['FinishedCube'];
        $DeliveredCube = $myRow['DeliveredCube'];
        $FCmptDTime = $myRow['FCmptDTime'];
        $CmptETime = $myRow['CmptETime'];
        $UpdatedTime = $myRow['UpdatedTime'];
        $confirm = $myRow['confirm']==1?'<span style="color:#009900">已确定</span>':'<span style="color:#FF6633">未确定</span>';
        $ValueArray=array(
            array(0=>$TradeNo,1=>"align='center'"),
            array(0=>$TradeName,1=>"align='center'"),
            array(0=>$CmptType,1=>"align='center'"),
            array(0=>$HoistFloors,1=>"align='center'"),
            array(0=>$WorkFloor,1=>"align='center'"),
            array(0=>$TotalCube,1=>"align='center'"),
            array(0=>$CmptTotal,1=>"align='center'"),
            array(0=>$FinishedCube,1=>"align='center'"),
            array(0=>$DeliveredCube,1=>"align='center'"),
            array(0=>$FCmptDTime,1=>"align='center'"),
            array(0=>$CmptETime,1=>"align='center'"),
            array(0=>$UpdatedTime,1=>"align='center'"),
            array(0=>$confirm,1=>$confirmColor." align='center'")
        );
        $checkidValue = $myRow['Id'];
        $ChooseOut = "N";
        $myOpration = "<input name='checkid[]' type='checkbox' id='checkid$i' value='$checkidValue' disabled>";
        include "../model/subprogram/read_model_6.php";
    }
}else{
    noRowInfo($tableWidth);
}
//步骤7：
echo '</div>';
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
echo"<DIV id='TotalStatusBar' style='display:none;'>&nbsp;</div>";//总计行
?>
<script>
    //下拉菜单筛选
    function FilterDate(host){
        $('form#checkFrom #' + host.id).val(host.value);
        jQuery('#Page').val('1');
        document.form1.submit();
    }
    //导入报表
    function ToImportData() {
        document.form1.action="report_projprog_dely_add.php";
        document.form1.target = "_self";
        document.form1.submit();
    }
    //导出报表
    function ToExportData() {

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
        document.form1.action="report_projprog_dely_export.php?Ids=" + Ids;
        document.form1.target = "download";
        document.form1.submit();
        document.form1.action='';//导出后还原form1属性
        document.form1.target='';
    }
    //删除
    function DeleteUd(){
        var choosedRow=0;
        var Ids='';
        var bool = false;
        jQuery('input[name^="checkid"]:checkbox').each(function() {
            if (jQuery(this).prop('checked') ==true) {
                //确定的记录不能删除
                if(jQuery(this).parent().parent().find('td:last').text()=='已确定'){
                    bool = true;
                    return false;
                }
                choosedRow=choosedRow+1;
                if (choosedRow == 1) {
                    Ids = jQuery(this).val();
                } else {
                    Ids = Ids + "," + jQuery(this).val();
                }
            }
        });
        if(bool){
            alert('已确定记录不能删除！');
            return;
        }
        if (choosedRow == 0) {
            alert("该操作要求选定记录！");
            return;
        }
        if(!confirm('确定要进行此操作吗？')){
            return;
        }
        document.form1.action="report_projprog_del.php?Ids=" + Ids;
        document.form1.submit();
    }
    //确认
    function ConfirmUd(){
        var choosedRow=0;
        var Ids='';
        var bool = false;
        jQuery('input[name^="checkid"]:checkbox').each(function() {
            if (jQuery(this).prop('checked') ==true) {
                //已确定的记录无需再确定
                if(jQuery(this).parent().parent().find('td:last').text()=='已确定'){
                    bool = true;
                    return false;
                }
                choosedRow=choosedRow+1;
                if (choosedRow == 1) {
                    Ids = jQuery(this).val();
                } else {
                    Ids = Ids + "," + jQuery(this).val();
                }
            }
        });
        if(bool){
            alert('已确定的记录无需再确定！');
            return;
        }
        if (choosedRow == 0) {
            alert("该操作要求选定记录！");
            return;
        }
        if(!confirm('确定要进行此操作吗？')){
            return;
        }
        document.form1.action="report_projprog_confirm.php?Ids=" + Ids;
        document.form1.submit();
    }
</script>
