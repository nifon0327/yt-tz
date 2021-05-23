<?php
defined('IN_COMMON') || include '../basic/common.php';
/*
已更新电信---yang 20120801
*/
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
echo"<link rel='stylesheet' href='../model/css/sharing.css'>";
//读取客户，产品ID，产品中文名，产品英文名，包装方式
$checkProduct=mysql_fetch_array(mysql_query("
SELECT P.ProductId,P.cName,P.eCode,T.mainType,P.Remark,C.Forshort,M.StartPlace,M.EndPlace,M.Address
FROM $DataIn.productdata P
LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId 
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId 
LEFT JOIN $DataIn.ch8_shipmodel M ON M.CompanyId=P.CompanyId
WHERE P.Id='$Id' LIMIT 1",$link_id));
$ProductId=$checkProduct["ProductId"];
$cName=$checkProduct["cName"];
$eCode=$checkProduct["eCode"];
$mainType=$checkProduct["mainType"];
$Remark=$checkProduct["Remark"];
$Forshort=$checkProduct["Forshort"];
$StartPlace=$checkProduct["StartPlace"];
$EndPlace=$checkProduct["EndPlace"];
$Address=$checkProduct["Address"];
/*
echo "
SELECT P.ProductId,P.cName,P.eCode,T.mainType,P.Remark,C.Forshort,M.StartPlace,M.EndPlace,M.Address
FROM $DataIn.productdata P
LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
LEFT JOIN $DataIn.ch8_shipmodel M ON M.CompanyId=P.CompanyId
WHERE P.Id='$Id' LIMIT 1 <br>";
*/
//读取数据库
$checkStuff=mysql_query("
SELECT D.StuffCname,D.TypeId,D.Spec,A.StuffId,A.Relation
FROM $DataIn.pands A 
LEFT JOIN $DataIn.stuffdata D ON D.StuffId=A.StuffId
WHERE A.ProductId='$ProductId'  ORDER BY A.Id",$link_id);
//读取操作员
/*
echo "SELECT D.StuffCname,D.TypeId,D.Spec,A.StuffId,A.Relation
FROM $DataIn.pands A
LEFT JOIN $DataIn.stuffdata D ON D.StuffId=A.StuffId
WHERE A.ProductId='$ProductId'  ORDER BY A.Id <br>";
*/
$checkStaff=mysql_fetch_array(mysql_query("SELECT Name,Nickname FROM $DataPublic.staffmain WHERE Number='$Login_P_Number'",$link_id));
//echo "SELECT Name,Nickname FROM $DataPublic.staffmain WHERE Number='$Login_P_Number'";
$StaffName=$checkStaff["Name"];
$Nickname=$checkStaff["Nickname"];

$filename="ts_model_1.pdf";
 if(file_exists($filename)){unlink($filename);}
define('FPDF_FONTPATH','../plugins/fpdf/font/');
require('../plugins/fpdf/chinese-unicode.php');


$pdf=new PDF_Unicode();
$pdf->SetAutoPageBreak("on",10);
$pdf->SetMargins(10,10,10);
$pdf->Open();
$pdf->AddPage("L");
$pdf->FloorSign=0;
//初始化参数
$DefalutL=3;
$pdf->SetFillColor(255,0,51);//填充色
//$pdf->SetDrawColor(255,0,51);
///////////////////////////////////////////////////////
$pdf->Setxy(10,25);			//定位光标
//$pdf->Rect($DefalutL,$DefalutL,35,8,"F"); 	//填充矩形:X,Y,W,H

$pdf->AddUniGBhwFont('uGB');
$pdf->SetFont('uGB','B',10);
/*  ---------------------------
$pdf->Rect($DefalutL,$DefalutL,35,11,"F"); 	//填充矩形:X,Y,W,H
$Title="检验标准图";
$TitleL=$pdf->GetStringWidth($Title);
$pdf->SetTextColor(255,255,255);
$pdf->Text($DefalutL+2,7,$Title);		//输出文字：X,Y
$Title="Specification";
$pdf->Text($DefalutL+2,12,$Title);		//输出文字：X,Y
*/
//echo "1111111111 <br>";

$HeaderJpg="../model/standardjpg/Header.jpg";  //标题
$pdf->Image($HeaderJpg,$DefalutL,$DefalutL,35,11,"JPG");
//echo "222222222222222222 <br>";

$pdf->SetTextColor(0,0,0);
$pdf->SetFont('uGB','',8);
$pdf->Text($DefalutL+48,7,"produced by:$Nickname");
//$pdf->Text($DefalutL+48,8,"制图:$StaffName");

$Date=date("Y-m-d");
$pdf->Text($DefalutL+48,12,"date:$Date");
//$pdf->Text($DefalutL+48,11,"日期:$Date");
$pdf->Text($DefalutL+265,$DefalutL,"No.:$ProductId-$Forshort");
/*   ----------------------
/////////////////////////////////////////////////////////
$pdf->SetLineWidth(0.3);
//$pdf->Rect($DefalutL,97,70,110,"F");	//红底
$pdf->Rect($DefalutL,85,70,112,"F");	//红底
//$pdf->Rect($DefalutL,12,70,195,"D");	//外矩形
$pdf->Rect($DefalutL,15,70,194,"D");	//外矩形
*/
$main_rectJpg="../model/standardjpg/main_rect.jpg";  //主框
$pdf->Image($main_rectJpg,$DefalutL,15,70.3,181.7,"JPG");

$pdf->SetFont('uGB','',11);

//$pdf->Text($DefalutL+2,20,"配件资料 (Parts List)");
//$pdf->Line($DefalutL+23,12,$DefalutL+23,18);		//竖分隔线:X1,Y1,X2,Y2
//$pdf->Line($DefalutL+23,18,73,18);
//$pdf->Text($DefalutL+27,17,"编号:$ProductId-$Forshort");
//echo "12345678 <br>";
$number=@mysql_num_rows($checkStuff);
if($number<11){
	$FontSize=11; $FontHeight=5;
	}
else{
	$FontSize=9; $FontHeight=4;
	}
if($checkStuffRow=mysql_fetch_array($checkStuff)){
	$pdf->SetFont('uGB','',$FontSize);
	$i=1;
	do{
		$StuufId=$checkStuffRow["StuffId"];
		$TypeId=$checkStuffRow["TypeId"];
		$Spec=$checkStuffRow["Spec"];
		if($i<10){
			$StuffCname=" ".$i."-".$StuufId." ".$checkStuffRow["StuffCname"];
			}
		else{
			$StuffCname=$i."-".$StuufId." ".$checkStuffRow["StuffCname"];
			}
		//$pdf->Text($DefalutL,17+$i*$FontHeight,$StuffCname);
		$pdf->Text($DefalutL,23+$i*$FontHeight,$StuffCname);
		if($TypeId==9040){
			$Field=explode("CM",strtoupper($Spec));
			$SpecArray=explode("*",$Field[0]);
			$LabelBox=$Field[0]."CM";
			$L=$SpecArray[0];
			$W=$SpecArray[1];
			$H=$SpecArray[2];
			}
		$i++;
		}while($checkStuffRow=mysql_fetch_array($checkStuff));
	}

///////////////////////////////////////////////////////////////////////////
//-------------$pdf->Line($DefalutL,83,73,83);
/*
$pdf->Text($DefalutL,88,"中文名:$cName");
$pdf->Line($DefalutL,90,73,90);
$pdf->Text($DefalutL,95,"英文名:$eCode");
$pdf->Line($DefalutL,97,73,97);
*/
///////////////////////////////////////////////////////////////////////////
//echo "!!!12345678 <br>";
$yup=-12;   //统一微调位置！
$pdf->SetFillColor(255,255,255);
/* ------------------------
$pdf->Rect($DefalutL+1,110+$yup,68,35,"F");
$pdf->Rect($DefalutL+1,147+$yup,68,51,"F");
*/
//////////////////////////////////////////
//画外箱简图
$pdf->SetDrawColor(0,0,0);
$pdf->SetLineWidth(0.2);
$yTemp=51+$yup;
$y=-5;
//$TempL=70;
$TempL=72;
/* ------------------------------------------
$pdf->Line(86-$TempL,70+$yTemp,86-$TempL,85+$yTemp);
$pdf->Line(86-$TempL,85+$yTemp,106-$TempL,96+$yTemp+$y);
$pdf->Line(106-$TempL,96+$yTemp+$y,130-$TempL,80+$yTemp-$y);
$pdf->Line(130-$TempL,80+$yTemp-$y,130-$TempL,65+$yTemp-$y);
$pdf->Line(130-$TempL,65+$yTemp-$y,110-$TempL,59+$yTemp-$y);
$pdf->Line(110-$TempL,59+$yTemp-$y,86-$TempL,70+$yTemp);
$pdf->Line(86-$TempL,70+$yTemp,106-$TempL,81+$yTemp+$y);
$pdf->Line(106-$TempL,81+$yTemp+$y,130-$TempL,65+$yTemp-$y);
$pdf->Line(106-$TempL,81+$yTemp+$y,106-$TempL,96+$yTemp+$y);
$pdf->Line(96-$TempL,73+$yTemp,120-$TempL,67+$yTemp);
$pdf->Line(86-$TempL,74+$yTemp,90-$TempL,75.5+$yTemp);
$pdf->Line(90-$TempL,75.5+$yTemp,90-$TempL,71.5+$yTemp);
*/
$pdf->SetFont('uGB','B',10);
//$pdf->SetTextColor(255,0,51);
$pdf->Text(116-$TempL,105+$yTemp,"L:".$L."CM");
$pdf->Text(91-$TempL,105+$yTemp,"W:".$W."CM");
$pdf->Text(103-$TempL,99+$yTemp,"H:".$H."CM");
//$pdf->Text(124-$TempL,64+$yTemp,"单重:   kg");

//标签说明
$pdf->SetFont('uGB','B',10);
//$pdf->SetTextColor(255,255,255);
//$pdf->Text($DefalutL+2,62.5+$yTemp,"外箱尺寸:$LabelBox");
//$pdf->Text($DefalutL+2,67.5+$yTemp,"包装要求:$Remark");
$pdf->Text($DefalutL+29,63+$yTemp,"$LabelBox");
$pdf->Text($DefalutL+29,68+$yTemp,"$Remark");

$pdf->SetFont('uGB','B',8);
//$pdf->Text($DefalutL+2,202+$yup,"       注意:标签尺寸100*115mm");
//$pdf->Text($DefalutL+2,206+$yup,"            标签贴在外箱短侧面左上角");
//////////////////////////////////////////////////

$TempL=131;
$TempB=111+$yup;
//画标签图
$pdf->SetLineWidth(0.3);
$pdf->SetFillColor(0,0,0);
$pdf->SetDrawColor(0,0,0);
/* -----------------------
$pdf->Rect(136-$TempL,37+$TempB,66,49,"D");
//内部横线
//$pdf->Line(136-$TempL,45+$TempB,202-$TempL,45+$TempB);
$pdf->Line(136-$TempL,43+$TempB,169-$TempL,43+$TempB);
$pdf->Line(136-$TempL,48+$TempB,202-$TempL,48+$TempB);
//$pdf->Line(136-$TempL,55+$TempB,165-$TempL,55+$TempB);
$pdf->Line(136-$TempL,53+$TempB,169-$TempL,53+$TempB);

$pdf->Line(169-$TempL,61+$TempB,202-$TempL,61+$TempB);

//$pdf->Line(136-$TempL,70+$TempB,202-$TempL,70+$TempB);
$pdf->Line(136-$TempL,68+$TempB,169-$TempL,68+$TempB);
//$pdf->Line(136-$TempL,77+$TempB,162-$TempL,77+$TempB);
$pdf->Line(136-$TempL,77+$TempB,202-$TempL,77+$TempB);

//内部竖线
//$pdf->Line(169-$TempL,37+$TempB,169-$TempL,45+$TempB);
$pdf->Line(169-$TempL,37+$TempB,169-$TempL,77+$TempB);
//$pdf->Line(175-$TempL,45+$TempB,175-$TempL,50+$TempB);
//$pdf->Line(165-$TempL,50+$TempB,165-$TempL,70+$TempB);
//$pdf->Line(162-$TempL,70+$TempB,162-$TempL,86+$TempB);
*/
//标签内文本
$pdf->SetFont('uGB','B',7);
//$pdf->SetTextColor(0,0,0);
/* ------------------------------
$pdf->Text(170-$TempL,44+$TempB,"BOX         OF");
//$pdf->Text(137-$TempL,49+$TempB,"MOD NO:");
$pdf->Text(137-$TempL,46+$TempB,"Date:");
//$pdf->Text(177-$TempL,49+$TempB,"Date:");

$pdf->Text(137-$TempL,51+$TempB,"Qty:");
$pdf->Text(137-$TempL,56+$TempB,"CENIMETERS:");
$pdf->Text(137-$TempL,64+$TempB,"GW:             KGS");
$pdf->Text(137-$TempL,67+$TempB,"NW:             KGS");
$pdf->Text(137-$TempL,72+$TempB,"P/O NO:");
$pdf->Text(137-$TempL,76+$TempB,"INVOICE:");
*/
//$pdf->Text(137-$TempL,80+$TempB,"TRACKING NO:");

//对本标签应酬变项目
$pdf->SetFont('uGB','B',10);
//$pdf->SetTextColor(0,153,0);
//$pdf->Text(176-$TempL,44+$TempB,"箱号   总箱数");	//1、箱号、箱数
$pdf->SetFont('uGB','B',8);
//$pdf->Text(145-$TempL,46+$TempB,"英文出货日期");	//2、出货日期
//$pdf->Text(145-$TempL,51.5+$TempB,"装箱数量");
//$pdf->Text(145-$TempL,64+$TempB,"毛重");		 	//3、毛重
//$pdf->Text(145-$TempL,67+$TempB,"净重");			//4、净重
//$pdf->Text(147-$TempL,72+$TempB,"订单PO号"); 	//5、PO
//$pdf->Text(149-$TempL,76+$TempB,"Invoice编号"); //6、INVOICE
//$pdf->Text(137-$TempL,83.5+$TempB,"Invoice编号"); //6、INVOICE

//$pdf->SetTextColor(0,0,204);//蓝色,从数据库中读取
$pdf->SetFont('uGB','B',7);
//-----$pdf->Text(137-$TempL,40.5+$TempB,$StartPlace);//1、发货公司
//$pdf->Text(147-$TempL,49+$TempB,$eCode);//2、英文名
//------$pdf->Text(145-$TempL,53+$TempB,$Remark);//3、数量
//-------$pdf->Text(170-$TempL,52+$TempB,"SHIP TO");//5、收货公司
$pdf->SetFont('uGB','B',6);
$pdf->Text(169-$TempL,70.5+$TempB,$EndPlace);//5、收货公司
$pdf->SetFont('uGB','B',6);
//----$pdf->Text(137-$TempL,60+$TempB,$LabelBox);//4、外箱尺寸
$pdf->Text(137-$TempL,74+$TempB,$LabelBox);//4、外箱尺寸

$pdf->SetFont('uGB','B',14);
$pdf->Text(160-$TempL,91.5+$TempB,$eCode);

/*
switch($mainType){
	case 1:
		break;
	case 2:
		break;
	case 3:
		break;
	case 4:
		break;
	case 5:
		break;
	case 6:
		break;
	default:
		break;

}
*/
$yup=112;
$mainTypeJpg="../model/standardjpg/mainType$mainType.jpg";  //标题
//$="../images/officialseal$E_SealType.jpg";  //Type:S简体E英C繁体
$pdf->Image($mainTypeJpg,$DefalutL,86+$yup,70.3,10,"JPG");


$pdf->SetFont('uGB','B',9);
//$pdf->Line($DefalutL,87+$yup,73,87+$yup);
//$pdf->Line($DefalutL,90+$yup,73,90+$yup);
//$pdf->Text($DefalutL+70,95+$yup,"$eCode");  //英文名
//$pdf->Line($DefalutL,97,73,97);
$pdf->SetFont('uGB','B',6);
$pdf->Text($DefalutL+73,95+$yup,"$eCode");  //英文名
$Title="$cName";
$TitleL=$pdf->GetStringWidth($Title);
$pdf->Text(290-$TitleL,95+$yup,$Title);  //中文名


$pdf->Output("$filename","F");
$Log="<p><a href='ts_model_1.pdf' target='_blank'>$cName 的检验标准图已下成，点击下载</a>";
//echo "12345667676!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!SSSSS";
//步骤4：
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
