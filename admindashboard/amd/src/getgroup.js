 /**
 * Add a create new group modal to the page.
 *
 * @module     local_studentdashboard/register
 * @class      register
 * @package    local_studentdashboard
 * @copyright  2017 Shivani
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/str', 'core/modal_factory', 'core/modal_events', 'core/fragment', 'core/templates', 'core/ajax', 'core/yui'],
        function( $,  Str, ModalFactory, ModalEvents, Fragment, Templates, Ajax, Y) {

    /**
     * Constructor
     *
     * @param {String} selector used to find triggers for the new group modal.
     * @param {int} contextid
     *
     * Each call to init gets it's own instance of this class.
     */
    var register = function( args) {

      
        this.contextid = args.contextid;
       
        this.courseid = args.courseid;
        //this.coursemoduleid = args.coursemoduleid;

        var self = this;
        self.init( args);
    };

    /**
     * @var {Modal} modal
     * @private
     */
    register.prototype.modal = null;

    /**
     * @var {int} contextid
     * @private
     */
    register.prototype.contextid = -1;

    /**
     * Initialise the class.
     *
     * @param {String} selector used to find triggers for the new group modal.
     * @private
     * @return {Promise}
     */
    register.prototype.init = function(args) {
      
        var self = this;
             var head =  Str.get_string('creategroup', 'block_admindashboard');

            
            return head.then(function(title) {
               
                return ModalFactory.create({
                    type: ModalFactory.types.SAVE_CANCEL,
                    title: title,
                    body: self.getBody()
                    // footer: this.getFooter()
                });
            }.bind(self)).then(function(modal) {

                
               self.modal = modal;
               
               
            
                self.modal.setLarge();

           

            // this.modal.getRoot().on('submit', 'form', function(form) {
            //     self.submitFormAjax(form, self.args);
            // });
            this.modal.show();
            this.modal.getRoot().animate({"right":"0%"}, 500);
            $(".close").click(function(){
                  modal.destroy();
            });

            // We catch the modal save event, and use it to submit the form inside the modal.
            // Triggering a form submission will give JS validation scripts a chance to check for errors.
            this.modal.getRoot().on(ModalEvents.save, this.submitForm.bind(this));
            // We also catch the form submit event and use it to submit the form with ajax.
            this.modal.getRoot().on('submit', 'form', this.submitFormAjax.bind(this));

            return this.modal;

            }.bind(this));

    };

    /**
     * @method getBody
     * @private
     * @return {Promise}
     */
    register.prototype.getBody = function(formdata) {
        // params = {};
        // params.contextid = this.contextid;
        // params.courseid = this.courseid;
        //params.coursemoduleid = this.coursemoduleid;
        // params.uid    = this.uid;
        // params.employee = this.employee; 
        if (typeof formdata === "undefined") {
            formdata = {};
        }
        // Get the content of the modal.
        var params = {jsonformdata: JSON.stringify(formdata),courseid:this.courseid};
       
        return Fragment.loadFragment('block_admindashboard', 'create_groups', this.contextid,  params);  
       
       
                
    };
     register.prototype.getFooter = function() {     
        
        
     
        $footer = '<button type="button" class="btn btn-secondary" data-action="cancel">Cancel</button>';
        return $footer;
    };

    /**
     * @method handleFormSubmissionResponse
     * @private
     * @return {Promise}
     */
    register.prototype.handleFormSubmissionResponse = function() {
        this.modal.hide();
        // We could trigger an event instead.
        // Yuk.
        Y.use('moodle-core-formchangechecker', function() {
            M.core_formchangechecker.reset_form_dirty_state();
        });

        
        var value =  0;
        var sub =  0;
        var subval =  $('#id_enrollcourse').val();
        var promises = Ajax.call([{   
            methodname: 'report_category', 
                args: {
                    id: value,
                    subid: sub,
                    unitid: subval ? subval : 0
                }
        }]);
        promises[0].done(function(data) {
            subselOpts =" <option value='0'>Select Group Name</option>";
            for (i=0;i<data.groups.length;i++){          
                var subid = data.groups[i]['groupid'];
                var subvalll = data.groups[i]['groupname'];
                subselOpts += "<option value='"+subid+"'>"+subvalll+"</option>";
            }
            $('#id_enrollgroup').children().remove().end().append(subselOpts);
        }).fail(function(ex){

        });

        // document.location.reload();
    };

    /**
     * @method handleFormSubmissionFailure
     * @private
     * @return {Promise}
     */
    register.prototype.handleFormSubmissionFailure = function(data) {
        // Oh noes! Epic fail :(
        // Ah wait - this is normal. We need to re-display the form with errors!
        this.modal.setBody(this.getBody(data));
    };

    /**
     * Private method
     *
     * @method submitFormAjax
     * @private
     * @param {Event} e Form submission event.
     */
    register.prototype.submitFormAjax = function(e) {
        // We don't want to do a real form submission.
        e.preventDefault();

        var changeEvent = document.createEvent('HTMLEvents');
       changeEvent.initEvent('change', true, true);

       // Prompt all inputs to run their validation functions.
       // Normally this would happen when the form is submitted, but
       // since we aren't submitting the form normally we need to run client side
       // validation.
       this.modal.getRoot().find(':input').each(function(index, element) {
           element.dispatchEvent(changeEvent);
       });

       // Now the change events have run, see if there are any "invalid" form fields.
       var invalid = $.merge(
           this.modal.getRoot().find('[aria-invalid="true"]'),
           this.modal.getRoot().find('.error')
       );

       // If we found invalid fields, focus on the first one and do not submit via ajax.
       if (invalid.length) {
           invalid.first().focus();
           return;
       }

        // Convert all the form elements values to a serialised string.
        var formData = this.modal.getRoot().find('form').serialize();

        // Now we can continue...
        Ajax.call([{
            methodname: 'block_admindashboard_submit_create_group_form',
            args: {contextid: this.contextid, jsonformdata: JSON.stringify(formData)},
            done: this.handleFormSubmissionResponse.bind(this, formData),
            fail: this.handleFormSubmissionFailure.bind(this, formData)
        }]);
    };

    /**
     * This triggers a form submission, so that any mform elements can do final tricks before the form submission is processed.
     *
     * @method submitForm
     * @param {Event} e Form submission event.
     * @private
     */
    register.prototype.submitForm = function(e) {
        e.preventDefault();
        this.modal.getRoot().find('form').submit();
    };




    return /** @alias module:local_course/register */ {
        // Public variables and functions.
        /**
         * Attach event listeners to initialise this module.
         *
         * @method init
         * @param {string} selector The CSS selector used to find nodes that will trigger this module.
         * @param {int} contextid The contextid for the course.
         * @return {Promise}
         */
        init: function() {
            var courseid = $("#id_enrollcourse").val();
            args = {};
            args.courseid = courseid;
            var maincontextid = 0;
            $.ajax({ 
             url: M.cfg.wwwroot + '/blocks/admindashboard/contextajax.php',
             data: {courseid: courseid},
             type: 'POST',
             success: function(output) {
                    console.log(output);
                    var data = JSON.parse(output);
                    console.log(data);
                          maincontextid = data['contextid'];
                          console.log(maincontextid);
                          args.contextid = maincontextid;
             console.log(args);
           return new register(args);
                      }
                });
             
           


        },
        load: function() {

        },deleteConfirm: function(args) {
            
            return Str.get_strings([{
                key: 'confirm'
            },
            {
                key: 'deleteconfirm',
                component: 'local_studentdashboard',
                param :args
            },
            {
                key: 'deleteallconfirm',
                component: 'local_studentdashboard'
            },
            {
                key: 'delete'
            }]).then(function(s) {
                ModalFactory.create({
                    title: s[0],
                    type: ModalFactory.types.SAVE_CANCEL,
                    body: s[1]
                }).done(function(modal) {
                    this.modal = modal;
                    modal.setSaveButtonText(s[3]);
                    modal.getRoot().on(ModalEvents.yes, function(e) {
                        e.preventDefault();
                        args.confirm = true;
                        console.log(args);
                        var params = {};
                        params.id = args.id;
                        params.contextid = args.contextid;
                    
                        var promise = Ajax.call([{
                            methodname: 'local_studentdashboard_delete_employee',
                            args: params
                        }]);
                        promise[0].done(function(resp) {
                            window.location.href = window.location.href;
                        }).fail(function(ex) {
                            // do something with the exception
                             console.log(ex);
                        });
                    }.bind(this));
                    modal.show();
                }.bind(this));
            }.bind(this));
        },
    };
});
