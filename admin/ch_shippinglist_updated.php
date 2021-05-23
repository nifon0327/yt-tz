<?php   
include "../model/modelhead.php";
$fromWebPage=$funFrom."_".$From;
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="出货资料";		//需处理
$upDataSheet="$DataIn.ch1_shipmain";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$x=1;
switch($ActionId){
	case 7:
		$Log_Funtion="锁定";	$SetStr="Locks=0";				include "../model/subprogram/updated_model_3d.php";		break;
	case 8:
		$Log_Funtion="解锁";	$SetStr="Locks=1";				include "../model/subprogram/updated_model_3d.php";		break;
	case 137://出货解锁
	  //  echo "Ids:" . $Ids;
	   $IdArr=explode("|", $Ids) ;
	   $Ids="";
	   $Lens=count($IdArr);
		for($i=0;$i<$Lens;$i++){
			$Id=$IdArr[$i] ;
			if($Id!=""){
				 $Ids=$Ids==""?$Id:$Ids .",".$Id;
				}
		}
		$Date=date("Y-m-d");
		$InsertSQL="REPLACE INTO $DataIn.ch1_unlock SELECT NULL,Id,POrderId,'$Reason',1,0,'$Date','$Operator','0','$Operator',NOW(),'$Operator',NOW()  FROM $DataIn.ch1_shipsplit WHERE Id IN ($Ids) ";
		
		$result1=mysql_query($InsertSQL,$link_id); 
			if($result1){
				$Log="ID号在 $Ids 的待出订单解锁成功.</br>";
				}
			else{
				$Log="ID号为 $Ids 的待出订单解锁失败! $InsertSQL</br>";
				$OperationResult="N";
		}			
	   break;
	case '20':
		$Log_Funtion="更新备注";	$SetStr="Remark='$Remark'";	$checkid[0]=$Mid; include "../model/subprogram/updated_model_3d.php";		break;
	case '21':
		$Log_Funtion="更新银行账号";	$SetStr="BankId='$BankId'";	$checkid[0]=$Mid; include "../model/subprogram/updated_model_3d.php";		break;
	case '22':
		$Log_Funtion="更新报关公司";	
		//$checkid[0]=$Mid;
        $FilePath="../download/shiptype/";
	    if(!file_exists($FilePath)){
			  makedir($FilePath);
			}
        if($Attached!=""){
	          $OldFile=$Attached;
	          $FileType=substr("$Attached_name", -4, 4);
	          $PreFileName=$Mid.$FileType;
	          $uploadInfo=UploadPictures($OldFile,$PreFileName,$FilePath);
             }
         $Attached=$uploadInfo==""?"":$uploadInfo;
		$count_Temp=mysql_query("SELECT count( * ) AS counts FROM $DataIn.ch1_shiptypedata WHERE Mid='$Mid'  LIMIT 1",$link_id);  
		$counts=mysql_result($count_Temp,0,"counts");
		if ($counts<=0){ //不存在则加入
			$insQL="INSERT INTO $DataIn.ch1_shiptypedata(Id,Mid,Type,CompanyId,Attached,BgBillNum,Locks,Operator,Estate,
			PLocks,creator, created,modifier,modified,Date) values (NULL ,'$Mid', '1','$bgCompanyId','$Attached','$BgBillNum',
			'0','$Operator', '1', '0', '$Operator', '$DateTime', '$Operator', '$DateTime', '$DateTime')";
			$result1=mysql_query($insQL,$link_id); //执行，不管是否成功
			if($result1){
				$Log="ID号在 $Mid 的记录成功 $Log_Funtion.</br>";
				}
			else{
				$Log="ID号为 $Mid 的记录$Log_Funtion 失败! $insQL</br>";
				$OperationResult="N";
				}			
		}
		else {
			$sql = "UPDATE $DataIn.ch1_shiptypedata SET Type='1',CompanyId='$bgCompanyId' ,Attached='$Attached',BgBillNum='$BgBillNum' 
			WHERE Mid='$Mid'";
			$result = mysql_query($sql);
			if($result){
				$Log="ID号在 $Mid 的记录成功 $Log_Funtion.</br>";
				}
			else{
				$Log="ID号为 $Mid 的记录$Log_Funtion 失败! $sql</br>";
				$OperationResult="N";
				}
		}
		
		break;

	case '23':
		$Log_Funtion="更新出货方式";	$SetStr="Ship='$Ship'";	$checkid[0]=$Mid; 
		include "../model/subprogram/updated_model_3d.php";		break;	
		
	case 26:
		$Log_Funtion="Invoice重置";
		$CheckSign=mysql_fetch_array(mysql_query("SELECT Sign,ShipType FROM $DataIn.ch1_shipmain WHERE Id='$Id'",$link_id));
		$ShipSign=$CheckSign["Sign"];
		$ShipType=$CheckSign["ShipType"];	
		if(($ShipType=='credit') || ($ShipType=='debit') ){  //说明是Credit note 或者 是debit note
			include "ch_creditnoteBlue_topdf.php";
			}
		else{
			include "ch_shippinglistBlue_toinvoice.php";
			}
			$fromWebPage=$funFrom."_read";
		break;
		
	case 153:
		$Log_Funtion="Invoice重置(新版)";
		$CheckSign=mysql_fetch_array(mysql_query("SELECT Sign,ShipType FROM $DataIn.ch1_shipmain WHERE Id='$Id'",$link_id));
		$ShipSign=$CheckSign["Sign"];
		$ShipType=$CheckSign["ShipType"];		
		if(($ShipType=='credit') || ($ShipType=='debit') ){  //说明是Credit note 或者 是debit note	
			include "ch_creditnoteBlue_topdf.php";
			}
		else{
			include "ch_shippinglistBlue_toinvoice.php";
			}
			$fromWebPage=$funFrom."_read";
		break;
		
	case 79:
		$Log_Funtion="Invoice重置";
		$NewAndOld="Old";
		$CheckSign=mysql_fetch_array(mysql_query("SELECT Sign,ShipType FROM $DataIn.ch1_shipmain WHERE Id='$Id'",$link_id));
		$ShipSign=$CheckSign["Sign"];
		$ShipType=$CheckSign["ShipType"];	
		if(($ShipType=='credit') || ($ShipType=='debit') ){  //说明是Credit note 或者 是debit note		
			include "ch_creditnoteBlue_topdf.php";
			}
		else{
			//include "ch_shippinglist_toinvoice.php";
			include "ch_shippinglistBlue_toinvoice.php";
			}
		break;		
	case 29:
		$Log_Funtion="出货";	
		$UpSql="UPDATE $DataIn.ch1_shipmain M 
		LEFT JOIN $DataIn.ch1_shipsheet C ON C.Mid=M.Id 
		LEFT JOIN $DataIn.ch5_sampsheet S ON S.SampId=C.POrderId 
       SET M.Estate='0',M.OPdatetime='$DateTime',S.Estate='0' WHERE M.Id in ($Id)";
		$UpResult = mysql_query($UpSql);$UpRows=mysql_affected_rows();
		if($UpResult && $UpRows>0){
		       $UpOrderSql="UPDATE $DataIn.yw1_ordersheet S 
                   LEFT JOIN (  SELECT IFNULL(SUM(C.Qty),0) AS shipQty,C.POrderId 
                              FROM $DataIn.ch1_shipsheet C 
                              LEFT JOIN $DataIn.ch1_shipmain M  ON M.Id=C.Mid
                             WHERE 1 AND M.Estate=0 AND   C.POrderId IN (SELECT POrderId FROM $DataIn.ch1_shipsheet WHERE Mid in ($Id))   GROUP BY  C.POrderId
                    ) A ON A.POrderId=S.POrderId
                   SET S.Estate=0  WHERE S.Qty=A.shipQty";
            $UpOrderResult=@mysql_query($UpOrderSql);
			$Log="出货单 $Id 状态更新为已出货,相关订单和随货项目的状态亦更新成功.<br>";
			include "ch_shippinglistBlue_toinvoice.php";

//            include_once "../weixin/weixin_api.php";
//
//            $weixin = new weixin_api();
//
//            $touser = 'op_Tyw_8h2hzceNzjvmkDMICU60s'; //微信已出货 open_id
//
//            $next_user = '客户';//发送给的用户名字，与$touser相对应
//
//            $login_user = $_SESSION['Login_Name'];  //当前登录用户
//
//            $Log_Item = $Log_Funtion;  //当前操作
//
//            $login_time = date('Y-m-d H:i:s');//操作时间
//
//            $time = explode(' ', $login_time);
//
//            $time = $time[1];
//
//            $login_detail = $login_user.'于今日'.$time.'完成'.$Log_Item.'流程。现需要您完成下一步"已出货"工作，请及时登录研砼治筑运营平台进行操作。';//登录详情
//
//            $remark = "\n 流程测试，如有疑问，请及时联系".$login_user."或ＩＴ部。";//备注
//
//            $res = $weixin->send_login_temp_msg($touser, $login_user, $next_user, $Log_Item, $login_time, $login_detail, $remark);
//
//            if ($res){
//                $Log.="<br>已通知 <span style='color: red'>$next_user</span> 进行下一步操作. <br>";
//            }

			}
		else{
			$Log="<div class='redB'>出货单 $Id 状态更新为已出货的操作失败.</div><br>";
			$OperationResult="N";
			}	
		/********************************/
		if($DeliverySign==0){
		   $DelSql="DELETE FROM $DataIn.ch1_shipout WHERE ShipId='$Id'";
		   $DelResult=mysql_query($DelSql);
		   $InSql="INSERT INTO $DataIn.ch1_shipout(Id, ShipId, Estate, Locks, Operator)VALUES(NULL,'$Id','1','0','$Operator')";
		   //echo $InSql;
		   $InRecode=mysql_query($InSql);
		   if($InRecode && mysql_affected_rows()>0){
		      $Log.="<br>不发货订单登记成功!<br>";
		       }
		   else{
		      $Log.="<div class='redB'>不发货订单登记失败!</div><br>";
			  $OperationResult="N";
		       }
		   }
		break;
	case 299:
		$fields=explode(",",$ids);
		$counts=count($fields);
		for($q=0;$q<$counts;$q++) {
			$Id = $fields[$q];
			$Log_Funtion = "出货";
			$UpSql = "UPDATE $DataIn.ch1_shipmain M 
				LEFT JOIN $DataIn.ch1_shipsheet C ON C.Mid=M.Id 
				LEFT JOIN $DataIn.ch5_sampsheet S ON S.SampId=C.POrderId 
			   SET M.CarNo = '$CarNo' ,M.Estate='0',M.OPdatetime='$DateTime',S.Estate='0' WHERE M.Id in ($Id)";
			$UpResult = mysql_query($UpSql);
			$UpRows = mysql_affected_rows();
			if ($UpResult && $UpRows > 0) {
				$UpOrderSql = "UPDATE $DataIn.yw1_ordersheet S 
						   LEFT JOIN (  SELECT IFNULL(SUM(C.Qty),0) AS shipQty,C.POrderId 
									  FROM $DataIn.ch1_shipsheet C 
									  LEFT JOIN $DataIn.ch1_shipmain M  ON M.Id=C.Mid
									 WHERE 1 AND M.Estate=0 AND   C.POrderId IN (SELECT POrderId FROM $DataIn.ch1_shipsheet WHERE Mid in ($Id))   GROUP BY  C.POrderId
							) A ON A.POrderId=S.POrderId
						   SET S.Estate=0  WHERE S.Qty=A.shipQty";
				$UpOrderResult = @mysql_query($UpOrderSql);
				$Log .= "<br>出货单 $Id 状态更新为已出货,相关订单和随货项目的状态亦更新成功.<br>";
				include "ch_shippinglistBlue_toinvoice.php";

                //更新出货明细状态
	 	                $pUpSql="UPDATE $DataIn.ch1_shipsplit  SET Estate='0' WHERE Id IN ($Id)";
		                $pUpResult=@mysql_query($pUpSql);
//				include_once "../weixin/weixin_api.php";
//
//				$weixin = new weixin_api();
//
//				$touser = 'op_Tyw_8h2hzceNzjvmkDMICU60s'; //微信已出货 open_id
//
//				$next_user = '客户';//发送给的用户名字，与$touser相对应
//
//				$login_user = $_SESSION['Login_Name'];  //当前登录用户
//
//				$Log_Item = $Log_Funtion;  //当前操作
//
//				$login_time = date('Y-m-d H:i:s');//操作时间
//
//				$time = explode(' ', $login_time);
//
//				$time = $time[1];
//
//				$login_detail = $login_user . '于今日' . $time . '完成' . $Log_Item . '流程。现需要您完成下一步"已出货"工作，请及时登录研砼治筑运营平台进行操作。';//登录详情
//
//				$remark = "\n 流程测试，如有疑问，请及时联系" . $login_user . "或ＩＴ部。";//备注
//
//				$res = $weixin->send_login_temp_msg($touser, $login_user, $next_user, $Log_Item, $login_time, $login_detail, $remark);
//
//				if ($res) {
//					$Log .= "已通知 <span style='color: red'>$next_user</span> 进行下一步操作. <br>";
//				}

			} else {
				$Log .= "<div class='redB'>出货单 $Id 状态更新为已出货的操作失败.</div><br>";
				$OperationResult = "N";
			}
		}
		break;
	case 34:
		$Log_Funtion="退回";
		$CheckSign=mysql_fetch_array(mysql_query("SELECT Sign,ShipType FROM $DataIn.ch1_shipmain WHERE Id='$Id'",$link_id));
		$ShipSign=$CheckSign["Sign"];
		$ShipType=$CheckSign["ShipType"];	
		//if($ShipSign==-1){
		if(($ShipType=='credit') || ($ShipType=='debit') ){  //说明是Credit note 或者 是debit note				
			$Log.="<div class='redB'>此出货单为扣款资料，不能退回.</div>";
			}
		else{
			//锁定表
			//订单的状态更新-2		随货项目的状态更新-1	扣款资料的状态更新-1
			$UpSql="UPDATE $DataIn.ch1_shipmain M 
			LEFT JOIN $DataIn.ch1_shipsheet C ON C.Mid=M.Id 
			LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=C.POrderId 
			LEFT JOIN $DataIn.ch5_sampsheet S ON S.SampId=C.POrderId SET M.Estate='1',Y.Estate='4',S.Estate='2' WHERE M.Id='$Id'";
			$UpResult = mysql_query($UpSql);$UpRows=mysql_affected_rows();
			if($UpResult && $UpRows>0){
		       $UpOrderSql2="UPDATE $DataIn.yw1_ordersheet S 
                   LEFT JOIN (  SELECT IFNULL(SUM(C.Qty),0) AS shipQty,C.POrderId 
                              FROM $DataIn.ch1_shipsheet C 
                             WHERE  C.POrderId IN (SELECT POrderId FROM $DataIn.ch1_shipsheet WHERE Mid=$Id)   GROUP BY  C.POrderId
                    ) A ON A.POrderId=S.POrderId
                   SET S.Estate=2  WHERE S.Qty!=A.shipQty";
               $UpOrderResult2=@mysql_query($UpOrderSql2);

		       $UpOrderSql4="UPDATE $DataIn.yw1_ordersheet S 
                   LEFT JOIN (  SELECT IFNULL(SUM(C.Qty),0) AS shipQty,C.POrderId 
                              FROM $DataIn.ch1_shipsheet C 
                             WHERE  C.POrderId IN (SELECT POrderId FROM $DataIn.ch1_shipsheet WHERE Mid=$Id)   GROUP BY  C.POrderId
                    ) A ON A.POrderId=S.POrderId
                   SET S.Estate=4  WHERE S.Qty==A.shipQty";
               $UpOrderResult4=@mysql_query($UpOrderSql4);

				$Log="出货单 $Id 退回成功,状态更新为准备出货,相关订单和随货项目的状态亦更新成功.<br>";
				}
			else{
				$Log="<div class='redB'>出货单 $Id 退回失败. $UpSql </div><br>";
				$OperationResult="N";
				}
			}
		break;
	case 35:
		$Log_Funtion="取消出货";		include "ch_shippinglist_cencel.php";		break;
	case 36://附件处理
		$FilePath="../download/invoice/";
		if(($ShipType=='credit') || ($ShipType=='debit') ){  //说明是Credit note 或者 是debit note
			$Log_Item="扣款单";
			$upResult = mysql_query("SELECT InvoiceNO FROM $DataIn.ch1_shipmain WHERE Id='$Id' LIMIT 1",$link_id);
			if($upData = mysql_fetch_array($upResult)){
				$InvoiceNO=$upData["InvoiceNO"].".pdf";
				//上传新的扣款单
				if($Attached!=""){
					$OldFile1=$Attached;
					$uploadInfo1=UploadFiles($OldFile1,$InvoiceNO,$FilePath);
					if($uploadInfo1!=""){
						$Log="新的扣款单PDF文件更新成功. ";
						}
					else{
						$Log="<div class='redB'>新的扣款单更新失败!</div>";
						$OperationResult="N";
						}
					}
				//上传结束
				}
			else{
				$Log="<div class='redB'>新的扣款单更新失败!</div>";
				$OperationResult="N";
				}
			}
		else{
			$Log_Item="Invoice附件";
			$Date=date("Y-m-d");
			$EndNumber=1;
			$checkEndFile=mysql_fetch_array(mysql_query("SELECT MAX(Picture) AS EndPicture FROM $DataIn.ch7_shippicture WHERE Mid='$Id'",$link_id));
			$EndFile=$checkEndFile["EndPicture"];
			if($EndFile!=""){
				$TempArray1=explode("_",$EndFile);
				$TempArray2=explode(".",$TempArray1[1]);
				$EndNumber=$TempArray2[0]+1;
				}
			$uploadNums=count($Picture);
			for($i=0;$i<$uploadNums;$i++){
				//上传文档				
				$upPicture=$Picture[$i];
				$Remark=$Remark[$i];
				$TempOldImg=$OldImg[$i];//原文件名
				$TempOldId=$OldId[$i];//原ID号
				if($upPicture!=""){	
					$OldFile=$upPicture;
					//检查是否有原档，如果有则使用原档名称，如果没有，则分配新档名
					if($TempOldImg!=""){
						$PreFileName=$TempOldImg;
						}
					else{
						$PreFileName=$Number."_".$EndNumber.".jpg";
						}
					$uploadInfo=$PreFileName;
					$uploadInfo=UploadFiles($OldFile,$PreFileName,$FilePath);
					if($uploadInfo!=""){
						if($TempOldImg==""){//写入记录
							$inRecode="INSERT INTO $DataIn.ch7_shippicture (Id,Mid,Remark,Picture,Date,Locks,Operator) VALUES (NULL,'$Id','$Remark','$uploadInfo','$Date','0','$Operator')";
							$inAction=@mysql_query($inRecode);
							if($inAction){
								$Log.="出货单 $Id 的附加文档 $uploadInfo 添加成功.<br>";
								$EndNumber++;}
							else{
								$Log.="<div class='redB'>出货单 $Id 的附加文档 $uploadInfo 添加失败. $inAction </div><br>";
								$OperationResult="N";
								}
							}
						else{				//更新记录
							$Log.="出货单 $Id 的附加文档 $uploadInfo 更新成功.<br>";
							$UpSql="UPDATE $DataIn.ch7_shippicture SET Remark='$Remark',Picture='$uploadInfo',Date=='$Date',Locks='0',Operator='$Operator')";
							$UpResult=mysql_query($UpSql);
							if($UpResult ){
								$Log.="出货单 $Id 的附加文档说明更新成功.<br>";
								}
							else{
								$Log.="<div class='redB'>出货单 $Id 的附加文档说明更新失败. $UpSql </div><br>";
								$OperationResult="N";
								}
							}
						}//end if($uploadInfo!="")
					}//end if($upPicture!="")
				}//end for($i=0;$i<$uploadNums;$i++)
				//重置Invoice
				//include "ch_shippinglist_toinvoice.php";
				include "ch_shippinglistBlue_toinvoice.php";
			}
		break;
	case 59://生成扣款单	固定银行帐号
		$Log_Item="扣款资料";			//需处理
		//保存主扣款单
		//锁定表
		//银行帐号处理
		/*
		if($Login_cSign==7){
			switch($CompanyId){

			case 1003:  //Laz
			case 1018:  //EUR
			case 1024:  //Kon
					$BankId=4;	
					break;
			default:  
				$BankId=5;
				break;					
				}
			}
		else{
			$BankId=3;					//阿香帐号
			}
		*/
		
		$info_comefrom="credit";  
		include "subprogram/mybank_info.php";//银行卡信息 add by zx 2011-10-25
		
		//$LockSql=" LOCK TABLES $DataIn.ch1_shipmain WRITE";$LockRes=@mysql_query($LockSql);
		$checkNumber=mysql_fetch_array(mysql_query("SELECT MAX(Number) AS Number FROM $DataIn.ch1_shipmain",$link_id));
		$Number=$checkNumber["Number"]+1;

		$Sign=-1;
		$ShipType='credit';
		$ShipDate=$Date;
		$mainInSql="INSERT INTO $DataIn.ch1_shipmain (Id,CompanyId,ModelId,BankId,Number,InvoiceNO,InvoiceFile,Wise,Notes,Terms,PaymentTerm,PreSymbol,Date,Estate,Locks,Sign,Ship,ShipType,cwSign,Remark,Operator) 
