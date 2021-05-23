<?php 
//开发管理（设计）
 $mySql="SELECT A.Id,A.GroupId,A.Remark,YEARWEEK(A.Targetdate,1)  AS Weeks,YEARWEEK(A.Date,1)  AS xdWeeks,
 A.Number,A.Date,A.Finishdate,S.StuffId,S.Price,S.StuffCname,P.Forshort,M.Name AS OperatorName  
							    FROM  $DataIn.stuffdevelop A
								LEFT JOIN  $DataIn.stuffdata S  ON S.StuffId=A.StuffId 
								LEFT JOIN $DataIn.pands D ON D.StuffId=A.StuffId 
                                LEFT JOIN $DataIn.productdata PD ON PD.ProductId=D.ProductId 
                                LEFT JOIN $DataIn.trade_object P ON P.CompanyId=PD.CompanyId AND P.ObjectSign=2 
                                LEFT JOIN $DataPublic.staffmain M ON M.Number=A.Operator  
								WHERE  A.Estate>0 AND S.DevelopState=1 $SearchRows GROUP BY A.StuffId ORDER BY Weeks,Number";
		$myResult = mysql_query($mySql,$link_id);
		 if($myRow = mysql_fetch_assoc($myResult)){
		       $tempArray=array(
						                      "Id"=>"Total",
						                      "Title"=>array("Text"=>"新品开发","FontSize"=>"14","Color"=>"#0066FF")
						                   );
				$tempArray2[]=array("Tag"=>"Total","data"=>$tempArray);
				$jsondata[]=array("data"=>$tempArray2); 
				
				$oldWeeks=$myRow["Weeks"];
				$oldNumber=$myRow["Number"];
				$dataArray=array();
				$weekCount=0;$overCount=0;$pos=0;$posCount=0;
		    do{
		          $StuffId=$myRow["StuffId"];
		         $Forshort=$myRow["Forshort"];
		         $StuffCname=$myRow["StuffCname"];//配件名称
		         $Price=$myRow["Price"];
		         $Remark=$myRow["Remark"];
			     $Weeks=$myRow["Weeks"];
			      
			    $overCount=$overCount==0?"":$overCount;
			    
			     $Number=$myRow["Number"];
		       if ($oldNumber!=$Number || $Weeks!=$oldWeeks){
		           $Operator=$oldNumber;
		          include '../../model/subprogram/staffname.php';
			        $tempArray=array(
						                      "Id"=>"$oldNumber",
						                      "Title"=>array("Text"=>"$Operator","Color"=>"#000000","FontSize"=>"14","Bold"=>"1")
						                       //"Col3"=>array("Text"=>"$posCount")
						                   );
			        $tempArray1[]=array("Tag"=>"Total","data"=>$tempArray);
			        array_splice($dataArray,$pos,0,$tempArray1);
			        $pos=count($dataArray);
			        
			        $oldNumber=$myRow["Number"];
			        $posCount=0;
			        $tempArray1=array();
		       } 
		
		
		        if ($Weeks!=$oldWeeks){
		                 $WeekSTR="Week " . substr($oldWeeks,4,2);
		                 $dateArray= GetWeekToDate($oldWeeks,"m/d");
		                 $dateSTR=$dateArray[0] . "-" .  $dateArray[1];
		                 
		                $bgColor2=$oldWeeks==$curWeek?$CURWEEK_BGCOLOR:"";
		                $Color3=$oldWeeks<$curWeek?"#FF0000":"";
		                $headArray=array(
		                  "Id"=>"$GroupId",
		                  "onTap"=>"1",
		                   "RowSet"=>array("bgColor"=>"$bgColor2"),
		                  "Title"=>array("Text"=>"$WeekSTR","FontSize"=>"14","Bold"=>"1","BelowTitle"=>"$dateSTR"),
		                  "Col1"=>array("Text"=>"$overCount","Color"=>"#FF0000"),
		                  "Col3"=>array("Text"=>"$weekCount","FontSize"=>"14","Color"=>"$Color3")
		               );
		               $jsondata[]=array("head"=>$headArray,"hidden"=>"$viewHidden","Layout"=>$Layout,"IconSet"=>$IconSet,"data"=>$dataArray); 
		               $oldWeeks=$myRow["Weeks"];
		               $weekCount=0;$overCount=0;
		               $dataArray=array();$pos=0;
		     }
		      $WeekSTR=substr($myRow["Weeks"],4,2); 
		      $bgColor=$myRow["Weeks"]<$curWeek?"#FF0000":"";
		      $Id=$myRow["Id"];
		      $Date=$myRow["Date"];
		      $xdWeekSTR=substr($myRow["xdWeeks"],4,2) . "周";
		      
		      $QtyResult=mysql_fetch_array(mysql_query("SELECT SUM(OrderQty) AS Qty FROM $DataIn.cg1_stocksheet WHERE StuffId='$StuffId'",$link_id));
		      $Qty=$QtyResult["Qty"];
		      
		      $OperatorName=$myRow["OperatorName"];
		        $tempArray=array(
		                       "Id"=>"$Id",
		                       "Index"=>array("Text"=>"$WeekSTR","bgColor"=>"$bgColor"),
		                      "Title"=>array("Text"=>"$StuffId-$StuffCname"),
		                      "Col1"=> array("Text"=>"$Forshort"),
		                      "Col3"=>array("Text"=>"$Qty"),
		                      "Col5"=>array("Text"=>"$xdWeekSTR","Color"=>"#0000FF"),
		                      "Remark"=>array("Text"=>"$Remark","Date"=>"$Date","Operator"=>"$OperatorName")
		                      //"rIcon"=>"ship$ShipType"
		                   );
		                   $dataArray[]=array("Tag"=>"data","onEdit"=>"1","data"=>$tempArray);
		         //if ($LoginNumber==10868){ 
		           $logResult=mysql_query("SELECT A.Date,A.Remark,M.Name   
		                                FROM  $DataIn.stuffdevelop_log A
		                                LEFT JOIN $DataPublic.staffmain M ON M.Number=A.Operator
										WHERE  A.Mid='$Id' ORDER BY A.Date DESC,A.Id DESC LIMIT 1",$link_id);
					 if($logRow = mysql_fetch_array($logResult)){
					         $tempArray=array(
		                       "Title"=>array("Text"=>$logRow["Remark"]),
		                       "Col1"=> array("Text"=>$logRow["Date"]),
		                       "Col3"=>array("Text"=>$logRow["Name"])
		                   );
		                   $dataArray[]=array("Tag"=>"Log","onTap"=>array("Target"=>"Log","Args"=>"$Id"),"data"=>$tempArray); 
					 } 
				// }    
		          $weekCount++;       $posCount++;     
		     }while($myRow = mysql_fetch_assoc($myResult));
				           $Operator=$oldNumber;
				          include '../../model/subprogram/staffname.php';
					        $tempArray=array(
								                      "Id"=>"$oldNumber",
								                      "Title"=>array("Text"=>"$Operator","Color"=>"#000000","FontSize"=>"14","Bold"=>"1")
								                       //"Col3"=>array("Text"=>"$posCount","Margin"=>"-8,0,0,0")
								                   );
					        $tempArray1[]=array("Tag"=>"Total","data"=>$tempArray);
					        array_splice($dataArray,$pos,0,$tempArray1);
		
					     $overCount=$overCount==0?"":$overCount;
					     $WeekSTR="Week " . substr($oldWeeks,4,2);   
					     $dateArray= GetWeekToDate($oldWeeks,"m/d");
		                 $dateSTR=$dateArray[0] . "-" .  $dateArray[1];
		                 
		                $bgColor2=$oldWeeks==$curWeek?$CURWEEK_BGCOLOR:"";
		                $Color3=$oldWeeks<$curWeek?"#FF0000":"";
		                $headArray=array(
		                  "Id"=>"$GroupId",
		                  "onTap"=>"1",
		                   "RowSet"=>array("bgColor"=>"$bgColor2"),
		                  "Title"=>array("Text"=>"$WeekSTR","FontSize"=>"14","Bold"=>"1","BelowTitle"=>"$dateSTR"),
		                  "Col1"=>array("Text"=>"$overCount","Color"=>"#FF0000"),
		                  "Col3"=>array("Text"=>"$weekCount","FontSize"=>"14","Color"=>"$Color3")
		               );
		     $jsondata[]=array("head"=>$headArray,"hidden"=>"$viewHidden","Layout"=>$Layout,"IconSet"=>$IconSet,"data"=>$dataArray); 
	}
?>