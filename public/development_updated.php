<?php 
//步骤1 $DataIn.development 二合一已更新电信---yang 20120801
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"] = $nowWebPage;
//步骤2：
$Log_Item="开发项目";		//需处理
$upDataSheet="$DataIn.development";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$Date=date("Y-m-d");
//步骤3：需处理，更新操作
$x=1;
switch($ActionId){
	case 7:
		  $Log_Funtion="锁定";	$SetStr="Locks=0";include "../model/subprogram/updated_model_3d.php";		break;
	case 8:
		  $Log_Funtion="解锁";	$SetStr="Locks=1";include "../model/subprogram/updated_model_3d.php";		break;
	case 15:
	      $Log_Funtion="退回";
		  $SetStr="Estate=2,sFrom=0"; include "../model/subprogram/updated_model_3d.php";		break;
		  
	case 17:
	      $fromWebPage=$funFrom."_m";
	      $Log_Funtion="新产品开发审核通过";
		  $SetStr="sFrom=1";   include "../model/subprogram/updated_model_3d.php";	
	  break;
	 
   case 93:
	     $Log_Funtion="任务分配";
	     $SetStr="Developer='$Developer',Estate='2'";
		 $updateSQL = "UPDATE $upDataSheet SET $SetStr WHERE Id IN ($Ids)";
         $updateResult = mysql_query($updateSQL);
         if ($updateResult){
	           $Log=$Log."&nbsp;&nbsp;ID号为 $Ids 的 $ItemName $Log_Item 资料 $Log_Funtion 成功!<br>";
			 }
	    else{
	           $Log=$Log."<div class=redB>&nbsp;&nbsp;ID号为 ($Ids) 的 $ItemName $Log_Item 资料 $Log_Funtion 失败! $updateSQL </div><br>";
	           $OperationResult="N";
	        }
        // include "../model/subprogram/updated_model_3a.php";
	     break;
	case 95:
	   //相关产品,配件资料,BOM表自动加入相关位置
//=========================================1.产品资料添加
	    $comSql=mysql_fetch_array(mysql_query("SELECT CompanyId FROM $DataIn.development WHERE Id='$Id'",$link_id));
		$CompanyId=$comSql["CompanyId"];
	    //$LockSql=" LOCK TABLES $DataIn.productdata WRITE"; $LockRes=@mysql_query($LockSql);
          $maxSql = mysql_query("SELECT MAX(ProductId) AS Mid FROM $DataIn.productdata",$link_id);
          $ProductId=mysql_result($maxSql,0,"Mid");
          if($ProductId){
	                     $ProductId=$ProductId+1;}
                   else{
	                     $ProductId=80001;}
		  $inProduct="INSERT INTO $DataIn.productdata(Id,ProductId,cName,
	     eCode,TypeId,Price,Unit,Moq,Weight,CompanyId,Description,Remark,pRemark,bjRemark,
	     TestStandard,PackingUnit,Code,Date,Estate,Locks,Operator)VALUES(NULL,'$ProductId',
	      '$cName','$eCode','$TypeId','$Price','$Unit','0','$Weight','$CompanyId','$Description','$Remark',
          '$pRemark','$bjRemark','0','$PackingUnit','$Code','$Date','2','0','$Operator')";
          $inAction1=@mysql_query($inProduct);
		  if ($inAction1){ 
	                     $Log="产品ID为 $ProductId 的产品添加成功!<br>";
	                    } 
                   else{ 
	                     $Log="<div class=redB>产品ID为 $ProductId 的产品资料添加失败! $inRecode </div><br>";
	                     $OperationResult="N";
	                    } 
          //$unLockSql="UNLOCK TABLES"; $unLockRes=@mysql_query($unLockSql);
//=====================================2.配件资料添加
         $stuffSql="SELECT S.Id,S.ItemId,S.StuffCname,S.TypeId,S.Price,S.CompanyId,S.BuyerId,S.Relation,S.Diecut,
				    S.Cutrelation,S.StuffId FROM $DataIn.developsheet S
							 LEFT JOIN $DataIn.development D ON D.ItemId=S.ItemId
							 WHERE D.Id='$Id'";
        $stuff=mysql_query($stuffSql,$link_id);
		$i=1;
