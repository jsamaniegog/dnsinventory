/* 
 * Copyright (C) 2017 Javier Samaniego García <jsamaniegog@gmail.com>
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
CREATE TABLE `glpi_plugin_dnsinventory_configs` (
        `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
        `type` varchar(32) NOT NULL default '' UNIQUE,
        `value` varchar(32) NOT NULL default ''
)ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
INSERT INTO `glpi_plugin_dnsinventory_configs`(type, value) VALUES ('current_record_task_limit', '0');

CREATE TABLE `glpi_plugin_dnsinventory_servers` (
        `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
        `name` varchar(32) NOT NULL default '' UNIQUE,
        `entities_id` int(11) NOT NULL default 0, 
        `is_recursive` tinyint(1) NOT NULL default 0,
        `address` varchar(32) NOT NULL default ''
)ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

