<?php 
//步骤1电信---yang 20120801
include "../model/modelhead.php";

//步骤2：需处理
$ColsNumber=16;
$tableMenuS=500;
ChangeWtitle("$SubCompany 条码、标签文件审核列表");
$funFrom="productdata";
$From=$From==""?"cm":$From;
$Th_Col="选项|55|序号|40|标签类型|100|产品Id|45|产品名称|280|下载|40|更新日期|70|操作|60";
//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 500;							//每页默认记录数量
$ActioToS="17,15";
//步骤3：
$nowWebPage=$funFrom."_cm";
include "../model/subprogram/read_model_3.php";

//步骤4：需处理-条件选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr	";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT 
	C.Id,C.ProductId,C.CodeType,C.Date,C.Operator,S.cName
	FROM $DataIn.file_codeandlable C
	LEFT JOIN $DataIn.productdata S ON S.ProductId=C.ProductId
	WHERE 1 AND C.Estate=2 ORDER BY S.Id";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$d=anmaIn("download/codeandlable/",$SinkOrder,$motherSTR);	
	do{
		$m=1;
		$Id=$myRow["Id"];
		$ProductId=$myRow["ProductId"];
		$cName=$myRow["cName"];
		//////////////////////////////////////
		$CodeType=$myRow["CodeType"];
		switch($CodeType){
			case 1:$FileRemark="背卡条码";break;
			case 2:$FileRemark="PE袋标签";break;
			case 3:$FileRemark="外箱标签";break;
			case 4:$FileRemark="白盒/坑盒";break;
			}
		$ext_Flag=0;
		$CodeFile=$ProductId."-".$CodeType.".qdf";
		$CodeFileFilePath="../download/codeandlable/".$CodeFile;
		if(file_exists($CodeFileFilePath)){
			$ext_Flag=1;
		   }
		   else{
		      $CodeFile=$ProductId."-".$CodeType.".pdf";
		      $CodeFileFilePath="../download/codeandlable/".$CodeFile;
              if(file_exists($CodeFileFilePath)){
			      $ext_Flag=1;
		      }	 
		    }
		  if ($ext_Flag==1){
			$AltStr="条码或标签文件审核中";
			$CodeFile=anmaIn($CodeFile,$SinkOrder,$motherSTR);
			//$CodeFile="<img onClick='OpenOrLoad(\"$d\",\"$CodeFile\",6)' src='../images/down.gif' alt='$AltStr' width='18' height='18'>";
			$CodeFile="<a href=\"openorload.php?d=$d&f=$CodeFile&Type=&Action=6\" target=\"download\"><img src='../images/down.gif' alt='$AltStr' width='18' height='18'></a>";
			}
		else{
			$AltStr="条码或标签文件出错.";
			$CodeFile="<img src='../images/remark.gif' alt='$AltStr' width='18' height='18'>";
			}
		
		
		//////////////////////////////////////
		$Date=$myRow["Date"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";

		$URL="Stuffdata_Gfile_ajax.php";
        $theParam="ProductId=$ProductId";
		/*
		$showPurchaseorder="<img onClick='PubblicShowOrHide(StuffList$i,showtable$i,StuffList$i,\"$URL\",\"$theParam\",$i,\"\");' name='showtable$i' src='../images/showtable.gif' 
		alt='显示或隐藏产品关联的情况.' width='13' height='13' style='CURSOR: pointer'>";
		//echo "PubblicShowOrHide(StuffList$i,showtable$i,StuffList$i,\"$URL\",\"$theParam\",$i,\"\")";
		$StuffListTB="
			<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";
		*/
		
		$Locks=1;
		$ValueArray=array(
			array(0=>$FileRemark, 		1=>"align='center'"),
			array(0=>$ProductId, 		1=>"align='center'"),
			array(0=>$cName),
			array(0=>$CodeFile, 		1=>"align='center'"),
			array(0=>$Date, 		1=>"align='center'"),
			array(0=>$Operator,		1=>"align='center'")
			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		//echo $StuffListTB;		
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