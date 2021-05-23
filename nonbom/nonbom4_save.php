<?php 
//EWEN 2013-02-18 OK
include "../model/modelhead.php";
//步骤2：
$Log_Item="非bom配件资料";			//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination";
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$GoodsName=FormatSTR($GoodsName);
$chinese=new chinese;
$Letter=substr($chinese->c($GoodsName),0,1);
$Company=FormatSTR($Company);
$maxSql = @mysql_query("SELECT MAX(GoodsId) AS GoodsId FROM $DataPublic.nonbom4_goodsdata ORDER BY GoodsId DESC LIMIT 1",$link_id);
$GoodsId=@mysql_result($maxSql,0);
if($GoodsId){
	$GoodsId=$GoodsId+1;
	}
else{
	$GoodsId=70001;
	}
//上传文件
if($Attached!=""){//有上传文件
	$FileType=".jpg";
	$OldFile=$Attached;
	$FilePath="../download/nonbom/";
	if(!file_exists($FilePath)){//检查目录是否存在，不存在则先创建
		makedir($FilePath);
		}
	$PreFileName=$GoodsId.$FileType;
	$Attached=UploadFiles($OldFile,$PreFileName,$FilePath);
	if($Attached){
		$Attached=1;
		}
	else{
		$Log.="<div class=redB>非bom配件图片失败！$inRecode </div><br>";
		$Attached=0;
		$OperationResult="N";			
		}
	}
else{
	$Attached=0;
	}
$ByNumber=$ByNumber==""?0:$ByNumber;
$ByCompanyId=$ByCompanyId==""?0:$ByCompanyId;
$WxNumber=$WxNumber==""?0:$WxNumber;
$WxCompanyId=$WxCompanyId==""?0:$WxCompanyId;
$DepreciationId=$DepreciationId==""?0:$DepreciationId;
$GetSign=$GetSign[0]==""?0:1;//模具配件 款项是否收回
$GetQty=$GetQty==""?0:$GetQty; //模具货款收回的条件 单位PCS
$inRecode="INSERT INTO $DataPublic.nonbom4_goodsdata (Id,GoodsId,GoodsName,BarCode,TypeId,Attached,Price,Unit,CkId,nxId,pdDate,
ByNumber,ByCompanyId,WxNumber,WxCompanyId,AssetType,DepreciationId,Salvage,ReturnReasons,Remark,GetSign,GetQty,Date,Estate,Locks,Operator,brand) VALUES (NULL,'$GoodsId','$GoodsName','','$TypeId','$Attached','$Price','$Unit','$CkId','$nxId','$pdDate','$ByNumber','$ByCompanyId','$WxNumber','$WxCompanyId','$AssetType','$DepreciationId','$Salvage','','$Remark','$GetSign','$GetQty','$DateTime','2','0','$Operator','$brand')";
$inAction=@mysql_query($inRecode);
$Id=mysql_insert_id();
if ($inAction){ 
	   $Log.="$TitleSTR 成功!<br>";
	   //保存属性
	   $tempCount=count($Property);
       for($k=0;$k<$tempCount;$k++){
                   $inSql3="INSERT INTO $DataPublic.nonbom4_goodsproperty(Id,GoodsId,Property)VALUES(NULL,'$GoodsId','$Property[$k]')";
                   $inRes3=@mysql_query($inSql3);
             }

       //关联BOM采购供应商
          /* if($BomCompany!=""){
                  $DelSql="DELETE  FROM  $DataPublic.nonbom4_bomcompany  WHERE  GoodsId=$GoodsId";
                   $DelResult=@mysql_query($DelSql);    
                  $BomCompanyArray=explode("@", $BomCompany);
                  $BomCount=count($BomCompanyArray);
                 for($k=0;$k<$BomCount;$k++){
                            $tempArray=explode("~",$BomCompanyArray[$k]);
                           $BomCompanyId=$tempArray[0];
                           $cSign=$tempArray[2];
                           $IN_Sql4="INSERT INTO $DataPublic.nonbom4_bomcompany SELECT NULL,'$GoodsId','$BomCompanyId','$cSign'";
                           $IN_recode4=@mysql_query($IN_Sql4);
                    }
          }*/
	   //写入库存表
	   $inRecode2="INSERT INTO $DataPublic.nonbom5_goodsstock (Id,GoodsId,wStockQty,lStockQty,oStockQty,mStockQty,CompanyId) VALUES (NULL,'$GoodsId','0','0','0','$mStockQty','$CompanyId')";
	   $inRes2=@mysql_query($inRecode2);
	    if($inRes2){
		     $Log.="<br>库存资料设定成功!!!";
	     	}
	else{
		   $Log.="<div class=redB>库存资料设定失败!!! $inRecode2</div>";
		    $OperationResult="N";
		    }
             //默认供应商的单价
            $defaultCount=count($checkdefaultId);
            for($k=0;$k<$defaultCount;$k++){
                 $TempCompanyId=$checkdefaultId[$k];
                 $TempPrice=$checkdefaultPrice[$k];
                 $IN_recode1="INSERT INTO $DataPublic.nonbom4_defaultcompany(Id,GoodsId,CompanyId,Price)VALUE(NULL,'$GoodsId','$TempCompanyId','$TempPrice')";       
                 $IN_res1=mysql_query($IN_recode1);
             }
    	if($ToolsId>0){
	    	$UpdateToolSql = "UPDATE $DataIn.fixturetool SET GoodsId ='$GoodsId' WHERE ToolsId='$ToolsId'";
	    	$UpdateToolResult = mysql_query($UpdateToolSql);
    	}       
    }	
else{
	$Log=$Log."<div class=redB>$TitleSTR 失败! $inRecode</div><br>";
	$OperationResult="N";
	} 
//步骤4：
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
