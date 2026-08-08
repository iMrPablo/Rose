<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
session_start();
date_default_timezone_set('Asia/Tehran');

define('DB_DIR', __DIR__ . '/data/');
define('DB_ACCOUNTS', DB_DIR . 'accounts.json');
define('DB_VOUCHERS', DB_DIR . 'vouchers.json');
define('DB_TRANSACTIONS', DB_DIR . 'transactions.json');
define('DB_CASHBOXES', DB_DIR . 'cashboxes.json');
define('DB_BANKS', DB_DIR . 'banks.json');
define('DB_PETTY_CASH', DB_DIR . 'petty_cash.json');
define('DB_CHECKS', DB_DIR . 'checks.json');
define('DB_CUSTOMERS', DB_DIR . 'customers.json');
define('DB_SUPPLIERS', DB_DIR . 'suppliers.json');
define('DB_PERIODS', DB_DIR . 'periods.json');
define('DB_SETTINGS', DB_DIR . 'settings.json');
define('DB_PURCHASES', DB_DIR . 'purchases.json');
define('DB_SALES', DB_DIR . 'sales.json');
define('DB_INVENTORY', DB_DIR . 'inventory.json');
define('DB_EXPENSES', DB_DIR . 'expenses.json');
define('DB_INCOME', DB_DIR . 'income.json');
define('DB_LOGS', DB_DIR . 'logs.json');
