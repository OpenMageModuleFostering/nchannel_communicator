<?php
class Nchannel_Communicator_Model_Productutility_Api_V2 extends Nchannel_Communicator_Model_Productutility_Api
{
	public function linkConfigurable($configurableProductID)
	{
		Mage::log("LinkConfigurable fired!" . $url, null, 'nChannel_Communicator.log');
		return "Hello World! My argument is : " . $configurableProductID;
	}
	public function attachProductToConfigurable( $childSku, $configurableProductID ) {
		Mage::log('sku ' . $childSku,null,'nChannel_Communicator.log');
		Mage::log('pid ' . $configurableProductID,null,'nChannel_Communicator.log');
		$configurableProduct = Mage::getModel('catalog/product')->load($configurableProductID);
		$loader = Mage::getResourceModel( 'catalog/product_type_configurable' )->load($configurableProduct,$configurableProductID);
		$product = Mage::getModel('catalog/product')->loadByAttribute('sku',$childSku);
		Mage::log('child product: ' . print_r($product,true));
		Mage::log($product->getSku(),null,'nChannel_Communicator.log');
		//return print_r($configurableProduct,true);
		//$ids = $configurableProduct->getTypeInstance(true)->getUsedProductIds();
		$ids = Mage::getModel('catalog/product_type_configurable')->getChildrenIds($configurableProductID);
		Mage::log('$ids ' . print_r($ids,true));
		//$ids = $configurableProduct->getUsedProductIds();
		$newids = array();
		//Mage::log('ids: ' . print_r($ids,true),null,'nChannel_Communicator.log');
		Mage::log('Loop IDs',null,'nChannel_Communicator.log');
		Mage::log('product id output: ' . $product->getId(),null,'nChannel_Communicator.log');
		
		Mage::log('Adding Product: '.$childSku,null,'nChannel_Communicator.log');
		
		foreach ( $ids as $arr ) {
			foreach($arr as $id)
				{
				Mage::log('id output: ' . $id,null,'nChannel_Communicator.log');
				
				$newids[$id] = 1;

					}
		}

		$newids[$product->getId()] = 1;

		try{
		Mage::log('Saving Products', null, 'nChannel_Communicator.log');
		$loader->saveProducts( $configurableProduct, array_keys( $newids ) );
		} catch(exception $ex)
		{
			array_pop($newids);
			$loader->saveProducts( $configurableProduct, array_keys( $newids ) );
			Mage::log($ex->getMessage(),null,'nChannel_Communicator.log');	
		}
		Mage::log('Product '.$childSku.' was added', null, 'nChannel_Communicator.log');
		Mage::log('Complete', null, 'nChannel_Communicator.log');
		return "Success!";
	}
	
	public function detachProductFromConfigurable( $childSku, $configurableProductID ) {
		Mage::log('sku ' . $childSku,null,'nChannel_Communicator.log');
		Mage::log('pid ' . $configurableProductID,null,'nChannel_Communicator.log');
		$configurableProduct = Mage::getModel('catalog/product')->load($configurableProductID);
		$loader = Mage::getResourceModel( 'catalog/product_type_configurable' )->load($configurableProduct,$configurableProductID);
		$product = Mage::getModel('catalog/product')->loadByAttribute('sku',$childSku);
		$productid = $product->getId();
		Mage::log('child product: ' . print_r($product,true));
		Mage::log($product->getSku(),null,'nChannel_Communicator.log');
		//return print_r($configurableProduct,true);
		//$ids = $configurableProduct->getTypeInstance(true)->getUsedProductIds();
		$ids = Mage::getModel('catalog/product_type_configurable')->getChildrenIds($configurableProductID);
		Mage::log('$ids ' . print_r($ids,true));
		//$ids = $configurableProduct->getUsedProductIds();
		$newids = array();
		//Mage::log('ids: ' . print_r($ids,true),null,'nChannel_Communicator.log');
		Mage::log('Loop IDs',null,'nChannel_Communicator.log');
		Mage::log('product id output: ' . $productid,null,'nChannel_Communicator.log');
				
		Mage::log('Removing Product: '.$childSku,null,'nChannel_Communicator.log');
		foreach ( $ids as $arr ) {
			foreach($arr as $id) {
				
				Mage::log('id output: ' . $id,null,'nChannel_Communicator.log');
				
				if ($productid != $id)
				{
					$newids[$id] = 1;
				}
			}
		}
		
		try{
		Mage::log('Saving Products', null, 'nChannel_Communicator.log');
		$loader->saveProducts( $configurableProduct, array_keys( $newids ) );
		} catch(exception $ex)
		{
			array_pop($newids);
			$loader->saveProducts( $configurableProduct, array_keys( $newids ) );
			Mage::log($ex->getMessage(),null,'nChannel_Communicator.log');	
		}

		Mage::log('Product '.$childSku.' was removed', null, 'nChannel_Communicator.log');
		Mage::log('Complete', null, 'nChannel_Communicator.log');
		
		//return "Success!";
	}
	
