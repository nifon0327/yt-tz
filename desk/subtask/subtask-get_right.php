<?php   
/*用于有审核权限的人可以直接进行相应审核,可以加在一个表进，扫表就行，以后不用改程序，发现表有的，就可以执行， add by zx 2011-08-02

WHERE ModuleName LIKE "%产品%"

*/
$ModuleId=0;
switch($ItemId){
	case 126://待审核检验标准图
		$ModuleId=1213;
		break;
	case 127://待审核出错案例
		$ModuleId=1214;
		break;	
	case 128://采购单审核
		$ModuleId=1046;
		break;	
	case 129://采购请款审核
		$ModuleId=1047;
		break;	
	case 130://预付订金审核
		$ModuleId=1048;
		break;	
	case 131://开发请款审核
		$ModuleId=1049;
		break;	
	case 132://员工薪资审核	
		$ModuleId=1050;
		break;	
	case 133://行政费用审核	
		$ModuleId=1107;
		break;	
	case 134://中港运费审核
		$ModuleId=1101;
		break;	
	case 135://Forward杂费审核	
		$ModuleId=1051;
		break;	
	case 136://快递费审核
		$ModuleId=1108;
		break;	
	case 137://客户样品邮寄费用审核	
		$ModuleId=1197;
		break;	
	case 138://社保请款审核	
		$ModuleId=1161;
		break;	
	case 139://假日加班审核	
		$ModuleId=1177;
		break;	
	case 140://配件报废审核
		$ModuleId=1135;
		break;	
	case 196://产品资实审核
		$ModuleId=1261;
		break;			
	default:

		break;
	}
	
if($ModuleId>0) {  //大于零,存在的模块		
		$subResult = mysql_query("SELECT F.ModuleName,F.ModuleId FROM $DataPublic.modulenexus M LEFT JOIN $DataIn.upopedom U ON U.ModuleId =M.dModuleId LEFT JOIN $DataPublic.funmodule F ON F.ModuleId=U.ModuleId WHERE U.UserId =$Login_Id and F.ModuleId=$ModuleId and U.Action>0 and F.Estate=1 order by M.OrderId",$link_id);	

		if($subRow = mysql_fetch_array($subResult)){
			do{
				$SubMenu=$subRow["ModuleName"];
				$SubModuleIdTemp=$subRow["ModuleId"];
				$SubModuleId=anmaIn($SubModuleIdTemp,$SinkOrder,$motherSTR);//加密
				$linkmodule="link";
				/*
				$linkmodule="<A onfocus=this.blur(); href='../admin/mainFrame.php?Id=$SubModuleId' target='mainFrame' onclick='ClickTotal(this,1,$SubModuleIdTemp)'>$SubMenu</A>";
				*/
				}while ($subRow = mysql_fetch_array($subResult));
			} 		
}
?>