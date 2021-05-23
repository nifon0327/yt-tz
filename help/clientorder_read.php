<html>
<head>
<?php 
include "../model/characterset.php";
?>
<title>订单操作帮助</title>
<style type="text/css">
<!--
.style1 {color: #FF0000}
.style2 {color: #000000; }
-->
</style>
</head>

<body>
<p class="style1">◆关于订单和采购单颜色标识</p>
<table width="405" height="89" cellpadding="0" cellspacing="0">
  <tr>
    <td width="81"><div align="center">未下采单</div></td>
    <td width="77"><div align="center">已下采单</div></td>
    <td width="81"><div align="center">配件齐</div></td>
    <td width="87"><div align="center">部分领料</div></td>
    <td width="88"><div align="center">领完料</div></td>
  </tr>
  <tr>
    <td><div align="center">白色</div></td>
    <td colspan="3"><div align="center">黄色</div></td>
    <td><div align="center">绿色</div></td>
  </tr>
  <tr>
    <td><div align="center">1</div></td>
    <td colspan="3" bgcolor="#FFCC00"><div align="center"></div>      <div align="center"></div>      <div align="center"></div></td>
    <td bgcolor="#339900"><div align="center"></div></td>
  </tr>
  <tr>
    <td><div align="center">等待采购</div></td>
    <td><div align="center">采购中</div></td>
    <td><div align="center">等待加工</div></td>
    <td><div align="center">加工中</div></td>
    <td><div align="center">待出</div></td>
  </tr>
</table>
<p>&nbsp;</p>
<p class="style1">◆下订单：</p>
<p class="style2">下订单时，通过产品配件关系表来自动生成配件需求单，操作过程：</p>
<p class="style2">1、先生成主订单数据（包括内部主订单编号/PO号/客户ID/订单日期）；</p>
<p class="style2">2、产品订单明细写入数据库，并根据该产品的产品配件关系表，找出相关的配件和对应数量关系；然后根据对应关系计算配件相应的订单需求数量；</p>
<p class="style2">4、读取配件的可用库存数据，并进行对比：</p>
<p class="style2">&nbsp;&nbsp;&nbsp;&nbsp;A、如果配件可用库存为0，即没有库存可用，则配件实际需求数量与订单需求数量一致</p>
<p class="style2">&nbsp;&nbsp;&nbsp;&nbsp;B、如果0&lt;配件可用库存&lt;配件订单需求数，则使用库存数=配件可用库存；配件实际需求=配件订单需求数-配件可用库存</p>
<p class="style2">&nbsp;&nbsp;&nbsp;&nbsp;C、如果配件可用库存&gt;配件可用库存数，则全部使用库存，无须采购</p>
<p class="style2">5、根据配件/采购/供应商关系表，将配件需求单指定给采购和供应商</p>
<p class="style2">6、根据使用可用库存的情况写入新的可用库存，如果可用库存计算结果少于0，则可用库存置为0，即可用库存必须大于或等于0；大于0表示有可用可用库存，等于0表示没有可用库存。</p>
<p class="style1">◆改变订单数量</p>
<p class="style2">因客户需求对订单数量的变化：</p>
<p class="style2">1、如果是增加数量：</p>
<p class="style2">2、如果是减少数量</p>
<p class="style1">◆拆分订单</p>
<p class="style2">因客户要求或出货需求，对订单进行拆分；</p>
<p class="style2">选用方案、先改变原订单数量，然后再新增订单（代码简单但操作较复杂）</p>
<p class="style2">（备用方案、直接拆分订单，需要对已入库、已领料的记录做详细分析并拆分）</p>
<p class="style1">◆取消订单：</p>
<p>取消订单有关的数据处理：</p>
<p>配件需求单如果全部是使用库存的，则将使用的数量退回到可用库存，相关的领料退料记录自动清除，将实际领料的数量（领料总数-退料总数）退回在库。</p>
<p>配件需求单有采购的，首先会将使用库存的数量退回可用库存，使用库存清0，将采购数量计入可用库存，配件需求数量=采购数量，清空订单流水号，并将需求单的主采购单清0，转为特采单。最后自动清除领料和退料记录，并将实际领料的数量退回在库。</p>
<p class="style1">◆订单Estate标记</p>
<p>0:已出</p>
<p>1：新单</p>
<p>2：待出</p>
<p>3：加工</p>
<p>4：即出</p>
<p>9：加急</p>
</body>
</html>
