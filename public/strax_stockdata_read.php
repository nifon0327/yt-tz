<style type="text/css">
.moveLtoR{ filter:revealTrans(Transition=6,Duration=0.3)};
.moveRtoL{ filter:revealTrans(Transition=7,Duration=0.3)};
/* 为 DIV 加阴影 */ 
.out {position:relative;background:#006633;margin:10px auto;width:400px;}
.in {background:#FFFFE6;border:1px solid #555;padding:10px 5px;position:relative;top:-5px;left:-5px;}  
/* 为 图片 加阴影 */ 
.imgShadow {position:relative;     background:#bbb;      margin:10px auto;     width:220px; } 
.imgContainer {position:relative;      top:-5px;     left:-5px;     background:#fff;      border:1px solid #555;     padding:0; } 
.imgContainer img {     display:block; } 
.glow1 { filter:glow(color=#FF0000,strengh=2)}

.list{position:relative;color:#FF0000;}
.list span img{ /*CSS for enlarged image*/
border-width: 0;
padding: 2px; width:100px;
}
.list span{ 
position: absolute;
padding: 3px;
border: 1px solid gray;
visibility: hidden;
background-color:#FFFFFF;
}
.list:hover{
background-color:transparent;
}
.list:hover span{
visibility: visible;
top:0; left:28px;
}
</style>
<?php
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=12;				
$tableMenuS=600;
ChangeWtitle("$SubCompany strax产品库存列表");
$funFrom="strax_stockdata";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|50|序号|50|图片|60|产品代码|80|描述|250|中文名|210|单价|60|最后出货日期|80|出货总数|70|StockQty|70|Rate|70|Stock in USD|70|Stock in EUR|70";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1";

//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>$CencalSstr&nbsp;&nbsp;&nbsp;&nbsp;<a href='strax_stockdata_toexcel.php' target='_blank' style='color:#FF0000'>导出EXCEL</a>";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT  D.eCode,D.StockQty,D.StockAmount,P.cName,IFNULL(P.Description,D.Description) AS Description,IFNULL(P.ProductId,0) AS ProductId,P.Price,P.TestStandard
FROM $DataIn.straxdata D 
LEFT JOIN $DataIn.productdata P ON P.eCode = D.eCode
WHERE 1 ORDER BY P.ProductId DESC";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$eCode = $myRow["eCode"];
		$StockQty= $myRow["StockQty"];
		$StockEUR= $myRow["StockAmount"];
		$cName = $myRow["cName"];
		$Description = $myRow["Description"];
		$ProductId = $myRow["ProductId"];
		$TestStandard = $myRow["TestStandard"];
		$Price = $myRow["Price"];
		include "../admin/Productimage/getProductImage.php";
		 $AppFilePath="../download/productIcon/" .$ProductId.".jpg";
		   if(file_exists($AppFilePath)){
		         $noStatue="onMouseOver=\"window.status='none';return true\"";
			     $AppFileSTR="<span class='list' >View<span><img src='$AppFilePath' $noStatue/></span></span>";
			}
           else{
	            $AppFileSTR="&nbsp;";
           }
     $LastMonth ="&nbsp;";  $ShipQty  ="&nbsp;";
     if($ProductId>0){
	     $CheckLastMonthRow= mysql_fetch_array(mysql_query("SELECT 
	            DATE_FORMAT(MAX(M.Date),'%Y-%m') AS LastMonth,
	            TIMESTAMPDIFF(MONTH,MAX(M.Date),now()) AS Months,
	            SUM(Qty) AS ShipQty           
                FROM $DataIn.ch1_shipmain M 
	            LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid=M.Id 
                WHERE  S.ProductId ='$ProductId'",$link_id));
		$LastMonth = $CheckLastMonthRow["LastMonth"];
		$ShipQty  = $CheckLastMonthRow["ShipQty"];
     }
       $StockQty = str_replace(",", "", $StockQty);
       if($ShipQty>0){
	       $StockRate = round($StockQty/$ShipQty, 4) * 100;
	       if($StockRate>=10){
		       $StockRate = "<span class='redB'>$StockRate%</span>";
	       }else{
		       $StockRate = $StockRate."%";
	       }
	       
       }else{
	       $StockRate ="&nbsp;";
       }
       $StockEUR = str_replace(",", "", $StockEUR);
       $StockUSA = round($StockEUR/1.117,2);
		$ValueArray=array(
		    array(0=>$AppFileSTR,1=>"align='center'"),
		    array(0=>$eCode,1=>"align='center'"),
			array(0=>$Description),
			array(0=>$TestStandard),
			array(0=>$Price,1=>"align='center'"),
			array(0=>$LastMonth,1=>"align='center'"),
			array(0=>$ShipQty,1=>"align='center'"),
			array(0=>$StockQty,1=>"align='center'"),
			array(0=>$StockRate,1=>"align='center'"),
			array(0=>$StockUSA,1=>"align='center'"),
			array(0=>$StockEUR,1=>"align='center'")
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
