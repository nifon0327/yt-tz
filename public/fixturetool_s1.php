<?php 
//电信---yang 20120801
//代码共享-EWEN 2012-08-19
include "../model/subprogram/s1_model_1.php";
//步骤2：需处理
$Th_Col="选项|40|序号|30|工具Id|50|工具名称|150|工具编码|100|使用周期|60|属性|60|说明|300|状态|40|更新日期|80|操作|80";
$ColsNumber=9;				
$tableMenuS=600;
$Page_Size = 100;							//每页默认记录数量
$isPage=1;//是否分页
include "../model/subprogram/s1_model_3.php";
//步骤4：可选，其它预设选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>";
echo $CencalSstr;
//步骤5：
$SearchSTR=0;//不要查询功能
include "../model/subprogram/s1_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1); 
$mySql="SELECT S.* FROM $DataIn.fixturetool  S  WHERE 1  ";

$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
$Dir=anmaIn("download/ztools/",$SinkOrder,$motherSTR);
	do{
		    $m=1;
	        $Id=$myRow["Id"];
            $ToolsId=$myRow["ToolsId"];
	   	    $GoodsId=$myRow["GoodsId"];
	        $ToolsName=$myRow["ToolsName"];

	        $ToolsCode=$myRow["ToolsCode"];
            $UseTimes=$myRow["UseTimes"];
	        $Remark=$myRow["Remark"]==""?"&nbsp;":$myRow["Remark"];
            $Type =$myRow["Type"]; 
            $Type = $Type==1?"自产":"<span class='redB'>外购</span>";    
		    $Picture=$myRow["Picture"];    
		    if($Picture==1){
			   $Picture=$ToolsId.".jpg";
			   $Picture=anmaIn($Picture,$SinkOrder,$motherSTR);
			   $ToolsName="<span onClick='OpenOrLoad(\"$Dir\",\"$Picture\",\"\",\"Limit\")'  style='CURSOR: pointer;color:#FF6633'>$ToolsName</span>";
			}    
			$Bdata = $ToolsId."^^".$ToolsName;     
		    $Date=$myRow["Date"];
		    $Operator=$myRow["Operator"];
		    $Estate=$myRow["Estate"];
		    switch($Estate){
		     case 1:$Estate="<div class='greenB' align='center'>√</div>";break;
		     case 0:$Estate="<div align='center' class='redB'>×</div>";break;
		    }
		   include "../model/subprogram/staffname.php";
	       $ValueArray=array(
            array(0=>$ToolsId,  1=>"align='center'"),
			array(0=>$ToolsName  ),
			array(0=>$ToolsCode ,1=>"align='center' "),
            array(0=>$UseTimes,  1=>"align='center'"),
            array(0=>$Type,    1=>"align='center'"),
			array(0=>$Remark),
            array(0=>$Estate,   1=>"align='center'"),
			array(0=>$Date,     1=>"align='center'"),
			array(0=>$Operator, 1=>"align='center'")
			);
	
		$checkidValue=$Bdata;
		include "../model/subprogram/read_model_6.php";
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth,"");
  	}
//步骤7：
echo '</div>';
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>