<?php   
$cgCount=0;
$LockCount=0;
$NoGCount=0;
$NoPICCount=0;
$NoGPCount=0;
$FLockCount=0;
$CheckCount=0;
$okCount=0;
$ywLock=0;   //业务锁定

$AmCount=0;
$PmCount=0;
$YesdayCount=0;
$OtherDaysCount=0;

$KF_A=0; $KF_OVER_A=0;
$KF_B=0; $KF_OVER_B=0;
$KF_C=0; $KF_OVER_C=0;
$SJ_A=0; $SJ_OVER_A=0; //add by zx 2014-12-30

$Other_P=0;

$GPKF_A=0;
$GPKF_B=0;
$GPKF_C=0;
$GPOther_P=0;

$GICKF_A=0;$GICKF_OVER_A=0;
$GICKF_B=0;$GICKF_OVER_B=0;
$GICKF_C=0;$GICKF_OVER_C=0;
$Other_GIC=0;

/*
$YW_LockArray=array();
$YW_Over_Lock=array();
$YW_Over_Lock3=array();
$YW_Over_Lock10=array();
*/
$NoPicLimitTime=$NoPicLimitTime==""?4:$NoPicLimitTime;
$NoGfileLimitTime=$NoGfileLimitTime==""?12:$NoGfileLimitTime;
$curDateTime=date("Y-m-d H:i:s");
$curDate=date("Y-m-d");

$mySql="SELECT 
C.Forshort AS Client,S.Id,S.Mid,S.StockId,S.POrderId,S.StuffId,S.Price,S.OrderQty,S.StockQty,S.AddQty,S.FactualQty,S.CompanyId,S.BuyerId,S.DeliveryDate,S.StockRemark,S.AddRemark,S.Estate,S.Locks,S.ywOrderDTime,Y.OrderPO,Y.Qty as PQty,Y.PackRemark,Y.sgRemark,Y.ShipType,PI.Leadtime,M.Operator,
A.StuffCname,P.cName,A.Gfile,A.Gstate,A.Gremark,A.Picture,A.SendFloor,A.TypeId,U.Name AS UnitName,A.ForcePicSpe,T.ForcePicSign,IF(A.Pjobid=-1,T.PicJobid ,A.Pjobid) as PicJobid,IF(A.Jobid=-1,T.GicJobid,A.Jobid) AS GicJobid
FROM $DataIn.cg1_stocksheet S
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=S.POrderId
LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=Y.OrderNumber 
LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=Y.Id
LEFT JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId
LEFT JOIN $DataPublic.stuffunit U ON U.Id=A.Unit
LEFT JOIN $DataIn.stufftype T ON T.TypeId=A.TypeId  
WHERE 1  AND (T.mainType<2) AND   S.Mid=0 and (S.FactualQty>0 OR S.AddQty>0) ORDER BY S.Estate DESC,S.StockId DESC";
//WHERE 1  AND (T.mainType<2) AND   S.Mid=0 and (S.FactualQty>0 OR S.AddQty>0) ORDER BY S.Estate DESC,S.StockId DESC";

