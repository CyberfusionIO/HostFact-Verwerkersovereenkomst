<?php

namespace Dpa;

use Settings_Model;
use Cache;

class Dpa_Model extends \Base_Model
{
    public $Error;
    public $Warning;
    public $Success;

    public $config;

    /**
     * dpa::__construct()
     *
     * @return void
     */
    public function __construct()
    {
        $this->Error = $this->Warning = $this->Success = array();

        // Load config
        include_once(CUSTOMPATH . '/plugins/dpa/config.php');
        $this->config = isset($config) ? $config : [];
    }

    /**
     * Check if DPA plugin is active (if PDF doc exists).
     *
     * @return boolean
     */
    public function isActivated()
    {
        // Config input entered.
        if (isset($this->config['fieldname']) && $this->config['fieldname'] != 'replaceme' &&
            isset($this->config['templateid']) && $this->config['templateid'] != 'replaceme' &&
            isset($this->config['pdffile']) && $this->config['pdffile'] != 'replaceme'
        ) {

            // Check if PDF exists
            if (file_exists(CUSTOMPATH . '/plugins/dpa/docs/' . $this->config['pdffile'])) {
                return true;
            } else {
                return false;
            }
        } // No config input entered.
        else {
            return false;
        }
    }

    /**
     * Edit Debtor with DPA accept date.
     *
     * @return boolean
     */
    public function updatePreference()
    {
        $debtor = $this->getCurrentDebtor();
        $response = $this->processSignDate($debtor);

        // Process signing.
        if ($response['status'] == "success") {

            // Send email if an email address is available. If error occurred, ignore (as if debtor has no emailaddress).
            if (isset($response['debtor']['EmailAddress']) && $response['debtor']['EmailAddress'] != '') {
                $this->sendEmail($debtor);
            }

            return true;
        } // Error processing signing.
        else {
            return false;
        }
    }

    /**
     * Get current Debtor identifier.
     *
     * @return int
     */
    public function getCurrentDebtor()
    {
        $debtor_model = new \Debtor_Model();
        $debtor_model->show();
        if (empty($debtor_model->Identifier)) {
            return false;
        }

        return $debtor_model->Identifier;
    }

    /**
     * Process signing
     *
     * @param int $debtor
     * @return array
     */
    public function processSignDate($debtor)
    {
        // Determine sign date
        $sign_date = date("d/m/Y H:i") . '-' . ' (' . $_SERVER['REMOTE_ADDR'] . ')';

        // Edit debtor custom field with sign date
        $response = $this->APIRequest('debtor', 'edit', array('Identifier' => $debtor, 'CustomFields' => array($this->config['fieldname'] => $sign_date)));

        return $response;
    }

    /**
     * Send email to Debtor if successfully agreed.
     *
     * @return boolean
     */
    public function sendEmail($debtorid)
    {
        $debtorParams = array(
            'Identifier' => $debtorid,
            'TemplateID' => $this->config['templateid'],
        );

        $response = $this->APIRequest('debtor', 'sendemail', $debtorParams);

        if ($response['status'] == "success") {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Retrieve debtor DPA info.
     *
     * @return string
     */
    public function debtorDPAStatus()
    {
        $debtor = $this->getCurrentDebtor();
        $response = $this->APIRequest('debtor', 'show', array('Identifier' => $debtor));

        if($this->validateDateIPString($response['debtor']['CustomFields'][$this->config['fieldname']])) {
            return $response['debtor']['CustomFields'][$this->config['fieldname']];
        }
        return '';

    }

    /**
     * Check if Date and IP are in the customfield to show the message on the index page and the agree screen.
     *
     * @param string $dt
     * @return bool
     */
    private function validateDateIPString(string $dt) {
        $dt = explode('-', $dt);
        $date = str_replace('/', '-', $dt[0]);
        $ip = str_replace(['(', ')', ' '], '', $dt[1]);
        if(strlen($date) == 11 && filter_var($ip, FILTER_VALIDATE_IP)) {
            return true;
        }
        return false;
    }

    /**
     * Get correct URL
     *
     * @return string
     */
    public function getURL($url)
    {
        if (strpos($url, 'dpa') !== false) {
            return 'dpa';
        } elseif (strpos($url, 'verwerkersovereenkomst') !== false) {
            return 'verwerkersovereenkomst';
        }
    }
}
