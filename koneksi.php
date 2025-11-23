<?php

$koneksi = mysqli_connect('localhost', 'root', '', 'ukkazni');
if (!$koneksi) {
    die('Unable to connect to database!');
}
