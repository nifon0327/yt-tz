<?php
//电信-zxq 2012-08-01
include "../model/modelhead.php";
$path = $_SERVER["DOCUMENT_ROOT"];
include_once('FactoryClass/AttendanceDatetype.php');
?>
<script>
function zhtj(obj){
    switch(obj){
        case "chooseDate"://改变采购
            if(document.all("CompanyId")!=null){
                document.forms["form1"].elements["CompanyId"].value="";
                }
        break;
        }
    document.form1.action="ck_th_read.php";
    document.form1.submit();
}
</script>
<?php
//步骤2：需处理
//$factoryCheck = "yes";

$ColsNumber=14;
$tableMenuS=500;
ChangeWtitle("$SubCompany 物料退换列表");
$funFrom="ck_th";
$From=$From==""?"read":$From;
$sumCols="5";           //求和列,需处理
$MergeRows=4;
$Th_Col="操作|40|退换日期|70|退换单号|75|图片说明|60|选项|40|序号|40|配件Id|50|配件名称|300|单位|40|退换数量|60|原因|50|图片|40|审核|50|供应商审核|65";

//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;  //默认分页方式:1分页，0不分页
$Page_Size = 300;                           //每页默认记录数量
$ActioToS="1,2,3,4,7,8";
//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-可选条件下拉框
if($tempStuffId!=""){
$SearchRowsA=" AND D.StuffId=$tempStuffId";
$From="slist";
}
if($From!="slist"){ //非查询：过滤采购、结付方式、供应商、月份
    $SearchRows="";
    $date_Result = mysql_query("SELECT Date FROM $DataIn.ck2_thmain WHERE 1 GROUP BY DATE_FORMAT(Date,'%Y-%m') ORDER BY Date DESC",$link_id);
    if($dateRow = mysql_fetch_array($date_Result)) {
        echo"<select name='chooseDate' id='chooseDate' onchange='zhtj(this.name)'>";
        do{
            $dateValue=date("Y-m",strtotime($dateRow["Date"]));
            $StartDate=$dateValue."-01";
            $EndDate=date("Y-m-t",strtotime($dateRow["Date"]));
            $chooseDate=$chooseDate==""?$dateValue:$chooseDate;
            if($chooseDate==$dateValue){
                echo"<option value='$dateValue' selected>$dateValue</option>";
                $SearchRows.=" and ((M.Date>'$StartDate' and M.Date<'$EndDate') OR M.Date='$StartDate' OR M.Date='$EndDate')";
                }
            else{
                echo"<option value='$dateValue'>$dateValue</option>";
                }
            }while($dateRow = mysql_fetch_array($date_Result));
        echo"</select>&nbsp;";
        }
    $providerSql = mysql_query("SELECT M.CompanyId,P.Forshort,P.Letter 
            FROM $DataIn.ck2_thmain M,$DataIn.trade_object P WHERE M.CompanyId=P.CompanyId $SearchRows GROUP BY M.CompanyId ORDER BY P.Letter",$link_id);
    if($providerRow = mysql_fetch_array($providerSql)){
        echo "<select name='CompanyId' id='CompanyId' onchange='zhtj(this.name)'>";
        echo"<option value='' selected>全部</option>";
        do{
            $Letter=$providerRow["Letter"];
            $Forshort=$providerRow["Forshort"];
            $Forshort=$Letter.'-'.$Forshort;
            $thisCompanyId=$providerRow["CompanyId"];
            if($CompanyId==$thisCompanyId){
                echo"<option value='$thisCompanyId' selected>$Forshort </option>";
                $SearchRows.=" and M.CompanyId='$thisCompanyId'";
                }
            else{
                echo"<option value='$thisCompanyId'>$Forshort</option>";
                }
            }while ($providerRow = mysql_fetch_array($providerSql));
        echo"</select>&nbsp;";
        }
    }
//检查进入者是否采购
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
    $CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT M.BillNumber,M.Date,M.Attached,M.CompanyId, M.Operator,S.Id,S.Mid,S.StuffId,S.Qty,S.Remark,S.Estate,S.Locks,D.StuffCname,D.Picture,U.Name AS UnitName,S.Picture AS thPicture,S.Id AS thisId
FROM $DataIn.ck2_thsheet S
LEFT JOIN $DataIn.ck2_thmain M ON S.Mid=M.Id 
LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
LEFT JOIN $DataPublic.stuffunit U ON U.Id=D.Unit 
WHERE 1 $SearchRows $SearchRowsA ORDER BY M.Date DESC,M.Id DESC";
$mainResult = mysql_query($mySql." $PageSTR",$link_id);
if($mainRows = mysql_fetch_array($mainResult)){
    $tbDefalut=0;
    $midDefault="";
    $ImgDir="../download/thimg/";
    do{
        $m=1;
        //主单信息/退换日期/退换单号
        $Id=$mainRows["Id"];
        $Mid=$mainRows["Mid"];
        $Date=$mainRows["Date"];
        $Number = $mainRows['Operator'];
        /************加入过滤***************/
        $Number = $mainRows['Operator'];
        $sheet = new WorkScheduleSheet($Number, $Date, $attendanceTime['start'], $attendanceTime['end']);
        $datetypeModle = new AttendanceDatetype($DataIn, $DataPublic, $link_id);
        $datetype = $datetypeModle->getDatetype($Number, $Date, $sheet);
        if($datetype['morning'] != 'G' && $datetype['afternoon'] != 'G'){
            continue;
        }
        /*************************************/

        $BillNumber=$mainRows["BillNumber"];
        $MidSTR=anmaIn($Mid,$SinkOrder,$motherSTR);
        $BillNumberStr="<a href='ck_th_view.php?f=$MidSTR' target='_blank'>$BillNumber</a>";

        $upMian="<img location.href=\"#\"' style='CURSOR: pointer' onclick='upMainData(\"ck_th_upmain\",$Mid)' src='../images/edit.gif' title='更新退换主单资料' width='13' height='13'>";
        $Attached=$mainRows["Attached"];
        $Dir=anmaIn($ImgDir,$SinkOrder,$motherSTR);
        if($Attached==1){
            $Attached="T".$Mid.".jpg";
            $Attached=anmaIn($Attached,$SinkOrder,$motherSTR);
            $Attached="<span onClick='OpenOrLoad(\"$Dir\",\"$Attached\")' style='CURSOR: pointer;color:#FF6633'>view</span>";
            }
        else{
            $Attached="&nbsp;";
            }
        //明细资料
        $StuffId=$mainRows["StuffId"];
        if($StuffId>0){
            $checkidValue=$mainRows["Id"];
            $StuffCname=$mainRows["StuffCname"];
            $UnitName=$mainRows["UnitName"]==""?"&nbsp;":$mainRows["UnitName"];
            $Qty=$mainRows["Qty"];
            $Remark=trim($mainRows["Remark"])==""?"&nbsp;":"<img src='../images/remark.gif' title='$mainRows[Remark]' width='18' height='18'>";
            $Locks=$mainRows["Locks"];

            //$EstateSTR=$mainRows["Estate"]==2?"<div class='redB'>未审核</div>":"<div class='greenB'>已审核</div>";
            $LockRemark="";
            switch($mainRows["Estate"]){
                case 0:$EstateSTR="<div class='greenB'>已审核</div>";$LockRemark="已审核通过";break;
                case 1:$EstateSTR="-";$LockRemark="";break;
                case 2:$EstateSTR="<div class='redB'>未审核</div>";$BillNumberStr=$BillNumber;break;
                case 4:$EstateSTR="<div class='redB'>退回</div>";$BillNumberStr=$BillNumber;break;
            }

        $thPicture=$mainRows["thPicture"];
        $thisId=$mainRows["thisId"];
        $Dir=anmaIn("download/thimg/",$SinkOrder,$motherSTR);
        if($thPicture==1){
            $thPicture="T".$thisId.".jpg";
            $thPicture=anmaIn($thPicture,$SinkOrder,$motherSTR);
            $thPicture="<span onClick='OpenOrLoad(\"$Dir\",\"$thPicture\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
            }
        else{
            $thPicture="-";
            }



            //供应商审核
            $CompanyId=$mainRows["CompanyId"];
            if ($OldCompanyId!=$CompanyId && $CompanyId!='2270'){
                     $checkCpSql=mysql_query("SELECT DISTINCT B.CompanyId FROM $DataIn.UserTable A 
                                                                                             LEFT JOIN $DataIn.linkmandata B ON B.Id=A.Number 
                                                                                             WHERE A.Estate=1 and A.uType=3 and B.CompanyId='$CompanyId'",$link_id);
                  if($checkCpRows = mysql_fetch_array($checkCpSql)){
                      $AuditSign=1;
                   }
                   else{
                       $AuditSign=0;
                   }
                   $OldCompanyId=$CompanyId;
          }

             if ( $AuditSign==1){
                //供应商审核
                $thEstateSTR="<div class='redB'>未审核</div>";
                $checkThSql=mysql_query("SELECT R.Estate,R.Remark  FROM $DataIn.ck2_threview R WHERE R.Mid='$Id' LIMIT 1",$link_id);
                if($checkThRows = mysql_fetch_array($checkThSql)){
                      $thEstate=$checkThRows["Estate"];
                      $thRemark=$checkThRows["Remark"];
                     if ($thEstate==2){
                        $thEstateSTR="有异议 &nbsp;&nbsp;<img src='../images/remark.gif' title='$thRemark' width='18' height='18'>";
                        if ($Login_P_Number=='10868' ||  $Login_P_Number=='10007' ) $LockRemark="";
                     }
                     else{
                         $thEstateSTR="<div class='greenB'>已审核</div>";$LockRemark.="供应商已审核通过";
                     }
                     //$upMian="<img src='../images/lock.png' title='锁定操作!' width='15' height='15'>";
                 }
             }
             else{
                  $thEstateSTR="-";$LockRemark.="供应商未开系统";
            }

            //$LockRemark=$mainRows["Estate"]==0?"已审核通过":"";
            //检查是否有图片
            $Picture=$mainRows["Picture"];
            $d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
            include "../model/subprogram/stuffimg_model.php";
            if($Locks==0){//锁定状态:A一种是可以操作记录（分权限）；B一种是不可操作记录（不分权限）
                if($Keys & mLOCK){
                    if($LockRemark!=""){//财务强制锁定
                        $Choose="&nbsp;&nbsp;&nbsp;<img src='../images/lock.png' title='$LockRemark' width='15' height='15'>";
                        }
                    else{
                        $Choose="<input name='checkid[]' type='checkbox' id='checkid$i' value='$checkidValue' disabled><img src='../images/lock.png' width='15' height='15'>";
                        }
                    }
                else{       //A2：无权限对锁定记录操作
                    $Choose="&nbsp;&nbsp;&nbsp;<img src='../images/lock.png' title='锁定操作!' width='15' height='15'>";
                    }
                }
            else{
                if($Keys & mUPDATE || $Keys & mDELETE || $Keys & mLOCK){//有权限
                    if($LockRemark!=""){
                        $Choose="&nbsp;&nbsp;&nbsp;<img src='../images/lock.png' title='$LockRemark' width='15' height='15'>";
                        }
                    else{
                        $Choose="<input name='checkid[]' type='checkbox' id='checkid$i' value='$checkidValue' disabled><img src='../images/unlock.png' width='15' height='15'>";
                        }
                    }
                else{//无权限
                    $Choose="&nbsp;&nbsp;&nbsp;<img src='../images/lock.png' title='锁定操作!' width='15' height='15'>";
                    }
                }
            $Sid=anmaIn($StockId,$SinkOrder,$motherSTR);
            ///////////////////////////////////////////////////


            if($tbDefalut==0 && $midDefault==""){//首行
                //并行列
                echo"<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'><tr>";
                echo"<td scope='col' class='A0111' width='$Field[$m]' align='center'>$upMian</td>";//更新
                $unitWidth=$tableWidth-$Field[$m];
                $m=$m+2;
                echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Date</td>";//退换日期
                $unitWidth=$unitWidth-$Field[$m];
                $m=$m+2;
                echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$BillNumberStr</td>";//退换单号
                $unitWidth=$unitWidth-$Field[$m];
                $m=$m+2;
                echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Attached</td>";//图片说明
                $unitWidth=$unitWidth-$Field[$m];
                $m=$m+2;
                //echo"<td width='$unitWidth' class='A0101'>";
                echo"<td width='' class='A0101'>";
                $midDefault=$Mid;
                }
            if($midDefault!="" && $midDefault==$Mid){//同属于一个主ID，则依然输出明细表格
                $m=9;
                echo"<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
                echo"<tr onmousedown='ClickKeyCheck(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' 
                    onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
                    onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
                $unitFirst=$Field[$m]-1;
                echo"<td class='A0001' width='$unitFirst' height='20' align='center' $tdBGCOLOR>$Choose</td>";//选项
                $m=$m+2;
                echo"<td class='A0001' width='$Field[$m]' align='center'>$j</td>";          //序号
                $m=$m+2;
                echo"<td class='A0001' width='$Field[$m]' align='center'>$StuffId</td>";    //配件ID
                $m=$m+2;
                echo"<td class='A0001' width='$Field[$m]'>$StuffCname</td>";                //配件名称
                $m=$m+2;
                echo"<td class='A0001' width='$Field[$m]' align='center'>$UnitName</td>";   //单位
                $m=$m+2;
                echo"<td class='A0001' width='$Field[$m]' align='right'>$Qty</td>";     //退换数量
                $m=$m+2;
                echo"<td  class='A0001'  width='$Field[$m]'  align='center'>$Remark</td>";      //退换原因
                $m=$m+2;
                echo"<td class='A0001' width='$Field[$m]' align='center'>$thPicture</td>";  //图片
                $m=$m+2;
                echo"<td  class='A0001'  width='$Field[$m]'  align='center'>$EstateSTR</td>";       //审核状态
                $m=$m+2;
                echo"<td  width='' align='center'>$thEstateSTR</td>";//供应商审核状态
                echo"</tr></table>";
                $i++;
                $j++;
                }
            else{
                //新行开始
                echo"</td></tr></table>";//结束上一个表格
                //并行列
                echo"<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'><tr>";
                echo"<td scope='col' class='A0111' width='$Field[$m]' align='center'>$upMian</td>";//更新
                $unitWidth=$tableWidth-$Field[$m];
                $m=$m+2;
                echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Date</td>";//退换日期
                $unitWidth=$unitWidth-$Field[$m];
                $m=$m+2;
                echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$BillNumberStr</td>";//退换单号
                $unitWidth=$unitWidth-$Field[$m];
                $m=$m+2;
                echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Attached</td>";//图片说明
                $unitWidth=$unitWidth-$Field[$m];
                $m=$m+2;
                //并行宽
                //echo"<td width='$unitWidth' class='A0101'>";
                echo"<td width='' class='A0101'>";
                $midDefault=$Mid;
                echo"<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
                echo"<tr onmousedown='ClickKeyCheck(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' 
                    onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
                    onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
                $unitFirst=$Field[$m]-1;
                echo"<td class='A0001' width='$unitFirst' height='20' align='center' $tdBGCOLOR>$Choose</td>";//选项
                $m=$m+2;
                echo"<td class='A0001' width='$Field[$m]' align='center'>$j</td>";          //序号
                $m=$m+2;
                echo"<td class='A0001' width='$Field[$m]' align='center'>$StuffId</td>";    //配件ID
                $m=$m+2;
                echo"<td class='A0001' width='$Field[$m]'>$StuffCname</td>";                //配件名称
                $m=$m+2;
                echo"<td class='A0001' width='$Field[$m]' align='center'>$UnitName</td>";   //单位
                $m=$m+2;
                echo"<td class='A0001' width='$Field[$m]' align='right'>$Qty</td>";     //退换数量
                $m=$m+2;
                echo"<td  class='A0001'  width='$Field[$m]' align='center'>$Remark</td>";       //退换原因
                $m=$m+2;
                echo"<td class='A0001' width='$Field[$m]' align='center'>$thPicture</td>";  //图片
                $m=$m+2;
                echo"<td  class='A0001'  width='$Field[$m]'  align='center'>$EstateSTR</td>";       //审核状态
                $m=$m+2;
                echo"<td  width='' align='center'>$thEstateSTR</td>";//供应商审核状态
                echo"</tr></table>";
                $i++;
                $j++;
                }
            }
        }while($mainRows = mysql_fetch_array($mainResult));
    echo"</tr></table>";
    }
else{
    noRowInfo($tableWidth);
    }
echo '</div>';
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>