$myResult = mysql_query($mySql,$link_id);
$tempStuffId="";
$DefaultBgColor=$theDefaultColor;
if($myRow = mysql_fetch_array($myResult)){
	//$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);		
	do{
		$m=1;
		$LockRemark="";
		$POrderId=$myRow["POrderId"];
		$tdBGCOLOR=$POrderId==""?"bgcolor='#FFCC99'":"";
		$PQty=$myRow["PQty"];
		$PackRemark=$myRow["PackRemark"];
		$sgRemark=$myRow["sgRemark"];
		$ShipType=$myRow["ShipType"];
		$Leadtime=$myRow["Leadtime"];		
		
		
		$theDefaultColor=$DefaultBgColor;
		$OrderSignColor=$POrderId==""?"bgcolor='#FFCC99'":"";
		
		$Id=$myRow["Id"];
		$StockId=$myRow["StockId"];
		$StuffId=$myRow["StuffId"];
		$cName=$myRow["cName"];
		$Client=$myRow["Client"];
		$LockStockId=$StockId;
		$StockId="<div title='$Client : $cName'>$StockId</div>";
		$StuffCname=$myRow["StuffCname"];
		$TypeId=$myRow["TypeId"];
        $QCImage="";
        $QCImage=$QCImage==""?"&nbsp;":$QCImage;
		$Gremark=$myRow["Gremark"];
		$Gfile=$myRow["Gfile"];
		$tempGfile=$Gfile;  ////2012-10-29
		$Gstate=$myRow["Gstate"];
		$GicJobid=$myRow["GicJobid"];
		//检查是否有图片
		$Picture=$myRow["Picture"];
		$ForcePicSpe=$myRow["ForcePicSpe"];
		$ForcePicSign=$myRow["ForcePicSign"];
		if ($ForcePicSpe>=0){  //-1表示用stufftype用的，否则用它指定
			$ForcePicSign=$ForcePicSpe;  
		}
		
		$ywOrderDTime=$myRow["ywOrderDTime"];  //业务下单时间
		$Hours=ceil((strtotime($curDateTime)-strtotime($ywOrderDTime))/3600);
		
		$PicJobid=$myRow["PicJobid"];
		$Estate=$myRow["Estate"];
		
		//检查是否未确定产品，是则锁定并标底色
		$CheckSignSql=mysql_query("SELECT Id,date FROM $DataIn.yw2_orderexpress WHERE POrderId ='$POrderId' AND Type='2' LIMIT 1",$link_id);
		if($CheckSignRow=mysql_fetch_array($CheckSignSql)){
			$LockRemark="未确定产品";
			$OrderSignColor="bgcolor='#FF0000'";
			$ywLock=$ywLock+1;

		}	//所有业务锁定的无需要显示  modify by zx 20131007	
		
		$CheckSignSql=mysql_query("SELECT Id FROM $DataIn.cg1_lockstock WHERE StockId ='$LockStockId' AND Locks=0 LIMIT 1",$link_id);
		if($CheckSignRow=mysql_fetch_array($CheckSignSql)){
			$LockRemark="未确定产品";
			$OrderSignColor="bgcolor='#FF0000'";
			}				
		if($Estate!=0) {
			$CheckCount=$CheckCount+1;
		}
		if($LockRemark=="") {
			
			switch($ForcePicSign){
				case 0: 
					$ForcePicSign="无图需求";
				break;
				case 1: 
					$ForcePicSign="需要图片";
					//if($Picture!=1) {  //需要图片，而无图片或重新上传或需要
					if($Picture==0) {  //需要图片，而无图片或重新上传或需要审核审核
						$LockRemark="需要图片?重新上传中,正在审核";
						$NoPICCount=$NoPICCount+1;
						switch($PicJobid){
							//case 6:
							case 5:
								$KF_A=$KF_A+1;
								$KF_OVER_A=$Hours>$NoPicLimitTime?$KF_OVER_A+1:$KF_OVER_A;
								break;
							//case 7:
							case 27:
								$KF_B=$KF_B+1;
								$KF_OVER_B=$Hours>$NoPicLimitTime?$KF_OVER_B+1:$KF_OVER_B;
								break;
							//case 32:
							case 34:
								 $KF_C=$KF_C+1;
								 $KF_OVER_C=$Hours>$NoPicLimitTime?$KF_OVER_C+1:$KF_OVER_C;
								break;
							case 5:
								$SJ_A=$SJ_A+1;
								$SJ_OVER_A=$Hours>$NoPicLimitTime?$SJ_OVER_A+1:$SJ_OVER_A;
								break;								
							default:
								$Other_P=$Other_P+1;
								break;
						}	
					}
				break;
				case 2: 
					$ForcePicSign="需要图档";
					//if($Gstate!=1  || $tempGfile=="") {  //需要图档，而无图档或重新上传或需要审核
					if($Gstate==0 || $tempGfile=="") {  //需要图档，而无图档或重新上传或需要审核
						$LockRemark="需要图档?重新上传中?正在审核";
						$NoGCount=$NoGCount+1;
						 switch($GicJobid){
							case 5:
								$GICKF_A=$GICKF_A+1;
								$GICKF_OVER_A=$Hours>$NoGfileLimitTime?$GICKF_OVER_A+1:$GICKF_OVER_A;
								break;
							case 27:
								$GICKF_B=$GICKF_B+1;
								$GICKF_OVER_B=$Hours>$NoGfileLimitTime?$GICKF_OVER_B+1:$GICKF_OVER_B;
								break;
							case 34:
								 $GICKF_C=$GICKF_C+1;
								 $GICKF_OVER_C=$Hours>$NoGfileLimitTime?$GICKF_OVER_C+1:$GICKF_OVER_C;
								break;
							default:
								$Other_GIC=$Other_GIC+1;
								break;
						}
					}				
				break;
				case 3: 
					$ForcePicSign="图片/图档";
					if($Picture!=1 || $Gstate!=1  || $tempGfile=="") {  //需要图片/图档，而无图片/图档或重新上传或需要审核
						$LockRemark="需要图片和图档?重新上传中?正在审核";
						$NoGPCount=$NoGPCount+1;
	
						switch($PicJobid){
							case 6:
								$GPKF_A=$GPKF_A+1;
								break;
							case 7:
								$GPKF_B=$GPKF_B+1;
								break;
							case 32:
								 $GPKF_C=$GPKF_C+1;
								break;
							default:
								$GPOther_P=$GPOther_P+1;
								break;
						}					
						
						
						
						
					}				
				break;
				case 4: 
					$ForcePicSign="强行锁定";
					$LockRemark="强行锁定中，请配件资料管理人解除";
					$FLockCount=$FLockCount+1;
				break;			
			}		
		}
		
		$SendFloor=$myRow["SendFloor"];
		$OrderQty=$myRow["OrderQty"];
		$StockQty=$myRow["StockQty"];
		$AddQty=$myRow["AddQty"];
		$FactualQty=$myRow["FactualQty"];
		$Qty=$AddQty+$FactualQty;
		$Amount=sprintf("%.2f",$Qty*$Price);//本记录金额合计
		//$Estate=$myRow["Estate"];
        $UnitName=$myRow["UnitName"]==""?"&nbsp;":$myRow["UnitName"];        
                $StockRemark=$myRow["StockRemark"];
                $StockRemarkTB="<input type='hidden' id='StockRemark$i' name='StockRemark$i' value='$StockRemark'/>";
                if ($StockRemark=="") {
                    $StockRemark="&nbsp;";
                   }
                else{
                   $StockRemark="<div title='$StockRemark'><img src='../images/remark.gif'></div>"; 
                }
                $AddRemark=$myRow["AddRemark"]==""?"&nbsp;":$myRow["AddRemark"];
		$Locks=1;
		$checkKC=mysql_fetch_array(mysql_query("SELECT oStockQty,mStockQty FROM $DataIn.ck9_stocksheet WHERE StuffId='$StuffId' ORDER BY StuffId",$link_id));
		$oStockQty=$checkKC["oStockQty"];
		$mStockQty=$checkKC["mStockQty"]==0?"&nbsp;":$checkKC["mStockQty"];
		$OrderQty=$OrderQty==""?0:$OrderQty;
		$StockQty=$StockQty==""?0:$StockQty;
		$FactualQty=$FactualQty==""?0:$FactualQty;
		$AddQty=$AddQty==""?0:$AddQty;
		$oStockQty=$oStockQty;
		if ($mStockQty>0){
			$mStockColor="title='最低库存:$mStockQty'";
			$oStockQty="<span style='color:#FF9900;font-weight:bold;'>$oStockQty</span>";
			}
		else{
			$mStockColor="";	
			}
		if($Estate==1){
			//$LockRemark="需审核";
			}
			
		/*	
		//检查是否未确定产品，是则锁定并标底色
		$CheckSignSql=mysql_query("SELECT Id,date FROM $DataIn.yw2_orderexpress WHERE POrderId ='$POrderId' AND Type='2' LIMIT 1",$link_id);
		if($CheckSignRow=mysql_fetch_array($CheckSignSql)){
			$LockRemark="未确定产品";
			$OrderSignColor="bgcolor='#FF0000'";
			$ywLock=$ywLock+1;

		}
		*/
		/*	
		$CheckSignSql=mysql_query("SELECT Id FROM $DataIn.cg1_lockstock WHERE StockId ='$LockStockId' AND Locks=0 LIMIT 1",$link_id);
		if($CheckSignRow=mysql_fetch_array($CheckSignSql)){
			$LockRemark="未确定产品";
			$OrderSignColor="bgcolor='#FF0000'";
			}				
		if($Estate!=0) {
			$CheckCount=$CheckCount+1;
		}
		*/
		if($Locks==0 || $LockRemark!="" || $Estate!=0 ){
			$LockCount=$LockCount+1;
		}
		else {
			$okCount=$okCount+1;
			$ywOrderDTime=$myRow["ywOrderDTime"];  //业务下单时间
			$CurrentDateTime=date("Y-m-d H:i:s");   //当前时间
			
			if($ywOrderDTime!="0000-00-00 00:00:00"){
				$ywOrderDate=substr($ywOrderDTime,0,10);  
				$ywOrderHour=substr($ywOrderDTime,11,2);
				$BuyerId=$myRow["BuyerId"];  //采购人
				$CurrentDate=substr($CurrentDateTime,0,10); 
				$CurrentHour=substr($CurrentDateTime,11,2);
				if($ywOrderDate==$CurrentDate && $ywOrderHour<12) {  //统计上午
					$AmCount=$AmCount+1;
				}
				else{ //1----- 
					if ($ywOrderDate==$CurrentDate && $ywOrderHour>=12) { //统计下午
						$PmCount=$PmCount+1;
					}
					else {
						//统计昨天
						$yesterday=date('Y-m-d',strtotime("$CurrentDate -1 day"));
						if($ywOrderDate==$yesterday) {
							$YesdayCount=$YesdayCount+1;
						}
						else {
							$OtherDaysCount=$OtherDaysCount+1;
						}
					}
				} ////1-----
				
			}
			else {
				$OtherDaysCount=$OtherDaysCount+1;  //以前的没有时间
			}
		}
		$cgCount=$cgCount+1;
		
		}while ($myRow = mysql_fetch_array($myResult));
	}

