require(['jquery', 'core/ajax','core/templates'], function($, ajax, Templates){

$(document).ready(function(){


    $('#id_submitbtn').on('click', function() {
        var selected_status = $('select#id_status').val();
        console.log(selected_status);
        var selected_roles = $('select#id_roles').val();
        console.log(selected_status);

            $.ajax({ 
             url: 'http://localhost/moodle/blocks/admindashboard/ajax.php',
             data: {selected_status: selected_status,selected_roles: selected_roles},
             type: 'POST',
             success: function(output) {
                          $(".resultset").html(output);
                      }
            });
    });

     $('#id_applybtn').on('click', function() {
        var selected_cats = $('select#id_coursestatus').val();
        console.log(selected_cats);
        var selected_subcats = $('select#id_sub_categories').val();
        console.log(selected_subcats);
        var selected_course = $('select#id_all_courses').val();
        console.log(selected_course);
        if(selected_cats && selected_subcats){
            var promises = ajax.call([{   
                methodname: 'report_courses', 
                    args: { 
                        categoryid: selected_cats,
                        courseid: selected_subcats,
                        unitid: selected_course ? selected_course.join() : 0
                    }
            }]); 
            promises[0].done(function(data) {
               
                Templates.renderForPromise('block_admindashboard/courses',data)
                 .then(({html,js}) => {
                    $(".resultsetdata").html(html);
                 }).catch((error)=>displayException(error));

            }).fail(function(ex){

            });
        }else{
            $(".resultsetdata").html('');
            
        }
    });


           $('#id_enrollbtn').on('click', function() {

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
                                courseid: selected_unit ? selected_unit.join() : 0,
                                userid: selected_user ? selected_user.join() : 0,
                                groupid: selected_group ? selected_group.join() : 0,
                                roleid: selected_role ? selected_role.join() : 0
                            }
                    }]); 
                    promises[0].done(function(data) {
                        console.log(data);
                        
                      if (data.status == 200) {
                            $(".enrollresultsetdata").html("<div class='alert alert-success'><strong>Success!</strong> User enrolled successfully.</div>");
                        } else if(data.status == 400) {
                            $(".enrollresultsetdata").html("<div class='alert alert-danger'><strong>Error!</strong> User already enrolled in this course.</div>");
                        } else if(data.status == 500) {
                            $(".enrollresultsetdata").html("<div class='alert alert-warning'><strong>Warning!</strong> User enrollment failed.</div>");
                        }                             

                    }).fail(function(ex){

                    });
                }else{
                    $(".enrollresultsetdata").html("Fail");
                    
                }
            });

});


});