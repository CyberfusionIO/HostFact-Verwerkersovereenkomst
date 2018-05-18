<?php

namespace Dpa;

use Settings_Model;
use Cache;

// User has uploaded the module to a special klantenpaneel folder
if (file_exists('../config.php')) {
	include_once('../config.php');
}

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
		if (file_exists('../docs/dpa.pdf')) {
			return true;
		}
		else {
			return false;
		}
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
			'TemplateID'	=> $templateid,
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
		$query = "SELECT Value FROM `HostFact_Debtor_Custom_Values` WHERE ReferenceID = :debtorid and FieldID = :fieldid";
		$pdo = $this->db->prepare($query);
		$pdo->bindParam(':debtorid', $debtor);
		$pdo->bindParam(':fieldid', $fieldid);
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
		return $this->checkExists($this->getCurrentDebtor());
	}
}
