 /**
 * Add a create new group modal to the page.
 *
 * @module     block_userdashboard/userdashboard
 * @class      userdashboard
 * @package    block_userdashboard
 * @copyright  2024 VGPL
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['local_rolemanagement/cardPaginate', 'jquery', 'core/str','core/templates', 'core/modal_factory', 'core/modal_events', 'core/fragment', 'core/ajax','core/notification', 'core/yui'],
        function(Cardpaginate, $,  Str,Templates,  ModalFactory, ModalEvents, Fragment, Ajax,Notification, Y) {
    return /** @alias module:block_course/register */ {
        // Public variables and functions.
        load: function() {
            $(document).ready(function(){
                // if(!$("#courses_"+tab).hasClass('active')){
                //     $(".nav-link.active").removeClass('active');
                //     $("#courses_"+tab).addClass('active');
                // }
                var studentuserid = $('#studentidvalue').val();
                if(studentuserid > 0){
                    var removeRelated = Ajax.call([{
                        methodname: 'block_userdashboard_data_for_mydashboard',
                            args:  {
                                tab: 'mydashboard',
                                userid: studentuserid
                            }
                        }
                    ]);

                    removeRelated[0].done(function(data) {
                        Templates.render('block_userdashboard/mydashboard', data).then(function(html,js) {
                                $("#v-pills-tabContentcommon").html(html);             
                        }).fail(Notification.exception);
                    }).fail(Notification.exception); 


                    $(document).on("click",".block_userdashboard .nav-link",function() {
                        var ele = this;
                        var tabid = $(this).attr('id');

                        if(!$("#"+tabid).hasClass('active')){
                            $(".nav-link.active").removeClass('active');
                            $("#"+tabid).addClass('active');
                        }

                        var result = tabid.split("-");

                        if(tabid === "v-pills-courses-tab") {
                                    $("#v-pills-tabContentcommon").html('');
                                    
                                    if(!$("#"+tabid).hasClass('active')){
                                        $(".nav-link.active").removeClass('active');
                                        $("#"+tabid).addClass('active');
                                    }            
                                    

                                    var options = {};
                                    options['targetID'] = 'manage_userdashboard_courses';
                                    options['perPage'] = 3;
                                    options['cardClass'] = 'w_one';
                                    options['viewType'] = 'card';
                                    options['methodName'] = 'block_userdashboard_data_for_courses';
                                    options['templateName'] = 'block_userdashboard/course_cards';

                                    var dataoptions = {};
                                    dataoptions['contextid'] = 1;
                                    dataoptions['tab'] = result[2];
                                    dataoptions['userid'] = studentuserid;
                                    var filterdata = {};

                                    var context = {};
                                        context['targetID'] = 'manage_userdashboard_courses';                                       
                                        context['options']  = JSON.stringify(options);
                                        context['dataoptions'] = JSON.stringify(dataoptions);
                                        context['filterdata']  = JSON.stringify(filterdata);

                                    Cardpaginate.reload(options,dataoptions,filterdata);

                                    if(!$("#"+tabid).hasClass('active')){
                                        $(".nav-link.active").removeClass('active');
                                        $("#"+tabid).addClass('active');
                                    }
                                }

                                else if(tabid === "v-pills-assesments-tab") {

                                    if(!$("#"+tabid).hasClass('active')){
                                        $(".nav-link.active").removeClass('active');
                                        $("#"+tabid).addClass('active');
                                    } 

                                    $("#v-pills-tabContentcommon").html('');
                                    var options = {};
                                    options['targetID'] = 'manage_userdashboard_assign';
                                    options['perPage'] = 6;
                                    options['cardClass'] = 'w_one';
                                    options['viewType'] = 'card';
                                    options['methodName'] = 'block_userdashboard_data_for_assesments';
                                    options['templateName'] = 'block_userdashboard/assesments';

                                    var dataoptions = {};
                                    dataoptions['contextid'] = 1;
                                    dataoptions['tab'] = result[2];
                                    dataoptions['userid'] = studentuserid;
                                    var filterdata = {};

                                    var context = {};
                                        context['targetID'] = 'manage_userdashboard_assign';                                       
                                        context['options']  = JSON.stringify(options);
                                        context['dataoptions'] = JSON.stringify(dataoptions);
                                        context['filterdata']  = JSON.stringify(filterdata);

                                    Cardpaginate.reload(options,dataoptions,filterdata);
                                }
                                else if(tabid === "v-pills-exams-tab") {
                                    $("#v-pills-tabContentcommon").html('');
                                    if(!$("#"+tabid).hasClass('active')){
                                        $(".nav-link.active").removeClass('active');
                                        $("#"+tabid).addClass('active');
                                    } 

                                    var options = {};
                                    options['targetID'] = 'manage_userdashboard_exam';
                                    options['perPage'] = 6;
                                    options['cardClass'] = 'w_one';
                                    options['viewType'] = 'card';
                                    options['methodName'] = 'block_userdashboard_data_for_exams';
                                    options['templateName'] = 'block_userdashboard/exams';

                                    var dataoptions = {};
                                    dataoptions['contextid'] = 1;
                                    dataoptions['tab'] = result[2];
                                    dataoptions['userid'] = studentuserid;
                                    var filterdata = {};

                                    var context = {};
                                        context['targetID'] = 'manage_userdashboard_exam';                                       
                                        context['options']  = JSON.stringify(options);
                                        context['dataoptions'] = JSON.stringify(dataoptions);
                                        context['filterdata']  = JSON.stringify(filterdata);

                                    Cardpaginate.reload(options,dataoptions,filterdata);
                                }else if(tabid === "v-pills-forums-tab") {
                                    $("#v-pills-tabContentcommon").html('');
                                    if(!$("#"+tabid).hasClass('active')){
                                        $(".nav-link.active").removeClass('active');
                                        $("#"+tabid).addClass('active');
                                    } 

                                    var options = {};
                                    options['targetID'] = 'manage_userdashboard_forum';
                                    options['perPage'] = 6;
                                    options['cardClass'] = 'w_one';
                                    options['viewType'] = 'card';
                                    options['methodName'] = 'block_userdashboard_data_for_forums';
                                    options['templateName'] = 'block_userdashboard/forums';

                                    var dataoptions = {};
                                    dataoptions['contextid'] = 1;
                                    dataoptions['tab'] = result[2];
                                    dataoptions['userid'] = studentuserid;
                                    var filterdata = {};

                                    var context = {};
                                        context['targetID'] = 'manage_userdashboard_forum';                                       
                                        context['options']  = JSON.stringify(options);
                                        context['dataoptions'] = JSON.stringify(dataoptions);
                                        context['filterdata']  = JSON.stringify(filterdata);

                                    Cardpaginate.reload(options,dataoptions,filterdata);
                                }

                        else {
                            if(result[2] == 'reports'){
                                var chartval = $('#chartSelect :selected').val();
                                chartval = chartval ? chartval : 'bar';
                                var removeRelated = Ajax.call([{
                                    methodname: "block_userdashboard_data_for_"+result[2],
                                        args:  {
                                            tab: result[2],
                                            userid: studentuserid,
                                            courseid: 0,
                                            graphval: chartval
                                        }
                                    }
                                ]);
                                var temp = "block_userdashboard/"+result[2];
                                removeRelated[0].done(function(data) {
                                    // console.log(data);
                                    Templates.render(temp, data).then(function(html,js) {
                                    // console.log(html); 
                                            $("#v-pills-tabContentcommon").html(html);
                                            $("#manage_userdashboard_examid").html('');
                                            $("#manage_userdashboard_assignid").html('');
                                            $("#manage_userdashboard_forumid").html('');
                                            $("#manage_userdashboard_coursesid").html('');
                                            $("#coursesearch").css("display", "none");
                                            $("#forumsearch").css("display", "none");
                                            $("#examsearch").css("display", "none");
                                            $("#assesmentsearch").css("display", "none");             
                                    }).fail(Notification.exception);
                                }).fail(Notification.exception); 
                            }else{
                                 var removeRelated = Ajax.call([{
                                    methodname: "block_userdashboard_data_for_"+result[2],
                                        args:  {
                                            tab: result[2],
                                            userid: studentuserid
                                        }
                                    }
                                ]);
                                var temp = "block_userdashboard/"+result[2];
                                removeRelated[0].done(function(data) {
                                    // console.log(data);
                                    Templates.render(temp, data).then(function(html,js) {
                                    // console.log(html); 
                                            $("#v-pills-tabContentcommon").html(html);
                                            $("#manage_userdashboard_examid").html('');
                                            $("#manage_userdashboard_assignid").html('');
                                            $("#manage_userdashboard_forumid").html('');
                                            $("#manage_userdashboard_coursesid").html('');
                                            $("#coursesearch").css("display", "none");
                                            $("#forumsearch").css("display", "none");
                                            $("#examsearch").css("display", "none");
                                            $("#assesmentsearch").css("display", "none");             
                                    }).fail(Notification.exception);
                                }).fail(Notification.exception); 
                            }
                        }
                    });
                }else{
                    $(document).on('change', '#id_studentids', function() {
                        var selecteduserid = $('#id_studentids :selected').val();
                        // console.log(selecteduserid);
                        if(selecteduserid > 0){
                            var subdata = {'userid': selecteduserid};
                            Templates.render('block_userdashboard/maintabsnew', subdata).then(function(html,js) {
                                $("#userdashboardcontent").html(html);             
                            }).fail(Notification.exception);

                            var removeRelated = Ajax.call([{
                                methodname: 'block_userdashboard_data_for_mydashboard',
                                    args:  {
                                        tab: 'mydashboard',
                                        userid: selecteduserid
                                    }
                                }
                            ]);

                            removeRelated[0].done(function(data) {
                                Templates.render('block_userdashboard/mydashboard', data).then(function(html,js) {
                                        $("#v-pills-tabContentcommon").html(html);             
                                }).fail(Notification.exception);
                            }).fail(Notification.exception); 


                            $(document).on("click",".block_userdashboard .nav-link",function() {
                                var ele = this;
                                var tabid = $(this).attr('id');
                                var studentuserid = $('#studentidvalue').val();
                                // console.log(studentuserid);

                                if(!$("#"+tabid).hasClass('active')){
                                    $(".nav-link.active").removeClass('active');
                                    $("#"+tabid).addClass('active');
                                }

                                var result = tabid.split("-");

                                if(tabid === "v-pills-courses-tab") {

                                    var subdata = {'userid': studentuserid};
                                    Templates.render('block_userdashboard/maintabsnew', subdata).then(function(html,js) {
                                        $("#userdashboardcontent").html(html); 
                                        $("#coursesearch").css("display", "block");
                                        if(!$("#"+tabid).hasClass('active')){
                                            $(".nav-link.active").removeClass('active');
                                            $("#"+tabid).addClass('active');
                                        }            
                                    }).fail(Notification.exception);

                                    var options = {};
                                    options['targetID'] = 'manage_userdashboard_courses';
                                    options['perPage'] = 3;
                                    options['cardClass'] = 'w_one';
                                    options['viewType'] = 'card';
                                    options['methodName'] = 'block_userdashboard_data_for_courses';
                                    options['templateName'] = 'block_userdashboard/course_cards';

                                    var dataoptions = {};
                                    dataoptions['contextid'] = 1;
                                    dataoptions['tab'] = result[2];
                                    dataoptions['userid'] = studentuserid;
                                    var filterdata = {};

                                    var context = {};
                                        context['targetID'] = 'manage_userdashboard_courses';                                       
                                        context['options']  = JSON.stringify(options);
                                        context['dataoptions'] = JSON.stringify(dataoptions);
                                        context['filterdata']  = JSON.stringify(filterdata);

                                    Cardpaginate.reload(options,dataoptions,filterdata);
                                }

                                else if(tabid === "v-pills-assesments-tab") {

                                    var subdata = {'userid': studentuserid};
                                    Templates.render('block_userdashboard/maintabsnew', subdata).then(function(html,js) {
                                        $("#userdashboardcontent").html(html); 
                                        $("#assesmentsearch").css("display", "block");
                                        if(!$("#"+tabid).hasClass('active')){
                                            $(".nav-link.active").removeClass('active');
                                            $("#"+tabid).addClass('active');
                                        }            
                                    }).fail(Notification.exception);

                                    var options = {};
                                    options['targetID'] = 'manage_userdashboard_assign';
                                    options['perPage'] = 6;
                                    options['cardClass'] = 'w_one';
                                    options['viewType'] = 'card';
                                    options['methodName'] = 'block_userdashboard_data_for_assesments';
                                    options['templateName'] = 'block_userdashboard/assesments';

                                    var dataoptions = {};
                                    dataoptions['contextid'] = 1;
                                    dataoptions['tab'] = result[2];
                                    dataoptions['userid'] = studentuserid;
                                    var filterdata = {};

                                    var context = {};
                                        context['targetID'] = 'manage_userdashboard_assign';                                       
                                        context['options']  = JSON.stringify(options);
                                        context['dataoptions'] = JSON.stringify(dataoptions);
                                        context['filterdata']  = JSON.stringify(filterdata);

                                    Cardpaginate.reload(options,dataoptions,filterdata);
                                }
                                else if(tabid === "v-pills-exams-tab") {

                                    var subdata = {'userid': studentuserid};
                                    Templates.render('block_userdashboard/maintabsnew', subdata).then(function(html,js) {
                                        $("#userdashboardcontent").html(html); 
                                        $("#examsearch").css("display", "block");
                                        if(!$("#"+tabid).hasClass('active')){
                                            $(".nav-link.active").removeClass('active');
                                            $("#"+tabid).addClass('active');
                                        }            
                                    }).fail(Notification.exception);

                                    var options = {};
                                    options['targetID'] = 'manage_userdashboard_exam';
                                    options['perPage'] = 6;
                                    options['cardClass'] = 'w_one';
                                    options['viewType'] = 'card';
                                    options['methodName'] = 'block_userdashboard_data_for_exams';
                                    options['templateName'] = 'block_userdashboard/exams';

                                    var dataoptions = {};
                                    dataoptions['contextid'] = 1;
                                    dataoptions['tab'] = result[2];
                                    dataoptions['userid'] = studentuserid;
                                    var filterdata = {};

                                    var context = {};
                                        context['targetID'] = 'manage_userdashboard_exam';                                       
                                        context['options']  = JSON.stringify(options);
                                        context['dataoptions'] = JSON.stringify(dataoptions);
                                        context['filterdata']  = JSON.stringify(filterdata);

                                    Cardpaginate.reload(options,dataoptions,filterdata);
                                }else if(tabid === "v-pills-forums-tab") {

                                    var subdata = {'userid': studentuserid};
                                    Templates.render('block_userdashboard/maintabsnew', subdata).then(function(html,js) {
                                        $("#userdashboardcontent").html(html); 
                                        $("#forumsearch").css("display", "block");
                                        if(!$("#"+tabid).hasClass('active')){
                                            $(".nav-link.active").removeClass('active');
                                            $("#"+tabid).addClass('active');
                                        }            
                                    }).fail(Notification.exception);

                                    var options = {};
                                    options['targetID'] = 'manage_userdashboard_forum';
                                    options['perPage'] = 6;
                                    options['cardClass'] = 'w_one';
                                    options['viewType'] = 'card';
                                    options['methodName'] = 'block_userdashboard_data_for_forums';
                                    options['templateName'] = 'block_userdashboard/forums';

                                    var dataoptions = {};
                                    dataoptions['contextid'] = 1;
                                    dataoptions['tab'] = result[2];
                                    dataoptions['userid'] = studentuserid;
                                    var filterdata = {};

                                    var context = {};
                                        context['targetID'] = 'manage_userdashboard_forum';                                       
                                        context['options']  = JSON.stringify(options);
                                        context['dataoptions'] = JSON.stringify(dataoptions);
                                        context['filterdata']  = JSON.stringify(filterdata);

                                    Cardpaginate.reload(options,dataoptions,filterdata);
                                }
                                else {

                                    if(result[2] == 'reports'){
                                        var chartval = $('#chartSelect :selected').val();
                                        chartval = chartval ? chartval : 'bar';
                                        var removeRelated = Ajax.call([{
                                            methodname: "block_userdashboard_data_for_"+result[2],
                                                args:  {
                                                    tab: result[2],
                                                    userid: studentuserid,
                                                    courseid: 0,
                                                    graphval: chartval
                                                }
                                            }
                                        ]);
                                        var temp = "block_userdashboard/"+result[2];
                                        removeRelated[0].done(function(data) {
                                            // console.log(data);
                                            Templates.render(temp, data).then(function(html,js) {
                                            // console.log(html); 
                                                    $("#v-pills-tabContentcommon").html(html);
                                                    $("#manage_userdashboard_examid").html('');
                                                    $("#manage_userdashboard_assignid").html('');
                                                    $("#manage_userdashboard_forumid").html('');
                                                    $("#manage_userdashboard_coursesid").html('');
                                                    $("#coursesearch").css("display", "none");
                                                    $("#forumsearch").css("display", "none");
                                                    $("#examsearch").css("display", "none");
                                                    $("#assesmentsearch").css("display", "none");             
                                            }).fail(Notification.exception);
                                        }).fail(Notification.exception); 
                                    }else{
                                        var removeRelated = Ajax.call([{
                                            methodname: "block_userdashboard_data_for_"+result[2],
                                                args:  {
                                                    tab: result[2],
                                                    userid: studentuserid
                                                }
                                            }
                                        ]);
                                        var temp = "block_userdashboard/"+result[2];
                                        removeRelated[0].done(function(data) {
                                            // console.log(data);
                                            Templates.render(temp, data).then(function(html,js) {
                                            // console.log(html); 
                                                    $("#v-pills-tabContentcommon").html(html); 
                                                    $("#manage_userdashboard_examid").html('');
                                                    $("#manage_userdashboard_assignid").html('');
                                                    $("#manage_userdashboard_forumid").html('');
                                                    $("#manage_userdashboard_coursesid").html('');
                                                    $("#coursesearch").css("display", "none");
                                                    $("#forumsearch").css("display", "none");
                                                    $("#examsearch").css("display", "none");
                                                    $("#assesmentsearch").css("display", "none");            
                                            }).fail(Notification.exception);
                                        }).fail(Notification.exception); 
                                    }
                                    
                                }
                                
                            });
                        }else{
                            $("#userdashboardcontent").html('');
                        }
                        

                    });
                }
                
                $(document).on('change', '#unitSelect', function() {
                    var courseid = $('#unitSelect :selected').val();
                    var chartval = $('#chartSelect :selected').val();
                    chartval = chartval ? chartval : 'bar';
                    var studentuserid = $('#studentidvalue').val();
                    if(!studentuserid){
                        var studentuserid = $('#id_studentids :selected').val();
                    }
                    
                        var removeRelated = Ajax.call([{
                            methodname: "block_userdashboard_data_for_reports",
                                args:  {
                                    tab: 'reports',
                                    userid: studentuserid,
                                    courseid: courseid,
                                    graphval: chartval
                                }
                            }
                        ]);
                        
                        var temp = "block_userdashboard/reports";
                        removeRelated[0].done(function(data) {
                            Templates.render(temp, data).then(function(html,js) {
                                $("#v-pills-tabContentcommon").html(html);             
                            }).fail(Notification.exception);
                        }).fail(Notification.exception); 
                });


                $(document).on('change', '#chartSelect', function() {
                    var courseid = $('#unitSelect :selected').val();
                    var chartval = $('#chartSelect :selected').val();
                    chartval = chartval ? chartval : 'bar';
                    var studentuserid = $('#studentidvalue').val();
                    if(!studentuserid){
                        var studentuserid = $('#id_studentids :selected').val();
                    }
                    
                        var removeRelated = Ajax.call([{
                            methodname: "block_userdashboard_data_for_reports",
                                args:  {
                                    tab: 'reports',
                                    userid: studentuserid,
                                    courseid: courseid,
                                    graphval: chartval
                                }
                            }
                        ]);
                        
                        var temp = "block_userdashboard/reports";
                        removeRelated[0].done(function(data) {
                            Templates.render(temp, data).then(function(html,js) {
                                $("#v-pills-tabContentcommon").html(html);             
                            }).fail(Notification.exception);
                        }).fail(Notification.exception); 
                });

                $("#fitem_id_studentids" ).on("keyup", function() { 

                     var childValue = $('#fitem_id_studentids').find('.form-control').first().val();

                     if(childValue.length > 3)
                     {
                   

                         var promises = Ajax.call([{   

                            methodname: 'block_userdashboard_search_for_students', 
                                args: { 

                                    search: childValue,
                                   
                                }
                        }]); 

                        promises[0].done(function(data) {

                            studentselOpts =" <option value='0'>Select Student Name</option>";
                            selopt = '';
                           for (i=0;i<data.users.length;i++)
                            {          

                                var studentid = data.users[i]['studentid'];

                                var studentval = data.users[i]['studentname'];
                                selopt += '<li role="option" data-value="'+studentid+'" aria-selected="false" >'+studentval+'</li>';

                                 studentselOpts += "<option value='"+studentid+"'>"+studentval+"</option>";

                            }
                           
                          
                            $('#id_studentids').children().remove().end().append(studentselOpts);
                            $('#fitem_id_studentids .form-autocomplete-suggestions').children().remove().end().append(selopt);

                            $("#fitem_id_studentids .form-autocomplete-suggestions").css('display','block');
                         }).fail(function(ex){

                        });

                     }
                });

            });
        },

        coursesearch: function(args) {          
            var studentuserid = $('#studentidvalue').val();                  
            var selected_course = $('#coursesearch').val(); 
                            
            if(studentuserid > 0){

                var options = {};
                options['targetID'] = 'manage_userdashboard_courses';
                options['perPage'] = 3;
                options['cardClass'] = 'w_one';
                options['viewType'] = 'card';
                options['methodName'] = 'block_userdashboard_data_for_courses';
                options['templateName'] = 'block_userdashboard/course_cards';

                var dataoptions = {};
                dataoptions['contextid'] = 1;
                dataoptions['tab'] = 'courses';
                dataoptions['userid'] = studentuserid;
                dataoptions['coursesearch'] = selected_course;
                var filterdata = {};

                var context = {};
                context['targetID'] = 'manage_userdashboard_courses';                                       
                context['options']  = JSON.stringify(options);
                context['dataoptions'] = JSON.stringify(dataoptions);
                context['filterdata']  = JSON.stringify(filterdata);

                Cardpaginate.reload(options,dataoptions,filterdata);
            }
        },

        assesmentsearch: function(args) {          
            var studentuserid = $('#studentidvalue').val();                  
            var assesmentsearch = $('#assesmentsearch').val(); 
                            
            if(studentuserid > 0){

                var options = {};
                options['targetID'] = 'manage_userdashboard_assign';
                options['perPage'] = 6;
                options['cardClass'] = 'w_one';
                options['viewType'] = 'card';
                options['methodName'] = 'block_userdashboard_data_for_assesments';
                options['templateName'] = 'block_userdashboard/assesments';

                var dataoptions = {};
                dataoptions['contextid'] = 1;
                dataoptions['tab'] = 'courses';
                dataoptions['userid'] = studentuserid;
                dataoptions['assesmentsearch'] = assesmentsearch;
                var filterdata = {};

                var context = {};
                context['targetID'] = 'manage_userdashboard_assign';                                       
                context['options']  = JSON.stringify(options);
                context['dataoptions'] = JSON.stringify(dataoptions);
                context['filterdata']  = JSON.stringify(filterdata);

                Cardpaginate.reload(options,dataoptions,filterdata);
            }
        },


        examsearch: function(args) {          
            var studentuserid = $('#studentidvalue').val();                  
            var examsearch = $('#examsearch').val(); 
                            
            if(studentuserid > 0){

                var options = {};
                options['targetID'] = 'manage_userdashboard_exam';
                options['perPage'] = 6;
                options['cardClass'] = 'w_one';
                options['viewType'] = 'card';
                options['methodName'] = 'block_userdashboard_data_for_exams';
                options['templateName'] = 'block_userdashboard/exams';

                var dataoptions = {};
                dataoptions['contextid'] = 1;
                dataoptions['tab'] = 'courses';
                dataoptions['userid'] = studentuserid;
                dataoptions['examsearch'] = examsearch;
                var filterdata = {};

                var context = {};
                context['targetID'] = 'manage_userdashboard_exam';                                       
                context['options']  = JSON.stringify(options);
                context['dataoptions'] = JSON.stringify(dataoptions);
                context['filterdata']  = JSON.stringify(filterdata);

                Cardpaginate.reload(options,dataoptions,filterdata);
            }
        },

        forumsearch: function(args) {          
            var studentuserid = $('#studentidvalue').val();                  
            var forumsearch = $('#forumsearch').val(); 
                            
            if(studentuserid > 0){

                var options = {};
                options['targetID'] = 'manage_userdashboard_forum';
                options['perPage'] = 6;
                options['cardClass'] = 'w_one';
                options['viewType'] = 'card';
                options['methodName'] = 'block_userdashboard_data_for_forums';
                options['templateName'] = 'block_userdashboard/forums';

                var dataoptions = {};
                dataoptions['contextid'] = 1;
                dataoptions['tab'] = 'forums';
                dataoptions['userid'] = studentuserid;
                dataoptions['forumsearch'] = forumsearch;
                var filterdata = {};

                var context = {};
                context['targetID'] = 'manage_userdashboard_forum';                                       
                context['options']  = JSON.stringify(options);
                context['dataoptions'] = JSON.stringify(dataoptions);
                context['filterdata']  = JSON.stringify(filterdata);

                Cardpaginate.reload(options,dataoptions,filterdata);
            }
        },
    };
});
