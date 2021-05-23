<?php 
//电信-zxq 2012-08-01
/*
$DataIn.ck9_stocksheet
$DataIn.cg1_stocksheet
$DataPublic.staffmain
$DataIn.trade_object
$DataIn.yw1_ordersheet
$DataIn.productdata
$DataIn.stuffdata
*/
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=25;
$tableMenuS=500;
ChangeWtitle("$SubCompany 锁定待购列表");
$funFrom="cg_cgdsheet";
$From=$From==""?"Lock":$From;
//$sumCols="8,9,10,11,12,13";			//求和列,需处理
//$sumCols="12,13,14,15,16,17";			//求和列,需处理
$sumCols="13,14,15,16,17,18";			//求和列,需处理
$Th_Col="选项|60|序号|30|PO|80|采购流水号|120|配件ID|45|配件名称|200|图档|30|QC图|40|认证|40|默认供应商|100|送货</br>楼层|40|历史<br>资料|40|单价|50|单位|45|订单<br>数量|40|使用<br>库存|40|需购<br>数量|40|增购<br>数量|40|实购<br>数量|40|金额|55|审核|35|图片职责|85|图档职责|85|采购<br>备注|30|增购备注|160|可用<br>库存|40|最低库存|60";
//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 200;							//每页默认记录数量
//$SearchRows.=" AND T.mainType<2";//需采购的配件需求单
$nowWebPage=$funFrom."_Lock";
include "../model/subprogram/read_model_3.php";
$Keys=31;
$SearchRows.=" AND (T.mainType<2)";//需采购的配件需求单
//步骤4：需处理-可选条件下拉框
$ActioToS="1,134";
//echo "PJobId:$PJobId";

