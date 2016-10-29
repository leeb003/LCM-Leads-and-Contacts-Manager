// lead.js corresponds to lead.php file (single lead)
$(document).on('click', '.addEmail', function() {         // Add another email to form up to 3
    var inc = 1;
    $('.secondaryEmail').each(function() {
        inc++;
    });
    var content = '<tr><td class="leadField">Secondary Email:</td><td><input type="text" class="secondaryEmail" /></td></tr>';
    $('.contactInfo').append(content);
    $('.addEmail').prop('checked', false);
    if (inc >= 3) {
        $('.addEmail').closest('tr').hide();
        return false;
    }
});

$(document).on('click', '.addEditContact', function() {           // Add or Edit a Contact
    var Address = '';
    var City = '';
    var Country = '';
    var Email = '';
    var Fax = '';
    var Phone = '';
    var secondaryPhone = '';
    var State = '';
    var Zip = '';
    var firstName = '';
    var lastName = '';
    var id = '';
    var sourceName = '';
    var statusName = '';
    var typeName = '';
    var existing = '';
    var customFIeld = '';
    var customField2 = '';
    var customField3 = '';
    var otherEmails = {};
    var Owner = '';
    if ($(this).hasClass('exists')) {  // Existing
    
        var type = 'editLead';
        var leadID = $(this).attr('class').split(' ')[2].replace(/\D+/g,'');
        var dataString = 'type='+ type + '&leadID=' + leadID + '&token=' + token;  
        $.ajax({  
            type: "POST",  
            dataType: 'json',
            url: "ajax/ajaxFunctions.php",  
            data: dataString,  
            success: function(response) {
                if (response.result == '1') {
                    var lead = response.lead;

                    existing = 'existing';
                    Address        = lead.Address;
                    City           = lead.City;
                    Country        = lead.Country;
                    Email          = lead.Email;
                    Fax            = lead.Fax;
                    Phone          = lead.Phone;
                    secondaryPhone = lead.secondaryPhone;
                    State          = lead.State;
                    Zip            = lead.Zip;
                    firstName      = lead.firstName;
                    lastName       = lead.lastName;
                    id             = lead.id;
                    sourceName     = lead.sourceName;
                    statusName     = lead.statusName;
                    typeName       = lead.typeName;
                    customField    = lead.customField;
                    customField2   = lead.customField2;
                    customField3   = lead.customField3;
                    otherEmails    = response.otherEmails;
                    Owner          = lead.assignedTo;
                    createForm(existing, Address, City, Country, Email, Fax, Phone, secondaryPhone, State, Zip, firstName,
                               lastName, id, sourceName, statusName, typeName, otherEmails,
                                customField, customField2, customField3, Owner);
                } else {
                    alert('There was a communication problem, please try again.');
                }
            }
        });
    } else {                        // New Lead
        createForm(existing, Address, City, Country, Email, Fax, Phone, secondaryPhone, State, Zip, firstName,
                   lastName, id, sourceName, statusName, typeName, otherEmails,
                   customField, customField2, customField3, Owner);
    }
});

