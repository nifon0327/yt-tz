<?php
include "../model/subprogram/s1_model_1.php";
$Th_Col="选项|40|序号|30|分类Id|60|分类名称|160|备注|220|计价公式|180|排序|60|颜色|80|登记日期|80|状态|40|操作|80";
$ColsNumber=9;
$tableMenuS=600;
$Page_Size = 100;							//每页默认记录数量
$isPage=1;//是否分页
include "../model/subprogram/s1_model_3.php";
echo $CencalSstr;
/* 搜索开始 Bend*/
$From=$From==""?"s1":$From;
$SearchSTR=0;
include "../model/subprogram/s1_model_5.php";

//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
$mySql="SELECT T.Id,T.gxTypeId,T.gxTypeName,T.Remark,T.Date,T.Operator,T.Estate,T.SortId,T.Color,T.Loss
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
                $Loss=$myRow["Loss"];
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
            $Bdata=$gxTypeId."^^".$gxTypeName;
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