<?php 
//退货记录
 include "../../basic/downloadFileIP.php";
 $imgBase = "http://www.ashcloud.com/download/qcbadpicture/";
// Q"."$Sid".".jpg"
$searCompany = "";
if ($andCompany && strlen($andCompany)>0) {
	$searCompany = " AND M.BillNumber=$andCompany ";
}
$monthResult=mysql_query("SELECT  S.Id,S.StuffId,S.Qty,S.Remark,M.BillNumber,M.Date,D.StuffCname,D.Picture,P.Forshort,N.Name AS Operator  ,S.Bid ,P.CompanyId 
			FROM  $DataIn.ck12_thmain M  
			LEFT JOIN $DataIn.ck12_thsheet S ON S.Mid=M.Id  
			LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
			LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
			LEFT JOIN $DataPublic.staffmain N ON N.Number=M.Operator  
			WHERE  DATE_FORMAT(M.Date,'%Y-%m-%d')='$CheckDate' AND D.SendFloor='$Floor' $searCompany  ORDER BY M.BillNumber,Date DESC ",$link_id);
$dataArray=array();
 $sumQty=0;$sumCount=0;$pos=0;
if($monthRow = mysql_fetch_array($monthResult)) 
  {
        $OldBillNumber=$monthRow["BillNumber"];
        $Forshort=$monthRow["Forshort"];
        $sCompanyId = $monthRow["CompanyId"];
     do {
           $BillNumber=$monthRow["BillNumber"];
           if ($OldBillNumber!=$BillNumber){
	           $sumArray=array();
	           $sumQty=number_format($sumQty);
	         
	         $signedEstate = 0;
	         $signedDate = $signedPerson = "";
	         $checkSignEstate = mysql_query("select S.Estate,date_format( S.Date,'%Y/%m/%d') Date,M.Name from $DataIn.ck12_thsignature S
	         left join $DataPublic.staffmain M on M.Number=S.Operator
	         where S.BillNumber='$OldBillNumber' order by S.Id desc limit 1
	         ");
	         if ($checkSignEstateRow = mysql_fetch_array($checkSignEstate)) {
		         $signedEstate = $checkSignEstateRow["Estate"];
		         $signedDate = $checkSignEstateRow["Date"];
                 $signedPerson = $checkSignEstateRow["Name"];		         
	         }
	         $addedRow = array();
	         $signonEdit = 100 ;
	         if ($signedEstate > 1 ) {
		             $addedRow= array(
					                     array("Text"=> $signedEstate==2?"已退回":"已销毁","Copy"=>"Title","Color"=>$signedEstate==2?"#00AA00":"#FF0000"),
					                     array("Text"=>"$signedPerson","Copy"=>"Col_1"),
					                     array("Text"=>"$signedDate","Copy"=>"Col_3"),
					                     );
					                     $signonEdit = 0;
	         }
	         
	           
	           $tempArray=array('can_edit'=>'1',
	                      "new_tap"=>array("Value"=>"1","Args"=>"List1|$CheckDate|$OldBillNumber"),"Title"=>array("Text"=>"$Forshort","Color"=>"$FORSHORT_COLOR","FontSize"=>"14"),
	                      "Col1"=>array("Text"=>"$OldBillNumber","Margin"=>"30,0,10,0"),
	                      "Col3"=>array("Text"=>"$sumQty($sumCount)","Margin"=>"-10,0,10,0"),
	                         "AddRow"=>$addedRow
	                   );
	                      if ($andCompany && strlen($andCompany)>0) {
		                      $sumArray = array();
		            } else 
	            $sumArray[]=array("Tag"=>"Total0","data"=>$tempArray,"onTap"=>"1","hidden"=>"1",'onEdit'=>"$signonEdit"); 
	         
	            array_splice($dataArray,$pos,0,$sumArray);
	            $pos=count($dataArray);
	            
	            $OldBillNumber=$BillNumber;
	            $Forshort=$monthRow["Forshort"];
	                $sCompanyId = $monthRow["CompanyId"];
	            $sumQty=0;$sumCount=0;
            }
            
            
            
            
            
            
            

            $Id=$monthRow["Id"];
            
               $sBid=$monthRow["Bid"];
            $StuffId=$monthRow["StuffId"];
            $StuffCname=$monthRow["StuffCname"];
            $Qty=$monthRow["Qty"];
            $sumQty+=$Qty;
            $Picture=$monthRow["Picture"];
            
            $ImagePath=$Picture>0?"$donwloadFileIP/download/stufffile/".$StuffId. "_s.jpg":"";
            include "submodel/stuffname_color.php";

            $Operator=$monthRow["Operator"];
            
            $PropertySTR=""; 
		     if ($StuffId==114133 || $StuffId==127622 || $StuffId==129301 || $StuffId==126088 ){
			        $PropertySTR="c1";
			 }
		    else{
			   $PropertyResult=mysql_query("SELECT Property FROM $DataIn.stuffproperty WHERE StuffId='$StuffId' ORDER BY Property",$link_id);
		       while($PropertyRow=mysql_fetch_array($PropertyResult)){
		                $Property=$PropertyRow["Property"];
		                  if($Property>0) $PropertySTR.=$PropertySTR==""?$Property:"|$Property";
		        }
            }
            
            
            $FileArr = array();
             $CheckReason=mysql_query("SELECT B.CauseId,B.Reason,T.Cause  ,B.Qty,B.Id ,
            B.Picture 
                         FROM $DataIn.qc_badrecordsheet B 
                         LEFT JOIN $DataIn.qc_causetype T ON T.Id=B.CauseId  
                         WHERE B.Mid='$sBid' order by CauseId",$link_id);    
                         
                         $iterS = 1;   
$RemarkText = "";
   $qc_html="/iphoneAPI/ManagerApp/inware/ck_th_report.php?Id=$BillNumber";
   
    $FileArr[]=array("Type"=>"html",
				        "url"=>"http://www.ashcloud.com"."$qc_html",
				        "url_thumb"=>"http://www.ashcloud.com"."$qc_html",
				        'title'=>" 退料单",
				        "NavTitle"=>"退料单 $BillNumber",
				        "sType"=>"html",
				        "shareUrl"=>"1");

   
   
        	while($ReasonRow = mysql_fetch_array($CheckReason)){
	        	
	        	$ssid = $ReasonRow["Id"];
			         $Reason=$ReasonRow["CauseId"]==-1?$ReasonRow["Reason"]:$ReasonRow["Cause"];
			         $badReasonQty = $ReasonRow["Qty"];
			         $RemarkText.="\n$iterS." . $Reason."-$badReasonQty".'pcs';
			         
			         $caused = $ReasonRow["Cause"]==""?$ReasonRow["Reason"]:$ReasonRow["Cause"];
			         $hasBill = $ReasonRow["Picture"];
			         $badReasons[]=array($caused,$badReasonQty);
			            if ($hasBill>0) {
				        $FileArr[]=array("Type"=>"img",
				        "url"=>"$imgBase"."Q$ssid".".jpg",
				        "url_thumb"=>"$imgBase"."Q$ssid".".jpg",
				        'title'=>" $Reason",
				        "sType"=>"jpg");
			        }
			        $iterS ++;
			}

            
            $Remark=$monthRow["Remark"];
            $Qty=number_format($Qty);
            $tempArray=array(
                       "Id"=>"$BillNumber",'has2'=>'1',
                      "Title"=>array("Text"=>"$StuffId-$StuffCname","Color"=>"$StuffColor","rtIcon"=>"$PropertySTR"),
                      "Col1"=> array("Text"=>"$Qty","Margin"=>"12,0,0,0"),
                      //"Col2"=> array("Text"=>"$Qty"),
                      //"Col3"=>array("Text"=>"$Qty","Color"=>"$qtyColor"),
                      "Col5"=>array("Text"=>"$Operator"),
                      //"Process"=>$ProcessArray 
                );
                
         if ($andCompany && strlen($andCompany)>0) {
	                    $dataArray[]=array("Tag"=>"data","onTap"=>array("NavTitle"=>"$BillNumber","Target"=>"WebPrint","Args"=>"$qc_html"),"onEdit"=>"0","Estate"=>"0","data"=>$tempArray);
            
            
                $dataArray[]=array("Tag"=>"remark1",
						     	"RID" => $RemarkText==""?$RemarkText:"-1",
						   	"Record" => "$RemarkText",
						   	"Recorder" => "",
						   	"anti_oper"=>"",'size_img'=>'60',
						   	"headline"=>"不良原因：",
						   	 	"Files"=>"1",
						   	 	"FileArray"=>$FileArr,"needReason"=>'0',
						   	 	'nooper'=>"1",
						   	'left_sper'=>"15","margin_left"=>"15"
						   	);

         }
 
             $sumCount++;
      } while($monthRow = mysql_fetch_array($monthResult));
      
      $sumArray=array();
	           $sumQty=number_format($sumQty);
	           
	           	         
	         $signedEstate = 0;
	         $signedDate = $signedPerson = "";
	         $checkSignEstate = mysql_query("select S.Estate,date_format( S.Date,'%Y/%m/%d') Date,M.Name from $DataIn.ck12_thsignature S
	         left join $DataPublic.staffmain M on M.Number=S.Operator
	         where S.BillNumber='$BillNumber' order by S.Id desc limit 1
	         ");
	         if ($checkSignEstateRow = mysql_fetch_array($checkSignEstate)) {
		         $signedEstate = $checkSignEstateRow["Estate"];
		         $signedDate = $checkSignEstateRow["Date"];
$signedPerson = $checkSignEstateRow["Name"];		         
	         }
	         $addedRow = array();
	          $signonEdit = 100 ;
	         if ($signedEstate > 1 ) {
		             $addedRow= array(
					                     array("Text"=> $signedEstate==2?"已退回":"已销毁","Copy"=>"Title","Color"=>$signedEstate==2?"#00AA00":"#FF0000"),
					                     array("Text"=>"$signedPerson","Copy"=>"Col_1"),
					                     array("Text"=>"$signedDate","Copy"=>"Col_3"),
					                     );
					                      $signonEdit = 0;
					                      
					 // shijian jing gang hao keyi shuijiao 
	         }
	         
	           
	           
	            $tempArray=array( "new_tap"=>array("Value"=>"1","Args"=>"List1|$CheckDate|$BillNumber"),
	                      "Title"=>array("Text"=>"$Forshort","Color"=>"$FORSHORT_COLOR","FontSize"=>"14"),
	                      "Col1"=>array("Text"=>"$BillNumber","Margin"=>"30,0,10,0"),
	                      "Col3"=>array("Text"=>"$sumQty($sumCount)","Margin"=>"-10,0,10,0"),'can_edit'=>'1',
	                      "AddRow"=>$addedRow
	                   );
	                     if ($andCompany && strlen($andCompany)>0) {} else {
		                     $sumArray[]=array("Tag"=>"Total0","data"=>$tempArray,"onTap"=>"1","hidden"=>"1",'onEdit'=>"$signonEdit"); 
	            
	            array_splice($dataArray,$pos,0,$sumArray);
	                     }
	            
  }
  
if ($FromPage!="Read"){
	    $jsonArray=$dataArray;
}

?>
