<?php
	
	abstract class AddFunctionCreator
	{
		protected abstract function factoryMethod(AddFunctionProduct $product);
		
		public function doFactory($productNow)
		{
			$addProduct = $productNow;
			$addMessge = $this->factoryMethod($addProduct);
			return  $addMessge;
		}
		
	}
	
?>