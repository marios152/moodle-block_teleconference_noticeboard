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

require_once dirname(dirname(dirname(__FILE__))).'/config.php';
require_once('teleconf_forms.php');
require_once('teleconf_functions.php');
require_login();

class block_teleconference_noticeboard extends block_base {
    function init() {
        $this->title = get_string('pluginname','block_teleconference_noticeboard');
    }
    function get_content() {
        global $DB, $CFG, $OUTPUT, $COURSE,$USER,$PAGE;
		$context = context_system::instance();
		$coursecontext = context_course::instance($COURSE->id);
		/* 
			if authorized (admins - managers) and editing is on then edit 
		*/ 
		$canEdit = canEdit(has_capability('block/teleconference_noticeboard:edit',$coursecontext), $PAGE->user_is_editing());		
		echo "<style>
			hr.myrow {
				border: 0;
				height: 1px;
				background-image: linear-gradient(to right, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0.75), rgba(0, 0, 0, 0));
				background-image: -webkit-linear-gradient(right, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0.75), rgba(0, 0, 0, 0)); /* For Safari 5.1 to 6.0 */
				background-image: -o-linear-gradient(right, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0.75), rgba(0, 0, 0, 0)); /* For Opera 11.1 to 12.0 */
				background-image: -moz-linear-gradient(right, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0.75), rgba(0, 0, 0, 0)); /* For Firefox 3.6 to 15 */
			}
			img.teleconfLogoCenter{
				display: block;
				margin-left: auto;
				margin-right: auto;
				max-width:100%;
				max-height:100%;
			}
			img.teleconfLogoLeft{
				display: block;
				margin-left: 0;
				margin-right: auto;
				max-width:100%;
				max-height:100%;
			}
			img.teleconfLogoRight{
				display: block;
				margin-left: auto;
				margin-right: 0;
				max-width:100%;
				max-height:100%;
			}			
		</style>";
		
        $this->content = new stdClass();
        $this->content->footer = '';
		/*
			Get teleconf image. If no image is set then set as default the webex image. 
			Get the settings from the edit_form.php
		*/
		$fs = get_file_storage();
		$files = $fs->get_area_files($this->context->id, 'block_teleconference_noticeboard', 'content');
		$this->content->text ='';
		$pictureUploaded=0;
		foreach ($files as $file) {
			$filename = $file->get_filename();
    		if ($filename <> '.') {
				$pictureUploaded=1;
				$url = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(), null, $file->get_filepath(), $filename);
				$this->content->text .= '<img class="'.$this->config->position.'" width="'.$this->config->width.'" height="'.$this->config->height.'" src="'.$url.'" alt="'.$filename.'" />';
			}
		}
		/*
			if t`here is no image uploaded then use one of the predefined images below.
		*/
		if($pictureUploaded!=1){ // user already uploaded a picture to use.
			$pngacronym='.png';
			if (!empty($this->config->teleconfLogo)) { 
				$teleconfLogo = $this->config->teleconfLogo;
				$imgUrl=$CFG->wwwroot.'/blocks/teleconference_noticeboard/img/';
				switch ($teleconfLogo){
					case 'webEx':{
						$filename='Webex teleconference logo';
						$this->content->text = '<img class="teleconfLogoCenter" src="'.$imgUrl.$teleconfLogo.$pngacronym.'" alt="'.$filename.'" />';
					}
					break;
					case 'skypeBus':{
						$filename='Skype for business teleconference logo';
						$this->content->text = '<img style="with:142px; height:142px;" class="teleconfLogoCenter" src="'.$imgUrl.$teleconfLogo.$pngacronym.'" alt="'.$filename.'" />';
					}
					break;
					case 'adobeConnect':{
						$filename='Adobe connect teleconference logo';
						$this->content->text = '<img class="teleconfLogoCenter" src="'.$imgUrl.$teleconfLogo.$pngacronym.'" alt="'.$filename.'" />';
					}
					break;
					case 'defaultTeleconf':{
						$filename='Default teleconference logo';
						$this->content->text = '<img class="teleconfLogoCenter" src="'.$imgUrl.$teleconfLogo.$pngacronym.'" alt="'.$filename.'" />';
					}
					break;
					case 'noImage':
						break;
					default:{
						$filename='Webex teleconference logo';
						$this->content->text = '<img class="teleconfLogoCenter" src="'.$imgUrl.$teleconfLogo.$pngacronym.'" alt="'.$filename.'" />';
					}
				}
			}else{
				$imgUrl=$CFG->wwwroot.'/blocks/teleconference_noticeboard/img/';
				$filename='Webex teleconference logo';
				$this->content->text = '<img class="teleconfLogoCenter" src="'.$imgUrl."webEx".$pngacronym.'" alt="'.$filename.'" />';
			}
		}
		/*
		 Output Upcoming Teleconference
		*/
		$action = null; //this was using popup_action() but popping up a fullsize window seems wrong
		$scheduledInfo= get_scheduled_info();
		if($scheduledInfo!=NULL){
			$this->content->footer .= '<h5>'.get_string('howToConnectInstr', 'block_teleconference_noticeboard').'</h5>';	
			foreach($scheduledInfo as $session){
				$this->content->footer .='<hr class="myrow"/>';
				if ($canEdit){
					$editSessionURL = new moodle_url("/blocks/teleconference_noticeboard/editSessionInfo.php", array('courseid'=>$COURSE->id, 'sessionId'=>$session->id));
					$deleteSessionURL = new moodle_url("/blocks/teleconference_noticeboard/deleteSessionInfo.php", array('courseid'=>$COURSE->id, 'sessionId'=>$session->id));
					$this->content->footer .='<div style="float:right;">';
					$this->content->footer .='<a href="'.$editSessionURL.'"><img style="width:15px; height:15px;" src="'.$CFG->wwwroot."/blocks/teleconference_noticeboard/img/edit.png".'"></img></a>';
					$this->content->footer .='&nbsp;';
					$this->content->footer .='<a href="'.$deleteSessionURL.'"><img style=" width:15px; height:15px;" src="'.$CFG->wwwroot."/blocks/teleconference_noticeboard/img/bin.png".'"></img></a>';
					$this->content->footer .='</div>';
				}
				$this->content->footer .='<p style="font-weight: bold;">'.get_string('date', 'block_teleconference_noticeboard').': <span style="color:#ff0000">'.date('d/m/y',$session->teleconf_date).'</span></p>';
				$this->content->footer .='<p style="font-weight: bold;">'.get_string('time', 'block_teleconference_noticeboard').': <span style="color:#ff0000">'.$session->teleconf_time.'</span></p>';
				$popupaction = new popup_action('click',$session->teleconf_url, 'teleconf', array('height' => 500, 'width' => 600)); //for popup
				if ($session->teleconf_title!= null){ // if the title is blank then use the predifined one ( Click here to join )
					$this->content->footer .='<p>'.get_string('joinSession', 'block_teleconference_noticeboard').'&nbsp;&nbsp;';
					$this->content->footer .= $OUTPUT->action_link($session->teleconf_url,$session->teleconf_title, $popupaction).'</p>';  /* 'http://' is always needed  ->popup in action  */
				}else{
					$this->content->footer .='<p>'.get_string('joinSession', 'block_teleconference_noticeboard').'&nbsp;&nbsp;';
					$this->content->footer .= $OUTPUT->action_link($session->teleconf_url,get_string('clickHereJoin', 'block_teleconference_noticeboard'), $popupaction).'</p>';  /* 'http://' is always needed  ->popup in action  */
				}
				if($session->teleconf_number !=0){
					$this->content->footer .='<p><span style="font-weight: bold;">'.get_string('meetingNumber', 'block_teleconference_noticeboard').' : </span><span style="font-weight: bold; color: #ff0000;">'.$session->teleconf_number.'</span></p>';
				}
				if($session->teleconf_pass !=NULL){
					$this->content->footer .='<p><span style="font-weight: bold;">'.get_string('meetingPassword', 'block_teleconference_noticeboard').': </span><span style="font-weight: bold; color: #ff0000;">'.$session->teleconf_pass.'</span></p>';			
				}
			}
		$this->content->footer .='<hr class="myrow"/>';
		}else{
			$this->content->footer .= '<h5>'.get_string('noSessionsYet', 'block_teleconference_noticeboard').'</h5>';	
		}
		/*
		 Output Recorded Sessions
		*/
		$recordedSession= get_recorded_session_info();
		if($recordedSession!=NULL){
		$this->content->footer .= '<h5>'.get_string('recordedSessionsTitle', 'block_teleconference_noticeboard').'</h5>';
		$this->content->footer .='<table cellpadding=3>';
			foreach($recordedSession as $rs){
				$popupaction = new popup_action('click',$rs->recorded_teleconf_url, 'teleconf', array('height' => 400, 'width' => 600));
				$this->content->footer .='<tr>';
				$this->content->footer .='<td>';
				$this->content->footer .='<img style="width:15px; height:15px;" src="'.$CFG->wwwroot."/blocks/teleconference_noticeboard/img/headset_icon.png".'"></img>';
				$this->content->footer .='</td>';
				$this->content->footer .='<td>';
				$this->content->footer .= $OUTPUT->action_link($rs->recorded_teleconf_url,$rs->recorded_teleconf_title, $popupaction);  // 'http://' is always needed 
				$this->content->footer .='</td>';
				if ($canEdit){
					$editRecordedSessionURL = new moodle_url("/blocks/teleconference_noticeboard/editRecordedSession.php", array('courseid'=>$COURSE->id, 'recordedSessionId'=>$rs->id));
					$deleteRecordedSessionURL = new moodle_url("/blocks/teleconference_noticeboard/deleteRecordedSession.php", array('courseid'=>$COURSE->id, 'recordedSessionId'=>$rs->id));
					$this->content->footer .='<td>';
					$this->content->footer .='<a href="'.$editRecordedSessionURL.'"><img style="width:15px; height:15px;" src="'.$CFG->wwwroot."/blocks/teleconference_noticeboard/img/edit.png".'"></img></a>';
					$this->content->footer .='</td>';
					$this->content->footer .='<td>';
					$this->content->footer .='<a href="'.$deleteRecordedSessionURL.'"><img style=" width:15px; height:15px;" src="'.$CFG->wwwroot."/blocks/teleconference_noticeboard/img/bin.png".'"></img></a>';
					$this->content->footer .='</td>';
				}
				$this->content->footer .='</tr>';
			}
		$this->content->footer .='</table>';
		}
		/*
			buttons for navigating to forms
		*/		
		if ($canEdit){
			$this->content->footer .='<hr class="myrow"/>';	
			$addRecordedSessionURL = new moodle_url("/blocks/teleconference_noticeboard/addRecordedSession.php", array('courseid'=>$COURSE->id));
			$addSessionInfoURL = new moodle_url("/blocks/teleconference_noticeboard/addSessionInfo.php", array('courseid'=>$COURSE->id));
			$this->content->footer .="<table cellpadding=3>";
			$this->content->footer .= "<tr><td><img style=' width:15px; height:15px;' src=".$CFG->wwwroot."/blocks/teleconference_noticeboard/img/plus.png"."></img></td><td><a href='".$addSessionInfoURL."'>".get_string('addSessionBtn', 'block_teleconference_noticeboard')."</a></td></tr>";
			$this->content->footer .= "<tr><td><img style=' width:15px; height:15px;' src=".$CFG->wwwroot."/blocks/teleconference_noticeboard/img/plus.png"."></img></td><td><a href='".$addRecordedSessionURL."'>".get_string('addRecordingBtn', 'block_teleconference_noticeboard')."</a></td></tr>";
			$this->content->footer .="</table>";
		}
        return $this->content;
    }
    public function applicable_formats() {
        return array(
			'all' => false,
    		'site' => true,
		    'site-index' => true,
		    'course-view' => true
		);
    }
    public function instance_allow_multiple() {
          return true;
    }
    function has_config() {return true;}
}
