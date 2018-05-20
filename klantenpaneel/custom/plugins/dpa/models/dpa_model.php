<?php

namespace Dpa;

use Settings_Model;
use Cache;

class Dpa_Model extends \Base_Model
{
	public $Error;
	public $Warning;
	public $Success;

	public $db;

	public $config;

	/**
	* dpa::__construct()
	*
	* @return void
	*/
	public function __construct()
	{
		$this->Error = $this->Warning = $this->Success = array();
		$this->db = new \Database_Model;

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
		}
		else {
			return false;
            }
        }

        // No config input entered.
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
	* Send email to Debtor if successfully agreed.
	*
	* @return boolean
	*/
	public function sendEmail($debtorid) {		
		$debtorParams = array(
			'Identifier'	=> $debtorid,
			'TemplateID'	=> $this->config['templateid'],
		);

		$response = $this->APIRequest('debtor', 'sendemail', $debtorParams);

		if ($response['status'] == "success") {
		    return true;
        	}
        	else {
		    return false;
        	}
	}

	/**
	* Process signing
	*
	* @param int $debtor
	* @param boolean $signing
	* @return array
	*/
	public function processSignDate($debtor) {
        	// Determine sign date
        	$sign_date = date("d-m-Y H:i") . ' (' . $_SERVER['REMOTE_ADDR'] . ')';

        	// Edit debtor custom field with sign date
        	$response = $this->APIRequest('debtor', 'edit', array('Identifier' => $debtor, 'CustomFields' => array($this->config['fieldname'] => $sign_date)));

        	return $response;
	}

	/**
	* Edit Debtor with DPA accept date.
	*
	* @return boolean
	*/
	public function updatePreference() {
        	$debtor = $this->getCurrentDebtor();
        	$response = $this->processSignDate($debtor, true);

        	// Process signing.
        	if ($response['status'] == "success") {

            		// Send email if an email address is available. If error occurred, ignore (as if debtor has no emailaddress).
            		if (isset($response['debtor']['EmailAddress']) && $response['debtor']['EmailAddress'] != '') {
                		$this->sendEmail($debtor);
            		}

            		return true;
        	}

        	// Error processing signing.
        	else {
            		return false;
        	}
	}

	/**
	* Retrieve debtor DPA info.
	*
	* @return string
	*/
	public function debtorDPAStatus() {
		$debtor = $this->getCurrentDebtor();
		$response = $this->APIRequest('debtor', 'show', array('Identifier' => $debtor));

        	// If custom field is filled, debtor agreed already
		if (isset($response['debtor']['CustomFields'][$this->config['fieldname']]) && $response['debtor']['CustomFields'][$this->config['fieldname']] != "") {
			if ($response['debtor']['CustomFields'][$this->config['fieldname']] != 1) // This should return 1 if signed, 0 or empty if not signed
				// or present
			{
				return '';
			}
			return $response['debtor']['CustomFields'][$this->config['fieldname']];
		}
		else {
			return '';
		}
	}
}
