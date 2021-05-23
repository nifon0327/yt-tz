<?php
	
	$path = $_SERVER["DOCUMENT_ROOT"];
	include_once("$path/ipdAPI/webClass/prototype/AddFunctionProduct.php");
	
	class AddSingleStaffBxProduct implements AddFunctionProduct
	{
		private $tableWidth;
		private $funFrom;
		private $addMsgProduct;
		
		public function setupInfomation($width, $fun)
		{
			$this->tableWidth = $width;
			$this->funFrom = $fun;
		}

		public function getProperties()
		{
			$this->addMsgProduct = "<table border='0' width='".$this->tableWidth."' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF'><tr><td class='A0011'>
	<table width='600' border='0' align='center' cellspacing='5'>
	      <tr>
            <td height='9' align='right'>起始日期：</td>
            <td width='520'><input name='StartDate' type='text' id='StartDate' value='".date('Y-m-d')."' size='38' maxlength='10' dataType='Date' formqt='ymd' Msg='未填或格式不对'></td>
          </tr>
          <tr>
            <td height='13' align='right'>时间：</td>
            <td><input name='StartTime' type='text' id='StartTime' value='08:00' size='38' maxlength='5' dataType='Time' Msg='未填写或格式不对'></td>
          </tr>
          <tr>
            <td height='9' align='right'>结束日期：</td>
            <td><input name='EndDate' type='text' id='EndDate' value='".date('Y-m-d')."' size='38' onblur='checkBxtime();'  maxlength='10' dataType='Date' formqt='ymd' Msg='未填或格式不对'></td>
          </tr>
          <tr>
            <td height='13' align='right'>时间：</td>
            <td><input name='EndTime' type='text' id='EndTime' value='17:00' size='38' onblur='checkBxtime();' maxlength='5' dataType='Time' Msg='未填写或格式不对'></td>
          </tr>
          <tr>
            <td height='13' align='right'>直落时间：</td>
            <td><input name='zlHours' type='text' id='zlHours' value='0.0' size='38' maxlength='5'></td>
          </tr>
           <tr>
            <td height='13' align='right'>计算方式：</td>
            <td><select id='CalculateType' name='CalculateType'>
	            <option value='0' selected>按小时计算</option>
	            <option value='1'>按天计算</option>
            </select></td>
          </tr>
          <tr>
            <td height='37' align='right' valign='top'>补休原因：</td>
            <td><textarea name='note' cols='50' rows='5' id='note' dataType='Require' Msg='未填写请假原因'></textarea></td>
          </tr>
          <tr>
            <td height='37' align='right' valign='top'><div align='right'>
              <input name='uType' type='hidden' id='uType'>
            注意事项：</div></td>
            <td>补休时间以0.5小时为单位，向上取整。如实际请假时间4.1小时，将计为4小时。<br><font color='red'>请在结束时间后24小时内申请补休</font></td>
          </tr>
      </table>
</td></tr></table>";
			
			return $this->addMsgProduct;
		}
		
	}
	
?>