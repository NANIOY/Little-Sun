<?php
session_save_path(__DIR__ . '/sessions');
session_start();
echo '<pre>';
print_r($_SESSION);
echo '</pre>';
