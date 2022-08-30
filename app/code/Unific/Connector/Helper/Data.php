<?php

namespace Unific\Connector\Helper;

use Magento\Framework\Exception\InputException;
use Symfony\Component\Config\Definition\Exception\Exception;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    private $integrationFactory;
    private $oauthService;
    private $authorizationService;
    private $oauthTokenModel;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Integration\Model\IntegrationFactory $integrationFactory
     * @param \Magento\Integration\Model\OauthService $oauthService
     * @param \Magento\Integration\Model\AuthorizationService $authorizationService
     * @param \Magento\Integration\Model\Oauth\Token $oauthTokenModel
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Integration\Model\IntegrationFactory $integrationFactory,
        \Magento\Integration\Model\OauthService $oauthService,
        \Magento\Integration\Model\AuthorizationService $authorizationService,
        \Magento\Integration\Model\Oauth\Token $oauthTokenModel
    ) {
        parent::__construct($context);

        $this->integrationFactory = $integrationFactory;
        $this->oauthService = $oauthService;
        $this->authorizationService = $authorizationService;
        $this->oauthTokenModel = $oauthTokenModel;
    }

    /**
     * @return mixed
     */
    public function getApiUser()
    {
        return $this->integrationFactory->create()->load(Settings::API_INTEGRATION_NAME, 'name')->getData();
    }

    /**
     * Create a new API User for Unific to work with
     */
    public function createApiUser()
    {
        $integrationExists = $this->integrationFactory->create()
            ->load(Settings::API_INTEGRATION_NAME, 'name')->getData();

        if (empty($integrationExists)) {
            $integrationData = [
                'name' => Settings::API_INTEGRATION_NAME,
                'email' => Settings::API_INTEGRATION_EMAIL,
                'status' => '1',
                'endpoint' => Settings::API_INTEGRATION_ENDPOINT,
                'setup_type' => '0'
            ];

            // Code to create Integration
            $integrationFactory = $this->integrationFactory->create();
            $integration = $integrationFactory->setData($integrationData);
            $integration->save();
            $integrationId = $integration->getId();
            $consumerName = 'Integration' . $integrationId;

            // Code to create consumer
            $consumer = $this->oauthService->createConsumer(['name' => $consumerName]);
            $consumerId = $consumer->getId();
            $integration->setConsumerId($consumer->getId());
            $integration->save();

            // Code to grant permission
            $this->authorizationService->grantAllPermissions($integrationId);

            // Code to Activate and Authorize
            $uri = $this->oauthTokenModel->createVerifierToken($consumerId);
            $this->oauthTokenModel->setType('access');
            $this->oauthTokenModel->save();
        }
    }
}
