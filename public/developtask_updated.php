<?php 
//步骤1 $DataIn.development 二合一已更新电信---yang 20120801
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"] = $nowWebPage;
$Log_Item="开发任务";		
$upDataSheet="$DataIn.development";	
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$x=1;
switch($ActionId){
	case 7:
		 $Log_Funtion="锁定";	$SetStr="Locks=0";		include "../model/subprogram/updated_model_3d.php";		break;
	case 8:
		 $Log_Funtion="解锁";	$SetStr="Locks=1";		include "../model/subprogram/updated_model_3d.php";		break;
	case 15:
	     $Log_Funtion="记录退回";
		 $SetStr="Estate=0"; include "../model/subprogram/updated_model_3d.php";		break;
	case 23:
		 $DelSql = "DELETE FROM $DataIn.developsheet WHERE ItemId='$ItemId'"; 
         $DelResult = mysql_query($DelSql);
	     $StuffArray=explode("|",$SIdList);
         $Count=count($Qty);
         $Date=date("Y-m-d");
         for ($i=0;$i<$Count;$i++){
	        $StuffId=$StuffArray[$i];
	        $Relation=$Qty[$i];
	        $sDiecut=$Diecut[$i];
	        $sCutrelation=$Cutrelation[$i]==""?0:$Cutrelation[$i];
			
		if($StuffId>='80001'){
		    $staffResult=mysql_query("SELECT S.StuffCname,S.TypeId,S.Price,B.BuyerId,B.CompanyId
			 FROM $DataIn.stuffdata S
			 LEFT JOIN $DataIn.bps B ON B.StuffId=S.StuffId WHERE S.StuffId='$StuffId'",$link_id);
		    if($staffRow=mysql_fetch_array($staffResult)){
		       $StuffCname=$staffRow["StuffCname"];
		       $TypeId=$staffRow["TypeId"];
			   $Price=$staffRow["Price"];
			   $BuyerId=$staffRow["BuyerId"];
			   $CompanyId=$staffRow["CompanyId"];
			   }
			 }//end if
		 else{
		    $staffResult=mysql_query("SELECT S.StuffCname,S.TypeId,S.Price,S.BuyerId,S.CompanyId
			 FROM $DataIn.developnewstaff S WHERE S.StuffId='$StuffId'",$link_id);
		    if($staffRow=mysql_fetch_array($staffResult)){
		       $StuffCname=$staffRow["StuffCname"];
		       $TypeId=$staffRow["TypeId"];
			   $Price=$staffRow["Price"];
			   $BuyerId=$staffRow["BuyerId"];
			   $CompanyId=$staffRow["CompanyId"];
			   }
		     }
		 //developsheet表中插入新配件关系	
	   $IN_recodeN="INSERT INTO $DataIn.developsheet(Id, ItemId, StuffId, StuffCname, TypeId, Price,Relation, Diecut, Cutrelation, BuyerId, CompanyId, Locks)VALUES	 (NULL,'$ItemId',
		'$StuffId','$StuffCname','$TypeId','$Price','$Relation','$sDiecut','$sCutrelation','$BuyerId','$CompanyId','1')";
		 //echo $IN_recodeN;
	     $resN=@mysql_query($IN_recodeN);
	     if($resN){
		           $Log.="&nbsp;&nbsp; $x -配件ID号为 $StuffId 的配件已加入项目 $ItemId 中!</br>";
		           }
	          else{
		           $Log.="<div class='redB'>&nbsp;&nbsp; $x -配件ID号为 $StuffId 的配件未能加入项目 $ItemId 中!</div></br>";
		         }
			 
		   $x++;
		 }
	     break;
	case 96:
	     $Log_Funtion="样品确认通过,任务完成";
		 $SetStr="Estate=1";   include "../model/subprogram/updated_model_3d.php";		break;
	case 94: 
	     $ItemSql="SELECT ItemId FROM $DataIn.development WHERE Id='$Id'";
         $ItemResult=mysql_fetch_array(mysql_query($ItemSql,$link_id));
         $ItemId=$ItemResult["ItemId"];
	if($Action==1){
	     if($Cutrelation==""){$Cutrelation=0;}
		 //新配件用developnewstaff备份（副表）
		 //$LockSql=" LOCK TABLES $DataIn.developnewstaff WRITE"; $LockRes=@mysql_query($LockSql);
		  $maxSql = mysql_query("SELECT MAX(StuffId) AS Mid FROM $DataIn.developnewstaff",$link_id);
          $StuffId=mysql_result($maxSql,0,"Mid");
          if($StuffId){
	                     $StuffId=$StuffId+1;}
                   else{
	                     $StuffId=1;}
		 $inRecode="INSERT INTO $DataIn.developnewstaff (Id, ItemId,StuffId, StuffCname,TypeId,   Price,Relation,Diecut,Cutrelation,BuyerId,CompanyId,Locks) VALUES (NULL,'$ItemId','$StuffId',
		 '$StuffCname','$TypeId','$Price','$Relation','$Diecut','$Cutrelation','$BuyerId','$CompanyId','1')";
         $inAction=@mysql_query($inRecode);
		
		// $unLockSql="UNLOCK TABLES"; $unLockRes=@mysql_query($unLockSql); 
		//主表developsheet
        // $LockSql_1=" LOCK TABLES $DataIn.developsheet WRITE"; $LockRes_1=@mysql_query($LockSql_1);
         $inRecode_1="INSERT INTO $DataIn.developsheet (Id, ItemId,StuffId, StuffCname,TypeId,   Price,Relation,Diecut,Cutrelation,BuyerId,CompanyId,Locks) VALUES (NULL,'$ItemId','$StuffId',
		 '$StuffCname','$TypeId','$Price','$Relation','$Diecut','$Cutrelation','$BuyerId','$CompanyId','1')";
         $inAction_1=@mysql_query($inRecode_1);
         if($inAction_1){ 
	                   $Log="<br>&nbsp;&nbsp;&nbsp;&nbsp;名称为 $StuffCname 的配件资料新增成功!.<br>";
	                  } 
             else{
	             $Log="<div class=redB>&nbsp;&nbsp;&nbsp;&nbsp;名称为 $StuffCname 的配件资料新增失败! $inRecode</div>";
	             $OperationResult="N";
	            }
	     // $unLockSql_1="UNLOCK TABLES"; $unLockRes_1=@mysql_query($unLockSql_1); 
			
				
		 }
	if($Action==2){
	     //相关配件读入developsheet表中
		 if($_POST['stuffId']){
		    $Count2=count($_POST['stuffId']);
		    $Id2="";
		    for($j=0;$j<$Count2;$j++){
		        $thisId=$_POST[stuffId][$j];
		        $Id2=$Id2==""?$thisId:$Id2.",".$thisId;
		        }
		     $TypeIdSTR2="and S.StuffId IN ($Id2)";
			 //echo $TypeIdSTR2;
			// $delstuff=  mysql_query("",$link_id);
		$stuffSql="SELECT S.StuffId,S.StuffCname,S.TypeId,S.Price,B.BuyerId,B.CompanyId 
			 FROM $DataIn.stuffdata S
			 LEFT JOIN $DataIn.bps B ON B.StuffId=S.StuffId
			 Where 1 $TypeIdSTR2";
		//echo $stuffSql;
		$stuffResult=mysql_query($stuffSql,$link_id);
	    while($stuffRow=mysql_fetch_array($stuffResult)){ 
			      $StuffId=$stuffRow["StuffId"];
				  $StuffCname=$stuffRow["StuffCname"];
				  $TypeId=$stuffRow["TypeId"];
				  $Price=$stuffRow["Price"];
				  $BuyerId=$stuffRow["BuyerId"];
				  $CompanyId =$stuffRow["CompanyId"];
		  $stuffRecode="INSERT INTO $DataIn.developsheet(Id,ItemId,StuffId,StuffCname,TypeId,Price,                       Relation,Diecut,Cutrelation,BuyerId,CompanyId,Locks)VALUES(NULL,'$ItemId',
		               '$StuffId','$StuffCname','$TypeId','$Price','1','','0','$BuyerId','$CompanyId','1')";
		          $stuffResult1=@mysql_query($stuffRecode);
		          if($stuffResult1){ 
	                        $Log.="<br>&nbsp;&nbsp;&nbsp;&nbsp;名称为 $StuffCname 的配件资料新增成功!.<br>";
	                       } 
                  else{
	                  $Log.="<div class=redB>&nbsp;&nbsp;&nbsp;&nbsp;名称为 $StuffCname 的配件资料新增失败! $inRecode</div>";
	                  $OperationResult="N";
	                  }
			   }//end while($stuffRow=mysql_fetch_array($stuffResult))
			 } //end if($_POST['stuffId'])
	      } //end if($Action==2)
	break;
	default:
		$FilePath="../download/kfimg/";
		if($upFile!=""){
			$FileType=substr("$upFile_name", -4, 4);
			$OldFile=$upFile;
			$PreFileName=$ItemId.$FileType;
			$uploadInfo=UploadPictures($OldFile,$PreFileName,$FilePath);
			$uploadSTR=$uploadInfo==""?",Attached='0'":",Attached='$uploadInfo'";
			}
		if($uploadSTR=="" && $oldFile!=""){//没有上传文件并且已选取删除原文件
			$FilePath=$FilePath.$oldFile;
			if(file_exists($FilePath)){
				unlink($FilePath);
				}
			$uploadSTR=",Attached='0'";
			}
		if($Gfile!=""){
	    $OldFile=$Gfile;
	    $strFileName = $_FILES['Gfile']['name'];
	    $extendFile=extend_3($strFileName);
	    $PreFileName=$ItemId."_1.".$extendFile;
	    $uploadInfo=UploadPictures($OldFile,$PreFileName,$FilePath);
		$Gfile=$uploadInfo;
		$uploadGfile=" ,Gfile='$Gfile',"; }
	     
	        $SetStr="Qty='$Qty',EndDate='$EndDate',Plan='$Plan' $uploadGfile $uploadSTR";
		    include "../model/subprogram/updated_model_3a.php";
		break;
	}
$ALType="From=$From&Estate=$Estate&Pagination=$Pagination";
//步骤4：操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
  