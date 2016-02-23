<?php

if (isset($_POST)) {
    $r = "fallback:\n" . print_r($_POST, true) . "\n\n";
    file_put_contents(dirname(__FILE__) . "/log", $r);
}