VALUES (NULL,'$CompanyId','$ModelId','$BankId','$Number','$InvoiceNO','1','$Wise','','','','','$ShipDate','0','1','$Sign','-1','$ShipType','1','','$Operator')";

		$mainInAction=@mysql_query($mainInSql);
		$Mid=mysql_insert_id();
		if($mainInAction){
			$Log="1-主出货单 $Mid 保存成功.<br>";
			$checkNumber=mysql_fetch_array(mysql_query("SELECT MAX(Number) AS Number FROM $DataIn.ch6_creditnote",$link_id));
			$thisNumber=$checkNumber["Number"];
			$cnSaveSql="";
			for($i=0;$i<$IdCount;$i++){
				$IdTemp="checkid".strval($i);
				$Id=$$IdTemp;
				if($Id!=""){
					//拆分为PO和注释
					$TEMP=explode("|",$Id);
					$OrderPO=$TEMP[0];
					$Description=$TEMP[1];
					$Qty="Qty".strval($i); 
					$thisQty=$$Qty;
					$Price="Price".strval($i);
					$thisPrice=$$Price;
					$thisNumber++;
					$cnSaveSql=$cnSaveSql==""?"INSERT INTO $DataIn.ch6_creditnote (Id,Mid,PO,CompanyId,Number,Description,Qty,Price,Date,Estate,Locks,Operator) VALUES (NULL,'$Mid','$OrderPO','$CompanyId','$thisNumber','$Description','$thisQty','$thisPrice','$DateTime','0','0','$Operator')":$cnSaveSql.",(NULL,'$Mid','$OrderPO','$CompanyId','$thisNumber','$Description','$thisQty','$thisPrice','$DateTime','0','0','$Operator')"; 
					}
				}
			if($cnSaveSql!=""){
				$cnAction=@mysql_query($cnSaveSql);
				//$unLockSql="UNLOCK TABLES";$unLockRes=@mysql_query($unLockSql);
				if($cnAction){
					$Log.="2-扣款资料保存成功.<br>";
					$InSheetData="INSERT INTO $DataIn.ch1_shipsheet SELECT NULL,Mid,'0',Number,'0',Qty,Price,'1',
					'3','1','1','1','0','$Operator',NOW(),'$Operator',NOW(),CURDATE(),'$Operator' FROM $DataIn.ch6_creditnote WHERE Mid='$Mid'";
				
					$InSheetAction=@mysql_query($InSheetData);
					if($InSheetAction){
						$Log.="3-出货明细记录保存成功. $InSheetData<br>";
						//生成PDF文档
						$Id=$Mid;
						//include "ch_creditnote_topdf.php";
						include "ch_creditnoteBlue_topdf.php";
						}
					else{
						$Log.="<div class='redB'>3-出货明细记录保存失败. $InSheetData</div><br>";
						$OperationResult="N";
						}
					}
				else{
					$Log.="<div class='redB'>2-扣款资料保存失败. $cnSaveSql</div><br>";
					$OperationResult="N";
					}
				}
			else{//如果没有扣款资料
				$OperationResult="N";
				//删除主单
				$DelSql = "DELETE FROM $DataIn.ch1_shipmain WHERE Id=$Id"; 
				$DelResult = mysql_query($DelSql);
				if($DelResult && mysql_affected_rows()>0){
					//$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataIn.ch1_shipmain");
					$Log.="<div class='redB'>2-没有扣款资料,删除主出货单 $Mid 成功.</div><br>";
					}
				else{
					$Log.="<div class='redB'>2-没有扣款资料,删除主出货单 $Mid 失败.</div><br>";
					}
				}
			}
		else{
			$Log="<div class='redB'>1-主出货单保存失败,终止操作. $mainInSql</div><br>";
			$OperationResult="N";
			}
		break;
