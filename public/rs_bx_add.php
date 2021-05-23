<?php
	
	include "../model/modelhead.php";
	$path = $_SERVER["DOCUMENT_ROOT"];
	include_once("$path/ipdAPI/webClass/prototype/AddFunctionFactory.php");
	include_once("$path/ipdAPI/webClass/BxClass/AddSingleStaffBxProduct.php");
	include_once("$path/ipdAPI/webClass/BxClass/AddMultiStaffBxProduct.php");
	
	//步骤2：
	ChangeWtitle("$SubCompany 新增补休记录");//需处理
	$nowWebPage =$funFrom."_add";	
	$toWebPage  =$funFrom."_save";	
	$_SESSION["nowWebPage"]=$nowWebPage;
	
	$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
	//步骤3：
	$tableWidth=850;$tableMenuS=500;
	include "../model/subprogram/add_model_t.php";

	$addFunctionClass = (strtolower($From) == "personal")?"AddSingleStaffBxProduct":"AddMultiStaffBxProduct";
	$addFunction = new $addFunctionClass;
	$addFunction->setupInfomation($tableWidth, $funFrom);
	
	$addFactory = new AddFunctionFactory();
	
	echo $addFactory->doFactory($addFunction);
	
	include "../model/subprogram/add_model_b.php";
?>

<script language="javascript">
	function checkBxtime(){
		var bxEndDate = document.getElementById('EndDate').value;
		var bxEndTime = document.getElementById('EndTime').value;
		var bxEnd = bxEndDate+' '+bxEndTime;
		var todayTimeStamp = new Date().getTime();
		var currentTime = new Date(Date.parse(bxEnd .replace(/-/g, "/"))).getTime();
		if((todayTimeStamp - currentTime)/(3600000) > 24){
			document.getElementById('buttonSaveBtn').style.visibility = 'hidden';
			document.getElementById('topSaveBtn').style.visibility = 'hidden';
		}else{
			document.getElementById('buttonSaveBtn').style.visibility = 'visible';
			document.getElementById('topSaveBtn').style.visibility = 'visible';
		}
	}
</script>