function createForm(existing, Address, City, Country, Email, Fax, Phone, secondaryPhone, State, Zip, firstName,  // Build the form
                    lastName, id, sourceName, statusName, typeName, otherEmails,
                    customField, customField2, customField3, Owner) {
    if (existing == 'existing') {
        var modalTitle = 'Edit Existing Contact';
    } else {
        var modalTitle = 'Add A New Contact';
    }
    var otherEmailsCount = otherEmails.length;
    if (otherEmailsCount >= 3) {
        var hideAdd = 'hidden';
    } else {
        var hideAdd = '';
    }
    var content = '<h3 class="modalH">' + modalTitle + '</h3>'
                + '<fieldset><legend>Contact Info</legend>'
                + '<table class="contactInfo">'
                + '<tr><td class="leadField">First Name:</td>'
                + '<td><input type="text" class="firstName" value="' + firstName + '" /></td>'
                + '<td class="secCol">' + siteSettings.Phone + 
                ':</td><td><input type="text" class="phone" value="' + Phone + '" /></td></tr>'
                + '<tr><td class="leadField">Last Name:</td>'
                + '<td><input type="text" class="lastName" value="' + lastName + '" /></td>'
                + '<td class="secCol">' + siteSettings.secondaryPhone + ':</td>'
                + '<td><input type="text" class="secondaryPhone" value="' + secondaryPhone + '" /></td></tr>'
                + '<tr><td class="leadField">Primary Email: </td>'
                + '<td><input type="text" class="email" value="' + Email + '" /></td>'
                + '<td>' + siteSettings.Fax + ':</td><td><input type="text" class="fax" value="' + Fax + '" /></td></tr>'
                + '<tr class="' + hideAdd + '"><td></td><td></td><td></td><td>Add Email?: '
                + '<input type="checkbox" class="addEmail"/></td></tr>'
                + '</table></fieldset><br />'
                + '<fieldset><legend>Address</legend>'
                + '<table class="leadAddress">'
                + '<tr><td class="leadField">' + siteSettings.Address + ':</td><td><input type="text" class="address" value="' 
                + Address + '" /></td>'
                + '<td>' + siteSettings.State + ':</td><td><input type="text" class="state" value="' 
                + State + '" /></td></tr>'
                + '<tr><td class="leadField">' + siteSettings.City + ':</td><td><input type="text" class="city" value="' 
                + City + '" /></td>'
                + '<td>' + siteSettings.Country + ':</td><td><input type="text" class="country" value="' 
                + Country + '" /></td></tr>'
                + '<tr><td class="leadField">' + siteSettings.Zip + ':</td><td><input type="text" class="zipCode" value="' 
                + Zip + '" /></td>'
                + '<td></td>'
                + '<td></td></tr>'
                + '</table></fieldset><br />'
                + '<fieldset><legend>Categories</legend>'
                + '<table class="leadCategory">'
                + '<tr><td class="leadField">Source: </td><td><select class="leadSource">'

                $.each(leadSources, function(key, val) {
                        content += '<option value="' + val.sourceID + '">' + val.sourceName + '</option>';
                });
                content += '</select></td>'
                        + '<td class="leadField">' + siteSettings.customField1 + ':</td>'
                        + '<td><input type="text" class="customField" value="' + customField + '" /></td></tr>'
                        + '<tr><td class="leadField">Type: </td><td class="selectDrop"><select class="leadType">';

                $.each(leadTypes, function(key, val) {
                        content += '<option value="' + val.typeID + '">' + val.typeName + '</option>';
                });
                content += '</select></td>'
                        + '<td class="leadField">' + siteSettings.customField2 + ':</td>'
                        + '<td><input type="text" class="customField2" value="' + customField2 + '" /></td></tr>'
                        + '<tr><td class="leadField">Status: </td><td class="selectDrop"><select class="leadStatus">';
                $.each(leadStatuss, function(key, val) {
                        content += '<option value="' + val.id + '">' + val.statusName + '</option>';
                });
                content += '</select></td>'
                        + '<td class="leadField">' + siteSettings.customField3 + ':</td>'
                        + '<td><input type="text" class="customField3" value="' + customField3 + '" /></td></tr>'
                        + '<tr><td class="leadField">' + siteSettings.assignedTo + ':</td>'
                        + '<td class="selectDrop"><select class="leadOwner">';

                $.each(Owners, function(key, val) {
                        content += '<option value="' + val.id + '">' + val.first + ' ' + val.last + '</option>';
                });

                content += '</select></td></tr>'
                + '</table>'
                + '</fieldset>'
                + '<p class="buttonRow">'
                + '<button class="buttons greenButton saveLead ' + existing + ' ' + id + '">Save</button>&nbsp;&nbsp;'
                + '<button class="closeModal buttons blueButton">Cancel</button></p>';

    $(content).modal({onOpen: function (dialog) {
        dialog.overlay.fadeIn('fast', function () {
            dialog.container.fadeIn('fast', function () {
                dialog.data.fadeIn('fast');
            });
        });
    }, minHeight:640});

    // Set pre selected variables for existing leads
    if (existing == 'existing') {
        $('.leadSource option').filter(function() {   // Set lead source
            return $(this).text() == sourceName;
        }).attr('selected', true);

        $('.leadType option').filter(function() {     // Set lead type
            return $(this).text() == typeName;
        }).attr('selected', true);
        
        $('.leadStatus option') .filter(function() {  // Set lead status
            return $(this).text() == statusName;
        }).attr('selected', true);

        $('.leadOwner option').filter(function() {    // Set Owner
            return $(this).val() == Owner;
        }).attr('selected', true);

        $.each(otherEmails, function(key, val) {
        //otherEmails.each(function (i, val) {          // Add other emails
        var content = '<tr><td class="leadField">Secondary Email:</td>'
                    + '<td><input type="text" class="secondaryEmail" value="' + val.email + '" /></td></tr>';
        $('.contactInfo').append(content);
        });
    }

    $(":input").each(function (i) { $(this).attr('tabindex', i + 1); });
};

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

