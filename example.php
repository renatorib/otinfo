<?php
include('otinfo.php'); // call the class baby

$server = new otinfo('underwar.org', 7171);

if($server->connect){
  echo "Players online: " . $server->players_online . " <br /> ";
  echo "Players max: " . $server->players_max. " <br /> ";
  echo "Players online record: " . $server->players_peak . " <br /> ";
  echo "Server location: " . $server->location . " <br /> ";
  echo "Client version: " . $server->client . " <br /> ";
} else {
  echo "Server offline";
}
