<?php 
//配件退换审核
include "../../basic/downloadFileIP.php";

 $mySql="SELECT S.Id,S.Qty,S.StuffId,M.Date,S.Remark,M.Operator,M.OPdatetime,A.StuffCname,A.Price,A.Picture,C.PreChar,U.Name AS UnitName,P.Forshort,S.Picture AS thPicture,S.Id AS thId  
         FROM $DataIn.ck2_thsheet S 
         LEFT JOIN $DataIn.ck2_thmain M ON M.Id=S.Mid 
         LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId 
         LEFT JOIN $DataPublic.stuffunit U ON U.Id=A.Unit 
         LEFT JOIN $DataIn.stufftype T ON A.TypeId=T.TypeId 
         LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
         LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency 
         WHERE  S.Estate=2  ORDER BY S.Id ";
 $Result=mysql_query($mySql,$link_id);

 $Dir= "$donwloadFileIP/download/stufffile/";
 $ImgDir="$donwloadFileIP/download/thimg/";
 while($myRow = mysql_fetch_array($Result)) 
 {
            $Id=$myRow["Id"];
            $Forshort=$myRow["Forshort"];
            $StuffCname=$myRow["StuffCname"];//配件名称
            $Qty=$myRow["Qty"];    //退换数量
            $Price=sprintf("%.2f",$myRow["Price"]);
            $PreChar=$myRow["PreChar"];
            $Date=$myRow["Date"];
            $Remark=$myRow["Remark"];

            $Operator=$myRow["Operator"];
            include "../../model/subprogram/staffname.php";
            
            $Qty=number_format($Qty);
	        $StuffId=$myRow["StuffId"];
	         $Picture=$myRow["Picture"];
	         $ImageFile=$Picture>0?"$Dir".$StuffId. "_s.jpg":"";
	         include "submodel/stuffname_color.php";
            
            //退换图片
	        $thPicture=$myRow["thPicture"];
	        $thId=$myRow["thId"];
	        $thImageFile=$thPicture==1?"$ImgDir" . "T$thId.jpg":"";
			
		    $sumQty+=$Qty;
		    $Amount=sprintf("%.2f",$Qty*$Price);
		    $sumAmount+=$Amount*$Rate;
		    $Amount=number_format($Amount,2);

             $OPdatetime=$myRow["OPdatetime"];
             //$Date=date("m-d H:i",strtotime($OPdatetime));
             $Date=GetDateTimeOutString($OPdatetime,'');
		    $opHours= geDifferDateTimeNum($OPdatetime,"",1);
		    if ($opHours>=$timeOut[0]) {$OverNums++;$DateColor="#FF0000";} else $DateColor="";
           
           $onTap=$Picture>0?1:0;
           $dataArray[]=array(
	                     "Id"=>"$Id",
	                     "onTap"=>array("Value"=>"$onTap","hidden"=>"1","Args"=>"$Id","Audit"=>"$AuditSign"),
	                     "Title"=>array("Text"=>"$StuffId-$StuffCname","Color"=>"$StuffColor"),
	                     "Col1"=>array("Text"=>"$Forshort"),
	                     "Col2"=>array("Text"=>"$Qty"),
	                     "Col3"=>array("Text"=>"$PreChar$Price"),
	                     "Col4"=>array("Text"=>"$PreChar$Amount"),
	                     "Remark"=>array("Text"=>"$Remark"),
	                     "Date"=>array("Text"=>"$Date","Color"=>"$DateColor"),
	                     "Operator"=>array("Text"=>"$Operator"),
	                      "List"=>array("Value"=>"$thPicture","Type"=>"JPG","ImageFile"=>"$thImageFile","data"=>array())
                     );
 }

?>