$(document).on('click', '.addNote', function() {                 // Add A Note
    var leadID= $(this).attr('class').split(' ')[1];
    var content = '<h3 class="modalH">Add a new Note</h3>'
                + '<table class="addNewNote">'
                + '<tr><td><textArea class="newNote" rows="5" cols="40"></textArea></td></tr>'
                + '</table>'
                + '<p class="buttonRow"><button class="buttons greenButton saveNote ' + leadID + '">Save</button>'
                + '&nbsp;&nbsp;<button class="closeModal buttons blueButton">Cancel</button></p>';

    $(content).modal({onOpen: function (dialog) {
        dialog.overlay.fadeIn('fast', function () {
            dialog.container.fadeIn('fast', function () {
                dialog.data.fadeIn('fast');
            });
        });
    }, minHeight:350});
    tinymce.init({
        selector: "textarea",
        entity_encoding : "raw",
        plugins: "paste"
    });
    return false;
});

$(document).on('click', '.editNote', function() {                 // Edit Notes
    var noteID= $(this).attr('class').split(' ')[1];
    var type = 'editNote';
    $.ajax({
        type: "POST",
        dataType: 'json',
        url: "ajax/ajaxFunctions.php",
        data: {type:type, noteID:noteID, token:token},
        success: function(response) {
            if (response.result == '1') {
                var content = '<h3 class="modalH">Edit Existing Note</h3>'
                    + '<table class="addNewNote">'
                    + '<tr><td><textArea class="newNote" rows="5" cols="40">' + response.note.Note + '</textArea></td></tr>'
                    + '</table>'
                    + '<p class="buttonRow"><button class="buttons greenButton saveEditNote ' + noteID + '">Save</button>'
                    + '&nbsp;&nbsp;<button class="closeModal buttons blueButton">Cancel</button></p>';

                $(content).modal({onOpen: function (dialog) {
                    dialog.overlay.fadeIn('fast', function () {
                        dialog.container.fadeIn('fast', function () {
                            dialog.data.fadeIn('fast');
                        });
                    });
                }, minHeight:350});
                tinymce.init({
                    selector: "textarea",
                    entity_encoding : "raw",
                    plugins: "paste"
                });

            } else {
                alert('There was a problem, please try again.');
            }
        }
    });
    return false;
});

$(document).on('click', '.saveEditNote', function() {                // Save an Edited Note
    var note = tinyMCE.activeEditor.getContent();
    if (note == '') {
        alert('Please Enter a note.');
        return false;
    }
    var type = 'saveEditNote';
    var noteID = $(this).attr('class').split(' ')[3];
    var dataString = 'type='+ type + '&noteID=' + noteID + '&note=' + note + '&token=' + token;
    $.ajax({
        type: "POST",
        dataType: 'json',
        url: "ajax/ajaxFunctions.php",
        data: dataString,
        success: function(response) {
            if (response.result == '1') {
                // remove No Notes section
                $('.noteContent.noNotes').remove();
                var newNote = '<div class="noteContainer ' + response.noteID + ' newNoteEntry">'
                            + '<div class="noteContent">' + response.note + '</div>'
                            + '<div class="userData">Creator: <span class="userHighlight"><b>' + response.userName + '</b>  '
                            + response.noteDate + '</span>'
                            + '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" class="editNote ' + response.noteID 
                            + '">Edit Note</a>'
                            + '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" class="removeNote ' + response.noteID
                            + '">Remove Note</a></div><hr class="thinLine">';
                            + '</div>';
                $('.noteContainer.' + response.noteID).replaceWith(newNote);
                $('.noteContainer.' + response.noteID + '.newNoteEntry').effect("highlight", {}, 3000);
                $.modal.close();
            } else {
                alert('There was a communication problem, please try again.');
            }
        }
    });
});

