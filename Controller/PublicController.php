<?php
namespace MauticPlugin\SmsreaderBundle\Controller;

use Mautic\CoreBundle\Controller\CommonController;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Util\Codes;
use Mautic\LeadBundle\Entity\Lead;
use DateTime;

/**
 * Class PublicController
 */

class PublicController extends CommonController
{
    /*
     * @param string $command
     */
    public function triggerAction($command)
    {
        $response  = new Response();
        $requiredFields = ['SmsMessageSid', 'AccountSid', 'From', 'Body'];

        $model = $this->getModel('smsreader');
        $config = $model->getConfig();
        $logger = $this->get('monolog.logger.mautic');
        if ($config === false || sizeof($config) === 0) {
            echo 'NOT CONFIGURED';
            return;
        }
        
        $validAccountId = $config['accountId'];
        $unsubscribeWords = $config['unsubscribeKeywords'];
        $resubscribeWords = $config['resubscribeKeywords'];
        
        // Twillio comes back with RAW non-form formatted POST data
        parse_str(file_get_contents('php://input'), $post);
        $post = array_map(function($el){
            return urldecode($el);
        }, $post);

        foreach( $requiredFields as $requiredField ) {
            if (!array_key_exists($requiredField, $post)) {
                return $this->json(['error' => 'Requirements not met'], 500);
            }
        }

        // Make sure that the account ID matches the account ID configured
        if ($post['AccountSid'] !== $validAccountId) {
            return $this->json(['error' => 'Account ID invalid'], 500);
        }

        // Does the text match any of the Unsubscribe or Resubscribe words?
        $sms_action = null;
        $sms_body = explode(' ', strtolower($post['Body']));
        foreach( ['unsub' => $config['unsubscribeKeywords'], 'resub' => $config['resubscribeKeywords']] as $tmp_action => $words_to_match) {
            foreach($sms_body as $sms_word) {
                if (in_array($sms_word, $words_to_match)) {
                    $sms_action = $tmp_action;
                }
            }
        }
        if (is_null($sms_action)) {
            return $this->json(['message' => 'No Actions relevant for this message']);
        }


        // Look up the Contact from the Number
        $leadModel = $this->getModel('lead');
        $possibleNumbers = [
            trim($post['From']),
            '+' . trim($post['From'])
        ];

        $found_contact = null;
        foreach($possibleNumbers as $possibleNumber) {
            $list = $leadModel->getLeadList(10, new DateTime('1980-01-01'), new DateTime(), [
                'mobile' => $possibleNumber
            ]);
            if (sizeof($list) > 0) {
                $found_contact = $list[0];
                break;
            }
        }
        
        if (is_null($found_contact)) {
            return $this->json(['error' => 'Lead phone does not exist'], 500);
        }

        $lead = $leadModel->getEntity($found_contact['id']);

        // Unsubscribe / Resubscribe person
        if ($sms_action === 'unsub') {
            $leadModel->addDncForLead($lead, 'sms', 'Unsubscribed via Twillio Webhook', \Mautic\LeadBundle\Entity\DoNotContact::UNSUBSCRIBED);
            $leadModel->modifyTags($lead, ['SMS Unsubscribed']);
            return $this->json(['message' => 'Unsubscribe successfully processed']);
        } else if ($sms_action === 'resub') {
            $leadModel->removeDncForLead($lead, 'sms');
            $leadModel->modifyTags($lead, ['-SMS Unsubscribed']);
            return $this->json(['message' => 'Resubscribe successfully processed']);
        }
        
        // Return success message
        return $this->json(['message' => 'Message Actioned Successfully']);
    }

    protected function json($data, $responseCode = 200) {
        $response = new Response( json_encode($data) );
        $response->setStatusCode($responseCode);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

}
