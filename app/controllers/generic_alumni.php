<?php 
/** Trans-Iluni DB @version 0.1.6 @package Trans-Iluni Database **/

if (!defined('BASEPATH')) exit('No direct script access allowed'); 
include("generic_detail.php");

abstract class Generic_alumni extends Generic_detail {

	function __construct () { 
		parent::__construct(); 	
		
		$allowedgroup = array('user', 'entry', 'admin');
		$group = $this->session->userdata('group');
		if (!in_array($group, $allowedgroup))	// Trap any request			
			show_error('Sorry. You are not authorized to manage alumni.');
			
		$this->icon48			= 'icon-48-section';		
		$this->load->helper('form');		
	}		
	
	protected function onBeforeManage(&$data) {
		$data['alumna']	=	$this->dbm->getAlumna($data['MasterID']);			
	}	
} // class

?>