<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 *  teleconference noticeboard block
 *
 * @package    block_teleconference_noticeboard
 * @copyright  2016 Marios Theodoulou mariostheodoulou.com <marios152 at gmail.com> 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir.'/formslib.php');
require_once($CFG->libdir.'/datalib.php');

/*
	The forms that are used by this plugin
*/
	
class teleconf_add_session_info extends moodleform{
	function definition(){
		global $COURSE;
		$mform=$this->_form;
		$mform->addElement('header', 'general', get_string('general'));
		
		$attributes='size="70"';
		$mform->addElement('text', 'session_title', get_string('sessionTitle','block_teleconference_noticeboard'),$attributes);
		$mform->setType('session_title',PARAM_NOTAGS);
		
		$mform->addElement('text', 'link', get_string('link','block_teleconference_noticeboard'),$attributes);
		$mform->setType('link',PARAM_NOTAGS);	
		$mform->addRule('link',get_string('requiredError','block_teleconference_noticeboard'),'required'); 
	
		$mform->addElement('text', 'time', get_string('time','block_teleconference_noticeboard'),$attributes);
		$mform->setType('time',PARAM_NOTAGS);	
		$mform->addRule('time',get_string('requiredError','block_teleconference_noticeboard'),'required'); 

		$mform->addElement('date_selector', 'date', get_string('date','block_teleconference_noticeboard'),$attributes);
		// $mform->addElement('text', 'date', get_string('date','block_teleconference_noticeboard'),$attributes);
		$mform->setType('date',PARAM_NOTAGS);	
		$mform->addRule('date',get_string('requiredError','block_teleconference_noticeboard'),'required'); 
	
		$mform->addElement('text', 'teleconfNum', get_string('teleconfNum','block_teleconference_noticeboard'),$attributes);
		$mform->setType('teleconfNum',PARAM_NOTAGS);
		$mform->addRule('teleconfNum',get_string('teleconfNumError','block_teleconference_noticeboard'),'numeric'); 		

		$mform->addElement('text', 'teleconfPass', get_string('teleconfPass','block_teleconference_noticeboard'),$attributes);
		$mform->setType('teleconfPass',PARAM_NOTAGS);	
	
        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);
        $mform->setDefault('courseid', $COURSE->id);
	
		$objs = array();
        $objs[] =& $mform->createElement('submit', '', get_string('submitBtn', 'block_teleconference_noticeboard'));
        $objs[] =& $mform->createElement('cancel', '', get_string('cancelBtn', 'block_teleconference_noticeboard'));
        $grp =& $mform->addElement('group', 'buttonsgrp', "Options", $objs, array(' ', '<br />'), false);
	
	}
	
	function validation($data,$files){ // check that the form is not empty
		$errors=parent::validation($data,$files);
		if (empty($data['link'])){
			$errors['link']='Link is required';
		}
		if (empty($data['time'])){
			$errors['time']='Time is required';
		}
		if (empty($data['date'])){
			$errors['date']='Date is required';
		}		
		return $errors;
	}
}	

class teleconf_edit_session_info extends moodleform{
	function definition(){
		global $COURSE;
		$sesId    = $this->_customdata['sesId'];
		$sesTitle = $this->_customdata['sesTitle'];
		$sesUrl   = $this->_customdata['sesUrl'];
		$sesTime  = $this->_customdata['sesTime'];
		$sesDate  = $this->_customdata['sesDate'];
		$sesNum	  = $this->_customdata['sesNumber'];
		$sesPass  = $this->_customdata['sesPassword'];
		$mform=$this->_form;
		$mform->addElement('header', 'general', get_string('general'));
		
		$attributes='size="70"';
		$mform->addElement('text', 'session_title', get_string('sessionTitle','block_teleconference_noticeboard'),$attributes);
		$mform->setType('session_title',PARAM_NOTAGS);
		$mform->setDefault('session_title', $sesTitle);
		
		$mform->addElement('text', 'link', get_string('link','block_teleconference_noticeboard'),$attributes);
		$mform->setType('link',PARAM_NOTAGS);	
		$mform->addRule('link',get_string('requiredError','block_teleconference_noticeboard'),'required');
        $mform->setDefault('link', $sesUrl);
	
		$mform->addElement('text', 'time', get_string('time','block_teleconference_noticeboard'),$attributes);
		$mform->setType('time',PARAM_NOTAGS);	
		$mform->addRule('time',get_string('requiredError','block_teleconference_noticeboard'),'required'); 
		$mform->setDefault('time', $sesTime);

		//$mform->addElement('text', 'date', get_string('date','block_teleconference_noticeboard'),$attributes);
		$mform->addElement('date_selector', 'date', get_string('date','block_teleconference_noticeboard'),$attributes);
		$mform->setType('date',PARAM_NOTAGS);	
		$mform->addRule('date',get_string('requiredError','block_teleconference_noticeboard'),'required'); 
		$mform->setDefault('date', $sesDate);
	
		$mform->addElement('text', 'teleconfNum', get_string('teleconfNum','block_teleconference_noticeboard'),$attributes);
		$mform->setType('teleconfNum',PARAM_NOTAGS);	
		$mform->setDefault('teleconfNum', $sesNum);
		$mform->addRule('teleconfNum',get_string('teleconfNumError','block_teleconference_noticeboard'),'numeric'); 

		$mform->addElement('text', 'teleconfPass', get_string('teleconfPass','block_teleconference_noticeboard'),$attributes);
		$mform->setType('teleconfPass',PARAM_NOTAGS);	
		$mform->setDefault('teleconfPass', $sesPass);
	
        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);
        $mform->setDefault('courseid', $COURSE->id);
		
