<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PluginDnsinventoryCron
 *
 * @author Javier Samaniego García <jsamaniegog@gmail.com>
 */
class PluginDnsinventoryCron extends CommonDBTM {

    /**
     * Number of network names that will be scanned each time.
     */
    const NUMBER_NETWORKNAMES_SCANNED = 1000;
    
    static function getTypeName($nb = 0) {
        return __('DNS Inventory Cron', 'dnsinventory');
    }

    /**
     * Executed by cron. This task search the name for each IP address and save 
     * it in database.
     */
    static function cronDnsinventory(CronTask $task) {
        global $DB;
        
        require_once GLPI_ROOT . '/plugins/dnsinventory/lib/netdns2/Net/DNS2.php';
        require_once GLPI_ROOT . '/plugins/dnsinventory/lib/netdns2/Net/DNS2/Lookups.php';
        require_once GLPI_ROOT . '/plugins/dnsinventory/lib/netdns2/Net/DNS2/Resolver.php';
        require_once GLPI_ROOT . '/plugins/dnsinventory/lib/netdns2/Net/DNS2/RR.php';
        require_once GLPI_ROOT . '/plugins/dnsinventory/lib/netdns2/Net/DNS2/RR/A.php';
        require_once GLPI_ROOT . '/plugins/dnsinventory/lib/netdns2/Net/DNS2/RR/SOA.php';
        require_once GLPI_ROOT . '/plugins/dnsinventory/lib/netdns2/Net/DNS2/Packet.php';
        require_once GLPI_ROOT . '/plugins/dnsinventory/lib/netdns2/Net/DNS2/Packet/Request.php';
        require_once GLPI_ROOT . '/plugins/dnsinventory/lib/netdns2/Net/DNS2/Packet/Response.php';
        require_once GLPI_ROOT . '/plugins/dnsinventory/lib/netdns2/Net/DNS2/Header.php';
        require_once GLPI_ROOT . '/plugins/dnsinventory/lib/netdns2/Net/DNS2/Question.php';
        require_once GLPI_ROOT . '/plugins/dnsinventory/lib/netdns2/Net/DNS2/Exception.php';
        require_once GLPI_ROOT . '/plugins/dnsinventory/lib/netdns2/Net/DNS2/Socket.php';
        require_once GLPI_ROOT . '/plugins/dnsinventory/lib/netdns2/Net/DNS2/Socket/Sockets.php';
        require_once GLPI_ROOT . '/plugins/dnsinventory/lib/netdns2/Net/DNS2/RR/PTR.php';
        require_once GLPI_ROOT . '/plugins/dnsinventory/lib/netdns2/Net/DNS2/RR/CNAME.php';

        $entities_with_server = array();
        
        // get dns servers and its entities
        $server = new PluginDnsinventoryServer();
        foreach ($server->find() as $id => $server) {
            
            $entities_with_server[] = $server['entities_id'];
                
            if ($server['is_recursive'] == 1) {
                $sons = getSonsOf("glpi_entities", $server['entities_id']);
                foreach ($sons as $value) {
                    $entities_with_server[] = $value;
                }
            }
            
            $server_addreses[] = $server['address'];
        }
        
        if (empty($server_addreses)) {
            $task->log(__("No DNS server configured.", "dnsinventory"));
            return false;
        }
        
        $dns_resolver = new Net_DNS2_Resolver(array(
            'nameservers'   => $server_addreses,
            'ns_random'     => true
        ));
        
        // get current limit scanned
        $config = new PluginDnsinventoryConfig();
        $current_limit = $config->find("type = 'current_record_task_limit'");
        $current_limit = array_values($current_limit);
        $current_limit = $current_limit[0];
        
        // search for ip address (limit self::NUMBER_NETWORKNAMES_SCANNED)
        $sql = "select ip.name ipaddress, nn.id, nn.name from glpi_ipaddresses ip, glpi_networknames nn "
            //. "where (nn.name is null or nn.name = '') "
            . "where ip.items_id = nn.id "
            . "and ip.itemtype ='NetworkName' "
            . "and ip.version = 4 "
            . "and ip.entities_id in (".implode(",", $entities_with_server).") "
            . "LIMIT " . $current_limit['value'] . ", " . self::NUMBER_NETWORKNAMES_SCANNED;
        
        $current_limit['value'] += self::NUMBER_NETWORKNAMES_SCANNED;
        
        $reset_limit = true;
        
        foreach ($DB->request($sql) as $data) {
            $reset_limit = false;
            $task->addVolume(1);
            
            try {
                // query for hostname
                $respuesta = $dns_resolver->query($data['ipaddress'], 'PTR');
                
                $dnsname = $respuesta->answer[0]->ptrdname;
                list($hostname, $domain) = explode("." ,$dnsname, 2);
                
                //todo: query for alias
                //$respuesta = $dns_resolver->query($respuesta->answer[0]->name, "CNAME");
                //$aliasname = $respuesta->answer[0]->ptrdname;
                
            } catch (Exception $e) {
                $task->log(__("Resolving ", "dnsinventory") . $data['ipaddress'] . ": " . $e->getMessage());
                continue;
            }
            
            // update networkname in database
            $fqdns = new FQDN();
            $fqdns->getFromDBByQuery("WHERE fqdn = '" . $domain ."'");
            
            $networkname = new NetworkName();
            $resultado = $networkname->update(array(
                'id' => $data['id'],
                'name' => $hostname,
                'fqdns_id' => $fqdns->fields['id']
            ));
            
            if ($resultado === false) {
                $task->log(__("Error updating IP address ", "dnsinventory") . $data['ipaddress'] . " name to " . $hostname);
            }
        }
        
        // to reset the limit
        if ($reset_limit == true) {
            $config->update(array(
                "id"    => $current_limit['id'],
                "value" => 0
            ));
            
            $task->log(__("No more ip address, search reseted.", "dnsinventory"));
            
            return 0;
        } else {
            $config->update(array(
                "id"    => $current_limit['id'],
                "value" => $current_limit['value']
            ));
        }
        
        return 1;
    }

}
