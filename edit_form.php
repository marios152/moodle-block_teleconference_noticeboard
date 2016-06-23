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
class block_teleconference_noticeboard_edit_form extends block_edit_form {

    protected function specific_definition($mform) {  
		/*
			Let the user chose the image he/she wants to have as a logo for the block
		*/
        // Section header title according to language file.
        $mform->addElement('header', 'configheader', get_string('config_teleconfLogo', 'block_teleconference_noticeboard'));	
		$arrayOptions = array('webEx'=>'WebEx','skypeBus'=>'Skype for business','adobeConnect'=>'Adobe Connect','defaultTeleconf'=>'Default','noImage'=>'No image');
		//  'config_' is needed in front of the declaration of the element. Otherwise you will not be able to retrieve it in your block when you edit it
		$mform->addElement('select', 'config_teleconfLogo', get_string('config_teleconfLogo', 'block_teleconference_noticeboard'),$arrayOptions , null);  
        $mform->addHelpButton('config_teleconfLogo', 'config_teleconfLogo', 'block_teleconference_noticeboard');
		//upload image
		$mform->addElement('filemanager', 'config_attachments', get_string('config_attachments', 'block_teleconference_noticeboard'), null,
							array('subdirs' => 0, 'maxbytes' => 5000000, 'maxfiles' => 1,
							'accepted_types' => array('.png', '.jpg', '.gif') ));
		$mform->addHelpButton('config_attachments', 'config_attachments', 'block_teleconference_noticeboard');
		//image width
        $mform->addElement('text', 'config_width', get_string('config_width', 'block_teleconference_noticeboard'));
        $mform->setDefault('config_width', '200');
        $mform->setType('config_width', PARAM_RAW);
		$mform->addHelpButton('config_width', 'config_width', 'block_teleconference_noticeboard');
        //image height
        $mform->addElement('text', 'config_height', get_string('config_height', 'block_teleconference_noticeboard'));
        $mform->setDefault('config_height', '116');
        $mform->setType('config_height', PARAM_RAW);
		$mform->addHelpButton('config_height', 'config_height', 'block_teleconference_noticeboard');
		//image position (left - middle - right)
		$skillsarray = array(
			'teleconfLogoLeft' => 'left',
			'teleconfLogoCenter' => 'center',
			'teleconfLogoRight' => 'right'
		);
		$mform->addElement('select', 'config_position', get_string('config_position', 'block_teleconference_noticeboard'), $skillsarray);
		$mform->getElement('config_position')->setMultiple(false);
		$mform->getElement('config_position')->setSelected('middle');
		$mform->addHelpButton('config_position', 'config_position', 'block_teleconference_noticeboard');
    }
	
    function set_data($defaults) {

        if (empty($entry->id)) {
            $entry = new stdClass;
            $entry->id = null;
        }

        $draftitemid = file_get_submitted_draft_itemid('config_attachments');

        file_prepare_draft_area($draftitemid, $this->block->context->id, 'block_teleconference_noticeboard', 'content', 0,
        array('subdirs'=>true));

        $entry->attachments = $draftitemid;

        parent::set_data($defaults);
        if ($data = parent::get_data()) {
            file_save_draft_area_files($data->config_attachments, $this->block->context->id, 'block_teleconference_noticeboard', 'content', 0, 
            array('subdirs' => true));
        }
    }	
}
