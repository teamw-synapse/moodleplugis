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

      
         this.userid = args.userid;
       
         this.courseid = args.courseid;

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
             var head =  Str.get_string('forum', 'block_facultydashboard');

            
            return head.then(function(title) {
               
                return ModalFactory.create({
                    type: ModalFactory.types.DEFAULT,
                    title: title,
                    body: self.getBody(),
                    footer: this.getFooter()
                });
            }.bind(self)).then(function(modal) {

                
               self.modal = modal;
               
               
            
                self.modal.setLarge();

            this.modal.getFooter().find('[data-action="cancel"]').on('click', function() {
                 
                modal.destroy();
            });
            this.modal.getFooter().find('[data-action="skip"]').on('click', function() {
                self.args.form_status = self.args.form_status + 1;
                 
                
                var data = self.getBody();
             
                modal.setBody(data);
            });

            // this.modal.getRoot().on('submit', 'form', function(form) {
            //     self.submitFormAjax(form, self.args);
            // });
            this.modal.show();
            this.modal.getRoot().animate({"right":"0%"}, 500);
            $(".close").click(function(){
                  modal.destroy();
            });
            return this.modal;
            }.bind(this));

    };

    /**
     * @method getBody
     * @private
     * @return {Promise}
     */
    register.prototype.getBody = function() {
        params = {};
        params.userid = this.userid;
        params.courseid = this.courseid;
        // params.uid    = this.uid;
        // params.employee = this.employee; 

       
          return Fragment.loadFragment('block_facultydashboard', 'forum_details', 5,  params);  
       
       
                
    };
     register.prototype.getFooter = function() {     
        
        
     
        $footer = '<button type="button" class="btn btn-secondary" data-action="cancel">Cancel</button>';
        return $footer;
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

           return new register(args);
        



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
