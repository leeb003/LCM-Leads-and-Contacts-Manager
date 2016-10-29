<?php
/**
 *  This is the working script for most of the ajax requests.
**/
session_start();
if (isset($_SESSION['token']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['token']) {
    require_once '../classes/dbClass.php';
    $db = new DB;
    require_once '../classes/logging.php';
    $log = new Log($dbPre);
    // Clean and convert post vars for html
    foreach ($_POST as $key => $value) {
        $key = trim(htmlentities($key, ENT_QUOTES, "UTF-8"));
        $value = trim(htmlentities($value, ENT_QUOTES, "UTF-8"));  // prior to 5.4 default is iso-8859-1
        $post[$key] = $value;
    }
    switch ($post['type']) {
        case 'login':                               // This is a standard login request
            $user = $post['user'];
            $password = $post['password'];
            $dbHash = $password.SALT;
            $where = array(
                'userName' => $user,
                'secret' => $dbHash
            );
            $userRow = $db->select($dbPre . 'users', '*', $where);
            if (isset($userRow[0]->userName)) {           // Successful login
                $jsonData['result'] = 1;
                $_SESSION['loggedIn'] = 'yes';
                $_SESSION['userID'] = $userRow[0]->id;
                $_SESSION['access'] = $userRow[0]->isAdmin;
                $_SESSION['ownLeadsOnly'] = $userRow[0]->ownLeadsOnly;
                $_SESSION['firstName'] = $userRow[0]->first;
                // logging
                $event = 'Logged In';
                $log->logEvent($_SESSION['userID'], $event);
            } else {
                $jsonData['hash'] = $dbHash;
                $jsonData['result'] = 0;
                $jsonData['text'] = 'Invalid Login.';
                unset($_SESSION['loggedIn']);
            }
            break;

        case 'editLead':
            $leadID = intval($post['leadID']);
            $sql = "select c.*, ls.sourceName, lt.typeName, lst.statusName, u.first, u.last"
                 . " from {$dbPre}contacts c, {$dbPre}leadSource ls, {$dbPre}leadType lt, {$dbPre}leadStatus lst,"
                 . " {$dbPre}users u"
                 . " where c.id='$leadID' and c.leadSource=ls.sourceID and c.leadType=lt.typeID and c.lStatus=lst.id"
                 . " and c.assignedTo = u.id";
            $lead = $db->extQueryRowObj($sql);
            $sql = "select * from {$dbPre}otherEmails where contact='$leadID'";
            $otherEmails = $db->extQuery($sql);
            $jsonData['result'] = 1;
            $jsonData['lead'] = $lead;
            $jsonData['otherEmails'] = $otherEmails;
            break;

        case 'saveLead':      // Save the lead
            $existing = $post['existing'];
            $invalid = false;
            if ($post['firstName'] == '' || $post['lastName'] == '') {
                $invalid = true;
                $jsonData['msg'] = 'You need at least a first and last name.';
            }

            if(get_magic_quotes_gpc()){                     // Magic Quotes fix
                $d = stripslashes($_POST['secondaryEmails']);
            }else{
                $d = $_POST['secondaryEmails'];
            }

            $secondaryEmails1 = array();
            $secondaryEmails = json_decode($d, true);
            foreach ($secondaryEmails as $key => $val) {
                $newEmail = trim($val['email']);
                $secondaryEmails1[$key]['email'] = $newEmail;
            }

            foreach ($secondaryEmails1 as $key => $val) {
                if ($val['email'] != '') {
                    if (!filter_var($val['email'], FILTER_VALIDATE_EMAIL)) {
                        $invalid = true;
                        $jsonData['msg'] = 'Invalid secondary email address.';
                    }
                }
            }
            if ($post['email'] != '') {
                if (!filter_var($post['email'], FILTER_VALIDATE_EMAIL)) {
                    $invalid = true;
                    $jsonData['msg'] = 'Invalid primary email address.';
                }
            }
            
            if ($invalid == true) {
                $jsonData['result'] = 0;

            } else {
                $dateAdded = date("Y-m-d H:i:s");

                if ($existing == 'existing') {          // Existing Lead update
                    $id = intval($post['id']);
                    $where = array(
                        'id'             => $id
                    );

                    $vals = array(
                        'firstName'      => $post['firstName'],
                        'lastName'       => $post['lastName'],
                        'Phone'          => $post['phone'],
                        'secondaryPhone' => $post['secondaryPhone'],
                        'Fax'            => $post['fax'],
                        'Email'          => $post['email'],
                        'Address'        => $post['address'],
                        'Zip'            => $post['zipCode'],
                        'City'           => $post['city'],
                        'State'          => $post['state'],
                        'Country'        => $post['country'],
                        'leadSource'     => $post['leadSource'],
                        'leadType'       => $post['leadType'],
                        'lStatus'        => $post['leadStatus'],
                        'dateModified'   => "$dateAdded",
                        'lastModifiedBy' => $_SESSION['userID'],
                        'customField'    => $post['customField'],
                        'customField2'   => $post['customField2'],
                        'customField3'   => $post['customField3'],
                        'assignedTo'     => $post['leadOwner']

                    );
                    $db->update("{$dbPre}contacts", $vals, $where);
                    $insertID = $id;
                    // delete existing other emails for this lead and reinsert new
                    $where = array(
                        'contact' => $id
                    );
                    $db->delete("{$dbPre}otherEmails", $where);
                    // logging
                    $event = 'Updated an Existing Contact';
                    $detail = "Contact {$post['firstName']} {$post['lastName']}";
                    $log->logEvent($_SESSION['userID'], $event, $detail);
                    

                } else {                                // New Lead
                    $vals = array(
                        'firstName'      => $post['firstName'],
                        'lastName'       => $post['lastName'],
                        'Phone'          => $post['phone'],
                        'secondaryPhone' => $post['secondaryPhone'],
                        'Fax'            => $post['fax'],
                        'Email'          => $post['email'],
                        'Address'        => $post['address'],
                        'Zip'            => $post['zipCode'],
                        'City'           => $post['city'],
                        'State'          => $post['state'],
                        'Country'        => $post['country'],
                        'leadSource'     => $post['leadSource'],
                        'leadType'       => $post['leadType'],
                        'lStatus'        => $post['leadStatus'],
                        'dateAdded'      => "$dateAdded",
                        'dateModified'   => "$dateAdded",
                        'lastModifiedBy' => $_SESSION['userID'],
                        'assignedTo'     => $_SESSION['userID'],
                        'customField'    => $post['customField'],
                        'customField2'   => $post['customField2'],
                        'customField3'   => $post['customField3'],
                        'assignedTo'     => $post['leadOwner']
                    );
                    $insertID = $db->insert("{$dbPre}contacts", $vals);
                    // logging
                    $event = 'Added a New Contact';
                    $detail = "Contact {$post['firstName']} {$post['lastName']}";
                    $log->logEvent($_SESSION['userID'], $event, $detail);
                }

                foreach ($secondaryEmails1 as $key => $val) {    // Insert emails for new and existing
                    if ($val['email'] != '') {
                        $vals = array(
                            'email' => $val['email'],
                            'contact' => $insertID
                        );
                    $secInsert = $db->insert("{$dbPre}otherEmails", $vals);
                    }
                }

                $where = array (
                    'typeID' => $post['leadType']
                );
                $typeName = $db->get_value("{$dbPre}leadType",'typeName',$where);
            
                $where = array (
                    'sourceID' => $post['leadSource']
                );
                $sourceName = $db->get_value("{$dbPre}leadSource",'sourceName',$where);

                $where = array (
                    'id' => $post['leadOwner']
                );
                $ownerName = $db->get_value("{$dbPre}users","CONCAT(first, ' ', last)", $where); 

                $sql = "select * from {$dbPre}sortOrder where used='1' order by orderSet asc";
                $sortOrder = $db->extQuery($sql);
                $sql = "select * from {$dbPre}contacts where id='$insertID'";
				$entry = $db->extQueryRowObj($sql);
				// convert entities for display
				$displayEntries = array();
				foreach ($entry as $k => $v) {
					$newV = html_entity_decode($v);
					$displayEntries[$k] = $newV;
				}

                $jsonData['typeName'] = $typeName;
                $jsonData['sourceName'] = $sourceName;
                $jsonData['ownerName'] = $ownerName;
                $jsonData['secondaryEmails'] = $secondaryEmails1;
                $jsonData['result'] = 1;
                $jsonData['insertID'] = $insertID;
                $jsonData['vals'] = $displayEntries;
                $jsonData['sortOrder'] = $sortOrder;
            }
            break;

        case 'saveNote':                        // Save a note for a lead
            $leadID = intval($post['leadID']);
            $note = $_POST['note'];
            $creator = $_SESSION['userID'];
            $date = date("Y-m-d H:i:s");
            $noteDate = date('m-d-Y H:i:s');

            mb_detect_encoding($note, "UTF-8") == "UTF-8" ? $note : $note = utf8_encode($note);

            $vals = array(
                'leadID'    => $leadID,
                'Note'      => $note,
                'creator'   => $creator,
                'dateAdded' => "$date"
            );
			$noteID = $db->insert("{$dbPre}leadNotes", $vals);

            $where = array(
                'id' => $noteID
            );
            $note = $db->get_value("{$dbPre}leadNotes", 'Note', $where);  // Send back what's in the db for the note

            $sql = "select userName from {$dbPre}users where id='$creator'";
            $creatorName = $db->extQueryRowObj($sql);

            $sql = "select * from {$dbPre}contacts where id=$leadID";
            $contact = $db->extQueryRowObj($sql);

            $jsonData['userName'] = $creatorName->userName;
            $jsonData['noteDate'] = $noteDate;
            $jsonData['result'] = 1;
            $jsonData['noteID'] = $noteID;
            $jsonData['note'] = html_entity_decode($note);
            // logging
            $event = 'Saved a Note';
            $detail = "Contact $contact->firstName $contact->lastName";
            $log->logEvent($_SESSION['userID'], $event, $detail);
            break;

        case 'saveEditNote':                    // Save an Edited Note
            $note = $_POST['note'];
            $noteID = $post['noteID'];
            mb_detect_encoding($note, "UTF-8") == "UTF-8" ? $note : $note = utf8_encode($note);

            $vals = array(
                'Note' => $note
            );
            $where = array(
                'id' => $noteID
            );
            $update = $db->update("{$dbPre}leadNotes", $vals, $where);

            $sql = "select * from {$dbPre}leadNotes where id='$noteID'";
            $noteRow = $db->extQueryRowObj($sql);
            $note = $noteRow->Note;

            $sql = "select userName from {$dbPre}users where id='$noteRow->creator'";
            $creatorName = $db->extQueryRowObj($sql);

            $sql = "select * from {$dbPre}contacts where id=$noteRow->leadID";
            $contact = $db->extQueryRowObj($sql);
            $noteDate = strtotime($noteRow->dateAdded);
            $noteDate = date('m-d-Y H:i:s', $noteDate);

            $jsonData['userName'] = $creatorName->userName;
            $jsonData['noteDate'] = $noteDate;
            $jsonData['result'] = 1;
            $jsonData['noteID'] = $noteID;
            $jsonData['note'] = html_entity_decode($note);

            // logging
            $event = 'Edited an existing Note';
            $detail = "Contact $contact->firstName $contact->lastName (noteID: $noteID)";
            $log->logEvent($_SESSION['userID'], $event, $detail);
            break;

        case 'editNote':                        // Edit a Note
            $noteID = intval($post['noteID']);
            $sql = "select * from {$dbPre}leadNotes where id=$noteID";
            $note = $db->extQueryRowObj($sql);
            $jsonData['note'] = $note;
            $jsonData['noteID'] = $noteID;
            $jsonData['result'] = 1;
            break;

        case 'removeNote':                      // Delete a note
            $noteID = intval($post['noteID']);
            $sql = "select n.leadID, c.* from {$dbPre}leadNotes n, {$dbPre}contacts c"
                 . " where n.id='$noteID' and n.leadID=c.id";
            $contact = $db->extQueryRowObj($sql);
            $where = array(
                'id' => $noteID
            );
            $db->delete("{$dbPre}leadNotes", $where);
            $jsonData['result'] = 1;
            // logging
            $event = 'Deleted a Note';
            $detail = "Contact $contact->firstName $contact->lastName";
            $log->logEvent($_SESSION['userID'], $event, $detail);
            break;

        case 'deleteLead':
            $leadID = intval($post['leadID']);
            $sql = "select * from {$dbPre}contacts where id='$leadID'";
            $contact = $db->extQueryRowObj($sql);
            $where = array(
                'id' => $leadID
                );
            $db->delete("{$dbPre}contacts", $where);
            $where = array(
                'leadID' => $leadID
                );
            $db->delete("{$dbPre}leadNotes", $where);
            $where = array(
                'contact' => $leadID
                );
            $db->delete("{$dbPre}otherEmails", $where);
            $jsonData['result'] = 1;
            // logging
            $event = 'Deleted a Contact';
            $detail = "Contact $contact->firstName $contact->lastName";
            $log->logEvent($_SESSION['userID'], $event, $detail);
            break;

        case 'saveNewSource':               // Save a new lead source
            $name = $post['name'];
            $desc = $post['desc'];
            $vals = array (
                'sourceName' => $name,
                'description' => $desc
            );
            $insertID = $db->insert("{$dbPre}leadSource", $vals);
            $jsonData['result'] = 1;
            $jsonData['insertID'] = $insertID;
            $jsonData['vals'] = $vals;
            // logging
            $event = 'Saved a New Source';
            $detail = "Source Name $name";
            $log->logEvent($_SESSION['userID'], $event, $detail);
            break;

        case 'saveSource':                  // Update a lead source
            $id = intval($post['id']);
            $name = $post['name'];
            $desc = $post['desc'];
            $where = array (
                'sourceID' => $id
            );
            $vals = array (
                'sourceName' => $name,
                'description' => $desc
            );
            $db->update("{$dbPre}leadSource", $vals, $where);
            $jsonData['result'] = 1;
            // logging
            $event = 'Updated a Source';
            $detail = "Source Name $name";
            $log->logEvent($_SESSION['userID'], $event, $detail);
            break;

        case 'deleteSource':                // Delete a source
            $id = intval($post['id']);
            $sql = "select * from {$dbPre}leadSource where sourceID='$id'";
            $source = $db->extQueryRowObj($sql);
            // first find contacts that belong to this source and move to None
            $where = array (
                'sourceName' => 'None'
            );
            $noneID = $db->get_value("{$dbPre}leadSource",'sourceID',$where);

            $vals = array(
                'leadSource' => $noneID
            );
            $where = array(
                'leadSource' => $id
            );
            $db->update("{$dbPre}contacts", $vals, $where);

            $where = array(
                'sourceID' => $id
            );
            $db->delete("{$dbPre}leadSource", $where);
            $jsonData['result'] = 1;
            // logging
            $event = 'Deleted a Source';
            $detail = "Source Name $source->sourceName";
            $log->logEvent($_SESSION['userID'], $event, $detail);
            break;

       case 'saveNewType':               // Save a new lead type
            $name = $post['name'];
            $desc = $post['desc'];
            $vals = array (
                'typeName' => $name,
                'description' => $desc
            );
            $insertID = $db->insert("{$dbPre}leadType", $vals);
            $jsonData['result'] = 1;
            $jsonData['insertID'] = $insertID;
            $jsonData['vals'] = $vals;
            // logging
            $event = 'Saved a New Type';
            $detail = "Type Name $name";
            $log->logEvent($_SESSION['userID'], $event, $detail);
            break;

        case 'saveType':                  // Update a lead type
            $id = intval($post['id']);
            $name = $post['name'];
            $desc = $post['desc'];
            $where = array (
                'typeID' => $id
            );
            $vals = array (
                'typeName' => $name,
                'description' => $desc
            );
            $db->update("{$dbPre}leadType", $vals, $where);
            $jsonData['result'] = 1;
            // logging
            $event = 'Updated a Type';
            $detail = "Type Name $name";
            $log->logEvent($_SESSION['userID'], $event, $detail);
            break;

        case 'deleteType':                // Delete a type
            $id = intval($post['id']);
            $sql = "select * from {$dbPre}leadType where typeID='$id'";
            $leadType = $db->extQueryRowObj($sql);
            // first find contacts that belong to this source and move to None
            $where = array (
                'typeName' => 'None'
            );
            $noneID = $db->get_value("{$dbPre}leadType",'typeID',$where);

            $vals = array(
                'leadType' => $noneID
            );
            $where = array(
                'leadType' => $id
            );
            $db->update("{$dbPre}contacts", $vals, $where);

            $where = array(
                'typeID' => $id
            );
            $db->delete("{$dbPre}leadType", $where);
            $jsonData['result'] = 1;
            // logging
            $event = 'Deleted a Type';
            $detail = "Type Name $leadType->typeName";
            $log->logEvent($_SESSION['userID'], $event, $detail);
            break;

       case 'saveNewStatus':               // Save a new lead status
            $name = $post['name'];
            $desc = $post['desc'];
            $vals = array (
                'statusName' => $name,
                'description' => $desc
            );
            $insertID = $db->insert("{$dbPre}leadStatus", $vals);
            $jsonData['result'] = 1;
            $jsonData['insertID'] = $insertID;
            $jsonData['vals'] = $vals;
            // logging
            $event = 'Saved a New Status';
            $detail = "Status Name $name";
            $log->logEvent($_SESSION['userID'], $event, $detail);
            break;

        case 'saveStatus':                  // Update a lead status
            $id = intval($post['id']);
            $name = $post['name'];
            $desc = $post['desc'];
            $where = array (
                'id' => $id
            );
            $vals = array (
                'statusName' => $name,
                'description' => $desc
            );
            $db->update("{$dbPre}leadStatus", $vals, $where);
            $jsonData['result'] = 1;
            // logging
            $event = 'Updated a Status';
            $detail = "Status Name $name";
            $log->logEvent($_SESSION['userID'], $event, $detail);
            break;

        case 'deleteStatus':                // Delete a status
            $id = intval($post['id']);
            $sql = "select * from {$dbPre}leadStatus where id='$id'";
            $statusRow = $db->extQueryRowObj($sql);
            // first find contacts that belong to this source and move to None
            $where = array (
                'statusName' => 'None'
            );
            $noneID = $db->get_value("{$dbPre}leadStatus",'id',$where);

            $vals = array(
                'lStatus' => $noneID
            );
            $where = array(
                'lStatus' => $id
            );
            $db->update("{$dbPre}contacts", $vals, $where);

            $where = array(
                'id' => $id
            );
            $db->delete("{$dbPre}leadStatus", $where);
            $jsonData['result'] = 1;
            // logging
            $event = 'Deleted a Status';
            $detail = "Status Name $statusRow->statusName";
            $log->logEvent($_SESSION['userID'], $event, $detail);
            break;

        case 'saveUser':                   // Save a new user
            $isAdmin   = $post['isAdmin'];
            $userName  = $post['userName'];
            $password  = $post['password'];
            $dbHash = $password.SALT;      // add salt

            $firstName = $post['firstName'];
            $lastName  = $post['lastName'];
            $userEmail = $post['userEmail'];
            $ownLeads  = $post['ownLeads'];
            $jsonData['result'] = 1;

            $sql = "select * from {$dbPre}users where userName='$userName'";  // Check for username existance
            $exists = $db->extQueryRowObj($sql);
            if ($exists) {
                $jsonData['result'] = 2;
                $jsonData['msg'] = 'This Username is already in use.';

            } elseif (!filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
                $jsonData['result'] = 2;
                $jsonData['msg'] = 'Invalid email address.';
            } else {                                                         // Insert user
                $dateAdded = date("Y-m-d H:i:s");
                $viewDate = date("m-d-Y H:i:s");

                $vals = array (
                    'userName'     => $userName,
                    'secret'       => $dbHash,
                    'email'        => $userEmail,
                    'created'      => "$dateAdded",
                    'first'        => $firstName,
                    'last'         => $lastName,
                    'isAdmin'      => $isAdmin,
                    'ownLeadsOnly' => $ownLeads
                );
                $insertID = $db->insert("{$dbPre}users", $vals);
                $jsonData['result'] = 1;
                $jsonData['viewDate'] = $viewDate;
                $jsonData['insertID'] = $insertID;
                // logging
                $event = 'Created User Account';
                $detail = "User Name $userName";
                $log->logEvent($_SESSION['userID'], $event, $detail);
            }
            break;

        case 'deleteUser':                  // Delete a user account
            $id = intval($post['id']);
            $sql = "select * from {$dbPre}users where id='$id'";
            $userRow = $db->extQueryRowObj($sql);
            $where = array (
                'userName' => 'admin'
            );
            $admin = $db->get_value("{$dbPre}users", "id", $where);
            if ($id == $admin) {                     // can't delete the admin account
                $jsonData['result'] = 2;
                $jsonData['msg'] = "You cannot delete the default admin account.";

            } elseif ($id == $_SESSION['userID']) {  // can't delete your logged in account
                $jsonData['result'] = 2;
                $jsonData['msg'] = "You cannot delete your own account.";

            } else {
                $where = array (
                    'id' => $id
                );
                $db->delete("{$dbPre}users", $where);

                // Update deleted user assignedTo Leads to admin user, since they need to be assigned to someone
                $where = array (
                    'userName' => 'admin'
                );
                $admin = $db->get_value("{$dbPre}users", 'id', $where);

                $sql = "select * from {$dbPre}contacts";
                $contactMv = $db->extQuery($sql);
                foreach ($contactMv as $row => $val) {
                    if ($val->assignedTo == $id) {
                        $sql = "update {$dbPre}contacts set assignedTo='$admin' where id='$val->id'";
                        $db->extQuery($sql);
                    }
                }
                // End update assignedTo

                $jsonData['result'] = 1;
                // logging
                $event = 'Deleted User Account';
                $detail = "User Name $userRow->userName";
                $log->logEvent($_SESSION['userID'], $event, $detail);
            }
            break;

        case 'modifyUser':                // Modify User account
            $id         = intval($post['id']);
            $email      = $post['email'];
            $isAdmin    = intval($post['isAdmin']);
            $updatePass = $post['updatePass'];
            $password   = $post['password'];
            $ownLeads   = $post['ownLeads'];
            $dbHash     = $password.SALT;      // add salt
            $sql = "select * from {$dbPre}users where id='{$_SESSION['userID']}' limit 1";
            $currUser = $db->extQueryRowObj($sql);
            $sql = "select * from {$dbPre}users where id='$id'";
            $userRow = $db->extQueryRowObj($sql);

            if ($currUser->isAdmin != 1) {   // not admin
                $jsonData['result'] = 2;
                $jsonData['msg'] = 'Insufficient rights to update accounts.';

            } elseif ($id == $_SESSION['userID'] && $currUser->isAdmin != $isAdmin) {  // Can't change own permission
                $jsonData['result'] = 2;
                $jsonData['msg'] = 'You cannot change your own permissions.';

            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $jsonData['result'] = 2;
                $jsonData['msg'] = 'Invalid email address.';

            } else {                      // Modify the account
                if ($updatePass == 'true') {
                    $vals = array (
                        'secret'       => $dbHash,
                        'email'        => $email,
                        'isAdmin'      => $isAdmin,
                        'ownLeadsOnly' => $ownLeads
                    );
                } else {
                    $vals = array (
                        'email'        => $email,
                        'isAdmin'      => $isAdmin,
                        'ownLeadsOnly' => $ownLeads
                    );
                }

                $where = array (
                    'id' => $id
                );
                $db->update("{$dbPre}users",$vals,$where);
                $jsonData['result'] = 1;
                $jsonData['id'] = $id;
                // logging
                $event = 'Modified User Account';
                $detail = "User Name $userRow->userName";
                $log->logEvent($_SESSION['userID'], $event, $detail);
            }
            break;

        case 'emptyDB':                  // Remove all leads from database
            $where = array();
            $db->delete("{$dbPre}contacts", $where);
            $db->delete("{$dbPre}leadNotes", $where);
            $db->delete("{$dbPre}otherEmails", $where);
            $jsonData['result'] = 1;            
            // logging
            $event = 'Emptied Leads and Contacts Database';
            $log->logEvent($_SESSION['userID'], $event);
            break;
        
        case 'showLog':                 // Show event log
            if (isset($post['search']) && $post['search'] != '') {
                $search = true;
                $searchTerm = $post['search'];
            } else {
                $search = false;
                $searchTerm = '';
            }
            // Pagination and sort queries
            $page = intval($post['page']);
            if ($search == true) {
                $sql = "select count(*) as 'count' from {$dbPre}log where userFirst LIKE '%$searchTerm%'"
                     . " OR userLast LIKE '%$searchTerm%' OR event LIKE '%$searchTerm%'"
                     . " OR detail LIKE '%$searchTerm%' OR eventTime LIKE '%$searchTerm%' OR ipAddr LIKE '%$searchTerm%'";
            } else {
                $sql = "select count(*) as 'count' from {$dbPre}log";
            }
            $totalC = $db->extQueryRowObj($sql);
            $logEntries = $totalC->count;
            // Get the results per page from siteSettings
            $sql = "select * from {$dbPre}siteSettings";
            $siteSettings = $db->extQueryRowObj($sql);
            $rpp = $siteSettings->pageResults;

            $adjacents = 3;
            $reload = "#";
            $tpages = ($logEntries) ? ceil($logEntries/$rpp) : 1;
            $startLimit = ($page -1) * $rpp;
            require_once '../classes/paging.php';
            $paging = new paging($reload, $page, $tpages, $adjacents);
            $pagDiv = $paging->getDiv();
            // End pagination work
            if ($search == true) {
                $sql = "select * from {$dbPre}log where userFirst LIKE '%$searchTerm%'"
                     . " OR userLast LIKE '%$searchTerm%' OR event LIKE '%$searchTerm%'"
                     . " OR detail LIKE '%$searchTerm%' OR eventTime LIKE '%$searchTerm%' OR ipAddr LIKE '%$searchTerm%'"
                     . " order by eventTime desc LIMIT $startLimit, $rpp";
            } else {
                $sql = "select * from {$dbPre}log order by eventTime desc LIMIT $startLimit, $rpp";
            }
            $log = $db->extQuery($sql);
            $jsonData['result'] = 1;
            $jsonData['pagDiv'] = $pagDiv;
            $jsonData['entries'] = $logEntries;
            $jsonData['search'] = $search;
            $jsonData['searchTerm'] = $searchTerm;
            $jsonData['log'] = $log;
            break;

        case 'updateRPP':                // Update Results per page
            $rpp = intval($post['rpp']);
            $vals = array (
                'pageResults' => $rpp
            );
            $where = array ( 
                'id' => 1
            );
            $db->update("{$dbPre}siteSettings", $vals, $where);
            $jsonData['result'] = 1;
            $jsonData['rpp'] = $rpp;
            // logging
            $event = 'Updated Site Settings - Results Per Page';
            $detail = "$rpp";
            $log->logEvent($_SESSION['userID'], $event, $detail);
            break;

        case 'updateCustomField':       // Update the custom field name
            $customField = $post['customField'];
            $vals = array (
                'customField1' => $customField
            );
            $where = array (
                'id' => 1
            );
            $db->update("{$dbPre}siteSettings", $vals, $where);
            $jsonData['result'] = 1;
            // logging
            $event = 'Updated Site Settings - Custom Field';
            $detail = "New Name $customField";
            $log->logEvent($_SESSION['userID'], $event, $detail);
            break;

        case 'updateField':             // Update Field Names
            $field = $post['fieldName'];
            $val = $post['fieldVal'];

            $vals = array (
                $field => $val
            );
            $where = array (
                'id' => 1
            );
            $db->update("{$dbPre}siteSettings", $vals, $where);
            $jsonData['result'] = 1;
            // logging
            $event = 'Updated Site Settings - Field Name';
            $detail = "New Name For $field set to $val";
            $log->logEvent($_SESSION['userID'], $event, $detail);
            break;

        case 'saveOrder':               // Save the Column Order
            if(get_magic_quotes_gpc()){              // Magic quotes messes with the object
                $d = stripslashes($_POST['order']);
            }else{
                $d = $_POST['order'];
            }
            $order = json_decode($d);
            foreach ($order as $row => $field) {
                $vals = array(
                    'orderSet' => intval($field->order),
                    'used' => intval($field->used)
                );
                $where = array(
                    'id' => intval($field->id)
                );
                $query = $db->update("{$dbPre}sortOrder", $vals, $where);
            }
            $jsonData['result'] = 1;
            $jsonData['vals'] = $vals;
            $jsonData['query'] = $query;
            // logging
            $event = 'Updated Site Settings - Sort Order';
            $log->logEvent($_SESSION['userID'], $event);
            break;

        default:
            $jsonData = array(
                'result' => 0,
                'text' => 'Unknown Method'
            );
    }
    echo json_encode($jsonData);
} else {
    echo "Nice try.";
}

