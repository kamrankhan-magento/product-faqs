<?php
namespace FME\Prodfaqs\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;

class Likes extends \Magento\Framework\App\Action\Action
{
    
    protected $jsonFactory;
        
    protected $_answersModel;
        

    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        \FME\Prodfaqs\Model\Answers $answersModel
    ) {
            
        $this->jsonFactory = $jsonFactory;
        $this->_answersModel = $answersModel;
                
        parent::__construct($context);
    }
        
    public function execute()
    {
        $resultJson = $this->jsonFactory->create();
            
        $params = $this->getRequest()->getParams();
        $error = false;
        $message = '';
            
        if (isset($params['faq'])) {
            $ans_id = (int) $params['faq'];
            $like_value = (int) $params['value'];
            $dislike_value = (int) $params['value2'];
            $sum_values = $like_value + $dislike_value;

            $answers = $this->_answersModel->load($ans_id);
           
            $likes_num = (int) $answers->getLikes();
            $dislikes_num = (int) $answers->getDislikes();
            $sum_nums = $likes_num + $dislikes_num;
            


            $new_likes = $likes_num + 1;
           
            
            try {
                $newData = ['likes' => $like_value , 'dislikes' => $dislike_value ];
                $answers->setData(array_merge($answers->getData(), $newData));
                    
                $this->_answersModel->save($answers);
                $message = __('Thanks for your feedback.');
                    
                //set in session to restrict customer over-rating for this faq
                       
                $customerSession = $this->_objectManager->get('Magento\Customer\Model\Session');
                $faqRating = $customerSession->getLikesRating();
                $ar = explode(',', $faqRating);
                array_push($ar, $ans_id);
                    
                $ar_str = implode(',', $ar);
                    
                $customerSession->setLikesRating($ar_str);
            } catch (\Exception $ex) {
                $message = $ex->getMessage();
                $error = true;
            }
        }
            
            
        return  $resultJson->setData([
                'message' => $message,
                'error' => $error
           ]);
    }
}
