<?php

class Nchannel_Communicator_Model_Api2_Stock_Item_Rest_Admin_V1 extends Mage_CatalogInventory_Model_Api2_Stock_Item
{
    /**
     * Load stock item by id
     *
     * @param string $sku
     * @throws Mage_Api2_Exception
     * @return Mage_CatalogInventory_Model_Stock_Item
     */
    protected function _update(array $data)
    {	
        /* @var $stockItem Mage_CatalogInventory_Model_Stock_Item */		

        $productId = Mage::getModel('catalog/product')->getIdBySku($data['sku']);
		$stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($productId);


        /* @var $validator Mage_CatalogInventory_Model_Api2_Stock_Item_Validator_Item */
        $validator = Mage::getModel('cataloginventory/api2_stock_item_validator_item', array(
            'resource' => $this
        ));

        if (!$validator->isValidData($data)) {
            foreach ($validator->getErrors() as $error) {
                $this->_error($error, Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
            }
            $this->_critical(self::RESOURCE_DATA_PRE_VALIDATION_ERROR);
        }

		// Quantity
		if ($data['qty'] != null && $data['qty'] != '')
		{
			$stockItem->setData('qty', $data['qty']);
			Mage::log('Qty: '.$data['qty'],null,'nChannel_Communicator.log');
		}
		
		// Reorder Point
		if ($data['notify_stock_qty'] != null && $data['notify_stock_qty'] != '')
		{
			$stockItem->setData('notify_stock_qty', $data['notify_stock_qty']);
			Mage::log('Notify_Stock_Qty: '.$data['notify_stock_qty'],null,'nChannel_Communicator.log');
		}
		
		// Restock Level
		if ($data['min_qty'] != null && $data['min_qty'] != '')
		{
			$stockItem->setData('min_qty', $data['min_qty']);
			Mage::log('Min_Qty: '.$data['min_qty'],null,'nChannel_Communicator.log');
		}
		
		if ($data['price'] != null && $data['price'] != '')
		{
			//$product->setPrice($data['price']);
			Mage::getSingleton('catalog/product_action')
    			->updateAttributes(array($productId), array('price' => $data['price']), 0);
			Mage::log('Price: '.$data['price'],null,'nChannel_Communicator.log');
		}
		
        try {
            $stockItem->save();
        } catch (Mage_Core_Exception $e) {
			Mage::log($e->getMessage(),null,'nChannel_Communicator.log');
            $this->_error($e->getMessage(), Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
        } catch (Exception $e) {
			Mage::log($e->getMessage(),null,'nChannel_Communicator.log');
            $this->_critical(self::RESOURCE_INTERNAL_ERROR);
        }
    }
	
	protected function _multiUpdate(array $data)
    {
        foreach ($data as $itemData) {
            try {			
                if (!is_array($itemData)) {
                    $this->_errorMessage(self::RESOURCE_DATA_INVALID, Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
                    $this->_critical(self::RESOURCE_DATA_PRE_VALIDATION_ERROR);
                }

				$product = Mage::getModel('catalog/product')->loadByAttribute('sku', $itemData['sku']);
				$stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);

                unset($itemData['item_id']); // item_id is not for update
				
				// Quantity
				if ($itemData['qty'] != null && $itemData['qty'] != '')
				{
					$stockItem->setData('qty', $itemData['qty']);
					Mage::log($itemData['qty'],null,'nChannel_Communicator.log');
				}
				
				// Reorder Point
				if ($itemData['notify_stock_qty'] != null && $itemData['notify_stock_qty'] != '')
				{
					$stockItem->setData('notify_stock_qty', $itemData['notify_stock_qty']);
					Mage::log($itemData['notify_stock_qty'],null,'nChannel_Communicator.log');
				}
				
				// Restock Level
				if ($itemData['min_qty'] != null && $itemData['min_qty'] != '')
				{
					$stockItem->setData('min_qty', $itemData['min_qty']);
					Mage::log($itemData['min_qty'],null,'nChannel_Communicator.log');
				}
				
                $stockItem->save();

                $this->_successMessage(self::RESOURCE_UPDATED_SUCCESSFUL, Mage_Api2_Model_Server::HTTP_OK, array(
                    'item_id' => $stockItem->getId()
                ));
            } catch (Mage_Api2_Exception $e) {
                // pre-validation errors are already added
                if ($e->getMessage() != self::RESOURCE_DATA_PRE_VALIDATION_ERROR) {
                    $this->_errorMessage($e->getMessage(), $e->getCode(), array(
                        'item_id' => isset($itemData['item_id']) ? $itemData['item_id'] : null
                    ));
                }
            } catch (Exception $e) {
                $this->_errorMessage(
                    Mage_Api2_Model_Resource::RESOURCE_INTERNAL_ERROR,
                    Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR,
                    array(
                        'item_id' => isset($itemData['item_id']) ? $itemData['item_id'] : null
                    )
                );
            }
        }
    }
	
	protected function _retrieve()
	{
		return 'retrieve';
	}
}