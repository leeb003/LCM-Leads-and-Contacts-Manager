// config.js corresponds to config.php file (configuration or settings page)

    // Lead Sources
    $(document).on('click', '.saveSource', function() {  // Update Source
        var id = $(this).closest('tr').attr('class').split(' ')[1];
        var name = $(this).closest('tr').find('.name').val();
        var desc = $(this).closest('tr').find('.description').val();
        var type = 'saveSource';
        if (name == '') {
            alert('Name cannot be empty.');
            return false;
        }
        var dataString = 'type=' + type + '&id=' + id + '&name=' + name + '&desc=' + desc + '&token=' + token;
        $.ajax({  
            type: "POST",  
            dataType: 'json',
            url: "ajax/ajaxFunctions.php",  
            data: dataString,  
            success: function(response) {
                if (response.result == '1') {
                    $('.entryItem.' + id).effect("highlight", {}, 3000);
                } else {
                    alert('There was a problem with communication, please try again.');
                }
            }
        });
        return false;
    });

    $(document).on('click', '.saveNewSource', function() {  // add new Source
        var name = $(this).closest('table').find('.newLeadSource').val();
        var desc = $(this).closest('table').find('.newLeadDesc').val();
        var type = 'saveNewSource';
        if (name == '') {
            alert('Name cannot be empty.');
            return false;
        }
        var dataString = 'type=' + type + '&name=' + name + '&desc=' + desc + '&token=' + token;
        $.ajax({  
            type: "POST",  
            dataType: 'json',
            url: "ajax/ajaxFunctions.php",  
            data: dataString,  
            success: function(response) {
                if (response.result == '1') {
                    var vals = response.vals;
                    var entry = '<tr class="entryItem ' + response.insertID + '">'
                              + '<td>Name: </td>'
                              + '<td class="itemName">'
                              + '<input type="text" class="name lnField" size="50" value="' + vals.sourceName + '" /></td>'
                              + '<td>Description:</td>'
                              + '<td class="notes"><input type="text" class="description lnDesc" value="' 
                              + vals.description + '" /></td>'
                              + '<td><button class="smallButtons blueButton saveSource">Save</button>&nbsp;&nbsp;'
                              + '<button class="smallButtons redButton removeSource">Delete</button></td></tr>';
                    $('.configure').prepend(entry);
                    $('.entryItem.' + response.insertID).effect("highlight", {}, 3000);
                    $('.addNewSource .newLeadSource').val('');
                    $('.addNewSource .newLeadDesc').val('');
                    // saved
                } else {
                    alert('There was a problem with communication, please try again.');
                }
            }
        });
        return false;
    });

    $(document).on('click', '.removeSource', function() {  // Delete Source prompt
        var id = $(this).closest('tr').attr('class').split(' ')[1];
        var name = $(this).closest('tr').find('.name').val();
        var content = '<h3 class="modalH">Delete Source</h3>' 
                 + '<p class="delSource ' + id + '">Are you sure you wish to delete the lead source <b>' + name + '</b>?<br />'
                 + ' Note: Leads that belong to this group will be moved to the "None" group</p>'
                 + '<p class="buttonRow"><button class="buttons blueButton closeModal">Cancel</button>&nbsp;'
                 + '<button class="buttons redButton confirmRemoveSource">Delete</button></p>';
        $(content).modal({onOpen: function (dialog) {
            dialog.overlay.fadeIn('fast', function () {
                dialog.container.fadeIn('fast', function () {
                    dialog.data.fadeIn('fast');
                });
            });
        }, minHeight:150});
    });

    $(document).on('click', '.confirmRemoveSource', function() {  // Delete the Source
        var id = $('.delSource').attr('class').split(' ')[1];
        var type = 'deleteSource';
        var dataString = 'type=' + type + '&id=' + id + '&token=' + token;
        $.ajax({  
            type: "POST",  
            dataType: 'json',
            url: "ajax/ajaxFunctions.php",  
            data: dataString,  
            success: function(response) {
                if (response.result == '1') {
                    $('.configure').find('.entryItem.' + id).remove();
                    $.modal.close();
                } else {
                    alert('There was a problem with communication, please try again.');
                }
            }
        });
        return false;
    });
    // End Lead Sources

    // Lead Types
    $(document).on('click', '.saveType', function() {  // Update Type
        var id = $(this).closest('tr').attr('class').split(' ')[1];
        var name = $(this).closest('tr').find('.name').val();
        var desc = $(this).closest('tr').find('.description').val();
        var type = 'saveType';
        if (name == '') {
            alert('Name cannot be empty.');
            return false;
        }
        var dataString = 'type=' + type + '&id=' + id + '&name=' + name + '&desc=' + desc + '&token=' + token;
        $.ajax({  
            type: "POST",  
            dataType: 'json',
            url: "ajax/ajaxFunctions.php",  
            data: dataString,  
            success: function(response) {
                if (response.result == '1') {
                    $('.entryItem.' + id).effect("highlight", {}, 3000);
                } else {
                    alert('There was a problem with communication, please try again.');
                }
            }
        });
        return false;
    });

    $(document).on('click', '.saveNewType', function() {  // add new Type
        var name = $(this).closest('table').find('.newLeadType').val();
        var desc = $(this).closest('table').find('.newLeadDesc').val();
        var type = 'saveNewType';
        if (name == '') {
            alert('Name cannot be empty.');
            return false;
        }
        var dataString = 'type=' + type + '&name=' + name + '&desc=' + desc + '&token=' + token;
        $.ajax({  
            type: "POST",  
            dataType: 'json',
            url: "ajax/ajaxFunctions.php",  
            data: dataString,  
            success: function(response) {
                if (response.result == '1') {
                    var vals = response.vals;
                    var entry = '<tr class="entryItem ' + response.insertID + '">'
                              + '<td>Name: </td>'
                              + '<td class="itemName">'
                              + '<input type="text" class="name lnField" size="50" value="' + vals.typeName + '" /></td>'
                              + '<td>Description:</td>'
                              + '<td class="notes"><input type="text" class="description lnDesc" value="' 
                              + vals.description + '" /></td>'
                              + '<td><button class="smallButtons blueButton saveType">Save</button>&nbsp;&nbsp;'
                              + '<button class="smallButtons redButton removeType">Delete</button></td></tr>';
                    $('.configureT').prepend(entry);
                    $('.entryItem.' + response.insertID).effect("highlight", {}, 3000);
                    $('.addNewType .newLeadType').val('');
                    $('.addNewType .newLeadDesc').val('');
                    // saved
                } else {
                    alert('There was a problem with communication, please try again.');
                }
            }
        });
        return false;
    });

    $(document).on('click', '.removeType', function() {  // Delete Type prompt
        var id = $(this).closest('tr').attr('class').split(' ')[1];
        var name = $(this).closest('tr').find('.name').val();
        var content = '<h3 class="modalH">Delete Type</h3>' 
                 + '<p class="delType ' + id + '">Are you sure you wish to delete the lead type <b>' + name + '</b>?<br />'
                 + ' Note: Leads that belong to this group will be moved to the "None" group</p>'
                 + '<p class="buttonRow"><button class="buttons blueButton closeModal">Cancel</button>&nbsp;'
                 + '<button class="buttons redButton confirmRemoveType">Delete</button></p>';
        $(content).modal({onOpen: function (dialog) {
            dialog.overlay.fadeIn('fast', function () {
                dialog.container.fadeIn('fast', function () {
                    dialog.data.fadeIn('fast');
                });
            });
        }, minHeight:150});
    });

    $(document).on('click', '.confirmRemoveType', function() {  // Delete the Type
        var id = $('.delType').attr('class').split(' ')[1];
        var type = 'deleteType';
        var dataString = 'type=' + type + '&id=' + id + '&token=' + token;
        $.ajax({  
            type: "POST",  
            dataType: 'json',
            url: "ajax/ajaxFunctions.php",  
            data: dataString,  
            success: function(response) {
                if (response.result == '1') {
                    $('.configureT').find('.entryItem.' + id).remove();
                    $.modal.close();
                } else {
                    alert('There was a problem with communication, please try again.');
                }
            }
        });
        return false;
    });
    // End lead Type

    // lead status
    $(document).on('click', '.saveStatus', function() {  // Update Status
        var id = $(this).closest('tr').attr('class').split(' ')[1];
        var name = $(this).closest('tr').find('.name').val();
        var desc = $(this).closest('tr').find('.description').val();
        var type = 'saveStatus';
        if (name == '') {
            alert('Name cannot be empty.');
            return false;
        }
        var dataString = 'type=' + type + '&id=' + id + '&name=' + name + '&desc=' + desc + '&token=' + token;
        $.ajax({  
            type: "POST",  
            dataType: 'json',
            url: "ajax/ajaxFunctions.php",  
            data: dataString,  
            success: function(response) {
                if (response.result == '1') {
                    $('.entryItem.' + id).effect("highlight", {}, 3000);
                    // saved
                } else {
                    alert('There was a problem with communication, please try again.');
                }
            }
        });
        return false;
    });

    $(document).on('click', '.saveNewStatus', function() {  // add new status
        var name = $(this).closest('table').find('.newLeadStatus').val();
        var desc = $(this).closest('table').find('.newLeadDesc').val();
        var type = 'saveNewStatus';
        if (name == '') {
            alert('Name cannot be empty.');
            return false;
        }
        var dataString = 'type=' + type + '&name=' + name + '&desc=' + desc + '&token=' + token;
        $.ajax({  
            type: "POST",  
            dataType: 'json',
            url: "ajax/ajaxFunctions.php",  
            data: dataString,  
            success: function(response) {
                if (response.result == '1') {
                    var vals = response.vals;
                    var entry = '<tr class="entryItem ' + response.insertID + '">'
                              + '<td>Name: </td>'
                              + '<td class="itemName">'
                              + '<input type="text" class="name lnField" size="50" value="' + vals.statusName + '" /></td>'
                              + '<td>Description:</td>'
                              + '<td class="notes"><input type="text" class="description lnDesc" value="' 
                              + vals.description + '" /></td>'
                              + '<td><button class="smallButtons blueButton saveStatus">Save</button>&nbsp;&nbsp;'
                              + '<button class="smallButtons redButton removeStatus">Delete</button></td></tr>';
                    $('.configureS').prepend(entry);
                    $('.entryItem.' + response.insertID).effect("highlight", {}, 3000);
                    $('.addNewStatus .newLeadStatus').val('');
                    $('.addNewStatus .newLeadDesc').val('');
                    // saved
                } else {
                    alert('There was a problem with communication, please try again.');
                }
            }
        });
        return false;
    });

    $(document).on('click', '.removeStatus', function() {  // Delete Status prompt
        var id = $(this).closest('tr').attr('class').split(' ')[1];
        var name = $(this).closest('tr').find('.name').val();
        var content = '<h3 class="modalH">Delete Status</h3>' 
                 + '<p class="delStatus ' + id + '">Are you sure you wish to delete the lead status <b>' + name + '</b>?<br />'
                 + ' Note: Leads that belong to this group will be moved to the "None" group</p>'
                 + '<p class="buttonRow"><button class="buttons blueButton closeModal">Cancel</button>&nbsp;'
                 + '<button class="buttons redButton confirmRemoveStatus">Delete</button></p>';
        $(content).modal({onOpen: function (dialog) {
            dialog.overlay.fadeIn('fast', function () {
                dialog.container.fadeIn('fast', function () {
                    dialog.data.fadeIn('fast');
                });
            });
        }, minHeight:150});
    });

    $(document).on('click', '.confirmRemoveStatus', function() {  // Delete the Status
        var id = $('.delStatus').attr('class').split(' ')[1];
        var type = 'deleteStatus';
        var dataString = 'type=' + type + '&id=' + id + '&token=' + token;
        $.ajax({  
            type: "POST",  
            dataType: 'json',
            url: "ajax/ajaxFunctions.php",  
            data: dataString,  
            success: function(response) {
                if (response.result == '1') {
                    $('.configureS').find('.entryItem.' + id).remove();
                    $.modal.close();
                } else {
                    alert('There was a problem with communication, please try again.');
                }
            }
        });
        return false;
    });

    // End lead status

    // Start Manage Users

    $(document).on('click', '.addNewUser', function() {  // Add a new user
        var content = '<h3 class="modalH">Add a new user</h3><fieldset><legend>Create User</legend>'
                    + '<table class="newUser">'
                    + '<tr class="userRole"><td>Role</td>'
                    + '<td>Admin: <input type="radio" class="Admin" value="1" name="role">&nbsp;&nbsp;&nbsp;&nbsp;'
                    + 'User: <input type="radio" class="User" value="2" name="role">&nbsp;&nbsp;&nbsp;&nbsp;'
                    + 'Read Only: <input type="radio" class="ReadOnly" value="0" name="role"></td>'
                    + '</tr>'
                    + '<tr><td>Username:</td><td><input type="text" class="userName" /></td></tr>'
                    + '<tr><td>First Name:</td><td><input type="text" class="firstName" /></td></tr>'
                    + '<tr><td>Last Name:</td><td><input type="text" class="lastName" /></td></tr>'
                    + '<tr><td>Password:</td><td><input type="password" class="password" /></td></tr>'
                    + '<tr><td>Confirm Password: </td><td><input type="password" class="confirm" /></td></tr>'
                    + '<tr><td>Email Address: </td><td><input type="text" class="userEmail" /></td></tr>'
                    + '</table></fieldSet>'
                    + '<p class="buttonRow"><button class="buttons blueButton closeModal">Cancel</button>&nbsp;'
                    + '<button class="buttons greenButton saveUser">Save</button></p>';
        $(content).modal({onOpen: function (dialog) {
            dialog.overlay.fadeIn('fast', function () {
                dialog.container.fadeIn('fast', function () {
                    dialog.data.fadeIn('fast');
                });
            });
        }, minHeight:380});
        $(":input").each(function (i) { $(this).attr('tabindex', i + 1); });
        return false;
    });

    $(document).on('click', '.saveUser', function() { // Save new user
        var isAdmin   = $('input[name=role]:checked').val();
        var userName  = $('.userName').val();
        var firstName = $('.firstName').val();
        var lastName  = $('.lastName').val();
        var password  = $('.password').val();
        var confirmP  = $('.confirm').val();
        var userEmail = $('.userEmail').val();
        var ownLeads = 0;
        if (isAdmin == 2) {  // User, we need to get 'ownLeads' value
            ownLeads = $('.ownLeads:checked').val() == 'on' ? '1': '0';
        }
        if (!isAdmin) {
            alert('Please Select a role for this user.');
            return false;
        }
        if (userName == '') {
            alert('Please Enter a username.');
            return false;
        }
        if (firstName == '') {
            alert('Please Enter a first name.');
            return false;
        }
        if (lastName == '') {
            alert('Please Enter a last name.');
            return false;
        }
        if (password == '') {
            alert('Please Enter a Password.');
            return false;
        } 
        if (confirmP == '') {
            alert('Please Enter a confirm password.');
            return false;
        }
        if (password != confirmP) {
            alert('Passwords do not match.');
            return false;
        }
        if (password.length < 6) {
            alert('Passwords need to be at least 6 characters long.');
            return false;
        }
        if (userEmail == '') {
            alert('Please Enter an email address.');
            return false;
        }

        password = Sha1.hash(password);  // hash our password
        var type = 'saveUser';
        $.ajax({  
            type: "POST",  
            dataType: 'json',
            url: "ajax/ajaxFunctions.php",  
            data: {type:type,isAdmin:isAdmin,userName:userName,password:password,firstName:firstName,lastName:lastName,
                    userEmail:userEmail,ownLeads:ownLeads,token:token},  
            success: function(response) {
                if (response.result == '1') {
                    $.modal.close();
                    if (isAdmin == '1') {
                        var roleText = 'Admin';
                    } else if (isAdmin == '2') {
                        var roleText = 'User';
                    } else if (isAdmin == '0') {
                        var roleText = 'Read Only';
                    }

                    var data = '<tr><td></td>'
                             + '<td>' + firstName + ' ' + lastName + '</td>'
                             + '<td>' + userName + '</td>'
                             + '<td>' + userEmail + '</td>'
                             + '<td>' + response.viewDate + '</td>'
                             + '<td>' + roleText + '</td>'
                             + '<td><button class="smallButtons blackButton changeAccount ' + response.insertID + '">'
                             + 'Update</button>&nbsp;&nbsp;'
                             + '<button class="smallButtons redButton removeUser ' + response.insertID + '">'
                             + 'Delete</button></td>';
                    $('.currentUsers').append(data);
                    if (ownLeads == 1) {
                        $('.changeAccount.' + response.insertID).closest('tr').find("td:nth-child(6)").addClass('ownLeads');
                    } else {
                        $('.changeAccount.' + response.insertID).closest('tr').find("td:nth-child(6)").removeClass('ownLeads');
                    }
                    $('.removeUser.' + response.insertID).closest('tr').effect("highlight", {}, 3000);

                } else if (response.result == '2') {
                    alert(response.msg);
                    return false;
                } else {
                    alert('There was a problem with communication, please try again.');
                }
            }
        });
        return false;
    });

    $(document).on('click', '.removeUser', function() {     // Delete a user
        var id = $(this).attr('class').split(' ')[3];
        var user = $(this).closest('tr').find("td:nth-child(2)").text();
        var content = '<h3 class="modalH">Delete User</h3>'
                    + '<p class="deleteUser ' + id + '">'
                    + 'Are you sure you wish to remove the user account for <b>' + user + '</b>?</p>'
                    + '<p class="buttonRow"><button class="buttons blueButton closeModal">Cancel</button>&nbsp;'
                    + '<button class="buttons redButton confRemoveUser">Delete</button></p>';
        $(content).modal({onOpen: function (dialog) {
            dialog.overlay.fadeIn('fast', function () {
                dialog.container.fadeIn('fast', function () {
                    dialog.data.fadeIn('fast');
                });
            });
        }, minHeight:150});
    });

    $(document).on('click', '.confRemoveUser', function() { // Confirm deletion of user
        var type = 'deleteUser';
        var id = $('.deleteUser').attr('class').split(' ')[1];
        var dataString = 'type='+ type + '&id=' + id + '&token=' + token; 
        $.ajax({  
            type: "POST",  
            dataType: 'json',
            url: "ajax/ajaxFunctions.php",  
            data: dataString,  
            success: function(response) {
                if (response.result == '1') {
                    $.modal.close();
                    $('.currentUsers').find('.removeUser.' + id).closest('tr').remove();
                
                } else if (response.result == '2') {
                    alert(response.msg);

                } else {
                    alert('There was a problem with communication, please try again.');
                }
            }
        });
        return false;
    });

    $(document).on('click', '.changeAccount', function() { // Modify account
        var id = $(this).attr('class').split(' ')[3];
        var email = $(this).closest('tr').find("td:nth-child(4)").text();
        var role = $(this).closest('tr').find("td:nth-child(6)").text();
        var ownLeads = $(this).closest('tr').find("td:nth-child(6)").hasClass('ownLeads');
        var userName = $(this).closest('tr').find("td:nth-child(3)").text();
        var content = '<h3 class="modalH">Modify User</h3>'
                    + '<p class="modifyUser ' + id + '">'
                    + '<fieldset><legend>Change User Account for ' + userName + '</legend>'
                    + '<table class="modUserTable">'
                    + '<tr class="userRole"><td>Role</td>'
                    + '<td>Admin: <input type="radio" class="Admin" value="1" name="role">&nbsp;&nbsp;&nbsp;&nbsp;' 
                    + 'User: <input type="radio" class="User" value="2" name="role">&nbsp;&nbsp;&nbsp;&nbsp;'
                    + 'Read Only: <input type="radio" class="ReadOnly" value="0" name="role"></td>'
                    + '</tr>'
                    + '<td>Email Address: </td><td><input type="text" class="userEmail" value="' + email + '" /></td></tr>'
                    + '<td>Change Password?</td>'
                    + '<td><input type="checkbox" class="changePass" /></td></tr>'
                    + '</table></fieldSet>'
                    + '<p class="buttonRow"><button class="buttons blueButton closeModal">Cancel</button>&nbsp;'
                    + '<button class="buttons yellowButton confModUser">Modify</button></p>';
        $(content).modal({onOpen: function (dialog) {
            dialog.overlay.fadeIn('fast', function () {
                dialog.container.fadeIn('fast', function () {
                    dialog.data.fadeIn('fast');
                });
            });
        }, minHeight:300});
        if (role == 'Admin') {
            $('.Admin').prop('checked', true);
        } else if (role == 'User') {
            $('.User').prop('checked', true);
            if (ownLeads == true) {
                var checked = 'checked="checked"';
            } else {
                var checked = '';
            }
            roleChange(2, checked);
        } else if (role == 'Read Only') {
            $('.ReadOnly').prop('checked', true);
        }
    });

    $(document).on('change', "input[name='role']", function() {  // Show Edit all or Edit own option
        var role = $(this).val();
        roleChange(role);
    });

    function roleChange(role, checked) {
        if (!checked) {
            checked = '';
        }
        if (role == '2') {      // This is a user so we show option
            var data = '<tr class="leadView">'
                     + '<td>Manages own leads only?</td><td><input type="checkbox" ' + checked + ' class="ownLeads">&nbsp;&nbsp;'
                     + '<img src="img/information.png" title="If selected, user can only view and manage their own leads that they'
                     + ' create.  Admins and Users without this checked can manage all leads." /></td></tr>';
            $('.userRole').after(data)
        } else {
            $('.leadView').remove();
        }
    };

    $(document).on('click', '.changePass', function() {  // Allow password modification
        if ($(this).is(':checked')) {
            var data = '<tr><td>New Password: </td><td><input class="newPass" type="password" /></td></tr>'
                     + '<tr><td>Confirm Password: </td><td><input class="passConfirm" type="password" /></td></tr>';
            $('.modUserTable').append(data);
        } else {
            $('.modUserTable').find('.newPass').closest('tr').remove();
            $('.modUserTable').find('.passConfirm').closest('tr').remove();
        }
    });

    $(document).on('click', '.confModUser', function() {    // continue modify account
        var id = $('.modifyUser').attr('class').split(' ')[1];
        var email = $('.userEmail').val();
        var isAdmin = $('input[name=role]:checked').val();
        var ownLeads = 0;
        if (isAdmin == 2) {  // User, we need to get 'ownLeads' value
            ownLeads = $('.ownLeads:checked').val() == 'on' ? '1': '0';
        }
        if ($('.changePass').is(':checked')) {
            var updatePass = 'true';
            var password = $('.newPass').val();
            var passConfirm = $('.passConfirm').val();
        } else {
            var updatePass = 'false';
            var password = '';
            var passConfirm = '';
        }

        if (password != passConfirm) {
            alert('Passwords do not match.');
            return false;
        }
        password = Sha1.hash(password);  // hash our password
        var type = 'modifyUser';
        $.ajax({  
            type: "POST",  
            dataType: 'json',
            url: "ajax/ajaxFunctions.php",  
            data: {type:type,id:id,email:email,updatePass:updatePass,password:password,isAdmin:isAdmin,ownLeads:ownLeads,
                    token:token},
            success: function(response) {
                if (response.result == '1') {
                    // update page
                    if (isAdmin == '1') {
                        var roleText = 'Admin';
                    } else if (isAdmin == '2') {
                        var roleText = 'User';
                    } else if (isAdmin == '0') {
                        var roleText = 'Read Only';
                    }
                    $.modal.close();
                    $('.changeAccount.' + id).closest('tr').find("td:nth-child(4)").text(email);
                    $('.changeAccount.' + id).closest('tr').find("td:nth-child(6)").text(roleText);
                    if (ownLeads == 1) {
                        $('.changeAccount.' + id).closest('tr').find("td:nth-child(6)").addClass('ownLeads');
                    } else {
                        $('.changeAccount.' + id).closest('tr').find("td:nth-child(6)").removeClass('ownLeads');
                    }
                    $('.changeAccount.' + id).closest('tr').effect("highlight", {}, 3000);
                
                } else if (response.result == '2') {
                    alert(response.msg);

                } else {
                    alert('There was a problem with communication, please try again.');
                }
            }
        });
        return false;
    });

    // End Manage Users

    // Start Import Export

    $(function() {
        $( "#tabs" ).tabs();
    });

    $(document).ajaxStart(function() { 
        $("#loading").show();
        $('#buttonUpload').attr('disabled', 'disabled');
    });

    $(document).ajaxComplete(function() {
        $("#loading").hide();
        $('#buttonUpload').removeAttr('disabled');
    });

    function ajaxFileUpload() {                // Ajax upload csv file...step 1
        var method='uploadFile';

        $.ajaxFileUpload
        (
            {
                url:'ajax/import.php',
                secureuri:false,
                fileElementId:'fileToUpload',
                dataType: 'json',
                data:{token: token, method: method},
                success: function (data, status)
                {
                    if(typeof(data.error) != 'undefined') {
                        if(data.error == '1') {
                            alert(data.msg);
                            return false;
                        } else if(data.error != '') {
                            alert(data.error);
                        } else {               // success
                            var output = '<p><b>Step 2.</b> Align your Data:</p>'
                                     + '<p>Match your data (right Column) with the columns on the left.  You can'
                                     + ' drag and drop in order and remove unneccessary columns for the import. Use the'
                                     + ' Blank Entries to fill gaps in your data.</p>'
                                     + '<table class="matchData">'
                                     + '<tr><td>First Name</td></tr>'
                                     + '<tr><td>Last Name</td</tr>'
                                     + '<tr><td>' + siteSettings.Address + '</td></tr>'
                                     + '<tr><td>' + siteSettings.Phone + '</td></tr>'
                                     + '<tr><td>' + siteSettings.secondaryPhone + '</td></tr>'
                                     + '<tr><td>' + siteSettings.Fax + '</td></tr>'
                                     + '<tr><td>Primary Email</td></tr>'
                                     + '<tr><td>Other Email 1</td></tr>'
                                     + '<tr><td>Other Email 2</td></tr>'
                                     + '<tr><td>Other Email 3</td></tr>'
                                     + '<tr><td>' + siteSettings.City + '</td></tr>'
                                     + '<tr><td>' + siteSettings.State + '</td></tr>'
                                     + '<tr><td>' + siteSettings.Country + '</td></tr>'
                                     + '<tr><td>' + siteSettings.Zip + '</td></tr>'
                                     + '<tr><td>' + siteSettings.customField1 + '</td></tr>'
                                     + '<tr><td>' + siteSettings.customField2 + '</td></tr>'
                                     + '<tr><td>' + siteSettings.customField3 + '</td></tr>'
                                     + '<tr><td>Notes</td></tr>'
                                     + '</table>'
                                     + '<table id="sortData" class="sortData"><tbody>';
                            $.each(data.topRow, function(n, val) {
                                output += '<tr><td class="sortEntry"><img src="img/updown.png" />'
                                        + '<span class="sortName">' + val + '</span></td>'
                                        + '<td><a href="#" class="removeCol ">'
                                        + '<img src="img/delete.png" title="Remove From Import">'
                                        + '</a></td></tr>';
                            });                            
                            output += '</tbody></table><div class="addBlankCol">'
                                    + '<p><a href="#" class="addBlank">Add Blank Entry</a></p><br /><br />'
                                    + '<p><button class="buttons redButton cancelImport">Cancel</button>'
                                    + '&nbsp;&nbsp;<button class="buttons blueButton continueImport">Continue >></button>'
                                    + '</div>'
                                    + '<div class="afterData"></div>';


                            $('.importSteps').html(output);                              
                            $("#sortData tbody").sortable({
                                helper: fixHelper
                            }).disableSelection();

                        }
                    }
                },
                error: function (data, status, e)
                {
                    alert(e);
                }
            }
        )
        return false;
    }

    $(document).on('click', '.continueImport', function() { // Continue, final step
        var method = 'continueImport';
        var columnOrderArray = {};
        var inc = 0;
        $('.sortData').find('tr').each(function() {
            inc++;
            var column = $(this).find('.sortName').text();
            columnOrderArray[inc] = {
                name: column
            }
        });
        var columnOrder = JSON.stringify(columnOrderArray);
        $.ajax({
            type: "POST",
            dataType: 'json',
            url: "ajax/import.php",
            data: {method: method, columnOrder: columnOrder, token: token},
            success: function(response) {
                if (response.error == '0') {
                    var output = '<p><b>Step 3.</b> Additional Settings:</b></p>'
                               + 'Choose how you would like to import this data:</p>'
                               + '<p>Choose the Source, Type, and Status for the imported data:</p><br />'
                               + '<table>'
                               + '<tr><td>Source:</td>'
                               + '<td><select class="selectSource"><option value="">--Select--</option>';
                    $.each(response.leadSource, function(n, val) {
                        output += '<option value="' + val.sourceID + '">' + val.sourceName + '</option>';
                    });
                    output += '</select></td></tr>'
                            + '<tr><td>Type:</td>'
                            + '<td><select class="selectType"><option value="">--Select--</option>';
                    $.each(response.leadType, function(n, val) {
                        output += '<option value="' + val.typeID + '">' + val.typeName + '</option>';
                    });
                    output += '</select></td></tr>'
                            + '<tr><td>Status:</td>'
                            + '<td><select class="selectStatus"><option value="">--Select--</option>';
                    $.each(response.leadStatus, function(n, val) {
                        output += '<option value="' + val.id + '">' + val.statusName + '</option>';
                    });
                    output += '</select></td></tr></table>'
                            + '<p> <button class="buttons redButton cancelImport">Cancel</button>&nbsp;&nbsp;'
                            + '<button class="buttons blueButton finishImport">Finish</button></p>';
                    $('.importSteps').html(output);
                    
                } else {
                    alert('There was a problem with communication, please try again.');
                }
            }
        });
    });

    $(document).on('click', '.finishImport', function() { // Finish Import
        var selectType = $('.selectType').val();
        var selectSource = $('.selectSource').val();
        var selectStatus = $('.selectStatus').val();
        if (selectSource == '') {
            alert('Please Select a Source for this data.');
            return false;
        }
        if (selectType == '') {
            alert('Please select a Type for this data.');
            return false;
        }
        if (selectStatus == '') {
            alert('Please select a status for this data.');
            return false;
        }
        var method = 'finishImport';
        $.ajax({
            type: "POST",
            dataType: 'json',
            url: "ajax/import.php",
            data: {method: method, source: selectSource, 
                type: selectType, status: selectStatus, token: token},
            success: function(response) {
                if (response.error == '0') {
                var output = '<p>Your Insert is complete, There were ' + response.insertCount + ' total entries'
                         + ' added to the database, please go verify your <a href="index.php">leads</a>.</p>';
                $('.importSteps').html(output);
            } else {
                    alert('There was a problem with communication, please try again.');
                }
            }
        });
    });

    $(document).on('click', '.cancelImport', function() {  // Cancel the import
        var method = 'cancelImport';
        $.ajax({
            type: "POST",
            dataType: 'json',
            url: "ajax/import.php",
            data: {method: method, token: token},
            success: function(response) {
                if (response.error == '0') {
                   location.reload(); 

                } else {
                    alert('There was a problem with communication, please try again.');
                }
            }
        });
        return false;
    });


    $(document).on('click', '.addBlank', function() { // Insert blank entry for empty column
        var data = '<tr><td class="blankData"><img src="img/updown.png" /><u><span class="sortName">No Data</span></u></td>'
                 + '<td><a href="#" class="removeCol ">'
                 + '<img src="img/delete.png" title="Remove From Import">';
        $(data).prependTo('.sortData').effect("highlight", {}, 1000);
        return false;
    });

    $(document).on('click', '.removeCol', function() { // Remove column from import
        $(this).closest('tr').fadeOut(150, function() { 
            $(this).remove();
        });
        return false;
    });

    var fixHelper = function(e, ui) {                  // Sortable helper to keep the width on cells
        ui.children().each(function() {
            $(this).width($(this).width());
        });
        return ui;
    };

    // End Import Export

    // Site Settings
    $(document).on('click', '.savePageResults', function() {   // Save the new pagination results setting
        var rpp = $('.pageResults').val();
        if (isNaN(rpp)) {
            alert('Please insert a numerical value.');
        } else if (rpp < 1 || rpp > 5000) {
            alert('Please insert a value between 1 and 5000.');
        } else {
            var type = 'updateRPP';
            $.ajax({
                type: "POST",
                dataType: 'json',
                url: "ajax/ajaxFunctions.php",
                data: {type: type, rpp: rpp, token: token},
                success: function(response) {
                    if (response.result == '1') {
                        $('.pageResultsP').effect("highlight", {}, 3000);
                    } else {
                        alert('There was a problem with communication, please try again.');
                    }
                }
            });
        }
    });

    $(document).on('click', '.saveField', function() {   // Save the Field Name
        var td = $(this).closest('td');
        var fieldVal= td.find('.lnField').val();
        var fieldName = td.find('.lnField').attr('class').split(' ')[0];
        if (fieldVal == '') {
            alert ('Field Name is empty, please provide a name.');
            return false;
        }
        var type = 'updateField';
        $.ajax({
            type: "POST",
            dataType: 'json',
            url: "ajax/ajaxFunctions.php",
            data: {type: type, fieldVal: fieldVal, fieldName: fieldName, token: token},
            success: function(response) {
                if (response.result == '1') {
                    td.effect("highlight", {}, 3000);
                    window.location = "config.php?page=siteSettings";
                } else {
                    alert('There was a problem with communication, please try again.');
                }
            }
        });
    });

    $(function() {                      // Make Sortable Results for Columns to show
        $( "#sortableCols, #sortableCols2" ).sortable({
            connectWith: ".connectedSortable"
        }).disableSelection();
    });

    $(document).on('click', '.saveOrder', function() { // Save the order of columns to display
        var sortOrder = {};
        var i = 0;
        var name = false;
        $('#sortableCols li').each(function () {         // Precursor check, name must exist
            if ($(this).text() == 'Name') {
                name = true;
            }
        });
        if (name == false) {
            alert('You must at least keep "Name" in "Columns Used".');
            return false;
        }

        $('#sortableCols li').each(function () {         // Keepers
            i++;
            var id = $(this).attr('class').replace(/\D+/g,'');
            sortOrder[i] = {
                id: id,
                order: i,
                used: 1
            };
        });

        $('#sortableCols2 li').each(function () {        // Non Keepers
            i++;
            var id = $(this).attr('class').replace(/\D+/g,'');
            sortOrder[i] = {
                id: id,
                order: i,
                used: 0
            };
        });
        var type = 'saveOrder';
        var sortOrderString = JSON.stringify(sortOrder);
        $.ajax({
            type: "POST",
            dataType: 'json',
            url: "ajax/ajaxFunctions.php",
            data: {type: type, order: sortOrderString, token: token},
            success: function(response) {
                if (response.result == '1') {
                    $('#sortableCols').effect("highlight", {}, 3000);
                
                } else if (response.result == '2') {
                    alert(response.msg);

                } else {
                    alert('There was a problem with communication, please try again.');
                }
            }
        });
        return false;
    });


    // End Site Settings
    // Remove leads from database
    $(document).on('click', '.emptyDB', function() { // Remove all leads (and corresponding data) from database
        var content = '<h3 class="modalH">Delete Leads from Database</h3>' 
                 + '<p><b>Final warning</b>...Are you sure you wish to remove all leads from the database?<br />'
                 + '<p class="buttonRow"><button class="buttons blueButton closeModal">Cancel</button>&nbsp;'
                 + '<button class="buttons redButton confirmEmptyDB">Delete All Leads</button></p>';
        $(content).modal({onOpen: function (dialog) {
            dialog.overlay.fadeIn('fast', function () {
                dialog.container.fadeIn('fast', function () {
                    dialog.data.fadeIn('fast');
                });
            });
        }, minHeight:150});
    });

    $(document).on('click', '.confirmEmptyDB', function() {    // Continue all leads removal
        var type = 'emptyDB';
        $.ajax({
            type: "POST",
            dataType: 'json',
            url: "ajax/ajaxFunctions.php",
            data: {type: type, token: token},
            success: function(response) {
                if (response.result == '1') {
                    $.modal.close();
                    var content = '<br /><h3 class="secTitle">Remove Lead data from database</h3>'
                                + '<hr class="thinLine"><br /><br />'
                                + 'All Lead Data has been Removed from the database,'
                                + ' start adding some new <a href="index.php">leads and contacts!</a>';
                    $('.emptyDBDisplay .configLeft').html(content);
                } else if (response.result == '2') {
                    alert(response.msg);

                } else {
                    alert('There was a problem with communication, please try again.');
                }
            }
        });
        return false;
    });
        

    // End Remove leads from database


    $(document).on('click', '.manageSource', function() {   // Show Source Div
        $('.sections').removeClass('selected');
        $(this).addClass('selected');
        $('.section').addClass('hidden');
        $('.sourceDisplay').removeClass('hidden');
        return false;
    });

    $(document).on('click', '.manageType', function() {   // Show Type Div
        $('.sections').removeClass('selected');
        $(this).addClass('selected');
        $('.section').addClass('hidden');
        $('.typeDisplay').removeClass('hidden');
        return false;
    });

    $(document).on('click', '.manageStatus', function() {   // Show Status Div
        $('.sections').removeClass('selected');
        $(this).addClass('selected');
        $('.section').addClass('hidden');
        $('.statusDisplay').removeClass('hidden');
        return false;
    });

    $(document).on('click', '.manageUsers', function() {   // Show Users Div
        $('.sections').removeClass('selected');
        $(this).addClass('selected');
        $('.section').addClass('hidden');
        $('.usersDisplay').removeClass('hidden');
        return false;
    });

    $(document).on('click', '.manageExp', function() {   // Show Export Import Div
        $('.sections').removeClass('selected');
        $(this).addClass('selected');
        $('.section').addClass('hidden');
        $('.exportDisplay').removeClass('hidden');
        return false;
    });

    $(document).on('click', '.manageSite', function() {   // Show Site Settings Div
        $('.sections').removeClass('selected');
        $(this).addClass('selected');
        $('.section').addClass('hidden');
        $('.siteDisplay').removeClass('hidden');
        return false;
    });

    $(document).on('click', '.emptyDatabase', function() {   // Show Empty Database Div
        $('.sections').removeClass('selected');
        $(this).addClass('selected');
        $('.section').addClass('hidden');
        $('.emptyDBDisplay').removeClass('hidden');
        return false;
    });

    $(document).on('click', '.showLogging', function() {   // Show Logging
        var page = 1;
        getLogs(page);
        return false;
    });

    $(document).on('click', '.logPage', function() { // Get the Page
        var search = $('.searchLog').val();
        page = $(this).text();
        getLogs(page, search);
        return false;
    });

    $(document).on('click', '.nextLog', function() { // Get the next page
        var search = $('.searchLog').val();
        page =  parseInt($('.current').text()) + 1;
        getLogs(page, search);
        return false;
    });

    $(document).on('click', '.prevLog', function() { // Get the previous page
        var search = $('.searchLog').val();
        page = parseInt($('.current').text()) - 1;
        getLogs(page, search);
        return false;
    });

    function getLogs(page, search) {                              // Get the log page and pagination
        $('.sections').removeClass('selected');
        $('.statusSelect').find('.showLogging').addClass('selected');
        $('.section').addClass('hidden');
        $('.loggingDisplay').removeClass('hidden');
        var type = 'showLog';
        $.ajax({
            type: "POST",
            dataType: 'json',
            url: "ajax/ajaxFunctions.php",
            data: {type: type, page: page, search: search, token: token},
            success: function(response) {
                if (response.result == '1') {
                    if (response.search == true) {
                        var search = response.searchTerm;
                    } else {
                        var search = '';
                    }
                    content = '<div class="pagingHolder">' + response.pagDiv + '</div>'
                            + '<div class="searchLogDiv"><input type="text" class="searchLog" value = "' + search + '"/>'
                            + '<button class="smallButtons blackButton goSearchLog">Search</button></div>'
                            + '<div class="clearBoth">&nbsp;</div>'
                            + '<table class="logs">'
                            + '<tr><th>User</th><th>Event</th><th>Detail</th><th>Event Time</th><th>Ip Address</th></tr>';
                    var i = 0;
                    var alt = 0;
                    $.each(response.log, function(n, val) {
                        i++;
                        alt = i & 1;
                        content += '<tr class="trClass' + alt + '"><td>' + val.userFirst + ' ' + val.userLast + '</td>'
                                 + '<td>' + val.event + '</td><td>' + val.detail + '</td>'
                                 + '<td>' + val.eventTime + '</td><td>' + val.ipAddr + '</td></tr>';
                    });            
                    content += '</table>';
                    $('.activityLog').html(content);
                } else if (response.result == '2') {
                    alert(response.msg);

                } else {
                    alert('There was a problem with communication, please try again.');
                }
            }
        });
        return false;
    };

    $(document).on('click', '.goSearchLog', function() {          // Search Logs
        var search = $('.searchLog').val();
        if (search == '') {
            alert('Please Enter a search term');
            return false;
        } else {
            getLogs(1, search);
        }
    });

    $(document).on('click', '.closeModal', function() {
        $.modal.close();
    });

    $(document).on('keydown', ':input', function(e) {   // tabbing correction on dynamic forms (remember to re-index them)
        var keyCode = e.keyCode || e.which;     
        if (keyCode == 9) {  
            var curTab = $(this).attr('tabindex');
            if (e.shiftKey) {
                curTab--;
            } else {
                curTab++;
            }
            $(":input[tabindex='"+curTab+"']").focus();
            return false;
        }
    });
