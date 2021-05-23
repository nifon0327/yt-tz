<?php   
include "../model/modelhead.php";
include "../model/subprogram/sys_parameters.php";
$ColsNumber=15;
$tableMenuS=500;
ChangeWtitle("Product-sku-master carton");
//序号，产品代码,装箱数量，外箱尺寸，重量，净重
$Th_Col="No.|40|Product Code|150|quantity|60<br>(pcs)|net weight<br>(g)|60|gross weight<br>(g)|60|width mt.<br>(cm)|60|length mt.<br>(cm)|60|height mt.<br>(cm)|60|volume<br>(cm3)|60|pieces inside master carton(pcs)|80|net weight<br>(kg)|80|gross weight<br>(kg)|80|width mt.carton<br>(cm)|80|length mt.carton<br>(cm)|80|height mt.carton<br>(cm)|80|volume<br>(m3)|80";
$toExcelStr="<input type='button' value='toExcel' id='toExcelcel' onclick='celtoExcel(\"$from\")'>";
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$ChooseOut="N";
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
       $chooseAction=$chooseAction==""?2:$chooseAction;
       $chooseStr="chooseAction".$chooseAction;
       $$chooseStr="selected";
       echo "<select id='chooseAction' name='chooseAction' onchange='document.form1.submit()'>";
       echo "<option value='2' $chooseAction2> all products</option> ";
       echo "<option value='0' $chooseAction0>new products</option> ";
       echo "<option value='1' $chooseAction1>normal products</option> ";
       echo "</select>&nbsp;&nbsp;&nbsp;&nbsp;";
        if($chooseAction==0)$SearchRows.="AND P.TestStandard=0";
        if($chooseAction==1)$SearchRows.="AND P.TestStandard=1";

echo $toExcelStr;
$searchtable="productdata|P|eCode|0"; //快速搜索的表名，字段名. 表名|别名|字段|1  1表示带Estate字段,其它值无无
include "../model/subprogram/QuickSearch.php";
include "../admin/subprogram/read_model_5.php";
switch($from){
     case "cel":
                    $CompanySTR="and (P.CompanyId='1004' OR P.CompanyId='1059' OR P.CompanyId='1072') "; break;
    case "AF":
                     $CompanySTR="and (P.CompanyId='1064' OR P.CompanyId='1071' )"; break;
      }