		$mform->addElement('hidden', 'sessionId');
        $mform->setType('sessionId', PARAM_INT);
        $mform->setDefault('sessionId', $sesId);
	
		$objs = array();
        $objs[] =& $mform->createElement('submit', '', get_string('saveBtn', 'block_teleconference_noticeboard'));
        $objs[] =& $mform->createElement('cancel', '', get_string('cancelBtn', 'block_teleconference_noticeboard'));
        $grp =& $mform->addElement('group', 'buttonsgrp', "Options", $objs, array(' ', '<br />'), false);
	
	}
	
	function validation($data,$files){ // check that the form is not empty
		$errors=parent::validation($data,$files);
		if (empty($data['link'])){
			$errors['link']='Link is required';
		}
		if (empty($data['time'])){
			$errors['time']='Time is required';
		}
		if (empty($data['date'])){
			$errors['date']='Date is required';
		}
		return $errors;
	}
}

class teleconf_delete_session_info extends moodleform{
	function definition(){
		global $COURSE;
		$sesId    = $this->_customdata['sesId'];
		$sesTitle = $this->_customdata['sesTitle'];
		$sesUrl   = $this->_customdata['sesUrl'];
		$sesTime  = $this->_customdata['sesTime'];
		$sesDate  = $this->_customdata['sesDate'];
		$sesNum	  = $this->_customdata['sesNumber'];
		$sesPass  = $this->_customdata['sesPassword'];
		$mform=$this->_form;
		$mform->addElement('header', 'general', get_string('general'));
		
		$attributesDelete='size="70", disabled';
		$mform->addElement('text', 'session_title', get_string('sessionTitle','block_teleconference_noticeboard'),$attributesDelete); 
		$mform->setType('session_title',PARAM_NOTAGS);
		$mform->setDefault('session_title', $sesTitle);
		
		$mform->addElement('text', 'link', get_string('link','block_teleconference_noticeboard'),$attributesDelete);
		$mform->setType('link',PARAM_NOTAGS);	
        $mform->setDefault('link', $sesUrl);
	
		$mform->addElement('text', 'time', get_string('time','block_teleconference_noticeboard'),$attributesDelete);
		$mform->setType('time',PARAM_NOTAGS);	
		$mform->setDefault('time', $sesTime);

		$mform->addElement('text', 'date', get_string('date','block_teleconference_noticeboard'),$attributesDelete);
		$mform->setType('date',PARAM_NOTAGS);	
		$mform->setDefault('date', $sesDate);
	
		$mform->addElement('text', 'teleconfNum', get_string('teleconfNum','block_teleconference_noticeboard'),$attributesDelete);
		$mform->setType('teleconfNum',PARAM_NOTAGS);	
		$mform->setDefault('teleconfNum', $sesNum);

		$mform->addElement('text', 'teleconfPass', get_string('teleconfPass','block_teleconference_noticeboard'),$attributesDelete);
		$mform->setType('teleconfPass',PARAM_NOTAGS);	
		$mform->setDefault('teleconfPass', $sesPass);
	
        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);
        $mform->setDefault('courseid', $COURSE->id);
		
		$mform->addElement('hidden', 'sessionId');
        $mform->setType('sessionId', PARAM_INT);
        $mform->setDefault('sessionId', $sesId);
	
		$objs = array();
        $objs[] =& $mform->createElement('submit', '', get_string('deleteBtn', 'block_teleconference_noticeboard'));
        $objs[] =& $mform->createElement('cancel', '', get_string('cancelBtn', 'block_teleconference_noticeboard'));
        $grp =& $mform->addElement('group', 'buttonsgrp', "Options", $objs, array(' ', '<br />'), false);
	
	}
	
}

