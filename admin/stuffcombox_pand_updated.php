<?php
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 子母配件BOM保存");
$fromWebPage="stuffcombox_pand_read";
$nowWebPage="stuffcombox_pand_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
$DateTime=date("Y-m-d H:i:s");
$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$OperationResult="Y";
$url="stuffcombox_pand";
$Log_Item="子母配件BOM";
$Log_Funtion="设置子母配件BOM";
$ALType="From=$From";
//锁定表
$mCheckRow =mysql_fetch_array(mysql_query("SELECT StuffCname  FROM $DataIn.stuffdata   WHERE StuffId='$mStuffId'",$link_id));
$mStuffCname = $mCheckRow["StuffCname"];

$DelSql = "DELETE FROM $DataIn.stuffcombox_bom WHERE mStuffId='$mStuffId'"; 
$DelResult = mysql_query($DelSql);

$x=1;
if ($SIdList!=""){
		$dataArray=explode("|",$SIdList);
		$Count=count($dataArray);
		$Date=date("Y-m-d");
		for ($i=0;$i<$Count;$i++){
				$tempArray=explode("^",$dataArray[$i]);
				$StuffId=$tempArray[0];
				if ($StuffId>0){
					$Relation=$tempArray[1];
					//插入新的关系	
					$IN_recodeN="INSERT INTO $DataIn.stuffcombox_bom (Id,mStuffId,StuffId,Relation,Date,Operator) VALUES (NULL,'$mStuffId','$StuffId','$Relation','$Date','$Operator')";
				    $resN=@mysql_query($IN_recodeN);
					if($resN){
					        $Log.="&nbsp;&nbsp; $x -子配件ID: $StuffId  已设为母配件 $mStuffId 的BOM关系!</br>";
					}
					else{
						 $Log.="<div class='redB'>&nbsp;&nbsp; $x -子配件ID: $StuffId  未设为母配件 $mStuffId 的BOM关系!</div></br>";
					} 
					$x++;
			    }
		}
}

if ($newList!=""){
		$newdataArray=explode("|",$newList);
		$newCount=count($newdataArray);
		for ($j=0;$j<$newCount;$j++){
				 $maxSql = mysql_query("SELECT MAX(StuffId) AS Mid FROM $DataIn.stuffdata",$link_id);
				 $ComStuffId=mysql_result($maxSql,0,"Mid");
				 if($ComStuffId){
					   $ComStuffId=$ComStuffId+1;
					}
				else{
					   $ComStuffId=90001;
			  		}
			  		
				 $newtempArray   = explode("^",$newdataArray[$j]);
				 if ($newtempArray[1]!=""){
					
					$newStuffCname = $mStuffCname . "/". $newtempArray[1];
					$newRelation        = $newtempArray[0];
		
				   $inRecode="INSERT INTO $DataIn.stuffdata (Id,StuffId,StuffCname,StuffEname,TypeId,Spec,Weight,Price,Unit,BoxPcs,ComboxSign,Remark,Gfile,Gstate,Gremark,Picture,
		            Pjobid,PicNumber,Jobid,GicNumber,GcheckNumber,Cjobid,SendFloor,CheckSign,ForcePicSpe,jhDays,DevelopState,Estate,Locks,Date,GfileDate,Operator,OPdatetime, PLocks, creator, created)  SELECT  NULL,'$ComStuffId','$newStuffCname','',TypeId,'','0','0',Unit,'0','1','','','0','','0',Pjobid,PicNumber,JobId,GicNumber,GcheckNumber,Cjobid,
		SendFloor,CheckSign,ForcePicSpe,jhDays,DevelopState,'2','0','$Date',NULL,'$Operator','$DateTime' ,'0','$Operator','$DateTime'
		FROM $DataIn.stuffdata WHERE StuffId=$mStuffId";
		         // echo $inRecode;
			     $inAction=@mysql_query($inRecode);
				if($inAction){ 
							$inSql1="INSERT INTO $DataIn.bps (Id,StuffId,BuyerId,CompanyId,Locks) SELECT NULL,'$ComStuffId',BuyerId,CompanyId,Locks FROM $DataIn.bps WHERE StuffId=$mStuffId";
							$inRres1=@mysql_query($inSql1);
							$inSql2="INSERT INTO $DataIn.ck9_stocksheet (Id,StuffId,dStockQty,tStockQty,oStockQty,mStockQty,Date) VALUES (NULL,'$ComStuffId','0','0','0','0','$Date')";
							$inRes2=@mysql_query($inSql2);
		                    $inSql3  ="INSERT INTO $DataIn.stuffproperty(Id,StuffId,Property)VALUES(NULL,'$ComStuffId','10')";
		                    $inRes3=@mysql_query($inSql3);
							$inSql4="INSERT INTO $DataIn.stuffcombox_bom (Id,mStuffId,StuffId,Relation,Date,Operator) VALUES (NULL,'$mStuffId','$ComStuffId','$newRelation','$Date','$Operator')";
						    $inRes4=@mysql_query($inSql4);
						     
						     $Log.="&nbsp;&nbsp; $x -子配件ID: $StuffId  已设为母配件 $mStuffId 的BOM关系!</br>";
					}
					else{
						    $Log.="<div class='redB'>&nbsp;&nbsp;$x - 添加子配件( $newStuffCname )失败!</div></br>";
					}
					$x++;
		}
   }
}

$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>