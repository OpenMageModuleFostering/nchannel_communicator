<?php

    class Nchannel_Communicator_Helper_Data extends Mage_Core_Helper_Abstract{
     
		public function getExtensionVersion()
		{
			return (string) Mage::getConfig()->getNode()->modules->Nchannel_Communicator->version;
		}
    }
