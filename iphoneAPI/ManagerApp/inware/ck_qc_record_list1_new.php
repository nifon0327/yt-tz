<?php 
//抽检、全检
 if ($Floor=="") $Floor=$dModuleId==2152?3:6;
 
 $imgBase = "http://www.middlecloud.com/download/qcbadpicture/";
 $hasNewQc = false;
$canlistens = array('11965','10341');
$listen_ip = "";
if (versionToNumber($AppVersion)>=329) {
	$hasNewQc = true;
	
	
	if (in_array($LoginNumber, $canlistens)) {
	//	$listen_ip = '192.168.19.132|30040';
	}
	}
 
 include "../../basic/downloadFileIP.php";
 
$monthResult=mysql_query("SELECT  B.Id,B.StuffId,B.shQty,B.CheckQty,B.Qty,B.Date,D.StuffCname,D.Picture,P.Forshort,N.Name AS Operator  ,B.StockId  
			FROM  $DataIn.qc_badrecord  B 
			LEFT JOIN $DataIn.gys_shmain M  ON M.Id=B.shMid 
			LEFT JOIN $DataIn.stuffdata D ON D.StuffId=B.StuffId 
			LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
			LEFT JOIN $DataPublic.staffmain N ON N.Number=B.Operator  
			LEFT JOIN $DataIn.cg1_stocksheet  G ON G.StockId=B.StockId  
			WHERE  DATE_FORMAT(B.Date,'%Y-%m-%d')='$CheckDate' AND M.Floor='$Floor'    ORDER BY Date DESC ",$link_id);
$dataArray=array();
if($monthRow = mysql_fetch_array($monthResult)) 
  {
     do {
            $Id=$monthRow["Id"];
            $StuffId=$monthRow["StuffId"];
            $StuffCname=$monthRow["StuffCname"];
            $Forshort=$monthRow["Forshort"];
            $Picture=$monthRow["Picture"];
            $stockid = $monthRow["StockId"];
            
            $ImagePath=$Picture>0?"$donwloadFileIP/download/stufffile/".$StuffId. "_s.jpg":"";
            include "submodel/stuffname_color.php";

            $Operator=$monthRow["Operator"];
            $shQty=number_format($monthRow["shQty"]);
            $Qty=number_format($monthRow["Qty"]);
            $qtyColor=$monthRow["Qty"]>0?"#FF0000":"";
            
            $Reason="";$ImageList=array();
            $qc_html="iphoneAPI/ManagerApp/inware/ck_qc_report.php?Id=$Id";
            $ImageList[]=array("Title"=>"品检报告","Type"=>"WEB","ImageFile"=>"$qc_html");
            
            $cause_Result=mysql_query("SELECT B.Id,T.Cause,B.CauseId,B.Reason,B.Picture,B.Qty FROM $DataIn.qc_badrecordsheet B LEFT JOIN $DataIn.qc_causetype T ON T.Id=B.CauseId  AND T.Type=1 WHERE B.Mid='$Id'",$link_id);
            $FileArr = array();
                $qc_html="/iphoneAPI/ManagerApp/inware/ck_qc_report.php?Id=$Id";
                
                    $FileArr[]=array("Type"=>"html",
				        "url"=>"http://www.middlecloud.com"."$qc_html",
				        "url_thumb"=>"http://www.middlecloud.com"."$qc_html",
				        'title'=>" 品检报告",
				        "sType"=>"web");
                
                 $iterS = 1; 
                 $Reason = "";$Cause = "";
		      while ( $cause_row = mysql_fetch_array($cause_Result)){
		                $CauseId=$cause_row["CauseId"];
		                $CauseQty = $cause_row["Qty"];
		                if ($CauseId=="-1"){
		                    $Reason.="\n$iterS.". $cause_row["Reason"]."-$CauseQty".'pcs';
		                    $Cause=$cause_row["Reason"];
		                }else{
		                   
		                       $Reason.="\n$iterS.". $cause_row["Cause"]."-$CauseQty".'pcs';
		                    $Cause=$cause_row["Cause"];
		                }
		                if ($cause_row["Picture"]==1){
		                     $Bid=$cause_row["Id"];
			                 $ImageList[]=array("Title"=>"$Cause","Type"=>"WEB","ImageFile"=>"download/qcbadpicture/Q" . $Bid . ".jpg");
			                 
			                 
			             
				        $FileArr[]=array("Type"=>"img",
				        "url"=>"$imgBase"."Q$Bid".".jpg",
				        "url_thumb"=>"$imgBase"."Q$Bid".".jpg",
				        'title'=>" $Cause",
				        "sType"=>"jpg");
			        

		                }
		                
		                $iterS ++;
		       }
            $tempArray=array(
                       "Id"=>"$Id",'has2'=>'1',
                     //  "onTap"=>array("Value"=>"1","hidden"=>"1"),
                      "Title"=>array("Text"=>"$StuffId-$StuffCname","Color"=>"$StuffColor","rtIcon"=>"$PropertySTR"),
                      "Col1"=> array("Text"=>"$Forshort","Color"=>"$FORSHORT_COLOR"),
                      "Col2"=> array("Text"=>"$shQty"),
                      "Col3"=>array("Text"=>"$Qty","Color"=>"$qtyColor","Margin"=>"30,0,0,0"),
                      "Col5"=>array("Text"=>"$Operator"),
                      //"Process"=>$ProcessArray 
                );
                
                /*
	                
                      "Remark"=>array("Text"=>"$Reason"),
                       "List"=>array("ImageList"=>$ImageList)
                */
     
        //
                
                
                
        
            $dataArray[]=array("Tag"=>"data",
            //"onTap"=>array("NavTitle"=>"品检报告","Target"=>"Web","Args"=>"$qc_html"),
            "onEdit"=>"0","Estate"=>"0","data"=>$tempArray);
            
            
                $dataArray[]=array("Tag"=>"remark1",
						     	"RID" => $Reason==""?$Reason:"-1",
						   	"Record" => "$Reason",
						   	"Recorder" => "",
						   	"anti_oper"=>"",
						   	"headline"=>"不良原因：",'size_img'=>'60',
						   	 	"Files"=>count($FileArr)>0?"1": "",
						   	 	"FileArray"=>$FileArr,"needReason"=>'0',
						   	 'nooper'=>"1",
						   	'left_sper'=>"15","margin_left"=>"15"
						   	);
   
            
            
      } while($monthRow = mysql_fetch_array($monthResult));
  }
  
if ($FromPage!="Read"){
	    $jsonArray=$dataArray;
}

?>
