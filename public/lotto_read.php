<?php 
//电信-EWEN
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=12;             
$tableMenuS=400;
ChangeWtitle("$SubCompany lotto码列表");
$funFrom="lotto";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|40|序号|40|客户名称|100|客户Id|100|lotto码|120|添加日期|150|操作人|80";
$Pagination=$Pagination==""?1:$Pagination;
$Page_Size = 200;
$ActioToS="2,3,4,7,8";
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项

echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
    $CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$Darray=array (0=>"日",1=>"一",2=>'二',3=>"三",4=>"四",5=>"五",6=>"六");
$mySql="SELECT A.Id, B.Forshort,A.lotto,A.date,C.Name,A.companyId FROM $DataIn.ch_initallotto A
        INNER JOIN $DataIn.trade_object B On A.companyId = B.CompanyId
        INNER JOIN $DataIn.staffmain C On C.Number = A.operator";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
    do{
        $m=1;
        $Id = $myRow['Id'];
        $CompanyId = $myRow['companyId'];
        $Forshort = $myRow['Forshort'];
        $lotto = $myRow['lotto'];
        $date = $myRow['date'];
        $Operator = $myRow['Name'];
        $ValueArray=array(
            array(0=>$Forshort,1=>"align='center'"),
            array(0=>$CompanyId, 1=>"align='center'"),
            array(0=>$lotto,1=>"align='center'"),
            array(0=>$date,1=>"align='center'"),
            array(0=>$Operator,1=>"align='center'")
            );
        $checkidValue=$Id;
        include "../model/subprogram/read_model_6.php";
        }while ($myRow = mysql_fetch_array($myResult));
    }
else{
    noRowInfo($tableWidth);
    }
//步骤7：
echo '</div>';
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>