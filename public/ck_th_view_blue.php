<?php
defined('IN_COMMON') || include '../basic/common.php';

$FilePath="../download/Ckth/";
if(!file_exists($FilePath)){
 makedir($FilePath);
}
$CheckReportFile=$Id.'.pdf';
$filename=$FilePath.$CheckReportFile;

if(!file_exists($filename)){

	$mySql="SELECT M.BillNumber,M.Date,P.Forshort,I.Company,A.Name AS Operator,B.Mobile,I.Address 
			FROM $DataIn.$thTableMain M 
			LEFT JOIN $DataIn.trade_object P ON M.CompanyId =P.CompanyId 
			LEFT JOIN $DataIn.companyinfo I ON I.CompanyId=P.CompanyId AND I.Type=8
			LEFT JOIN $DataIn.staffmain A ON A.Number=M.Operator 
	        LEFT JOIN $DataIn.staffsheet B ON B.Number = A.Number 
			WHERE M.Id='$Id'";
	$j=1;
	$comResult=mysql_query($mySql,$link_id);
	while($comRow=mysql_fetch_array($comResult)){
	     $Date=substr($comRow["Date"],0, 10);

	     $BillNumber=$comRow["BillNumber"];
	     $Company=$comRow["Company"];
	     $Forshort=$comRow["Forshort"];
         $Address=$comRow["Address"];
	     $Operator=$comRow["Operator"];
	     $Mobile=$comRow["Mobile"];
	     $Operator = $Operator."(+86 ".$Mobile.")";
	}



	$CheckMySql=mysql_query("SELECT * FROM $DataIn.my1_companyinfo",$link_id);
	if($CheckMyRow=mysql_fetch_array($CheckMySql)){
		do{
			$Type=$CheckMyRow["Type"];
			$Temp0=strval($Type)."_SealType";$$Temp0=$CheckMyRow["Type"];
			$Temp1=strval($Type)."_Company";$$Temp1=$CheckMyRow["Company"];
			$Temp2=strval($Type)."_Forshort";$$Temp2=$CheckMyRow["Forshort"];
			$Temp3=strval($Type)."_Tel";$$Temp3=$CheckMyRow["Tel"];
			$Temp4=strval($Type)."_Fax";$$Temp4=$CheckMyRow["Fax"];
			$Temp5=strval($Type)."_Address";$$Temp5=$CheckMyRow["Address"];
			$Temp6=strval($Type)."_ZIP";$$Temp6=$CheckMyRow["ZIP"];
			$Temp7=strval($Type)."_WebSite";$$Temp7=$CheckMyRow["WebSite"];
			$Temp8=strval($Type)."_LinkMan";$$Temp8=$CheckMyRow["LinkMan"];
			$Temp9=strval($Type)."_Mobile";$$Temp9=$CheckMyRow["Mobile"];
			$Temp10=strval($Type)."_Email";$$Temp10=$CheckMyRow["Email"];
			}while($CheckMyRow=mysql_fetch_array($CheckMySql));
	}



	$TotalQty=0;
	$Result = mysql_query("SELECT S.StuffId,S.Qty,D.StuffCname,S.Remark
                FROM $DataIn.$thTableSheet S
                LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
                WHERE S.StuffId=D.StuffId AND S.MId='$Id' ORDER BY D.StuffCname",$link_id);
	if($myRow = mysql_fetch_array($Result)){
		$i=1;
		do{
			$Qty=$myRow["Qty"];
			$StuffId=$myRow["StuffId"];
			$StuffCname=$myRow["StuffCname"];
			$Remark=$myRow["Remark"];
			$TotalQty +=$Qty;


			$AppFileJPGPath="../download/stuffIcon/" .$StuffId.".jpg";
			$AppFilePNGPath="../download/stuffIcon/" .$StuffId.".png";

			$AppFilePath ="";
		    if(file_exists($AppFileJPGPath)){
		       $AppFilePath  = $AppFileJPGPath;
		    }else{
		      if(file_exists($AppFilePNGPath)){
		         $im  = imagecreatefrompng($AppFilePNGPath);
		         imagejpeg($im, "../download/stuffIcon/" .$StuffId.'.jpg');
		         $AppFilePath =$AppFileJPGPath;

		      }
		      else{
			      $AppFilePath ="";
		      }
		    }

		    $AppFilePath = $AppFilePath ==""?"../public/checkReport_blue/nopic.jpg":$AppFilePath;

		    $eurAppFileNo="eurAppFileNo".strval($i);
		    $$eurAppFileNo  = $AppFilePath;

			$eurTableNo="eurTableNo".strval($i);
			$$eurTableNo="<table  border=1 >
			<tr >
			<td width=18 align=center valign=middle height=15></td>
			<td width=98 align=left  valign=middle >$StuffCname</td>
			<td width=50 align=left valign=middle >$Remark</td>	
			<td width=18 align=center valign=middle >$Qty</td>
			</tr></table>";
			$i++;
		}while ($myRow = mysql_fetch_array($Result));
	}//end if


	$Counts=$i;  //记录条数
	$eurTableNo="eurTableNo".strval($Counts);


	$$eurTableNo=" 
	<table  border=1 >
	<tr >
	<td width=18  align=center valign=middle height=$RowsHight></td>
	<td width=98  align=left valign=middle ></td>
	<td width=50  align=left valign=middle ></td>
	<td width=18  align=center valign=middle ></td>
	</tr>
	<tr >
	<td width=18  align=center valign=middle height=53></td>
	<td width=98 align=left   valign=middle ></td>
	<td width=60 align=left   valign=middle ></td>
	<td width=18 align=center valign=middle ></td>
	</tr>
	</table>";


	$eurTableNoTotal="<table  border=0 >
	<tr bgcolor=#E8F5FC repeat>
	<td width=18   align=center valign=middle height=7>合计:</td>	
	<td width=98  align=left   valign=middle ></td>
	<td width=50  align=left  valign=middle ></td>			
	<td width=18  align=center  valign=middle color=#FF0000 style=bold >$TotalQty</td>	
	</tr></table>";

	include "Ckth_blue/Ckthmodel.php";
	$pdf->Output("$filename","F");


}



if(file_exists($filename)){
	//文件的类型
	header("Content-type: application/pdf");
	//下载显示的名字
	header("Content-Disposition: attachment; filename=$Id");
	readfile("$filename");
	exit();
}


?>