if($From!="slist"){	//非查询：过滤采购、结付方式、供应商、月份
	//检查进入者是否有采购记录:是则默认显示该员工的记录，否则显示读入的第一个员工记录
	/*
	$checkSql = mysql_query("SELECT S.Id 
	FROM $DataIn.cg1_stocksheet S 
	LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
	LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
	WHERE S.Mid=0 $SearchRows AND (S.FactualQty>0 OR S.AddQty>0) AND S.BuyerId='$Login_P_Number' GROUP BY S.BuyerId ORDER BY S.BuyerId",$link_id);
	*/
	/*
	if($isMe==1){
		$checkSql = mysql_query("SELECT S.Id 
		FROM $DataIn.cg1_stocksheet S 
		LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
		LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
		WHERE S.Mid=0 $SearchRows AND (S.FactualQty>0 OR S.AddQty>0) AND S.BuyerId='$Login_P_Number'  Limit 1",$link_id);	
		
		
		//$Number="";
		if($checkRow=mysql_fetch_array($checkSql)){
			$Number=$Number==""?$Login_P_Number:$Number;//首次打开页面时，如果员工有采购记录，则为默认采购
			//echo "HERER";
			}
	}
	*/
	
	//采购
	/*
	$buyerSql = mysql_query("SELECT  S.BuyerId,M.Name 
	FROM $DataIn.cg1_stocksheet S 
	LEFT JOIN $DataPublic.staffmain M ON S.BuyerId=M.Number 
	LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
	LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
	WHERE S.Mid=0 $SearchRows AND (S.FactualQty>0 OR S.AddQty>0)  GROUP BY S.BuyerId ORDER BY S.BuyerId",$link_id);
	*/
	
	$buyerSql = mysql_query("SELECT DISTINCT  S.BuyerId,M.Name  from (SELECT DISTINCT   BuyerId,StuffId   
	FROM $DataIn.cg1_stocksheet WHERE Mid=0  AND (FactualQty>0 OR AddQty>0) ORDER BY BuyerId  )  S 
	LEFT JOIN $DataPublic.staffmain M ON S.BuyerId=M.Number 
	LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
	LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
	WHERE 1  $SearchRows ",$link_id);	
	
	
	if($buyerRow = mysql_fetch_array($buyerSql)){
		echo "<select name='Number' id='Number' onchange='zhtj()'>";
		echo "<option value='' > 全部人员 </option>";
		do{
			$thisBuyerId=$buyerRow["BuyerId"];
			$Buyer=$buyerRow["Name"];
			//$Number=$Number==""?$thisBuyerId:$Number;
			if ($Number==$thisBuyerId){
				echo "<option value='$thisBuyerId' selected>$Buyer</option>";
				$SearchRows.=" AND S.BuyerId='$thisBuyerId'";
				}
			else{
				echo "<option value='$thisBuyerId'>$Buyer</option>";
				}
			}while ($buyerRow = mysql_fetch_array($buyerSql));
		echo"</select>&nbsp;";
		}
		
		
    //供应商
	/*
	$providerSql= mysql_query("SELECT 
	S.CompanyId,P.Forshort,P.Letter 
	FROM $DataIn.cg1_stocksheet S
	LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
	LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
	LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId
	WHERE 1 $SearchRows and S.Mid=0 and (S.FactualQty>0 OR S.AddQty>0) GROUP BY S.CompanyId ORDER BY P.Letter",$link_id);
	*/
	/*
	$providerSql= mysql_query("SELECT 
	S.CompanyId,P.Forshort,P.Letter 
	FROM $DataIn.cg1_stocksheet S
	LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
	LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
	LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId
	WHERE 1 $SearchRows and S.Mid=0 and (S.FactualQty>0 OR S.AddQty>0) GROUP BY S.CompanyId ORDER BY P.Letter",$link_id);
	
	if($providerRow = mysql_fetch_array($providerSql)){
		echo "<select name='CompanyId' id='CompanyId' onchange='document.form1.submit();'>";
		do{
			$Letter=$providerRow["Letter"];
			$Forshort=$providerRow["Forshort"];
			$Forshort=$Letter.'-'.$Forshort;
			$thisCompanyId=$providerRow["CompanyId"];
			$CompanyId=$CompanyId==""?$thisCompanyId:$CompanyId;				
			if($CompanyId==$thisCompanyId){
				echo"<option value='$thisCompanyId' selected>$Forshort</option>";
				$SearchRows.=" and S.CompanyId='$thisCompanyId'";
				}
			else{
				echo"<option value='$thisCompanyId'>$Forshort</option>";
				}
			}while ($providerRow = mysql_fetch_array($providerSql));
		echo"</select>&nbsp;";
		}
	else{
		//无供应商记录
		$SearchRows.=" and S.CompanyId=''";
		}
	*/
	//echo "ForceSign:$ForceSign";
	$TempForceSTR="ForceSignStr".strval($ForceSign); 
	$$TempForceSTR="selected";
	echo "<select name='ForceSign' id='ForceSign' onchange='zhtj()'>";
	echo "<option  value='0' $ForceSignStr0> 未确定 </option>";
	echo  "<option value='1' $ForceSignStr1 >无图片</option>";
	echo  "<option value='2' $ForceSignStr2 >无图档</option>";
	echo  "<option value='4' $ForceSignStr4 >业务锁定</option>";
	//echo  "<option value='3' $ForceSignStr3 >无图片/图档</option>";
	//echo  "<option value='4' $ForceSignStr4 >正常采购</option>";
	echo"</select>&nbsp;";
	$KSearchRows="";
	switch ($ForceSign){		
		case 1:
			$KSearchRows.=" and (A.ForcePicSpe=1 OR (T.ForcePicSign=1 AND A.ForcePicSpe=-1) ) AND  A.Picture!=1";
			break;
		case 2:
			$KSearchRows.=" and (A.ForcePicSpe=2 OR (T.ForcePicSign=2 AND A.ForcePicSpe=-1)) AND  (A.Gstate!=1  OR A.Gfile='') ";
			break;
		case 3:
			$KSearchRows.=" and (A.ForcePicSpe=3 OR (T.ForcePicSign=3 AND A.ForcePicSpe=-1)) AND  ((A.Gstate!=1  OR A.Gfile='') OR  A.Picture!=1)";
			//$SearchRows.=" and (H.Type='2' or I.Locks=0 or S.Estate!=0)";
			$KSearchRows.="";
			break;
		case 4:
			$KSearchRows.=" and (H.Type='2') ";
			/*
			$SearchRows.="and    NOT ( (H.Type='2' AND H.Type is NOT NULL ) or (I.Locks=0 AND I.Locks is NOT NULL)) AND NOT ((A.ForcePicSpe=1 OR (T.ForcePicSign=1 AND A.ForcePicSpe=-1)) AND  A.Picture!=1)  AND NOT ((A.ForcePicSpe=2 OR (T.ForcePicSign=2 AND A.ForcePicSpe=-1)) AND  (A.Gstate!=1  OR A.Gfile=''))   AND  S.Estate=0  ";  
			*/
			//$SearchRows.="and  NOT ( ( (H.Type='2' AND H.Type is NOT NULL)  or (I.Locks=0 AND I.Locks is NOT NULL)) OR ((A.ForcePicSpe=1 OR (T.ForcePicSign=1 AND A.ForcePicSpe=-1)) AND  A.Picture!=1)  OR ((A.ForcePicSpe=2 OR (T.ForcePicSign=2 AND A.ForcePicSpe=-1)) AND  (A.Gstate!=1  OR A.Gfile=''))  )   ";  
			
			break;
		default:
			$KSearchRows.="and ( 
							    (H.Type='2' or I.Locks=0) 
								OR ((A.ForcePicSpe=1 OR (T.ForcePicSign=1 AND A.ForcePicSpe=-1)) AND  A.Picture!=1)  
								OR ((A.ForcePicSpe=2 OR (T.ForcePicSign=2 AND A.ForcePicSpe=-1)) AND  (A.Gstate!=1  OR A.Gfile='')) 
								OR (A.ForcePicSpe=3 OR (T.ForcePicSign=3 AND A.ForcePicSpe=-1)) AND  ((A.Gstate!=1  OR A.Gfile='') OR  A.Picture!=1)
								OR S.Estate!=0 
						  )  ";  
			break;
		
	}
	
	


	
	switch ($ForceSign){		
		case 1:
		case 3:
			echo"<select name='PJobType' id='PJobType' onchange='zhtj()'>";
			echo"<option value='' >--图片职责--</option>";
			/*
			if ($PJobId!="") {
				$mySql="SELECT Id,Name FROM $DataPublic.jobdata  
					 WHERE Estate=1 AND Id in($PJobId) order by Id,Name";
					 
				
			}
			else {
				$mySql="SELECT Id,Name FROM $DataPublic.jobdata  
					 WHERE Estate=1 AND Id in(3,4,6,7,32) order by Id,Name";
			}
			*/
			/*
			$mySql="SELECT Id,Name FROM $DataPublic.jobdata  
					 WHERE Estate=1 AND Id in(6,7,32) order by Id,Name";
			*/		 
			$mySql="SELECT Id,GroupName as Name FROM $DataIn.staffgroup  
					 WHERE Estate=1 AND Id in(5,27,34,35) order by Id,Name";		 
			
			$result = mysql_query($mySql,$link_id);
			if($myrow = mysql_fetch_array($result)){
				do{
					$jobId=$myrow["Id"];
					$jobName=$myrow["Name"];
					if ($PJobId!="" && $PJobType=="") {
						$PJobType=$PJobId;
					}
					if ($jobId==$PJobType){
						echo "<option value='$jobId' selected >$jobName</option>";
						$SearchRows.=" AND (A.PJobId='$jobId' || (A.PJobId=-1 AND T.Picjobid='$jobId' ))  ";
						}
					else{
						echo "<option value='$jobId'>  $jobName</option>";
						}
					}while ($myrow = mysql_fetch_array($result));
				}
			echo "</select>&nbsp;";	

			break;
		case 2:
			echo"<select name='JobType' id='JobType' onchange='zhtj()'>";
			echo"<option value='' >--图档职责--</option>";
			/*
			$mySql="SELECT Id,Name FROM $DataPublic.jobdata  
					 WHERE Estate=1 AND Id in(3,4,6,7,32) order by Id,Name";
			*/		 
			$mySql="SELECT Id,GroupName as Name FROM $DataIn.staffgroup  
					 WHERE Estate=1 AND Id in(5,27,34) order by Id,Name";
			$result = mysql_query($mySql,$link_id);					 
			$result = mysql_query($mySql,$link_id);
			if($myrow = mysql_fetch_array($result)){
				do{
					$jobId=$myrow["Id"];
					$jobName=$myrow["Name"];
					if ($PJobId!="" && $JobType=="") {
						$JobType=$PJobId;
					}		
					if ($jobId==$JobType){
						echo "<option value='$jobId' selected>$jobName</option>";
						$SearchRows.=" AND (A.JobId='$jobId' || (A.JobId=-1 AND T.Gicjobid='$jobId' ) ) ";
						}
					else{
						echo "<option value='$jobId'>$jobName</option>";
						}
					}while ($myrow = mysql_fetch_array($result));
				}
			echo "</select>&nbsp;";		

			break;
		//case 3:
		//	break;
		case 4:

			
			break;
		default:
			
			break;
		
	}
	
	
	switch ($ForceSign){
		case 1:
		    if($PicOrder==""){$PicOrder=3;}
			$TempPicOrderStr="PicOrderStr".strval($PicOrder); 
			$$TempPicOrderStr="selected";
			echo "<select name='PicOrder' id='PicOrder' onchange='zhtj()'>";
			echo "<option  value='1' $PicOrderStr1> 已送货</option>";
			echo  "<option value='2' $PicOrderStr2 >已下单</option>";
			echo  "<option value='3' $PicOrderStr3 >未下单</option>";
			//echo  "<option value='4' $ForceSignStr4 >正常采购</option>";
			echo"</select>&nbsp;";		

			break;
		default:
			
			break;		
	}

	
	
	
	
}
	
	
	
