<?php 
include "../model/subprogram/s1_model_1.php";
$Th_Col="选项|40|序号|30|条码|140|资产编号|160|图片|30|入库地点|70|入库时间|80|入库单|100|状态|30";
$ColsNumber=12;
$tableMenuS=600;
$Page_Size = 100;							//每页默认记录数量
$Parameter.=",Jid,$Jid,Bid,$Bid";

//步骤3：
include "../model/subprogram/s1_model_3.php";
//步骤4：可选，其它预设选项
$sSearch=$From!="slist"?"":$sSearch;
$sSearch.=$Jid==""?"":" AND A.CompanyId='$Jid'";
$sSearch.=$Bid==""?"":" AND A.BuyerId='$Bid'";

echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>";
echo $CencalSstr;
//步骤5：
include "../model/subprogram/s1_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT C.BarCode,C.Id,C.GoodsNum,K.Name AS rkName,C.Picture,C.Estate,M.Date AS rkDate,M.BillNumber,M.Bill,S.Mid
    FROM $DataIn.nonbom7_code  C 
   LEFT JOIN $DataPublic.nonbom0_ck  K  ON K.Id=C.CkId
   LEFT JOIN $DataIn.nonbom7_insheet  S  ON S.Id=C.rkId
   LEFT JOIN $DataIn.nonbom7_inmain M ON M.Id=S.Mid
   WHERE  C.GoodsId=$GoodsId AND C.Estate=1";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRows = mysql_fetch_array($myResult)){
	$DirRK=anmaIn("download/nonbom_rk/",$SinkOrder,$motherSTR);
       do{
		       $m=1;
              $BarCode=$myRows["BarCode"];
              $GoodsNum=$myRows["GoodsNum"];
              $rkName=$myRows["rkName"];
              $Picture=$myRows["Picture"];
		      $checkidValue=$BarCode."^^".$GoodsNum;
		                         $Dir=anmaIn("download/nonbomCode/",$SinkOrder,$motherSTR);
                                      if($Picture!=""){
                                       		$PictureStr="<span onClick='OpenOrLoad(\"$Dir\",\"$Picture\")'  style='CURSOR: pointer;color:#FF6633'>View</span>";
                                           }
             $rkDate=$myRows["rkDate"];
             $BillNumber=$myRows["BillNumber"];
             $Bill=$myRows["Bill"];
             $Mid=$myRows["Mid"];
		           			if($Bill==1){
			           			$Bill=$Mid.".jpg";
				           		$Bill=anmaIn($Bill,$SinkOrder,$motherSTR);
			           			$BillNumber="<span onClick='OpenOrLoad(\"$DirRK\",\"$Bill\")' style='CURSOR: pointer;color:#FF6633'>$BillNumber</span>";
			           			}
	           				else{
			           			$BillNumber=$BillNumber;
			           			}
             $Estate=$myRows["Estate"];
             $Estate=$Estate==1?"<span class='greenB'>√</span>":"<span class='redB'>X</span>";
		   $ValueArray=array(
		    	array(0=>$BarCode,		1=>"align='center'"),
	    		array(0=>$GoodsNum),
	    		array(0=>$PictureStr,	1=>"align='center'"),
	    		array(0=>$rkName,	1=>"align='center'"),
    			array(0=>$rkDate,		1=>"align='center'"),
			    array(0=>$BillNumber,		1=>"align='center'"),
		    	array(0=>$Grade,		1=>"align='center'")
		     	);
		    include "../model/subprogram/s1_model_6.php";
         }while($myRows = mysql_fetch_array($myResult));
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