<?php

namespace Uala\Bis\Setup\Patch\Data;

use Magento\Framework\App\Config\ConfigResource\ConfigInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Encryption\EncryptorInterface;


/**
 * Class AddAUalaBis
 */
class AddUalaBisConfiguration implements DataPatchInterface, PatchRevertableInterface
{
    /**
     * @var ConfigInterface
     */
    protected $configInterface;

    /**
     * @var ModuleDataSetupInterface
     */
    protected $moduleDataSetup;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var SetupHelper
     */
    private $setupHelper;

    /**
     * AddUalaBisConfiguration constructor.
     *
     * @param ConfigInterface $configInterface
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param LoggerInterface $logger
     */
    public function __construct(
        ConfigInterface $configInterface,
        ModuleDataSetupInterface $moduleDataSetup,
        LoggerInterface $logger,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->configInterface = $configInterface;
        $this->logger = $logger;
        $this->encryptor = $encryptor;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        // Start setup process
        $this->moduleDataSetup->getConnection()->startSetup();
        try
        {
            $this->configInterface->saveConfig('payment/bis/user_name', '', 'default', 0);
            $this->configInterface->saveConfig('payment/bis/client_id', '', 'default', 0);
            $this->configInterface->saveConfig('payment/bis/client_secret_id', '', 'default', 0);
            $this->configInterface->saveConfig('payment/bis/order_url', '', 'default', 0);
            $this->configInterface->saveConfig('payment/bis/token_url', '', 'default', 0);
            $this->configInterface->saveConfig('payment/bis/checkout_url', '', 'default', 0);
            $this->configInterface->saveConfig('payment/bis/enable_module', '0', 'default', 0);

        } catch (\Exception $e) {
            $this->logger->debug('Unable to create Uala config');
        }

        // End setup process
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public static function getVersion()
    {
        return '1.0.0';
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function revert()
    {
    }
}


