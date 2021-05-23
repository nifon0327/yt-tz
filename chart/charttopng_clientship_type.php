<?php   
/*下单和出货,图表高度固定
分隔线数量和代表金额值自动根据最高值计算//立体柱形图**********电信---yang 20120801
*/

//关闭当前页面的PHP警告及提示信息
 error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING) ; 
 
include "../basic/chksession.php";
include "../basic/parameter.inc";
$CheckMonths=$ChooseMonth;				//要计算的月份数
$CheckMonth=date("Y-m-01");		//当前月第一天
$StartDate=date("Y-m-01",strtotime("$CheckMonth -$CheckMonths month"));//计算的起始日期
$StartMonth=date("Y-m",strtotime("$CheckMonth -$CheckMonths month"));
$StartY=date("Y",strtotime("$StartDate"));
$ToDate=date("Y-m-d");
include "chartgetcolor.php";   //取得相对应类别的颜色
$ClientStr=$ClientStr==""?" AND M.CompanyId='$CID'":$ClientStr;
//$Tile=$Forshort." 月下单、出货金额统计图 ";

//出货或下单最高金额
$TjOut=" AND DATE_FORMAT(M.Date,'%Y-%m')>='$StartMonth'";
$TjIn=" AND DATE_FORMAT(M.OrderDate,'%Y-%m')>='$StartMonth'";
//********************客户颜色
$ClientColor=mysql_fetch_array(mysql_query("SELECT B.ColorCode FROM $DataIn.chart2_color B 
      LEFT JOIN $DataIn.trade_object C ON C.CompanyId=B.CompanyId
      WHERE 1 AND C.CompanyId='$CID' LIMIT 1",$link_id));
$ColorCode=$ClientColor["ColorCode"];
$RGB=hexdec(substr($ColorCode,0,2));
$rColor= hexdec(substr($ColorCode,0,2));
$gColor= hexdec(substr($ColorCode,2,2));
$bColor= hexdec(substr($ColorCode,-2));

//加分类统计的高度
$checkTypeSql=mysql_query("SELECT MT.Name FROM $DataIn.productmaintype MT 
               LEFT JOIN $DataIn.producttype T ON T.mainType=MT.Id
			   LEFT JOIN $DataIn.productdata P ON P.TypeId=T.TypeId
			   WHERE P.CompanyId='$CID' AND T.Estate='1' GROUP BY MT.Id",$link_id);
$TypeNum= mysql_num_rows($checkTypeSql);
$TypeimH=($TypeNum+3)*50;
$imH=$TypeimH+100;								//Y轴高
//********************

$MonthStep=80;										//月份间隔步长
//$imW=($ChooseMonth+1)*$MonthStep+360;	
$imW=(12+1)*$MonthStep+360;							//图像宽度										
$UseFont = "../model/fonts/simhei.ttf"; 			//使用的中文字体
$image = imagecreatetruecolor($imW,$imH);			//输出空白图像
$back = imagecolorallocate($image, 255, 255, 255);	//底图
imagefilledrectangle($image, 0, 0, $imW-1, $imH-1, $back);
////设置字体颜色
$TextBlack = imagecolorallocate($image,0,0,0);
$TextColor = imagecolorallocate($image,$rColor,$gColor,$bColor);
$Gridcolor=imagecolorallocate($image,153,153,153);
$Gridbgcolor=imagecolorallocate($image,221,221,221);
$TextRed= imagecolorallocate($image,255,0,0);
$TextGreen= imagecolorallocate($image,0,204,0);
$TextWhite= imagecolorallocate($image,255,255,255);
$JgColor= imagecolorallocate($image,155,200,255);

//颜色列表
$T1001=imagecolorallocate($image,0,60,121); 	
$F1001=imagecolorallocate($image,0,60,121); 	
$R1001=imagecolorallocate($image,0,60,121);
$T1002=imagecolorallocate($image,111,141,185);		
$F1002=imagecolorallocate($image,111,141,185); 	
$R1002=imagecolorallocate($image,111,141,185);
//$alpha_white=imagecolorallocatealpha($image, 255, 255, 255,50);

//画矩形
$RemarkSql=mysql_query("SELECT * FROM $DataIn.productmaintype ORDER BY Id DESC",$link_id);
if($RemarkRow= mysql_fetch_array($RemarkSql)){
	do{
		$Id=$RemarkRow["Id"];
		$Name=$RemarkRow["Name"];
		$R=$RemarkRow["rColor"];
		$G=$RemarkRow["gColor"];
		$B=$RemarkRow["bColor"];
		$mtColor="mtColor".strval($Id);
		$$mtColor=imagecolorallocate($image,$R,$G,$B);
		}while($RemarkRow= mysql_fetch_array($RemarkSql));
	}
imagesetthickness($image,1);

$TypeimL=50;                      //顶部X:左边距
$TypeimT=50;                      //顶部Y:上边距
$TypeimR=$TypeimL+($imW-280)/2 ;  //底部X:
$TypeimB=$TypeimH;                //底部Y:


$TypeimL_X=$TypeimR+50;          //下单图
$TypeimR_X=$TypeimL_X+($imW-250)/2;
imagefilledrectangle($image,$TypeimR,$TypeimT,$TypeimL_X,$TypeimB+50,$JgColor);

imagerectangle($image,$TypeimL,$TypeimT,$TypeimR,$TypeimB,$TextBlack); 
include "charttopng_clientship_type_1.php";//出货图

imagerectangle($image,$TypeimL_X,$TypeimT,$TypeimR_X,$TypeimB,$TextBlack); 
include "charttopng_clientship_type_2.php";
//输出图像
header("Content-type: image/png");
imagepng($image);
imagedestroy($image);              //释放资源
?>