<?php
class Nchannel_Communicator_Model_Productutility_Api_V2 extends Nchannel_Communicator_Model_Productutility_Api
{
	public function attachProductToConfigurable( $childSku, $configurableProductID ) {

            Mage::log('attachProductToConfigurable: sku ' . $childSku, null, 'nChannel_Communicator.log');
            Mage::log('attachProductToConfigurable: pid ' . $configurableProductID, null, 'nChannel_Communicator.log');
            $model = Mage::getModel('catalog/product');
            $configurableProduct = $model->load($configurableProductID);
            Mage::log('attachProductToConfigurable: Configurable Product ID: ' . print_r($configurableProduct->getId(), true), null, 'nChannel_Communicator.log');
            $newModel = Mage::getModel('catalog/product');
            $loader = Mage::getResourceModel('catalog/product_type_configurable')->load($newModel, $configurableProductID);
            $product = Mage::getModel('catalog/product')->loadByAttribute('sku', $childSku);

            $ids = Mage::getModel('catalog/product_type_configurable')->getChildrenIds($configurableProductID);
            $newids = array();
            Mage::log('attachProductToConfigurable: current child product id output: ' . $product->getId(), null, 'nChannel_Communicator.log');

            Mage::log('attachProductToConfigurable: Loop existing IDs', null, 'nChannel_Communicator.log');
            foreach ($ids as $arr) {
                foreach ($arr as $id) {
                    Mage::log('attachProductToConfigurable: existing id output: ' . $id, null, 'nChannel_Communicator.log');
                    $newids[$id] = 1;
                }
            }
            $newids[$product->getId()] = 1;
        try {
            try {
                Mage::log('attachProductToConfigurable: Saving Products', null, 'nChannel_Communicator.log');
                Mage::log('attachProductToConfigurable: Configurable Product ID: ' . print_r($configurableProduct->getId(), true), null, 'nChannel_Communicator.log');
                $loader->saveProducts($configurableProduct, array_keys($newids));
            } catch (exception $ex) {
                array_pop($newids);
                $loader->saveProducts($test, array_keys($newids));
                Mage::log('attachProductToConfigurable: erorr ->' . $ex->getMessage(), null, 'nChannel_Communicator.log');
            }
            Mage::log('attachProductToConfigurable: Product ' . $childSku . ' was added', null, 'nChannel_Communicator.log');
            Mage::log('attachProductToConfigurable: Complete', null, 'nChannel_Communicator.log');
            return "Success!";
        }
        catch (exception $ex)
        {
            Mage::log('attachProductToConfigurable: error =->' . $ex->getMessage(), null, 'nChannel_Communicator.log');
        }
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
	
	public function getConfigurableAttribute($configurableProductSKU)
	{
		$product = Mage::getModel('catalog/product')->loadByAttribute('sku',$configurableProductSKU);
		$attrs = $product->getTypeInstance(true)->getConfigurableAttributesAsArray($product);
		Mage::log($attrs[0]['label'],null,'nChannel_Communicator.log');
		
		return $attrs[0]['label'];
		// foreach ( $attrs as $attr )
		// {
				// Mage::log('id output: ' . $attr['label'],null,'nChannel_Communicator.log');
				
				// $result[] = $attr['label'];
		// }
		// return $result;
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
	
	public function getCustomerGroups()
	{
		$collection = Mage::getModel('customer/group')->getCollection();

        $result = array();
        foreach ($collection as $group)
		{
            $result[] = $group->toArray(array('customer_group_id', 'customer_group_code'));
        }

        return $result;
	}
	
	public function addCustomerGroup($customer)
	{
		$collection = Mage::getModel('customer/group')->getCollection()->addFieldToFilter('customer_group_code', $customer);
        $customerGroup = Mage::getModel('customer/group')->load($collection->getFirstItem()->getId());
		
		if ($customerGroup != null)
		{
			Mage::log('Customer Group Exists. Updating.', null, 'nChannel_Communicator.log');
		}
		else
		{
			Mage::log('Adding new Customer Group.', null, 'nChannel_Communicator.log');
		}
		$customerGroup->setCode($customer);
		$customerGroup->setTaxClassId(3);
		$customerGroup->save();
		
		return $customerGroup['customer_group_id'];
	}
	
	public function addGroupPrice($storeView, $customer, $price, $sku)
	{
		$product = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);
		$customerGroupId = $this->addCustomerGroup($customer);
        $groupPrices = $product->getData('group_price');

		if (is_null($groupPrices)) {
			$attribute = $product->getResource()->getAttribute('group_price');
			if($attribute) {
				$attribute->getBackend()->afterLoad($product);
				$groupPrices = $product->getData('group_price');
			}
		}
		
		$result = array();
		$existing = false;

        foreach ($groupPrices as $groupPrice) {
            $row = array();
            $row['cust_group'] = (empty($groupPrice['all_groups']) ? $groupPrice['cust_group'] : 'all' );
            $row['website_id']           = ($groupPrice['website_id'] ?
                            Mage::app()->getWebsite($groupPrice['website_id'])->getId() :
                            'all'
                    );
            $row['price']             = $groupPrice['price'];
			
			if ($groupPrice['cust_group'] == $customerGroupId && $groupPrice['website_id'] = $storeView)
			{
				$row['price']             = $groupPrice['price'];
				$existing = true;
				Mage::log('Found existing Group Price.', null, 'nChannel_Communicator.log');
			}

            $result[] = $row;
        }
		
		if (!$existing)
		{
			$row = array(array('cust_group' => $customerGroupId, 'website_id' => (empty($groupPrice['all_groups']) ? $storeView : 'all' ), 'price' => $price));

            $result = array_merge($result, $row);
			Mage::log('Adding new Group Price.', null, 'nChannel_Communicator.log');			
		}
		
		$product->setData('group_price', $result);
		$product->save();

        return $result;
	}
}
?>