$i=1;$j=1;
List_Title($Th_Col,"1",1);
$mySql= "SELECT P.ProductId,P.eCode,P.MainWeight,P.Weight,G.Relation,S.Spec,P.TestStandard
FROM $DataIn.productdata P
LEFT JOIN $DataIn.pands G ON. G.ProductId=P.ProductId
LEFT JOIN $DataIn.stuffdata S ON S.StuffId=G.StuffId
WHERE 1  AND S.TypeId='9040' AND P.Estate=1 $SearchRows $CompanySTR order by P.Estate DESC,P.Id DESC";
$myResult = mysql_query($mySql,$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$d3=anmaIn("download/productfile/",$SinkOrder,$motherSTR);
	do{
		$m=1;
		$eCode=$myRow["eCode"]==""?"&nbsp;":$myRow["eCode"];
        $ProductId=$myRow["ProductId"];
		$TestStandard=$myRow["TestStandard"];
		if($TestStandard==1){
			$FileName="T".$ProductId.".jpg";
			$f=anmaIn($FileName,$SinkOrder,$motherSTR);
			$d=anmaIn("download/teststandard/",$SinkOrder,$motherSTR);			
			$eCode="<span onClick='OpenOrLoad(\"$d\",\"$f\")' style='CURSOR: pointer;color:#FF6633;'>$eCode</span>";
			}
        $MainWeight=$myRow["MainWeight"]==0?"&nbsp;":$myRow["MainWeight"];
		$Weight=$myRow["Weight"]==0?"&nbsp;":$myRow["Weight"];

		$Relation=explode("/",$myRow["Relation"]); 
		$Boxs=$Relation[1];
		$Spec=explode("cm", $myRow["Spec"]);        
        $SpecArray=explode("×",$Spec[0]);
        $BoxLenght=$SpecArray[0];
        $BoxWidth=$SpecArray[1];
        $BoxHight=$SpecArray[2];
        $BoxVolume=sprintf("%.2f",($BoxLenght*$BoxWidth*$BoxHight)/1000000);
		$BoxNW=($Boxs*$Weight)/1000;
		if($BoxNW>0){
			    $BoxGW=$BoxNW+1;
		       }
		else{
			    $BoxNW="&nbsp;";$BoxGW="&nbsp;";
			  }

            $SizeResult=mysql_fetch_array(mysql_query("SELECT * FROM $DataIn.product_size WHERE ProductId='$ProductId'",$link_id));
            $Width=$SizeResult["Width"];
            $Lenght=$SizeResult["Length"];
            $Hight=$SizeResult["height"];
            $Volume=$Width*$Lenght*$Hight;
            $Volume=$Volume==0?"&nbsp;":$Volume;
            if(ceil($Width)==$Width && $Width!="")$Width=intval($Width);
            if(ceil($Lenght)==$Lenght && $Lenght!="")$Lenght=intval($Lenght);
            if(ceil($Hight)==$Hight && $Hight!="")$Hight=intval($Hight);

            $addstr1="onclick=\"openWinDialog(this,$ProductId,420,260,'center',$i)\"";
			$ValueArray=array(
				array(0=>$eCode),//产品代码
				array(0=>"1",1=>"align='center'"),//数量
				array(0=>$MainWeight,			1=>"align='center'"),//单个产品的净重
				array(0=>$Weight,			            1=>"align='center'"),//单个产品的毛重
				array(0=>$Width,			            1=>"align='center'",2=>$addstr1),//单个产品的宽
				array(0=>$Lenght,			            1=>"align='center'",2=>$addstr1),//单个产品的长
				array(0=>$Hight,			            1=>"align='center'",2=>$addstr1),//单个产品的高
				array(0=>$Volume,			        1=>"align='center'"),//单个产品的体积
				array(0=>$Boxs,			                1=>"align='center'"),//一箱的数量
				array(0=>$BoxNW,			            1=>"align='center'"),//整箱的净重
				array(0=>$BoxGW,			            1=>"align='center'"),//整箱的毛重
				array(0=>$BoxWidth,			    1=>"align='center'"),//整箱的宽
				array(0=>$BoxLenght,			    1=>"align='center'"),//整箱的长
				array(0=>$BoxHight,			        1=>"align='center'"),//整箱的高
				array(0=>$BoxVolume,			1=>"align='center'"),//整箱的体积
				);
		$checkidValue=$Id;
		include "../admin/subprogram/read_model_6.php";
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
  	}
//步骤7：
echo '</div>';
include "../model/subprogram/read_model_menu.php";
?>
<div id='winDialog' style="position:absolute;display:none;z-index:9;-moz-border-radius:12px;-webkit-border-radius:12px;border: 2px solid #333;background:#CCC;" onDblClick="closeWinDialog()"></div>
<script>

function celtoExcel(tempfrom){ 
      document.form1.action="product_skuma_toexcel.php?myCompanyId="+<?php echo $myCompanyId?>+"&from="+tempfrom;
       document.form1.target="_self";
       document.form1.submit();	
}

function savedata(ProductId,index){
    var tableId="ListTable"+index;
	var ListTable=document.getElementById(tableId);
    var widthpcs=document.getElementById("widthpcs").value;
    var lengthpcs=document.getElementById("lengthpcs").value;
    var heightpcs=document.getElementById("heightpcs").value;
    if(widthpcs=="" || lengthpcs=="" || heightpcs==""){closeWinDialog();return false;}
    var url="product_skuma_ajax.php?ProductId="+ProductId+"&width="+widthpcs+"&length="+lengthpcs+"&height="+heightpcs;
　 var ajax=InitAjax();
　	 ajax.open("GET",url,true);
	 ajax.onreadystatechange =function(){
	　　if(ajax.readyState==4 && ajax.status ==200){
                if(ajax.responseText=="Y"){
                     ListTable.rows[0].cells[5].innerHTML=widthpcs;
                     ListTable.rows[0].cells[6].innerHTML=lengthpcs;
                     ListTable.rows[0].cells[7].innerHTML=heightpcs;
                     ListTable.rows[0].cells[8].innerHTML=widthpcs*lengthpcs*heightpcs;
                    closeWinDialog();
                    }
			}
		}
　	ajax.send(null);
}

//打开DIV弹出窗口
function openWinDialog(e,ProductId,w,h,Pos,index){
    bfButton=e;
	var showDialog=document.getElementById("winDialog");
	showDialog.innerHTML="";
	var url="product_skuma_add.php?ProductId="+ProductId+"&index="+index;
　	var ajax=InitAjax();
　	ajax.open("GET",url,true);
	ajax.onreadystatechange =function(){
	　　if(ajax.readyState==4 && ajax.status ==200){
	　　　	showDialog.innerHTML=ajax.responseText;
			}
		}
　	ajax.send(null);
    showDialog.style.width=w+"px";
    //showDialog.style.height=h+"px";
	//定位对话框
	//var Pos="center";
	var offsetPos=getAbsolutePos(e);
	switch(Pos){
		case "top":
		   var calTop=offsetPos.y-h.height-5;		  
	       var calLeft=offsetPos.x-(w-e.offsetWidth)/2;
		   break;
		case "left":
		   var calTop=offsetPos.y-(h-e.offsetHeight)/2;
		   var calLeft=offsetPos.x-w -5;
		   break;
		case "right":
		   var calTop=offsetPos.y-(h-e.offsetHeight)/2;
		   var calLeft=offsetPos.x+e.offsetWidth+5;
		   break;
		case "bottom":
		   var calTop=offsetPos.y+e.offsetHeight+5;
	       var calLeft=offsetPos.x-(w-e.offsetWidth)/2;
		   break;
		case "center":
		   if(!-[1,]){  //判断是否为IE
		     calTop=document.documentElement.scrollTop +(document.documentElement.clientHeight -h)/2;
             calLeft =document.documentElement.scrollLeft +(document.documentElement.clientWidth-w)/2;
               }
           else{
	           calLeft = window.pageXOffset+(window.innerWidth-w)/2;
	           calTop= window.pageYOffset+(window.innerHeight-h)/2;
            }
		  break;
	}
	  if (calTop<=0) calTop=5;
	  if (calLeft<=0) calLeft=5;
	 showDialog.style.left =calLeft+"px";
	 showDialog.style.top =calTop+"px";
	 showDialog.style.display='block';
}

function closeWinDialog(){
	document.getElementById('winDialog').style.display='none';
}
function getAbsolutePos(el) { //取得对象的绝对位置
   var r = { x: el.offsetLeft, y: el.offsetTop };
   if (el.offsetParent) {
    var tmp = getAbsolutePos(el.offsetParent);
    r.x += tmp.x;
    r.y += tmp.y;
   }
   return r;
  };

</script>