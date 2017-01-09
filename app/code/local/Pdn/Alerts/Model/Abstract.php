<?php

/**
 * Abstrack Alert Class
 * 
 */
class Pdn_Alerts_Model_Abstract  extends Mage_Core_Model_Abstract 
{
    /**
     * Notificaiton email address
     * 
     */
    private $_alertEmail = null;


    /**
     * Constructor
     * 
     * @return void
     */
    public function __construct($alertEmail = null)
    {
        $this->setAlertEmailAddress($alertEmail);
        parent::__construct();
    }


    /**
     * Get notificaiton Email address.this is set in site config and
     * is not changed anywhere in code
     * 
     * 
     * @return string
     */
    public function getAlertEmailAddress()
    {
        return $this->_alertEmail;
    }


    /**
     * Set notificaiton Email address.this is set in site config and
     * is not changed anywhere in code
     * 
     * @return void
     */
    public function setAlertEmailAddress($alertEmail = null)
    {
        if ($alertEmail == null)
        {
            $alertEmail = Mage::getStoreConfig('alerts/messaging/email');
        }
        
        $this->_alertEmail = $alertEmail;
    }


    /**
     *  Send a notificaiton. Email only right now
     * 
     * @return void
     */
    public function sendAlert($subject, $message)
    {
        $this->sendEmailAlert($message, $subject, $this->getAlertEmailAddress(), $this->getSiteContactEmail());
    }


    /**
     * Set teh email address that will be used as sender for 
     * alerts
     * 
     * @return string 
     */  
    public function getSiteContactEmail()
    {
        return Mage::getStoreConfig('contacts/email/recipient_email');
    }


    /**
     * Email Notificaiton
     * 
     * @return void
     */
    public function sendEmailAlert($message, $subject, $recipient, $sender)
    {

        $mail = Mage::getModel('core/email');
        $mail->setToName('');
        $mail->setToEmail($recipient);
        $mail->setBody($message);
        $mail->setSubject($subject);
        $mail->setFromEmail($sender);
        $mail->setFromName("Magento Alerts");
        $mail->setType('text'); // 'html' or 'text'

        try {
            $mail->send();
        }
        catch (Exception $e) {
            Mage::log('Unable to send alert email with message: ' . $message);
        }
    }

    


}