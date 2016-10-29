<?php
/**
 *  This is the working script for most of the ajax requests.
**/
session_start();
if (isset($_SESSION['token']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['token']) {
    // Clean and convert post vars for html
    foreach ($_POST as $key => $value) {
        $key = trim(htmlentities($key, ENT_QUOTES, "UTF-8"));
        $value = trim(htmlentities($value, ENT_QUOTES, "UTF-8"));
        $post[$key] = $value;
    }
    switch ($post['type']) {
        
        case 'testDB':                 // Test Database Connection
            $host       = $post['hostName'];
            $database   = $post['database'];
            $dbUser     = $post['dbUser'];
            $dbPassword = $post['dbPassword'];
            $dbPre      = $post['dbPre'];
            try {
                $connection = new PDO("mysql:host=$host;dbname=$database", 
                                      $dbUser, 
                                      $dbPassword,
                                      array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
                $result = 'success';
            } catch(PDOException $ex) {
                $result = 'fail';
            }
            if ($result == 'fail') {
                $jsonData['error'] = 1;
                $jsonData['msg'] = "Cannot Connect with supplied credentials.";
            } else {
                $jsonData['error'] = 0;
                $jsonData['msg'] = 'Database Test Passed!';
            }
            echo json_encode($jsonData);
            break;

        case 'installLCM':            // Install Tables and config file
            $host       = $post['hostName'];
            $database   = $post['database'];
            $dbUser     = $post['dbUser'];
            $dbPassword = $post['dbPassword'];
            $dbPre      = $post['dbPre'];
            require_once "../classes/dbClass.php";
            $db = new DB($host, $database, $dbUser, $dbPassword);
            try {
                $connection = new PDO("mysql:host=$host;dbname=$database",
                                      $dbUser,
                                      $dbPassword,
                                      array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
                $result = 'success';
            } catch(PDOException $ex) {
                $result = 'fail';
            }
            if ($result == 'fail') {
                $jsonData['error'] = 1;
                $jsonData['msg'] = "Cannot Connect with supplied credentials.";
            } else {

                // contacts table
                $sql = "CREATE TABLE IF NOT EXISTS `{$dbPre}contacts` ("
                     . "`id` int(11) NOT NULL AUTO_INCREMENT,"
                     . "`firstName` varchar(255) NOT NULL,"
                     . "`lastName` varchar(255) NOT NULL,"
                     . "`Address` varchar(100) NOT NULL,"
                     . "`Phone` varchar(25) NOT NULL,"
                     . "`secondaryPhone` varchar(25) NOT NULL,"
                     . "`Fax` varchar(25) NOT NULL,"
                     . "`Email` varchar(50) NOT NULL,"
                     . "`leadType` int(11) NOT NULL,"
                     . "`leadSource` int(11) NOT NULL,"
                     . "`City` varchar(50) NOT NULL,"
                     . "`State` varchar(30) NOT NULL,"
                     . "`Country` varchar(255) NOT NULL,"
                     . "`Zip` varchar(10) NOT NULL,"
                     . "`dateAdded` datetime NOT NULL,"
                     . "`dateModified` datetime NOT NULL,"
                     . "`lastModifiedBy` int(11) NOT NULL,"
                     . "`assignedTo` int(11) NOT NULL COMMENT 'Who owns the lead',"
                     . "`lStatus` varchar(255) NOT NULL,"
                     . "`customField` varchar(255) NOT NULL,"
                     . "`customField2` varchar(255) NOT NULL,"
                     . "`customField3` varchar(255) NOT NULL,"
                     . "PRIMARY KEY (`id`)"
                     . ") ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
                $installContacts = $db->extQuery($sql);

                // alter and convert to utf-8 for other language support
                $sql = "ALTER TABLE {$dbPre}contacts CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci";
                $convert = $db->extQuery($sql);

                // Add Custom Fields to table if needed
                $sql = "ALTER TABLE {$dbPre}contacts Add `customField` varchar(255) NOT NULL after `lStatus`";
                $db->extQuery($sql);
                $sql = "ALTER TABLE {$dbPre}contacts Add `customField2` varchar(255) NOT NULL after `customField`";
                $db->extQuery($sql);
                $sql = "ALTER TABLE {$dbPre}contacts Add `customField3` varchar(255) NOT NULL after `customField2`";
                $db->extQuery($sql);
                $sql = "ALTER TABLE {$dbPre}contacts Add `assignedTo` int(11) NOT NULL after `lastModifiedBy`";
                $db->extQuery($sql);

                // lead Notes table
                $sql = "CREATE TABLE IF NOT EXISTS `{$dbPre}leadNotes` ("
                     . "`id` int(11) NOT NULL AUTO_INCREMENT,"
                     . "`leadID` int(11) NOT NULL,"
                     . "`Note` text NOT NULL,"
                     . "`creator` int(11) NOT NULL,"
                     . "`dateAdded` datetime NOT NULL,"
                     . "PRIMARY KEY (`id`)"
                     . ") ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
                $installNotes = $db->extQuery($sql);

                // alter and convert to utf-8 for other language support
                $sql = "ALTER TABLE {$dbPre}leadNotes CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci";
                $convert = $db->extQuery($sql);

                // lead Source table
                $sql = "CREATE TABLE IF NOT EXISTS `{$dbPre}leadSource` ("
                     . "`sourceID` int(11) NOT NULL AUTO_INCREMENT,"
                     . "`sourceName` varchar(255) NOT NULL,"
                     . "`description` text NOT NULL,"
                     . "PRIMARY KEY (`sourceID`)"
                     . ") ENGINE=MyISAM  DEFAULT CHARSET=utf8;";

                $installSource = $db->extQuery($sql);
                // alter and convert to utf-8 for other language support
                $sql = "ALTER TABLE {$dbPre}leadSource CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci";
                $convert = $db->extQuery($sql);

                $sql = "select * from {$dbPre}leadSource where sourceName='None'";
                $exists = $db->extQueryRowObj($sql);
                if (!$exists) {
                    $vals = array (
                        'sourceName' => 'None',
                        'description' => 'None Category for empty matches.'
                    );
                    $noneSource = $db->insert("{$dbPre}leadSource", $vals);
                }

                // lead Status table
                $sql = "CREATE TABLE IF NOT EXISTS `{$dbPre}leadStatus` ("
                     . "`id` int(11) NOT NULL AUTO_INCREMENT,"
                     . "`statusName` varchar(255) NOT NULL,"
                     . "`description` varchar(255) NOT NULL,"
                     . "PRIMARY KEY (`id`)"
                     . ") ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
                $installStatus = $db->extQuery($sql);

                // alter and convert to utf-8 for other language support
                $sql = "ALTER TABLE {$dbPre}leadStatus CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci";
                $convert = $db->extQuery($sql);

                $sql = "select * from {$dbPre}leadStatus where statusName='None'";
                $exists = $db->extQueryRowObj($sql);
                if (!$exists) {
                    $vals = array (
                        'statusName' => 'None',
                        'description' => 'Default Holder for empty matches.'
                    );
                    $noneStatus = $db->insert("{$dbPre}leadStatus", $vals);
                }

                // lead Type table
                $sql = "CREATE TABLE IF NOT EXISTS `{$dbPre}leadType` ("
                     . "`typeID` int(11) NOT NULL AUTO_INCREMENT,"
                     . "`typeName` varchar(255) NOT NULL,"
                     . "`description` text NOT NULL,"
                     . "PRIMARY KEY (`typeID`)"
                     . ") ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
                $installType = $db->extQuery($sql);

                // alter and convert to utf-8 for other language support
                $sql = "ALTER TABLE {$dbPre}leadType CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci";
                $convert = $db->extQuery($sql);

                $sql = "select * from {$dbPre}leadType where typeName='None'";
                $exists = $db->extQueryRowObj($sql);
                if (!$exists) {
                    $vals = array (
                        'typeName' => 'None',
                        'description' => 'None Type for empty matches.'
                    );
                    $noneType = $db->insert("{$dbPre}leadType", $vals);
                }

                // logging table
                $sql = "CREATE TABLE IF NOT EXISTS `{$dbPre}log` ("
                     . "`id` int(11) NOT NULL AUTO_INCREMENT,"
                     . "`userFirst` varchar(255) NOT NULL,"
                     . "`userLast` varchar(255) NOT NULL,"
                     . "`event` varchar(255) NOT NULL,"
                     . "`detail` varchar(255) NOT NULL,"
                     . "`eventTime` datetime NOT NULL,"
                     . "`ipAddr` varchar(255) NOT NULL,"
                     . "PRIMARY KEY (`id`)"
                     . ") ENGINE=MyISAM  DEFAULT CHARSET=utf8;";

                $installLog = $db->extQuery($sql);

                // alter and convert to utf-8 for other language support
                $sql = "ALTER TABLE {$dbPre}log CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci";
                $convert = $db->extQuery($sql);

                // Install other emails table
                $sql = "CREATE TABLE IF NOT EXISTS `{$dbPre}otherEmails` ("
                     . "`id` int(11) NOT NULL AUTO_INCREMENT,"
                     . "`email` varchar(255) NOT NULL,"
                     . "`contact` int(11) NOT NULL,"
                     . "`notes` text NOT NULL,"
                     . "PRIMARY KEY (`id`)"
                     . ") ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
                $installOtherEm = $db->extQuery($sql);

                // alter and convert to utf-8 for other language support
                $sql = "ALTER TABLE {$dbPre}otherEmails CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci";
                $convert = $db->extQuery($sql);

                // users table
                $sql = "CREATE TABLE IF NOT EXISTS `{$dbPre}users` ("
                     . "`id` int(11) NOT NULL AUTO_INCREMENT,"
                     . "`userName` varchar(255) NOT NULL,"
                     . "`secret` varchar(80) NOT NULL,"
                     . "`email` varchar(255) NOT NULL,"
                     . "`created` datetime NOT NULL,"
                     . "`first` varchar(255) NOT NULL,"
                     . "`last` varchar(255) NOT NULL,"
                     . "`isAdmin` tinyint(4) NOT NULL COMMENT '1 = admin',"
                     . "`ownLeadsOnly` int(11) NOT NULL COMMENT 'users, if 1 can only manage their own',"
                     . "PRIMARY KEY (`id`)"
                     . ") ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
                $installUsers = $db->extQuery($sql);

                // add ownLeadsOnly if needed
                $sql = "ALTER TABLE {$dbPre}users Add `ownLeadsOnly` int(11) NOT NULL after `isAdmin`";
                $db->extQuery($sql);

                // alter and convert to utf-8 for other language support
                $sql = "ALTER TABLE {$dbPre}users CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci";
                $convert = $db->extQuery($sql);
            
                // default 'admin' 'admin' user and password check if it already exists first
                $sql = "select * from {$dbPre}users where userName='admin'";
                $exists = $db->extQueryRowObj($sql);
                if (!$exists) {
                    $dateAdded = date("Y-m-d H:i:s");
                    $vals = array (
                        'userName' => 'admin',
                        'secret'   => 'd033e22ae348aeb5660fc2140aec35850c4da997263294b28c3e477023dba725d8eb4c9d23719c93',
                        'email'    => 'yourEmail@yoursite.com',
                        'created'  => "$dateAdded",
                        'first'    => 'admin',
                        'last'     => 'admin',
                        'isAdmin'  => '1'
                    );
                    $adminID = $db->insert("{$dbPre}users", $vals);
                }

                // Drop the old site settting if it exists
                $sql = "DROP TABLE IF EXISTS `{$dbPre}siteSettings`;";
                $dropSettings = $db->extQuery($sql);

                // Site Settings table
                $sql = "CREATE TABLE IF NOT EXISTS `{$dbPre}siteSettings` ("
                     . "`id` int(11) NOT NULL AUTO_INCREMENT,"
                     . "`pageResults` int(11) NOT NULL COMMENT 'Results Per Page',"
                     . "`customField1` varchar(255) NOT NULL COMMENT 'Custom Field 1',"
                     . "`customField2` varchar(255) NOT NULL,"
                     . "`customField3` varchar(255) NOT NULL,"
                     . "`Address` varchar(255) NOT NULL,"
                     . "`City` varchar(255) NOT NULL,"
                     . "`State` varchar(255) NOT NULL,"
                     . "`Country` varchar(255) NOT NULL,"
                     . "`Zip` varchar(255) NOT NULL,"
                     . "`Phone` varchar(255) NOT NULL,"
                     . "`secondaryPhone` varchar(255) NOT NULL,"
                     . "`Fax` varchar(255) NOT NULL,"
                     . "`assignedTo` varchar(255) NOT NULL COMMENT 'Who owns the lead',"
                     . "PRIMARY KEY (`id`)"
                     . ") ENGINE=MyISAM  DEFAULT CHARSET=utf8;";

                $installSettings = $db->extQuery($sql);

                // alter and convert to utf-8 for other language support
                $sql = "ALTER TABLE {$dbPre}siteSettings CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci";
                $convert = $db->extQuery($sql);

                $sql = "select * from {$dbPre}siteSettings";
                $exists = $db->extQueryRowObj($sql);
                if (!$exists) {
                    $vals = array (
                        'pageResults' => 20,
                        'customField1' => 'Extra1',
                        'customField2' => 'Extra2',
                        'customField3' => 'Extra3',
                        'Address' => 'Address',
                        'City' => 'City',
                        'State' => 'State',
                        'Country' => 'Country',
                        'Zip' => 'Zip',
                        'Phone' => 'Phone',
                        'secondaryPhone' => 'Phone 2',
                        'Fax' => 'Fax',
                        'assignedTo' => 'Owner'
                    );
                    $settingsID = $db->insert("{$dbPre}siteSettings", $vals);
                }
            
                // drop Sort table if exists
                $sql = "DROP TABLE IF EXISTS `{$dbPre}sortOrder`;";
                $dropSort = $db->extQuery($sql);

                // Sort Order table
                $sql = "CREATE TABLE IF NOT EXISTS `{$dbPre}sortOrder` ("
                     . "`id` int(11) NOT NULL AUTO_INCREMENT,"
                     . "`setName` varchar(255) NOT NULL,"
                     . "`columnName` varchar(255) NOT NULL,"
                     . "`orderSet` int(11) NOT NULL,"
                     . "`used` int(11) NOT NULL COMMENT 'Used or Not',"
                     . "PRIMARY KEY (`id`)"
                     . ") ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
                $installSort = $db->extQuery($sql);


                // alter and convert to utf-8 for other language support
                $sql = "ALTER TABLE {$dbPre}sortOrder CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci";
                $convert = $db->extQuery($sql);

                $sql = "select * from {$dbPre}sortOrder";
                $exists = $db->extQueryRowObj($sql);
                if (!$exists) {
                    $sql = "INSERT INTO `{$dbPre}sortOrder` (`id`, `setName`, `columnName`, `orderSet`, `used`) VALUES"
                         . "(1, 'Name', 'lastName', 1, 1),"
                         . "(2, 'Address', 'Address', 4, 1),"
                         . "(3, 'Phone', 'Phone', 2, 1),"
                         . "(4, 'Primary Email', 'Email', 3, 1),"
                         . "(5, 'Source', 'sourceName', 6, 1),"
                         . "(6, 'Type', 'typeName', 7, 1),"
                         . "(7, 'secondaryPhone', 'secondaryPhone', 9, 0),"
                         . "(8, 'Fax', 'Fax', 8, 0),"
                         . "(9, 'City', 'City', 12, 0),"
                         . "(10, 'State', 'State', 14, 0),"
                         . "(11, 'Country', 'Country', 13, 0),"
                         . "(12, 'Zip', 'Zip', 5, 1),"
                         . "(13, 'Date Added', 'dateAdded', 11, 0),"
                         . "(14, 'customField', 'customField', 10, 0),"
                         . "(15, 'customField2', 'customField2', 15, 0),"
                         . "(16, 'customField3', 'customField3', 16, 0),"
                         . "(17, 'Owner', 'assignedTo', 17, 0);";
                    $sortInstall = $db->extQuery($sql);
                }

                // Update '0' assignedTo Leads to admin user, since they need to be assigned to someone
                $where = array (
                    'userName' => 'admin'
                );
                $admin = $db->get_value("{$dbPre}users", 'id', $where);

                $sql = "select * from {$dbPre}contacts";
                $contactMv = $db->extQuery($sql);
                foreach ($contactMv as $row => $val) {
                    if ($val->assignedTo == '0') {
                        $sql = "update {$dbPre}contacts set assignedTo='$admin' where id='$val->id'";
                        $db->extQuery($sql);
                    }
                }
                // End update assignedTo


                $configFile = createConfig($host, $database, $dbUser, $dbPassword, $dbPre);
                @$perms = chmod(dirname(__FILE__) . "/../classes", 0777);         // Attempt to make writable
                $file = dirname(__FILE__) . "/../classes/variables.php";
                @$makeFile = file_put_contents($file, $configFile, LOCK_EX);
                $jsonData['file'] = $file;
                $jsonData['error'] = 0;
                $jsonData['msg'] = "Complete...If there was a problem writing the file, download and copy variables.php"
                                 . " in the classes directory with the following link...<br />\n\r";

                
            } 
            echo json_encode($jsonData);
            break;

        case 'returnConfig':                       // just output config as download file
            $host       = $post['hostName'];
            $database   = $post['database'];
            $dbUser     = $post['dbUser'];
            $dbPassword = $post['dbPassword'];
            $dbPre      = $post['dbPre'];

            $configFile = createConfig($host, $database, $dbUser, $dbPassword, $dbPre);

            header("Cache-Control: ");
            header("Content-type: text/plain");
            header('Content-Disposition: attachment; filename="variables.php"');
            echo $configFile;
            break;


        default:
            $jsonData = array(
                'result' => 0,
                'text' => 'Unknown Method'
            );
            echo json_encode($jsonData);
    }
} else {
    echo "Nice try.";
}

/* Function to create the configuration file variables.php */
function createConfig($host, $database, $dbUser, $dbPassword, $dbPre) {   
    $configFile = <<<EOT
<?php\r
/**\r
 * Variables / Config File for the leads and contacts management tool\r
 * Filename: variables.php \r
 * Make sure it is located in classes directory\r
 *                                          lb 04-25-2013\r
**/\r
\$DB = array();\r
\$DB["host"]   = '$host';\r
\$DB["user"]   = '$dbUser';\r
\$DB["pass"]   = '$dbPassword';\r
\$DB["dbName"] = '$database';\r
\r
\$dbPre = '$dbPre';   // table prefix in database\r
\r
// Other Constants\r
define('SALT', sha1('SomeRandomPhraseThatShouldntChange')); // Password login salt,\r
                                                            // changing this value will cause all of your passwords to be invalid.
EOT;
    return $configFile;
}
