<?php 
//配件名称审核
 $mySql="SELECT S.Id,S.StuffId,S.StuffCname,S.Price,S.Picture,S.Date,S.Operator,S.OPdatetime,S.CheckSign,S.ForcePicSpe,S.DevelopState,S.Remark,
 IF(S.Pjobid=-1,T.PicNumber ,S.PicNumber) as PicNumber,IF(S.Pjobid=-1,M.GroupName,K.GroupName) as PJobname,
 IF(S.jobid=-1,T.GicNumber ,S.GicNumber) as GicNumber,IF(S.Jobid=-1,N.GroupName,F.GroupName) as GJobname,
 B.BuyerId,C.PreChar,U.Name AS UnitName,P.Forshort,T.ForcePicSign,T.TypeName 
         FROM  $DataIn.stuffdata S 
         LEFT JOIN $DataIn.bps B ON B.StuffId=S.StuffId 
         LEFT JOIN $DataPublic.stuffunit U ON U.Id=S.Unit 
         LEFT JOIN $DataIn.stufftype T ON S.TypeId=T.TypeId 
         LEFT JOIN $DataIn.trade_object P ON P.CompanyId=B.CompanyId 
         LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency 
         LEFT JOIN $DataIn.staffgroup K ON K.Id=S.Pjobid 
     	 LEFT JOIN $DataIn.staffgroup F ON F.Id=S.Jobid
     	 LEFT JOIN $DataIn.staffgroup M ON M.Id=T.Picjobid 
	     LEFT JOIN $DataIn.staffgroup N ON N.Id=T.GicJobid 
         WHERE  S.Estate=2 ORDER BY  S.TypeId,S.Date,S.OPdatetime" ;
 $Result=mysql_query($mySql,$link_id);
 while($myRow = mysql_fetch_array($Result)) 
 {
    $Id=$myRow["Id"];
    $TypeName=$myRow["TypeName"];
    $Forshort=$myRow["Forshort"];
    $StuffId=$myRow["StuffId"];
    $StuffCname=$myRow["StuffCname"];//配件名称
    $UnitName=$myRow["UnitName"]=="Pcs"?"pcs":$myRow["UnitName"];
    
    $Price=sprintf("%.2f",$myRow["Price"]);
    $PreChar=$myRow["PreChar"];
    
     $CheckSign=$myRow["CheckSign"]==1?"全检":"抽检";
     $DevelopState=$myRow["DevelopState"]==1?"是":"否";
    
    $ForcePicSpe=$myRow["ForcePicSpe"];
	$ForcePicSign=$ForcePicSpe>=0?$ForcePicSpe:$myRow["ForcePicSign"];
	
	switch($ForcePicSign){
			case 0: 
				$ForcePicSign="无需求";
			break;
			case 1: 
				$ForcePicSign="需要图片";
			break;
			case 2: 
				$ForcePicSign="需要图档";
			break;
			case 3: 
				$ForcePicSign="图片/图档";
			break;
			case 4: 
				$ForcePicSign="强行锁定";
			break;			
		}	
		
    $PicNumber=$myRow["PicNumber"];
	$PicName="";
		if ($PicNumber!=0 ){  //说明指定的人
			$Operator=$PicNumber;
			include "../../model/subprogram/staffname.php";	
			$PicName=$Operator;
		}
		
		$PJobname=$myRow["PJobname"];
		if ($PJobname!=""){
			$PJobname=$PicName==""?"$PJobname-(未指定人)":$PJobname."-$PicName";
		}
		
	$GicName="";
	 $GicNumber=$myRow["GicNumber"];
		if ($GicNumber!=0 ){  //说明指定的人
			$Operator=$GicNumber;
			include "../../model/subprogram/staffname.php";	
			$GicName=$Operator;
	}
			
	$GJobname=$myRow["GJobname"];
	if ($GJobname!=""){
			$GJobname=$GicName==""?"$GJobname-(未指定人)":$GJobname."-$GicName";
	}
		
	 $Operator=$myRow["BuyerId"];
     include '../../model/subprogram/staffname.php';
     $Buyer=$Operator;
    
    $Operator=$myRow["Operator"];
     include "../../model/subprogram/staffname.php";

    $cgDate=$myRow["Date"];
    $OPdatetime=$myRow["OPdatetime"];
    //$Date=date("m-d H:i",strtotime($OPdatetime));
    $Date=GetDateTimeOutString($OPdatetime,'');
   
    $opHours= geDifferDateTimeNum($OPdatetime,"",1);
    if ($opHours>=$timeOut[0]) $OverNums++;

     $Remark=$myRow["Remark"];
      $Price=number_format($Price,3);
      
     $listArray=array();
     $listArray[]=array("Cols"=>"1","Name"=>"下单需求:","Text"=>"$ForcePicSign");
     $listArray[]=array("Cols"=>"1","Name"=>"图片职责:","Text"=>"$PJobname");
     $listArray[]=array("Cols"=>"1","Name"=>"图档职责:","Text"=>"$GJobname");
     $listArray[]=array("Cols"=>"1","Name"=>"开发需求:","Text"=>"$DevelopState");
     $listArray[]=array("Cols"=>"1","Name"=>"品检方式:","Text"=>"$CheckSign");
     $listArray[]=array("Cols"=>"1","Name"=>"采  购  员:","Text"=>"$Buyer");
      
     $dataArray[]=array(
	                     "Id"=>"$Id",
	                     "onTap"=>array("Value"=>"1","hidden"=>"$hidden","Args"=>"$Id","Audit"=>"$AuditSign"),
	                     "Title"=>array("Text"=>"$StuffId-$StuffCname","Color"=>""),
	                     "Col1"=>array("Text"=>"$TypeName"),
	                     "Col2"=>array("Text"=>"$Forshort","Align"=>"R"),
	                     "Col3"=>array("Text"=>"$PreChar$Price","Align"=>"R"),
	                     "Col4"=>array("Text"=>"$UnitName"),
	                     "Date"=>array("Text"=>"$Date"),
	                      "Remark"=>array("Text"=>"$Remark"),
	                     "Operator"=>array("Text"=>"$Operator"),
	                     "List"=>array("Value"=>"0","data"=>$listArray)
                     );
 }

?>