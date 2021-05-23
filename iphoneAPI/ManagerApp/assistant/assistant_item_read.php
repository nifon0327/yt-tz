<?php 
	//add by cabbage 20150105 傳入kq_YearHolday
	$iPhoneTag = "yes";
	include "../../basic/parameter.inc";
	include "../../model/kq_YearHolday.php";
    //根据员工:$Number获得个人助理项目(ModuleId:(1044)的权限
    //取得权限
    $modelArray=array();
     $cSignResult=mysql_query("SELECT cSign FROM  $DataPublic.staffmain WHERE Number='$LoginNumber' AND Estate=1 LIMIT 1",$link_id);
      if  ($cSignRow = mysql_fetch_array($cSignResult)){
           $DataIn=$cSignRow["cSign"]==3?$DataOut:$DataIn;
      }

    $userIdResult=mysql_query("SELECT Id FROM  $DataIn.usertable WHERE Number='$LoginNumber' AND Estate=1 LIMIT 1",$link_id);
    if  ($userIdRow = mysql_fetch_array($userIdResult)){
            $userId=$userIdRow["Id"];
            
                         
            $dModuleIdResult=mysql_query("SELECT dModuleId FROM  $DataPublic.modulenexus WHERE  ModuleId='1057'",$link_id);
            while ($dModuleIdRow = mysql_fetch_array($dModuleIdResult)){
               $ModuleId=$dModuleIdRow["dModuleId"];
               $dModelId.=$dModelId==""?$ModuleId:",$ModuleId";
            }
             //$dModelId.=$dModelId==""?"1426":",1426";
             
            $rMenuResult = mysql_query("SELECT A.ModuleId 
                    FROM $DataIn.upopedom A 
                    LEFT JOIN $DataPublic.funmodule B ON B.ModuleId=A.ModuleId 
                    LEFT JOIN  $DataPublic.modulenexus C ON C.dModuleId=A.ModuleId  
                    WHERE A.Action>0 AND B.TypeId=5 AND A.UserId='$userId' AND B.Estate=1 AND (C.ModuleId IN ($dModelId) OR A.ModuleId='1426')  ORDER BY B.OrderId",$link_id);
        while ($rMenuRow = mysql_fetch_array($rMenuResult)){
                    $ModuleId=$rMenuRow["ModuleId"];
                    array_push($modelArray, $ModuleId);
            }

            if (in_array("1190",$modelArray)){
                  //未外出车辆
                  $checkResult=mysql_fetch_array(mysql_query("SELECT COUNT(*) AS Counts FROM $DataPublic.cardata A
  LEFT JOIN (SELECT DISTINCT CarId FROM $DataPublic.info1_business WHERE EndTime='0000-00-00 00:00:00' OR EndTime>NOW()) C ON C.CarId=A.Id
  WHERE A.Estate=1 and A.UserSign=1 and A.TypeId !=3 AND C.CarId IS NULL",$link_id));
                   $Counts=$checkResult["Counts"]==""?0:$checkResult["Counts"];
                   $jsonArray[]=array("Id"=>"301","ModuleId"=>"1190", "Name"=>"出差登记","Count"=>"$Counts");
            }
            
             if (in_array("1346",$modelArray)){
               //年、休假信息
		         $YearDays=0;$BxDays=0;
		         /*
		         $checkHsql=mysql_query("SELECT YearDays,BxDays FROM $DataPublic.staffholiday WHERE Number='$LoginNumber'",$link_id);
				 if($checkHrow = mysql_fetch_array($checkHsql)) {
				       $YearDays=$checkHrow["YearDays"];
				       $BxDays=$checkHrow["BxDays"];
				       
				       $YearDays=abs($YearDays-round($YearDays))>=0.1?number_format($YearDays,1):number_format($YearDays);
				       $BxDays=abs($BxDays-round($BxDays))>=0.1?number_format($BxDays,1):number_format($BxDays);
				 }  
		 
				 //modify by cabbage 20150105 修正年假的算法，改成和系統算法相同
			 	$YearDays = GetYearHolDayDays($LoginNumber, date("Y"), date("Y")."-12-31", $DataIn, $DataPublic, $link_id);
			 	*/
			 	 //年假
				 $YearDays=GetYearHolDays($LoginNumber,date("Y-m-d"),"",$DataIn,$DataPublic,$link_id);
			    $YearDays=$YearDays<0?0:$YearDays;
			      //补休
			      $bxCheckSql = "Select  Sum(hours) as hours From $DataPublic.bxSheet Where Number = '$LoginNumber'";
				  $bxCheckResult =mysql_fetch_array(mysql_query($bxCheckSql,$link_id));
			       $bxHours=$bxCheckResult["hours"]*1;
			       
			     if ($bxHours>0){
				        $usedBxHours=0;
				        $bxQjCheckSql = "Select * From $DataPublic.kqqjsheet Where Number = '$LoginNumber' and Type= '5' AND DATE_FORMAT(StartDate,'%Y')>='2013'";
						$bxQjCheckResult = mysql_query($bxQjCheckSql,$link_id);
						
						while($bxQjCheckRow = mysql_fetch_array($bxQjCheckResult))
						{
							$startTime = $bxQjCheckRow["StartDate"];
							$endTime = $bxQjCheckRow["EndDate"];
							$bcType = $bxQjCheckRow["bcType"];
							$usedBxHours+= GetBetweenDateDays($LoginNumber,$startTime,$endTime,$bcType,$DataIn,$DataPublic,$link_id);
							
						}
						// echo "$Number ----$bxHours-$usedBxHours </br>";
						$bxHours-=$usedBxHours;
				}
				 $bxDays=$bxHours>0?$bxHours/8:0;
			     if (is_float($bxDays) && abs($bxDays-round($bxDays))>=0.1) {
			            $bxDays=number_format($bxDays,1);
			     }
			     else{
			          $bxDays=number_format($bxDays);
			     }

                $jsonArray[]=array("Id"=>"302","ModuleId"=>"1346", "Name"=>"请假登记","Count"=>"","Vacation"=>array("T0"=>"$YearDays","T1"=>"$bxDays"));
            }
                
            if (in_array("1060",$modelArray)){
                $checkResult=mysql_fetch_array(mysql_query("SELECT SUM(S.Amount*C.Rate) AS Amount FROM $DataIn.hzqksheet  S 
                   LEFT JOIN $DataPublic.currencydata C ON C.Id=S.Currency
                   WHERE S.Operator='$LoginNumber'  AND S.Estate=2 ",$link_id));
                   $Amount=$checkResult["Amount"]==""?"":"¥" . $checkResult["Amount"];
                   $jsonArray[]=array("Id"=>"303","ModuleId"=>"1060", "Name"=>"费用报销","Count"=>"$Amount");
            }
            
            if (in_array("1532",$modelArray)){
                $checkResult=mysql_fetch_array(mysql_query("SELECT COUNT(*) AS Counts FROM $DataPublic.bxsheet   WHERE Number='$LoginNumber'  AND Estate=1 ",$link_id));
                   $Counts=$checkResult["Counts"]== 0 ? "" : $checkResult["Counts"];
                   $jsonArray[]=array("Id"=>"304","ModuleId"=>"1532", "Name"=>"补休申请","Count"=>"$Counts");
            }
            
            if (in_array("1377",$modelArray)){
               $checkResult=mysql_query("SELECT D.MID FROM $DataPublic.fixed_userdata D,
						   (SELECT A.MID,MAX(A.SDate) AS SDate,B.TypeId,B.Model,B.Estate 
						      FROM $DataPublic.fixed_userdata A 
						      LEFT JOIN $DataPublic.fixed_assetsdata B ON A.MID=B.Id
						    WHERE B.TypeId IN (14,17,46) AND A.UserType=1 GROUP BY A.MID
	                    ) C WHERE D.User='$LoginNumber' AND D.UserType=1 AND C.Estate=1 AND C.MID=D.MID AND C.SDate=D.SDate GROUP BY D.MID ",$link_id);
	               $Counts=mysql_num_rows($checkResult);        
                   $Counts=$Counts==""?0:$Counts;
                  $jsonArray[]=array("Id"=>"305","ModuleId"=>"1377", "Name"=>"领用登记","Count"=>"$Counts");
            }      
    }
    
     $jsonArray[]=array("Id"=>"306","ModuleId"=>"1402", "Name"=>"点餐登记","Count"=>"");
    
    
      $sMonth=date("Y-m",strtotime("$curDate  -3   month"));
      $unSignRow=mysql_fetch_array(mysql_query("SELECT COUNT(*) AS unCounts FROM (
            SELECT S.Month FROM $DataIn.cwxzsheet S 
            WHERE S.Number='$LoginNumber' AND S.Estate=0 AND S.Month>='$sMonth' 
            AND S.Month NOT IN (SELECT SignMonth FROM $DataPublic.wage_list_sign WHERE Number='$LoginNumber')
      UNION ALL
            SELECT S.Month FROM $DataOut.cwxzsheet S 
            WHERE S.Number='$LoginNumber' AND S.Estate=0 AND S.Month>='$sMonth' 
            AND S.Month NOT IN (SELECT SignMonth FROM $DataPublic.wage_list_sign WHERE Number='$LoginNumber')
      )A",$link_id));
      
      $unSign=$unSignRow["unCounts"]>0?1:0;
      $jsonArray[]=array("Id"=>"307","ModuleId"=>"3201", "Name"=>"薪金签收","Count"=>"","Badge"=>"$unSign");
      $jsonArray[]=array("Id"=>"308","ModuleId"=>"1426", "Name"=>"个人信息","Count"=>"");
	
?>