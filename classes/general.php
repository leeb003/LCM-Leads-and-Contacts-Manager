<?php
/**
 *     Class for General purpose methods
**/

class Gen 
{
    // Properties

    // Methods
    public function __construct() {
    }

    public function nameField($field, $siteSettings) {       // Use the alternate name across site
        switch ($field) {
            case 'customField' :
            $name = $siteSettings->customField1;
            break;
            case 'customField2':
            $name = $siteSettings->customField2;
            break;
            case 'customField3':
            $name = $siteSettings->customField3;
            break;
            case 'Address':
            $name = $siteSettings->Address;
            break;
            case 'City':
            $name = $siteSettings->City;
            break;
            case 'State':
            $name = $siteSettings->State;
            break;
            case 'Country':
            $name = $siteSettings->Country;
            break;
            case 'Zip':
            $name = $siteSettings->Zip;
            break;
            case 'Phone':
            $name = $siteSettings->Phone;
            break;
            case 'secondaryPhone':
            $name = $siteSettings->secondaryPhone;
            break;
            case 'Fax':
            $name = $siteSettings->Fax;
            break;
            case 'assignedTo':
            $name = 'Owner';
            break;
            default:
            $name = $field;
        }
        return $name;
    }
}
