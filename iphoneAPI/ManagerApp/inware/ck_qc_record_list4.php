<?php 
//抽检、全检
 if ($Floor=="") $Floor=$dModuleId==2152?3:6;
 include "../../basic/downloadFileIP.php";
 
$monthResult=mysql_query("SELECT  B.Id,B.StuffId,B.shQty,B.CheckQty,B.Qty,B.Date,D.StuffCname,D.Picture,P.Forshort,N.Name AS Operator    
			FROM  $DataIn.qc_badrecord  B 
			LEFT JOIN $DataIn.gys_shmain M  ON M.Id=B.shMid 
			LEFT JOIN $DataIn.stuffdata D ON D.StuffId=B.StuffId 
			LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
			LEFT JOIN $DataPublic.staffmain N ON N.Number=B.Operator  
			LEFT JOIN $DataIn.cg1_stocksheet  G ON G.StockId=B.StockId  
			WHERE  M.CompanyId=$CheckCompany AND M.Floor='$Floor'    ORDER BY Date DESC ",$link_id);
$dataArray=array();
if($monthRow = mysql_fetch_array($monthResult)) 
  {
     do {
            $Id=$monthRow["Id"];
            $StuffId=$monthRow["StuffId"];
            $StuffCname=$monthRow["StuffCname"];
            $Forshort=$monthRow["Forshort"];
            $Picture=$monthRow["Picture"];
            
            $ImagePath=$Picture>0?"$donwloadFileIP/download/stufffile/".$StuffId. "_s.jpg":"";
            include "submodel/stuffname_color.php";

            $Operator=$monthRow["Operator"];
            $shQty=number_format($monthRow["shQty"]);
            $Qty=number_format($monthRow["Qty"]);
            $qtyColor=$monthRow["Qty"]>0?"#FF0000":"";
            
            $Reason="";$ImageList=array();
            $qc_html="iphoneAPI/ManagerApp/inware/ck_qc_report.php?Id=$Id";
            $ImageList[]=array("Title"=>"品检报告","Type"=>"WEB","ImageFile"=>"$qc_html");
            
            $cause_Result=mysql_query("SELECT B.Id,T.Cause,B.CauseId,B.Reason,B.Picture FROM $DataIn.qc_badrecordsheet B LEFT JOIN $DataIn.qc_causetype T ON T.Id=B.CauseId  AND T.Type=1 WHERE B.Mid='$Id'",$link_id);
		      while ( $cause_row = mysql_fetch_array($cause_Result)){
		                $CauseId=$cause_row["CauseId"];
		                if ($CauseId=="-1"){
		                    $Reason.=$Reason==""?$cause_row["Reason"] : " / " . $cause_row["Reason"];
		                    $Cause=$cause_row["Reason"];
		                }else{
		                    $Reason.=$Reason==""?$cause_row["Cause"] : " / " . $cause_row["Cause"];
		                    $Cause=$cause_row["Cause"];
		                }
		                if ($cause_row["Picture"]==1){
		                     $Bid=$cause_row["Id"];
			                 $ImageList[]=array("Title"=>"$Cause","Type"=>"WEB","ImageFile"=>"download/qcbadpicture/Q" . $Bid . ".jpg");
		                }
		       }
            $tempArray=array(
                       "Id"=>"$Id",
                       "onTap"=>array("Value"=>"1","hidden"=>"1"),
                      "Title"=>array("Text"=>"$StuffId-$StuffCname","Color"=>"$StuffColor","rtIcon"=>"$PropertySTR",'Frame'=>'20, 2, 230, 25'),
                      "Col1"=> array("Text"=>"06-14",'Frame'=>'260, 2, 50, 25','Align'=>'R'),
                      "Col2"=> array("Text"=>"$shQty"),
                      "Col3"=>array("Text"=>"$Qty","Color"=>"$qtyColor"),
                      "Col5"=>array("Text"=>"$Operator"),
                      "Remark"=>array("Text"=>"$Reason"),
                       "List"=>array("ImageList"=>$ImageList)
                      //"Process"=>$ProcessArray 
                );
                
            $qc_html="/iphoneAPI/ManagerApp/inware/ck_qc_report.php?Id=$Id";
            $dataArray[]=array("Tag"=>"data","onTap"=>array("NavTitle"=>"品检报告","Target"=>"Web","Args"=>"$qc_html"),"onEdit"=>"0","Estate"=>"0","data"=>$tempArray);
      } while($monthRow = mysql_fetch_array($monthResult));
  }
  
if ($FromPage!="Read"){
	    $jsonArray=$dataArray;
}

?>
