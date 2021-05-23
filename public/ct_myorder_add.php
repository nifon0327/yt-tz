<?php 
//电信-EWEN
//更新到public by zx 2012-08-03
//代码、数据共享-EWEN 2012-08-14
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 点餐");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;$tableMenuS=500;
$SaveSTR="NO";
$SaveFun="<span onClick='SaveMenu(\"$toWebPage\",\"$ActionId\")' $onClickCSS>保存</span>&nbsp";
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
	<table width="650" border="0" align="center" cellspacing="5">
		<tr>
            <td height="31" scope="col" align="right">餐厅</td>
            <td scope="col" colspan="3"><select name="CtId" id="CtId" style="width:380px" dataType="Require"  msg="未选择餐厅" onchange='getMenu(this)'>
            <option value="" selected>请选择</option>
            <?php 
				$CheckResult = mysql_query("SELECT Id,Name FROM $DataPublic.ct_data WHERE Estate='1' ORDER BY Id",$link_id);
				while ($CheckRow = mysql_fetch_array($CheckResult)){
					$Id=$CheckRow["Id"];
					$Name=$CheckRow["Name"];
					echo"<option value='$Id'>$Name</option>";
					}
				?>
           </select></td></tr>
		<tr>
		  <td height="31" scope="col" align="right">菜式分类</td>
		  <td scope="col"  colspan="3"><select name="mType" id="mType" style="width:380px" dataType="Require"  msg="未选择分类"  onchange='getMenu(this)'>
            <option value="" selected>请选择</option>
            <?php 
				$CheckResult = mysql_query("SELECT Id,Name FROM $DataPublic.ct_type WHERE Estate='1' ORDER BY Id",$link_id);
				while ($CheckRow = mysql_fetch_array($CheckResult)){
					$Id=$CheckRow["Id"];
					$Name=$CheckRow["Name"];
					echo"<option value='$Id'>$Name</option>";
					}
				?>
           </select></td>
	    </tr>
		<tr>
		  <td height="220" scope="col" align="right" >菜单列表</td>
		  <td scope="col"><select name="MenuList" id="MenuList" style="width:200px;height:200px;background:#EEE;"  multiple "></td>
		  <td height="31" scope="col" align="center" valign="middle">
		  <input name="Number" id="Number" size="3" value="1" style='text-align:right;'>份 </br></br></br>
			  <input type="button" name="addBtn" id="addBtn"  value="添 加" onclick="addOrderList()"/>
		  </td>
		  <td height="107" scope="col" align="center">今天点餐记录</br>
              <select name="OrderList" id="OrderList" style="width:200px;height:200px;background:#EEE;"  multiple ></td>
	    </tr> 
	     <input name="OrderMenu" id="OrderMenu" type="hidden" value=""/>
	      <td height="31" scope="col" align="right">&nbsp;</td>
	      <td scope="col" colspan="2">备注:<input name="Remark" type="text" id="Remark" value="" size="40" > </td>
		  <td scope="col">餐费合计:<input name="Price" id="Price" size="10" value="0" style='text-align:right;' readonly>元</td>
       
</td></tr>


</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>
<script language="javascript">

function getMenu(e){

    var CtId=document.getElementById("CtId").value;
     var mType=document.getElementById("mType").value;
      if (mType=="") return;
      if (CtId=="") {
         alert("请选择餐厅名称");
         return;
     }
     var url="ctmenu_read_ajax.php?CtId="+CtId+"&mType="+mType+"&do="+Math.random();
    var ajax=InitAjax();
　	 ajax.open("GET",url,true);
     ajax.onreadystatechange =function(){
     if(ajax.readyState==4){
			var BackData=ajax.responseText;
				 setMenuName(BackData);
		 }	
	   }
	 ajax.send(null);	 
}

function setMenuName(nameStr)
{
	var MenuList=document.getElementById("MenuList");
	var slen=MenuList.options.length;
	for(var i=0;i<slen;i++)
	{
		MenuList.options[0]=null;
	}
	var nameArr=nameStr.split("|");
	var len=nameArr.length;
	var j=0;
	for(var i=1;i<len;i=i+4)
	{
		 MenuList.options[j]=new Option(nameArr[i]+"(￥"+nameArr[i+1]+")",nameArr[i-1]);
         MenuList.options[j].setAttribute("data",nameArr[i-1] +"|"+nameArr[i] +"|"+nameArr[i+1] +"|"+nameArr[i+2]);
		j++;
	}
}

function addOrderList(){
     var number=document.getElementById("Number").value;
     var checkNum=fucCheckNUM(number,"");
     if(checkNum==0 || number==0)  alert("请输入正确的点餐数量(份数)");
    
    var Price=document.getElementById("Price").value*1;
    var OrderList=document.getElementById("OrderList");
    var j=OrderList.options.length;
    var MenuList=document.getElementById("MenuList");
    
	var slen=MenuList.options.length;
	for(var i=0;i<slen;i++)
	{
		if (MenuList.options[i].selected){
                    var dataStr=MenuList.options[i].attributes["data"].nodeValue; 
                    var dataArr=dataStr.split("|");
                    OrderList.options[j]=new Option(dataArr[1]+" ["+number+" 份]",dataArr[0]);
                    odataStr=dataArr[3]+"|"+dataArr[0]+"|"+dataArr[2]+"|"+number;
                    OrderList.options[j].setAttribute("data",odataStr);
                    Price+=dataArr[2]*1;
                    j++;
              }
        }
        document.getElementById("Price").value=Price;
}

function SaveMenu(toWebPage,ActionId)
{
 var OrderList=document.getElementById("OrderList");
    var slen=OrderList.options.length;
    if (slen<1) alert("请选择菜单及数量");
    var menuStr="";
    for(var i=0;i<slen;i++)
	{
                    var dataStr=OrderList.options[i].attributes["data"].nodeValue; 
                     menuStr+= menuStr==""?dataStr:"^"+dataStr;
        }
    document.getElementById("OrderMenu").value=menuStr;
    document.form1.action=toWebPage+".php?ActionId="+ActionId;
    document.form1.submit();
}

</script>