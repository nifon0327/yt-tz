<?php
//电信-zxq 2013-07-11
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
/** Include PHPExcel */
require_once '../plugins/PHPExcel/Classes/PHPExcel.php';

//echo $chooseMonth;
//exit();

$objPHPExcel = new PHPExcel();
 $style_left= array( 
         'font'    => array (
                         'size'      => 14
          ),
        'alignment' => array(
          'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
          'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
          'wrap'       => true
        )
  );
  
$style_center = array( 
         'font'    => array (
                         'size'      => 14
          ),
        'alignment' => array(
          'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
          'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
          'wrap'       => true
        )
  );
  
  $style_right = array( 
         'font'    => array (
                         'size'      => 14
          ),
        'alignment' => array(
          'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
          'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
          'wrap'       => true
        )
  );
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(15);


$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(20);
$objPHPExcel->getActiveSheet()->mergeCells("A1:T1"); 
$objPHPExcel->getActiveSheet()->setCellValue("A1", "$chooseMonth 考勤表");
 $objPHPExcel->getActiveSheet()->getStyle( 'A1:T1')->applyFromArray($style_center);
$objPHPExcel->getActiveSheet()->getStyle('A1:T1')->getFont()->setSize(18);

$Rows=2;

//"选项|40|序号|30|月份|50|部门|50|职位|50|员工ID|50|姓名|50|应到|40|实到|40|1.5倍|50|2倍工时|50|3倍工时|50|迟到次数|40|迟到次数|40|事假|40|病假|40|有薪|40|无薪|40|缺勤工时|40|无效工时|40|旷工工时|40|有薪工时|40";


$objPHPExcel->getActiveSheet()->getRowDimension("$Rows")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("A$Rows", "月份");
$objPHPExcel->getActiveSheet()->setCellValue("B$Rows", "部门");
$objPHPExcel->getActiveSheet()->setCellValue("C$Rows", "职位");
$objPHPExcel->getActiveSheet()->setCellValue("D$Rows", "员工ID");
$objPHPExcel->getActiveSheet()->setCellValue("E$Rows", "姓名");
$objPHPExcel->getActiveSheet()->setCellValue("F$Rows", "应到");
$objPHPExcel->getActiveSheet()->setCellValue("G$Rows", "实到");
$objPHPExcel->getActiveSheet()->setCellValue("H$Rows", "1.5倍");
$objPHPExcel->getActiveSheet()->setCellValue("I$Rows", "2倍工时");
$objPHPExcel->getActiveSheet()->setCellValue("J$Rows", "3倍工时");
$objPHPExcel->getActiveSheet()->setCellValue("K$Rows", "迟到次数");
$objPHPExcel->getActiveSheet()->setCellValue("L$Rows", "早退次数");
$objPHPExcel->getActiveSheet()->setCellValue("M$Rows", "事假");
$objPHPExcel->getActiveSheet()->setCellValue("N$Rows", "病假");
$objPHPExcel->getActiveSheet()->setCellValue("O$Rows", "有薪");
$objPHPExcel->getActiveSheet()->setCellValue("P$Rows", "无薪");
$objPHPExcel->getActiveSheet()->setCellValue("Q$Rows", "缺勤工时");
$objPHPExcel->getActiveSheet()->setCellValue("R$Rows", "无效工时");
$objPHPExcel->getActiveSheet()->setCellValue("S$Rows", "旷工工时");
$objPHPExcel->getActiveSheet()->setCellValue("T$Rows", "有薪工时");

$objPHPExcel->getActiveSheet()->getStyle( "A$Rows:T$Rows")->applyFromArray($style_center);
$Rows=3;
//$searchRow = $APP_FACTORY_CHECK?"AND M.WorkAdd != 6 and M.JobId != 38":"";
$mySql = "SELECT K.*,M.Name,M.Estate,B.Name AS Branch,J.Name AS Job
FROM $DataIn.kqdata K 
LEFT JOIN $DataPublic.staffmain M ON M.Number=K.Number 
LEFT JOIN $DataPublic.branchdata B ON B.Id=M.BranchId 
LEFt JOIN $DataPublic.jobdata J ON J.Id=M.JobId
WHERE 1 and K.Month='$chooseMonth'  AND M.JobId!=38 AND M.WorkAdd!=6 AND M.Number != '10744' ORDER BY K.Month DESC,M.Estate DESC,M.BranchId,M.JobId,K.Number";

