<?php

use SilverStripe\ORM\DB;

DB::setConfig([
    'type' => 'MySQLPDODatabase',
    'server' => 'backend.{{ domain }}',
    'username' => '{{ silverstripe_user }}',
    'password' => '{{ silverstripe_password }}',
    'database' => '{{ silverstripe_database }}'
]);
