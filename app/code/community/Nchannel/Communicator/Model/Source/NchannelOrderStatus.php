<?php

class Nchannel_Communicator_Model_Source_NchannelOrderStatus
{
	public function toOptionArray($isMultiselect)
	{
		return array(
			array('value' => 'all', 'label' => Mage::helper('Communicator')->__('All')),
			array('value' => 'pending', 'label' => Mage::helper('Communicator')->__('Pending')),
			array('value' => 'pending_payment', 'label' => Mage::helper('Communicator')->__('Pending Payment')),
			array('value' => 'processing', 'label' => Mage::helper('Communicator')->__('Processing')),
			array('value' => 'holded', 'label' => Mage::helper('Communicator')->__('On Hold')),
			array('value' => 'complete', 'label' => Mage::helper('Communicator')->__('Complete')),			
			array('value' => 'closed', 'label' => Mage::helper('Communicator')->__('Closed')),
			array('value' => 'canceled', 'label' => Mage::helper('Communicator')->__('Cancelled')),
			);
	}
}

?>