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
 * block faculty dashboard rendrer
 *
 * @package    faculty dashboard
 * @copyright  2024 Vivenns Global
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;
// use core_component;
class block_facultydashboard_renderer extends plugin_renderer_base {

	public function get_facultydashboard($filter = false){
        global $USER; 

        $systemcontext = \context_system::instance();

        $options = array('targetID' => 'dashboard','perPage' => 4, 'cardClass' => 'w_one', 'viewType' => 'card');
        
        $options['methodName']='block_facultydashboard';
        $options['templateName']='block_facultydashboard/view-cards'; 
        $options = json_encode($options);

        $dataoptions = json_encode(array('userid' =>$USER->id,'contextid' => $systemcontext->id));
        $filterdata = json_encode(array());

        $context = [
                'targetID' => 'dashboard',
                'options' => $options,
                'dataoptions' => $dataoptions,
                'filterdata' => $filterdata
        ];

        if($filter){
            return  $context;
        }else{
            return  $this->render_from_template('local_rolemanagement/cardPaginate', $context);
        }
    }

    /**
     * @method list of accounts
     * @todo To add action buttons
     */
    public function courses_list($stable,$filterdata, $userid = 0) {
        global $DB, $PAGE,$USER,$CFG,$OUTPUT;
        require_once($CFG->dirroot . '/blocks/facultydashboard/lib.php');
        $systemcontext = context_system::instance(); 
        $params = array();
        $userid = $userid > 0 ? $userid : $USER->id;
        $countsql = "SELECT count(DISTINCT c.id) ";

        $selectsql = "SELECT c.id as courseid, c.shortname, c.category, c.fullname AS my_courses, c.startdate AS startdate, c.enddate AS enddate, f.id as forumid, COUNT(DISTINCT a.id) as assign_count, COUNT(DISTINCT q.id) as 
            quiz_count, COUNT(DISTINCT f.id) as forum_count";

        $formsql = " FROM mdl_course AS c
            LEFT JOIN mdl_forum AS f ON f.course = c.id AND f.type != 'news'
            LEFT JOIN mdl_assign AS a ON a.course = c.id
            LEFT JOIN mdl_quiz AS q ON q.course = c.id
            JOIN mdl_enrol AS e ON e.courseid = c.id
            JOIN mdl_user_enrolments AS ue ON ue.enrolid = e.id
            JOIN mdl_user AS u ON u.id = ue.userid
            WHERE c.visible = 1 AND u.id = $userid";

        $ordersql = " GROUP by c.id";
        $groupsql = " ORDER BY c.id DESC";

        $totalcourses = $DB->count_records_sql($countsql.$formsql.$groupsql,$params);
        
        $mycourses = $DB->get_records_sql($selectsql.$formsql.$ordersql,$params,$stable->start,$stable->length);
        $data = array();
        foreach ($mycourses as $courses) {

                $url = $CFG->wwwroot."/course/view.php?id=".$courses->courseid;

                require_once($CFG->dirroot . '/blocks/facultydashboard/groupuser.php');

                $studentcount = group_member($userid, $courses->courseid);

                $courses_record = array();
                $courses->id = $courses->courseid;
                $courseimage = get_courses_image($courses);
                $catname =  $DB->get_field('course_categories','name',array('id' => $courses->category));
                $courses_record['catname'] = $catname ? $catname : 'NA';
                $courses_record['coursepic'] = $courseimage['imageurl'];
                $courses_record['imgurlflag'] = $courseimage['imgurlflag'];
                $courses_record['userid'] = $userid;              
                $courses_record['courseid'] = $courses->courseid; 
                $courses_record['url'] = $url; 
                $courses_record['studentcount'] = $studentcount;
                $courses_record['startdate'] = $courses->startdate?userdate($courses->startdate,'%d/%m/%Y'):'Not Available';
                $courses_record['enddate'] = $courses->enddate?userdate($courses->enddate,'%d/%m/%Y'):'Not Available';

                $coursetitle = $courses->my_courses;
                $courses_record['shortname'] = $courses->shortname;
                $max = 25;
                if(strlen($coursetitle) > $max) {
                $coursestring =  substr($coursetitle, 0, $max). "....";
                } else {
                  $coursestring = $coursetitle;
                 }
              
                $courses_record['my_courses'] = $coursestring; 
                $courses_record['my_coursesfull'] = $coursetitle;
                $courses_record['assign_count'] = $courses->assign_count; 
                $courses_record['quiz_count']= $courses->quiz_count;  
                $courses_record['forum_count']= $courses->forum_count;
                $courses_record['forumid']= $courses->forumid;
                $data[] = $courses_record;

        }
        return array('totalcount' => $totalcourses,'data' => $data);
    }
}