	public function addAttributes($sku , $configAttrCodes)
	{
		/*$product = new Mage_Catalog_Model_Product();
		$product->setSku($sku);
		$product->setAttributeSetId('9');
		$product->setTaxClassId(0);
		$product->setStatus(1);
		$product->setTypeID('configurable');
		$product->setWebsiteIds(array(1));
		$product->setName('testing1234');
		$product->setDescription('desc');
		$product->setShortDescription('short');
		$product->setPrice('9.99');
		$product->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH);
		try    {
			$product->save();
			Mage::log("Saved",null,'nChannel_Communicator.log');
		}
		catch (Exception $e){         
			Mage::log("exception:$e",null,'nChannel_Communicator.log');
		}*/
		
		Mage::log("Created Product",null,'nChannel_Communicator.log');
		$configProduct = Mage::getModel('catalog/product')->loadByAttribute('sku',$sku);
		
		foreach($configAttrCodes as $attrCode){
			Mage::log("Setting up attr " . $attrCode,null,'nChannel_Communicator.log');
			$super_attribute= Mage::getModel('eav/entity_attribute')->load( $attrCode, 'attribute_code');

			Mage::log('super ID = ' . $super_attribute->getId(),null,'nChannel_Communicator.log');
			Mage::log('super label = ' . $super_attribute->getFrontend()->getLabel(),null,'nChannel_Communicator.log');

			
			$configurableAtt = Mage::getModel('catalog/product_type_configurable_attribute')->setProductAttribute($super_attribute);
			Mage::log('configurable label = ' . $configurableAtt->getLabel(),null,'nChannel_Communicator.log');
			$newAttributes[] = array(
				'id'             => null,
				'label'          => $configurableAtt->getLabel(),
				'position'       => $super_attribute->getPosition(),
				'values'         => $configurableAtt->getPrices() ? $configProduct->getPrices() : array(),
				'attribute_id'   => $super_attribute->getId(),
				'attribute_code' => $super_attribute->getAttributeCode(),
				'frontend_label' => $super_attribute->getFrontend()->getLabel(),
				);
		}
		$existingAtt = $configProduct->getTypeInstance()->getConfigurableAttributes();
		Mage::log('Count: ' . count($newAttributes),null,'nChannel_Communicator.log');
		Mage::log('Count: ' . count($existingAtt),null,'nChannel_Communicator.log');
		foreach($newAttributes as $att)
		{
			Mage::log('attr id = ' . $att['attribute_code'],null,'nChannel_Communicator.log');
			Mage::log('attr = ' . print_r($att),null,'nChannel_Communicator.log');
			
			}
		if(count($existingAtt) == 0 && count($newAttributes) > 0){
			Mage::log("Adding attrs ",null,'nChannel_Communicator.log');
			$configProduct->setCanSaveConfigurableAttributes(true);
			$configProduct->setConfigurableAttributesData($newAttributes);
			$configProduct->save();
	     }
		Mage::log("complete",null,'nChannel_Communicator.log');
		Mage::log($configProduct->getId(),null,'nChannel_Communicator.log');
		return $configProduct->getId();
		
	}
	
	public function loadSimpleProductsFromConfigurable($configurableProductSKU)
	{
		$product = Mage::getModel('catalog/product')->loadByAttribute('sku', $configurableProductSKU);
		$ids = Mage::getModel('catalog/product_type_configurable')->getChildrenIds($product->getId());
		Mage::log('$ids ' . print_r($ids,true),null,'nChannel_Communicator.log');
		
		foreach ( $ids as $arr ) {
			foreach($arr as $id)
			{
				$row = array();
				Mage::log('id output: ' . $id,null,'nChannel_Communicator.log');
				
				$row = Mage::getModel('catalog/product')->load($id)->getSku();
				$result[] = $row;
			}
		}
		return $result;
	}
	
	public function productLastModified($storeView, $date)
	{		
		$products = Mage::getModel('catalog/product')->getCollection()->addStoreFilter($storeView)->addFieldToFilter('updated_at', array("gt" => $date));
		
		$result = array();
		foreach ($products as $product)
		{
			$row = array();
			Mage::log('id output: ' . print_r($product->getSku()),'nChannel_Communicator.log');
			
			$row = $product->getSku();
			$result[] = $row;
		}
		
		return $result;
	}
}
?>