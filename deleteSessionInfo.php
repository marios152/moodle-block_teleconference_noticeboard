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
require_once dirname(dirname(dirname(__FILE__))) . '/config.php';
require_once ('teleconf_forms.php');
require_once ('teleconf_functions.php');
global $PAGE,$OUTPUT,$COURSE,$CFG, $USER, $DB;

$courseid = optional_param('courseid', SITEID, PARAM_INT);
$sessionId = optional_param('sessionId', SITEID, PARAM_INT);
$context = context_course::instance($courseid);
/*
	from moodle/calendar/event.php  -> line 91 (moodle patch->3.0)
*/
if ($courseid != SITEID && !empty($courseid)) {
    $course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
    $courses = array($course->id => $course);
} else {
    $course = get_site();
}
require_login($course, false); // this is required as well for the navigation bar (path). 
$PAGE->set_pagelayout('admin');
$PAGE->set_context($context);
$PAGE->set_title('Delete Session');
$PAGE->set_url($CFG->wwwroot.'/blocks/teleconference_noticeboard/deleteSessionInfo.php');
$get_rec= get_session_info_by_id($sessionId);
$sesTitle= $get_rec->teleconf_title;
$sesUrl= $get_rec->teleconf_url;
$sesId= $get_rec->id;
$sesTime=$get_rec->teleconf_time;
$sesDate=$get_rec->teleconf_date;
$sesNumber=$get_rec->teleconf_number;
$sesPassword=$get_rec->teleconf_pass;
$sesUserId=$get_rec->userid;
$sessionForm = new teleconf_delete_session_info(NULL, array('sesTitle'=>$sesTitle,'sesUrl'=>$sesUrl,'sesId'=>$sesId,'sesTime'=>$sesTime,'sesDate'=>$sesDate,'sesNumber'=>$sesNumber,'sesPassword'=>$sesPassword,'sesUserId'=>$sesUserId));
$data = $sessionForm->get_data(); // form submitted

if ($sessionForm->is_cancelled()){ //form cancelled
	navigateToCourse($course->id);
}else if( $data ){ // when form is submitted  
	$sessionArr=[];
	$sessionArr['id']= $data->sessionId;
	$sessionArr['userid']= $data->sesUserID;
	$sessionArr['courseid']= $data->courseid;
	$sessionArr['teleconf_title']= $data->session_title; 
    $sessionArr['teleconf_url']= $data->link;
    $sessionArr['teleconf_time']= $data->time;
    $sessionArr['teleconf_date']= $data->date;
    $sessionArr['teleconf_number']= $data->teleconfNum;
    $sessionArr['teleconf_pass']= $data->teleconfPass;
	$DB->delete_records('teleconf_info',$sessionArr); 
	navigateToCourse($data->courseid);
}
$PAGE->set_heading($course->fullname);
$PAGE->navbar->add('Delete Session', new moodle_url('/blocks/teleconference_noticeboard/deleteSessionInfo.php', array('courseid'=>$course->id, 'sessionId'=>$sessionId)));
echo $OUTPUT->header();
echo "<h1 style='color:red;'>".get_string('deleteSessionQstn', 'block_teleconference_noticeboard')."</h1>";
$sessionForm->display();
echo $OUTPUT->footer();

























