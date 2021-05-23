<?php
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=13;
$tableMenuS=500;
ChangeWtitle("$SubCompany 报表");
$funFrom="report_produce";
$From=$From==""?"read":$From;
$sumCols="5,6,7,8,9,10,11";//合计列数
$Th_Col='选项|40|序号|40|日期|100|生产线|100|班组|60|计划量（m³）|100|实际完成量（m³）|100|达成率（%）|100|出勤工时（H）|60|出勤人数|60|每日人均效率|100|人均小时效率|100|未达成原因分析|120|确认|60';
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
<table border="0" cellspacing="0" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;margin-bottom: 5px;border: 1px solid #000;width: 975px;'>
    <tr>
        <td class="tds1 firsttr" style="padding-left: 10px;" >
            <?php
                //生产线
                $workshopSql= mysql_query("select DISTINCT WorkShop
                            from $DataIn.rp_produce",$link_id);
                if($workshopRow = mysql_fetch_array($workshopSql)){
                    echo "<select name='WorkShop' id='WorkShop' class='select1' onchange='FilterDate(this)'>";
                    echo "<option value='' selected>全部生产线</option>";
                    do{
                        $ThisWorkShop=$workshopRow["WorkShop"];

                        if($WorkShop==$ThisWorkShop){
                            echo "<option value='$ThisWorkShop' selected>$ThisWorkShop</option>";
                            $FilterSql.=" and WorkShop = '$ThisWorkShop' ";
                        }
                        else{
                            echo "<option value='$ThisWorkShop'>$ThisWorkShop</option>";
                        }
                    }while ($workshopRow = mysql_fetch_array($workshopSql));
                    echo "</select>";
                }

                //班组
                $workgroupSql= mysql_query("select DISTINCT WorkGroup
                            from $DataIn.rp_produce",$link_id);
                if($workgroupRow = mysql_fetch_array($workgroupSql)){
                    echo "<select name='WorkGroup' id='WorkGroup'class='select1' onchange='FilterDate(this)'>";
                    echo "<option value='' selected>全部班组</option>";
                    do{
                        $ThisWorkGroup=$workgroupRow["WorkGroup"];

                        if($WorkGroup==$ThisWorkGroup){
                            echo"<option value='$ThisWorkGroup' selected>$ThisWorkGroup</option>";
                            $FilterSql.=" and WorkGroup = '$ThisWorkGroup' ";
                        }
                        else{
                            echo"<option value='$ThisWorkGroup'>$ThisWorkGroup</option>";
                        }
                    }while ($workgroupRow = mysql_fetch_array($workgroupSql));
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

                //时间
                if ($periodCon == 1) {
                    //最近7天
                    $FilterSql .= " AND TO_DAYS(NOW()) - TO_DAYS(WorkDate) <= 7";
                } else if ($periodCon == 2) {
                    //最近15天
                    $FilterSql .= " AND TO_DAYS(NOW()) - TO_DAYS(WorkDate) <= 15";
                } else if ($periodCon == 3) {
                    //最近30天
                    $FilterSql .= " AND TO_DAYS(NOW()) - TO_DAYS(WorkDate) <= 30";
                } else if ($periodCon == 4) {
                    //30天前
                    $FilterSql .= " AND TO_DAYS(NOW()) - TO_DAYS(WorkDate) > 30";
                }
            ?>
            <select name='periodCon' id='periodCon' class="select1" onchange='FilterDate(this)'>
                <option value='0' selected>时间选择</option>
                <option value='1' <?php if ($periodCon == 1) echo "selected" ?>>最近7天</option>
                <option value='2' <?php if ($periodCon == 2) echo "selected" ?>>最近15天</option>
                <option value='3' <?php if ($periodCon == 3) echo "selected" ?>>最近30天</option>
                <option value='4' <?php if ($periodCon == 4) echo "selected" ?>>30天前</option>
            </select>
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
                $f=anmaIn("report_produce_sample.xls",$SinkOrder,$motherSTR);
                echo "<a href=\"../admin/openorload.php?d=$d&f=$f&Type=&Action=6\" target='download' style='color: #169BD5;margin: 0 5px;'>下载模板</a>";
            ?>
        </td>
    </tr>
</table>
<?php
include "../report/subprogram/read_model_3.php";

//检索条件隐藏值
echo " <input type='hidden' name='WorkShop' id='WorkShop' value='$WorkShop' />";
echo " <input type='hidden' name='WorkGroup' id='WorkGroup' value='$WorkGroup' />";
echo " <input type='hidden' name='Confirm' id='Confirm' value='$Confirm' />";
echo " <input type='hidden' name='periodCon' id='periodCon' value='$periodCon' />";

//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);

$mySql = "select Id, WorkDate, WorkShop, WorkGroup, PlanCube, FinishedCube, AttainmentRate, WorkHours, WorkerNum, DPCEffy, PCHourlyEffy, CauseAnalysis, confirm
from $DataIn.rp_produce
WHERE 1=1 $SearchRows $FilterSql 
GROUP BY Id  ORDER BY Id ";
//echo $mySql;

$DefaultBgColor=$theDefaultColor;
if($myResult = mysql_query($mySql.$PageSTR,$link_id)){
    while($myRow = mysql_fetch_array($myResult)){
        $m=1;
        $WorkDate = $myRow['WorkDate'];
        $WorkShop = $myRow['WorkShop'];
        $WorkGroup = $myRow['WorkGroup'];
        $PlanCube = $myRow['PlanCube'];
        $FinishedCube = $myRow['FinishedCube'];
        $AttainmentRate = $myRow['AttainmentRate'];
        $WorkHours = $myRow['WorkHours'];
        $WorkerNum = $myRow['WorkerNum'];
		$DPCEffy = $myRow['DPCEffy'];
        $PCHourlyEffy = $myRow['PCHourlyEffy'];
        $CauseAnalysis = $myRow['CauseAnalysis'];
        $confirm = $myRow['confirm']==1?'<span style="color:#009900">已确定</span>':'<span style="color:#FF6633">未确定</span>';
        $ValueArray=array(
            array(0=>$WorkDate,1=>"align='center'"),
            array(0=>$WorkShop,1=>"align='center'"),
            array(0=>$WorkGroup,1=>"align='center'"),
            array(0=>$PlanCube,1=>"align='center'"),
            array(0=>$FinishedCube,1=>"align='center'"),
            array(0=>$AttainmentRate,1=>"align='center'"),
            array(0=>$WorkHours,1=>"align='center'"),
            array(0=>$WorkerNum,1=>"align='center'"),
            array(0=>$DPCEffy,1=>"align='center'"),
            array(0=>$PCHourlyEffy,1=>"align='center'"),
            array(0=>$CauseAnalysis,1=>"align='center'"),
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
        document.form1.action="report_produce_dely_add.php";
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
        document.form1.action="report_produce_dely_export.php?Ids=" + Ids;
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
        document.form1.action="report_produce_del.php?Ids=" + Ids;
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
        document.form1.action="report_produce_confirm.php?Ids=" + Ids;
        document.form1.submit();
    }
</script>
