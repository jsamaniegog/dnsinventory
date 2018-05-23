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
 * Init the hooks of the plugins -Needed
 * @global array $PLUGIN_HOOKS
 * @glpbal array $CFG_GLPI
 */
function plugin_init_dnsinventory() {
    global $PLUGIN_HOOKS, $CFG_GLPI;

    Plugin::registerClass('PluginDnsinventoryConfig', array('addtabon' => 'Config'));
    Plugin::registerClass('PluginDnsinventoryServer');
    Plugin::registerClass('PluginDnsinventoryDns');

    // Config page (muestra el acceso en el menu superior, en la parte de configuración)
    if (Session::haveRight('config', UPDATE)) {
        $PLUGIN_HOOKS['config_page']['dnsinventory'] = 'front/config.php';
        // añade menú. ver uso de CommonGLPI::getMenuContent()
        $PLUGIN_HOOKS['menu_toadd']['dnsinventory'] = array(
            'config' => 'PluginDnsinventoryConfig',
            'config' => 'PluginDnsinventoryServer'
        );
    }

    $PLUGIN_HOOKS['csrf_compliant']['dnsinventory'] = true;
}

/**
 * Fonction de définition de la version du plugin
 * @return type
 */
function plugin_version_dnsinventory() {
    return array('name' => __('DNS Inventory', 'dnsinventory'),
        'version' => '1.1.0',
        'author' => 'Javier Samaniego',
        'license' => 'AGPLv3+',
        'homepage' => 'https://github.com/jsamaniegog/dnsinventory',
        'minGlpiVersion' => '0.90');
}

/**
 * Fonction de vérification des prérequis
 * @return boolean
 */
function plugin_dnsinventory_check_prerequisites() {
    if (version_compare(GLPI_VERSION, '0.90', 'lt')) {
        echo __('This plugin requires GLPI >= 0.90', 'dnsinventory');
        return false;
    }

    return true;
}

/**
 * Fonction de vérification de la configuration initiale
 * Uninstall process for plugin : need to return true if succeeded
 * may display messages or add to message after redirect.
 * @param type $verbose
 * @return boolean
 */
function plugin_dnsinventory_check_config($verbose = false) {
    // check here
    if (true) {
        return true;
    }

    if ($verbose) {
        echo __('Installed / not configured', 'dnsinventory');
    }

    return false;
}

?>