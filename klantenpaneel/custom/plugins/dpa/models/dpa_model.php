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

	public function __construct()
	{
		$this->Error = $this->Warning = $this->Success = array();
		$this->db = new \Database_Model;
	}
	
	public function isActivated() {
		return true;
	}

	/**
	 * Get current Debtor identifier
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
	
	public function sendEmail($debtorid) {		
		$debtorParams = array(
			'Identifier'	=> $debtorid,
			'Subject'       => 'Replace me',
			'TemplateID'	=> 'Replace me',
			'Message'	=> 'Replace me',
		);

		$response = $this->APIRequest('debtor', 'sendemail', $debtorParams);

		return $response;
	}

	public function updatePreference() {
		$debtor = $this->getCurrentDebtor();

		$response = $this->APIRequest('debtor', 'edit', array('Identifier' => $debtor, 'CustomFields' => array('DPA' => 'yes')));
		
		$this->sendEmail($debtor);

		return $response;
	}

	public function checkExists($debtor) {
		$query = "SELECT Value FROM `HostFact_Debtor_Custom_Values` WHERE ReferenceID = :id and FieldID = REPLACEME";
		$pdo = $this->db->prepare($query);
		$pdo->bindParam(':id', $debtor);
		$pdo->execute();
		// true = result found
		if ($pdo->rowCount() > 0) {
			return true;
		}
		else {
			return false;
		}
	}

	public function getPreference() {
		$debtor = $this->getCurrentDebtor();

		return $this->checkExists($debtor);
	}
}
