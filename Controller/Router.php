<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace FME\Prodfaqs\Controller;

class Router implements \Magento\Framework\App\RouterInterface
{
    
    protected $actionFactory;

    protected $_eventManager;

    protected $_storeManager;

    protected $_topicFactory;
    
    protected $_faqsFactory;

    protected $_appState;

    protected $_url;

    protected $_response;

    
    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\UrlInterface $url,
        \FME\Prodfaqs\Model\TopicFactory $topicFactory,
        \FME\Prodfaqs\Model\FaqsFactory $faqsFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\ResponseInterface $response
    ) {
        $this->actionFactory = $actionFactory;
        $this->_eventManager = $eventManager;
        $this->_url = $url;
        $this->_topicFactory = $topicFactory;
        $this->_faqsFactory = $faqsFactory;
        $this->_storeManager = $storeManager;
        $this->_response = $response;
    }

    /**
     * Validate and Match Faqs main-page, detail page and modify request
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return bool
     */
    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        $topic = $this->_topicFactory->create();
        
        $faqs = $this->_faqsFactory->create();
        
        $identifier = trim($request->getPathInfo(), '/');
        $identifier = str_replace($topic->getFaqsSeoIdentifierPostfix(), '', $identifier);
        
        $condition = new \Magento\Framework\DataObject(['identifier' => $identifier, 'continue' => true]);
        $this->_eventManager->dispatch(
            'faqs_controller_router_match_before',
            ['router' => $this, 'condition' => $condition]
        );
        $identifier = $condition->getIdentifier();
        
        if ($condition->getRedirectUrl()) {
            $this->_response->setRedirect($condition->getRedirectUrl());
            $request->setDispatched(true);
            return $this->actionFactory->create('Magento\Framework\App\Action\Redirect');
        }

        if (!$condition->getContinue()) {
            return null;
        }
        
        /*for main page */
        /*check identifier against faqs-configuration main page idendifier */
        
        $mainIdentifier = $topic->getMainPageIdentifer();
         
        if ($mainIdentifier == $identifier) {
            $request->setModuleName('prodfaqs')->setControllerName('index')->setActionName('index');
            $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $identifier);
            return $this->actionFactory->create('Magento\Framework\App\Action\Forward');
        }
        
        $identifier = substr($identifier, strpos($identifier, '/')+1, strlen($identifier));
        
        
        
        /*check identifier for faq page */
        $faqId = $faqs->checkIdentifier($identifier);
        if ($faqId) {
            $request->setModuleName('prodfaqs')->setControllerName('index')->setActionName('faq')->setParam('id', $faqId);
            $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $identifier);
            return $this->actionFactory->create('Magento\Framework\App\Action\Forward');
        }
        
        
        
        /*check identifier for faq page */
        $topicId = $topic->checkIdentifier($identifier, $this->_storeManager->getStore()->getId());
        if (!$topicId) {
            return null;
        }
        
        $request->setModuleName('prodfaqs')->setControllerName('index')->setActionName('detail')->setParam('topic', $topicId);
        $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $identifier);
        return $this->actionFactory->create('Magento\Framework\App\Action\Forward');
    }
}
