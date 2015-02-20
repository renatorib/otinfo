<?php
require('otinfo.php');

$server = new Otinfo('underwar.org');

if ($server->execute()) {
    echo 'Players online: ', $server->players['online'], '<br />';
    echo 'Players max: ', $server->players['max'], '<br />';
    echo 'Players peak: ', $server->players['peak'], '<br />';
    echo 'Server location: ', $server->serverinfo['location'], '<br />';
    echo 'Client version: ', $server->serverinfo['client'] , '<br />';
} else {
    echo 'Server offline';
}
