<?php
function gregorian_to_jalali($gy, $gm, $gd) {
    $g_d_m = [0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334];
    $gy2 = ($gm > 2) ? ($gy + 1) : $gy;
    $days = 355666 + (365 * $gy) + ((int)(($gy2 + 3) / 4)) - ((int)(($gy2 + 99) / 100)) + ((int)(($gy2 + 399) / 400)) + $gd + $g_d_m[$gm - 1];
    $jy = -1595 + (33 * ((int)($days / 12053)));
    $days %= 12053;
    $jy += 4 * ((int)($days / 1461));
    $days %= 1461;
    if ($days > 365) {
        $jy += (int)(($days - 1) / 365);
        $days = ($days - 1) % 365;
    }
    if ($days < 186) {
        $jm = 1 + (int)($days / 31);
        $jd = 1 + ($days % 31);
    } else {
        $jm = 7 + (int)(($days - 186) / 30);
        $jd = 1 + (($days - 186) % 30);
    }
    return [$jy, $jm, $jd];
}

function jdate($format, $timestamp = null) {
    if ($timestamp === null) $timestamp = time();
    $gd = date('Y-m-d', $timestamp);
    list($gy, $gm, $gd) = explode('-', $gd);
    list($jy, $jm, $jd) = gregorian_to_jalali((int)$gy, (int)$gm, (int)$gd);
    $result = $format;
    $result = str_replace('Y', $jy, $result);
    $result = str_replace('m', str_pad($jm, 2, '0', STR_PAD_LEFT), $result);
    $result = str_replace('d', str_pad($jd, 2, '0', STR_PAD_LEFT), $result);
    $result = str_replace('H', date('H', $timestamp), $result);
    $result = str_replace('i', date('i', $timestamp), $result);
    $result = str_replace('s', date('s', $timestamp), $result);
    return $result;
}

function jtoday() { return jdate('Y/m/d'); }
function jnow() { return jdate('Y/m/d H:i:s'); }

function db_read($file) {
    if (!file_exists($file)) return [];
    $content = file_get_contents($file);
    $data = json_decode($content, true);
    return is_array($data) ? $data : [];
}

function db_write($file, $data) {
    if (!is_dir(DB_DIR)) mkdir(DB_DIR, 0755, true);
    return file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
}

function next_id($file) {
    $data = db_read($file);
    if (empty($data)) return 1;
    $max = 0;
    foreach ($data as $item) if (isset($item['id']) && $item['id'] > $max) $max = $item['id'];
    return $max + 1;
}

function is_installed() {
    return file_exists(DB_DIR) && file_exists(DB_SETTINGS);
}

function redirect($action = null) {
    if ($action) $_SESSION['last_action'] = $action;
    header("Location: ?" . ($action ? "#" . $action : ""));
    exit;
}

function flash($msg, $type = 'success') {
    $_SESSION['flash'] = ['msg' => $msg, 'type' => $type];
}

