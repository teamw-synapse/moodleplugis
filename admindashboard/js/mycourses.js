function get_terms(valsub) {
    // Fetch the selected course status
  if(valsub == 1){
        var value =  $('#id_enrollcat').val();
        var sub =  0;
        var subval =  0;
  }else{
        var value =  $('#id_coursestatus').val();
        var sub =  0;
        var subval =  0;
  }
// var value =  $('#id_coursestatus').val();
// var sub =  0;
    

require(['core/ajax'], function(ajax) {

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
    
  
   
}
// function get_units(valsub) {

// // Fetch the selected course status
//     if(valsub == 1){
//         var value =  $('#id_enrollcat').val();
//         var sub =  $('#id_enrollsub').val();
//         var subval =  0;
//     }else{
//         var value =  $('#id_coursestatus').val();
//     var sub =  $('#id_sub_categories').val();
//     var subval =  0;
//     }
    

// require(['core/ajax'], function(ajax) {

//         var promises = ajax.call([{   

//             methodname: 'report_category', 
//                 args: { 

//                     id: value,
//                     subid: sub,
//                     unitid: subval
//                 }
//         }]); 

//         promises[0].done(function(data) {

//             subselOpts =" <option value='0'>Select Unit Name</option>";

//             for (i=0;i<data.courses.length;i++)

//             {          

//                 var subid = data.courses[i]['courseid'];

//                 var subval = data.courses[i]['coursename'];

//                  subselOpts += "<option value='"+subid+"'>"+subval+"</option>";

//             }
//             if(valsub == 1){
//                 $('#id_enrollcourse').children().remove().end().append(subselOpts);
//             }else{
//                 $('#id_all_courses').children().remove().end().append(subselOpts);
//             }
//         }).fail(function(ex){

//         });
 
 
// });

   
// }

function get_groups(val) {

// Fetch the selected course status
  
        var value =  0;
        var sub =  0;
        var subval =  $('#id_enrollcourse').val();
   
    

require(['core/ajax'], function(ajax) {

        var promises = ajax.call([{   

            methodname: 'report_category', 
                args: { 

                    id: value,
                    subid: sub,
                    unitid: subval ? subval.join() : 0
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


                studentselOpts =" <option value='0'>Select Student Name</option>";

            for (i=0;i<data.users.length;i++)

            {          

                var studentid = data.users[i]['studentid'];

                var studentval = data.users[i]['studentname'];

                 studentselOpts += "<option value='"+studentid+"'>"+studentval+"</option>";

            }
           
               
                $('#id_enrolluser').children().remove().end().append(studentselOpts);


                roleselOpts =" <option value='0'>Select Assign Role</option>";

            for (i=0;i<data.roles.length;i++)

            {          

                var roleid = data.roles[i]['roleid'];

                var roleval = data.roles[i]['rolename'];

                 roleselOpts += "<option value='"+roleid+"'>"+roleval+"</option>";

            }
           
               
                $('#id_assignrole').children().remove().end().append(roleselOpts);

        }).fail(function(ex){

        });
 
 
});

   
}
