<?php

/*
 * Copyright (C) 2016 Javier Samaniego García <jsamaniegog@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Hook called on profile change
 * Good place to evaluate the user right on this plugin
 * And to save it in the session
 */
function plugin_change_profile_dnsinventory() {
    
}

/**
 * Fonction d'installation du plugin
 * @return boolean
 */
function plugin_dnsinventory_install() {
    global $DB;

    // todo: añadir TSIG or SIG como método de autenticación con el servidor
    if (!TableExists("glpi_plugin_dnsinventory_servers")) {
        $DB->runFile(GLPI_ROOT . "/plugins/dnsinventory/sql/1.0.0.sql");
    }

    // register a cron for task execution
    $res= CronTask::Register(
        "PluginDnsinventoryCron", 
        "dnsinventory", 
        3600, 
        array(
            'comment' => __('Query to DNS servers for network names.', 'dnsinventory'),
            'mode' => CronTask::MODE_EXTERNAL,
            'logs_lifetime' => '7' // days
        )
    );

    if (!$res) {
        $res = $res;
    }
    
    return true;
}

/**
 * Fonction de désinstallation du plugin
 * @return boolean
 */
function plugin_dnsinventory_uninstall() {
    global $DB;

    $DB->runFile(GLPI_ROOT . "/plugins/dnsinventory/sql/uninstall-1.0.0.sql");

    return true;
}

?>