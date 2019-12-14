<?php
namespace FME\Prodfaqs\Controller\Index;

use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;

class Faqajax extends \Magento\Framework\App\Action\Action
{
    
    protected $resultPageFactory;
    protected $jsonFactory;

    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
         $this->jsonFactory = $jsonFactory;
        parent::__construct($context);
    }
        
    public function execute()
    {
         $resultJson = $this->jsonFactory->create();

             $params = $this->getRequest()->getParams();

       // $resultPage = $this->resultPageFactory->create();
            
        $faqsHelper = $this->_objectManager->get('FME\Prodfaqs\Helper\Data');
            
        $values[] = $params['value'];
        $values[] = $params['proid'];
        $values[] = $params['sortbyarrow'];

        $data= $this->_view->getLayout()
                     ->createBlock("FME\Prodfaqs\Block\Product\Faqs")
                     ->setTemplate("FME_Prodfaqs::faqsajax.phtml")
                     ->setData('sort_type', $values)
                     ->toHtml();


         return  $resultJson->setData([
                'Data' => $data,
                
           ]);
           
           
        //return $resultPage;
    }
}
