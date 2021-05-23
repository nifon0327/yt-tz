<?php   
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	$Id=$checkid[$i];
	if ($Id!=""){
		$Ids=$Ids==""?$Id:($Ids.",".$Id);$y++;
		}
	}


/** Include PHPExcel */
require_once '../plugins/PHPExcel/Classes/PHPExcel.php';
 // Create new PHPExcel object
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

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(14);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);

$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue('A1', '奖金项目');
$objPHPExcel->getActiveSheet()->setCellValue('B1', '部门');
$objPHPExcel->getActiveSheet()->setCellValue('C1', '职位');
$objPHPExcel->getActiveSheet()->setCellValue('D1', '员工ID');
$objPHPExcel->getActiveSheet()->setCellValue('E1', '员工姓名');
$objPHPExcel->getActiveSheet()->setCellValue('F1', '工龄Y(M)');
$objPHPExcel->getActiveSheet()->setCellValue('G1', '计算月份');
$objPHPExcel->getActiveSheet()->setCellValue('H1', '比率参数');
$objPHPExcel->getActiveSheet()->setCellValue('I1', '总金额');
$objPHPExcel->getActiveSheet()->setCellValue('J1', '请款月份');
  
 $objPHPExcel->getActiveSheet()->getStyle( 'A1:J1')->applyFromArray($style_center);
