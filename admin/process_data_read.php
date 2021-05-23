<?php   
//$DataPublic.msg3_notice 二合一已更新电信---yang 20120801
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=8;				
$tableMenuS=600;
ChangeWtitle("$SubCompany 加工工序资料设置");
$funFrom="process_data";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|40|序号|30|工序Id|50|所属加工|100|工序名称|160|排序号|40|单  价|60|工序说明|300|基础损耗<br>比率|60|图档|30|登记日期|80|状态|40|操作|80";
//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;                           //每页默认记录数量
$ActioToS="1,2,3,4,5,6,40";							
//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";

if($From!="slist"){
	$SearchRows="";
	/*$result = mysql_query("SELECT * FROM $DataIn.stufftype WHERE Estate=1 AND mainType='3' order by Letter",$link_id);
	if($myrow = mysql_fetch_array($result)){
	echo"<select name='StuffType' id='StuffType' onchange='ResetPage(this.name)'><option value='' selected>--工序类型--</option>";
	  $NameRule="";
		do{
			$theTypeId=$myrow["TypeId"];
			$TypeName=$myrow["Letter"]."-".$myrow["TypeName"];
			if ($StuffType==$theTypeId){
				echo "<option value='$theTypeId' selected>$TypeName</option>";
				$SearchRows=" AND S.TypeId='$theTypeId' ";
				$NameRule=$myrow["NameRule"];
				}
			else{
				echo "<option value='$theTypeId'>$TypeName</option>";
				
				}
			}while ($myrow = mysql_fetch_array($result));
			echo "</select>&nbsp;";
		}*/
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
$mySql="SELECT S.Id,S.TypeId,S.ProcessId,S.ProcessName,S.Price,S.Picture,S.Remark,S.Date,S.Operator,S.Estate,T.TypeName,S.BassLoss ,PT.Color,PT.gxTypeName,PT.SortId 
        FROM $DataIn.process_data  S 
        LEFT JOIN $DataIn.process_type PT ON PT.gxTypeId=S.gxTypeId
        LEFT JOIN $DataIn.stufftype T ON T.TypeId=S.TypeId 
        WHERE 1 $SearchRows  GROUP BY S.Id ORDER BY PT.SortId,S.ProcessName";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
$d=anmaIn("download/process/",$SinkOrder,$motherSTR);
	do{
	    $m=1;
	        $Id=$myRow["Id"];
            $ProcessId=$myRow["ProcessId"];
	   	    $ProcessName=$myRow["ProcessName"];
	        $TypeName=$myRow["TypeName"];
	        $gxTypeName=$myRow["gxTypeName"];
             $SortId=$myRow["SortId"];
	        $Price=$myRow["Price"]==""?"&nbsp;":$myRow["Price"];
	        $Remark=$myRow["Remark"]==""?"&nbsp;":$myRow["Remark"];
            $BassLoss =$myRow["BassLoss"];     
		    $Picture=$myRow["Picture"];
            include "subprogram/process_Gfile.php";	//图档显示
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
            array(0=>$ProcessId,  1=>"align='center'"),
			//array(0=>$TypeName,    1=>"align='center'"),
			array(0=>$gxTypeName,    1=>"align='center'"),
			array(0=>$ProcessName ,1=>"align='left' $bgColor"),
            array(0=>$SortId,  1=>"align='center'"),
            array(0=>$Price,    1=>"align='center'"),
			array(0=>$Remark),
            array(0=>$BassLoss,  1=>"align='center'"),
            array(0=>$Gfile,  1=>"align='center'"),
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
