OTServer Info [![Build Status](https://travis-ci.org/gpedro/otinfo.svg)](https://travis-ci.org/gpedro/otinfo)
======
Catch information from servers' response by ip and port

Install
------
Just download this repo, and include otinfo.php
**Important:** Must have a writable `cache` folder in the same level of `otinfo.php` or will not work correctly
```php
include('otinfo.php');
```

Get started
------
**To instantiate an otserv, create a new Otinfo object**
```php
$server = new Otinfo('shadowcores.twifysoft.net');
```
The first parameter is the server `$ip`, and the second is the `$port` (defined 7171 as default)

**Get informations**
```php
if ($server->execute()) {
  echo "Players online: " . $server->players['online'] . "<br />";
  echo "Server location: " . $server->serverinfo['location'] . "<br />";
  echo "Client version: " . $server->serverinfo['client'] . "<br />";
  // these are just a few examples
} else {
  echo "Server offline";
  // if execute() returns false, the server are offline
}
```
The `execute()' method catch/parse the responses returned by server and return false if server are offline.

Possible responses
------
**Each server has its own response, and may be different from the others. This means that not all respond with the same information, and a server may have information that others do not have.**

Here are some possible answers nodes
* `players`
* `serverinfo`
* `motd`
* `owner`
* `monsters`
* `map`
* `npcs` (thanks to DSpeichert)

This means if you want to test, can `print_r`, `var_dump`, and whatelse, the nodes to know returned responses
```php
print_r($server->players);
print_r($server->serverinfo);
//etc
```

Cache
------
Otinfo cache itself not only for performance, but also to avoid empty responses, caused due to the protection of tfs.
As said before, you must have a writable `cache` folder in the same level of `otinfo.php` or will not work correctly.
The time of cache as default 180 seconds (three minutes). May you edit in `otinfo.php` changing this line
```php
private static $cache = 180; //seconds you want
```

Timeout
------
When the connection is bad, you can increase the timeout
```php
private static $timeout = 5; //seconds you want
```

**Made with :heart: by Renato Ribeiro and Ranieri Althoff**

Thanks to **yrpen**, **gpedro** and **DSpeichert** for their contributions