$staffResult = mysql_query($mySql);
while($myRow = mysql_fetch_assoc($staffResult)){

    $Id=$myRow["Id"];
    $Month=$myRow["Month"];
    $Number=$myRow["Number"];
    $Name=$myRow["Name"];
    $Branch=$myRow["Branch"];       
    $Job=$myRow["Job"];   

    //减去隐藏的双休
    $descHours = 0;
    if($chooseMonth == '2015-10'){
        $hiddenMonthSql = "SELECT checkid FROM $DataIn.disablecheckid WHERE left(checkid, 7)='$Month'"; 
        $hiddenMonthResult = mysql_query($hiddenMonthSql);
        while($hiddenMontRow = mysql_fetch_assoc($hiddenMonthResult)){

            $CheckDate = $hiddenMontRow['checkid'];
            $staff = new AttendanceAvatar($Number, $DataIn, $DataPublic, $link_id);
            $staff->setupAttendanceData($Number, $CheckDate, $DataIn, $DataPublic, $link_id);
            $staff->attendanceSetup($DataIn, $DataPublic, $link_id);

            $dayAttendanceInfomation =  $staff->getInfomationByTag();
            $descHours += $dayAttendanceInfomation["weekOtTime"]>$otherDayOverTimeStarndard?$otherDayOverTimeStarndard:$dayAttendanceInfomation["weekOtTime"];
            //$descHours += $dayAttendanceInfomation["weekZlHours"];
        }
    }
    //2倍加班工时
    if($Month<"2013-05"){
        $XhoursResult=mysql_fetch_array(mysql_query("SELECT xHours,fHours FROM $DataIn.hdjbsheet WHERE Number=$Number and Month='$Month'",$link_id));

        //echo "SELECT xHours,fHours FROM $DataIn.hdjbsheet WHERE Number=$Number and Month='$Month' <br>";

        $Xhours=$XhoursResult["xHours"];
        $FHours=$XhoursResult["fHours"];
    }
    else{
        $Xhours=$myRow["Xhours"];
        $Fhours=$myRow["Fhours"];
    }
    //echo $Month;
    include "../public/kqcode/checkio1_model_zl.php";
    $Dhours=$myRow["Dhours"] == 0?"":$myRow["Dhours"];      //应到工时
    $Whours=$myRow["Whours"] == 0?"":$myRow["Whours"];      //实到工时
    $Ghours=$myRow["Ghours"] == 0?"":$myRow["Ghours"];      //1.5倍工时
    $XhoursReal = $Xhours-$descHours < 0 ? 0 : $Xhours-$descHours;       //2倍工时
    $Xhours = $XhoursReal == 0 ? "": $XhoursReal;

    $Fhours=$Fhours == 0? "" : $Fhours;       //3倍工时
    $InLates=$myRow["InLates"] == 0?"":$myRow["InLates"];    //迟到次数
    $OutEarlys=$myRow["OutEarlys"] == 0?"":$myRow["OutEarlys"];//早退次数
    $SJhours=$myRow["SJhours"] == 0?"":$myRow["SJhours"];    //事假工时
    $BJhours=$myRow["BJhours"] == 0?"":$myRow["BJhours"];    //病假工时
    $BXhours=$myRow["BXhours"] == 0?"":$myRow["BXhours"];    //补休工时 
    $YXJhours=$myRow["YXJhours"] == 0?"":$myRow["YXJhours"];  //有薪假工时:婚、丧等有薪假
    $WXJhours=$myRow["WXJhours"] == 0?"":$myRow["WXJhours"];  //无薪假工时
    $QQhours=$myRow["QQhours"] == 0?"":$myRow["QQhours"];    //缺勤工时
    $WXhours=$myRow["WXhours"] == 0?"":$myRow["WXhours"];    //无效工时
    $KGhours=$myRow["KGhours"] == 0?"":$myRow["KGhours"];    //旷工工时
    $dkhours=$myRow["dkhours"] == 0?"":$myRow["dkhours"];    //有薪工时
    $Estate=$myRow["Estate"];
    $Locks=$myRow["Locks"];



    //*********************************************
    $objPHPExcel->getActiveSheet()->getRowDimension('$Rows')->setRowHeight(20);
    $objPHPExcel->getActiveSheet()->getStyle("A$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->setCellValue("A$Rows", "$Month");
    
    $objPHPExcel->getActiveSheet()->getStyle("B$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->setCellValue("B$Rows", "$Branch");
    
    $objPHPExcel->getActiveSheet()->getStyle("C$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->setCellValue("C$Rows", "$Job");
            
    $objPHPExcel->getActiveSheet()->getStyle("D$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->setCellValue("D$Rows", "$Number");
    
    $objPHPExcel->getActiveSheet()->getStyle("E$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->setCellValue("E$Rows", "$Name");
    
    $objPHPExcel->getActiveSheet()->getStyle("F$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->setCellValue("F$Rows", "$Dhours");
    
    $objPHPExcel->getActiveSheet()->getStyle("G$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->setCellValue("G$Rows", "$Whours");
    
    $objPHPExcel->getActiveSheet()->getStyle("H$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->setCellValue("H$Rows", "$Ghours");
    
    $objPHPExcel->getActiveSheet()->getStyle("I$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->setCellValue("I$Rows", "$Xhours");
    
    $objPHPExcel->getActiveSheet()->getStyle("J$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->setCellValue("J$Rows", "$Fhours");

    $objPHPExcel->getActiveSheet()->getStyle("K$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->setCellValue("K$Rows", "$InLates");

    $objPHPExcel->getActiveSheet()->getStyle("L$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->setCellValue("L$Rows", "$OutEarlys");

    $objPHPExcel->getActiveSheet()->getStyle("M$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->setCellValue("M$Rows", "$SJhours");

    $objPHPExcel->getActiveSheet()->getStyle("N$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->setCellValue("N$Rows", "$BJhours");

    $objPHPExcel->getActiveSheet()->getStyle("O$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->setCellValue("O$Rows", "$YXJhours");

    $objPHPExcel->getActiveSheet()->getStyle("P$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->setCellValue("P$Rows", "$WXJhours");

    $objPHPExcel->getActiveSheet()->getStyle("Q$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->setCellValue("Q$Rows", "$QQhours");

    $objPHPExcel->getActiveSheet()->getStyle("R$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->setCellValue("R$Rows", "$WXhours");

    $objPHPExcel->getActiveSheet()->getStyle("S$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->setCellValue("S$Rows", "$KGhours");

    $objPHPExcel->getActiveSheet()->getStyle("T$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->setCellValue("T$Rows", "$dkhours");

    $i++; 
    $Rows++;
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=Staff.xlsx');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;

?>