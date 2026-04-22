<?php

ini_set('session.use_only_cookies', 1);       // never put session ID in the URL
ini_set('session.use_strict_mode', 1);        // reject session IDs the server didn't create
ini_set('session.cookie_httponly', 1);        // JS in the browser cannot read this cookie
ini_set('session.cookie_secure', 1);          // cookie only travels over HTTPS, never plain HTTP
ini_set('session.cookie_samesite', 'Strict'); // blocks the cookie being sent in cross-site requests
ini_set('session.gc_maxlifetime', 1800);      // session expires after 30 minutes of inactivity

session_start();

