<?php

include_once 'config.php';

foreach (glob(dirname(__FILE__) . "/includes/*.php") as $filename) {
    include $filename;
}