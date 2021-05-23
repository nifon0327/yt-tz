<?php
//电信-zxq 2013-07-11
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
/** Include PHPExcel */
require_once '../plugins/PHPExcel/Classes/PHPExcel.php';

$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
    $Id=$checkid[$i];
    if ($Id!=""){
        $Ids=$Ids==""?$Id:($Ids.",".$Id);$y++;
    }
}

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
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(45);
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
$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(20);


$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(20);
$objPHPExcel->getActiveSheet()->mergeCells("A1:P1"); 
$objPHPExcel->getActiveSheet()->setCellValue("A1", "人事资料表");
 $objPHPExcel->getActiveSheet()->getStyle( 'A1:P1')->applyFromArray($style_center);
$objPHPExcel->getActiveSheet()->getStyle('A1:P1')->getFont()->setSize(18);

$Rows=2;
$objPHPExcel->getActiveSheet()->getRowDimension("$Rows")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("A$Rows", "工作地点");
$objPHPExcel->getActiveSheet()->setCellValue("B$Rows", "ID");
$objPHPExcel->getActiveSheet()->setCellValue("C$Rows", "姓名");
$objPHPExcel->getActiveSheet()->setCellValue("D$Rows", "身份证");
$objPHPExcel->getActiveSheet()->setCellValue("E$Rows", "部门");
$objPHPExcel->getActiveSheet()->setCellValue("F$Rows", "小组");
$objPHPExcel->getActiveSheet()->setCellValue("G$Rows", "移动电话");
$objPHPExcel->getActiveSheet()->setCellValue("H$Rows", "入职日期");
$objPHPExcel->getActiveSheet()->setCellValue("I$Rows", "在职时间");
$objPHPExcel->getActiveSheet()->setCellValue("J$Rows", "年龄");
$objPHPExcel->getActiveSheet()->setCellValue("K$Rows", "性别");
$objPHPExcel->getActiveSheet()->setCellValue("L$Rows", "籍贯");
$objPHPExcel->getActiveSheet()->setCellValue("M$Rows", "职业健康检查");
$objPHPExcel->getActiveSheet()->setCellValue("N$Rows", "健康检查");
$objPHPExcel->getActiveSheet()->setCellValue("O$Rows", "婚姻状况");
$objPHPExcel->getActiveSheet()->setCellValue("P$Rows", "工衣尺寸");

$objPHPExcel->getActiveSheet()->getStyle( "A$Rows:P$Rows")->applyFromArray($style_center);
$Rows=3;

$staffSql = "SELECT 
    M.Id,M.Number,M.Name,M.Grade,M.Mail,M.AppleID, M.ExtNo,M.ComeIn,M.ContractSDate,M.ContractEDate,M.WorkAdd,M.Introducer,M.Estate,M.Locks,M.Date,M.Operator,
    S.Birthday,S.Sex,S.Rpr,S.Idcard,S.Mobile,S.Dh,S.InFile,M.KqSign,S.Photo,S.IdcardPhoto,S.HealthPhoto,S.DriverPhoto,S.Tel,B.Name AS Branch,J.Name AS Job,IF(M.cSign!=7,OG.GroupName,G.GroupName) AS GroupName,M.IdNum,T.Name AS BloodGroup,S.PassPort,S.PassTicket,DU.Description AS Jobduties,J.WorkNote,AD.Name as WorkAddress, S.Married,S.ClothesSize
    FROM $DataPublic.staffmain M
    LEFT JOIN $DataIn.staffgroup G ON G.GroupId=M.GroupId
    LEFT JOIN $DataOut.staffgroup OG ON OG.GroupId=M.GroupId
    LEFT JOIN $DataPublic.staffsheet S ON S.Number=M.Number
    LEFT JOIN $DataPublic.branchdata B ON B.Id=M.BranchId
    LEFT JOIN $DataPublic.jobdata J ON J.Id=M.JobId
    LEFt JOIN $DataPublic.bloodgroup_type T ON T.ID=S.BloodGroup 
    Left Join $DataPublic.attendance_floor AT On M.AttendanceFloor = AT.Id
    LEFT JOIN $DataPublic.staff_jobduties  DU ON DU.Number=M.Number
    LEFT JOIN $DataPublic.staffworkadd AD ON AD.Id = M.WorkAdd
    WHERE M.Id in ($Ids) AND M.Estate=1 AND M.WorkAdd != 6 and M.JobId != 38 AND M.cSign = 7";

