<html>
<head>
<?php 
include "../model/characterset.php";
?>
<title>无标题文档</title>
<style type="text/css">
<!--
.style1 {color: #FF0000}
-->
</style>
</head>

<body>
<p class="style1">配件需求单还原：</p>
<p>还原的原因：</p>
<p>1、因订单取消，需要删除配件需求单（可以直接还原，领退料记录在取消订单时已清除）</p>
<p>2、因改变供应商，需要还原</p>
<p>还原的条件：</p>
<p>1、没有领料记录，没有入库记录，否则不能直接还原，需先处理领、退料、退换和补仓以及入库记录</p>
<p class="style1">配件需求单拆分：</p>
<p>拆分原因：</p>
<p>1、同一张需求单需向不同供应商采购</p>
<p>拆分条件：</p>
<p>1、入库、领料的数量不能大于拆分后原单号的数量</p>
<p>例如：原需求单号030207010401 采购数量是5000</p>
<p>现将030207010401拆分为030207010401/3000PCS和030207010402/2000PCS</p>
<p>则030207010401的实际入库数量不能大于3000PCS（多出的部分需退货）</p>
<p>&nbsp;</p>
</body>
</html>
