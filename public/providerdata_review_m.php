<?php 
/* 
二合一已更新
*/
//步骤1
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=16;
$tableMenuS=500;
ChangeWtitle("$SubCompany 供应商评审结果审核列表");
$funFrom="providerdata";
$From=$From==""?"review_m":$From;
$Th_Col="选项|40|序号|40|编号|45|供应商简称|100|年度|50|结论|250|主要原因|250|评审日期|80|操作|50";
//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 500;							//每页默认记录数量
$ActioToS="17,15";
//步骤3：
$nowWebPage=$funFrom."_review_m";
$_SESSION["nowWebPage"]=$nowWebPage; 

include "../model/subprogram/read_model_3.php";

//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT W.Id,W.Year,W.Results,W.Reason,W.Verifier,W.Approver,W.Date,W.Estate,Q.Grade,W.CompanyId,P.Forshort,P.Id AS PId    
         FROM $DataIn.providerreview W 
         LEFT JOIN $DataIn.trade_object P  ON P.CompanyId=W.CompanyId 
         LEFT JOIN $DataPublic.qualitygrade Q ON Q.Id=W.Results 
         WHERE W.Estate=1 ORDER BY W.Year DESC";
  //    echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	 $Idc=anmaIn("providerdata",$SinkOrder,$motherSTR);	
	do{
		$m=1;
                $Id=$myRow["Id"];
		$Year=$myRow["Year"] . "年";
                $Results=$myRow["Results"];
                $Grade=$myRow["Grade"];
                if ($Results>99){
                    $checkGrade=mysql_fetch_array(mysql_query("SELECT Id,Moregrade FROM $DataPublic.qualitymoregrade WHERE Id='$Results'",$link_id));
                    $ResultSTR=$checkGrade["Moregrade"]==""?"&nbsp;":$checkGrade["Moregrade"];        
                    }
                else{
                    $ResultSTR=$Grade;     
                  }
                  
                $PId=$myRow["PId"];
		$Ids=anmaIn($PId,$SinkOrder,$motherSTR);		
		$CompanyId=$myRow["CompanyId"];
		//加密
		$Forshort="<a href='companyinfo_view.php?c=$Idc&d=$Ids' target='_blank'>".$myRow["Forshort"]."</a>";
                
                $Reason=$myRow["Reason"];
                $Operator=$myRow["Verifier"];
                include "../model/subprogram/staffname.php";
                $Date=$myRow["Date"];
	        
		$ValueArray=array(
			array(0=>$CompanyId, 		1=>"align='center'"),
			array(0=>$Forshort),
			array(0=>$Year, 		1=>"align='center'"),
			array(0=>$ResultSTR),
			array(0=>$Reason),
			array(0=>$Date, 		1=>"align='center'"),
			array(0=>$Operator,		1=>"align='center'")
			);
		$checkidValue=$Id;
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