class teleconf_add_recorded_session extends moodleform{
	function definition(){
	global $COURSE;
		$mform=$this->_form;
		$mform->addElement('header', 'general', get_string('general'));
		
		$attributes='size="70"';
		$mform->addElement('text', 'session_title', 'Session title',$attributes);
		$mform->setType('session_title',PARAM_NOTAGS);
		$mform->addRule('session_title',get_string('requiredError','block_teleconference_noticeboard'),'required');
		
		$mform->addElement('text', 'link', 'Link',$attributes);
		$mform->setType('link',PARAM_NOTAGS);	
		$mform->addRule('link',get_string('requiredError','block_teleconference_noticeboard'),'required');
	
        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);
        $mform->setDefault('courseid', $COURSE->id);
	
		$objs = array();
        $objs[] =& $mform->createElement('submit', '', get_string('submitBtn', 'block_teleconference_noticeboard'));
        $objs[] =& $mform->createElement('cancel', '', get_string('cancelBtn', 'block_teleconference_noticeboard'));
        $grp =& $mform->addElement('group', 'buttonsgrp', "Options", $objs, array(' ', '<br />'), false);
	
	}
	
	function validation($data,$files){ // check that the form is not empty
		$errors=parent::validation($data,$files);
		if (empty($data['session_title'])){
			$errors['session_title']='Session title is required';
		}
		if (empty($data['link'])){
			$errors['link']='Link is required';
		}
		return $errors;
	}
	
}

class teleconf_edit_recorded_session extends moodleform{
	public function definition(){
	global $COURSE;
		$sesTitle = $this->_customdata['sesTitle'];
		$sesUrl   = $this->_customdata['sesUrl'];
		$sesId	  = $this->_customdata['sesId'];
		$mform=$this->_form;
		$mform->addElement('header', 'general', get_string('general'));
		$attributes='size="70"';
		$mform->addElement('text', 'session_title', 'Session title',$attributes);
		$mform->setType('session_title',PARAM_NOTAGS);
		$mform->addRule('session_title',get_string('requiredError','block_teleconference_noticeboard'),'required');
		$mform->setDefault('session_title', $sesTitle);
		 
		$mform->addElement('text', 'link', 'Link',$attributes);
		$mform->setType('link',PARAM_NOTAGS);
		$mform->addRule('link',get_string('requiredError','block_teleconference_noticeboard'),'required');
		$mform->setDefault('link', $sesUrl);
		
        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);
        $mform->setDefault('courseid', $COURSE->id);

        $mform->addElement('hidden', 'recordedSessionId');
        $mform->setType('recordedSessionId', PARAM_INT);
        $mform->setDefault('recordedSessionId', $sesId);
	
		$objs = array();
        $objs[] =& $mform->createElement('submit', '', get_string('saveBtn', 'block_teleconference_noticeboard'));
        $objs[] =& $mform->createElement('cancel', '', get_string('cancelBtn', 'block_teleconference_noticeboard'));
        $grp =& $mform->addElement('group', 'buttonsgrp', "Options", $objs, array(' ', '<br />'), false);
	
	}
	
	public function validation($data,$files){ // check that the form is not empty
		$errors=parent::validation($data,$files);
		if (empty($data['session_title'])){
			$errors['session_title']='Session title is required';
		}
		if (empty($data['link'])){
			$errors['link']='Link is required';
		}
		return $errors;
	}
	
}

class teleconf_delete_recorded_session extends moodleform{
	public function definition(){
	global $COURSE, $CFG;
		$sesTitle = $this->_customdata['sesTitle'];
		$sesUrl   = $this->_customdata['sesUrl'];
		$sesId	  = $this->_customdata['sesId'];
		
		$mform=$this->_form;
		$mform->addElement('header', 'general', get_string('general')); 
		
		$attributesDelete='size="70", disabled';
		
		$mform->addElement('text', 'session_title', 'Session title',$attributesDelete);
		$mform->setType('session_title',PARAM_NOTAGS);
		$mform->setDefault('session_title', $sesTitle);
		      
		$mform->addElement('text', 'link', 'Link',$attributesDelete);
		$mform->setType('link',PARAM_NOTAGS);	
		$mform->setDefault('link', $sesUrl);
		      
        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);
        $mform->setDefault('courseid', $COURSE->id);
		      
        $mform->addElement('hidden', 'recordedSessionId');
        $mform->setType('recordedSessionId', PARAM_INT);
        $mform->setDefault('recordedSessionId', $sesId);

		$objs = array();
        $objs[] =& $mform->createElement('submit', '', get_string('deleteBtn', 'block_teleconference_noticeboard'));
        $objs[] =& $mform->createElement('cancel', '', get_string('cancelBtn', 'block_teleconference_noticeboard'));
        $grp =& $mform->addElement('group', 'buttonsgrp', "Options", $objs, array(' ', '<br />'), false);
	  
		// $mform->addElement('submit','',get_string('deleteBtn', 'block_teleconference_noticeboard'));
		// $mform->addElement('cancel','',get_string('cancelBtn', 'block_teleconference_noticeboard'));
	
	}
	
	
}