$(document).on('click', '.saveLead', function() {  // save the lead
    var existing = '';
    var id = '';
    if ($(this).hasClass('existing')) {
        existing = 'existing';
        id = $(this).attr('class').split(' ')[4];
    }
    var firstName = $('.firstName').val();
    var lastName = $('.lastName').val();
    var phone = $('.phone').val();
    var secondaryPhone = $('.secondaryPhone').val();
    var fax = $('.fax').val();
    var email = $('.email').val();
    var secondaryEmails = {};
    var inc = 0;
    $('.secondaryEmail').each(function() {
        inc++;
        secondaryEmails[inc] = {
            email: $(this).val()
        }
    });
    var secondaryEmails = JSON.stringify(secondaryEmails);
    var address = $('.address').val();
    var zipCode = $('.zipCode').val();
    var city = $('.city').val();
    var state = $('.state').val();
    var country = $('.country').val();
    var leadSource = $('.leadSource').val();
    var leadType = $('.leadType').val();
    var leadStatus = $('.leadStatus').val();
    var customField = $('.customField').val();
    var customField2 = $('.customField2').val();
    var customField3 = $('.customField3').val();
    var leadOwner    = $('.leadOwner').val();


    var type = 'saveLead';
    $.ajax({
        type: "POST",
        dataType: 'json',
        url: "ajax/ajaxFunctions.php",
        data: {type:type, firstName:firstName, lastName:lastName, phone:phone, secondaryPhone:secondaryPhone,
               fax:fax, email:email, secondaryEmails:secondaryEmails, address:address, zipCode:zipCode,
               city:city, state:state, country:country, leadSource:leadSource, leadType:leadType, leadStatus:leadStatus,
               customField:customField, customField2:customField2, customField3:customField3, token:token,
               existing:existing, id:id, leadOwner:leadOwner},
        success: function(response) {
           if (response.result == '1') {
                var insert = response.insertID
                var url = 'lead.php?lead=' + insert;
                window.location = url;
            } else {
                alert(response.msg);
            }
        }
    });
});

$(document).on('click', '.saveNote', function() {
    var note = tinyMCE.activeEditor.getContent();
    if (note == '') {
        alert('Please Enter a note.');
        return false;
    }
    var type = 'saveNote';
    var leadID = $(this).attr('class').split(' ')[3];
    var dataString = 'type='+ type + '&leadID=' + leadID + '&note=' + note + '&token=' + token; 
    $.ajax({
        type: "POST",  
        dataType: 'json',
        url: "ajax/ajaxFunctions.php",  
        data: dataString,  
        success: function(response) {
            if (response.result == '1') {
                // remove No Notes section
                $('.noteContent.noNotes').remove();
                var newNote = '<div class="noteContainer ' + response.noteID + ' newNoteEntry">'
                            + '<div class="noteContent">' + response.note + '</div>'
                            + '<div class="userData">Creator: <span class="userHighlight"><b>' + response.userName + '</b>  '
                            + response.noteDate + '</span>'
                            + '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" class="editNote ' + response.noteID 
                            + '">Edit Note</a>'
                            + '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" class="removeNote ' + response.noteID 
                            + '">Remove Note</a></div><hr class="thinLine">';
                            + '</div>';
                $('.addNote').after(newNote);
                $('.noteContainer.' + response.noteID + '.newNoteEntry').effect("highlight", {}, 3000);
                $.modal.close();
            } else {
                alert('There was a communication problem, please try again.');
            }
        }
    }); 
});

$(document).on('click', '.removeNote', function() {
    var noteID = $(this).attr('class').split(' ')[1];
    var content = '<h3 class="modalH">Remove Note</h3>'
                + '<p>Are you sure you wish to remove this note?</p>'
                + '<p class="buttonRow"><button class="buttons redButton confirmRemoveNote ' + noteID + '">Remove</button>'
                + '&nbsp;&nbsp;<button class="closeModal buttons blueButton">Cancel</button></p>';
    $(content).modal({minHeight:150});
});

$(document).on('click', '.confirmRemoveNote', function() {
    var noteID = $(this).attr('class').split(' ')[3];
    var type = 'removeNote';
    var dataString = 'type='+ type + '&noteID=' + noteID + '&token=' + token; 
    $.ajax({
        type: "POST",  
        dataType: 'json',
        url: "ajax/ajaxFunctions.php",  
        data: dataString,  
        success: function(response) {
            if (response.result == '1') {
                $('.noteContainer.' + noteID ).remove();
                $.modal.close();
            } else {
                alert('There was a communication problem, please try again.');
            }
        }
    }); 
});

$(document).on('click', '.closeModal', function() {
    $.modal.close();
});
