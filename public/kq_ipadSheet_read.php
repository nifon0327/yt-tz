<?php 
//电信-joseph
//代码共享、数据库共享-EWEN 2012-08-14
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=10; 
$tableMenuS=600;
ChangeWtitle("$SubCompany 考勤ipad记录表");
$funFrom="kq_ipadSheet";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|50|序号|50|名称|100|考勤楼层|100|公司|80|iPad识别码|350|";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,3,4";
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
$mySql="SELECT A.Id,A.Name,A.Identifier,A.Floor, A.cSign
     FROM $DataPublic.attendanceipadsheet A ";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myResult &&$myRow = mysql_fetch_array($myResult)){
$SharingShow="Y";//显示共享
    do{
        $m=1;
        $Id=$myRow["Id"];
        $Name=$myRow["Name"];
        $ipadId = $myRow["Identifier"];
        $Floor = $myRow["Floor"];
        $cSign = $myRow["cSign"]=="7"?"包装":"皮套";
        $ValueArray=array(
            array(0=>$Name,1=>"align='center'"),
            array(0=>$Floor,1=>"align='center'"),
            array(0=>$cSign,1=>"align='center'"),
            array(0=>$ipadId,1=>"align='center'")
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
if($myResult)$RecordToTal= mysql_num_rows($myResult);
else $RecordToTal=0;
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>