<?php
	
	include_once('AddFunctionCreator.php');
	include_once('AddFunctionProduct.php');
	
	class AddFunctionFactory extends AddFunctionCreator
	{
		private $addProduct;
		
		protected function factoryMethod(AddFunctionProduct $product)
		{
			$this->addProduct = $product;
			return $this->addProduct->getProperties();
		}
		
	}
?>