<?php

namespace Dpa;

use Settings_Model;
use Cache;

// User has uploaded the module to a special klantenpaneel folder
require_once('../config.php');

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

    public function isActivated()
    {
        if (file_exists('../docs/dpa.pdf')) {
            return true;
        } else {
            return false;
        }
    }

    public function updatePreference()
    {
        $debtor = $this->getCurrentDebtor();

        $response = $this->APIRequest('debtor', 'edit', array('Identifier' => $debtor, 'CustomFields' => array('DPA' => 'yes')));

        $this->sendEmail($debtor);

        return $response;
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

    public function sendEmail($debtorid)
    {
        global $templateid;
        $debtorParams = array(
            'Identifier' => $debtorid,
            'TemplateID' => $templateid,
        );

        $response = $this->APIRequest('debtor', 'sendemail', $debtorParams);

        return $response;
    }

    public function getPreference()
    {
        return $this->checkExists($this->getCurrentDebtor());
    }

    public function checkExists($debtor)
    {
        global $fieldid;
        $query = "SELECT count(Value) FROM `HostFact_Debtor_Custom_Values` WHERE ReferenceID = :debtorid and FieldID = :fieldid";
        $pdo = $this->db->prepare($query);
        $pdo->bindParam(':debtorid', $debtor);
        $pdo->bindParam(':fieldid', $fieldid);
        $pdo->execute();
        // true = result found
        if ($pdo->fetchColumn() > 0 && $pdo->errorCode == 00000) {
            return true;
        } else {
            return false;
        }
    }
}