if($stuffRow=mysql_fetch_array($stuff)){
	do{
		    //$LockSql=" LOCK TABLES $DataIn.stuffdata WRITE"; $LockRes=@mysql_query($LockSql);
	        $ItemId=$stuffRow["ItemId"];
		    $TypeId=$stuffRow["TypeId"];
		    $Price=$stuffRow["Price"];
		    $StuffCname=$stuffRow["StuffCname"];
		    $CompanyId=$stuffRow["CompanyId"];
		    $BuyerId=$stuffRow["BuyerId"];
			$Relation=$stuffRow["Relation"];
			$Diecut=$stuffRow["Diecut"];
			$Cutrelation=$stuffRow["Cutrelation"];
			$developStuffId=$stuffRow["StuffId"];
	 if($developStuffId<=90000){
	        $maxSql = mysql_query("SELECT MAX(StuffId) AS Mid FROM $DataIn.stuffdata",$link_id);
            $StuffId=mysql_result($maxSql,0,"Mid");
            if($StuffId){$StuffId=$StuffId+1; }
                  else{ $StuffId=90001;}
           $inRecode="INSERT INTO $DataIn.stuffdata(Id,StuffId,StuffCname,StuffEname,TypeId,Spec,
		   Weight,Price,Remark,Gfile,Gstate,Gremark,Picture,Jobid,SendFloor,Estate,Locks,
		   Date,GfileDate,Operator) VALUES (NULL,'$StuffId','$StuffCname','','$TypeId','',
           '0.0000','$Price','','','0','','0','0','0','2','0','$Date',NULL,'$Operator')";
           $inAction2=@mysql_query($inRecode);
          // $unLockSql="UNLOCK TABLES"; $unLockRes=@mysql_query($unLockSql);
        if($inAction2){ 
	       $inRecode1="INSERT INTO $DataIn.bps (Id,StuffId,BuyerId,CompanyId,Locks) 
		              VALUES(NULL,'$StuffId','$BuyerId','$CompanyId','0')";
	       $inRres1=@mysql_query($inRecode1);
	       if($inRres1){ 
		          $Log.="<br>$i --名称为 $StuffCname 的配件资料新增成功!配件采购供应商关系设定成功!<br>";
		        }
	       else{
		         $Log.="<div class=redB>$i --名称为 $StuffCname 的配件资料新增成功!但配件采购供应商关系设定不成功!<br></div>";
		         $OperationResult="N";
		        }
	      $inRecode2="INSERT INTO $DataIn.ck9_stocksheet(Id,StuffId,dStockQty,tStockQty,oStockQty,Date)
		              VALUES (NULL,'$StuffId','0','0','0','$Date')";
	      $inRes2=@mysql_query($inRecode2);
	      if($inRes2){
		             $Log.="<br>$i --名称为 $StuffCname 的配件库存资料设定成功!<br>";}
	            else{
		             $Log.="<div class=redB>$i --名称为 $StuffCname 的配件库存资料设定失败!<br></div>";
		             $OperationResult="N";
		            }
	         } 
         else{
	          $Log.="<div class=redB>&nbsp;&nbsp;&nbsp;&nbsp;$i --名称为 $StuffCname 的配件资料新增失败! $inRecode</div>";
	          $OperationResult="N";
	         }	
		 //$LockSql=" LOCK TABLES $DataIn.pands WRITE";$LockRes=@mysql_query($LockSql);
	     $IN_recodeN="INSERT INTO $DataIn.pands (Id,ProductId,StuffId,Relation,Diecut,Cutrelation,Date,
		 Operator)VALUES (NULL,'$ProductId','$StuffId','$Relation','$Diecut','$Cutrelation','$Date','$Operator')"; 
		 echo $IN_recodeN;
	     $resN=@mysql_query($IN_recodeN);
		 //$unLockSql="UNLOCK TABLES"; $unLockRes=@mysql_query($unLockSql);
	     if($resN){
		       $Log.="&nbsp;&nbsp; $i --配件ID号为 $StuffId 的配件已加入产品 $ProductId 的BOM!</br>";
		      }
	      else{
		      $Log.="<div class='redB'>&nbsp;&nbsp; $i --配件ID号为 $StuffId 的配件未能加入产品 $ProductId 的BOM! $IN_recodeN</div></br>";
		      } 	
	  }//end if($developStuffId>=90000)
  else{
    //===============================3.BOM表添加。
	     //$LockSql=" LOCK TABLES $DataIn.pands WRITE";$LockRes=@mysql_query($LockSql);
	     $IN_recodeN="INSERT INTO $DataIn.pands (Id,ProductId,StuffId,Relation,Diecut,Cutrelation,Date,
		 Operator)VALUES (NULL,'$ProductId','$developStuffId','$Relation','$Diecut','$Cutrelation','$Date','$Operator')"; 
		 echo $IN_recodeN;
	     $resN=@mysql_query($IN_recodeN);
		// $unLockSql="UNLOCK TABLES"; $unLockRes=@mysql_query($unLockSql);
	     if($resN){
		       $Log.="&nbsp;&nbsp; $i --配件ID号为 $StuffId 的配件已加入产品 $ProductId 的BOM!</br>";
		      }
	      else{
		      $Log.="<div class='redB'>&nbsp;&nbsp; $i --配件ID号为 $StuffId 的配件未能加入产品 $ProductId 的BOM! $IN_recodeN</div></br>";
		      } 
		 } 
	    $i++;
       }while($stuffRow=mysql_fetch_array($stuff));
      }//end  if($stuffRow=mysql_fetch_array($stuff))
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
		//完成日期的处理
	    if($Gfile!=""){
	    $OldFile=$Gfile;
	    $strFileName = $_FILES['Gfile']['name'];
	    $extendFile=extend_3($strFileName);
	    $PreFileName=$ItemId."_1.".$extendFile;
	    $uploadInfo1=UploadPictures($OldFile,$PreFileName,$FilePath);
		$uploadSTR1=",Gfile='$uploadInfo1'";
	    }
		else{
		$uploadSTR1="";
		}
	    $Gfile=$uploadInfo1==""?0:$uploadInfo1;
		$ItemName=FormatSTR($ItemName);
		$Content=addslashes($Content);
		$SetStr="CompanyId='$CompanyId',ItemName='$ItemName',Content='$Content',Qty='$Qty' $uploadSTR1 $uploadSTR";
		include "../model/subprogram/updated_model_3a.php";
		break;
	}
$ALType="From=$From&Estate=$Estate&Pagination=$Pagination&Page=$Page";
//步骤4：操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
  