else{
	//$ActioToS="3,4,22,26,13,51,21";
	//$ActioToS="0";
	$SearchRows.="and ( (H.Type='2' or I.Locks=0) OR ((A.ForcePicSpe=1 OR (T.ForcePicSign=1 AND A.ForcePicSpe=-1)) AND  A.Picture!=1)  OR ((A.ForcePicSpe=2 OR (T.ForcePicSign=2 AND A.ForcePicSpe=-1)) AND  (A.Gstate!=1  OR A.Gfile='')) OR S.Estate!=0 )  ";  
	
	}
//检查进入者是否采购
$checkResult = mysql_query("SELECT JobId FROM $DataPublic.staffmain WHERE Number=$Login_P_Number order by Id LIMIT 1",$link_id);
if($checkRow = mysql_fetch_array($checkResult)){
	$JobId=$checkRow["JobId"];//3为采购
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
$helpFile=1;
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
/*
switch ($PicOrder){
	case 1:
		$mySql="SELECT 
		C.Forshort AS Client,S.Id,S.Mid,S.StockId,S.POrderId,S.StuffId,S.Price,S.OrderQty,S.StockQty,S.AddQty,S.FactualQty,S.CompanyId,S.BuyerId,S.DeliveryDate,S.StockRemark,S.AddRemark,S.Estate,S.Locks,Y.OrderPO,Y.Qty as PQty,Y.PackRemark,Y.sgRemark,Y.ShipType,PI.Leadtime,E.Forshort as PForshort,
		A.StuffCname,P.cName,A.Gfile,A.Gstate,A.Gremark,A.Picture,P.cName,A.SendFloor,A.TypeId,U.Name AS UnitName,A.ForcePicSpe,IF(A.Pjobid=-1,T.PicNumber ,A.PicNumber) as PicNumber,IF(A.Pjobid=-1,M.Name,K.Name) as PJobname,IF(A.Jobid=-1,N.Name,F.Name) as GJobname,T.ForcePicSign,H.Type AS LockType,I.Locks as LockLocks
		FROM $DataIn.cg1_stocksheet S
		LEFT JOIN $DataIn.gys_shsheet J ON J.StockId=S.StockId  
		LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=S.POrderId
		LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=Y.Id
		LEFT JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId
		LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
		LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId
		LEFT JOIN $DataIn.bps B ON B.StuffId=A.StuffId 
		LEFT JOIN $DataIn.trade_object E ON E.CompanyId=B.CompanyId 
		LEFT JOIN $DataPublic.jobdata K ON K.Id=A.Pjobid 
		LEFT JOIN $DataPublic.jobdata F ON F.Id=A.Jobid 
		LEFT JOIN $DataPublic.stuffunit U ON U.Id=A.Unit
		LEFT JOIN $DataIn.stufftype T ON T.TypeId=A.TypeId 
		LEFT JOIN $DataPublic.jobdata M ON M.Id=T.Picjobid 
		LEFT JOIN $DataPublic.jobdata N ON N.Id=T.GicJobid 
		LEFT JOIN $DataIn.yw2_orderexpress H ON H.POrderId =S.POrderId
		LEFT JOIN $DataIn.cg1_lockstock I ON I.StockId =S.StockId
		WHERE 1 $SearchRows and  S.rkSign>0 And J.Id>0 AND A.Picture!=1  and (S.FactualQty>0 OR S.AddQty>0) ORDER BY S.Estate DESC,S.StockId DESC";	
	
		break;
	
	case 2:
		$mySql="SELECT 
		C.Forshort AS Client,S.Id,S.Mid,S.StockId,S.POrderId,S.StuffId,S.Price,S.OrderQty,S.StockQty,S.AddQty,S.FactualQty,S.CompanyId,S.BuyerId,S.DeliveryDate,S.StockRemark,S.AddRemark,S.Estate,S.Locks,Y.OrderPO,Y.Qty as PQty,Y.PackRemark,Y.sgRemark,Y.ShipType,PI.Leadtime,E.Forshort as PForshort,
		A.StuffCname,P.cName,A.Gfile,A.Gstate,A.Gremark,A.Picture,P.cName,A.SendFloor,A.TypeId,U.Name AS UnitName,A.ForcePicSpe,IF(A.Pjobid=-1,T.PicNumber ,A.PicNumber) as PicNumber,IF(A.Pjobid=-1,M.Name,K.Name) as PJobname,IF(A.Jobid=-1,N.Name,F.Name) as GJobname,T.ForcePicSign,H.Type AS LockType,I.Locks as LockLocks
		FROM $DataIn.cg1_stocksheet S  
		LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=S.POrderId
		LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=Y.Id
		LEFT JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId
		LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
		LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId
		LEFT JOIN $DataIn.bps B ON B.StuffId=A.StuffId 
		LEFT JOIN $DataIn.trade_object E ON E.CompanyId=B.CompanyId 
		LEFT JOIN $DataPublic.jobdata K ON K.Id=A.Pjobid 
		LEFT JOIN $DataPublic.jobdata F ON F.Id=A.Jobid 
		LEFT JOIN $DataPublic.stuffunit U ON U.Id=A.Unit
		LEFT JOIN $DataIn.stufftype T ON T.TypeId=A.TypeId 
		LEFT JOIN $DataPublic.jobdata M ON M.Id=T.Picjobid 
		LEFT JOIN $DataPublic.jobdata N ON N.Id=T.GicJobid 
		LEFT JOIN $DataIn.yw2_orderexpress H ON H.POrderId =S.POrderId
		LEFT JOIN $DataIn.cg1_lockstock I ON I.StockId =S.StockId
		WHERE 1 $SearchRows  AND S.Mid>0 AND S.rkSign>0  AND A.Picture!=1  AND  A.Estate=1 and (S.FactualQty>0 OR S.AddQty>0)  ORDER BY S.Estate DESC,S.StockId DESC ";		
		break;
	
	default:
		$mySql="SELECT 
		C.Forshort AS Client,S.Id,S.Mid,S.StockId,S.POrderId,S.StuffId,S.Price,S.OrderQty,S.StockQty,S.AddQty,S.FactualQty,S.CompanyId,S.BuyerId,S.DeliveryDate,S.StockRemark,S.AddRemark,S.Estate,S.Locks,Y.OrderPO,Y.Qty as PQty,Y.PackRemark,Y.sgRemark,Y.ShipType,PI.Leadtime,E.Forshort as PForshort,
		A.StuffCname,P.cName,A.Gfile,A.Gstate,A.Gremark,A.Picture,P.cName,A.SendFloor,A.TypeId,U.Name AS UnitName,A.ForcePicSpe,IF(A.Pjobid=-1,T.PicNumber ,A.PicNumber) as PicNumber,IF(A.Pjobid=-1,M.Name,K.Name) as PJobname,IF(A.Jobid=-1,N.Name,F.Name) as GJobname,T.ForcePicSign,H.Type AS LockType,I.Locks as LockLocks
		FROM $DataIn.cg1_stocksheet S
		LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=S.POrderId
		LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=Y.Id
		LEFT JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId
		LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
		LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId
		LEFT JOIN $DataIn.bps B ON B.StuffId=A.StuffId 
		LEFT JOIN $DataIn.trade_object E ON E.CompanyId=B.CompanyId 
		LEFT JOIN $DataPublic.jobdata K ON K.Id=A.Pjobid 
		LEFT JOIN $DataPublic.jobdata F ON F.Id=A.Jobid 
		LEFT JOIN $DataPublic.stuffunit U ON U.Id=A.Unit
		
		LEFT JOIN $DataIn.stufftype T ON T.TypeId=A.TypeId 
		LEFT JOIN $DataPublic.jobdata M ON M.Id=T.Picjobid 
		LEFT JOIN $DataPublic.jobdata N ON N.Id=T.GicJobid 
		
		
		LEFT JOIN $DataIn.yw2_orderexpress H ON H.POrderId =S.POrderId
		LEFT JOIN $DataIn.cg1_lockstock I ON I.StockId =S.StockId
		WHERE 1 $SearchRows $KSearchRows and S.Mid=0 and (S.FactualQty>0 OR S.AddQty>0) ORDER BY S.Estate DESC,S.StockId DESC";
		break;
}
*/
//echo $PicOrder;
switch ($PicOrder){
	case 1:
		$mySql="SELECT 
		C.Forshort AS Client,S.Id,S.Mid,S.StockId,S.POrderId,S.StuffId,S.Price,S.OrderQty,S.StockQty,S.AddQty,S.FactualQty,S.CompanyId,S.BuyerId,S.DeliveryDate,S.StockRemark,S.AddRemark,S.Estate,S.Locks,Y.OrderPO,Y.Qty as PQty,Y.PackRemark,Y.sgRemark,Y.ShipType,PI.Leadtime,E.Forshort as PForshort,
		A.StuffCname,P.cName,A.Gfile,A.Gstate,A.Gremark,A.Picture,P.cName,A.SendFloor,A.TypeId,U.Name AS UnitName,
		A.ForcePicSpe,IF(A.Pjobid=-1,T.PicNumber ,A.PicNumber) as PicNumber,IF(A.Pjobid=-1,M.GroupName,K.GroupName) as PJobname,
		IF(A.Jobid=-1,T.GicNumber ,A.GicNumber) as GicNumber,IF(A.Jobid=-1,N.GroupName,F.GroupName) as GJobname,
		T.ForcePicSign,H.Type AS LockType,I.Locks as LockLocks
		FROM $DataIn.cg1_stocksheet S
		LEFT JOIN $DataIn.gys_shsheet J ON J.StockId=S.StockId  
		LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=S.POrderId
		LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=Y.Id
		LEFT JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId
		LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
		LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId
		LEFT JOIN $DataIn.bps B ON B.StuffId=A.StuffId 
		LEFT JOIN $DataIn.trade_object E ON E.CompanyId=B.CompanyId 
		LEFT JOIN $DataIn.staffgroup K ON K.Id=A.Pjobid 
		LEFT JOIN $DataIn.staffgroup F ON F.Id=A.Jobid 
		LEFT JOIN $DataPublic.stuffunit U ON U.Id=A.Unit
		LEFT JOIN $DataIn.stufftype T ON T.TypeId=A.TypeId 
		LEFT JOIN $DataIn.staffgroup M ON M.Id=T.Picjobid 
		LEFT JOIN $DataIn.staffgroup N ON N.Id=T.GicJobid 
		LEFT JOIN $DataIn.yw2_orderexpress H ON H.POrderId =S.POrderId
		LEFT JOIN $DataIn.cg1_lockstock I ON I.StockId =S.StockId
		WHERE 1 $SearchRows and  S.rkSign>0 And J.Id>0 AND A.Picture!=1  and (S.FactualQty>0 OR S.AddQty>0) ORDER BY S.StuffID,S.Estate DESC,S.StockId DESC";	
	
		break;
	
	case 2:
		$mySql="SELECT 
		C.Forshort AS Client,S.Id,S.Mid,S.StockId,S.POrderId,S.StuffId,S.Price,S.OrderQty,S.StockQty,S.AddQty,S.FactualQty,S.CompanyId,S.BuyerId,S.DeliveryDate,S.StockRemark,S.AddRemark,S.Estate,S.Locks,Y.OrderPO,Y.Qty as PQty,Y.PackRemark,Y.sgRemark,Y.ShipType,PI.Leadtime,E.Forshort as PForshort,
		A.StuffCname,P.cName,A.Gfile,A.Gstate,A.Gremark,A.Picture,P.cName,A.SendFloor,A.TypeId,U.Name AS UnitName,A.ForcePicSpe,
		IF(A.Pjobid=-1,T.PicNumber ,A.PicNumber) as PicNumber,IF(A.Pjobid=-1,M.GroupName,K.GroupName) as PJobname,
		IF(A.Jobid=-1,T.GicNumber ,A.GicNumber) as GicNumber,IF(A.Jobid=-1,N.GroupName,F.GroupName) as GJobname,
		T.ForcePicSign,H.Type AS LockType,I.Locks as LockLocks
		FROM $DataIn.cg1_stocksheet S  
		LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=S.POrderId
		LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=Y.Id
		LEFT JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId
		LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
		LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId
		LEFT JOIN $DataIn.bps B ON B.StuffId=A.StuffId 
		LEFT JOIN $DataIn.trade_object E ON E.CompanyId=B.CompanyId 
		LEFT JOIN $DataIn.staffgroup K ON K.Id=A.Pjobid 
		LEFT JOIN $DataIn.staffgroup F ON F.Id=A.Jobid 
		LEFT JOIN $DataPublic.stuffunit U ON U.Id=A.Unit
		LEFT JOIN $DataIn.stufftype T ON T.TypeId=A.TypeId 
		LEFT JOIN $DataIn.staffgroup M ON M.Id=T.Picjobid 
		LEFT JOIN $DataIn.staffgroup N ON N.Id=T.GicJobid 
		LEFT JOIN $DataIn.yw2_orderexpress H ON H.POrderId =S.POrderId
		LEFT JOIN $DataIn.cg1_lockstock I ON I.StockId =S.StockId
		WHERE 1 $SearchRows  AND S.Mid>0 AND S.rkSign>0  AND A.Picture!=1  AND  A.Estate=1 and (S.FactualQty>0 OR S.AddQty>0)  ORDER BY S.StuffID,S.Estate DESC,S.StockId DESC ";		
		break;
	
	default:
		$mySql="SELECT 
		C.Forshort AS Client,S.Id,S.Mid,S.StockId,S.POrderId,S.StuffId,S.Price,S.OrderQty,S.StockQty,S.AddQty,S.FactualQty,S.CompanyId,S.BuyerId,S.DeliveryDate,S.StockRemark,S.AddRemark,S.Estate,S.Locks,Y.OrderPO,Y.Qty as PQty,Y.PackRemark,Y.sgRemark,Y.ShipType,PI.Leadtime,E.Forshort as PForshort,
		A.StuffCname,P.cName,A.Gfile,A.Gstate,A.Gremark,A.Picture,P.cName,A.SendFloor,A.TypeId,U.Name AS UnitName,A.ForcePicSpe,
		IF(A.Pjobid=-1,T.PicNumber ,A.PicNumber) as PicNumber,IF(A.Pjobid=-1,M.GroupName,K.GroupName) as PJobname,
		IF(A.Jobid=-1,T.GicNumber ,A.GicNumber) as GicNumber,IF(A.Jobid=-1,N.GroupName,F.GroupName) as GJobname,
		T.ForcePicSign,H.Type AS LockType,I.Locks as LockLocks
		FROM $DataIn.cg1_stocksheet S
		LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=S.POrderId
		LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=Y.Id
		LEFT JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId
		LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
		LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId
		LEFT JOIN $DataIn.bps B ON B.StuffId=A.StuffId 
		LEFT JOIN $DataIn.trade_object E ON E.CompanyId=B.CompanyId 
		LEFT JOIN $DataIn.staffgroup K ON K.Id=A.Pjobid 
		LEFT JOIN $DataIn.staffgroup F ON F.Id=A.Jobid 
		LEFT JOIN $DataPublic.stuffunit U ON U.Id=A.Unit
		
		LEFT JOIN $DataIn.stufftype T ON T.TypeId=A.TypeId 
		LEFT JOIN $DataIn.staffgroup M ON M.Id=T.Picjobid 
		LEFT JOIN $DataIn.staffgroup N ON N.Id=T.GicJobid 
		
		
		LEFT JOIN $DataIn.yw2_orderexpress H ON H.POrderId =S.POrderId
		LEFT JOIN $DataIn.cg1_lockstock I ON I.StockId =S.StockId
		WHERE 1 $SearchRows $KSearchRows and S.Mid=0 and (S.FactualQty>0 OR S.AddQty>0) ORDER BY S.StuffID,S.Estate DESC,S.StockId DESC";
		break;
}


//echo "$PicOrder:  $mySql";
//if ($Login_P_Number=="10868")  echo "$mySql";
//echo "$mySql";
$stmp_StuffId="";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
$tempStuffId="";
$DefaultBgColor=$theDefaultColor;
if($myRow = mysql_fetch_array($myResult)){
	$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);		
	$tempModuleIdTemp=1077;// $subRow["ModuleId"];
	$tempModuleId=anmaIn($tempModuleIdTemp,$SinkOrder,$motherSTR);//加密
	do{
		$m=1;
		$LockRemark="";
		$StuffId=$myRow["StuffId"];
			
		if ($stmp_StuffId==$StuffId){
			continue;
		}
		$stmp_StuffId=$StuffId;
		
		//$POrderId=$myRow["POrderId"];
		//$OrderPO=toSpace($myRow["OrderPO"]);
		$OrderPO=toSpace($myRow["OrderPO"]);
		$POrderId=$myRow["POrderId"];
		//$tdBGCOLOR=$mainRows["POrderId"]==""?"bgcolor='#FFCC99'":"";
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
		//加急订单标色
		include "../model/subprogram/cg_cgd_jj.php";

		$cName=$myRow["cName"];
		$Client=$myRow["Client"];
		$LockStockId=$StockId;
		$StockId="<div title='$Client : $cName'> $StockId </div>";
		$StuffCname=$myRow["StuffCname"];
		$TypeId=$myRow["TypeId"];
		//配件QC检验标准图
        $QCImage="";
        include "../model/subprogram/stuffimg_qcfile.php";
        $QCImage=$QCImage==""?"&nbsp;":$QCImage;
		$Gremark=$myRow["Gremark"];
		$Gfile=$myRow["Gfile"];
		$tempGfile=$Gfile;  ////2012-10-29
		$Gstate=$myRow["Gstate"];
		//REACH 法规图
		include "../model/subprogram/stuffreach_file.php";
		//=====
		include "../model/subprogram/stuffimg_Gfile.php";	//图档显示	
		//检查是否有图片
		$Picture=$myRow["Picture"];
		include "../model/subprogram/stuffimg_model.php";
		
		$ForcePicSpe=$myRow["ForcePicSpe"];
		$ForcePicSign=$myRow["ForcePicSign"];
		if ($ForcePicSpe>=0){  //-1表示用stufftype用的，否则用它指定
			$ForcePicSign=$ForcePicSpe;  
		}

		$LockRemark="未确定产品";
		$PicLink="";	
		$GicLink="";
		if ($ForceSign==0) {
			switch($ForcePicSign){
				case 0: 
					$ForcePicSign="无图需求";
				break;
				case 1: 
					//$ForcePicSign="需要图片";
					if($Picture!=1) {  //需要图片，而无图片或重新上传或需要审核
						//$LockRemark="需要图片?重新上传中?正在审核";
						$LockRemark="";
						if($Picture==0) {  //stuffdata_read.php
							$PicLink= " <A onfocus=this.blur();   href='../public/stuffdata_read.php?From=slist&SearchRows=+AND+A.StuffId%3D$StuffId' target='_blank' style='CURSOR: pointer;color:#FF6633'> 
							<img src='../images/upFile.gif' style='background:#F00' alt='请上传图片' width='9' height='9'>
							</A>";
						
						}
					}
				break;
				case 2: 
					$ForcePicSign="需要图档";
					if($Gstate!=1  || $tempGfile=="") {  //需要图档，而无图档或重新上传或需要审核
						//$LockRemark="需要图档?重新上传中?正在审核";
						$LockRemark="";
						if($Gstate==0 || $tempGfile=="") {  //stuffdata_read.php
							$GicLink= " <A onfocus=this.blur();   href='../public/stuffdata_read.php?From=slist&SearchRows=+AND+A.StuffId%3D$StuffId' target='_blank' style='CURSOR: pointer;color:#FF6633'> 
							<img src='../images/upFile.gif' style='background:#F00' alt='请上传图档' width='9' height='9'>
							</A>";
						
						}						
					}				
				break;
				case 3: 
					$ForcePicSign="图片/图档";
					if($Picture!=1 || $Gstate!=1  || $tempGfile=="") {  //需要图片/图档，而无图片/图档或重新上传或需要审核
						//$LockRemark="需要图片和图档?重新上传中?正在审核";
						$LockRemark="";
						if($Picture==0) {  //stuffdata_read.php
							$PicLink= " <A onfocus=this.blur();   href='../public/stuffdata_read.php?From=slist&SearchRows=+AND+A.StuffId%3D$StuffId' target='_blank' style='CURSOR: pointer;color:#FF6633'> 
							<img src='../images/upFile.gif' style='background:#F00' alt='请上传图片' width='9' height='9'>
							</A>";
						
						}
						if($Gstate==0 || $tempGfile=="") {  //stuffdata_read.php
							$GicLink= " <A onfocus=this.blur();   href='../public/stuffdata_read.php?From=slist&SearchRows=+AND+A.StuffId%3D$StuffId' target='_blank' style='CURSOR: pointer;color:#FF6633'> 
							<img src='../images/upFile.gif' style='background:#F00' alt='请上传图档' width='9' height='9'>
							</A>";
						
						}						
						
					}				
				break;
				case 4: 
					$ForcePicSign="强行锁定";
					$LockRemark="强行锁定中，请配件资料管理人解除";
				break;			
			}				
		}
		else {

			switch ($ForceSign){
				case 1:
					//$LockRemark="需要图片?重新上传中,正在审核";
					$LockRemark="";
					if($Picture==0) {  //stuffdata_read.php
							/*
							$PicLink= "<A onfocus=this.blur(); href='../desk/mainFrame.php?Id=$tempModuleId' target='mainFrame' onclick='ClickTotal(this,1,$tempModuleIdTemp)' 
							style='CURSOR: pointer;color:#FF6633'>".$Picture." </A>";
							*/
							$PicLink= " <A onfocus=this.blur();   href='../public/stuffdata_read.php?From=slist&SearchRows=+AND+A.StuffId%3D$StuffId' target='_blank' style='CURSOR: pointer;color:#FF6633'> 
							<img src='../images/upFile.gif' style='background:#F00' alt='请上传图片' width='9' height='9'> </A>";
							
						
					}					
					break;
				case 2:
					//$LockRemark="需要图档?重新上传中?正在审核";
					$LockRemark="";
					if($Gstate==0 || $tempGfile=="") {  //stuffdata_read.php
						$GicLink= " <A onfocus=this.blur();   href='../public/stuffdata_read.php?From=slist&SearchRows=+AND+A.StuffId%3D$StuffId' target='_blank' style='CURSOR: pointer;color:#FF6633'> 
						<img src='../images/upFile.gif' style='background:#F00' alt='请上传图档' width='9' height='9'>
						</A>";
					
					}					
					
					break;
				case 3:
					$LockRemark="未确定产品";
					break;
				default:
				
					break;
				
			}	
		}
		
		$LockType=$myRow["LockType"];
		$LockLocks=$myRow["LockLocks"];		
		if ($LockType!="" || $LockLocks!=""){
			$OrderSignColor="bgcolor='#FF0000'";
		}
		
		
		$Price=$myRow["Price"];
		//默认单价
		$priceRes=mysql_query("SELECT S.Price FROM $DataIn.stuffdata S WHERE S.StuffId='$StuffId'",$link_id);
		if($priceRow=mysql_fetch_array($priceRes)){
			$DefaultPrice=$priceRow["Price"];
		}
		if($DefaultPrice!=$Price){
			$Price="<div class='redB'>$Price</div>";
			$PriceTitle="Title=\"默认单价：$DefaultPrice\"";
		}
		
		$PForshort=$myRow["PForshort"];
		$SendFloor=$myRow["SendFloor"];
		include "../model/subprogram/stuff_GetFloor.php";
		
		$OrderQty=$myRow["OrderQty"];
		$StockQty=$myRow["StockQty"];
		$AddQty=$myRow["AddQty"];
		$FactualQty=$myRow["FactualQty"];
		$Qty=$AddQty+$FactualQty;
		$Amount=sprintf("%.2f",$Qty*$Price);//本记录金额合计
		$Estate=$myRow["Estate"];
        $UnitName=$myRow["UnitName"]==""?"&nbsp;":$myRow["UnitName"];   
		
		//$Jobname=$myRow["Jobname"];		
		//$Jobname=$Jobname==""?"&nbsp;":$Jobname;
		$PicNumber=$myRow["PicNumber"];
		$PicName="";
		if ($PicNumber!=0 ){  //说明指定的人
			$Operator=$PicNumber;
			include "../model/subprogram/staffname.php";	
			$PicName=$Operator;
			$Operator="";
		}
		
		$PJobname=$myRow["PJobname"];		
		//$PJobname=$PJobname==""?"--":$PJobname;
		if ($PJobname!=""){
			$PJobname=$PicName==""?"$PJobname-(未指定人)":$PJobname."-$PicName";
		}
		else {
			$PJobname="&nbsp;";
		}		
		
		$GicNumber=$myRow["GicNumber"];
		$GicName="";
		if ($GicNumber!=0 ){  //说明指定的人
			$Operator=$GicNumber;
			include "../model/subprogram/staffname.php";	
			$GicName=$Operator;
			$Operator="";
		}
		
		$GJobname=$myRow["GJobname"];		
		if ($GJobname!=""){
			$GJobname=$GicName==""?"$GJobname-(未指定人)":$GJobname."-$GicName";
		}
		else {
			$GJobname="&nbsp;";
		}		
		
		
                $StockRemark=$myRow["StockRemark"];
                $StockRemarkTB="<input type='hidden' id='StockRemark$i' name='StockRemark$i' value='$StockRemark'/>";
                if ($StockRemark=="") {
                    $StockRemark="&nbsp;";
                   }
                else{
                   $StockRemark="<div title='$StockRemark'><img src='../images/remark.gif'></div>"; 
                }
                $AddRemark=$myRow["AddRemark"]==""?"&nbsp;":$myRow["AddRemark"];
                /*if ($AddRemark==""){
                    if ($StockRemark=="") $StockRemark="&nbsp;";
                    }
                else{
                    if ($StockRemark=="") {
                        $StockRemark="[增]" . $AddRemark;
                    }else{
                        $StockRemark.="</br>[增]" . $AddRemark;
                    }
                }*/
                //$AddRemark=$myRow["AddRemark"]==""?"&nbsp;":$myRow["AddRemark"];
		
		//$Locks=1;
		$checkKC=mysql_fetch_array(mysql_query("SELECT oStockQty,mStockQty FROM $DataIn.ck9_stocksheet WHERE StuffId='$StuffId' ORDER BY StuffId",$link_id));
		$oStockQty=$checkKC["oStockQty"];
		$mStockQty=$checkKC["mStockQty"]==0?"&nbsp;":$checkKC["mStockQty"];
		/*/可用库存计算
		if($StuffId!=$tempStuffId){
			$checkKC=mysql_fetch_array(mysql_query("SELECT oStockQty FROM $DataIn.ck9_stocksheet WHERE StuffId='$StuffId' ORDER BY StuffId",$link_id));
			$oStockQty=$checkKC["oStockQty"];
			$tempStuffId=$StuffId;
			//历史单价,最大值和最小值
			$checkPrice=mysql_query("SELECT MAX(Price) AS maxPrice,MIN(Price) AS minPrice FROM $DataIn.cg1_stocksheet WHERE Mid>0 and StuffId='$StuffId' ORDER BY StuffId",$link_id);
			$maxPrice=mysql_result($checkPrice,0,"maxPrice");
			$minPrice=mysql_result($checkPrice,0,"minPrice");
			if($maxPrice==""){
				$PriceInfo="&nbsp;";
				}
			else{
				$PriceInfo="<a href='cg_historyprice.php?StuffId=$StuffId' target='_blank' title='最低历史单价: $minPrice 最高历史单价: $maxPrice'>查看</a>";
				}
			}*/
		//清0
		
		$checkNum=mysql_query("SELECT S.Price,D.StuffCname,M.Date FROM $DataIn.cg1_stocksheet S
	                          LEFT JOIN $DataIn.stuffdata D ON S.StuffId=D.StuffId 
	                          LEFT JOIN $DataIn.cg1_stockmain M ON S.Mid=M.Id 
	                          WHERE S.StuffId=$StuffId and S.Mid!=0",$link_id);
		if($checkRow=mysql_fetch_array($checkNum))
		{
		  $OrderQtyInfo="<a href='cg_historyorder.php?StuffId=$StuffId&Id=$Id' target='_blank'>查看</a>"; 
		}
		else{
		 $OrderQtyInfo="&nbsp;";}
		
		$OrderQty=zerotospace($OrderQty);
		$StockQty=zerotospace($StockQty);
		$FactualQty=zerotospace($FactualQty);
		$AddQty=zerotospace($AddQty);
		$oStockQty=zerotospace($oStockQty);
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
		//检查是否未确定产品，是则锁定并标底色
		
		$CheckSignSql=mysql_query("SELECT Id FROM $DataIn.yw2_orderexpress WHERE POrderId ='$POrderId' AND Type='2' LIMIT 1",$link_id);
		if($CheckSignRow=mysql_fetch_array($CheckSignSql)){
			$LockRemark="业务,未确定产品";
			$OrderSignColor="bgcolor='#FF0000'";
			}
		
		
			
		//检查是否锁定 add by zx 20110109
		//$lockcolor='';
		//$OnclickStr="onclick='updateLock(\"$TableId\",$i,$StockId)' style='CURSOR: pointer;'";
		//$lock="<div title='采购未锁定' > <img src='../images/unlock.png' width='15' height='15'> </div>";
		
		$CheckSignSql=mysql_query("SELECT Id FROM $DataIn.cg1_lockstock WHERE StockId ='$LockStockId' AND Locks=0 LIMIT 1",$link_id);
		//echo "SELECT Id FROM $DataIn.cg1_lockstock WHERE StockId ='$StockId' AND Locks=0 LIMIT 1";
		if($CheckSignRow=mysql_fetch_array($CheckSignSql)){
			$LockRemark="采购,未确定产品";
			$OrderSignColor="bgcolor='#FF0000'";
			//$lock="<div style='background-color:#FF0000' title='采购已锁定'> <img src='../images/lock.png' width='15' height='15'></div>";
			//$lockcolor='#FF0000';
			}
		
		if($Estate!=0) {$LockRemark.="--正在审核中..";}					
		$Estate=$Estate==0?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
		
			
		/*加入同一单的配件  // add by zx 2011-08-04 */
		$showPurchaseorder="<img onClick='ShowOrHide(StuffList$i,showtable$i,StuffList$i,\"$POrderId\",$i,\"\");' name='showtable$i' src='../images/showtable.gif' 
		title='显示或隐藏配件采购明细资料.' width='13' height='13' style='CURSOR: pointer'>";
		$XtableWidth=$tableWidth-160;
		$XtableWidth=0;
		//$XsubTableWidth=$subTableWidth-160;
		$StuffListTB="
			<table width='$XtableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' ><br> &nbsp;PO：$OrderPO&nbsp;<span class='redB'>业务单流水号：$POrderId </span>($Client : $cName)&nbsp;<span class='redB'>数量：$PQty </span>&nbsp;订单备注：$PackRemark <span class='redB'>出货方式：$ShipType</span> 生管备注：$sgRemark <span class='redB'>PI交期：$Leadtime</span></td>
			</tr>
			
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' height='30' align='left'><br><div id='showStuffTB$i' width='$XsubTableWidth'>&nbsp;</div><br></td></tr></table>";

		switch ($PicOrder){
			case 1:
			   break;
			
			default:
				if($LockRemark!=""){
					continue;
				}
			break;
		}
		
		$ValueArray=array(
			array(0=>$OrderPO, 		1=>"align='center'"),			  
			array(0=>$StockId, 		1=>"align='center'"),
			array(0=>$StuffId,		1=>"align='center'"),
			array(0=>$StuffCname.$PicLink),
			array(0=>$Gfile.$GicLink,   1=>"align='center'"),
			array(0=>$QCImage, 	    1=>"align='center'"),
			array(0=>$ReachImage, 	1=>"align='center'"),
			array(0=>$PForshort, 	1=>"align='center'"),
			array(0=>$SendFloor, 	1=>"align='center'"),
			array(0=>$OrderQtyInfo, 1=>"align='center'"),
			array(0=>$Price,	 	1=>"align='right' $PriceTitle"),
			array(0=>$UnitName,	 	1=>"align='center'"),
			array(0=>$OrderQty,		1=>"align='right'"),
			array(0=>$StockQty,		1=>"align='right'"),
			array(0=>$FactualQty, 	1=>"align='right'"),
			array(0=>$AddQty, 		1=>"align='right'"),
			array(0=>$Qty, 			1=>"align='right'"),
			array(0=>$Amount, 		1=>"align='right'"),
			array(0=>$Estate, 		1=>"align='center'"),
			array(0=>$PJobname, 		1=>"align='center'"),
			array(0=>$GJobname, 		1=>"align='center'"),			
			array(0=>$StockRemark,  1=>"align='center' style='CURSOR: pointer'",
                              2=>"onmousedown='window.event.cancelBubble=true;' onclick='addRemarks($i,$Id)'"),
            array(0=>$AddRemark),
            array(0=>$oStockQty,	1=>"align='center' $mStockColor"),
			array(0=>$mStockQty, 	1=>"align='center'")
			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		echo $StuffListTB;echo $StockRemarkTB;	
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
  	}
//步骤7：
echo '</div>';
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>
<script language="JavaScript">
function zhtj(){
	/*
	if(document.all("CompanyId")!=null){
		document.forms["form1"].elements["CompanyId"].value="";
		}
		*/
	document.form1.action="cg_cgdsheet_Lock.php";
	document.form1.submit();
	}
        
function addRemarks(index,Ids){
    
     var Stockid="StockRemark" + index;
     var oldStr=document.getElementById(Stockid).value;
     var inputStr=prompt("请输入采购备注",oldStr);
     if(inputStr) {
        inputStr=inputStr.replace(/(^\s*)|(\s*$)/g,"");  //去除前后空格
        var url="cg_cgdsheet_updated.php?Id="+Ids+"&ActionId=701&Remark="+inputStr; 
        var ajax=InitAjax(); 
	    ajax.open("GET",url,true);
	    ajax.onreadystatechange =function(){
		 if(ajax.readyState==4){// && ajax.status ==200
			 if(ajax.responseText=="Y"){//更新成功
                               var tabIndex="ListTable" + index;
                               var TDid=document.getElementById(tabIndex).rows[0].cells[17];
                               document.getElementById(Stockid).value=inputStr;
                               if (inputStr==""){
                                  TDid.innerHTML="&nbsp;"; 
                               }else{
                                  TDid.innerHTML="<div title='"+inputStr+"'><img src='../images/remark.gif'/></div>"; 
                               }
                              
			     }
			 else{
			    alert ("更新采购备注失败！"); 
			  }
			}
		 }
	   ajax.send(null); 
	 }
 }
</script>
