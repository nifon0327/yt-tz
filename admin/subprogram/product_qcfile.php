<?php     //产品QC检验标准图电信---yang 20120801
//if($TestStandardSign==1){
		$Dir="download/QCstandard/";
    	$Dir=anmaIn($Dir,$SinkOrder,$motherSTR);
		$imgResult=mysql_query("SELECT Q.Picture,Q.Date,Q.Operator FROM $DataIn.qcstandardimg D
			        LEFT JOIN $DataIn.qcstandarddata Q ON Q.Id=D.QcId
			       WHERE D.ProductId='$ProductId' AND Q.Estate=1 AND Q.IsType=0",$link_id); 
		$typeSql = mysql_query("SELECT TypeId AS QCTypeId FROM $DataIn.productdata where ProductId='$ProductId'",$link_id);
		$QCTypeId=mysql_result($typeSql,0,"QCTypeId");
		if($imgRow=mysql_fetch_array($imgResult)) {
		         do {	
			              $QCImage=$imgRow["Picture"];
			              $QCImage=anmaIn($QCImage,$SinkOrder,$motherSTR);
			              $QCImage="<span onClick='OpenOrLoad(\"$Dir\",\"$QCImage\")' style='CURSOR: pointer;color:#F00;'>View</span>";	
			           }while ($imgRow=mysql_fetch_array($imgResult));   
		        }
       else 
       {
         $QCImage="&nbsp;";
	     $LessResult=mysql_query("SELECT L.Id From $DataIn.qcstandardless L  
                     LEFT  JOIN $DataIn.productdata P ON P.ProductId=L.ProductId
                     WHERE TypeId='$QCTypeId' AND P.ProductId='$ProductId' limit 1",$link_id);//如果存在，则说明这个产品不能用相关类图的QC标准图
              if (mysql_num_rows($LessResult)<=0)
              {
	               $typeResult=mysql_query("select * from $DataIn.qcstandarddata where TypeId='$QCTypeId' AND Estate=1 AND IsType=1",$link_id); 
                if($typeRow=mysql_fetch_array($typeResult)) {
					do {	  
					          $QCImage=$typeRow["Picture"];
					          $QCImage=anmaIn($QCImage,$SinkOrder,$motherSTR);
					          $QCImage="<span onClick='OpenOrLoad(\"$Dir\",\"$QCImage\")' style='CURSOR: pointer;color:#F00;'>View</span>";	
					      }while ($typeRow=mysql_fetch_array($typeResult));   
				      }
	       }
       }
?>