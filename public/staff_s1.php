<?php
include "../model/subprogram/s1_model_1.php";
//步骤2：需处理
switch ($Action) {
    case 21:
        $Th_Col = "选项|40|序号|40|员工ID|50|姓名|60|部门|60|小组|80|职位|70|员工等级|80|考勤状态|80|入职日期|75|在职时间|80|离职日期|75|离职类型|75|离职原因|180|性别|40|籍贯|40|社保|50|介绍人|50";
        break;
	case 22:
		$Th_Col = '选项|65|序号|75|员工ID|85|姓名|95|部门|95|小组|115|职位|105';
		break;//选人插件 by ckt 2017-12-28
    default:
        $Th_Col = "选项|40|序号|40|员工ID|50|姓名|60|部门|60|小组|80|职位|70|员工等级|80|考勤状态|80|入职日期|75|在职时间|80|性别|40|籍贯|40|社保|50|介绍人|50";
        break;
}
$ColsNumber = 16;
$tableMenuS = 600;
$Page_Size = 100;                            // 每页默认记录数量
$Parameter .= ",Bid,$Bid,Jid,$Jid,Kid,$Kid,Month,$Month,theYear,$theYear,ItemName,$ItemName";//ewen 加入年终奖金过滤条件
$jjItem = $theYear . $ItemName;
$BranchIdSTR = $Bid == "" ? "" : " and M.BranchId=$Bid";
$JobIdSTR = $Jid == "" ? "" : " and M.JobId=$Jid";
$KqSignSTR = $Kid == "" ? "" : " and M.KqSign=$Kid";
$Page_Size = 100;                           // 每页默认记录数量
$isPage = 1; // 是否分页
// 非必选,过滤条件
switch ($Action) {
    case"0"://来自新增社保资料，需过滤已加入社保的记录OKM.cSign='$Login_cSign' and
        $AddTB = " LEFT JOIN $DataIn.sbdata Z ON Z.Number=M.Number";
        $NumberSTR = "  AND  M.Estate=1 AND Z.Number IS NULL " . $BranchIdSTR;
        break;
    case"1"://来自于登录帐户：EWEN 2012-08-10
        $SelectFrom = 5;
        $AddTB = " LEFT JOIN $DataIn.usertable Z ON Z.Number=M.Number";
        $NumberSTR = " and M.Estate=1 AND Z.Number IS NULL";
        break;
    case "2"://来自员工等级设定OKM.cSign='$Login_cSign'  and
        //$NumberSTR=" AND M.Estate=1 ".$BranchIdSTR.$JobIdSTR;
        if ($fSearchPage == "cw_jj_frist") {////ewen 加入年终奖金过滤条件：即使离职也需计算        条件1:入职日期需要在当年之内；条件2:离职日期在当年之后；条件3:还没有生成奖金的
            $Year = $theYear;
            $tempYear = $theYear + 1;
            $NumberSTR = " AND M.ComeIn<'$tempYear-01-01' AND M.Number NOT IN (SELECT Number FROM $DataIn.cw11_jjsheet_frist WHERE ItemName='$jjItem') AND ( (M.Estate=1 $BranchIdSTR $JobIdSTR ) OR ( M.Number  IN (SELECT Number FROM $DataIn.dimissiondata WHERE left(outDate,7)>'$Year-12' OR  outDate='$Year-12-31')  $BranchIdSTR $JobIdSTR  ))";
        } else {
            $NumberSTR = " AND M.Estate=1 " . $BranchIdSTR . $JobIdSTR;
        }
        break;
    case "3"://来自考勤:过滤 1:考勤有效        2：考勤无效        3：无须考勤(过滤)OKM.cSign='$Login_cSign' and
        //$NumberSTR="  AND M.Estate=1 and M.KqSign!=3 ".$BranchIdSTR.$JobIdSTR;
        $NumberSTR = "  AND M.Estate=1  AND M.OffStaffSign=0" . $BranchIdSTR . $JobIdSTR;
        break;
    case "4"://来自部门界定OKM.cSign='$Login_cSign'  and
        $NumberSTR = " AND M.Estate=1 " . $BranchIdSTR;
        break;
    case "5"://来自职位设定OKM.cSign='$Login_cSign' and
        $NumberSTR = "  AND M.Estate=1 " . $JobIdSTR;
        break;
    case "6"://社保有效 且当月没有缴费的     来自社保缴费记录 OK" ".
        $MonthSTR = $Month == "" ? "" : " and M.Number NOT IN (SELECT Number FROM $DataIn.sbpaysheet WHERE Month='$Month' AND TypeId=1 ORDER BY Number)";
        $NumberSTR = "  and M.Number IN (SELECT Number FROM $DataIn.sbdata WHERE Estate=1 AND (eMonth='' OR eMonth>'$Month' OR eMonth='$Month' ) ORDER BY Number) " . $MonthSTR . $BranchIdSTR;
        break;
    case "7"://来自考勤调动M.cSign='$Login_cSign' and
        $NumberSTR = " AND M.Estate=1 " . $KqSignSTR;
        break;
    case "8"://M.cSign='7' and
        $NumberSTR = "  AND M.Estate=1 and M.KqSign=3";
        break;
    case "9":
        $NumberSTR = " AND M.Estate=1 AND M.JobId>10 AND M.GroupId=0 " . $JobIdSTR;
        break;
    case "10"://来自员工等级设定OKM.cSign='$Login_cSign'  and
        $NumberSTR = " AND M.Estate=1 " . $BranchIdSTR . $JobIdSTR;
        //echo "$NumberSTR=$NumberSTR";
        break;
    case "11"://住房公积金
        $MonthSTR = $Month == "" ? "" : " and M.Number NOT IN(SELECT Number FROM $DataIn.sbpaysheet WHERE Month='$Month' AND TypeId=2 ORDER BY Number)";
        $NumberSTR = " and M.Number IN(SELECT Number FROM $DataIn.epfdata WHERE Estate=1 AND (eMonth='' OR eMonth>'$Month' OR eMonth='$Month' ) ORDER BY Number)" . $MonthSTR . $BranchIdSTR;
        break;
    case "12"://来自新增公积金资料
        $AddTB = " LEFT JOIN $DataIn.epfdata Z ON Z.Number=M.Number";
        $NumberSTR = "  AND  M.Estate=1 AND Z.Number IS NULL " . $BranchIdSTR;
        break;
    case 13: //来自意外险
        $DateStr = $Month . '-01';
        $MonthSTR = $Month == "" ? "" : " AND M.Number  NOT  IN (
            SELECT X.Number FROM $DataIn.sbpaysheet X WHERE  (date_add(DATE_FORMAT(CONCAT(X.Month,'-01'),'%Y-%m-%d'),interval 1 year)>'$DateStr' AND X.ValidMonth>'$Month') AND X.TypeId=3 AND X.Number=M.Number )";
        $NumberSTR = "   AND M.Estate=1 " . $MonthSTR . $BranchIdSTR;
        break;
    case 14: //来自商业险
        $DateStr = $Month . '-01';
        $MonthSTR = $Month == "" ? "" : " and M.Number NOT IN (SELECT Number FROM $DataIn.sbpaysheet WHERE Month='$Month' AND TypeId=4 ORDER BY Number)";
        $NumberSTR = "   AND M.Estate=1 " . $MonthSTR . $BranchIdSTR;
        break;
	case "22"://用户选择插件 by ckt 2017-12-28
        $SelectFrom = 5;
        $NumberSTR = " and M.Estate=1 ";
        break;
    default:
        break;
}
//步骤3：
include "../model/subprogram/s1_model_3.php";
//步骤4：可选，其它预设选项
echo "<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>";
echo $CencalSstr;

