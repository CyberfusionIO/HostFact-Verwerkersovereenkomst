<?php

namespace Dpa;

use Template;
use Plugin;
use Settings_Model;

class Dpa_Controller extends \Base_Controller
{
	public function __construct(Template $template)
	{
		// Call service controller construct
		parent::__construct($template);
		$this->Dpa = new Dpa_Model();	
	}

	public function index()
	{
		$this->Template->preference = $this->Dpa->getPreference();
		
		if (isset($_GET['rt'])) {
			$event = explode("/", $_GET['rt']);
			if (isset($event[1]) AND $event[1]) {
				$this->{$event[1]}();
				exit();
			}
			
		}

		if (!$this->Dpa->isActivated()) {
			$this->Dpa->Warning[] = __('signed soon');
			$this->Template->parseMessage($this->Dpa);
			$this->Template->Dpa = $this->Dpa->getPreference();
			$this->Template->show('dpa.inactive');
		}
		else {
			if (isset($_POST['agree'])) {
				$this->Dpa->Success[] = __('success');
				$this->Dpa->updatePreference();
				$this->Template->parseMessage($this->Dpa);
				$this->Template->Dpa = $this->Dpa->getPreference();
				$this->Template->show('dpa.list');
			}
			elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && !(isset($_POST['agree']))) {
				$this->Dpa->Warning[] = __('not checked');
				$this->Template->parseMessage($this->Dpa);
				$this->Template->Dpa = $this->Dpa->getPreference();
				$this->Template->show('dpa.list');
			}
			elseif ($this->Template->preference == true) {
				$this->Dpa->Warning[] = __('already agreed');
				$this->Template->parseMessage($this->Dpa);
				$this->Template->Dpa = $this->Dpa->getPreference();
				$this->Template->show('dpa.list');
			}
			else {
				$this->Template->parseMessage($this->Dpa);
				$this->Template->Dpa = $this->Dpa->getPreference();
				$this->Template->show('dpa.list');
			}
		}
	}

	public function pdf() {
		$file = realpath(__DIR__ . '/../docs/dpa.pdf');

		header('Content-type: application/pdf');
		header('Content-Disposition: inline; filename="' . 'Verwerkersovereenkomst' . '"');
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: ' . filesize($file));
		header('Accept-Ranges: bytes');

	       	@readfile($file);
   }
}
