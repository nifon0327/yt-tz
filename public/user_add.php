<?php
//电信-EWEN
//代码共享-EWEN
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增用户");//需处理
$nowWebPage = $funFrom . "_add";
$toWebPage = $funFrom . "_save";
$_SESSION["nowWebPage"] = $nowWebPage;
$Parameter = "fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,pNumber,,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth = 850;
$tableMenuS = 500;
include "../model/subprogram/add_model_t.php";
?>
    <SCRIPT type="text/javascript">

        //窗口打开方式修改为兼容性的模态框 by ckt 2017-12-23
        function SearchData(fSearchPage, SearchNum, Action) {//来源页面，可取记录数，动作（因共用故以参数区别）
            var uType = document.getElementById('uType').value;
            var SafariReturnValue = document.getElementById('SafariReturnValue');
            if (!arguments[3]) {
                var num = Math.random();
                SafariReturnValue.value = "";
                SafariReturnValue.callback = 'SearchData("","","",true)';
                var tSearchPage = '';
                switch (uType) {
                    case "1"://员工
                        tSearchPage = "staff";
                        break;
                    case "4"://外部人员员工
                        tSearchPage = "ot_staff";
                        break;
                    default://客户/供应商
                        tSearchPage = "linkman";
                        break;
                }
                url = "/public/"+tSearchPage + "_s1.php?r=" + num + "&uType=" + uType + "&Action=" + Action + "&tSearchPage=" + tSearchPage + "&fSearchPage=" + fSearchPage + "&SearchNum=" + SearchNum;
                openFrame(url, 980, 650);//url需为绝对路径
                return false;
            }
            if (SafariReturnValue.value) {
                var CL = SafariReturnValue.value.split("^^");
                SafariReturnValue.value = '';
                SafariReturnValue.callback = "";
                document.form1.pNumber.value = CL[0];
                document.form1.pName.value = CL[1];
            }
        }
    </script>
    <table border="0" width="<?php echo $tableWidth ?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
        <tr>
            <td class="A0011">
                <table width="595" height="210" border="0" align="center" cellspacing="5" id="NoteTable">
                    <tr>
                        <td height="10" align="right">用户类型</td>
                        <td><?php
                            include "../model/subselect/userType.php";
                            ?></td>
                    </tr>
                    <tr>
                        <td height="10" align="right">用户角色</td>
                        <td>
                            <select name="roleId" id="roleId" style="width:380px" dataType="Require" msg="未选择角色">
                                <option value="" selected>请选择</option>
                            <?php
                            $cResult = mysql_query("SELECT name,id FROM $DataIn.ac_roles  order by Id", $link_id);
                            if ($cRow = mysql_fetch_array($cResult)) {
                                do {
                                    echo "<option value='$cRow[id]'>$cRow[name]</option>";
                                } while ($cRow = mysql_fetch_array($cResult));
                            }
                            ?></td>
                    </tr>
                    <tr>
                        <td height="8" align="right">用户姓名</td>
                        <td><input name="pName" type="text" id="pName" style="width:380px" maxlength="16"
                                   onclick="SearchData('<?php echo $funFrom ?>',1,1)" title="必选项，点击在查询窗口选取"
                                   DataType="Require" Msg="没有选取用户姓名" readonly></td>
                    </tr>
                    <tr>
                        <td height="12" align="right">登 录 名</td>
                        <td><input name="uName" type="text" id="uName" style="width:380px" maxlength="16"
                                   title="必选项,英文、下划线、数字的组合字串" DataType="Username" Msg="不符合规定"></td>
                    </tr>
                    <tr>
                        <td height="8" align="right">登录密码</td>
                        <td><input name="uPwd" type="password" id="uPwd" style="width:380px" maxlength="16"></td>
                    </tr>
                    <tr>
                        <td height="12" align="right">确认密码</td>
                        <td><input name="Repassword" type="password" id="Repassword" style="width:380px" maxlength="16">
                        </td>
                    </tr>
                    <tr>
                        <td height="8" align="right">印章图片</td>
                        <td><input name="Attached" type="file" id="Attached" style="width:380px" dataType="Filter"
                                   msg="请选择gif图片" accept="gif" Row="5" Cel="1"></td>
                    </tr>
                    <tr>
                        <td height="0" align="right"><input name="uSign" type="checkbox" id="uSign" value="1" checked>
                        </td>
                        <td>用户可以更改密码</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
<?php
//步骤5：
include "../model/subprogram/add_model_b.php";
?>