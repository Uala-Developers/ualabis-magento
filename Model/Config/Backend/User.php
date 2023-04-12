<?php

namespace Uala\Bis\Model\Config\Backend;

use \Magento\Framework\App\Config\Value;
use \Magento\Framework\App\Config\Storage\WriterInterface;
use \Magento\Framework\Message\ManagerInterface;

class User extends Value
{    
    protected $message;
    public $writer;
    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\App\Config\ValueFactory $configValueFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param string $runModelPath
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Config\ValueFactory $configValueFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        WriterInterface $writer,
        ManagerInterface $message,
        array $data = []
    ) {
        $this->_configValueFactory = $configValueFactory;
        $this->writer = $writer;    
        $this->message = $message;  
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }
    public function beforeSave()
    {
        $scope = "default";
        if ($this->getValue() != $this->getOldValue()) {
            if ($this->getValue() == 'no') {
                $scope = "default";
                $this->writer->save('payment/bis/user_name', "", $scope);
                $this->writer->save('payment/bis/client_id', "", $scope);
                $this->writer->save('payment/bis/client_secret_id', "", $scope);
                $this->writer->save('payment/bis/order_url', "", $scope);
                $this->writer->save('payment/bis/token_url', "", $scope);
                $this->writer->save('payment/bis/checkout_url', "", $scope);
                $this->writer->save('payment/bis/active', "0", $scope);
                $this->message->addError(__("No has configurado credenciales, no sera posible usar el modulo"));
            } else if ($this->getValue() == 'test') {
                $scope = "default";
                $this->writer->save('payment/bis/user_name', "new_user_1631906477", $scope);
                $this->writer->save('payment/bis/client_id', "5qqGKGm4EaawnAH0J6xluc6AWdQBvLW3", $scope);
                $this->writer->save('payment/bis/client_secret_id', "cVp1iGEB-DE6KtL4Hi7tocdopP2pZxzaEVciACApWH92e8_Hloe8CD5ilM63NppG", $scope);
                $this->writer->save('payment/bis/order_url', "https://checkout.stage.ua.la/1/order", $scope);
                $this->writer->save('payment/bis/token_url', "https://auth.stage.ua.la/1/auth/token", $scope);
                $this->writer->save('payment/bis/checkout_url', "https://checkout.stage.ua.la/1/checkout", $scope);
                $this->writer->save('payment/bis/active', "1", $scope);
                $this->message->addSuccess(__("Credenciales de test configuradas correctamente"));
            } else if ($this->getValue() == 'prod') {
                $this->writer->save('payment/bis/active', "1", $scope);
                $this->message->addSuccess(__("Credenciales de produccion configuradas correctamente"));
                //LETS KEEP THIS TO ADD CHECK FOR POPUP WORK
                //throw new \Magento\Framework\Exception\ValidatorException(__($this->getValue()));
            }
        }

    }
}
