<?php
namespace MauticPlugin\SmsreaderBundle\Controller;

use Mautic\CoreBundle\Controller\CommonController;

/**
 * Class SmsreaderBundle
 */

class SmsreaderController extends CommonController
{
    /*
     * Display the SMSReader Configuration Page
     */
    public function indexAction()
    {
        $model = $this->getModel('smsreader');
        $config = $model->getConfig();
        if(array_key_exists('unsubscribeKeywords', $config)) { $config['unsubscribeKeywords'] = implode(',', $config['unsubscribeKeywords']); }
        if(array_key_exists('resubscribeKeywords', $config)) { $config['resubscribeKeywords'] = implode(',', $config['resubscribeKeywords']); }

        $formFactory = $this->get('form.factory');
        $form = new \MauticPlugin\SmsreaderBundle\Form\Type\Config();
        $formBuilder = $this->createFormBuilder($config);
        $form = $form->buildForm($formBuilder, [])->getForm();
        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $keywordSanitize = function($el) {
                return trim(strtolower($el));
            };
            $unsub = array_map($keywordSanitize, explode(',', $data['unsubscribeKeywords']));
            $resub = array_map($keywordSanitize, explode(',', $data['resubscribeKeywords']));
            $model->saveConfig($data['accountId'], $unsub, $resub);
            $this->addNotification('Settings Saved', 'success');
            $this->addFlash('Settings Saved', []);
        }

        return $this->delegateView([
            'viewParameters'    => [
                'title'         => 'smsreader.title',
                'form' => $form->createView(),
                'saved' => ($form->isSubmitted() && $form->isValid())
            ],
            'contentTemplate' => 'SmsreaderBundle:Smsreader:index.html.php',
        ]);
    }
}
