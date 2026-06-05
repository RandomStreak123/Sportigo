<?php
$m = new mysqli('db', 'db', 'db', 'db');
$res = $m->query('SELECT id, title, category, date FROM matches');
while($row = $res->fetch_assoc()) {
    echo $row['id'] . ' - ' . $row['title'] . ' - ' . $row['category'] . ' - ' . $row['date'] . PHP_EOL;
}
