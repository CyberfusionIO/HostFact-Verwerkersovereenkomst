<?php

namespace Dpa;

use Template;
use Plugin;
use Settings_Model;

class Dpa_Controller extends \Base_Controller
{
	protected $Dpa;

	public function __construct(Template $template)
	{
        	// Call service controller construct
        	parent::__construct($template);
        	$this->Dpa = new Dpa_Model();
    	}

	public function index()
	{
		if (isset($_GET['rt'])) {
			$event = explode("/", $_GET['rt']);
			if (isset($event[1])) {
			    $this->{$event[1]}();
			    return;
			}
		}

		// Check config file and if PDF exists, else disable plugin.
		if ($this->Dpa->isActivated() === false) {
            		$this->Template->Active = false;
			$this->Dpa->Warning[] = __('signed soon');
			$template = "dpa.deactivated";
		}
		
		// Plugin is active.
		else {
            		$this->Template->Active = true;
            		$this->Template->DPAInfo = $this->Dpa->debtorDPAStatus();

            		// Debtor already agreed.
           	 	if ($this->Template->DPAInfo != '') {
                		$this->Dpa->Warning[] = __('already agreed');
                		$template = "dpa.agreed";
            	}
			
            	// Debtor agreed to the terms.
            	elseif (isset($_POST['agree'])) {
			// Process successful.
			if ($this->Dpa->updatePreference() === true){
                    		$this->Template->DPAInfo = $this->Dpa->debtorDPAStatus();
                    		$this->Dpa->Success[] = __('success');
                    		$template = "dpa.agreed";
               		}

                	// Error occurred while processing in the debtor info.
                	else {
                    		$this->Dpa->Success[] = __('error processing');
                    		$template = "dpa";
                	}
		}

		// If debtor agrees, but forgot the checkbox.
		elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && !(isset($_POST['agree']))) {
			$this->Dpa->Warning[] = __('not checked');
                	$template = "dpa";
		}

		// Debtor has not agreed yet.
		else {
                	$template = "dpa";
            	}
		}

        	$this->Template->parseMessage($this->Dpa);
        	$this->Template->show($template);
	}

	// Show PDF (inline)
	public function pdf() {
		$file = realpath(CUSTOMPATH . '/plugins/dpa/docs/' . $this->Dpa->config['pdffile']);

        	header('Content-type: application/pdf');
        	header('Content-Disposition: inline; filename="' . $this->Dpa->config['pdffile'] . '"');
        	header('Content-Transfer-Encoding: binary');
        	header('Content-Length: ' . filesize($file));
        	header('Accept-Ranges: bytes');

        	@readfile($file);
   }

    	// Download PDF (attachment)
	public function download() {
        	$file = realpath(CUSTOMPATH . '/plugins/dpa/docs/' . $this->Dpa->config['pdffile']);

        	header('Content-type: application/pdf');
        	header('Content-Disposition: attachment; filename="' . $this->Dpa->config['pdffile'] . '"');
        	header('Content-Transfer-Encoding: binary');
        	header('Content-Length: ' . filesize($file));
        	header('Accept-Ranges: bytes');

        	@readfile($file);
	}
}