/*
start by lwh
 */
$SelectTB="M";$SelectFrom=1;
//选择部门
include "../model/subselect/BranchId.php";

//选择职位
$SelectTB="M";
$selectResult = mysql_query("SELECT B.Id,B.Name FROM  $DataPublic.jobdata B  
                                 WHERE B.Estate=1 AND (cSign=$Login_cSign OR cSign=0 ) ORDER BY B.Id",$link_id);
if($selectRow = mysql_fetch_array($selectResult)){
    $SelectName="JobId";
    $SelectListStr="<select name=$SelectName id=$SelectName onchange='document.form1.submit()'>
                <option value='' selected>--全部职位--</option>";
    do{
        $theId=$selectRow["Id"];
        $theName=$selectRow["Name"];
        if ($theId==$JobId){
            $SelectListStr.="<option value='$JobId' selected>$theName</option>";
            if ($SelectTB!="") $SearchRows.=" AND $SelectTB.JobId='$theId' ";
        }
        else{
            $SelectListStr.="<option value='$theId'>$theName</option>";
        }
    }while ($selectRow = mysql_fetch_array($selectResult));
    $SelectListStr.="</select>&nbsp;";
}
/*
finish by lwh
 */
echo $SelectListStr;

//快速搜索 add by ckt 2018-01-05
if($Action==22){
	$From=$From==""?"s1":$From;
	echo "<input name='From' type='hidden' id='From' value='$From'>";
	$searchtable="branchdata|B|Name|0|0";
	include "../model/subprogram/QuickSearch.php";
	if ($FromSearch=="FromSearch") {  //来自快速搜索
		$Arraysearch=explode("|",$searchtable);
		$TAsName=$Arraysearch[1];
		$TField=$Arraysearch[2];
		$SearchRows =" AND $TAsName.$TField like '%$search%' ";
	}
}
//步骤5：
include "../model/subprogram/s1_model_5.php";
include "../model/subprogram/sSearch_reload.php";

//步骤6：需处理数据记录处理
$i = 1;
$j = ($Page - 1) * $Page_Size + 1;
List_Title($Th_Col, "1", 0);
switch ($Action) {
    //if ($Action==110) {
    case "-1":
        $mySql = "SELECT 
        M.Id,M.Number,M.Name,M.BranchId,M.JobId,M.Grade,M.Introducer,M.ComeIn,
        S.Sex,S.Rpr,B.Name AS BranchName,J.Name AS JobName,K.Name AS KqSign,G.GroupName,M.Estate
        FROM $DataIn.staffmain M
        LEFT JOIN $DataIn.staffsheet S ON M.Number=S.Number
        LEFT JOIN $DataIn.jobdata J ON J.Id=M.JobId
        LEFT JOIN $DataIn.staffgroup G ON G.GroupId=M.GroupId
        LEFT JOIN $DataIn.branchdata B ON B.Id=M.BranchId
        LEFT JOIN $DataIn.kqtype K ON M.KqSign=K.Id
        $AddTB
        WHERE 1 AND M.Estate=1 $sSearch ORDER BY M.BranchId,M.GroupId,M.Number";
        break;
    case    "110":
        $mySql = "SELECT 
        M.Id,M.Number,M.Name,M.BranchId,M.JobId,M.Grade,M.Introducer,M.ComeIn,
        S.Sex,S.Rpr,B.Name AS BranchName,J.Name AS JobName,K.Name AS KqSign,'' as GroupName,M.Estate
        FROM $DataIn.dimissiondata D 
        LEFT JOIN $DataIn.staffmain M ON D.Number=M.Number
        LEFT JOIN $DataIn.staffsheet S ON M.Number=S.Number
        LEFT JOIN $DataIn.jobdata J ON J.Id=M.JobId
        LEFT JOIN $DataIn.staffgroup G ON G.GroupId=M.GroupId
        LEFT JOIN $DataIn.branchdata B ON B.Id=M.BranchId
        LEFT JOIN $DataIn.kqtype K ON M.KqSign=K.Id
        $AddTB
        WHERE 1   $sSearch  ORDER BY M.BranchId,M.GroupId,M.Number";
        break;
    case    "111":  // Rs_Casualty_other.php
        $mySql = "SELECT 
        M.Id,M.Number,M.Name,M.BranchId,M.JobId,M.Grade,M.Introducer,M.ComeIn,
        S.Sex,S.Rpr,B.Name AS BranchName,J.Name AS JobName,K.Name AS KqSign,G.GroupName,M.Estate
        FROM $DataIn.staffmain M
        LEFT JOIN $DataIn.staffsheet S ON M.Number=S.Number
        LEFT JOIN $DataIn.jobdata J ON J.Id=M.JobId
        LEFT JOIN $DataIn.staffgroup G ON G.GroupId=M.GroupId
        LEFT JOIN $DataIn.branchdata B ON B.Id=M.BranchId
        LEFT JOIN $DataIn.kqtype K ON M.KqSign=K.Id
        WHERE 1 $sSearch  ORDER BY M.BranchId,M.GroupId,M.Number";
        break;
    case "122":
        $mySql = "SELECT 
        M.Id,M.Number,M.Name,M.BranchId,M.JobId,M.Grade,M.Introducer,M.ComeIn,
        S.Sex,S.Rpr,B.Name AS BranchName,J.Name AS JobName,K.Name AS KqSign,G.GroupName,M.Estate
        FROM $DataIn.staffmain M
        LEFT JOIN $DataIn.staffsheet S ON M.Number=S.Number
        LEFT JOIN $DataIn.jobdata J ON J.Id=M.JobId
        LEFT JOIN $DataIn.staffgroup G ON G.GroupId=M.GroupId
        LEFT JOIN $DataIn.branchdata B ON B.Id=M.BranchId
        LEFT JOIN $DataIn.kqtype K ON M.KqSign=K.Id
        WHERE M.kqSign in ('2', '3') And M.Estate = '1'  ORDER BY M.BranchId,M.GroupId,M.Number";
        break;
    case "21": //离职人员
        $mySql = "SELECT 
        M.Id,M.Number,M.Name,M.BranchId,M.JobId,M.Grade,M.Introducer,M.ComeIn,
        S.Sex,S.Rpr,B.Name AS BranchName,J.Name AS JobName,K.Name AS KqSign,G.GroupName,M.Estate,
        D.outDate,T.Name AS TypeName,D.Reason AS LeaveReason
        FROM $DataIn.staffmain M
        LEFT JOIN $DataIn.staffsheet S ON M.Number=S.Number
        LEFT JOIN $DataIn.jobdata J ON J.Id=M.JobId
        LEFT JOIN $DataIn.staffgroup G ON G.GroupId=M.GroupId
        LEFT JOIN $DataIn.branchdata B ON B.Id=M.BranchId
        LEFT JOIN $DataIn.kqtype K ON M.KqSign=K.Id
        LEFT JOIN $DataIn.dimissiondata D ON D.Number=M.Number 
        LEFT JOIN $DataIn.dimissiontype T ON T.Id =D.LeaveType
        WHERE 1  And M.Estate = '0'  ORDER BY D.outDate DESC";
        break;
	case "22"://员工选择插件 by ckt 2017-12-28
		$mySql = "SELECT M.Number,M.Name,B.Name AS BranchName,G.GroupName,J.Name AS JobName,M.Estate 
        FROM $DataIn.staffmain M
        LEFT JOIN $DataIn.staffsheet S ON M.Number=S.Number
        LEFT JOIN $DataIn.jobdata J ON J.Id=M.JobId
        LEFT JOIN $DataIn.staffgroup G ON G.GroupId=M.GroupId
        LEFT JOIN $DataIn.branchdata B ON B.Id=M.BranchId
        LEFT JOIN $DataIn.kqtype K ON M.KqSign=K.Id
        WHERE 1 $NumberSTR $sSearch $SearchRows ORDER BY M.BranchId,M.GroupId,M.Number";	
		break;
    default:
        $mySql = "SELECT 
        M.Id,M.Number,M.Name,M.BranchId,M.JobId,M.Grade,M.Introducer,M.ComeIn,
        S.Sex,S.Rpr,B.Name AS BranchName,J.Name AS JobName,K.Name AS KqSign,G.GroupName,M.Estate
        FROM $DataIn.staffmain M
        LEFT JOIN $DataIn.staffsheet S ON M.Number=S.Number
        LEFT JOIN $DataIn.jobdata J ON J.Id=M.JobId
        LEFT JOIN $DataIn.staffgroup G ON G.GroupId=M.GroupId
        LEFT JOIN $DataIn.branchdata B ON B.Id=M.BranchId
        LEFT JOIN $DataIn.kqtype K ON M.KqSign=K.Id
        $AddTB
        WHERE 1 $NumberSTR $sSearch $SearchRows ORDER BY M.BranchId,M.GroupId,M.Number";
        break;
}
//if ($Login_P_Number==10871) echo $mySql;
$myResult = mysql_query($mySql . " $PageSTR", $link_id);
if ($myRow = mysql_fetch_array($myResult)) {
    do {
        $m = 1;
        $Number = $myRow["Number"];
        $Name = $myRow["Name"];
        $BranchName = $myRow["BranchName"];
        $JobName = $myRow["JobName"];
		$GroupName = $myRow["GroupName"];;
		if($Action!=22){
			$Id = $myRow["Id"];
			$Grade = $myRow["Grade"] == 0 ? "&nbsp;" : $myRow["Grade"];
			$KqSign = $myRow["KqSign"];
			$Mobile = $myRow["Mobile"] == "" ? "&nbsp;" : $myRow["Mobile"];
			$Dh = $myRow["Dh"] == "" ? "&nbsp;" : $myRow["Dh"];
			$Mail = $myRow["Mail"] == "" ? "&nbsp;" : "<a href='mailto:$myRow[Mail]'><img src='../images/email1.gif' title='$myRow[Mail]' width='18' height='18' border='0'></a>";
			$ComeIn = $myRow["ComeIn"];			
			$JobId = $myRow["JobId"];
		}
        switch ($Action) {
            case"2"://等级设定
                $Low = 1;
                $Hight = 30;
                $gResult = mysql_query("SELECT Low,Hight FROM $DataIn.gradedata where 1 and JobId=$JobId LIMIT 1", $link_id);
                if ($gRow = mysql_fetch_array($gResult)) {
                    $Low = $gRow["Low"];
                    $Hight = $gRow["Hight"];
                }
                $checkidValue = $Number . "^^" . $Name . "^^" . $BranchName . "^^" . $JobName . "^^" . $Low . "^^" . $Hight . "^^" . $Grade;
                break;
            case"10"://等级设定
            case"21"://离职员工
                $TypeName = $myRow["TypeName"];
                $LeaveReason = $myRow["LeaveReason"];
                $outDate = $myRow["outDate"];
                //月平均工资
                $MonthE = substr($outDate, 0, 7);
                $MonthS = date('Y-m', strtotime('-1 year'));
                $LocalSql = "SELECT Month,sum(Amount) as Amount  from(
                                SELECT M.Month, M.Amount+M.Jz-M.taxbz AS Amount 
                                FROM $DataIn.cwxzsheet M
                                LEFT JOIN $DataIn.staffmain P ON M.Number=P.Number
                                WHERE M.Month >= '$MonthS' AND M.Month>= DATE_FORMAT(P.ComeIn,'%Y-%m') AND M.Month <= '$MonthE'  AND M.Number='$Number'  
                                UNION ALL 
                                SELECT M.Month as Month,M.Amount AS Amount 
                                FROM $DataIn.hdjbsheet M 
                                LEFT JOIN $DataIn.staffmain P ON M.Number=P.Number
                                WHERE M.Month >= '$MonthS' AND M.Month>= DATE_FORMAT(P.ComeIn,'%Y-%m') AND M.Month <= '$MonthE'  AND M.Number='$Number'
                         )K GROUP BY Month";
                $SumAmount = 0;
                $LocalResult = mysql_query($LocalSql, $link_id);
                if ($LocalRow = mysql_fetch_array($LocalResult)) {
                    do {
                        $CurMonth = $LocalRow["Month"];
                        $CurAmount = $LocalRow["Amount"];
                        if ($CurMonth != "" && $CurAmount != "") {
                            $SumAmount = $SumAmount + $CurAmount;
                        }
                    } while ($LocalRow = mysql_fetch_array($LocalResult));
                }
                $AveSalary = sprintf("%.0f", $SumAmount / 12);
                $checkidValue = $Number . "^^" . $Name . "^^" . $AveSalary . "^^" . $TypeName;
                break;
            case"110"://等级设定
                $checkidValue = $Name . "^^" . $Number;
                break;
            case"111"://意外保除替换 add by zx 2014-12-18  Rs_Casualty_other.php
                $checkidValue = $Name . "^^" . $Number;
                break;
            default:
                $checkidValue = $Number . "^^" . $Name;
                break;
        }
		if($Action!=22){//非用户选择窗口 by ckt 2017-12-29
			$Sex = $myRow["Sex"] == 1 ? "男" : "女";
			$Rpr = $myRow["Rpr"];
			$rResult = mysql_query("SELECT Name FROM $DataIn.rprdata WHERE Estate=1 and Id=$Rpr order by Id", $link_id);
			if ($rRow = mysql_fetch_array($rResult)) {
				$Rpr = $rRow["Name"];
			}
			/*    $sbResult = mysql_query("SELECT Id FROM $DataIn.sbdata WHERE Number=$Number order by Id LIMIT 1",$link_id);
				$Sb="&nbsp;";
				if($sbRow = mysql_fetch_array($sbResult)){
					$Sb="<a href='staff_sbview.php?Number=$Number' target='_blank'>查看</a>";
					}*/
			$Introducer = $myRow["Introducer"];
			if ($Introducer != "") {
				$iResult = mysql_query("SELECT Name FROM $DataIn.staffmain WHERE Number=$Introducer order by Id", $link_id);
				if ($iRow = mysql_fetch_array($iResult)) {
					$Introducer = $iRow["Name"];
				}
			} else {
				$Introducer = "&nbsp;";
			}
			//计算在职时间
			$ThisDay = date("Y-m-d");
			$ThisEndDay = $Month . "-" . date("t", strtotime($ThisDay));
			$Years = date("Y", strtotime($ThisDay)) - date("Y", strtotime($ComeIn));
			$ThisMonth = date("m", strtotime($ThisDay));
			$CominMonth = date("m", strtotime($ComeIn));
			//年计算
			if ($ThisMonth < $CominMonth) {//计薪月份少于进公司月份
				$Years = ($Years - 1);
				$MonthSTR = $ThisMonth + 12 - $CominMonth;
				$gl_STR = $Years <= 0 ? "&nbsp;" : $Years . "年";
			} else {
				$MonthSTR = $ThisMonth - $CominMonth;
				$gl_STR = $Years <= 0 ? "&nbsp;" : $Years . "年";
			}
			if (date("d", strtotime($ComeIn)) < 4) {
				$MonthSTR = $MonthSTR + 1;
			}
			$MonthSTR = $MonthSTR > 0 ? $MonthSTR . "个月" : "";
			$gl_STR = $gl_STR . $MonthSTR;
			
		}
		if ($myRow["Estate"] == 1) {//在职 //ewen 标识
			$Name = "<span class='greenB'>$Name</span>";
		} else {//离职
			$Name = "<span class='redB'>$Name</span>";
			$outResult = mysql_fetch_array(mysql_query("SELECT outDate FROM $DataIn.dimissiondata WHERE Number='$Number' order by Id LIMIT 1", $link_id));
			$outDate = $outResult["outDate"];
			$ComeIn = $ComeIn . "~<br><span class='redB'>" . $outDate . "</span>";
		}
        $Locks = 1;
        switch ($Action) {
            case 21:
                $ValueArray = array(
                    array(0 => $Number, 1 => "align='center'"),
                    array(0 => $Name, 1 => "align='center'"),
                    array(0 => $BranchName, 1 => "align='center'"),
                    array(0 => $GroupName, 1 => "align='center'"),
                    array(0 => $JobName, 1 => "align='center'"),
                    array(0 => $Grade, 1 => "align='center'"),
                    array(0 => $KqSign, 1 => "align='center'"),
                    array(0 => $ComeIn, 1 => "align='center'"),
                    array(0 => $gl_STR, 1 => "align='center'"),
                    array(0 => $outDate, 1 => "align='center'"),
                    array(0 => $TypeName, 1 => "align='center'"),
                    array(0 => $LeaveReason),
                    array(0 => $Sex, 1 => "align='center'"),
                    array(0 => $Rpr, 1 => "align='center'"),
                    array(0 => $Sb, 1 => "align='center'"),
                    array(0 => $Introducer, 1 => "align='center'")
                );
                break;
			case 22://用户选择插件 by ckt 2017-12-28
				$ValueArray = array(
                    array(0 => $Number, 1 => "align='center'"),
                    array(0 => $Name, 1 => "align='center'"),
                    array(0 => $BranchName, 1 => "align='center'"),
                    array(0 => $GroupName, 1 => "align='center'"),
                    array(0 => $JobName, 1 => "align='center'")
                );
				break;
            default:
                $ValueArray = array(
                    array(0 => $Number, 1 => "align='center'"),
                    array(0 => $Name, 1 => "align='center'"),
                    array(0 => $BranchName, 1 => "align='center'"),
                    array(0 => $GroupName, 1 => "align='center'"),
                    array(0 => $JobName, 1 => "align='center'"),
                    array(0 => $Grade, 1 => "align='center'"),
                    array(0 => $KqSign, 1 => "align='center'"),
                    array(0 => $ComeIn, 1 => "align='center'"),
                    array(0 => $gl_STR, 1 => "align='center'"),
                    array(0 => $Sex, 1 => "align='center'"),
                    array(0 => $Rpr, 1 => "align='center'"),
                    array(0 => $Sb, 1 => "align='center'"),
                    array(0 => $Introducer, 1 => "align='center'")
                );
                break;
        }
        include "../model/subprogram/s1_model_6.php";
    } while ($myRow = mysql_fetch_array($myResult));
} else {
    noRowInfo($tableWidth);
}

//步骤7：
echo '</div>';
List_Title($Th_Col, "0", 0);
$myResult = mysql_query($mySql, $link_id);
$RecordToTal = mysql_num_rows($myResult);
pBottom($RecordToTal, $i - 1, $Page, $Pagination, $Page_Size, $timer, $Login_WebStyle, $tableWidth);
include "../model/subprogram/read_model_menu.php";
?>