<?php
namespace Mageplaza\HelloWorld\Observer; 
use Psr\Log\LoggerInterface; 
use Magento\Framework\Event\ObserverInterface;
use Magento\InventorySalesAdminUi\Model\GetSalableQuantityDataBySku; 
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
class ProductdataObserver implements ObserverInterface
{
    protected $_logger;
 
    public function __construct(
        \Psr\Log\LoggerInterface $logger,GetSalableQuantityDataBySku $getSalableQuantityDataBySku, ProductRepositoryInterface $productRepository
    )
    {        
        $this->_logger = $logger;
        $this->getSalableQuantityDataBySku = $getSalableQuantityDataBySku;
        $this->productRepository = $productRepository;
    }
    
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $product = $observer->getProduct();
        $productId = $product->getId();
        $productSku = $product->getSku();
        $productStatus = $product->getStatus();
        
        
        $stockItem = $product->getExtensionAttributes()->getStockItem();
        $stockData = $stockItem->getQty();
        $stockStatus = $stockItem->getIsInStock();
        

        $salable = $this->getSalableQuantityDataBySku->execute($productSku);
        $this->_logger->debug('====================MY LOG=======================');
        $this->_logger->debug('Product Id ' . $productId); 
        $this->_logger->debug('Product Sku ' . $productSku); 
        $this->_logger->debug('Product Status ' . $productStatus);
        $this->_logger->debug('Product quantity: ' . $stockData);
        $this->_logger->debug('Product stock status: ' . $stockStatus);
        $this->_logger->debug('Product sablable quantity: ' . $salable[0]['qty']);
        
        if($stockData > 0 && $stockStatus == 1 && $productStatus == 2){
            // $product = $this->productRepository->get($productSku);
            // $product->setStatus(Status::STATUS_ENABLED);
            // $this->productRepository->save($product);
            // $this->_logger->debug('ENABLED SUCCESSFULLY');


            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $storeManager = $objectManager->create('\Magento\Store\Model\StoreManagerInterface');
            $storeIds = array_keys($storeManager->getStores());
            $action = $objectManager->create('\Magento\Catalog\Model\ResourceModel\Product\Action');
            $updateAttributes['status'] = 1;
            $productCollectionFactory = $objectManager->get('\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');
            $collection = $productCollectionFactory->create();
            $collection->addAttributeToFilter('sku', $productSku);
            $collection->addAttributeToSelect('*');
            foreach ($collection as $product) 
            {
                foreach ($storeIds as $storeId) {
                    $action->updateAttributes([$product->getId()], $updateAttributes, $storeId);
                }
            }
        }
    }
}