$Rows=2;
$mySql="SELECT S.Id,S.ItemName,B.Name AS Branch,W.Name AS Job,S.Number,P.Name,P.ComeIn,S.Month,S.MonthS,S.MonthE,S.Divisor,S.Rate,S.Amount,S.Estate,S.Locks,S.Date,P.Name AS Operator,F.Idcard,P.Estate AS PEstate  
FROM $DataIn.cw11_jjsheet_frist  S 
LEFT JOIN $DataPublic.branchdata B ON B.Id=S.BranchId 
LEFT JOIN $DataPublic.jobdata W ON W.Id=S.JobId 
LEFT JOIN $DataPublic.staffmain P ON P.Number=S.Number
LEFT JOIN $DataPublic.staffsheet F ON F.Number=S.Number 
WHERE 1  AND S.Id IN ($Ids) ";
$result = mysql_query($mySql,$link_id);
 if($myRow = mysql_fetch_array($result)){
	$i=1;
	$Page=1;
	$SUMQTY=0;
	do{

		$Id=$myRow["Id"];
		$ItemName=$myRow["ItemName"];
		$Branch=$myRow["Branch"];
		$Job=$myRow["Job"];
		$Number=$myRow["Number"];
		$Name=$myRow["Name"];
		$PEstate=$myRow["PEstate"];
		$Month=$myRow["Month"];
		$MonthS=$myRow["MonthS"];
		$MonthE=$myRow["MonthE"];
		$MonthSTR=$MonthS."~".$MonthE;
		$Divisor=$myRow["Divisor"];
        $Idcard=$myRow["Idcard"];
		$Rate=$myRow["Rate"]*100/100;
		$Amount=$myRow["Amount"];
	   $Locks=$myRow["Locks"];
		$Date=$myRow["Date"];
		$ComeIn=$myRow["ComeIn"];
		$chooseMonth="";
	  // include "subprogram/staff_model_gl.php";
$chooseMonth=$chooseMonth==""?date("Y-m"):$chooseMonth;

//工龄计算:参数-入职日期ComeIn、当月chooseMonth  二合一已更新
$ThisEndDay=date("Y-m-t",strtotime($chooseMonth."-01"));	//薪资当月最后一天
$sumY=substr($chooseMonth,0,4)-substr($ComeIn,0,4);			//入职至薪资的年数
$ThisMonth=date("m",strtotime($chooseMonth."-01"));			//薪资当月月份
$CominMonth=date("m",strtotime($ComeIn));					//入职当月月份

//有些员工离职后又回来 add by zx 2013-11-05,staff_read.php:$ComeInYM
$ComeInStr="";
if($ComeInYM!="") {
	$ComeInStr=" AND Month>='$ComeInYM'";
}
//有效扣除工龄月份数
$checkKCGLSql=mysql_query("SELECT SUM(Months) AS kcgl FROM $DataPublic.rs_kcgl WHERE Number=$Number AND Month<='$chooseMonth'  $ComeInStr ",$link_id);
if ($checkKCGLSql){
	if($checkKCGLRow=mysql_fetch_array($checkKCGLSql)){
		$kcgl=$checkKCGLRow["kcgl"];	//需扣除的工龄月份数
		}
}
$kcgl=$kcgl==""?0:$kcgl;
//年计算
if($ThisMonth<$CominMonth){//计薪月份少于进公司月份,有不足年，年数需减1
	$sumY=($sumY-1);
	$sumM=$ThisMonth+12-$CominMonth;
	}
else{
	$sumM=$ThisMonth-$CominMonth;
	}

//月计算
if(date("d",strtotime($ComeIn))==1){
	//$sumM=$sumM+1;
	}
//需扣除的工龄月
if($sumM<=$kcgl && $kcgl>0){
	$sumY=$sumY-1;	//工龄减一年
	$sumM=$sumM+12-$kcgl;
	if ($sumM==12) {
		$sumY=$sumY+1;
		$sumM=0;
		}
	}
else{
	$sumM=$sumM-$kcgl;
	}
//天数工龄
if($sumY==0 && $sumM==2 && $CominMonth!=$ThisMonth){
	$sumD=date("d",strtotime($ThisEndDay))-date("d",strtotime($ComeIn))+1-($kcgl*30);//有效在职天数
	}
//月总天数
$theMonthDays=date("t",strtotime($ThisEndDay));

//转输出字符
$Gl="";
$Gl_STR="";

//用于ipad接口的变量
$glPad = "";

if($sumY==0){
	if($sumM>0){
		$Gl="(".$sumM.")";
		$Gl_STR=$sumM."个月";
		$glPad = $sumM."个月";
		}
	else{
		$Gl_STR="&nbsp;";
		}
	}
else{
	if($sumM>0){
		$Gl=$sumY."(".$sumM.")";
		}
	else{
		$Gl=$sumY;
		}
	}
		$TotalResult=mysql_query("SELECT SUM(Amount) AS Amount,SUM(JfRate) AS  JfRate  FROM $DataIn.cw11_jjsheet    
		                            WHERE ItemName='$ItemName' AND Number='$Number'",$link_id);
		$TotalAmount =mysql_result($TotalResult,0,"Amount");
		$TotalJfRate  =mysql_result($TotalResult,0,"JfRate");
		$TotalAmount=$TotalAmount==""?"&nbsp;":$TotalAmount;
		$TotalJfRate=$TotalJfRate==""?"&nbsp;":$TotalJfRate;

		       $Operator="&nbsp;";
		 switch($ItemName){
			 case "2013年终奖金": $Rate=floor($Rate/100);break;
			 default: $Rate.="%"; break;
		 }
     
        $objPHPExcel->getActiveSheet()->getRowDimension('$Rows')->setRowHeight(20);
        $objPHPExcel->getActiveSheet()->getStyle("A$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->setCellValue("A$Rows", "$ItemName");
		$objPHPExcel->getActiveSheet()->setCellValue("B$Rows", "$Branch");
		$objPHPExcel->getActiveSheet()->setCellValue("C$Rows", "$Job");
		$objPHPExcel->getActiveSheet()->setCellValue("D$Rows", "$Number");
        $objPHPExcel->getActiveSheet()->getStyle("E$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->setCellValue("E$Rows", "$Name");
        $objPHPExcel->getActiveSheet()->getStyle("F$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->setCellValue("F$Rows", "$Gl");
        $objPHPExcel->getActiveSheet()->getStyle("G$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->setCellValue("G$Rows", "$MonthSTR");
        $objPHPExcel->getActiveSheet()->getStyle("H$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->setCellValue("H$Rows", "$Rate");
		$objPHPExcel->getActiveSheet()->setCellValue("I$Rows", "$Amount");
		$objPHPExcel->getActiveSheet()->setCellValue("J$Rows", "$Month");

		$i++; $Rows++;
		}while ($myRow = mysql_fetch_array($result));
 }


header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=jiangjin.xlsx');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
?>