$staffResult = mysql_query($staffSql);
while($staffRow = mysql_fetch_assoc($staffResult)){

    $number = $staffRow['Number'];
    $name = $staffRow['Name'];
    $WorkAdd = $staffRow['WorkAddress'];
    //include_once "../model/subselect/WorkAdd.php";
    $Idcard = ' '.$staffRow['Idcard'];
    $Branch = $staffRow['Branch'];
    $GroupName = $staffRow['GroupName'];
    $Mobile = ' '.$staffRow['Mobile'];
    $ComeIn = $staffRow['ComeIn'];
    $Birthday = $staffRow['Birthday'];
    $Sex=$staffRow["Sex"]==1?"男":"女";
    $Rpr=$staffRow["Rpr"];
    $Married = $staffRow['Married'];
    $ClothesSize=$staffRow["ClothesSize"]==""?"":$staffRow["ClothesSize"];
    $Age = date('Y', time()) - date('Y', strtotime($Birthday)) - 1;
    if (date('m', time()) == date('m', strtotime($Birthday))){
        if (date('d', time()) > date('d', strtotime($Birthday))){
            $Age++;
        }
    }else{
        if (date('m', time()) > date('m', strtotime($Birthday))){
            $Age++;
        }
    }

    //*********************************************籍贯
    $rResult = mysql_query("SELECT Name FROM $DataPublic.rprdata WHERE Estate=1 and Id=$Rpr order by Id",$link_id);
    if ($rResult ){
        if($rRow = mysql_fetch_array($rResult)){
            $Rpr=$rRow["Name"];
        }
    }

    $ComeInYM=substr($ComeIn,0,7);
    include "subprogram/staff_model_gl.php";

    //健康检查
    $NexttjDate = '';
    $tjResult1 = mysql_fetch_array(mysql_query("SELECT Id,tjDate,HG FROM $DataIn.cw17_tjsheet WHERE Number=$number AND tjType<=3 order by Date DESC LIMIT 1",$link_id));
    if($tjResult1){// From MC
        $tjDate=$tjResult1["tjDate"];
        $NexttjDate=date("Y-m-d",strtotime("+1 year",strtotime($tjDate)));
    }else{ //From PT
        $tjResult1 = mysql_fetch_array(mysql_query("SELECT Id,tjDate,HG FROM $DataOut.cw17_tjsheet WHERE Number=$number AND tjType<=3  order by Date DESC LIMIT 1",$link_id));
        if($tjResult1){
            $tjDate=$tjResult1["tjDate"];
            $NexttjDate=date("Y-m-d",strtotime("+1 year",strtotime($tjDate)));
        }
    }

    //职业健康检查
    $NextZgtjDate = '';
    $tjResult1 = mysql_fetch_array(mysql_query("SELECT Id,tjDate,HG FROM $DataIn.cw17_tjsheet WHERE Number=$number AND tjType=4 order by Date DESC LIMIT 1",$link_id));
    if($tjResult1){ //From MC
        $tjDate=$tjResult1["tjDate"];
        $NextZgtjDate=date("Y-m-d",strtotime("+1 year",strtotime($tjDate))); 
    }else{ // From Pt
        $tjResult1 = mysql_fetch_array(mysql_query("SELECT Id,tjDate,HG FROM $DataOut.cw17_tjsheet WHERE Number=$number  AND tjType=4  order by Date DESC LIMIT 1",$link_id));
        if($tjResult1){
            $tjDate=$tjResult1["tjDate"];
            $NextZgtjDate=date("Y-m-d",strtotime("+1 year",strtotime($tjDate)));
        }
    }

    //婚姻状况
    switch($Married){
        case 0:
            $Married = "已婚";
        break;
        case 1:
            $Married = "未婚";
        break;
        case 2:
            $Married = "离异";
        break;
        case 3:
            $Married = "再婚";
        break;
    }

    $objPHPExcel->getActiveSheet()->getRowDimension('$Rows')->setRowHeight(20);
    $objPHPExcel->getActiveSheet()->getStyle("A$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->setCellValue("A$Rows", "$WorkAdd");
    
    $objPHPExcel->getActiveSheet()->getStyle("B$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->setCellValue("B$Rows", "$number");
    
    $objPHPExcel->getActiveSheet()->getStyle("C$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->setCellValue("C$Rows", "$name");
            
    $objPHPExcel->getActiveSheet()->getStyle("D$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->setCellValue("D$Rows", "$Idcard");
    
    $objPHPExcel->getActiveSheet()->getStyle("E$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->setCellValue("E$Rows", "$Branch");
    
    $objPHPExcel->getActiveSheet()->getStyle("F$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->setCellValue("F$Rows", "$GroupName");
    
    $objPHPExcel->getActiveSheet()->getStyle("G$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->setCellValue("G$Rows", "$Mobile");
    
    $objPHPExcel->getActiveSheet()->getStyle("H$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->setCellValue("H$Rows", "$ComeIn");
    
    $Gl_STR = strip_tags($Gl_STR);
    $objPHPExcel->getActiveSheet()->getStyle("I$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->setCellValue("I$Rows", "$Gl_STR");
    
    $objPHPExcel->getActiveSheet()->getStyle("J$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->setCellValue("J$Rows", "$Age");

    $objPHPExcel->getActiveSheet()->getStyle("K$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->setCellValue("K$Rows", "$Sex");

    $objPHPExcel->getActiveSheet()->getStyle("L$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->setCellValue("L$Rows", "$Rpr");

    $objPHPExcel->getActiveSheet()->getStyle("M$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->setCellValue("M$Rows", "$NexttjDate");

    $objPHPExcel->getActiveSheet()->getStyle("N$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->setCellValue("N$Rows", "$NextZgtjDate");

    $objPHPExcel->getActiveSheet()->getStyle("O$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->setCellValue("O$Rows", "$Married");

    $objPHPExcel->getActiveSheet()->getStyle("P$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->setCellValue("P$Rows", "$ClothesSize");
    
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