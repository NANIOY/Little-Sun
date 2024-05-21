<?php
session_save_path(__DIR__ . '/sessions');
session_start();

error_log('Session data: ' . print_r($_SESSION, true));
