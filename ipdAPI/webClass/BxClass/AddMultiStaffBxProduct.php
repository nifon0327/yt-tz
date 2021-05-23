<?php
	
	$path = $_SERVER["DOCUMENT_ROOT"];
	include_once("$path/ipdAPI/webClass/prototype/AddFunctionProduct.php");
	
	class AddMultiStaffBxProduct implements AddFunctionProduct
	{
		private $addMsgProduct;
		
		private $tableWidth;
		private $funFrom;
		
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
		<td height='18' align='right' valign='top' scope='col'>员工:</td>
		<td valign='middle' scope='col'>
		 	<select name='ListId[]' size='10' id='ListId' multiple style='width: 430px;' onclick=SearchRecord('staff','".$this->funFrom."',2,122) dataType='PreTerm' Msg='没有指定员工' readonly>
		 	</select>
		</td>
		</tr>			          
          <tr>
            <td height='9' align='right'>起始日期：</td>
            <td width='520'><input name='StartDate' type='text' id='StartDate' value='".date('Y-m-d')."' size='38' maxlength='10' onfocus='WdatePicker()' dataType='Date' formqt='ymd' Msg='未填或格式不对' readonly></td>
          </tr>
          <tr>
            <td height='13' align='right'>时间：</td>
            <td><input name='StartTime' type='text' id='StartTime' value='08:00' size='38' maxlength='5' dataType='Time' Msg='未填写或格式不对'></td>
          </tr>
          <tr>
            <td height='9' align='right'>结束日期：</td>
            <td><input name='EndDate' type='text' id='EndDate' value='".date('Y-m-d')."' size='38' maxlength='10' onfocus='WdatePicker()' dataType='Date' formqt='ymd' Msg='未填或格式不对' readonly></td>
          </tr>
          <tr>
            <td height='13' align='right'>时间：</td>
            <td><input name='EndTime' type='text' id='EndTime' value='17:00' size='38' maxlength='5' dataType='Time' Msg='未填写或格式不对'></td>
          </tr>
          <tr>
            <td height='20' align='right'>直落时间：</td>
            <td><input name='zlHours' type='text' id='zlHours' value='0.0' size='38' maxlength='5'></td>
          </tr>
           <tr>
            <td height='20' align='right'>计算方式：</td>
            <td><select id='CalculateType' name='CalculateType'>
	            <option value='0' selected>按小时计算</option>
	            <option value='1'>按天计算</option>
            </select></td>
          </tr>
          <tr>
            <td height='20' align='right'>备注：</td>
            <td><input name='note' type='text' id='note' size='38'></td>
          </tr>
           <tr>
            <td height='20' align='right'>凭证：</td>
            <td><input name='Attached' type='file' id='Attached'  DataType='Filter' Accept='jpg' Msg='文件格式不对,请重选'></td>
          </tr>
          
          
      </table>
</td></tr></table>";
			
			
			return $this->addMsgProduct;
		}
		
	}
	
	
	
?>