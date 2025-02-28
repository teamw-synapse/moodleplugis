 /**
 * Add a create new group modal to the page.
 *
 * @module     local_note/register
 * @class      register
 * @package    local_note
 * @copyright  2023 Bhupathi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'local_rolemanagement/cardPaginate','core/str', 'core/modal_factory', 'core/modal_events', 'core/fragment', 'core/ajax', 'core/yui','core/notification','core/templates'],
        function( $, Cardpaginate, Str, ModalFactory, ModalEvents, Fragment, Ajax, Y,notification,Templates) {

    /**
     * Constructor
     *
     * @param {String} selector used to find triggers for the new group modal.
     * @param {int} contextid
     *
     * Each call to init gets it's own instance of this class.
     */
    var register = function(selector, contextid, id) {

        this.contextid = contextid;
        this.id = id;

        var self = this;
        self.init(selector);
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
    register.prototype.init = function(selector) {
        //var triggers = $(selector);
        var self = this;
        // Fetch the title string.
            
                var head =  Str.get_string('note', 'local_note');
           

            
            return head.then(function(title) {
               
                return ModalFactory.create({
                    type: ModalFactory.types.DEFAULT,
                    title: title,
                    body: self.getBody(),
                    footer: this.getFooter()
                });
            }.bind(self)).then(function(modal) {

                
               self.modal = modal;
               
                self.modal.getRoot().addClass('customclass local_note');
            
                self.modal.setLarge();

           
                this.modal.getRoot().on(ModalEvents.hidden, function() {
               
                    modal.destroy();
              
            }.bind(this));
            this.modal.getFooter().find('[data-action="save"]').on('click', this.submitForm.bind(this));
            this.modal.getFooter().find('[data-action="cancel"]').on('click', function() {
                window.location.href =  window.location.href;
            });
            this.modal.getFooter().find('[data-action="skip"]').on('click', function() {
                self.args.form_status = self.args.form_status + 1;
                 
                
                var data = self.getBody();
             
                modal.setBody(data);
            });

            this.modal.getRoot().on('submit', 'form', function(form) {
                self.submitFormAjax(form, self.args);

            });
            this.modal.show();
            this.modal.getRoot().animate({"right":"0%"}, 500);
            $(".close").click(function(){
                window.location.href =  window.location.href;
            });
            return this.modal;
            }.bind(this));

    };

    /**
     * @method getBody
     * @private
     * @return {Promise}
     */
    register.prototype.getBody = function(formdata) {
        if (typeof formdata === "undefined") {
            formdata = {};
        }
        params = {};
        params.jsonformdata = JSON.stringify(formdata);
        params.id = this.id;
        // params.employee = this.employee; 

        return Fragment.loadFragment('local_note', 'note_form', this.contextid, params);
    };
     register.prototype.getFooter = function() {
        console.log(this);
        if(this.id){
             $footer = '<button type="button" class="btn btn-primary" data-action="save">Update</button>&nbsp;';
        }
        else{
        $footer = '<button type="button" class="btn btn-primary" data-action="save">Save</button>&nbsp;';
        }
        if (this.form_status == 0) {
            $style = 'style="display:none;"';
            $footer += '<button type="button" class="btn btn-secondary" data-action="skip" ' + $style + '>Skip</button>&nbsp;';
        }
        $footer += '<button type="button" class="btn btn-secondary" data-action="cancel">Cancel</button>';
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
        document.location.reload();
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

        // Convert all the form elements values to a serialised string.
        var formData = this.modal.getRoot().find('form').serialize();
         console.log(JSON.stringify(formData));
       
        Ajax.call([{
            methodname: 'local_note_save',
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
        var self = this;
        self.modal.getRoot().find('form').submit();
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
        init: function(args) {
            console.log(args)
         
           var promises = Ajax.call([{   
            methodname: 'block_facultydashboard', 
              args: {userid: args, }
            }]); 
            promises[0].done(function(data) {
             
             console.log(data.totalcourses)
             
             var totalcourses = data.totalcourses
             var userid = data.userid
             Templates.renderForPromise('block_facultydashboard/dashboard',data,totalcourses,userid)
             .then(({html,js}) => {
                //Templates.appendNodeContents('.block_facultydashboard',html,js);
                $("#facultydashboardid").html(html);
             }).catch((error)=>displayException(error));
                       
             
            }).fail(function(ex){
            });
             
             console.log("fail")
                                },

        load: function(args) {

            $(document).ready(function(){
                
                    $(document).on('change', '#id_facultyids', function() {
                        var selecteduserid = $('#id_facultyids :selected').val();
                        if(selecteduserid > 0){
                            
                            var options = {};
                            options['targetID'] = 'dashboard';
                            options['perPage'] = 4;
                            options['cardClass'] = 'w_one';
                            options['viewType'] = 'card';
                            options['methodName'] = 'block_facultydashboard';
                            options['templateName'] = 'block_facultydashboard/view-cards';

                            var dataoptions = {};
                            dataoptions['userid'] =selecteduserid;
                            dataoptions['contextid'] = 1;
                               
                       
                            var filterdata = {};
                             
                            var context = {};
                            context['targetID'] = 'dashboard';
                            context['options']  = JSON.stringify(options);
                            context['dataoptions'] = JSON.stringify(dataoptions);
                            context['filterdata']  = JSON.stringify(filterdata);


                            Cardpaginate.reload(options,dataoptions,filterdata);
                        }
                });  


                    $("#fitem_id_facultyids" ).on("keyup", function() { 

                         var childValue = $('#fitem_id_facultyids').find('.form-control').first().val();

                         if(childValue.length > 3)
                         {
                       

                             var promises = Ajax.call([{   

                                methodname: 'report_searchfaculty', 
                                    args: { 

                                        search: childValue,
                                       
                                    }
                            }]); 

                            promises[0].done(function(data) {

                                facultyselOpts =" <option value='0'>Select Faculty Name</option>";
                                selopt = '';

                               for (i=0;i<data.users.length;i++)
                                {          

                                    var facultyid = data.users[i]['facultyid'];

                                    var facultyval = data.users[i]['facultyname'];

                                    selopt += '<li role="option" data-value="'+facultyid+'" aria-selected="false" >'+facultyval+'</li>';

                                     facultyselOpts += "<option value='"+facultyid+"'>"+facultyval+"</option>";

                                }
                               
                              
                                $('#id_facultyids').children().remove().end().append(facultyselOpts);
                                $('#fitem_id_facultyids .form-autocomplete-suggestions').children().remove().end().append(selopt);
 
                                $("#fitem_id_facultyids .form-autocomplete-suggestions").css('display','block');
                                 }).fail(function(ex){

                                });

                         }
                      

                        
                      });     
            });

        },deleteConfirm: function(args) {
            console.log(args);
            return Str.get_strings([{
                key: 'confirm'
            },
            {
                key: 'deleteconfirm',
                component: 'local_note',
                param :args
            },
            {
                key: 'deleteallconfirm',
                component: 'local_note'
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
                            methodname: 'local_note_delete_employee',
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
