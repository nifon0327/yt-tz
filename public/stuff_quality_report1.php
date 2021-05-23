<?php
defined('IN_COMMON') || include '../basic/common.php';
$FilePath="../download/CheckReport/";
if(!file_exists($FilePath)){
 makedir($FilePath);
}
$CheckReportFile=$Id.'.pdf';
$filename=$FilePath.$CheckReportFile;

if(!file_exists($filename)){

	$mySql="SELECT S.Date,S.StuffId,S.StockId,S.shQty,S.checkQty,S.Qty,D.StuffCname,P.Company,
	    A.Name AS Operator,M.GysNumber,B.Mobile 
	    FROM $DataIn.qc_badrecord S 
		LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
		LEFT JOIN $DataIn.gys_shmain M ON M.Id=S.shMid 
		LEFT JOIN $DataIn.companyinfo P ON M.CompanyId=P.CompanyId AND P.Type=8
	    LEFT JOIN $DataIn.staffmain A ON A.Number=S.Operator 
	    LEFT JOIN $DataIn.staffsheet B ON B.Number = A.Number 
		WHERE S.Id = '$Id'  ORDER BY S.Date DESC";
	$j=1;
	$comResult=mysql_query($mySql,$link_id);
	while($comRow=mysql_fetch_array($comResult)){
	     $Date=substr($comRow["Date"],0, 10);
	     $StuffId=$comRow["StuffId"];
	     $StockId=$comRow["StockId"];
	     $StuffCname=$comRow["StuffCname"];
	     $GysNumber=$comRow["GysNumber"];
	     $Company=$comRow["Company"];
	     $Qty=$comRow["Qty"];
	     $shQty=$comRow["shQty"];
	     $checkQty=$comRow["checkQty"];
	     $Operator=$comRow["Operator"];
	     $Mobile=$comRow["Mobile"];
	     $Operator = $Operator."(+86 ".$Mobile.")";



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



	$TotalBadQty=0;
	$TotalBadRate = 0.00;
	$Result = mysql_query("SELECT S.Qty,IF(S.CauseId=-1,S.Reason,T.Cause) AS Reason
			 FROM $DataIn.qc_badrecordsheet S 
			 LEFT JOIN $DataIn.qc_causetype T ON T.Id = S.CauseId  
			 WHERE  S.Mid='$Id' ",$link_id);
	if($myRow = mysql_fetch_array($Result)){
		$i=1;
		do{
			$BadQty=$myRow["Qty"];
			$BadReason=$myRow["Reason"];
			$TotalBadQty +=$BadQty;
			$BadRate = sprintf("%.2f",$BadQty/$shQty*100);
			$TotalBadRate+=$BadRate;
			$BadRate =$BadRate."%";

			$eurTableNo="eurTableNo".strval($i);
			$$eurTableNo="<table  border=1 >
			<tr >
			<td width=2 align=center valign=middle height=$RowsHight></td>
			<td width=118 align=left  valign=middle >$BadReason</td>
			<td width=40 align=left valign=middle >$BadQty</td>	
			<td width=24 align=center valign=middle >$BadRate</td>
			</tr></table>";
			$i++;
		}while ($myRow = mysql_fetch_array($Result));
	}//end if

	$GoodRate = 100-$TotalBadRate;
	$GoodRate = $GoodRate."%";
	$TotalBadRate = $TotalBadRate."%";

	$Counts=$i;  //记录条数
	$eurTableNo="eurTableNo".strval($Counts);


	$$eurTableNo=" 
	<table  border=1 >
	<tr >
	<td width=2   align=center valign=middle height=$RowsHight></td>
	<td width=118  align=left valign=middle ></td>
	<td width=40  align=left valign=middle ></td>
	<td width=24  align=center valign=middle ></td>
	</tr>
	<tr >
	<td width=2  align=center valign=middle height=53></td>
	<td width=118 align=left  valign=middle ></td>
	<td width=40 align=left   valign=middle ></td>
	<td width=24 align=center valign=middle ></td>
	</tr>
	</table>";


	$eurTableNoTotal="<table  border=0 >
	<tr bgcolor=#E8F5FC repeat>
	<td width=2   align=center valign=middle height=7></td>	
	<td width=118  align=left   valign=middle >合计:</td>
	<td width=40  align=left  valign=middle >$TotalBadQty</td>			
	<td width=24  align=center  valign=middle color=#FF0000 style=bold >$TotalBadRate</td>	
	</tr></table>";



	include "CheckReport_Blue/CheckReportmodel.php";
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
