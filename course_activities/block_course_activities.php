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
 * Version details
 *
 * @package    block_course_activities
 * @copyright  Ganesh K(ganeshkrishnamurthy24@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
global $CFG;
require_once($CFG->libdir.'/completionlib.php');
require_once($CFG->libdir.'/gradelib.php');

class block_course_activities extends block_base {
    public function init() {
        $this->title = get_string('title', 'block_course_activities');
    }
    public function get_content() {
        if ($this->content !== null) {
            return $this->content;
        }
        $this->content = new stdClass;
        $this->content->text = $this->get_activity_list();
        $this->content->footer = '';
        return $this->content;
    }
    public function applicable_formats() {
        return array( 'course-view' => true);
    }
    public function get_activity_list() {
        global $DB, $CFG, $USER, $COURSE, $OUTPUT;
        //to get all the activities inside the course
        $activities = get_array_of_activities($COURSE->id);
        $html = "";
        if (!empty($activities)) {
            foreach ($activities as $activity) {
                if($DB->get_field('course_modules','deletioninprogress',array('id'=>$activity->cm))){
                    continue;
                }
                $state = $DB->get_field('course_modules_completion', 'id', array('coursemoduleid' => $activity->cm, 'userid' => $USER->id));
                $link = $CFG->wwwroot.'/mod/'.$activity->mod.'/view.php?id='.$activity->cm;
                $data = array('link'=>$link,'cm'=>$activity->id,'name'=>$activity->name,'added'=>date('d-M-Y', $activity->added),'completion'=>(!empty($state)?get_string('completed', 'block_course_activities'):""));
                $html.=$OUTPUT->render_from_template('block_course_activities/list',$data);
            }
        }
        return $html;
    }
}