function get_flash() {
    if (isset($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $f;
    }
    return null;
}

function e($str) { return htmlspecialchars($str, ENT_QUOTES, 'UTF-8'); }
function format_money($amount) { return number_format((float)$amount, 0, '.', ','); }
function today() { return jtoday(); }
function now() { return jnow(); }

function log_action($action, $details = '') {
    $logs = db_read(DB_LOGS);
    $logs[] = ['id' => count($logs) + 1, 'action' => $action, 'details' => $details, 'time' => now()];
    db_write(DB_LOGS, $logs);
}

function current_period() {
    $periods = db_read(DB_PERIODS);
    foreach ($periods as $p) if (!empty($p['is_current'])) return $p;
    return null;
}

function install_system() {
    if (!is_dir(DB_DIR)) mkdir(DB_DIR, 0755, true);
    
    $default_accounts = [
        ['id' => 1, 'code' => '1', 'title' => 'دارایی‌ها', 'type' => 'header', 'parent' => 0, 'level' => 1],
        ['id' => 2, 'code' => '11', 'title' => 'دارایی‌های جاری', 'type' => 'group', 'parent' => 1, 'level' => 2],
        ['id' => 3, 'code' => '1101', 'title' => 'وجه نقد', 'type' => 'account', 'parent' => 2, 'level' => 3],
        ['id' => 4, 'code' => '1102', 'title' => 'بانک', 'type' => 'account', 'parent' => 2, 'level' => 3],
        ['id' => 5, 'code' => '1103', 'title' => 'حساب‌های دریافتنی', 'type' => 'account', 'parent' => 2, 'level' => 3],
        ['id' => 6, 'code' => '1104', 'title' => 'موجودی کالا', 'type' => 'account', 'parent' => 2, 'level' => 3],
        ['id' => 7, 'code' => '12', 'title' => 'دارایی‌های ثابت', 'type' => 'group', 'parent' => 1, 'level' => 2],
        ['id' => 8, 'code' => '2', 'title' => 'بدهی‌ها', 'type' => 'header', 'parent' => 0, 'level' => 1],
        ['id' => 9, 'code' => '21', 'title' => 'بدهی‌های جاری', 'type' => 'group', 'parent' => 8, 'level' => 2],
        ['id' => 10, 'code' => '2101', 'title' => 'حساب‌های پرداختنی', 'type' => 'account', 'parent' => 9, 'level' => 3],
        ['id' => 11, 'code' => '2102', 'title' => 'اسناد پرداختنی', 'type' => 'account', 'parent' => 9, 'level' => 3],
        ['id' => 12, 'code' => '3', 'title' => 'حقوق صاحبان سهام', 'type' => 'header', 'parent' => 0, 'level' => 1],
        ['id' => 13, 'code' => '31', 'title' => 'سرمایه', 'type' => 'group', 'parent' => 12, 'level' => 2],
        ['id' => 14, 'code' => '4', 'title' => 'درآمدها', 'type' => 'header', 'parent' => 0, 'level' => 1],
        ['id' => 15, 'code' => '41', 'title' => 'درآمد فروش', 'type' => 'group', 'parent' => 14, 'level' => 2],
        ['id' => 16, 'code' => '4101', 'title' => 'فروش داخلی', 'type' => 'account', 'parent' => 15, 'level' => 3],
        ['id' => 17, 'code' => '5', 'title' => 'هزینه‌ها', 'type' => 'header', 'parent' => 0, 'level' => 1],
        ['id' => 18, 'code' => '51', 'title' => 'بهای تمام شده', 'type' => 'group', 'parent' => 17, 'level' => 2],
        ['id' => 19, 'code' => '5101', 'title' => 'بهای تمام شده کالای فروش رفته', 'type' => 'account', 'parent' => 18, 'level' => 3],
        ['id' => 20, 'code' => '52', 'title' => 'هزینه‌های عملیاتی', 'type' => 'group', 'parent' => 17, 'level' => 2],
        ['id' => 21, 'code' => '5201', 'title' => 'هزینه حقوق', 'type' => 'account', 'parent' => 20, 'level' => 3],
        ['id' => 22, 'code' => '5202', 'title' => 'هزینه اجاره', 'type' => 'account', 'parent' => 20, 'level' => 3],
        ['id' => 23, 'code' => '5203', 'title' => 'هزینه آب و برق', 'type' => 'account', 'parent' => 20, 'level' => 3],
    ];
    
    db_write(DB_ACCOUNTS, $default_accounts);
    db_write(DB_VOUCHERS, []);
    db_write(DB_TRANSACTIONS, []);
    db_write(DB_CASHBOXES, [['id' => 1, 'name' => 'صندوق اصلی', 'balance' => 0]]);
    db_write(DB_BANKS, []);
    db_write(DB_PETTY_CASH, []);
    db_write(DB_CHECKS, []);
    db_write(DB_CUSTOMERS, []);
    db_write(DB_SUPPLIERS, []);
    db_write(DB_PERIODS, [['id' => 1, 'name' => 'دوره مالی ' . jdate('Y'), 'start_date' => jdate('Y') . '/01/01', 'end_date' => jdate('Y') . '/12/29', 'is_current' => true, 'is_closed' => false]]);
    db_write(DB_SETTINGS, ['company_name' => 'شرکت نمونه', 'fiscal_year' => jdate('Y'), 'currency' => 'ریال', 'installed_at' => now(), 'version' => '01 بتا']);
    db_write(DB_PURCHASES, []);
    db_write(DB_SALES, []);
    db_write(DB_INVENTORY, []);
    db_write(DB_EXPENSES, []);
    db_write(DB_INCOME, []);
    db_write(DB_LOGS, []);
    return true;
}