case 801://上传快递签收单
	if($Attached!=""){//有上传文件
		$FileType=".pdf";
		$OldFile=$Attached;
		$FilePath="../download/ExpressReback/";
		if(!file_exists($FilePath)){
			makedir($FilePath);
			}
		$PreFileName=$Mid."_".$ExpressNum.$FileType;
		$Attached=UploadFiles($OldFile,$PreFileName,$FilePath);
		if($Attached){
		        	$Log.="&nbsp;&nbsp;快递签收单上传成功！ <br>";
                  $CheckResult = mysql_fetch_array(mysql_query("SELECT  Id FROM $DataIn.ch1_shipfile WHERE ShipId=$Mid",$link_id));
                  if($CheckResult["Id"]>0){
                         $UpdateSql = "UPDATE   $DataIn.ch1_shipfile  SET ExpressNum='$ExpressNum' WHERE ShipId=$Mid";  
                         $UpdateResult=@mysql_query($UpdateSql);
                         if($UpdateResult && mysql_affected_rows()>0){
		                     	$Log.="&nbsp;&nbsp;快递签收单上传成功！ <br>";
                                }
		                        else{
		                        	$Log.="<div class=redB>&nbsp;&nbsp;快递签收单上传失败！ </div><br>";
			                        $OperationResult="N";			
		                       	}
                      }
                 else{
                    $InSql ="INSERT INTO  $DataIn.ch1_shipfile(Id,ShipId,ExpressNum,ForwardNum,
                    BillNum,BgBillNum)VALUES(NULL,'$Mid','$ExpressNum','','','')"; 
                     $InResult=@mysql_query($InSql);
                        if($InResult && mysql_affected_rows()>0){
		                     	$Log.="&nbsp;&nbsp;快递签收单上传成功！ <br>";
                                }
		                        else{
		                        	$Log.="<div class=redB>&nbsp;&nbsp;快递签收单上传失败！ </div><br>";
			                        $OperationResult="N";			
		                       	}
                     }
			}
		else{
			$Log.="<div class=redB>&nbsp;&nbsp;快递签收单上传失败！ </div><br>";
			$OperationResult="N";			
			}
		}
      break;
	 case 802: //上传Forward签收单
	if($Attached!=""){//有上传文件
		$FileType=".pdf";
		$OldFile=$Attached;
		$FilePath="../download/ForwardReback/";
		if(!file_exists($FilePath)){
			makedir($FilePath);
			}
		$PreFileName=$Mid."_".$ForwardNum.$FileType;
		$Attached=UploadFiles($OldFile,$PreFileName,$FilePath);
		if($Attached){
                  $CheckResult = mysql_fetch_array(mysql_query("SELECT  Id FROM $DataIn.ch1_shipfile WHERE ShipId=$Mid",$link_id));
                  if($CheckResult["Id"]>0){
                         $UpdateSql = "UPDATE   $DataIn.ch1_shipfile  SET ForwardNum='$ForwardNum' WHERE ShipId=$Mid";  
                         $UpdateResult=@mysql_query($UpdateSql);
                        if($UpdateResult && mysql_affected_rows()>0){
		                     	$Log.="&nbsp;&nbsp;Forward签收单上传成功！ <br>";
                                }
		                        else{
		                        	$Log.="<div class=redB>&nbsp;&nbsp;Forward签收单上传失败！ </div><br>";
			                        $OperationResult="N";			
		                       	}
                      }
                 else{
                        $InSql ="INSERT INTO  $DataIn.ch1_shipfile(Id,ShipId,ExpressNum,ForwardNum,
                        BillNum,BgBillNum)VALUES(NULL,'$Mid','','$ForwardNum','','')"; 
                        $InResult=@mysql_query($InSql);
                        if($InResult && mysql_affected_rows()>0){
		                     	$Log.="&nbsp;&nbsp;Forward签收单上传成功！ <br>";
                                }
		                        else{
		                        	$Log.="<div class=redB>&nbsp;&nbsp;Forward签收单上传失败！ </div><br>";
			                        $OperationResult="N";			
		                       	}
                     }
			}
		else{
			$Log.="<div class=redB>&nbsp;&nbsp;Forward签收单上传失败！ </div><br>";
			$OperationResult="N";			
			}
		}

      break;
      
      
     case 803: //上传发票单
        $FilePath="../download/billback/";
		if(!file_exists($FilePath)){
			makedir($FilePath);
		 }
        $tempMidArray = explode("^^",$Mids);
        $MidCount  = count($tempMidArray);
        
		if($Attached!=""){//有上传文件
		   $FileType=".pdf";
		   $OldFile=$Attached;
		   $PreFileName=$CompanyId."_".$BillNum.$FileType;
		   $Attached=UploadFiles($OldFile,$PreFileName,$FilePath);
		    for($tempk = 0 ;$tempk<$MidCount;$tempk++){
		          $tempMid  = $tempMidArray[$tempk]; 
		          if($tempMid>0){
		                  $CheckResult = mysql_fetch_array(mysql_query("SELECT  Id FROM $DataIn.ch1_shipfile 
		                  WHERE ShipId=$tempMid",$link_id));
		                  if($CheckResult["Id"]>0){
		                         $UpdateSql = "UPDATE   $DataIn.ch1_shipfile  SET BillNum='$BillNum',modifier='$Operator',modified=NOW() WHERE ShipId=$tempMid";  
		                         $UpdateResult=@mysql_query($UpdateSql);
		                         if($UpdateResult && mysql_affected_rows()>0){
				                     	$Log.="&nbsp;&nbsp;发票单单上传成功！ <br>";
		                            }
			                        else{
			                        	$Log.="<div class=redB>&nbsp;&nbsp;发票单上传失败！ </div><br>";
				                        $OperationResult="N";			
			                       	}
		                      }
		                 else{
		                        $InSql ="INSERT INTO  $DataIn.ch1_shipfile(Id,ShipId,ExpressNum,ForwardNum,
		                        BillNum,BgBillNum,Operator,Date,creator,created)VALUES(NULL,'$tempMid','','','$BillNum','','$Operator',CURDATE(),'$Operator',NOW())"; 
		                        $InResult=@mysql_query($InSql);
		                        if($InResult && mysql_affected_rows()>0){
				                     	$Log.="&nbsp;&nbsp;发票单上传成功！ <br>";
		                            }
			                        else{
			                        	$Log.="<div class=redB>&nbsp;&nbsp;发票单上传失败！ </div><br>";
				                        $OperationResult="N";			
			                       	}
		                      }
		                }
                 }
			}else{
				$Log.="<div class=redB>&nbsp;&nbsp;发票单未选择！ </div><br>";
		        $OperationResult="N";	
			}

      break;


     case 804: //上传发票单
		if($Attached!=""){//有上传文件
			$FileType=".pdf";
			$OldFile=$Attached;
			$FilePath="../download/bgbillback/";
			if(!file_exists($FilePath)){
				makedir($FilePath);
				}
			$PreFileName=$Mid."_".$BgBillNum.$FileType;
			$Attached=UploadFiles($OldFile,$PreFileName,$FilePath);
			if($Attached){
	                  $CheckResult = mysql_fetch_array(mysql_query("SELECT  Id FROM $DataIn.ch1_shipfile 
	                  WHERE ShipId=$Mid",$link_id));
	                  if($CheckResult["Id"]>0){
	                         $UpdateSql = "UPDATE   $DataIn.ch1_shipfile  SET BgBillNum='$BgBillNum' WHERE ShipId=$Mid";  
	                         $UpdateResult=@mysql_query($UpdateSql);
	                        if($UpdateResult && mysql_affected_rows()>0){
		                     	$Log.="&nbsp;&nbsp;报关单上传成功！ <br>";
                            }
	                        else{
	                        	$Log.="<div class=redB>&nbsp;&nbsp;报关单上传失败！ </div><br>";
		                        $OperationResult="N";			
	                       	}
	                  }
	                 else{
	                        $InSql ="INSERT INTO  $DataIn.ch1_shipfile(Id,ShipId,ExpressNum,ForwardNum,
	                        BillNum,BgBillNum)VALUES(NULL,'$Mid','','','','$BgBillNum')"; 
	                        $InResult=@mysql_query($InSql);
	                        if($InResult && mysql_affected_rows()>0){
	                     	$Log.="&nbsp;&nbsp;报关单上传成功！ <br>";
                            }
	                        else{
	                        	$Log.="<div class=redB>&nbsp;&nbsp;报关单上传失败！ </div><br>";
		                        $OperationResult="N";			
	                       	}
	                   }
				}
			else{
				$Log.="<div class=redB>&nbsp;&nbsp;发票单上传失败！ </div><br>";
				$OperationResult="N";			
				}
			}

      break;      

	case 934:	//更新页面 取消出货项目
	$Log_Funtion="出货项目退回";
		$checkPacking=mysql_query("SELECT Id FROM $DataIn.ch2_packinglist WHERE POrderId='$POrderId' AND Mid=$Id",$link_id);
		if($PackingRow=mysql_fetch_array($checkPacking)){
			$Log="<div class='redB'>出货项目 $POrderId 已装箱不能删除.</div><br>";
			}
		else{
			//订单的状态更新-2		随货项目的状态更新-1	扣款资料的状态更新-1
			$UpSql="UPDATE $DataIn.ch1_shipsheet C 
			LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=C.POrderId
             LEFT JOIN $DataIn.ch1_shipsplit  SP ON SP.ShipId=C.Id
			LEFT JOIN $DataIn.ch5_sampsheet S ON S.SampId=C.POrderId 
			LEFT JOIN $DataIn.ch6_creditnote N ON N.Number=C.POrderId 
			SET  S.Estate=1,N.Estate=1,Y.Estate=2 ,SP.Estate=1,SP.ShipId=0  WHERE C.Id='$sId'";
			$UpResult = mysql_query($UpSql);
			if($UpResult && mysql_affected_rows()>0){
				$Log="出货项目待出状态恢复成功.<br>";
				$DelSql="DELETE FROM $DataIn.ch1_shipsheet WHERE POrderId='$POrderId' AND Id=$sId";
				$delRresult = mysql_query($DelSql);
				if ($delRresult && mysql_affected_rows()>0){
					$Log.="出货项目取消成功.<br>";
					//重置Invoice
					$CheckSign=mysql_fetch_array(mysql_query("SELECT Sign,ShipType FROM $DataIn.ch1_shipmain WHERE Id='$Id'",$link_id));
					$ShipSign=$CheckSign["Sign"];					
					$ShipType=$CheckSign["ShipType"];		
					if(($ShipType=='credit') || ($ShipType=='debit') ){  //说明是Credit note 或者 是debit note								
						//include "ch_creditnote_topdf.php";
						include "ch_creditnoteBlue_topdf.php";
						}
					else{
						//include "ch_shippinglist_toinvoice.php";
						include "ch_shippinglistBlue_toinvoice.php";
						}
					}
				}
			else{
				$Log="<div class='redB'>出货项目状态更新失败,取消操作不成功. $UpSql </div>";
				$OperationResult="N";
				}
			}
		break;
	case 935://价格更新
		$Log_Funtion="更新出货项目价格";
		$UpSql="UPDATE $DataIn.ch1_shipsheet C 
		LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=C.POrderId 
		LEFT JOIN $DataIn.ch5_sampsheet S ON S.SampId=C.POrderId 
		LEFT JOIN $DataIn.ch6_creditnote N ON N.Number=C.POrderId 
		SET C.Price='$NewPrice',Y.Price='$NewPrice',S.Price='$NewPrice',N.Price='$NewPrice' WHERE C.Id='$sId'";
		$UpResult = mysql_query($UpSql);$UpRows=mysql_affected_rows();
		if($UpResult && $UpRows>0){
			$Log.="出货项目 $sId 的单价更新成功.<br>";
			$CheckSign=mysql_fetch_array(mysql_query("SELECT Sign,ShipType FROM $DataIn.ch1_shipmain WHERE Id='$Id'",$link_id));
			$ShipSign=$CheckSign["Sign"];
			$ShipType=$CheckSign["ShipType"];		
			//if($ShipSign==-1){
			if(($ShipType=='credit') || ($ShipType=='debit') ){  //说明是Credit note 或者 是debit note					
				//include "ch_creditnote_topdf.php";
				include "ch_creditnoteBlue_topdf.php";
				}
			else{
				//include "ch_shippinglist_toinvoice.php";
				include "ch_shippinglistBlue_toinvoice.php";
				}
			}
		else{
			$Log.="<div class='redB'>出货项目 $sId 的单价更新失败. $UpSql </div><br>";
			$OperationResult="N";
			}
			break;
	case 936://删除附件
		$Log_Funtion="删除附件图片";
		$CheckImgSql=mysql_query("SELECT Mid,Picture FROM $DataIn.ch7_shippicture WHERE Id='$ImgId' LIMIT 1",$link_id);
		if($CheckImgRow=mysql_fetch_array($CheckImgSql)){
			$DelSql="DELETE FROM $DataIn.ch7_shippicture WHERE Id='$ImgId'";
			$DelResult=mysql_query($DelSql);
			if($DelResult){
				$Id=$CheckImgRow["Mid"];
				$Picture=$CheckImgRow["Picture"];
				$FilePath="../download/Invoice/$Picture";
				if(file_exists($FilePath)){
					unlink($FilePath);
					}
				}
				//include "ch_shippinglist_toinvoice.php";
				include "ch_shippinglistBlue_toinvoice.php";
			}
		break;
    case 124:
               include "deliverybill/ch_shippinglist_todelivery.php";
         break;
	default://出货单还是扣款单更新                      不更新银行资料
		$OrderArray=explode("|",$OrderIds);
		$OrderNums=count($OrderArray);
		$Ids1="";
		$Ids2="";
		for($i=0;$i<$OrderNums;$i++){
			$Records=$OrderArray[$i];		
			$TEMP=explode("^^",$Records);
			$Type=$TEMP[0];
			$theId=$TEMP[1];
			if($Type==1){
				$Ids1=$Ids1==""?$theId:$Ids1.",".$theId;
				}
			else{
				$Ids2=$Ids2==""?$theId:$Ids2.",".$theId;
				}
			}
		//判断是出货单还是扣款单
		if(($ShipType=='credit') || ($ShipType=='debit') ){  //说明是Credit note 或者 是debit note
			$Wise=FormatSTR($Wise);
			//更新主单信息
			$UpSql="UPDATE $DataIn.ch1_shipmain SET ModelId='$ModelId',InvoiceNO='$InvoiceNO',Wise='$Wise',Notes='$Notes',
			Terms='$Terms',PaymentTerm='$PaymentTerm',PreSymbol='$PreSymbol',Date='$Date',Locks='0',Operator='$Operator' WHERE Id='$Id'";
			$UpResult = mysql_query($UpSql);
			if($UpResult){
				$Log.="主出货单信息更新成功.<br>";
				}
			else{
				$Log.="<div class='redB'>主出货单信息更新失败.</div><br>";
				$OperationResult="N";
				}
			//加入扣款资料至出货单
			if($Ids2!=""){
				   $sheetInSql="INSERT INTO $DataIn.ch1_shipsheet SELECT NULL,'$Id',Number,'0',Qty,Price,'1','3','1','1','1','0','$Operator',NOW(),'$Operator',NOW(),CURDATE(),'$Operator' FROM $DataIn.ch6_creditnote WHERE Id IN ($Ids2)";
				
				$sheetInAction=@mysql_query($sheetInSql);
				if($sheetInAction && mysql_affected_rows()>0){
					$Log.="扣款项目($Ids2)加入扣款单明细表成功.<br>";
					//更新扣款资料的状态
					//更新状态
					$pUpSql=mysql_query("UPDATE $DataIn.ch6_creditnote SET Estate='0' WHERE Id IN ($Ids2)");
					if($pUpSql && mysql_affected_rows()>0){
						$Log.="扣款项目($Ids2)的已出状态更新成功.<br>";
						}
					else{
						$Log.="<div class='redB'>扣款项目($Ids2)的已出状态更新失败. $pUpSql </div><br>";
						$OperationResult="N";
						}
					}
				else{
					$Log.="<div class='redB'>扣款项目($Ids2)加入扣款单明细表失败. $sheetInSql </div><br>";
					$OperationResult="N";
					}
				}
			include "ch_creditnoteBlue_topdf.php";
			}
		else{
			$Wise=FormatSTR($Wise);
			//更新主单信息
			$UpSql="UPDATE $DataIn.ch1_shipmain SET ModelId='$ModelId',InvoiceNO='$InvoiceNO',Wise='$Wise',Notes='$Notes',Terms='$Terms',PaymentTerm='$PaymentTerm',PreSymbol='$PreSymbol',Date='$Date',Locks='0',Operator='$Operator' WHERE Id='$Id'";
			$UpResult = mysql_query($UpSql);
			if($UpResult){
				$Log.="主出货单信息更新成功.<br>";
				}
			else{
				$Log.="<div class='redB'>主出货单信息更新失败.</div><br>";
				$OperationResult="N";
				}
			


              if($From=="wait"){
				 $EstateSTR1 ="Y.Estate='4'";
                 $EstateSTR2="S.Estate='2'";
                }
			    else{
				   $EstateSTR1 ="Y.Estate='0'";
                  $EstateSTR2="S.Estate='0'";
                 }		

    		  for($i=0;$i<$OrderNums;$i++){
					$Records=$OrderArray[$i];		
					$TEMP=explode("^^",$Records);
					$Type=$TEMP[0];
					$thisId=$TEMP[1];
					$thisQty=$TEMP[2];
				     switch($Type){
                             case 1://订单
                                $sheetInSql="INSERT INTO $DataIn.ch1_shipsheet  SELECT NULL,'$Id','$thisId',Y.POrderId,Y.ProductId,
                                '$thisQty',Y.Price,'1','1','1','1','1','0','$Operator',NOW(),'$Operator',NOW(),CURDATE(),'$Operator' 
                                FROM $DataIn.ch1_shipsplit SP
                                LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=SP.POrderId WHERE SP.Id=$thisId";
                                $sheetInAction=@mysql_query($sheetInSql);
                                $ShipId=mysql_insert_id();
                                 if($sheetInAction && mysql_affected_rows()>0){
                                       $Log.="出货的订单加入出货明细表成功.<br>";
                                          //更新状态
                                       $pUpSql="UPDATE $DataIn.ch1_shipsplit  SET Estate='0',ShipId=$ShipId WHERE Id=$thisId";
                                       $pUpResult=@mysql_query($pUpSql);
                                   }
                                else{
                                      $Log.="<div class='redB'>出货的订单加入出货明细表失败.</div><br>";
                                      $OperationResult="N";
                                    }
                               $updateSql="UPDATE $DataIn.yw1_ordersheet Y
                               LEFT JOIN (  SELECT IFNULL(SUM(C.Qty),0) AS shipQty,C.POrderId 
                               FROM  $DataIn.ch1_shipsheet C 
                               WHERE C.POrderId  IN ( 
                                   SELECT  POrderId FROM $DataIn.ch1_shipsplit WHERE Id IN ($Ids1)) GROUP BY C.POrderId
                                   ) A ON A.POrderId=Y.POrderId
                               SET $EstateSTR1  WHERE Y.Qty=A.shipQty";
		                      $upAction=@mysql_query($updateSql,$link_id);
		                      if($upAction && mysql_affected_rows()>0){
		                            $Log.="更新订单已出货状态成功.<br>";
		                          }
		                	  else{
		                           $Log.="<div class='redB'>更新订单已出货状态失败.</div><br>";
		                        }
                                 break;
                             case 2://样品
                                  $IN_SampSql="INSERT INTO $DataIn.ch1_shipsheet  SELECT NULL,'$Id','0',SampId,'0',Qty,Price,'1','2','1','1','1','0','$Operator',NOW(),'$Operator',NOW(),CURDATE(),'$Operator' FROM $DataIn.ch5_sampsheet WHERE Id=$thisId";
                                   $IN_SampResult=@mysql_query($IN_SampSql);
                                   if($IN_SampResult && mysql_affected_rows()>0){
                                  	   	 $Log.="随货项目($thisId)加入出货明细表成功<br>";
	                                 	 $sUpSql="UPDATE $DataIn.ch5_sampsheet  S SET $EstateSTR2 WHERE S.Id IN ($thisId)";
 	                                	 $sUpResult=mysql_query($sUpSql);
	                                     if($sUpResult && mysql_affected_rows()>0){
                                  	 	   	$Log.="随货项目($thisId)的状态更新成功.<br>";
                                  		 }
  	                               	 	 else{
    	                                	$Log.="<div class='redB'>随货项目($thisId)的将出状态更新失败.</div><br>";
     	                              	   	$OperationResult="N";
    	                                 }
                                     }
                             	    else{
                                  	 	  $Log.="<div class='redB'>随货项目($thisId)加入出货明细表失败.$IN_SampSql</div><br>";
                                   	 	 $OperationResult="N";
                                  	}
                            break;
                      }
               }
			include "ch_shippinglistBlue_toinvoice.php";
	      }
		break;
	}
$ALType="From=$From&Pagination=$Pagination&Page=$Page&CompanyId=$CompanyId&chooseDate=$chooseDate";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
