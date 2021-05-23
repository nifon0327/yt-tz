<?php
//电信-zxq 2012-08-01
include "cj_chksession.php";
include "../basic/parameter.inc";
include "../basic/config.inc";
include "../model/modelfunction.php";
include "../model/subprogram/sys_parameters.php";//行政费用比率
$SignS=$SignS==""?0:$SignS;
$CompanyId=$SignS==0?"":$CompanyId;
$ProductTypeId=$SignS==0?"":$ProductTypeId;
$m=$m==""?5:$m;
$Links="item5_1.php";//默认页面
$ErrorInfo="没有设置权限！";
$outArray = explode("|", $_GET['Id']);
$RuleStr = $outArray[0];
$EncryptStr = $outArray[1];
// 真实的 ModuleId
$Mid = anmaOut($RuleStr, $EncryptStr);

/*  START 权限 by.XYG 20180523 */
$checkSubSql=mysql_query("SELECT F.ModuleId,F.ModuleName,F.Parameter,F.OrderId,P.Action
					FROM $DataPublic.sc4_modulenexus M
					LEFT JOIN $DataIn.sc4_upopedom P ON M.dModuleId=P.ModuleId 
					LEFT JOIN $DataPublic.sc4_funmodule F ON F.ModuleId=P.ModuleId 
					LEFT JOIN $DataIn.usertable U ON U.Id=P.UserId
					WHERE F.ModuleId='$Mid' AND U.Number='$Login_P_Number' 
					AND P.Action>0 AND F.Estate=1 GROUP BY F.ModuleId ORDER BY M.OrderId",$link_id);
if($checkSubRow=mysql_fetch_array($checkSubSql)){
do{
    $SubAction=$checkSubRow["Action"];
}while($checkSubRow=mysql_fetch_array($checkSubSql));
}
/* END */
?>
<!doctype html>
<html lang="en">
<head>
<META content='MSHTML 6.00.2900.2722' name=GENERATOR />
<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
<link rel='stylesheet' href='lightgreen/read_line.css'>
<link rel='stylesheet' href='../model/keyright.css'>
<script src='cj_function.js' type=text/javascript></script>
<script language='javascript' type='text/javascript' src='../model/DatePicker/WdatePicker.js'></script>
<script language='javascript' type='text/javascript' src='../model/js/jquery-1.11.1.js'></script>
<!--<script language="javascript" type="text/javascript">toOnline();</script> 暂时禁用(20180518 by xfy)-->
<!--by.lwh 屏蔽右键-->
    <style>
        .ButtonH_25{font: 12px 思源雅黑;
            color: #fff!important;
            background-color: #48bbcb;
            border-radius: 2px;
            height: 20px;
            line-height: 20px;
            width: auto;
            padding-left: 8px;
            padding-right: 8px;
            text-align: center;
            display: inline-block;
            border: none;
            margin-left: 5px;
            cursor: pointer;
        }
        .ButtonH_26{font: 12px 思源雅黑;
          color: #fff!important;
          background-color: #006633;
          border-radius: 2px;
          height: 20px;
          line-height: 20px;
          width: auto;
          padding-left: 8px;
          padding-right: 8px;
          text-align: center;
          display: inline-block;
          border: none;
          margin-left: 5px;
          cursor: pointer;
        }
        .ButtonH_27{font: 12px 思源雅黑;
          color: #fff!important;
          background-color: #990000;
          border-radius: 2px;
          height: 20px;
          line-height: 20px;
          width: auto;
          padding-left: 8px;
          padding-right: 8px;
          text-align: center;
          display: inline-block;
          border: none;
          margin-left: 5px;
          cursor: pointer;
        }
        select{
            color: rgb(148, 149, 152);
            background-color: rgb(245, 245, 245);
            border-width: 0px;
            border-style: initial;
            border-color: initial;
            border-image: initial;
            font: 12px 思源雅黑;
        }
    </style>
<script language="JavaScript">
    document.oncontextmenu=new Function("event.returnValue=false;");
    document.onreadystatechange = function(){
        if(document.readyState=="complete")
        {
            setTimeout(function(){
                document.getElementById('loading').style.display='none';
            }, 1000);
        }
    }
</script>
<title>生产登记</title></head>
<body onload="init()" style="background-color: #f2f3f5;">
<div id="loading" style="text-align: center;vertical-align: middle;display:block;position: fixed;width:110%;height:110%;background-color: rgba(0,0,0,0.2);z-index: 999;top:-10px;left:-10px">
    <img src="../public/img/loading.gif" alt="请稍后..." style="width:200px;height:200px;position:absolute;top:30%;left:40%">
</div>
<!-- 顶部菜单 //-->
<form name='form1' id='form1' enctype='multipart/form-data' method='post' action=''>
<table width="100%" border="0"  cellpadding="0" cellspacing="0" style="position: absolute;margin-bottom: 30px;z-index: -99;">
	<tr><td valign="middle" class="Css_DropShadow"><input name="SignS" type="hidden" id="SignS" value="<?php    echo $SignS?>" /><input name="m" type="hidden" id="m" value="<?php    echo $m?>" />
	  <input name="textarea" type="hidden" class="text" value="<?php    echo $Login_GroupName.":".$Login_Name;?>" size="20">
	  </td>
		<td align="right">
			<table cellpadding="0" cellspacing="10">
			<tr>
			<?php
			$checkMenuSql=mysql_query("SELECT F.ModuleId,F.ModuleName,F.Parameter,F.OrderId
			FROM sc4_funmodule F
			WHERE F.ModuleId=$Mid",$link_id);
					if($checkMenuRow=mysql_fetch_array($checkMenuSql)){
				do{
					$ModuleId=$checkMenuRow["ModuleId"];		//功能ID
					$ModuleName=$checkMenuRow["ModuleName"];	//功能名称
					$Parameter=$checkMenuRow["Parameter"];		//连接参数
					$OrderId=$checkMenuRow["OrderId"];			//排序ID

					echo"<td height='40px' align='center'><select style='display:none' name='Item[$OrderId]' id='Item[$OrderId]' onChange='javascript:ResetPage(0,$OrderId);'>";
							$Links=$Parameter;
					//子功能项目
					///////////////////////
					$checkSubSql=mysql_query("SELECT F.ModuleId,F.ModuleName,F.Parameter,F.OrderId,P.Action
					FROM $DataPublic.sc4_modulenexus M
					LEFT JOIN $DataIn.sc4_upopedom P ON M.dModuleId=P.ModuleId 
					LEFT JOIN $DataPublic.sc4_funmodule F ON F.ModuleId=P.ModuleId 
					LEFT JOIN $DataIn.usertable U ON U.Id=P.UserId
					WHERE M.ModuleId='$ModuleId' AND U.Number='$Login_P_Number' 
					AND P.Action>0 AND F.Estate=1 GROUP BY F.ModuleId ORDER BY M.OrderId",$link_id);

					 $NowModuleId="";
					 if($checkSubRow=mysql_fetch_array($checkSubSql)){
					 	do{

							$SubModuleId=$checkSubRow["ModuleId"];		//功能ID
							$SubModuleName=$checkSubRow["ModuleName"];	//功能名称
							$SubParameter=$checkSubRow["Parameter"];	//连接参数
							$SubOrderId=$checkSubRow["OrderId"];		//排序ID
							if($Item[$OrderId]==""){
								$NowModuleId=$NowModuleId==""?$SubModuleId:$NowModuleId;
								}
							else{
								$NowModuleId=$Item[$OrderId];
								}

							//如果是当前项
							if($m==$OrderId && $SubModuleId==$NowModuleId){
							$SubAction=$checkSubRow["Action"];			//权限
								echo "<option value='$SubModuleId' selected>$SubModuleName</option>";
								if($ModuleId==101){
									$TypeId=$SubParameter;
									$ItemRemark=$SubModuleName;
									}
								else{
									$Links=$SubParameter;
									}
									$fromWorkshopAndAction = $SubParameter;
								}
							else{
								echo "<option value='$SubModuleId'>$SubModuleName</option>";
								}
							}while($checkSubRow=mysql_fetch_array($checkSubSql));
						}
					///////////////////////
					echo"</select></td>";
					echo"<td height='40px' width='5px'>&nbsp;</td>";
					}while($checkMenuRow=mysql_fetch_array($checkMenuSql));
				}
			else{//没有权限的情况
				$checkMenuSql=mysql_query("SELECT F.ModuleName,F.OrderId
				FROM $DataPublic.sc4_funmodule F
				WHERE 1 AND F.Place='1' AND F.Estate=1 ORDER BY F.OrderId DESC
				",$link_id);
				if($checkMenuRow=mysql_fetch_array($checkMenuSql)){
					do{
						$ModuleName=$checkMenuRow["ModuleName"];
						$OrderId=$checkMenuRow["OrderId"];
						echo"<td height='40px' align='center'>
						<select name='Item[$OrderId]' id='Item[$OrderId]' disabled>
						<option value='' selected>$ModuleName</option></select></td>";
						}while($checkMenuRow=mysql_fetch_array($checkMenuSql));
					}
				}
				?>
			</tr></table>
		</td>
	</tr>
</table>
<?php
if($Links==""){
	$Links="item0_1.php";
	$ErrorInfo="功能没开通!";
	}
if (strpos($Links,'|')) {
    $arrTemp = explode('|',$Links);
    $fromWorkshop = $arrTemp[1];
    $fromActionId   = $arrTemp[2];

    include $arrTemp[0];
}else{
    include $Links;
}

//echo "<p align='center'>----" . str_replace(".php", '', $Links)  . "----</p>";
?>

    <div id="ie5menu" style="border-color: rgb(155, 201, 223); border-style: solid; border-width: 1px; display: none; z-index: 99999; position: absolute; background-color: white; visibility: hidden; left: 798px; top: 253px;" class="skin1">
        <div id="ColorSide" style="z-index: 866; left: 0px; width: 20px; position: absolute; background-color: lightblue; height: 77px;"></div>
        <div class="menuitems" onmouseover="myover(this);" onmouseout="myout(this);" onclick="All_elects()" align="right">全选记录&nbsp;&nbsp;</div>
        <div class="menuitems" onmouseover="myover(this);" onmouseout="myout(this);" onclick="Instead_elects()" align="right">反选记录&nbsp;&nbsp;</div>
        <div class="hr"><hr size="1" color="#9bc9df"></div><div class="hr"><hr size="1" color="#9bc9df"></div>
        <div class="menuitems" onmouseover="myover(this);" onmouseout="myout(this);" onclick="window.location.reload();" align="right">刷新页面 &nbsp;&nbsp;</div>
    </div>

    <script>
        var menu = document.getElementById("ie5menu");
        document.oncontextmenu = function(ev) {
            var oEvent = ev || event;
            //自定义的菜单显示
            menu.style.display = "block";
            menu.style.visibility = "visible";
            //让自定义菜单随鼠标的箭头位置移动
            menu.style.left = oEvent.clientX + "px";
            menu.style.top = oEvent.clientY + "px";
            //return false阻止系统自带的菜单，
            //return false必须写在最后，否则自定义的右键菜单也不会出现
            return false;

        }
        //实现点击document，自定义菜单消失
        document.onclick = function() {
            menu.style.display = "none";
            menu.style.visibility = "hidden";
        }

        function myover(obj){
            obj.className = "itemshovor";
        }

        function myout(obj){
            obj.className = "menuitems";
        }

        //全选 反选
        var showmenuFlag=0 //new
        var showIpad=1;
        function showmenuie5(event){
            event.preventDefault();

            if (showIpad==0) showmenuFlag=2;
            else showmenuFlag=1; //new

            var menu = $("ie5menu");
            var Color = $("ColorSide");

            menu.style.display="block";
            menu.style.visibility ="visible";

            var rightedge=table.offsetWidth-event.clientX;
            var bottomedge=table.offsetHeight-event.clientY;

            if(rightedge<menu.offsetWidth){
                menu.style.left=window.scrollX+event.clientX-menu.offsetWidth + "px";
            }
            else{
                menu.style.left=window.scrollX+event.clientX + "px";
            }
            if(bottomedge<menu.offsetHeight){
                menu.style.top=window.scrollY+event.clientY-menu.offsetHeight + "px";
            }
            else{
                menu.style.top=window.scrollY+event.clientY + "px";
            }
            Color.style.height=menu.offsetHeight + "px";
            return false;
        }

        function hidemenuie5(){
            var menu = $("ie5menu");
            menu.style.display="none";
            menu.style.visibility ="hidden";
            showmenuFlag=0; //new
        }

        function $(objName){

            if(document.getElementById){
                return document.getElementById(objName );
            }
            else
            if(document.layers){
                return eval("document.layers['" + objName +"']");
            }
            else{
                return eval('document.all.' + objName);
            }
        }

        function myover(obj){
            obj.className = "itemshovor";
        }

        function myout(obj){
            obj.className = "menuitems";
        }

        var table = document.getElementById("menudiv592");
        if (table !== null){
            table.oncontextmenu=showmenuie5;

            table.onclick=function(){  //new
                if(showmenuFlag==1) {hidemenuie5();}

            };
        }



        function All_elects() {
            jQuery('input[name^="checkId"]:checkbox').each(function() {

                if (jQuery(this).attr("disabled") == "disabled") {
                } else {
                    if(this.checked==false){
                        //this.checked = true;
                        jQuery(this).click();
                    }
                }
            });
            hidemenuie5();
            chooseRow();
        }
        function Instead_elects() {
            jQuery('input[name^="checkId"]:checkbox').each(function() {

                if (jQuery(this).attr("disabled") == "disabled") {
                } else {
                    //if(this.checked==false){
                    //    this.checked = true;
                    //} else {
                    //    this.checked = false;
                    //}
                    jQuery(this).click();
                }
            });
            hidemenuie5();
            chooseRow();
        }

    </script>
