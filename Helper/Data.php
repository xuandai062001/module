<?php

namespace Mageplaza\HelloWorld\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Data extends AbstractHelper
{
	/**
     * @var array
     */
    protected $configModule;
    protected $_storeManager;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
           \Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {
        parent::__construct($context);
         $this->_storeManager = $storeManager;
    }

	public function getBaseUrlMedia()
    {
       return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

	const XML_PATH_HELLOWORLD = 'helloworld/';

	public function getConfigValue($field, $storeId = null)
	{
		return $this->scopeConfig->getValue(
			$field, ScopeInterface::SCOPE_STORE, $storeId
		);
	}

	public function getGeneralConfig($code, $storeId = null)
	{
		return $this->getConfigValue(self::XML_PATH_HELLOWORLD .'general/'. $code, $storeId);
	}

}