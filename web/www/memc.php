<?php
echo "Hi";
$m = new Memcached();
$m->addServer('localhost', 80);
$m->flush();