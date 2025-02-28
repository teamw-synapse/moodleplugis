<?php
class block_facultydashboard extends block_base {

    public function init() {
        $this->title = 'Faculty Dashboard';
    }

    public function get_content() {
      global $USER, $DB, $CFG, $PAGE, $OUTPUT;

      require_once($CFG->dirroot.'/blocks/facultydashboard/filterform.php');

        if ($this->content !== null) {
            return $this->content;
        }

          $this->content = new stdClass();
          $totalcourses = $DB->count_records_sql('SELECT 
                count(c.id) FROM mdl_user u 
                INNER JOIN {role_assignments} ra ON ra.userid = u.id
                INNER JOIN {context} ct ON ct.id = ra.contextid
                INNER JOIN {course} c ON c.id = ct.instanceid
                INNER JOIN {role} r ON r.id = ra.roleid
                WHERE ra.userid =:userid and ra.roleid in (3,4,9,20,17) AND c.visible = 1 AND ct.contextlevel = 50 AND ra.userid > 2',array('userid'=>$USER->id));
          if(is_siteadmin()){

            $mform = new facultyfilter_form();
            $this->content->text = $mform->render();
            
            $this->content->text .= '<div class="d-flex justify-content-end mb-3">
         <input type="text" class="form-control w-25" id="quizusersearch"  placeholder="Search..." aria-label="Search"  onkeyup="(function(e){ require(\'block_admindashboard/quizuser\').search({quizid:'.$args['quizid'].',courseid:'.$args['courseid'].',userid:'.$args['userid'].' }) })(event)">
     </div>'; 
            $this->content->text .= $OUTPUT->render_from_template('block_facultydashboard/coursesview', array());

          } elseif((!is_siteadmin() && $totalcourses > 0)) {

            $output = $PAGE->get_renderer('block_facultydashboard');
            $this->content->text .= $output->get_facultydashboard();

          } else {
            $this->content->text = '';
          }

          $this->content->footer = '';

          return $this->content;
      }

      /**
     * Locations where block can be displayed.
     *
     * @return array
     */
      public function applicable_formats() {
          return array('my' => true);
      }

      /**
       * Allow the block to have a configuration page.
       *
       * @return boolean
       */
      public function has_config() {
          return true;
      }

    public function get_required_javascript() {
      global $DB, $USER;
        $totalcourses =  $DB->count_records_sql('SELECT 
              count(c.id)
              FROM mdl_user u
              INNER JOIN {role_assignments} ra ON ra.userid = u.id
              INNER JOIN {context} ct ON ct.id = ra.contextid
              INNER JOIN {course} c ON c.id = ct.instanceid
              INNER JOIN {role} r ON r.id = ra.roleid
              WHERE ra.userid =:userid and ra.roleid in (4,9,20,17) AND c.visible = 1 AND ct.contextlevel = 50 AND ra.userid > 2
          ',array('userid'=>$USER->id));
        if(is_siteadmin()){
          $this->page->requires->js_call_amd('block_facultydashboard/courses', 'load');
        }else if((!is_siteadmin() && $totalcourses > 0)){
            // $this->page->requires->js('/blocks/facultydashboard/js/template.js');
            $this->page->requires->js_call_amd('block_facultydashboard/courses', 'init',  ['userid'=>$USER->id]);
            
        }

        $this->page->requires->js_call_amd('block_admindashboard/getassign','load');
            $this->page->requires->js_call_amd('block_admindashboard/getquiz','load');
            $this->page->requires->js_call_amd('block_admindashboard/getuser','load');
            $this->page->requires->js_call_amd('block_admindashboard/getforum','load');
            $this->page->requires->js_call_amd('block_admindashboard/assignuser','load');
            $this->page->requires->js_call_amd('block_admindashboard/assignsub','load');
            $this->page->requires->js_call_amd('block_admindashboard/quizuser','load');
            $this->page->requires->js_call_amd('block_admindashboard/quizsub','load');
            $this->page->requires->js_call_amd('block_admindashboard/forumuser','load');
            $this->page->requires->js_call_amd('block_admindashboard/forumsub','load');
            $this->page->requires->js_call_amd('block_admindashboard/forumdisc','load');
      }
}
