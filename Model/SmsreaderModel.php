<?php
namespace MauticPlugin\SmsreaderBundle\Model;

use Mautic\CoreBundle\Model\AbstractCommonModel;
use Mautic\CoreBundle\Helper\CoreParametersHelper;
use Mautic\CoreBundle\Configurator\Configurator;
use Mautic\CoreBundle\Helper\CacheHelper;
use Mautic\CoreBundle\Helper\EncryptionHelper;
use Mautic\CoreBundle\Model\FormModel;

/**
 * Class SmsreaderModel
 */
class SmsreaderModel extends AbstractCommonModel
{
    /**
     * Parameters from local.php
     * 
     * @var array
     */
    protected $config;

    /**
     * @var Configurator
     */
    protected $configurator;

    /**
     * @var Constructor
     */
    public function __construct(
        CoreParametersHelper $coreParametersHelper,
        Configurator $configurator,
        CacheHelper $cacheHelper)
    {
        $this->config = $coreParametersHelper->getParameter('smsreader');
        $this->configurator = $configurator;
        $this->cache = $cacheHelper;

    }

    /**
     * Gets the Config from Local.php and returns in array form
     *
     * @return array
     */
    public function getConfig() {
        $parameters = $this->configurator->getParameters();
        return array_key_exists('smsreader', $parameters) ? $parameters['smsreader'] : array();
    }

    /**
     * Saves the configuration to local.php
     *
     * @param string $accountId Twillio Account ID
     * @param array $unsubscribeKeywords List of words (lowercase, trimmed) that you want to be used for unsubscribe
     * @param array $resubscribeKeywords List of words (lowercase, trimmed) that you want to be used for resubscribe
     * @return void
     */
    public function saveConfig($accountId, array $unsubscribeKeywords, array $resubscribeKeywords) {
        $this->configurator->mergeParameters(
            [
                'smsreader' => [
                    'accountId' => $accountId,
                    'unsubscribeKeywords' => $unsubscribeKeywords,
                    'resubscribeKeywords' => $resubscribeKeywords
                ]
            ]
        );
        $this->configurator->write();
        $this->cache->clearContainerFile();
    }
}
