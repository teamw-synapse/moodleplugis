define(['local_rolemanagement/cardPaginate','jquery', 'core/ajax', 'core/templates'],function( Cardpaginate,$, ajax, Templates) {
    return {
        search: function(args) {          
           
             var selected_status = $('select#id_status').val();
                
                    var selected_roles = $('select#id_roles').val();
                     var selected_type = $('select#id_type').val();
                    
                    var selected_search = $('#studentsearch').val();

                    if(selected_status != "" && selected_roles != ""){

                        var options = {};
                        options['targetID'] = 'manage_usermanagementusers';
                        options['perPage'] = 10;
                        options['cardClass'] = 'w_one';
                        options['viewType'] = 'card';
                        options['methodName'] = 'block_admindashboard_users_list';
                        options['templateName'] = 'block_admindashboard/users';

                        var dataoptions = {};
                        dataoptions['userid'] =1;
                        dataoptions['contextid'] = 1;
                        dataoptions['selected_status'] = selected_status;
                        dataoptions['selected_roles'] =selected_roles;
                        dataoptions['selected_type'] = selected_type;
                        dataoptions['search'] = selected_search ;                          
                   
                        var filterdata = {};                           
                         
                        var context = {};
                        context['targetID'] = 'manage_usermanagementusers';
                       
                        context['options']  = JSON.stringify(options);
                        context['dataoptions'] = JSON.stringify(dataoptions);
                        context['filterdata']  = JSON.stringify(filterdata);

                        
                        Cardpaginate.reload(options,dataoptions,filterdata);





                    
                    }else{
                        $(".manage_usermanagementusers").html("<div class='alert alert-warning'><strong>Warning!</strong> Please Select Status and Role</div>");
                        setInterval(function(){ $(".alert").fadeOut(); }, 30000);
                    }
        },coursesearch: function(args) {          
           
                    var selected_cats = $('select#id_coursestatus').val();                 
                    var selected_subcats = $('select#id_sub_categories').val();
                    var selected_chcats =  $('select#id_child_categories').val();                 
                    var selected_course = $('select#id_all_courses').val(); 
                    var search          =  $('#coursesearch').val(); 


                    

                        var options = {};
                        options['targetID'] = 'manage_usermanagementcourse';
                        options['perPage'] = 3;
                        options['cardClass'] = 'w_one';
                        options['viewType'] = 'card';
                        options['methodName'] = 'report_courses';
                        options['templateName'] = 'block_admindashboard/courses';

                        var dataoptions = {};
                        dataoptions['userid'] =1;
                        dataoptions['contextid'] = 1;
                        dataoptions['maincategoryid'] = selected_cats;
                        dataoptions['catagory'] =selected_subcats;
                        dataoptions['subcatagory'] =selected_chcats;
                        dataoptions['unitid'] = selected_course ? selected_course.join() : 0 ;  
                         dataoptions['search'] = search;                         
                   
                        var filterdata = {};                           
                         
                        var context = {};
                        context['targetID'] = 'manage_usermanagementcourse';
                       
                        context['options']  = JSON.stringify(options);
                        context['dataoptions'] = JSON.stringify(dataoptions);
                        context['filterdata']  = JSON.stringify(filterdata);

                        $("#coursesearch").css("visibility", "visible");
                        Cardpaginate.reload(options,dataoptions,filterdata);

                      
                    

                      
                    
        },
        load: function() {

          
            $(document).ready(function(){




                $('#id_submitbtn').on('click', function() {


                   
                    var selected_status = $('select#id_status').val();
                
                    var selected_roles = $('select#id_roles').val();
                    var selected_type = $('select#id_type').val();
                    
                    var selected_search = $('#id_userssearch').val();
                    if(selected_status != "" && selected_roles != "" && selected_type != ""){

                         $("#studentsearch").css("visibility", "visible");
                        $("#manage_usermanagementusers").css("visibility", "visible");
                        var options = {};
                        options['targetID'] = 'manage_usermanagementusers';
                        options['perPage'] = 10;
                        options['cardClass'] = 'w_one';
                        options['viewType'] = 'card';
                        options['methodName'] = 'block_admindashboard_users_list';
                        options['templateName'] = 'block_admindashboard/users';

                        var dataoptions = {};
                        dataoptions['userid'] =1;
                        dataoptions['contextid'] = 1;
                        dataoptions['selected_status'] = selected_status;
                        dataoptions['selected_roles'] = selected_roles;
                        dataoptions['selected_type'] = selected_type;
                        dataoptions['search'] = selected_search ;                          
                   
                        var filterdata = {};                           
                         
                        var context = {};
                        context['targetID'] = 'manage_usermanagementusers';
                       
                        context['options']  = JSON.stringify(options);
                        context['dataoptions'] = JSON.stringify(dataoptions);
                        context['filterdata']  = JSON.stringify(filterdata);

                        $("#studentsearch").css("visibility", "visible");
                        Cardpaginate.reload(options,dataoptions,filterdata);                    
                    }else{
                        $(".error").html("<div class='alert alert-warning'><strong>Warning!</strong> Please Select Status and Role</div>");
                        setInterval(function(){ $(".alert").fadeOut(); }, 3000);
                    }
                    
                       
                });



                $('#id_applybtn').on('click', function() {

                   
                    var selected_cats = $('select#id_coursestatus').val();                 
                    var selected_subcats = $('select#id_sub_categories').val();
                    var selected_chcats =  $('select#id_child_categories').val();                 
                    var selected_course = $('select#id_all_courses').val();                   
                    if(selected_cats && selected_subcats){

                         $("#coursesearch").css("visibility", "visible");
                        $("#manage_usermanagementcourse").css("visibility", "visible");
                        var options = {};
                        options['targetID'] = 'manage_usermanagementcourse';
                        options['perPage'] = 2;
                        options['cardClass'] = 'w_one';
                        options['viewType'] = 'card';
                        options['methodName'] = 'report_courses';
                        options['templateName'] = 'block_admindashboard/courses';

                        var dataoptions = {};
                        dataoptions['userid'] =1;
                        dataoptions['contextid'] = 1;
                        dataoptions['maincategoryid'] = selected_cats;
                        dataoptions['catagory'] =selected_subcats;
                        dataoptions['subcatagory'] =selected_chcats;
                        dataoptions['unitid'] = selected_course ? selected_course.join() : 0 ;                          
                   
                        var filterdata = {};                           
                         
                        var context = {};
                        context['targetID'] = 'manage_usermanagementcourse';
                       
                        context['options']  = JSON.stringify(options);
                        context['dataoptions'] = JSON.stringify(dataoptions);
                        context['filterdata']  = JSON.stringify(filterdata);

                        $("#coursesearch").css("visibility", "visible");
                        Cardpaginate.reload(options,dataoptions,filterdata);

                      
                    }else{
                        $(".resultsetdata").html("<div class='alert alert-warning'><strong>Warning!</strong> Please Select Course and Sub Course</div>");
                        setInterval(function(){ $(".alert").fadeOut(); }, 30000);
                        
                    }
                });


                $('#id_enrollbtn').on('click', function(e) {

                        var selected_unit = $('select#id_enrollcourse').val();
                        console.log(selected_unit);        
                        var selected_group = $('select#id_enrollgroup').val();
                        console.log(selected_group);
                        var selected_user = $('select#id_enrolluser').val();
                        console.log(selected_user);
                        var selected_role = $('select#id_assignrole').val();
                        console.log(selected_role);
                        if(selected_unit && selected_group && selected_user && selected_role){
                            var promises = ajax.call([{   
                                methodname: 'admin_user_enrollment', 
                                    args: { 
                                        courseid: selected_unit ? selected_unit : 0,
                                        userid: selected_user ? selected_user.join(',') : 0,
                                        groupid: selected_group ? selected_group.join(',') : 0,
                                        roleid: selected_role
                                    }
                            }]); 
                            promises[0].done(function(data) {
                                console.log(data);
                                if(data.users.length > 0){
                                    $(".enrollresultsetdata").html();
                                    $.each(data.users, function(index, itemData) {
                                        if (itemData.status == 200) {
                                            //var form = document.getElementsByClassName('mform');
                                             e.preventDefault();
                                            $('#fitem_id_assignrole .form-autocomplete-selection').children().remove();
                                            $('#fitem_id_enrolluser .form-autocomplete-selection').children().remove();
                                            $('#fitem_id_enrollcourse .form-autocomplete-selection').children().remove();
                                            $('#fitem_id_enrollgroup .form-autocomplete-selection').children().remove();
                                            $(".enrollresultsetdata").append("<div class='alert alert-success'><strong>Success!</strong> <strong>"+itemData.studentname+"</strong> has been enrolled to <strong>"+itemData.unitname+"</strong> with  <strong>"+itemData.roles+"</strong> roles. <button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span>  </button></div>");
                                            setInterval(function(){ $(".alert").fadeOut(); }, 30000);
                                        } else if(itemData.status == 400) {

                                            //var form = document.getElementsByClassName('mform');
                                            e.preventDefault();
                                            // Reset the form to its initial state
                                             $('#fitem_id_assignrole .form-autocomplete-selection').children().remove();
                                            $('#fitem_id_enrolluser .form-autocomplete-selection').children().remove();
                                            $('#fitem_id_enrollcourse .form-autocomplete-selection').children().remove();
                                            $('#fitem_id_enrollgroup .form-autocomplete-selection').children().remove();
                                            $(".enrollresultsetdata").append("<div class='alert alert-danger'><strong>Error!</strong> <strong>"+itemData.studentname+"</strong> has been already enrolled to <strong>"+itemData.unitname+"</strong> <button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span>  </button></div>");
                                            setInterval(function(){ $(".alert").fadeOut(); }, 30000);
                                        }
                                    });  
                                }else{
                                    $(".enrollresultsetdata").html("<div class='alert alert-warning'><strong>Warning!</strong> "+data.methodstatus+" <button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span>  </button></div>");
                                    setInterval(function(){ $(".alert").fadeOut(); }, 30000);
                                }
                                                        

                            }).fail(function(ex){

                            });
                        }else{
                            $(".enrollresultsetdata").html("Fail");
                            
                        }
                });
            });
        }
    };
});