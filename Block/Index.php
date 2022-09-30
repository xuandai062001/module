<?php
namespace Mageplaza\HelloWorld\Block;
class Index extends \Magento\Framework\View\Element\Template
{
    protected $_helper;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = [],
        \Mageplaza\HelloWorld\Helper\Data $helper
    ) {
        parent::__construct($context, $data);
    
        $this->_helper = $helper;
    }


    public function showUrlMedia(){
        return $this->_helper->getBaseUrlMedia();
    }

    public function showImage(){
        return $this->_helper->getGeneralConfig('image');
    }
    
    public function showTextField(){
            return $this->_helper->getGeneralConfig('display_text');
        }
        
    public function showTextarea(){
        return $this->_helper->getGeneralConfig('display_textarea');
    }


}