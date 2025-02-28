<?php
class block_userdashboard extends block_base {
    public function init() {
        $this->title = 'User Dashboard';
    }

    public function get_content() {
       global $USER, $DB, $CFG,$PAGE,$OUTPUT;
       require_once($CFG->dirroot . '/blocks/userdashboard/filterform.php');
        if ($this->content !== null) {
            return $this->content;
        }
        $this->content =  new stdClass;
        if(is_siteadmin()){
            $mform = new filterform_form();
            $this->content->text = $mform->render();
            // $this->content->text .= $OUTPUT->render_from_template('block_userdashboard/script', array());
            //$this->content->text .= $OUTPUT->render_from_template('block_userdashboard/maintabs', array());
            $this->content->text .= '<script src="https://cdn.jsdelivr.net/npm/chart.js"></script><div id="userdashboardcontent"></div>';
        }else{
            $totalcourses =  $DB->count_records_sql('SELECT 
                    count(c.id)
                    FROM mdl_user u
                    INNER JOIN {role_assignments} ra ON ra.userid = u.id
                    INNER JOIN {context} ct ON ct.id = ra.contextid
                    INNER JOIN {course} c ON c.id = ct.instanceid
                    INNER JOIN {role} r ON r.id = ra.roleid
                    WHERE ra.userid =:userid and ra.roleid = 5 AND c.visible = 1 AND ct.contextlevel = 50
                ',array('userid'=>$USER->id));
            if(!is_siteadmin() && $totalcourses > 0){
                // $this->content->text = $OUTPUT->render_from_template('block_userdashboard/script', array());
                $this->content->text .= '<script src="https://cdn.jsdelivr.net/npm/chart.js"></script><div id="userdashboardcontent"></div>';
                $this->content->text .= $OUTPUT->render_from_template('block_userdashboard/maintabsnew', array('userid' => $USER->id));
            }
        }
        

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
        $this->page->requires->js_call_amd('block_userdashboard/userdashboard', 'load');
        // $this->page->requires->js('/blocks/userdashboard/js/chart.js');
    }
}