//以下数据用于Iphone
if ($FromOutPage=="iPhone"){
		 $iPhone_NOPicture=$KF_A+$KF_B+$KF_C+$SJ_A;
	     $iPhone_NOGfile=$NoGCount;
	     $iPhone_NOYwLock=$ywLock;
	     
	     $iPhone_KF_A=$KF_A; $iPhone_KF_B=$KF_B; $iPhone_KF_C=$KF_C; $iPhone_SJ_A=$SJ_A;
	     $iPhone_GICKF_A=$GICKF_A; $iPhone_GICKF_B=$GICKF_B;  $iPhone_GICKF_C=$GICKF_C;
}
else{
		$OLockCount=$LockCount-$NoGCount-$NoPICCount;  //未确定或强行锁=
		$tempModuleIdTemp=1184;// $subRow["ModuleId"];
		$tempModuleId=anmaIn($tempModuleIdTemp,$SinkOrder,$motherSTR);//加密
		
		//$KF_A="<A onfocus=this.blur();  href='../public/cg_cgdsheet_Lock.php?ForceSign=1&isMe=1&PJobId=6' target='_blank'>$KF_A </A>";
		//$KF_B=" / <A onfocus=this.blur();  href='../public/cg_cgdsheet_Lock.php?ForceSign=1&isMe=1&PJobId=7' target='_blank'>$KF_B </A>";
		//$KF_C=" / <A onfocus=this.blur();  href='../public/cg_cgdsheet_Lock.php?ForceSign=1&isMe=1&PJobId=32' target='_blank'>$KF_C </A>";
		
		$GPKF_A="<A onfocus=this.blur();  href='../public/cg_cgdsheet_Lock.php?ForceSign=3&isMe=1&PJobId=6' target='_blank'>$GPKF_A </A>";
		$GPKF_B=" / <A onfocus=this.blur();  href='../public/cg_cgdsheet_Lock.php?ForceSign=3&isMe=1&PJobId=7' target='_blank'>$GPKF_B </A>";
		$GPKF_C=" / <A onfocus=this.blur();  href='../public/cg_cgdsheet_Lock.php?ForceSign=3&isMe=1&PJobId=32' target='_blank'>$GPKF_C </A>";
		
		$contentSTR="<li class=TitleA>$Title</li>";
		$contentSTR.="<li class=DataBL>总计</span></li><li class=DataBR><A onfocus=this.blur(); href='../desk/mainFrame.php?Id=$tempModuleId' target='_blank' onclick='ClickTotal(this,1,$tempModuleIdTemp)' >".$cgCount." </A></li>";
		$contentSTR.="<li class=DataBL>正常</span></li><li class=DataBR><A onfocus=this.blur();  href='../public/cg_cgdsheet_OK.php?&isMe=1' target='_blank' >$okCount</A></li>";
		$contentSTR.="<li class=DataBL>业务锁定</span></li><li class=DataBR><A onfocus=this.blur();  href='../public/cg_cgdsheet_Lock.php?ForceSign=4&isMe=1' target='_blank' >$ywLock</A></li>";
		//$contentSTR.="<li class=DataBL>无图片</span></li><li class=DataBR><A onfocus=this.blur();  href='../public/cg_cgdsheet_Lock.php?ForceSign=1&isMe=1' target='_blank' >$NoPICCount</A></li>";
		
		//$contentSTR.="<li class=DataBL>无图片(A/B/C)</span></li><li class=DataBR>$KF_A $KF_B $KF_C</li>";
		//$contentSTR.="<li class=DataBL>无图档</span></li><li class=DataBR><A onfocus=this.blur();  href='../public/cg_cgdsheet_Lock.php?ForceSign=2&isMe=1' target='_blank' >$NoGCount</A></li>";
		
		//$contentSTR.="<li class=DataBL>无图片/图档</span></li><li class=DataBR><A onfocus=this.blur();  href='../public/cg_cgdsheet_Lock.php?ForceSign=3&isMe=1' target='_blank' >$NoGPCount</A></li>";
		
		//$contentSTR.="<li class=DataBL>无图片/图档(A/B/C)</span></li><li class=DataBR>$GPKF_A $GPKF_B $GPKF_C</li>";
		if ($linkmodule!="") {   //有审核权限
			$contentSTR.="<li class=DataBL>待审核</span></li><li class=DataBR><A onfocus=this.blur(); href='../desk/mainFrame.php?Id=$SubModuleId' target='mainFrame' onclick='ClickTotal(this,1,$SubModuleIdTemp)' >".$CheckCount ."</A></li>";
			}
		else {
			$contentSTR.="<li class=DataBL>待审核</span></li><li class=DataBR><span class='yellowN'>$CheckCount</span></li>";
			}
		$contentSTR.="<li class=DataBL>AM/PM/1day/...</span></li><li class=DataBR><A onfocus=this.blur();  href='../public/cg_cgdsheet_OK.php?&isMe=2' target='_blank'>$AmCount/$PmCount/$YesdayCount/$OtherDaysCount"."</A></li>";
		
		$contentSTR.="<li class=TitleA>无图片</li>";
		$contentSTR.="<li class=DataBL>开发A</span></li><li class=DataBR><A onfocus=this.blur();  href='../public/cg_cgdsheet_Lock.php?ForceSign=1&isMe=1&PJobId=5' target='_blank' >$KF_A</A></li>";
		$contentSTR.="<li class=DataBL>开发B</span></li><li class=DataBR><A onfocus=this.blur();  href='../public/cg_cgdsheet_Lock.php?ForceSign=1&isMe=1&PJobId=27' target='_blank' >$KF_B</A></li>";
		$contentSTR.="<li class=DataBL>开发C</span></li><li class=DataBR><A onfocus=this.blur(); href='../public/cg_cgdsheet_Lock.php?ForceSign=1&isMe=1&PJobId=34' target='_blank' >$KF_C</A></li>";
		$contentSTR.="<li class=DataBL>设计</span></li><li class=DataBR><A onfocus=this.blur(); href='../public/cg_cgdsheet_Lock.php?ForceSign=1&isMe=1&PJobId=35' target='_blank' >$SJ_A</A></li>";

		$contentSTR.="<li class=TitleA>无图档</li>";
		$contentSTR.="<li class=DataBL>开发A</span></li><li class=DataBR><A onfocus=this.blur();  href='../public/cg_cgdsheet_Lock.php?ForceSign=2&isMe=1&PJobId=5' target='_blank' >$GICKF_A</A></li>";
		$contentSTR.="<li class=DataBL>开发B</span></li><li class=DataBR><A onfocus=this.blur();  href='../public/cg_cgdsheet_Lock.php?ForceSign=2&isMe=1&PJobId=27' target='_blank' >$GICKF_B</A></li>";
		$contentSTR.="<li class=DataBL>开发C</span></li><li class=DataBR><A onfocus=this.blur(); href='../public/cg_cgdsheet_Lock.php?ForceSign=2&isMe=1&PJobId=34' target='_blank' >$GICKF_C</A></li>";
}
?> 