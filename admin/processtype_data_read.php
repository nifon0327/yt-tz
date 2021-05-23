<?php   
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=3;				
$tableMenuS=600;
ChangeWtitle("$SubCompany 工序分类设置");
$funFrom="processtype_data";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|40|序号|30|分类Id|60|分类名称|160|备注|220|排序|60|颜色|80|登记日期|80|状态|40|操作|80";
//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;                           //每页默认记录数量
$ActioToS="1,2,3,4,5,6";							
//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";

if($From!="slist"){
	$SearchRows="";
	}

echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";

//步骤5：
//$helpFile=1;

include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT T.Id,T.gxTypeId,T.gxTypeName,T.Remark,T.Date,T.Operator,T.Estate,T.SortId,T.Color
        FROM $DataIn.process_type T 
        WHERE 1 $SearchRows  ORDER BY SortId ASC";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
$d=anmaIn("download/process/",$SinkOrder,$motherSTR);
	do{
	    $m=1;
	        $Id=$myRow["Id"];
                $gxTypeId=$myRow["gxTypeId"];
		       $gxTypeName=$myRow["gxTypeName"];
                $SortId=$myRow["SortId"];
                $Price=$myRow["Price"]==""?"&nbsp;":$myRow["Price"];
                $Remark=$myRow["Remark"]==""?"&nbsp;":$myRow["Remark"];
                $Color=$myRow["Color"];
                $bgColor="bgcolor='$Color'";              
		        $Date=$myRow["Date"];
		        $Operator=$myRow["Operator"];
		        $Estate=$myRow["Estate"];
		        switch($Estate){
		        		        case 1:$Estate="<div class='greenB' align='center'>√</div>";break;
		        		        case 0:$Estate="<div align='center' class='redB'>×</div>";break;
	            	   }
			include "../model/subprogram/staffname.php";
			$ValueArray=array(
                        array(0=>$gxTypeId,  1=>"align='center'"),
                        array(0=>$gxTypeName),
                        array(0=>$Remark),
                        array(0=>$SortId,    1=>"align='center'"),
                        array(0=>$Color,    1=>"align='center' $bgColor"),
                        array(0=>$Date,     1=>"align='center'"),
                        array(0=>$Estate,   1=>"align='center'"),
                        array(0=>$Operator, 1=>"align='center'")
			);
		$checkidValue=$Id;
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
