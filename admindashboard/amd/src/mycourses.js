define(['local_rolemanagement/cardPaginate','jquery', 'core/ajax', 'core/templates'],function( Cardpaginate,$, ajax, Templates) {
    return {
        load: function() {
            $(document).ready(function(){


              
                            
                    $("#id_coursestatus" ).on("change", function() {
                      
                        var valsub = 0;
                        if(valsub == 1){
                            var value =  $('#id_enrollcat').val();
                            var sub =  0;
                            var subval =  0;
                        }else{
                            var value =  $('#id_coursestatus').val();
                            var sub =  0;
                            var subval =  0;
                        }
                        var promises = ajax.call([{   

                            methodname: 'report_category', 
                                args: { 

                                    id: value,
                                    subid: sub,
                                    unitid: subval
                                }
                        }]); 

                        promises[0].done(function(data) {

                            var selOpts = "";

                            selOpts =" <option value='0'>Select Course Name</option>";

                            for (i=0;i<data.category.length;i++)

                            {          

                                var id = data.category[i]['categoryid'];

                                var val = data.category[i]['categoryname'];

                                 selOpts += "<option value='"+id+"'>"+val+"</option>";

                            }
                            if(valsub == 1){
                                $('#id_enrollsub').children().remove().end().append(selOpts);
                            }else{
                                $('#id_sub_categories').children().remove().end().append(selOpts);
                            }


                        }).fail(function(ex){

                        });
                    });
                


                //function get_groups(val) {

                    // Fetch the selected course status
                    $("#id_enrollcourse" ).on( "change", function() {
                        var value =  0;
                        var sub =  0;
                        var subval =  $('#id_enrollcourse').val();
                        if(subval > 0){
                            $('#id_add-course-btn').css('display','block');
                        }else{
                            $('#id_add-course-btn').css('display','none');
                        }
                        var promises = ajax.call([{   

                            methodname: 'report_category', 
                                args: { 

                                    id: value,
                                    subid: sub,
                                    unitid: subval ? subval : 0
                                }
                        }]); 

                        promises[0].done(function(data) {

                            subselOpts =" <option value='0'>Select Group Name</option>";

                            for (i=0;i<data.groups.length;i++)

                            {          

                                var subid = data.groups[i]['groupid'];

                                var subvalll = data.groups[i]['groupname'];

                                 subselOpts += "<option value='"+subid+"'>"+subvalll+"</option>";

                            }
                           
                               
                                $('#id_enrollgroup').children().remove().end().append(subselOpts);
                                
                                
                            //     studentselOpts =" <option value='0'>Select Student Name</option>";

                            // for (i=0;i<data.users.length;i++)

                            // {          

                            //     var studentid = data.users[i]['studentid'];

                            //     var studentval = data.users[i]['studentname'];

                            //      studentselOpts += "<option value='"+studentid+"'>"+studentval+"</option>";

                            // }
                           
                               
                            //     $('#id_enrolluser').children().remove().end().append(studentselOpts);


                                roleselOpts =" <option value='0'>Select Assign Role</option>";

                            for (i=0;i<data.roles.length;i++)

                            {          

                                var roleid = data.roles[i]['roleid'];

                                var roleval = data.roles[i]['rolename'];

                                 roleselOpts += "<option value='"+roleid+"'>"+roleval+"</option>";

                            }
                           
                               
                                //$('#id_assignrole').children().remove().end().append(roleselOpts);

                        }).fail(function(ex){

                        });
                    });
                //}

                    $( "#id_sub_categories" ).on( "change", function() {
                            var valsub = 0;
                            if(valsub == 1){
                                var value =  $('#id_enrollcat').val();
                                var sub =  $('#id_enrollsub').val();
                                var subval =  0;
                            }else{
                                var value =  $('#id_coursestatus').val();
                                var sub =  $('#id_sub_categories').val();
                                var subval =  0;
                            }


                            var promises = ajax.call([{   

                                methodname: 'report_category', 
                                    args: { 

                                        id: value,
                                        subid: sub,
                                        unitid: subval
                                    }
                            }]); 

                            promises[0].done(function(data) {

                                subselOpts =" <option value='0'>Select Unit Name</option>";

                                for (i=0;i<data.ccategory.length;i++)

                                {          

                                    var subid = data.ccategory[i]['categoryid'];

                                    var subval = data.ccategory[i]['categoryname'];

                                     subselOpts += "<option value='"+subid+"'>"+subval+"</option>";

                                }
                                if(valsub == 1){
                                    $('#id_enrollcourse').children().remove().end().append(subselOpts);
                                }else{
                                    $('#id_child_categories').children().remove().end().append(subselOpts);
                                }
                            }).fail(function(ex){

                            });
                    });




                    $( "#id_child_categories" ).on( "change", function() {
                        

                             var value =  $('#id_child_categories').val();    
                            var promises = ajax.call([{   

                                methodname: 'get_courselist',
                                   
                                args: {categoryid: value}
                            }]); 

                            promises[0].done(function(data) {

                               var selOpts = "";
                                selOpts =" <option value='0'>Select Course Name</option>";
                      
                              for (i=0;i<data.course.length;i++)
                                {          
                                    var id = data.course[i]['courseid'];
                                    var val = data.course[i]['courseshortname'];
                                    selOpts += "<option value='"+id+"'>"+val+"</option>";
                                }    
                               
                                    $('#id_all_courses').children().remove().end().append(selOpts);
                                
                            }).fail(function(ex){

                            });
                    });

                      $("#fitem_id_enrollcourse" ).on("keyup", function() { 

                         var childValue = $('#fitem_id_enrollcourse').find('.form-control').first().val();

                         if(childValue.length > 3)
                         {
                       

                             var promises = ajax.call([{   

                                methodname: 'report_searchcourses', 
                                    args: { 

                                        search: childValue,
                                       
                                    }
                            }]); 

                            promises[0].done(function(data) {

                                subselOpts =" <option value='0'>Select Unit Name</option>";
                                selopt = '';

                                for (i=0;i<data.courses.length;i++)
                                {          

                                    var subid = data.courses[i]['courseid'];
                                    var subval = data.courses[i]['coursename'];
                                    selopt += '<li role="option" data-value="'+subid+'" aria-selected="false" >'+subval+'</li>';
                                    subselOpts += "<option value='"+subid+"'>"+subval+"</option>";
                                }                                
                                $('#id_enrollcourse').children().remove().end().append(subselOpts);
                                $('#fitem_id_enrollcourse .form-autocomplete-suggestions').children().remove().end().append(selopt);
                                $("#fitem_id_enrollcourse .form-autocomplete-suggestions").css('display','block');
                             }).fail(function(ex){

                            });

                         }
                      

                        
                      });



                      $("#fitem_id_enrolluser" ).on("keyup", function() { 

                         var childValue = $('#fitem_id_enrolluser').find('.form-control').first().val();

                         if(childValue.length > 3)
                         {
                       

                             var promises = ajax.call([{   

                                methodname: 'report_searchusers', 
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
                                    var studentidnumber = data.users[i]['studentidnumber'];
                                    selopt += '<li role="option" data-value="'+studentid+'" aria-selected="false" >'+studentval+'('+studentidnumber+')</li>';
                                    studentselOpts += "<option value='"+studentid+"'>"+studentval+"("+studentidnumber+")</option>";
                                }                                
                                // alert(studentselOpts);                              
                                $('#id_enrolluser').children().remove().end().append(studentselOpts);
                                $('#fitem_id_enrolluser .form-autocomplete-suggestions').children().remove().end().append(selopt);
                                $("#fitem_id_enrolluser .form-autocomplete-suggestions").css('display','block');
                             }).fail(function(ex){

                            });

                         }
                      

                        
                      });




                       $("#fitem_id_unenrollcourse" ).on("keyup", function() { 

                         var childValue = $('#fitem_id_unenrollcourse').find('.form-control').first().val();

                         if(childValue.length > 3)
                         {
                       

                             var promises = ajax.call([{   

                                methodname: 'report_searchcourses', 
                                    args: { 

                                        search: childValue,
                                       
                                    }
                            }]); 

                            promises[0].done(function(data) {

                                subselOpts =" <option value='0'>Select Unit Name</option>";
                                selopt = '';

                                for (i=0;i<data.courses.length;i++)
                                {          

                                    var subid = data.courses[i]['courseid'];
                                    var subval = data.courses[i]['coursename'];
                                    selopt += '<li role="option" data-value="'+subid+'" aria-selected="false" >'+subval+'</li>';
                                    subselOpts += "<option value='"+subid+"'>"+subval+"</option>";
                                }                                
                                $('#id_unenrollcourse').children().remove().end().append(subselOpts);
                                $('#fitem_id_unenrollcourse .form-autocomplete-suggestions').children().remove().end().append(selopt);
                                $("#fitem_id_unenrollcourse .form-autocomplete-suggestions").css('display','block');
                             }).fail(function(ex){

                            });

                         }
                      

                        
                      });


                     $( "#id_unenrollcourse" ).on( "change", function() {
                        

                             var value =  $('#id_unenrollcourse').val();    
                            var promises = ajax.call([{   

                                methodname: 'get_courseusers',
                                   
                                args: {courseid: value}
                            }]); 

                            promises[0].done(function(data) {

                               var selOpts = "";
                                selOpts =" <option value='0'>Select User Name</option>";
                      
                             for (i=0;i<data.users.length;i++)
                                {        

                                    var userid = data.users[i]['userid'];
                                    var userval = data.users[i]['username'];
                                    var useremail = data.users[i]['useremail'];
                                   
                                    selOpts += "<option value='"+userid+"'>"+userval+"("+useremail+")</option>";
                                }
                                    $('#id_unenrolluser').children().remove().end().append(selOpts);
                                
                            }).fail(function(ex){

                            });
                    });

                $('#id_unenrollbtn').on('click', function() {

                    var courseid =  $('#id_unenrollcourse').val();
                    var userid =  $('#id_unenrolluser').val();

                     var promises = ajax.call([{   

                                methodname: 'admin_user_unenrollment',
                                   
                                args: {
                                    userid: userid,
                                    courseid : courseid
                                }
                            }]); 

                            promises[0].done(function(data) {

                               if(data.status == 200){

                                $('#fitem_id_unenrolluser .form-autocomplete-selection').children().remove();
                                $('#fitem_id_unenrollcourse .form-autocomplete-selection').children().remove();
                                 $(".unenrollresultsetdata").append("<div class='alert alert-success'><strong>Success!</strong> <strong>"+data.username+"</strong> has been unenrolled to <strong>"+data.unitname+"</strong> <button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span>  </button></div>");
                                            setInterval(function(){ $(".alert").fadeOut(); }, 30000);
                               }else{

                                 $('#fitem_id_unenrolluser .form-autocomplete-selection').children().remove();
                                $('#fitem_id_unenrollcourse .form-autocomplete-selection').children().remove();
                                $(".unenrollresultsetdata").append("<div class='alert alert-danger'><strong>Error!</strong> <strong>"+data.username+"</strong> has been already unenrolled to <strong>"+data.unitname+"</strong> <button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span>  </button></div>");
                                            setInterval(function(){ $(".alert").fadeOut(); }, 30000);
                               }
                                
                            }).fail(function(ex){

                            });
                  
                });

                 $('#id_unrollcancelbtn').on('click', function() {

                        $('#fitem_id_unenrolluser .form-autocomplete-selection').children().remove();
                                $('#fitem_id_unenrollcourse .form-autocomplete-selection').children().remove();
                 });

                 $('#id_enrollcancelbtn').on('click', function() {

                        $('#fitem_id_assignrole .form-autocomplete-selection').children().remove();
                        $('#fitem_id_enrolluser .form-autocomplete-selection').children().remove();
                        $('#fitem_id_enrollcourse .form-autocomplete-selection').children().remove();
                        $('#fitem_id_enrollgroup .form-autocomplete-selection').children().remove();
                 });

                  $('#id_applycancelbtn').on('click', function() {

                        $("#coursesearch").css("visibility", "hidden");
                        $("#manage_usermanagementcourse").css("visibility", "hidden");

                        $('#fitem_id_coursestatus .form-autocomplete-selection').children().remove();
                        $('#fitem_id_sub_categories .form-autocomplete-selection').children().remove();
                        $('#fitem_id_child_categories .form-autocomplete-selection').children().remove();
                        $('#fitem_id_all_courses .form-autocomplete-selection').children().remove();

                        $('#id_child_categories').children().remove();
                         $('#id_sub_categories').children().remove();
                          $('#id_all_courses').children().remove();

                          $('select#id_coursestatus').val('');                 
                     $('select#id_sub_categories').val('');
                      $('select#id_child_categories').val('');                 
                     $('select#id_all_courses').val(''); 
                 });

                $('#id_submitcancelbtn').on('click', function() {

                      

                        $("#studentsearch").css("visibility", "hidden");
                        $("#manage_usermanagementusers").css("visibility", "hidden");

                        $('#fitem_id_status .form-autocomplete-selection').children().remove();
                        $('#fitem_id_type .form-autocomplete-selection').children().remove();
                        $('#fitem_id_roles .form-autocomplete-selection').children().remove();
                        $('select#id_status').val('');
                        $('select#id_roles').val('');
                        $('select#id_type').val('');
                        
                 });
            });
        },search: function(){

            alert();

        },
    };
});