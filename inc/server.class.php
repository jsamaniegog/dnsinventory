<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of server
 *
 * @author Javier Samaniego García <jsamaniegog@gmail.com>
 */
class PluginDnsinventoryServer extends CommonDBTM {

    static $rightname = 'config';
    public $dohistory = false;

    //public $fields = array("address");

    static function getTypeName($nb = 0) {
        return _n('DNS Server', 'DNS Servers', $nb, 'dnsinventory');
    }

    /**
     * @return array
     */
    function getSearchOptions() {

        $tab = array();

        $tab['common'] = _n('DNS Server', 'DNS Servers', $nb, 'dnsinventory');

        $tab[1]['table'] = $this->getTable();
        $tab[1]['field'] = 'name';
        $tab[1]['name'] = __('DNS Server name');
        $tab[1]['datatype'] = 'itemlink';
        $tab[1]['itemlink_type'] = $this->getType();

        $tab[2]['table'] = $this->getTable();
        $tab[2]['field'] = 'address';
        $tab[2]['name'] = __('DNS Server Address');

        return $tab;
    }

    /**
     * Show DNS server form to add or edit.
     * @global type $DB
     * @param type $options
     * @return boolean
     */
    function showForm($options) {
        global $DB;

        if (!Session::haveRight("config", UPDATE)) {
            return false;
        }

        // get server data
        $this->getFromDB($options['id']);

        $this->showFormHeader($options);

        // HTML
        // fields
        echo "<tr><td colspan='2'>";

        // hidden id
        echo Html::hidden("id", array('value' => $this->fields['id']));

        echo __('Name') . "</td><td colspan='2'>";
        echo Html::input("name", array('value' => $this->fields['name']));

        echo "</td></tr><tr><td colspan='2'>";

        echo __('Address', 'dhcpinventory') . "</td><td colspan='2'>";
        echo Html::input("address", array('value' => $this->fields['address']));

        echo "</td></tr>";


        $this->showFormButtons($options);

        return true;
    }
}
