<?php

$m = new Memcached();
$m->addServer('127.0.0.1', 11211);
$m->flush();

$s = $m->getStats();
echo "<pre>";
foreach ($s as $server => $data) {
    echo "server > $server:\n";
    foreach (array('curr_items','total_items','bytes') as $x)
        echo "\t$x: {$data[$x]}\n";
}
echo "</pre>";
