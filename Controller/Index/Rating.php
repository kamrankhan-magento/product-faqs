<?php
namespace FME\Prodfaqs\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use FME\Prodfaqs\Model\Faqs as FaqsModel;

class Rating extends \Magento\Framework\App\Action\Action
{
    
    protected $jsonFactory;
        
    protected $faqsModel;
        

    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        FaqsModel $faqsModel
    ) {
            
        $this->jsonFactory = $jsonFactory;
        $this->faqsModel = $faqsModel;
                
        parent::__construct($context);
    }
        
    public function execute()
    {
        $resultJson = $this->jsonFactory->create();
            
        $params = $this->getRequest()->getParams();
        $error = false;
        $message = '';
            
        if (isset($params['faq'])) {
            $faq_id = (int) $params['faq'];
            $star_value = (int) $params['value'];
            $faqData = $this->faqsModel->load($faq_id);
                
            $rating_num = (int) $faqData->getRatingNum();
            $rating_count = (int) $faqData->getRatingCount();
                                
            $new_rating_num = $rating_num + $star_value;
            $new_rating_count = $rating_count + 1;
            $new_rating_stars = round($new_rating_num / $new_rating_count);
                
            try {
                $newData = ['rating_num' => $new_rating_num, 'rating_count' => $new_rating_count, 'rating_stars' => $new_rating_stars];
                $faqData->setData(array_merge($faqData->getData(), $newData));
                    
                $this->faqsModel->save($faqData);
                $message = __('Thanks for your feedback.');
                    
                //set in session to restrict customer over-rating for this faq
                    
                $customerSession = $this->_objectManager->get('Magento\Customer\Model\Session');
                $faqRating = $customerSession->getFaqRating();
                $ar = explode(',', $faqRating);
                array_push($ar, $faq_id);
                    
                $ar_str = implode(',', $ar);
                    
                $customerSession->setFaqRating($ar_str);
            } catch (\Exception $ex) {
                $message = $ex->getMessage();
                $error = true;
            }
        }
            
            
        return  $resultJson->setData([
                'message' => $message,
                'stars' => $new_rating_stars,
                'error' => $error
            ]);
    }
}
