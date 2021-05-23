<style type="text/css">
<!--
.moveLtoR{ filter:revealTrans(Transition=6,Duration=0.3)};
.moveRtoL{ filter:revealTrans(Transition=7,Duration=0.3)};
/* 为 DIV 加阴影 */
.out {position:relative;background:#006633;margin:10px auto;width:300px;}
.in {background:#FFFFE6;border:1px solid #555;padding:10px 5px;position:relative;top:-5px;left:-5px;}
/* 为 图片 加阴影 */
.imgShadow {position:relative;     background:#bbb;      margin:10px auto;     width:220px; }
.imgContainer {position:relative;      top:-5px;     left:-5px;     background:#fff;      border:1px solid #555;     padding:0; }
.imgContainer img {     display:block; }
.glow1 { filter:glow(color=#FF0000,strengh=2)}
-->
</style>

<?php
//mc 内部文件:OK ewen2013-08-03
include "../model/modelhead.php";
echo"<SCRIPT src='../model/js/kq_checkio.js' type=text/javascript></script>";

$From=$From==""?"read":$From;
$Pagination=$Pagination==""?0:$Pagination;
//需处理参数
$ColsNumber=9;
$tableMenuS=500;
ChangeWtitle("$SubCompany 内部-原始考勤记录");
$funFrom="kq_checkio";
$nowWebPage=$funFrom."_read";
$Th_Col="日期|70|星 期|70|部门|50|小组|50|职位|50|员工姓名|60|选项|40|序号|40|考勤楼层|60|设定时间|60|出勤时间|60|出勤状态|80|异常状态|70|修改意见|260|来源|40|审核|40";
$Page_Size = 100;
$ActioToS="1,4,7,8,12,17";
$CheckDate=$CheckDate==""?date("Y-m-d"):$CheckDate;//默认日期
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
    $SearchRows ="";
    echo"<input name='CheckDate' type='text' id='CheckDate' size='10' maxlength='10' value='$CheckDate' onchange='ResetPage(this.name)' >&nbsp;";
    $SearchRows.="and (
    (C.CheckTime LIKE '$CheckDate%' and C.KrSign='0')  
    OR 
    (DATE_SUB(C.CheckTime,INTERVAL 1 DAY) LIKE '$CheckDate%' and C.KrSign='1')
    )";
    $showTypeFlag = $showTypeFlag==''?'0':$showTypeFlag;
    $selectTypeFlag = 'selectType'.$showTypeFlag;
    $$selectTypeFlag = 'selected';
    echo"<select name='showTypeFlag' id='showTypeFlag' onchange='ResetPage(this.name)'>
             <option value='0' $selectType0>非固定薪</option>
             <option value='1' $selectType1>固定薪</option>";
    echo "</select>&nbsp;";
    if($showTypeFlag=='0'){
        $SearchRows.=" AND (M.BranchId IN ( 6, 7, 8 ) OR M.JobId IN ( 10, 38 )) ";
    }
    else{
        $SearchRows.=" AND (M.BranchId NOT IN ( 6, 7, 8 ) AND M.JobId NOT IN ( 10, 38 )) ";
    }

    $FormalSign=$FormalSign==""?0:$FormalSign;
        $selStr="selFlag" . $FormalSign;
        $$selStr="selected";
        echo"<select name='FormalSign' id='FormalSign' onchange='ResetPage(this.name)'>
             <option value='0' $selFlag0>全部</option>
             <option value='1' $selFlag1>考勤有效</option>
             <option value='2' $selFlag2>考勤参考</option>";
        echo "</select>&nbsp;";
        if($FormalSign>0){$SearchRows.=" AND M.kqSign='$FormalSign'";}

        $attendaceFloor=$attendaceFloor==""?'-1':$attendaceFloor;
        $attendanceFloorSql = mysql_query("SELECT Id, Name From $DataPublic.attendanceipadsheet WHERE cSign = 7 and Estate = 1
                                           UNION Select -1 as Id, '全部' as Name
                                           UNION Select 0 as Id, '人事' as Name
                                           Order by Id");
        echo"<select name='attendaceFloor' id='attendaceFloor' onchange='ResetPage(this.name)'>";
        while($attendaceFloorRow = mysql_fetch_assoc($attendanceFloorSql)){
            $floorId = $attendaceFloorRow['Id'];
            $floorName = $attendaceFloorRow['Name'];
            if($attendaceFloorRow['Id'] === $attendaceFloor){
                echo "<option value='$floorId' selected>$floorName</iption>";
            }
            else{
                echo "<option value='$floorId'>$floorName</iption>";
            }
        }
        echo "</select>&nbsp;";
        if($attendaceFloor != '-1'){$SearchRows.=" AND C.dFromId='$attendaceFloor'";}

        //选择部门
        $SelectFrom = 1;
        $SelectTB = 'M';
        include "../model/subselect/BranchId.php";

    }
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
    $CencalSstr";
    echo "<a href='Kq_change.php' target='_blank'  title=''><font color='red'>考勤调动</font></a>";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理


echo"<div id='Jp' style='position:absolute; left:341px; top:229px; width:250px; height:50px;z-index:1;visibility:hidden;' tabIndex=0><input name='ActionTableId' type='hidden' id='ActionTableId'><input name='ActionRowId' type='hidden' id='ActionRowId'><input name='ActionColId' type='hidden' id='ActionColId'><input name='ObjId' type='hidden' id='ObjId'><input name='A_Id' type='hidden' id='A_Id'>
        <div class='out'>
            <div class='in' id='infoShow'>
            </div>
        </div>
    </div>";



$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$Darray=array (0=>"日",1=>"一",2=>'二',3=>"三",4=>"四",5=>"五",6=>"六");
$mySql="SELECT C.Id,C.CheckTime,C.CheckType,C.dFrom,C.Estate,C.Locks,C.ZLSign,C.KrSign,C.Number,M.Name,M.BranchId,M.JobId,G.GroupName,A.Floor       FROM d7check.fakecheckinout C 
        LEFT JOIN $DataPublic.staffmain M ON M.Number=C.NUMBER 
        LEFT JOIN $DataIn.staffgroup G ON G.GroupId=M.GroupId
        Left Join $DataPublic.attendance_floor A On A.Id = M.AttendanceFloor
        WHERE 1 $SearchRows
        ORDER BY  M.BranchId,M.GroupId,M.Number,C.CheckTime";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
    $tbDefalut=0;
    $midDefault="";
    do{
        $m=1;
        $Id=$myRow["Id"];
        $CheckTime=$myRow["CheckTime"];                                             //签卡时间
        $Name=$myRow["Name"];                                                       //员工姓名
        $Number=$myRow["Number"];                                                   //员工Id
        //echo $Number;
        $BranchId=$myRow["BranchId"];
        $GroupName=$myRow["GroupName"];
        $CheckType=$myRow["CheckType"];
        $KrSign=$myRow["KrSign"];
        $Floor = $myRow["Floor"];

        $dFrom = $myRow["dFrom"];
        if($dFrom != 1)
        {
            $isFromIpadSql = "Select * From $DataPublic.attendanceipadsheet Where Name = '$dFrom' and Estate = '1'";
            $isFromIpadResult = mysql_query($isFromIpadSql);
            $ipadFloorRow = mysql_fetch_assoc($isFromIpadResult);
            $ipadFloor = $ipadFloorRow["Floor"];
            if($Floor != $ipadFloor)
            {
                $dFrom = "<span class='redB'>$dFrom</span>";
            }
        }
        else
        {
            $dFrom = "人事";
        }

        //如果请假期间提前上班，则显示红色名字 add by zx 2014-01-21
        $B_Result = mysql_fetch_array(mysql_query("SELECT Number FROM $DataPublic.kqqjsheet where 1 and StartDate<='$CheckTime' and EndDate>='$CheckTime' and Number='$Number' LIMIT 1",$link_id));
        //echo "SELECT Number FROM $DataPublic.kqqjsheet where 1 and SUBSTR(StartDate,1,10)<=SUBSTR('$CheckTime',1,10) and SUBSTR(EndDate,1,10)>=SUBSTR('$CheckTime',1,10) and Number=$Number LIMIT 1";
        $H_W_Number=$B_Result["Number"];
        if($H_W_Number!=""){
            $Name="<div class='redB' title='核查下是否有请假！'>$Name</div>";
        }

        $tmpEstate=$myRow["Estate"];  //用于是否可更改时间
        $Estate=$myRow["Estate"]==0?"<div class='greenB'>已核</div>":"<div class='redB'>未核</div>";
        $Locks=$myRow["Locks"];
        $B_sql =mysql_query("SELECT Name FROM $DataPublic.branchdata where 1 and Id=$BranchId LIMIT 1",$link_id);
        if ($B_sql )
        {
            $B_Result = mysql_fetch_array($B_sql);
            $Branch=$B_Result["Name"];
            $JobId=$myRow["JobId"];
        }
        else{
                $Branch="&nbsp;";
        }
        $J_sql=mysql_query("SELECT Name FROM $DataPublic.jobdata where 1 and Id=$JobId LIMIT 1",$link_id);
        if ($J_sql){
                $J_Result = mysql_fetch_array($J_sql);
                  $Job=$J_Result["Name"];
        }
         else{
              $Job="&nbsp;";
         }
        include "../public/kqcode/checkio_model_week.php";
        $UnusualInfo="&nbsp";$UpdateInfo="&nbsp";       //提示信息每行均初始化
        if($midDefault!=$Number.$ToDay){                        //不同员工或日期换行，重新初始化
            $ChickIn="";$ChickOut="";
            include "../public/kqcode/checkio_model_pb.php";
            $pbTypeSTR=$pbType==1?"临":"";
            }
        $Kr_bgColor=$KrSign==0?"":"class='greenB'";
        include "../public/kqcode/checkio_model_".$CheckType.".php";

        //***************************
            if($Locks==0){//锁定状态
                if($Keys & mLOCK){
                    $Choose="<input name='checkid[]' type='checkbox' id='checkid$i' value='$Id' disabled><img src='../images/lock.png' alt='此记录已锁定操作!' width='15' height='15'>";
                    }
                else{
                    $Choose="&nbsp;<img src='../images/lock.png' alt='此记录已锁定操作!' width='15' height='15'>";}
                }
            else{       //非锁定状态，分权限
                if(($Keys & mUPDATE)||($Keys & mDELETE)||($Keys & mLOCK)){//有更新、删除、锁定权限
                    $Choose="<input name='checkid[]' type='checkbox' id='checkid$i' value='$Id' disabled><img src='../images/unlock.png' alt='此记录未锁定操作!' width='15' height='15'>";
                    }
                else{//无权限
                    $Choose="<input name='checkid[]' type='checkbox' id='checkid$i' value='$Id' disabled><img src='../images/unlock.png' alt='此记录未锁定操作!' width='15' height='15'>";//不可选
                    }
                }
        if($midDefault==""){//首行
            //并行列
            $k=1;
            //echo "k1:$k";
            echo"<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'><tr>";
            echo"<td scope='col' class='A0111' width='$Field[$m]' align='center'>$ToDay</td>";//签到日期
            $unitWidth=$tableWidth-$Field[$m];
            $m=$m+2;
            echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$weekInfo</td>";//结付日期
            $unitWidth=$unitWidth-$Field[$m];
            $m=$m+2;
            echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Branch</td>";     //部门
            $unitWidth=$unitWidth-$Field[$m];
            $m=$m+2;
            echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$GroupName</td>";  //小组
            $unitWidth=$unitWidth-$Field[$m];
            $m=$m+2;
            echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Job</td>";        //职位
            $unitWidth=$unitWidth-$Field[$m];
            $m=$m+2;
            echo"<td width='$Field[$m]' class='A0101' align='center'>$Name</td>";       //员工姓名
            $unitWidth=$unitWidth-$Field[$m];
            $m=$m+2;
            //并行宽
            //echo"<td width='$unitWidth' class='A0101'>";
            echo"<td width='' class='A0101'>";
            $midDefault=$Number.$ToDay;
            }
        if($midDefault!="" && $midDefault==$Number.$ToDay){//同属于一个主Id，则依然输出明细表格

            $m=13;
            echo"<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
            echo"<tr onmousedown='ClickKeyCheck(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' 
                onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
                onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
            $unitFirst=$Field[$m]-1;
            echo"<td class='A0001' width='$unitFirst' height='20' align='right' $tdBGCOLOR>$Choose&nbsp;</td>";//选项
            $m=$m+2;
            echo"<td class='A0001' width='$Field[$m]'><div align='center'>$i</div></td>";//序号

            //考勤楼层
            $m=$m+2;
            echo"<td class='A0001' width='$Field[$m]' align='center'>$Floor</td>";//

            $m=$m+2;
            echo"<td class='A0001' width='$Field[$m]' align='center'>$dSetTime $pbTypeSTR</td>";//设定时间
            $m=$m+2;

            $NowC=3;

            $NowR=0;
            //echo "NowR:$NowR";
            $tempstr="$Name-$ioTime";
            if ($tmpEstate==0)
            {
                echo"<td class='A0001' width='$Field[$m]' align='center' ><div align='center' $Kr_bgColor>$ioTime</div></td>";//出勤时间
            }
            else{
                echo"<td class='A0001' width='$Field[$m]' align='center'  onmousedown='window.event.cancelBubble=true;' onclick='updateJq($i,$NowC,$NowR,$Id,\"$tempstr\",18)' ><div align='center' $Kr_bgColor>$ioTime</div></td>";//出勤时间
            }

            $m=$m+2;
            if($Locks==1){
                echo"<td class='A0001' width='$Field[$m]' id='text$i' onmousedown='window.event.cancelBubble=true;' 
                onclick='updateTHIS(this.id,\"CheckType\",$Id,\"$CheckType\")' 
                onDblClick='backTHIS(this.id,\"CheckType\",$Id,\"$CheckType\")'>    
                <input type='hidden' name='hField$i' value='$Id'>$CHECKSIGN</td>";
                }
            else{
                echo"<td class='A0001' width='$Field[$m]' align='center'>$CHECKSIGN</td>";//出勤状态
                }
            $m=$m+2;
            echo"<td class='A0001' width='$Field[$m]' align='center'>$UnusualInfo</td>";//异常状态
            $m=$m+2;
            echo"<td  class='A0001' width='$Field[$m]'>$UpdateInfo</td>";//修改意见
            $m=$m+2;
            echo"<td  class='A0001' width='$Field[$m]' align='center'>$dFrom</td>";//来源
            $m=$m+2;
            echo"<td  class='A0001' width='' align='center'>$Estate</td>";//审核状态
            echo"</tr></table>";
            $k++;
            $i++;
            }
        else{
            //新行开始
            $k=1;
            //echo "k3:$k";
            echo"</td></tr></table>";//结束上一个表格
            ////该员工首记录初始化
            //并行列
            echo"<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'><tr>";
            echo"<td scope='col' class='A0111' width='$Field[$m]' align='center'>$ToDay</td>";//签到日期
            $unitWidth=$tableWidth-$Field[$m];
            $m=$m+2;
            echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$weekInfo</td>";//结付日期
            $unitWidth=$unitWidth-$Field[$m];
            $m=$m+2;
            echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Branch</td>";     //部门
            $unitWidth=$unitWidth-$Field[$m];
            $m=$m+2;
            echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$GroupName</td>";  //小组
            $unitWidth=$unitWidth-$Field[$m];
            $m=$m+2;
            echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Job</td>";        //职位
            $unitWidth=$unitWidth-$Field[$m];
            $m=$m+2;
            echo"<td width='$Field[$m]' class='A0101' align='center'>$Name</td>";       //员工姓名
            $unitWidth=$unitWidth-$Field[$m];
            $m=$m+2;
            //并行宽
            //echo"<td width='$unitWidth' class='A0101'>";
            echo"<td width='' class='A0101'>";
            $midDefault=$Number.$ToDay;
            echo"<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
            echo"<tr onmousedown='ClickKeyCheck(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' 
                onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
                onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
            $unitFirst=$Field[$m]-1;
            echo"<td class='A0001' width='$unitFirst' height='20' align='right' $tdBGCOLOR>$Choose&nbsp;</td>";//选项
            $m=$m+2;
            echo"<td class='A0001' width='$Field[$m]'><div align='center'>$i</div></td>";//序号

            //考勤楼层
            $m=$m+2;
            echo"<td class='A0001' width='$Field[$m]' align='center'>$Floor</td>";//

            $m=$m+2;
            echo"<td class='A0001' width='$Field[$m]' align='center'>$dSetTime $pbTypeSTR</td>";//设定时间
            $m=$m+2;

            $NowC=3;
            $NowR=$k-1;  //相当是0
            //echo "NowR:$NowR";
            $tempstr="$Name-$ioTime";
            if ($tmpEstate==0)
            {
                echo"<td class='A0001' width='$Field[$m]' align='center' ><div align='center' $Kr_bgColor>$ioTime</div></td>";//出勤时间
            }
            else{
                echo"<td class='A0001' width='$Field[$m]' align='center'  onmousedown='window.event.cancelBubble=true;' onclick='updateJq($i,$NowC,$NowR,$Id,\"$tempstr\",18)' ><div align='center' $Kr_bgColor>$ioTime</div></td>";//出勤时间
            }
            $m=$m+2;
            if($Locks==1){
                echo"<td class='A0001' width='$Field[$m]' id='text$i' onmousedown='window.event.cancelBubble=true;'     
                onclick='updateTHIS(this.id,\"CheckType\",$Id,\"$CheckType\")' 
                onDblClick='backTHIS(this.id,\"CheckType\",$Id,\"$CheckType\")'>    
                <input type='hidden' name='hField$i' value='$Id'>$CHECKSIGN</td>";
                }
            else{
                echo"<td class='A0001' width='$Field[$m]' align='center'>$CHECKSIGN</td>";//出勤状态
                }
            $m=$m+2;
            echo"<td class='A0001' width='$Field[$m]' align='center'>$UnusualInfo</td>";//异常状态
            $m=$m+2;
            echo"<td  class='A0001' width='$Field[$m]'>$UpdateInfo</td>";//修改意见
            $m=$m+2;
            echo"<td  class='A0001' width='$Field[$m]' align='center'>$dFrom</td>";//来源
            $m=$m+2;
            echo"<td  class='A0001' width='' align='center'>$Estate</td>";//审核状态
            echo"</tr></table>";
            $k++;
            $i++;
            }
        }while($myRow = mysql_fetch_array($myResult));
    echo"</tr></table>";
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
<script  src='../model/IE_FOX_MASK.js' type=text/javascript></script>
<script  type=text/javascript>
function webpage_read(){
    }
function AutoInducts(WebPage,From){//自动导入数据
    if (From!=""){
        From="From="+From;}
    location.href="../admin/"+WebPage+".php?"+From;

    }
</script>

<script language="JavaScript" type="text/JavaScript">

function updateJq(TableId,ColId,RowId,CheckId,runningNum,toObj){//行即表格序号;列，流水号，更新源
    //CloseDiv();
    showMaskBack();  // add by zx 加入庶影   20110323  IE_FOX_MASK.js
    var InfoSTR="";
    var buttonSTR="";
    var theDiv=document.getElementById("Jp");
    theDiv.style.visibility="hidden";
    var ObjId=document.form1.ObjId.value;
    var tempTableId=document.form1.ActionTableId.value;
    theDiv.style.top=event.clientY + document.body.scrollTop+'px';
    theDiv.style.left=event.clientX + document.body.scrollLeft;//-parseInt(theDiv.style.width)+'px';
    if(theDiv.style.visibility=="hidden" || toObj!=ObjId || TableId!=tempTableId){
        document.form1.ActionTableId.value=TableId;
        document.form1.ActionColId.value=ColId;
        document.form1.ActionRowId.value=RowId;
        document.form1.A_Id.value=CheckId;
        document.form1.ObjId.value=toObj;
        switch(toObj){
            case 1: //采购单交货期
                InfoSTR="更新采购流水号为<input name='runningNum' type='text' id='runningNum' value='"+runningNum+"' size='12' class='INPUT0000' readonly>的采购单交货期:<input name='DeliveryDate' type='text' id='DeliveryDate' size='10' maxlength='10' class='INPUT0100' onFocus='WdatePicker()' readonly>&nbsp;<input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='更新' onclick='aiaxUpdate()'>&nbsp;<input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='取消' onclick='CloseDiv()'><input name='ActionTableId' type='hidden' id='ActionTableId'><input name='ActionRowId' type='hidden' id='ActionRowId'>";
                break;
            case 2: //订单备注
                InfoSTR="更新订单流水号为<input name='runningNum' type='text' id='runningNum' value='"+runningNum+"' size='12' class='INPUT0000' readonly>的<br>订单备注<input name='PackRemark' type='text' id='PackRemark' size='50' class='INPUT0100'><br>";
                break;
            case 3://出货方式
                InfoSTR="更新订单流水号为<input name='runningNum' type='text' id='runningNum' value='"+runningNum+"' size='12' class='INPUT0000' readonly>的<br>出货方式<input name='ShipType' type='text' id='ShipType' size='50' class='INPUT0100'><br>";
                break;
            case 4://出货时间
                InfoSTR="更新订单流水号为<input name='runningNum' type='text' id='runningNum' value='"+runningNum+"' size='12' class='INPUT0000' readonly>的出货时间<input name='DeliveryDate' type='text' id='DeliveryDate' size='10' maxlength='10' class='INPUT0100' onFocus='WdatePicker()' readonly>";
                break;
            case 5://采购备注
                InfoSTR="更新订单流水号为<input name='runningNum' type='text' id='runningNum' value='"+runningNum+"' size='12' class='INPUT0000' readonly>的采购备注<input name='cgRemark' type='text' id='cgRemark' size='85' class='INPUT0100'>";
                break;
            case 6:
                InfoSTR="更新订单流水号为<input name='runningNum' type='text' id='runningNum' value='"+runningNum+"' size='12' class='INPUT0000' readonly>的生管备注<input name='sgRemark' type='text' id='sgRemark' size='85' class='INPUT0100'>";
                break;
            /*
            case 14://新出货时间  //add my zx 20100416
                InfoSTR="更新订单流水号为<input name='runningNum' type='text' id='runningNum' value='"+runningNum+"' size='12' class='INPUT0000' readonly>的出货时间<input name='DeliveryDate' type='text' id='DeliveryDate' size='10' maxlength='10' class='INPUT0100' onFocus='WdatePicker()' readonly><br>未能及时出货原因（英文）<input name='UnDeliveryReson' type='text' id='UnDeliveryReson' size='50' class='INPUT0100'>";
                break;
            */
            case 14://新出货时间  //add my zx 20100416
                InfoSTR="更新订单流水号为<input name='runningNum' type='text' id='runningNum' value='"+runningNum+"' size='12' class='INPUT0000' readonly>的预定出货时间<input name='DeliveryDate' type='text' id='DeliveryDate' size='10' maxlength='10' class='INPUT0100' onFocus='WdatePicker()' readonly>";
                break;
            case 141://新出货时间  //add my zx 20100416
                InfoSTR="更新订单流水号为<input name='runningNum' type='text' id='runningNum' value='"+runningNum+"' size='12' class='INPUT0000' readonly>的出货时间<input name='DeliveryDateR' type='text' id='DeliveryDateR' size='10' maxlength='10' class='INPUT0100' onFocus='WdatePicker()' readonly>";
                break;
            case 15://新出货时间  //add my zx 20100416
                InfoSTR="更新订单流水号为<input name='runningNum' type='text' id='runningNum' value='"+runningNum+"' size='12' class='INPUT0000' readonly><br>未能及时出货原因（英文）<input name='UnDeliveryReson' type='text' id='UnDeliveryReson' size='50' class='INPUT0100'>";
                break;

            case 16://更新价格  //add my zx 20100511
                InfoSTR="更新订单流水号为<input name='runningNum' type='text' id='runningNum' value='"+runningNum+"' size='12' class='INPUT0000' readonly>的price<input name='price' type='text' id='price' size='20' class='INPUT0100'>";
                break;
            case 17://更新价格  //add my zx 20100511
                InfoSTR="产品ID-配件ID<input name='runningNum' type='text' id='runningNum' value='"+runningNum+"' size='12' class='INPUT0000' readonly>的对应数量<input name='Relation' type='text' id='Relation' size='20' class='INPUT0100'>";
                break;
            case 18://更新价格  //add my zx 20100511
                InfoSTR="姓名--时间：<input name='runningNum' type='text' id='runningNum' value='"+runningNum+"' size='12' class='INPUT0000' readonly><br>新出勤时间：<input name='NewTime' type='text' id='NewTime' size='12' class='INPUT0100'>";
                break;

            }
        if(toObj>1){
            var buttonSTR="&nbsp;<div align='right'><input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='更新' onclick='aiaxUpdate()'>&nbsp;<input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='取消' onclick='CloseDiv()'>";
            }
        infoShow.innerHTML=InfoSTR+buttonSTR;
        theDiv.className="moveRtoL";
        if (isIe()) {  //只有IE才能用   add by zx 加入庶影   20110323  IE_FOX_MASK.js
            theDiv.filters.revealTrans.apply();//防止错误
            theDiv.filters.revealTrans.play(); //播放
        }
        else{
            theDiv.style.opacity=0.9;
        }
        theDiv.style.visibility = "";
        theDiv.style.display="";
        }
    }

function CloseDiv(){
    var theDiv=document.getElementById("Jp");
    theDiv.className="moveLtoR";
    if (isIe()) {  //只有IE才能用 add by zx 加入庶影   20110323  IE_FOX_MASK.js
        theDiv.filters.revealTrans.apply();
        //theDiv.style.visibility = "hidden";
        theDiv.filters.revealTrans.play();
    }
    theDiv.style.visibility = "hidden";
    //theDiv.filters.revealTrans.play();
    infoShow.innerHTML="";
    closeMaskBack();    //add by zx 关闭庶影   20110323   add by zx 加入庶影   20110323  IE_FOX_MASK.js
    }

function aiaxUpdate(){
    var ObjId=document.form1.ObjId.value;
    var tempTableId=document.form1.ActionTableId.value;
    var tempRowId=document.form1.ActionRowId.value;
    var tempColId=document.form1.ActionColId.value;
    var temprunningNum=document.form1.runningNum.value;
    switch(ObjId){
        case "1":       //更新采购单交货期:
            var tempDeliveryDate=document.form1.DeliveryDate.value;
            myurl="purchaseorder_updated.php?StockId="+temprunningNum+"&DeliveryDate="+tempDeliveryDate+"&ActionId=jq";
            retCode=openUrl(myurl);
            if (retCode!=-2){
                //更新成功,隐藏DIV，并且重新写该单元格的内容   或 重新动态更新需求单列表
                if(tempDeliveryDate==""){
                    tempDeliveryDate="未确定";
                    }
                eval("ListTB"+tempTableId).rows[tempRowId].cells[16].innerHTML="<span onclick='updateJq("+tempTableId+","+tempRowId+","+temprunningNum+",1)' style='CURSOR: pointer;color:#FF9966'>"+tempDeliveryDate+"</sapn>";
                CloseDiv();
                }
            break;
        case "2":       //订单说明 PackRemark
            var tempPackRemark0=document.form1.PackRemark.value;
            var tempPackRemark1=encodeURIComponent(tempPackRemark0);
            myurl="yw_order_updated.php?POrderId="+temprunningNum+"&tempPackRemark="+tempPackRemark1+"&ActionId=PackRemark";
            var ajax=InitAjax();
    　       ajax.open("GET",myurl,true);
            ajax.onreadystatechange =function(){
    　           if(ajax.readyState==4){// && ajax.status ==200
                    eval("ListTable"+tempTableId).rows[0].cells[tempRowId].innerHTML="<DIV STYLE='width:$Field[$m] px;overflow: hidden; text-overflow:ellipsis' title='$Value0'><NOBR>&nbsp;"+tempPackRemark0+"</NOBR></DIV>";
                    CloseDiv();
                    }
                }
            ajax.send(null);
            break;
        case "3":       //出货方式
            var tempShipType0=document.form1.ShipType.value;
            var tempShipType1=encodeURIComponent(tempShipType0);
            myurl="yw_order_updated.php?POrderId="+temprunningNum+"&tempShipType="+tempShipType1+"&ActionId=ShipType";
            retCode=openUrl(myurl);
            if (retCode!=-2){
                eval("ListTable"+tempTableId).rows[0].cells[tempRowId].innerHTML="<DIV STYLE='width:$Field[$m] px;overflow: hidden; text-overflow:ellipsis' title='$Value0'><NOBR>&nbsp;"+tempShipType0+"</NOBR></DIV>";
                CloseDiv();
                }
            break;
        case "4":       //出货时间
            var tempDeliveryDate=document.form1.DeliveryDate.value;
            myurl="yw_order_updated.php?POrderId="+temprunningNum+"&DeliveryDate="+tempDeliveryDate+"&ActionId=DeliveryDate";
            retCode=openUrl(myurl);
            if (retCode!=-2){
                eval("ListTable"+tempTableId).rows[0].cells[tempRowId].innerHTML="<DIV STYLE='width:$Field[$m] px;overflow: hidden; text-overflow:ellipsis' title='$Value0'><NOBR>&nbsp;"+tempDeliveryDate+"</NOBR></DIV>";
                CloseDiv();
                }
            break;
        case "5":
            var tempcgRemark=document.form1.cgRemark.value;
            var tempcgRemark1=encodeURIComponent(tempcgRemark);//传输中文
            myurl="yw_order_updated.php?POrderId="+temprunningNum+"&cgRemark="+tempcgRemark1+"&ActionId=cgRemark";
            retCode=openUrl(myurl);
            if (retCode!=-2){
                eval("ListTable"+tempTableId).rows[0].cells[tempRowId].innerHTML="<DIV STYLE='width:$Field[$m] px;overflow: hidden; text-overflow:ellipsis' title='$Value0'><NOBR>&nbsp;"+tempcgRemark+"</NOBR></DIV>";
                CloseDiv();
                }
            break;
        case "6"://更新生管备注
            var tempsgRemark=document.form1.sgRemark.value;
            var tempsgRemark1=encodeURIComponent(tempsgRemark);//传输中文
            myurl="yw_order_updated.php?POrderId="+temprunningNum+"&sgRemark="+tempsgRemark1+"&ActionId=sgRemark";
            retCode=openUrl(myurl);
            if (retCode!=-2){
                eval("ListTable"+tempTableId).rows[0].cells[tempRowId].innerHTML="<DIV STYLE='width:$Field[$m] px;overflow: hidden; text-overflow:ellipsis' title='$Value0'><NOBR>&nbsp;"+tempsgRemark+"</NOBR></DIV>";
                CloseDiv();
                }
            break;
        /*
        case "14":      //出货时间 add by zx 20100416
            var tempDeliveryDate=document.form1.DeliveryDate.value;
            //alert(tempDeliveryDate);
            var tempUnDeliveryReson=document.form1.UnDeliveryReson.value;
            //alert(tempUnDeliveryReson);
            var tempStr="";
            var myurl="";
            tempUnDeliveryReson=strtrim(tempUnDeliveryReson);  //除掉空格

            if(tempUnDeliveryReson.length==0 && tempDeliveryDate.length>0)
            {
                myurl="yw_order_updated.php?POrderId="+temprunningNum+"&DeliveryDate="+tempDeliveryDate+"&ActionId=DeliveryDate";
                tempStr=tempDeliveryDate;
            }
            else
            {
                if(tempUnDeliveryReson.length>0){
                    myurl="yw_order_updated.php?POrderId="+temprunningNum+"&UnDeliveryReson="+tempUnDeliveryReson+"&ActionId=UnDeliveryReson";
                    tempStr=tempUnDeliveryReson;
                }

            }

            //alert(myurl);
            //alert(myurl.length);
            if (myurl.length>0)
            {
                retCode=openUrl(myurl);
                if (retCode!=-2){
                eval("ListTable"+tempTableId).rows[0].cells[tempRowId].innerHTML="<DIV STYLE='width:$Field[$m] px;overflow: hidden; text-overflow:ellipsis' title='$Value0'><NOBR>&nbsp;"+tempStr+"</NOBR></DIV>";
                CloseDiv();
                }
            }

            break;
        */

        case "14":      //出货时间 add by zx 20100416
            var tempDeliveryDate=document.form1.DeliveryDate.value;
            //alert(tempDeliveryDate);
            //var tempUnDeliveryReson=document.form1.UnDeliveryReson.value;
            //alert(tempUnDeliveryReson);
            var tempStr="";
            var myurl="";
            //tempUnDeliveryReson=strtrim(tempUnDeliveryReson);  //除掉空格
            if(tempDeliveryDate.length==0)
            {
                var message=confirm("确定没有交货日期！？");
                if (message!=true){
                    return false;
                }
                else
                {
                    tempDeliveryDate="0000-00-00";
                }
            }

            if(tempDeliveryDate.length>0)
            {
                myurl="yw_order_updated.php?POrderId="+temprunningNum+"&DeliveryDate="+tempDeliveryDate+"&ActionId=DeliveryDate";
                if(tempDeliveryDate!="0000-00-00")
                {
                    tempStr=tempDeliveryDate;
                }
                else
                {
                    tempStr="";
                }
            }
            //alert(myurl);
            //alert(myurl.length);
            if (myurl.length>0)
            {
                retCode=openUrl(myurl);
                if (retCode!=-2){
                eval("ListTable"+tempTableId).rows[0].cells[tempRowId].innerHTML="<DIV STYLE='width:$Field[$m] px;overflow: hidden; text-overflow:ellipsis' title='$Value0'><NOBR>&nbsp;"+tempStr+"</NOBR></DIV>";
                CloseDiv();
                }
            }

            break;

        case "141":     //出货时间 add by zx 20100416
            var tempDeliveryDateR=document.form1.DeliveryDateR.value;
            //alert(tempDeliveryDate);
            //var tempUnDeliveryReson=document.form1.UnDeliveryReson.value;
            //alert(tempUnDeliveryReson);
            var tempStr="";
            var myurl="";
            //tempUnDeliveryReson=strtrim(tempUnDeliveryReson);  //除掉空格
            if(tempDeliveryDateR.length==0)
            {
                var message=confirm("确定没有交货日期！？");
                if (message!=true){
                    return false;
                }
                else
                {
                    tempDeliveryDateR="0000-00-00";
                }
            }

            if(tempDeliveryDateR.length>0)
            {
                myurl="yw_order_updated.php?POrderId="+temprunningNum+"&DeliveryDateR="+tempDeliveryDateR+"&ActionId=DeliveryDateR";
                if(tempDeliveryDateR!="0000-00-00")
                {
                    tempStr=tempDeliveryDateR;
                }
                else
                {
                    tempStr="";
                }
            }
            //alert(myurl);
            //alert(myurl.length);
            if (myurl.length>0)
            {
                retCode=openUrl(myurl);
                if (retCode!=-2){
                eval("ListTable"+tempTableId).rows[0].cells[tempRowId].innerHTML="<DIV STYLE='width:$Field[$m] px;overflow: hidden; text-overflow:ellipsis' title='$Value0'><NOBR>&nbsp;"+tempStr+"</NOBR></DIV>";
                CloseDiv();
                }
            }

            break;


        case "15":      //未能及进出货原因 add by zx 20100416
            //var tempDeliveryDate=document.form1.DeliveryDate.value;
            //alert(tempDeliveryDate);
            var tempUnDeliveryReson=document.form1.UnDeliveryReson.value;
            tempUnDeliveryReson=strtrim(tempUnDeliveryReson);
            //alert(tempUnDeliveryReson);
            var tempStr="";
            var myurl="";
            //tempUnDeliveryReson=strtrim(tempUnDeliveryReson);  //除掉空格
            if(tempUnDeliveryReson.length>=0){
                myurl="yw_order_updated.php?POrderId="+temprunningNum+"&UnDeliveryReson="+tempUnDeliveryReson+"&ActionId=UnDeliveryReson";
                tempStr=tempUnDeliveryReson;
            }
            if (myurl.length>0)
            {
                retCode=openUrl(myurl);
                if (retCode!=-2){
                eval("ListTable"+tempTableId).rows[0].cells[tempRowId].innerHTML="<DIV STYLE='width:$Field[$m] px;overflow: hidden; text-overflow:ellipsis' title='$Value0'><NOBR>&nbsp;"+tempStr+"</NOBR></DIV>";
                CloseDiv();
                }
            }

            break;

        case "16":      //更新价格 add by zx 20100511
            var tempprice=document.form1.price.value;
            tempprice=strtrim(tempprice);
            //alert(tempUnDeliveryReson);
            var tempStr="";
            var myurl="";
            //tempUnDeliveryReson=strtrim(tempUnDeliveryReson);  //除掉空格
            if(IsNum(tempprice)==1){
                myurl="yw_order_updated.php?POrderId="+temprunningNum+"&price="+tempprice+"&ActionId=Price";
                tempStr=tempprice;
            }
            if (myurl.length>0)
            {
                //alert(myurl);
                retCode=openUrl(myurl);
                if (retCode!=-2){
                eval("ListTable"+tempTableId).rows[0].cells[tempRowId].innerHTML="<DIV STYLE='width:$Field[$m] px;overflow: hidden; text-overflow:ellipsis' title='$Value0'><NOBR>&nbsp;"+tempStr+"</NOBR></DIV>";
                CloseDiv();
                }
            }

            break;
        case "17":      //更新价格 add by zx 20100511
            var tempRelation=document.form1.Relation.value;
            tempRelation=strtrim(tempRelation);
            var strs=new Array(); //定义一数组
            strs=temprunningNum.split("-");
            temProductId=strs[0];
            tempStuffId=strs[1];
            //alert(tempUnDeliveryReson);
            var tempStr="";
            var myurl="";
            //tempUnDeliveryReson=strtrim(tempUnDeliveryReson);  //除掉空格
            if(IsNum(tempRelation)==1){
                myurl="pands_updated.php?ProductId="+temProductId+"&StuffId="+tempStuffId+"&Relation="+tempRelation+"&ActionId=Relation";
                tempStr=tempRelation;
            }
            if (myurl.length>0)
            {
                //alert(myurl);
                retCode=openUrl(myurl);
                if (retCode!=-2){
                eval("ListTable"+tempTableId).rows[tempColId].cells[tempRowId].innerHTML="<DIV STYLE='width:$Field[$m] px;overflow: hidden; text-overflow:ellipsis' title='$Value0'><NOBR>&nbsp;"+tempStr+"</NOBR></DIV>";

                CloseDiv();
                }
            }

            break;
        case "18":      //更新时间 add by zx 20100817
            var NewTime=document.form1.NewTime.value;
            NewTime=strtrim(NewTime);
            var checkid=document.form1.A_Id.value;

            var strs=new Array(); //定义一数组
            strs=NewTime.split(":");
            tempTime1=strs[0];
            tempTime2=strs[1];
            var isTime=0;
            //alert(NewTime+":"+tempTime1+":"+tempTime2);
            if (NewTime.length==5  &&  IsNum(tempTime1) && (tempTime1*1)<24)
            {
                if (IsNum(tempTime2) && (tempTime2*1)<60)
                {
                    isTime=1;
                }
            }
            //alert(tempUnDeliveryReson);
            var tempStr="";
            var myurl="";
            //tempUnDeliveryReson=strtrim(tempUnDeliveryReson);  //除掉空格
            if(isTime==1){
                myurl="kq_checkio_updated.php?NewTime="+NewTime+":00"+"&checkid[]="+checkid+"&ActionId=UpdateTime";
                //myurl="test.php?NewTime="+NewTime+":00"+"&checkid[]="+checkid+"&ActionId=UpdateTime";
                //window.open(myurl);
                //NewTime=NewTime;
            }
            if (myurl.length>0)
            {
                //alert(myurl);
                //alert(tempRowId+":"+tempColId);
                /*
                retCode=openUrl(myurl);
                if (retCode!=-2){
                eval("ListTable"+tempTableId).rows[0].cells[tempColId].innerHTML="<DIV align='center' STYLE='overflow: hidden; ' >"+NewTime+"</DIV>";

                CloseDiv();
                }
                */
                var ajax=InitAjax();
                ajax.open("GET",myurl,true);
                ajax.onreadystatechange =function(){
                if(ajax.readyState==4){// && ajax.status ==200
                    eval("ListTable"+tempTableId).rows[0].cells[tempColId].innerHTML="<DIV align='center' STYLE='overflow: hidden; ' >"+NewTime+"</DIV>";
                        CloseDiv();
                        }
                    }
                ajax.send(null);

            }

            break;

        }
    }

function strtrim(str){ //删除左右两端的空格
return str.replace(/(^\s*)|(\s*$)/g, "");
}

function IsNum(s)
{
    if (s!=null && s!="")
    {
        return !isNaN(s);
    }
    return false;
}
//-->
</script>