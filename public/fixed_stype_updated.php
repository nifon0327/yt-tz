<?php 
//电信-ZX  2012-08-01
//电信-joseph
//代码、数据共享-EWEN 2012-08-15
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="固定资产分类";		//需处理
$upDataSheet="$DataPublic.oa1_fixedmaintype";	//需处理
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
	case 5:
		$Log_Funtion="可用";	$SetStr="Estate=1,Locks=0";		include "../model/subprogram/updated_model_3d.php";		break;
	case 6:
		$Log_Funtion="禁用";	$SetStr="Estate=0,Locks=0";		include "../model/subprogram/updated_model_3d.php";		break;
	case 7:
		$Log_Funtion="锁定";	$SetStr="Locks=0";				include "../model/subprogram/updated_model_3d.php";		break;
	case 8:
		$Log_Funtion="解锁";	$SetStr="Locks=1";				include "../model/subprogram/updated_model_3d.php";		break;
	case "UpdateSubName":
		$Log_Funtion="更新子类名";
		$SubName=FormatSTR($SubName);
		if($SubName==""){break;}
		$chinese=new chinese;
		$SubLetter=substr($chinese->c($SubName),0,1);	
		$sql = "UPDATE $DataPublic.oa2_fixedsubtype SET Name='$SubName',Letter='$SubLetter' WHERE Id='$Id' ";
		$result = mysql_query($sql);
		if($result){
			$Log="子分类 $SubName  ID号在 $Id 的记录成功 $Log_Funtion.</br>";
			}
		else{
			$Log="子分类 $SubName  ID号为 $Id 的记录$Log_Funtion 失败! $sql</br>";
			$OperationResult="N";
			}		
		break;
	case "delSubName":
		$Log_Funtion="删除子分类";
		
		$Id_row=mysql_fetch_array(mysql_query("SELECT F.Id FROM $DataPublic.oa2_fixedsubtype O
												 LEFT JOIN $DataPublic.fixed_assetsdata F ON F.TypeId=O.Id
												 WHERE O.Name='$SubName'  LIMIT 1",$link_id));    //是否有生复的记录
		$Id_Temp=$Id_row["Id"];
		if($Id_Temp==""){  //说明不存在固定资产，可以删除 		
			$DelSql = "DELETE FROM $DataPublic.oa2_fixedsubtype WHERE Name='$SubName'"; 
			$DelResult = mysql_query($DelSql);
			if ($DelResult){
				$Log.="子分类 $SubName 删除操作成功(如果记录仍在，则已有记录不能删除).<br>";
				}
			else{
				$OperationResult="N";
				$Log.="<div class='redB'>子分类 $SubName  删除操作失败. $DelSql </div><br>";
				}
			}
		break;
	case 3:
		//$Log_Funtion="更新";	
		$Name=FormatSTR($Name);  //主分类更改
		$chinese=new chinese;
		$Letter=substr($chinese->c($Name),0,1);

		$SetStr="Name='$Name',Letter='$Letter',Date='$DateTime',Operator='$Operator'";
		include "../model/subprogram/updated_model_3a.php";

		
		//新加的子分类
	    include "../admin/subprogram/FireFox_Safari_PassVar.php";   //add by zx 2011-05-04 专门为FirFox,Safari设计传递在Javascript中的自定义元
		$Log.="新增子分类：<br>";
		$IdCount=count($SubName);	
		for($i=1;$i<=$IdCount;$i++){
			$thisSubName=FormatSTR($SubName[$i]);
			if ($thisSubName!=""){
					$Name_row=mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.oa2_fixedsubtype 
															WHERE Name='$thisSubName'  LIMIT 1",$link_id));    //是否有生复的记录
					$Name_Temp=$Name_row["Name"];
					if(Name_Temp!=""){  

						
						$chinese=new chinese;
						$SubLetter=substr($chinese->c($thisSubName),0,1);							
						$inRecode="INSERT INTO $DataPublic.oa2_fixedsubtype (Id,Name,Letter,MainTypeID,Estate,Locks,Date,Operator) VALUES (NULL,'$thisSubName','$SubLetter','$Id','1','0','$Date','$Operator')";
						$inAction=@mysql_query($inRecode);
						if ($inAction){ 
							$Log.="子分类: $thisSubName 增加成功!<br>";
							} 
						else{
							$Log=$Log."<div class=redB>子分类: $thisSubName 增加失败! $inRecode </div><br>";
							$OperationResult="N";
							} 					
						
						
					}
			}
		}
		
		break;
	/*
	default:
		$Name=FormatSTR($Name);
		$SetStr="Name='$Name',Date='$DateTime',Operator='$Operator'";
		include "../model/subprogram/updated_model_3a.php";
		break;
	*/
	
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>