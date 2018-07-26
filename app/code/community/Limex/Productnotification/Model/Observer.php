<?php

class Limex_Productnotification_Model_Observer {
    
    public function detectProductChanges($observer)
    {
       
       $product = $observer->getEvent()->getProduct();
        if($product->hasDataChanges()) {
		
			$data = $observer->getEvent()->getData();
			$status = $product['status'];
			$getstatus = $product->getStatus();	
			$productname = $product->getName();
			$store = $product->getStoreId();
			if($getstatus == 2) {
			$this->_sendStatusMail($productname, $store);
			}
        }
	}
	
	public function detectImportProductChanges($observer)
    {       
		$adapter = $observer->getEvent()->getAdapter();
		$affectedEntityIds = $adapter->getAffectedEntityIds();
		$productnames = array();
		foreach($affectedEntityIds as $eachId)
			{
				$obj = Mage::getModel('catalog/product');
				$_product = $obj->load($eachId);		
				$store = $_product->getStoreId();
				if($_product->getStatus() == 2) { 
					$productnames[] = $_product->getName();				
				}
				
			}
				$product_all_name = implode(",",$productnames);
				$this->_sendStatusMail($product_all_name, $store);
	}
	
	
	
	
	
	private  function _sendStatusMail($product_name, $store)
    {
        $emailTemplate  = Mage::getModel('core/email_template');
 
        $emailTemplate->loadDefault('limex_productnotification_tpl');
        $emailTemplate->setTemplateSubject('Product has been disabled');
 
        // Get General email address (Admin->Configuration->General->Store Email Addresses)
        $salesData['email'] = Mage::getStoreConfig('trans_email/ident_general/email');
        $salesData['name'] = Mage::getStoreConfig('trans_email/ident_general/name'); 
        $emailTemplate->setSenderName($salesData['name']);
        $emailTemplate->setSenderEmail($salesData['email']);
 
        $emailTemplateVariables['product_name'] = $product_name;
		$emailTemplateVariables['dates'] = date("l jS \of F Y h:i:s A");
        $check = $emailTemplate->send($salesData['email'], $store, $emailTemplateVariables);
		
    }
	
	
	
	
	public function detectProductAttributesChanges($observer)
    {
        $attributesData = $observer->getEvent()->getAttributesData();
        $productIds     = $observer->getEvent()->getProductIds();		
		if($attributesData['status'] == 2) {
			foreach($productIds as $eachId)
			{
				$obj = Mage::getModel('catalog/product');
				$_product = $obj->load($eachId);		
				$store = $_product->getStoreId();
				$this->_sendStatusMail($_product->getName(), $store);
			}
		}
    }
	
	
	
}
