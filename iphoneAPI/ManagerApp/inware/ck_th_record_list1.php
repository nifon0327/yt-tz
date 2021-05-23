<?php 
//退货记录
 include "../../basic/downloadFileIP.php";
 
$monthResult=mysql_query("SELECT  S.Id,S.StuffId,S.Qty,S.Remark,M.BillNumber,M.Date,D.StuffCname,D.Picture,P.Forshort,N.Name AS Operator    
			FROM  $DataIn.ck12_thmain M  
			LEFT JOIN $DataIn.ck12_thsheet S ON S.Mid=M.Id  
			LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
			LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
			LEFT JOIN $DataPublic.staffmain N ON N.Number=M.Operator  
			WHERE  DATE_FORMAT(M.Date,'%Y-%m-%d')='$CheckDate' AND D.SendFloor='$Floor'   ORDER BY M.BillNumber,Date DESC ",$link_id);
$dataArray=array();
 $sumQty=0;$sumCount=0;$pos=0;
if($monthRow = mysql_fetch_array($monthResult)) 
  {
        $OldBillNumber=$monthRow["BillNumber"];
        $Forshort=$monthRow["Forshort"];
     do {
           $BillNumber=$monthRow["BillNumber"];
           if ($OldBillNumber!=$BillNumber){
	           $sumArray=array();
	           $sumQty=number_format($sumQty);
	           $tempArray=array(
	                      "Title"=>array("Text"=>"$Forshort","Color"=>"$FORSHORT_COLOR","FontSize"=>"14"),
	                      "Col1"=>array("Text"=>"$OldBillNumber","Margin"=>"30,0,10,0"),
	                      "Col3"=>array("Text"=>"$sumQty($sumCount)","Margin"=>"0,0,10,0")
	                   );
	            $sumArray[]=array("Tag"=>"Total0","data"=>$tempArray); 
	            array_splice($dataArray,$pos,0,$sumArray);
	            $pos=count($dataArray);
	            
	            $OldBillNumber=$BillNumber;
	            $Forshort=$monthRow["Forshort"];
	            $sumQty=0;$sumCount=0;
            }

            $Id=$monthRow["Id"];
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
            
            $Remark=$monthRow["Remark"];
            $Qty=number_format($Qty);
            $tempArray=array(
                       "Id"=>"$BillNumber",
                      "Title"=>array("Text"=>"$StuffId-$StuffCname","Color"=>"$StuffColor","rtIcon"=>"$PropertySTR"),
                      "Col1"=> array("Text"=>"$Qty","Margin"=>"12,0,0,0"),
                      //"Col2"=> array("Text"=>"$Qty"),
                      //"Col3"=>array("Text"=>"$Qty","Color"=>"$qtyColor"),
                      "Col5"=>array("Text"=>"$Operator"),
                      "Remark"=>array("Text"=>"$Remark"),
                      //"Process"=>$ProcessArray 
                );
                
            $qc_html="/iphoneAPI/ManagerApp/inware/ck_th_report.php?Id=$BillNumber";
            $dataArray[]=array("Tag"=>"data","onTap"=>array("NavTitle"=>"$BillNumber","Target"=>"WebPrint","Args"=>"$qc_html"),"onEdit"=>"0","Estate"=>"0","data"=>$tempArray);
             $sumCount++;
      } while($monthRow = mysql_fetch_array($monthResult));
      
      $sumArray=array();
	           $sumQty=number_format($sumQty);
	            $tempArray=array(
	                      "Title"=>array("Text"=>"$Forshort","Color"=>"$FORSHORT_COLOR","FontSize"=>"14"),
	                      "Col1"=>array("Text"=>"$BillNumber","Margin"=>"30,0,10,0"),
	                      "Col3"=>array("Text"=>"$sumQty($sumCount)","Margin"=>"0,0,10,0")
	                   );
	            $sumArray[]=array("Tag"=>"Total0","data"=>$tempArray); 
	            array_splice($dataArray,$pos,0,$sumArray);
  }
  
if ($FromPage!="Read"){
	    $jsonArray=$dataArray;
}

?>
