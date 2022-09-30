<?php 
namespace Mageplaza\HelloWorld\Observer; 
use Magento\Framework\Event\ObserverInterface; 
use Psr\Log\LoggerInterface; 
use Magento\InventorySalesAdminUi\Model\GetSalableQuantityDataBySku; 
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
class OrderdataObserver implements ObserverInterface 
{ 
    protected $_logger;
    private $getSalableQuantityDataBySku;
 
    public function __construct(
        \Psr\Log\LoggerInterface $logger, GetSalableQuantityDataBySku $getSalableQuantityDataBySku, ProductRepositoryInterface $productRepository
    )
    {        
        $this->_logger = $logger;
        $this->getSalableQuantityDataBySku = $getSalableQuantityDataBySku;
        $this->productRepository = $productRepository;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
        {
        
            $order = $observer->getEvent()->getOrder();
            $orderId= $order->getIncrementId();
            $this->_logger->debug('====================MY LOG=======================');
            $this->_logger->debug('Order Id' . $orderId); 
            foreach ($order->getAllVisibleItems() as $_item) {
                $this->_logger->debug('Product Id: ' . $_item->getProductId()); 
                $this->_logger->debug('Product Sku: ' . $_item->getSku());
                $this->_logger->debug('Product Name: ' . $_item->getName());
                $this->_logger->debug('Product Type: ' . $_item->getProductType());
                $this->_logger->debug('Product Quantity: ' . $_item->getQtyOrdered());
                $salable = $this->getSalableQuantityDataBySku->execute($_item->getSku());
                $this->_logger->debug('Product sablable quantity: ' . $salable[0]['qty']);
                $currentSalable = $salable[0]['qty'] - $_item->getQtyOrdered();
                $this->_logger->debug('Current product sablable quantity: ' .$currentSalable);
                if($currentSalable == 0){
                    // $product = $this->productRepository->get($_item->getSku());
                    // $product->setStatus(Status::STATUS_DISABLED);
                    // $this->_logger->debug('DISABLED SUCCESSFULLY');
                    // $this->productRepository->save($product);


                    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                    $storeManager = $objectManager->create('\Magento\Store\Model\StoreManagerInterface');
                    $storeIds = array_keys($storeManager->getStores());
                    $action = $objectManager->create('\Magento\Catalog\Model\ResourceModel\Product\Action');
                    $updateAttributes['status'] = 2;
                    $productCollectionFactory = $objectManager->get('\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');
                    $collection = $productCollectionFactory->create();
                    $collection->addAttributeToFilter('sku', $_item->getSku());
                    $collection->addAttributeToSelect('*');
                    foreach ($collection as $product) 
                    {
                        foreach ($storeIds as $storeId) {
                            $action->updateAttributes([$product->getId()], $updateAttributes, $storeId);
                        }
                    }
                }
                $this->_logger->debug('--------------------------------------------------');
            }
        }
} 