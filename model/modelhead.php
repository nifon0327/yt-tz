<?php
if (!isset($ipadTag)) $ipadTag = "no";
if ($ipadTag != "yes") {
    include "../basic/chksession.php";
}
include "../basic/parameter.inc";
include "../basic/config.inc";
include "../model/modelfunction.php";
echo "<html><head><META content='MSHTML 6.00.2900.2722' name=GENERATOR>

<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
<link rel='stylesheet' href='../model/css/read_line.css'>
<link rel='stylesheet' href='../model/css/sharing.css'>
<link rel='stylesheet' href='../model/Totalsharing.css'>
<link rel='stylesheet' href='../model/keyright.css'>
<link rel='stylesheet' href='../model/SearchDiv.css'>
<link rel='stylesheet' href='../model/scrollbar.css'>
<script src='../model/jquery_corners/jquery-1.8.3.js'></script>
<script src='../plugins/layer/layer.js'></script>

<style>
/*主表浮动及组件样式*/
.div-mcmain{
    position: relative;
    top: 15px;
    left: 30px;
    -webkit-box-shadow: #e6e7e9 0px 0px 10px;
    -moz-box-shadow: #e6e7e9 0px 0px 10px;
    box-shadow: #e6e7e9 0px 0px 10px;
}
.div-select{
    position: relative;
    top: 15px;
    left: 30px;
}
.div-select select{
    border: 0;
    color: #949598;
    background-color: #F5F5F5;
    font:12px 思源雅黑;
}
.div-select textarea{
    color: #949598;
}
/*按钮样式*/
.btn-confirm{
    font: 14px 思源雅黑;
    color: #fff!important;
    background-color: #48bbcb;
    border-radius: 2px;
    height: 28px;
    line-height: 28px;
    width: 60px;
    padding-left: 8px;
    padding-right: 8px;
    text-align:center;
    display:inline-block;
}
.btn-confirm:hover{
    background-color: #3FA8BB;
    cursor : pointer;
}
.btn-cancel{
    font: 14px 思源雅黑;
    color: #fff!important;
    background-color: #949599;
    border-radius: 2px;
    height: 28px;
    line-height: 28px;
    width: 60px;
    padding-left: 8px;
    padding-right: 8px;
    text-align:center;
}
.btn-cancel:hover{
    background-color: #7B7C7E;
    cursor : pointer;
}

/*表格线段样式 */
.table-border-grey td{
    border: 1px solid #e7e7e7;
    
}
/*table字体大小*/
table tr td{
  font:12px 思源雅黑;
}
.demo{min-width:360px;margin:10px auto;padding:10px 20px}
.demo h3{line-height:40px; font-weight: bold;}
.file-item{float: left; position: relative; width: 110px;height: 110px; margin: 0 20px 20px 0; padding: 4px;}
.file-item .info{overflow: hidden;}
.uploader-list{width: 100%; overflow: hidden;}
input{height: auto}
</style>
<script src='../model/pagefun.js' type=text/javascript></script>
<script src='../model/checkform.js' type=text/javascript></script>
<script src='../model/lookup.js' type=text/javascript></script>
<script language='javascript' type='text/javascript' src='../model/js/float_headtable.js'></script>
<script language='javascript' type='text/javascript' src='../model/DatePicker/WdatePicker.js'></script></head>";
?>
