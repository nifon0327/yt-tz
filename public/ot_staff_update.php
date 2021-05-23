<?php 
//电信-joseph
//代码共享-EWEN 2012-08-13
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新外部人员资料");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upRow =mysql_fetch_array(mysql_query("SELECT Name,Forshort FROM $DataIn.ot_staff WHERE Id='$Id'",$link_id));
$Name=$upRow["Name"];
$Forshort=$upRow["Forshort"];
$comSql = "SELECT ForShort FROM trade_object WHERE Id IN ($Forshort) ";
$comRes = mysql_query($comSql,$link_id);
if($comRow = mysql_fetch_array($comRes)){
    do {
        if ($companyName) {
            $companyName .= '，'.$comRow["ForShort"];
        }else{
            $companyName = $comRow["ForShort"];
        }
    }while ($comRow = mysql_fetch_array($comRes));
}
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
	<table width="650" border="0" align="center" cellspacing="5">
		<tr>
            <td height="31" scope="col" align="right">外部人员姓名</td>
            <td scope="col"><input name="Name" type="text" id="Name" style="width:380px;" value="<?php  echo $Name?>" maxlength="20" title="必选项,在20个汉字内." dataType="LimitB" min="1" max="40" msg="没有填写或超出许可范围"></td>
    	</tr>
        <tr>
            <td height="31" scope="col" align="right">公司名称</td>
            <td scope="col"><input name="company" type="text" id="company" style="width:380px;" onclick="f()"  value="<?php  echo $companyName?>"
                                   dataType="LimitB">
                <input name="Forshort" type="text" id="Forshort" style="width:380px;display: none   "
                       dataType="LimitB"></td>
        </tr>
	</table>
</td></tr></table>
<?php
//步骤5：
include "../model/subprogram/add_model_b.php";
include "../model/subprogram/read_model_menu.php";
?>
<script type="text/javascript" src="../plugins/layer/layer.js"></script>
<script>
    function f() {
        layer.open({
            type: 2,
            area: ['800px', '500px'],
            title: false
            , closeBtn: 0
            , content: 'ot_staff_company.php'
            , btn: ['确定', '取消']
            , success: function (layero) {
                layero.find('.layui-layer-btn').css('text-align', 'center')
            }
            , yes: function (index) {//

                var chooses = 0;
                var val = '';
                var GName = '';
                var Field = '';
                var $table = layer.getChildFrame('.div-mcmain', index);
                $table.find('input[type=checkbox]:checked').each(function () {
                    chooses = chooses + 1;
                    val = jQuery(this).val().split("|");
                    if (chooses === 1) {
                        Field = val[0];
                        GName = val[1]
                    } else {
                        Field = Field + ',' + val[0];
                        GName = GName + ',' + val[1];
                    }
                });

                if (chooses == 0) {
                    layer.msg("该操作要求选定记录！", function () {
                    });
                    return;
                }

                jQuery("#company").val(GName);
                jQuery("#Forshort").val(Field);
                layer.close(index);
            }


        })

    }


</script>