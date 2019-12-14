<?php
namespace FME\Prodfaqs\Block;

use Magento\Framework\View\Element\Template;

class Faqs extends Template
{
    protected $_topicModel;

    protected $_faqsModel;

    protected $_objectManager;
        
    protected $_coreRegistry = null;
    protected $_answersModel;
    protected $urlBuilder;
        
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \FME\Prodfaqs\Model\Topic $topicModel,
        \FME\Prodfaqs\Model\Faqs $faqsModel,
        \FME\Prodfaqs\Model\Answers $answersModel,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $registry
    ) {
                        
        $this->_topicModel = $topicModel;
        $this->_faqsModel = $faqsModel;
        $this->_answersModel = $answersModel;
        $this->_objectManager = $objectManager;
        $this->_coreRegistry = $registry;
        $this->urlBuilder = $context->getUrlBuilder();
                
        parent::__construct($context);
    }
        
      


    public function getfaqanswers($faq_id)
    {
    
        $answers = $this->_answersModel->loadFaqanswers($faq_id);
        return $answers;
    }

    public function getFrontPageTopics()
    {
            
        $topicsCollection = $this->_topicModel->loadFrontPageTopics();
                
        return $topicsCollection;
    }

        
    public function getFrontPageFaqs($topic_id)
    {
                
        $faqsCollection = $this->_faqsModel->loadFaqsOfTopic($topic_id, true);
                
        return $faqsCollection;
    }
        
    public function numberOfQuestionToDisplay()
    {
            
        $noq = $this->_objectManager->get('FME\Prodfaqs\Helper\Data')->getNumOfQuestionsForFaqsPage();
                
        return $noq;
    }
        
    public function getTopicUrl($topic_id)
    {
                
        $topic = $this->_topicModel->load($topic_id);
        $topic_identifier = $topic->getIdentifier();
                
        $main_identifier = $this->_topicModel->getMainPageIdentifer();
        $url_sufix = $this->_topicModel->getFaqsSeoIdentifierPostfix();
                
        $url = $main_identifier.'/'.$topic_identifier.$url_sufix;
        return $this->urlBuilder->getUrl($url);
    }
        
    public function getMainPageUrl()
    {
                              
        $main_identifier = $this->_topicModel->getMainPageIdentifer();
        $url_sufix = $this->_topicModel->getFaqsSeoIdentifierPostfix();
                
        $url = $main_identifier.$url_sufix;
        return $this->urlBuilder->getUrl($url);
    }
        
    public function getFaqUrl($faqObj)
    {
                
        $faq_id = $faqObj->getId();
        $faq = $this->_faqsModel->load($faq_id);
        $faq_identifier = $faq->getIdentifier();
                
        $main_identifier = $this->_topicModel->getMainPageIdentifer();
        $url_sufix = $this->_topicModel->getFaqsSeoIdentifierPostfix();
                
        $url = $main_identifier.'/'.$faq_identifier.$url_sufix;
        return $this->urlBuilder->getUrl($url);
    }
        
    public function getFaqsBlockNumberOfTopics()
    {
            
        return $this->_objectManager->get('FME\Prodfaqs\Helper\Data')->getFaqsBlockNumberOfTopics();
    }
        
    public function isViewMoreLinkEnable()
    {
            
        return $this->_objectManager->get('FME\Prodfaqs\Helper\Data')->isViewMoreLinkEnable();
    }
        
    public function isAccordionEnable()
    {
            
        return $this->_objectManager->get('FME\Prodfaqs\Helper\Data')->isAccordionEnable();
    }
        
    public function allowedAnswerLength()
    {
            
        return $this->_objectManager->get('FME\Prodfaqs\Helper\Data')->allowedAnswerLength();
    }
        
    public function isRatingEnable()
    {
            
        return $this->_objectManager->get('FME\Prodfaqs\Helper\Data')->isRatingEnable();
    }
        
    public function isCustomerRatingReadonly()
    {
            
        $conf = $this->_objectManager->get('FME\Prodfaqs\Helper\Data')->getAllowedCustomerForRating();
        $customer = $this->_objectManager->get('Magento\Customer\Model\Session');
        $readonly = 'true';
        if ($conf == 'all') {
            $readonly = 'false';
        } elseif ($conf == 'guests') {
            if (!$customer->isLoggedIn()) {
                $readonly = 'false';
            } else {
                $readonly = 'true';
            }
        } elseif ($conf == 'registered') {
            if ($customer->isLoggedIn()) {
                $readonly = 'false';
            } else {
                $readonly = 'true';
            }
        } else {
            $readonly = 'true';
        }
        return $readonly;
    }
       

    public function isCustomerReadonlyStars($faq_id)
    {
            
        // form configuration
        $conf = $this->isCustomerRatingReadonly();
        if ($conf == 'true') {
            return 1;
        }
                
        //now check if customer already rated for that faq
        $customerSession = $this->_objectManager->get('Magento\Customer\Model\Session');
        $faqRating = $customerSession->getFaqRating();
        $ar = explode(',', $faqRating);
                
        $found = array_search($faq_id, $ar);
                
        return $found;
    }

    public function isCustomerReadonlyLikes($faq_id)
    {
            
        // form configuration
        
                
        //now check if customer already rated for that faq
        $customerSession = $this->_objectManager->get('Magento\Customer\Model\Session');
        $faqRating = $customerSession->getLikesRating();
        $ar = explode(',', $faqRating);
                
        $found = array_search($faq_id, $ar);
                
        return $found;
    }
        /*
         * functions for topic detail page
         */
        
    public function getCurrentTopicDetail()
    {
            
        $topicData = $this->_coreRegistry->registry('current_topic');
        return $topicData ? $topicData : false;
    }
        
    public function getCurrentFaqDetail()
    {
            
        $faqData = $this->_coreRegistry->registry('current_faq');
        return $faqData ? $faqData : false;
    }
        
    public function getDetailPageFaqs($topic_id)
    {
                
        $faqsCollection = $this->_faqsModel->loadFaqsOfTopic($topic_id, false);
                
        return $faqsCollection;
    }
        
    public function getRatingAjaxUrl()
    {
            
        $rating_url = $this->urlBuilder->getUrl('prodfaqs/index/rating');
                
        return $rating_url;
    }
    public function getLikesAjaxUrl()
    {
            
        $rating_url = $this->urlBuilder->getUrl('prodfaqs/index/likes');
                
        return $rating_url;
    }
    public function getAjaxUrl()
    {
            
        $rating_url = $this->urlBuilder->getUrl('prodfaqs/index/faqajax');
                
        return $rating_url;
    }
    public function getSearchUrl()
    {
            
        $search_url = $this->urlBuilder->getUrl('prodfaqs/index/search');
                
        return $search_url;
    }
        
    public function getFaqSearchDetail()
    {
            
        $searchData = $this->_coreRegistry->registry('faq_search_results');
               
        return $searchData;
    }
        
    public function getPopularTags()
    {
            
        $tags = $this->_faqsModel->getPopularTags();
        return $tags;
    }
        
    public function getMediaDirectoryUrl()
    {
            
        $media_dir = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')
        ->getStore()
        ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
            
        return $media_dir;
    }
}
