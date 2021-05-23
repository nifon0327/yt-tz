<?php   
//步骤1电信---yang 20120801
include "../model/subprogram/s1_model_1.php";
//步骤2：需处理
$Th_Col="选项|40|序号|30|工序Id|50|工序名称|120|工序说明|280|基础损耗|60|工序类型|70|所属加工|100|单  价|60|登记日期|80|状态|40|操作|80";
$ColsNumber=8;
$tableMenuS=600;
$Page_Size = 100;							//每页默认记录数量
$isPage=1;//是否分页
//已传入参数：目的查询页面，来源页面，可选记录数，动作，类别uType

//步骤3：
include "../model/subprogram/s1_model_3.php";
//步骤4：可选，其它预设选项
//if($From!="slist"){
if ($fSearchPage=="processbom"){
	//$SearchRows.=" AND S.TypeId='$SelTypeId' ";
}
else{
	$SearchRows="";
	/*$result = mysql_query("SELECT * FROM $DataIn.stufftype WHERE Estate=1 AND mainType='3' order by Letter",$link_id);
	if($myrow = mysql_fetch_array($result)){
	echo"<select name='StuffType' id='StuffType' onchange='ResetPage(this.name)'><option value='' selected>--工序类型--</option>";
	  $NameRule="";
		do{
			$theTypeId=$myrow["TypeId"];
			$TypeName=$myrow["Letter"]."-".$myrow["TypeName"];
			if ($StuffType==$theTypeId && $search==""){
				echo "<option value='$theTypeId' selected>$TypeName</option>";
				$SearchRows.=" AND S.TypeId='$theTypeId' ";
				$NameRule=$myrow["NameRule"];
				}
			else{
				echo "<option value='$theTypeId'>$TypeName</option>";
				
				}
			}while ($myrow = mysql_fetch_array($result));
			echo "</select>&nbsp;";
		}*/
}

//echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>";
echo $CencalSstr;
//echo "<input name='SelTypeId' type='hidden' id='SelTypeId' value='$SelTypeId'>";
/* 搜索开始 Bend*/
$From=$From==""?"s1":$From;
echo "<input name='From' type='hidden' id='From' value='$From'>";
//$oldresearch=$oldresearch==""?$sSearch:$oldresearch;  //把第一次载放时的条件存起来
//echo "<input name='oldresearch' type='hidden' id='oldresearch' value='$oldresearch'>";
$searchtable="process_data|S|ProcessName|0|0"; //快速搜索的表名，字段名. 表名|别名|字段|1  1表示带Estate字段,其它值无
include "../model/subprogram/QuickSearch.php";

if ($FromSearch=="FromSearch" && $search!="") {  //来自快速搜索
		$Arraysearch=explode("|",$searchtable);
		$TAsName=$Arraysearch[1];
		$TField=$Arraysearch[2];
		$SearchRows="  AND $TAsName.$TField like '%$search%'  ";
		if ($SelTypeId!="")  $SearchRows.=" AND S.TypeId='$SelTypeId' ";
		//$SearchRows=$oldresearch.$SearchRows;
	}
//步骤5：

$SearchSTR=0;
include "../model/subprogram/s1_model_5.php";

//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);

$mySql="SELECT S.Id,S.ProcessId,S.ProcessName,S.Price,S.Picture,S.Remark,S.Date,S.Operator,S.Estate,PT.SortId,T.TypeName,PT.Color ,S.BassLoss,PT.gxTypeName 
        FROM $DataIn.process_data  S 
        LEFT JOIN $DataIn.process_type PT ON PT.gxTypeId=S.gxTypeId
        LEFT JOIN $DataIn.stufftype T ON T.TypeId=S.TypeId 
        WHERE 1 $SearchRows AND S.Estate=1 ORDER BY PT.SortId";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
                $ProcessId=$myRow["ProcessId"];
                           $BassLoss=$myRow["BassLoss"];              
                           $BassLoss=($BassLoss*100)."%";  
		$ProcessName=$myRow["ProcessName"];
         $gxTypeName=$myRow["gxTypeName"];
                $TypeName=$myRow["TypeName"];
                $SortId=$myRow["SortId"];
                $Price=$myRow["Price"]==""?"&nbsp;":$myRow["Price"];
                $Remark=$myRow["Remark"]==""?"&nbsp;":$myRow["Remark"];
                
                switch($Action){
		           case "1"://读取加工工序资料
			            $Bdata=$ProcessId . "^^" . $ProcessName;
			             break;
			       case "2":
			            $Bdata=$ProcessId . "^^" . $ProcessName. "^^" . $TypeName. "^^" . $Remark. "^^" . $SortId;
			            break;
                }
                
		$Picture=$myRow["Picture"];
                if ($Picture==1){
                   $f=anmaIn($ProcessId.".jpg",$SinkOrder,$motherSTR); 
                   $ProcessName="<span onClick='OpenOrLoad(\"$d\",\"$f\")' style='CURSOR: pointer;color:#F00;'>$ProcessName</span>";
                }
                
		$Date=$myRow["Date"];
		$Operator=$myRow["Operator"];
		$Estate=$myRow["Estate"];
		switch($Estate){
		   case 1:$Estate="<div class='greenB' align='center'>√</div>";break;
		   case 0:$Estate="<div align='center' class='redB'>×</div>";break;
		 }
		include "../model/subprogram/staffname.php";
		$ValueArray=array(
                        array(0=>$ProcessId,  1=>"align='center'"),
			array(0=>$ProcessName),
			array(0=>$Remark),
                        array(0=>$BassLoss,    1=>"align='center'"),
                        array(0=>$gxTypeName,    1=>"align='center'"),
                        array(0=>$TypeName,    1=>"align='center'"),
                        array(0=>$Price,    1=>"align='center'"),
			array(0=>$Date,     1=>"align='center'"),
			array(0=>$Estate,   1=>"align='center'"),
			array(0=>$Operator, 1=>"align='center'")
			);
                
		
		$checkidValue=$Bdata;
		include "../model/subprogram/read_model_6.php";
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