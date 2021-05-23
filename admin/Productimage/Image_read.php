<?php   
//电信-ZX  2012-08-01
//include "../../chksession.php";
include "../../basic/parameter.inc";
$mType=$_GET["mType"];

if ($mType=="") $mType=1;
$ProductId=$_GET["PId"];

if ($ProductId!=""){
  switch($mType){
	case 1:
	   $ImageFile="../../download/teststandard/" . "T".$ProductId.".jpg";
	   $OperaStr="";
		$imgResult=mysql_query("select * from $DataIn.productstandimg where ProductId='$ProductId'",$link_id); 
		if($imgRow=mysql_fetch_array($imgResult)) {
			$Operator=$imgRow["Operator"];
			include "../subprogram/staffname.php";
			$Date=$imgRow["Date"];	
			$OperaStr=$Operator . "||" . $Date;
		}
	 if (file_exists($ImageFile)){
	        echo "<img src='$ImageFile' />||" . $OperaStr; 
	        /*
             if ($fromIpad==1){   //来自Ipad，图片大小超过1500应处理。
                 $img_info = getimagesize($ImageFile); 
	         $imgWidth=$img_info[0];
                 $imgHeight=$img_info[1];
                 if ($imgWidth>1500 || $imgHeight>1500){
                      include "cutbigimage.php";
                      echo $imgList . "||" . $OperaStr;   
                      }
                  else{
                      echo "<img src='$ImageFile' />||" . $OperaStr;   
                      }
              }else{
                    echo "<img src='$ImageFile' />||" . $OperaStr;  
                 } 
                 */ 
	  }else{
              echo "0";
             }
	 break;
	case 2:
	    $ImagePath="../../download/QCstandard/";
		$ImageFile="";
		$OperaStr="";
		$n=0;
		$imgResult=mysql_query("SELECT Q.Picture,Q.Date,Q.Operator FROM $DataIn.qcstandardimg D
			LEFT JOIN $DataIn.qcstandarddata Q ON Q.Id=D.QcId
			WHERE D.ProductId='$ProductId' AND Q.Estate=1 AND Q.IsType=0",$link_id); 
		if($imgRow=mysql_fetch_array($imgResult)) {
		   do {	
			   $Picture=$imgRow["Picture"];
			   $ImageFile=$ImagePath . $Picture;
			   $Operator=$imgRow["Operator"];
			   $Date=$imgRow["Date"];	
			  // $OperaStr=$Operator . "||" . $Date;
			    if (file_exists($ImageFile)){
			       $n+=1;  
				   include "../subprogram/staffname.php";
				   $OperaStr=$OperaStr . "||" . "$Operator#$Date";
				   echo "<img id='qcImg$n' src='$ImageFile' /><br />";
				}
			}while ($imgRow=mysql_fetch_array($imgResult));   
		}else{
		   $typeSql = mysql_query("SELECT TypeId FROM $DataIn.productdata where ProductId='$ProductId'",$link_id);
		   $TypeId=mysql_result($typeSql,0,"TypeId");
		   $typeResult=mysql_query("select * from $DataIn.qcstandarddata where TypeId='$TypeId' AND Estate=1 AND IsType=1",$link_id); 	
		  if($typeRow=mysql_fetch_array($typeResult)) {
			do {	  
			  $Picture=$typeRow["Picture"];
			  $ImageFile=$ImagePath . $Picture;
			  $Operator=$typeRow["Operator"];
			  $Date=$typeRow["Date"];
			  if (file_exists($ImageFile)){
			       $n+=1;  
				   include "../subprogram/staffname.php";
				   $OperaStr=$OperaStr . "||" . "$Operator#$Date";
				   echo "<img id='qcImg$n' src='$ImageFile' /><br />";
				}
			 }while ($typeRow=mysql_fetch_array($typeResult));   
		  }
		}
	    if ($n==0){
			  echo "0";
		  }else{
	          echo "||$n" . $OperaStr;
		  }
	 break;
	case 3:
	 $mastakeResult = mysql_query("select E.Id,E.Title,E.Picture,E.Owner,E.Operator,E.Date   
					FROM $DataIn.casetoproduct C 
					LEFT JOIN $DataIn.errorcasedata E ON E.Id=C.cId 
					WHERE C.ProductId='$ProductId' AND E.Estate=1 ",$link_id); 
	if($mastakeRow=mysql_fetch_array($mastakeResult)) {
		$n=0;$OperaStr="";
		do {
			$Picture=$mastakeRow["Picture"];
			$Owner=$mastakeRow["Owner"];
			$Operator=$mastakeRow["Operator"];
			$Date=$mastakeRow["Date"];
            $cId=$mastakeRow["Id"];
			$ImageFile ="../../download/errorcase/".$Picture;
			if (file_exists($ImageFile)){
			   $n+=1;
			   include "../subprogram/staffname.php";
			   $OperaStr=$OperaStr . "||" . "$cId#$Owner#$Operator#$Date";
	           echo "<img id='caseImg$n' src='$ImageFile' /><br />";
	        }
		  } while ($mastakeRow = mysql_fetch_array($mastakeResult));
		  if ($n==0){
			  echo "0";
		  }else{
	          echo "||$n" . $OperaStr;
		  }
	 }else{
		 echo "0"; 
	 }
	 break;
   } 
}
?>