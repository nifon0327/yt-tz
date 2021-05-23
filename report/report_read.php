<?php
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=13;
$tableMenuS=500;
ChangeWtitle("$SubCompany 报表");
$funFrom="report";

$sumCols="5,6,7,8,9,10";//合计列数
$Th_Col='选项|60|序号|60|项目编号|100|项目名称|100|构件类型|60|构件总层数|60|总方量（m³）|100|完成方量|100|发货放量|100|生产层数|60|发货层数|60|最后更新时间|120|确认|60';
//分页
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 20;							//每页默认记录数量
$nowWebPage=$funFrom."_read";
$rightShow=true;//显示系统右键菜单
include "../model/subprogram/read_model_3.php";

//全选
echo "<input type='button' name='Submit' value='全选' onclick=\"All_elects('$theDefaultColor', '$thePointerColor', '$theMarkColor', $ColsNumber)\">";
echo "&nbsp;&nbsp;<input type='button' name='Submit' value='反选' onclick=\"Instead_elects('$theDefaultColor', '$theDefaultColor', '$theMarkColor', $ColsNumber)\">";
//项目名称
$tradeSql= mysql_query("select a.Letter, a.Forshort, b.TradeNo, a.CompanyId
            from $DataIn.trade_object a
            INNER join $DataIn.trade_info b on a.id = b.TradeId
            where a.ObjectSign = 2 order by a.Letter",$link_id);
if($tradeRow = mysql_fetch_array($tradeSql)){
    echo "&nbsp;&nbsp;<select name='TradeNo' id='TradeNo' onchange='FormSubmit()'>";
    echo "<option value='' selected>项目名称</option>";
    do{
        $Letter=$tradeRow["Letter"];
        $Forshort=$tradeRow["Forshort"];
        $Forshort=$Letter.'-'.$Forshort;
        $ThisTradeNo=$tradeRow["CompanyId"];

        if($TradeNo==$ThisTradeNo){
            echo"<option value='$ThisTradeNo' selected>$Forshort</option>";
            $SearchRows.=" and o.CompanyId = '$ThisTradeNo' ";
        }
        else{
            echo"<option value='$ThisTradeNo'>$Forshort</option>";
        }
    }while ($tradeRow = mysql_fetch_array($tradeSql));
    echo"</select>";
}
//是否确定
echo "&nbsp;&nbsp;<select name='Confirm' id='Confirm' onchange='FormSubmit()'>";
echo "<option value='' selected>是否确定</option>";
if($Confirm==='1'){
    echo "<option value='0'>未确定</option>";
    echo "<option value='1' selected>已确定</option>";
    $SearchRows .= " and c.confirm=1";
}else if($Confirm==='0'){
    echo "<option value='0' selected>未确定</option>";
    echo "<option value='1'>已确定</option>";
    $SearchRows .= " and c.confirm=0";
}else{
    echo "<option value='0'>未确定</option>";
    echo "<option value='1'>已确定</option>";
}
echo"</select>";
echo "&nbsp;&nbsp;<input type='button' name='Submit' value='删除' onclick='DeleteUd()'>";
echo "&nbsp;&nbsp;<input type='button' name='Submit' value='确定' onClick='ConfirmUd()'>";
echo "&nbsp;&nbsp;<input type='button' name='Submit' value='导入数据' onClick='ToImportPrice()'>";
echo "&nbsp;&nbsp;<input type='button' name='Submit' value='导出数据' onClick='ToExportPrice()'>";
$d=anmaIn("report/phpExcelReader/",$SinkOrder,$motherSTR);
$f=anmaIn("report_sample.xls",$SinkOrder,$motherSTR);
echo "&nbsp;&nbsp;<a href=\"../admin/openorload.php?d=$d&f=$f&Type=&Action=6\"target=\"download\">下载模板</a>";

//步骤5：
//$helpFile=1;
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);

//$dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(NOW(),1) AS CurWeek",$link_id));
//$curWeeks=$dateResult["CurWeek"];

$mySql = "select c.Id, i.TradeNo, o.Forshort,c.CmptType,c.CmptFloors,c.TotalCube,c.FinishedCube,c.DeliveredCube,
c.BuildFloors,c.DeliveredFloors,c.modified,c.confirm
from $DataIn.rep_cube c 
left join $DataIn.trade_info i on c.TradeId = i.TradeId
left join $DataIn.trade_object o on o.Id=c.TradeId
WHERE 1=1 $SearchRows 
GROUP BY c.Id  ORDER BY c.Id ";
//echo $mySql;

//$tempStuffId="";
$DefaultBgColor=$theDefaultColor;
if($myResult = mysql_query($mySql.$PageSTR,$link_id)){
    while($myRow = mysql_fetch_array($myResult)){
        $m=1;
        $TradeNo = $myRow['TradeNo'];
        $Forshort = $myRow['Forshort'];
        $CmptType = $myRow['CmptType'];
        $CmptFloors = $myRow['CmptFloors'];
        $TotalCube = $myRow['TotalCube'];
        $FinishedCube = $myRow['FinishedCube'];
        $DeliveredCube = $myRow['DeliveredCube'];
        $BuildFloors = $myRow['BuildFloors'];
        $DeliveredFloors = $myRow['DeliveredFloors'];
        $modified = $myRow['modified'];
        $confirm = $myRow['confirm']==1?'<span style="color:#009900">已确定</span>':'<span style="color:#FF6633">未确定</span>';
        $ValueArray=array(
            array(0=>$TradeNo,1=>"align='center'"),
            array(0=>$Forshort,1=>"align='center'"),
            array(0=>$CmptType,1=>"align='center'"),
            array(0=>$CmptFloors,1=>"align='center'"),
            array(0=>$TotalCube,1=>"align='center'"),
            array(0=>$FinishedCube,1=>"align='center'"),
            array(0=>$DeliveredCube,1=>"align='center'"),
            array(0=>$BuildFloors,1=>"align='center'"),
            array(0=>$DeliveredFloors,1=>"align='center'"),
            array(0=>$modified,1=>"align='center'"),
            array(0=>$confirm,1=>$confirmColor." align='center'")
        );
        $checkidValue = $myRow['Id'];
        include "../model/subprogram/read_model_6.php";
    }
}else{
    noRowInfo($tableWidth);
}
//步骤7：
List_Title($Th_Col,"0",0);
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
echo"<DIV id='TotalStatusBar' style='display:none;'>&nbsp;</div>";//总计行
?>
<script>
    //导入报表
    function ToImportPrice() {
        document.form1.action="report_dely_add.php";
        document.form1.target = "_self";
        document.form1.submit();
    }
    //导出报表
    function ToExportPrice() {

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
        document.form1.action="report_dely_export.php?Ids=" + Ids;
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
        document.form1.action="report_del.php?Ids=" + Ids;
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
        document.form1.action="report_confirm.php?Ids=" + Ids;
        document.form1.submit();
    }
    //
    function FormSubmit(){
        jQuery('#Page').val('1');
        document.form1.submit();
    }
</script>
