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

$action = $_GET['action'] ?? ($_SESSION['last_action'] ?? 'dashboard');

if ($action === 'install') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (install_system()) {
            flash('نصب با موفقیت انجام شد', 'success');
            redirect('dashboard');
        } else {
            flash('خطا در نصب سیستم', 'error');
        }
    }
}

if (!is_installed() && $action !== 'install') redirect('install');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($action) {
        case 'save_voucher':
            $vouchers = db_read(DB_VOUCHERS);
            $id = next_id(DB_VOUCHERS);
            $items = []; $td = 0; $tc = 0;
            for ($i = 0; $i < count($_POST['account_id'] ?? []); $i++) {
                $d = (float)($_POST['debit'][$i] ?? 0);
                $c = (float)($_POST['credit'][$i] ?? 0);
                if ($d > 0 || $c > 0) {
                    $items[] = ['account_id' => (int)$_POST['account_id'][$i], 'description' => $_POST['description'][$i] ?? '', 'debit' => $d, 'credit' => $c];
                    $td += $d; $tc += $c;
                }
            }
            if (abs($td - $tc) < 0.01 && count($items) >= 2) {
                $vouchers[] = ['id' => $id, 'voucher_no' => $id, 'date' => $_POST['date'], 'description' => $_POST['description_main'] ?? '', 'items' => $items, 'total_debit' => $td, 'total_credit' => $tc, 'status' => 'posted', 'created_at' => now()];
                db_write(DB_VOUCHERS, $vouchers);
                flash('سند ثبت شد', 'success');
                redirect('vouchers');
            } else { flash('جمع بدهکار و بستانکار باید برابر باشد', 'error'); }
            break;
            
        case 'save_account':
            $accounts = db_read(DB_ACCOUNTS);
            $accounts[] = ['id' => next_id(DB_ACCOUNTS), 'code' => $_POST['code'], 'title' => $_POST['title'], 'type' => $_POST['type'], 'parent' => (int)($_POST['parent'] ?? 0), 'level' => (int)($_POST['level'] ?? 1)];
            db_write(DB_ACCOUNTS, $accounts);
            flash('سرفصل ایجاد شد', 'success');
            redirect('accounts');
            break;
            
        case 'save_customer':
            $customers = db_read(DB_CUSTOMERS);
            $customers[] = ['id' => next_id(DB_CUSTOMERS), 'name' => $_POST['name'], 'code' => $_POST['code'], 'phone' => $_POST['phone'] ?? '', 'address' => $_POST['address'] ?? '', 'national_id' => $_POST['national_id'] ?? '', 'balance' => 0, 'created_at' => now()];
            db_write(DB_CUSTOMERS, $customers);
            flash('مشتری ثبت شد', 'success');
            redirect('customers');
            break;
            
        case 'save_supplier':
            $suppliers = db_read(DB_SUPPLIERS);
            $suppliers[] = ['id' => next_id(DB_SUPPLIERS), 'name' => $_POST['name'], 'code' => $_POST['code'], 'phone' => $_POST['phone'] ?? '', 'address' => $_POST['address'] ?? '', 'national_id' => $_POST['national_id'] ?? '', 'balance' => 0, 'created_at' => now()];
            db_write(DB_SUPPLIERS, $suppliers);
            flash('تأمین‌کننده ثبت شد', 'success');
            redirect('suppliers');
            break;
            
        case 'save_transaction':
            $transactions = db_read(DB_TRANSACTIONS);
            $type = $_POST['type']; $amount = (float)$_POST['amount'];
            $transactions[] = ['id' => next_id(DB_TRANSACTIONS), 'type' => $type, 'date' => $_POST['date'], 'amount' => $amount, 'account_id' => (int)$_POST['account_id'], 'party_type' => $_POST['party_type'] ?? '', 'party_id' => (int)($_POST['party_id'] ?? 0), 'description' => $_POST['description'] ?? '', 'reference' => $_POST['reference'] ?? '', 'created_at' => now()];
            db_write(DB_TRANSACTIONS, $transactions);
            if (!empty($_POST['party_type']) && !empty($_POST['party_id'])) {
                $pf = $_POST['party_type'] === 'customer' ? DB_CUSTOMERS : DB_SUPPLIERS;
                $parties = db_read($pf);
                foreach ($parties as &$p) {
                    if ($p['id'] == $_POST['party_id']) {
                        $p['balance'] += ($type === 'receipt') ? -$amount : $amount;
                        break;
                    }
                }
                db_write($pf, $parties);
            }
            flash('عملیات ثبت شد', 'success');
            redirect('transactions');
            break;
            
        case 'save_check':
            $checks = db_read(DB_CHECKS);
            $checks[] = ['id' => next_id(DB_CHECKS), 'type' => $_POST['type'], 'check_no' => $_POST['check_no'], 'bank' => $_POST['bank'], 'amount' => (float)$_POST['amount'], 'due_date' => $_POST['due_date'], 'issuer' => $_POST['issuer'] ?? '', 'account_id' => (int)$_POST['account_id'], 'status' => 'pending', 'created_at' => now()];
            db_write(DB_CHECKS, $checks);
            flash('چک ثبت شد', 'success');
            redirect('checks');
            break;
            
        case 'change_check_status':
            $checks = db_read(DB_CHECKS);
            foreach ($checks as &$c) {
                if ($c['id'] == $_POST['check_id']) {
                    $c['status'] = $_POST['new_status'];
                    break;
                }
            }
            db_write(DB_CHECKS, $checks);
            flash('وضعیت چک تغییر کرد', 'success');
            redirect('checks');
            break;
            
        case 'save_cashbox':
            $cashboxes = db_read(DB_CASHBOXES);
            $cashboxes[] = ['id' => next_id(DB_CASHBOXES), 'name' => $_POST['name'], 'balance' => 0];
            db_write(DB_CASHBOXES, $cashboxes);
            flash('صندوق ایجاد شد', 'success');
            redirect('cashboxes');
            break;
            
        case 'save_bank':
            $banks = db_read(DB_BANKS);
            $banks[] = ['id' => next_id(DB_BANKS), 'name' => $_POST['name'], 'account_no' => $_POST['account_no'], 'branch' => $_POST['branch'] ?? '', 'balance' => 0];
            db_write(DB_BANKS, $banks);
            flash('بانک ثبت شد', 'success');
            redirect('banks');
            break;
            
        case 'save_petty_cash':
            $petty = db_read(DB_PETTY_CASH);
            $petty[] = ['id' => next_id(DB_PETTY_CASH), 'name' => $_POST['name'], 'holder' => $_POST['holder'], 'amount' => (float)$_POST['amount'], 'balance' => (float)$_POST['amount'], 'created_at' => now()];
            db_write(DB_PETTY_CASH, $petty);
            flash('تنخواه ایجاد شد', 'success');
            redirect('petty_cash');
            break;
            
        case 'save_period':
            $periods = db_read(DB_PERIODS);
            if (!empty($_POST['is_current'])) foreach ($periods as &$p) $p['is_current'] = false;
            $periods[] = ['id' => next_id(DB_PERIODS), 'name' => $_POST['name'], 'start_date' => $_POST['start_date'], 'end_date' => $_POST['end_date'], 'is_current' => !empty($_POST['is_current']), 'is_closed' => false];
            db_write(DB_PERIODS, $periods);
            flash('دوره مالی ایجاد شد', 'success');
            redirect('periods');
            break;
            
        case 'save_purchase':
            $purchases = db_read(DB_PURCHASES);
            $id = next_id(DB_PURCHASES);
            $items = []; $total = 0;
            for ($i = 0; $i < count($_POST['product_name'] ?? []); $i++) {
                $qty = (float)$_POST['qty'][$i]; $price = (float)$_POST['price'][$i];
                $amount = $qty * $price;
                $items[] = ['product_name' => $_POST['product_name'][$i], 'qty' => $qty, 'price' => $price, 'amount' => $amount];
                $total += $amount;
                $inventory = db_read(DB_INVENTORY);
                $found = false;
                foreach ($inventory as &$inv) if ($inv['name'] === $_POST['product_name'][$i]) { $inv['qty'] += $qty; $found = true; break; }
                if (!$found) $inventory[] = ['id' => next_id(DB_INVENTORY), 'name' => $_POST['product_name'][$i], 'qty' => $qty, 'avg_price' => $price];
                db_write(DB_INVENTORY, $inventory);
            }
            $purchases[] = ['id' => $id, 'invoice_no' => $id, 'date' => $_POST['date'], 'supplier_id' => (int)$_POST['supplier_id'], 'items' => $items, 'total' => $total, 'description' => $_POST['description'] ?? '', 'created_at' => now()];
            db_write(DB_PURCHASES, $purchases);
            $suppliers = db_read(DB_SUPPLIERS);
            foreach ($suppliers as &$s) if ($s['id'] == $_POST['supplier_id']) { $s['balance'] += $total; break; }
            db_write(DB_SUPPLIERS, $suppliers);
            auto_voucher_purchase($id, $total, (int)$_POST['supplier_id']);
            flash('فاکتور خرید ثبت شد', 'success');
            redirect('purchases');
            break;
            
        case 'save_sale':
            $sales = db_read(DB_SALES);
            $id = next_id(DB_SALES);
            $items = []; $total = 0;
            for ($i = 0; $i < count($_POST['product_name'] ?? []); $i++) {
                $qty = (float)$_POST['qty'][$i]; $price = (float)$_POST['price'][$i];
                $amount = $qty * $price;
                $items[] = ['product_name' => $_POST['product_name'][$i], 'qty' => $qty, 'price' => $price, 'amount' => $amount];
                $total += $amount;
                $inventory = db_read(DB_INVENTORY);
                foreach ($inventory as &$inv) if ($inv['name'] === $_POST['product_name'][$i]) { $inv['qty'] -= $qty; break; }
                db_write(DB_INVENTORY, $inventory);
            }
            $sales[] = ['id' => $id, 'invoice_no' => $id, 'date' => $_POST['date'], 'customer_id' => (int)$_POST['customer_id'], 'items' => $items, 'total' => $total, 'description' => $_POST['description'] ?? '', 'created_at' => now()];
            db_write(DB_SALES, $sales);
            $customers = db_read(DB_CUSTOMERS);
            foreach ($customers as &$c) if ($c['id'] == $_POST['customer_id']) { $c['balance'] += $total; break; }
            db_write(DB_CUSTOMERS, $customers);
            auto_voucher_sale($id, $total, (int)$_POST['customer_id']);
            flash('فاکتور فروش ثبت شد', 'success');
            redirect('sales');
            break;
            
        case 'save_expense':
            $expenses = db_read(DB_EXPENSES);
            $expenses[] = ['id' => next_id(DB_EXPENSES), 'date' => $_POST['date'], 'account_id' => (int)$_POST['account_id'], 'amount' => (float)$_POST['amount'], 'description' => $_POST['description'] ?? '', 'created_at' => now()];
            db_write(DB_EXPENSES, $expenses);
            flash('هزینه ثبت شد', 'success');
            redirect('expenses');
            break;
            
        case 'save_income':
            $income = db_read(DB_INCOME);
            $income[] = ['id' => next_id(DB_INCOME), 'date' => $_POST['date'], 'account_id' => (int)$_POST['account_id'], 'amount' => (float)$_POST['amount'], 'description' => $_POST['description'] ?? '', 'created_at' => now()];
            db_write(DB_INCOME, $income);
            flash('درآمد ثبت شد', 'success');
            redirect('income');
            break;
            
        case 'close_period':
            $periods = db_read(DB_PERIODS);
            foreach ($periods as &$p) if ($p['id'] == $_POST['period_id']) { $p['is_closed'] = true; $p['is_current'] = false; break; }
            db_write(DB_PERIODS, $periods);
            flash('دوره بسته شد', 'success');
            redirect('periods');
            break;
            
        case 'reset_section':
            $section = $_POST['section'];
            $map = ['vouchers' => DB_VOUCHERS, 'transactions' => DB_TRANSACTIONS, 'checks' => DB_CHECKS, 'customers' => DB_CUSTOMERS, 'suppliers' => DB_SUPPLIERS, 'inventory' => DB_INVENTORY, 'purchases' => DB_PURCHASES, 'sales' => DB_SALES, 'expenses' => DB_EXPENSES, 'income' => DB_INCOME, 'accounts' => DB_ACCOUNTS, 'cashboxes' => DB_CASHBOXES, 'banks' => DB_BANKS, 'petty_cash' => DB_PETTY_CASH];
            if ($section === 'all') {
                foreach ($map as $f) db_write($f, []);
                db_write(DB_CASHBOXES, [['id' => 1, 'name' => 'صندوق اصلی', 'balance' => 0]]);
                flash('تمام داده‌ها ریست شد', 'success');
                redirect('dashboard');
            } elseif (isset($map[$section])) {
                db_write($map[$section], []);
                if ($section === 'cashboxes') db_write(DB_CASHBOXES, [['id' => 1, 'name' => 'صندوق اصلی', 'balance' => 0]]);
                flash('بخش با موفقیت ریست شد', 'success');
                redirect($section);
            }
            break;
            
        case 'backup':
            $backup = [];
            foreach (glob(DB_DIR . '*.json') as $f) $backup[basename($f)] = db_read($f);
            header('Content-Type: application/json');
            header("Content-Disposition: attachment; filename=\"backup_" . jdate('Y-m-d_H-i-s') . ".json\"");
            echo json_encode($backup, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            exit;
            break;
            
        case 'restore':
            if (isset($_FILES['backup_file'])) {
                $data = json_decode(file_get_contents($_FILES['backup_file']['tmp_name']), true);
                if (is_array($data)) {
                    foreach ($data as $filename => $content) db_write(DB_DIR . $filename, $content);
                    flash('اطلاعات بازیابی شد', 'success');
                } else flash('فایل نامعتبر است', 'error');
                redirect('backup');
            }
            break;
            
        case 'settings':
            if (isset($_POST['company_name'])) {
                $settings = db_read(DB_SETTINGS);
                $settings['company_name'] = $_POST['company_name'];
                $settings['currency'] = $_POST['currency'];
                db_write(DB_SETTINGS, $settings);
                flash('تنظیمات ذخیره شد', 'success');
                redirect('settings');
            }
            break;
    }
}

function auto_voucher_purchase($purchase_id, $amount, $supplier_id) {
    $vouchers = db_read(DB_VOUCHERS);
    $id = next_id(DB_VOUCHERS);
    $vouchers[] = ['id' => $id, 'voucher_no' => $id, 'date' => today(), 'description' => "فاکتور خرید شماره $purchase_id", 'items' => [['account_id' => 4, 'description' => 'موجودی کالا', 'debit' => $amount, 'credit' => 0], ['account_id' => 10, 'description' => "تأمین‌کننده $supplier_id", 'debit' => 0, 'credit' => $amount]], 'total_debit' => $amount, 'total_credit' => $amount, 'status' => 'auto', 'created_at' => now()];
    db_write(DB_VOUCHERS, $vouchers);
}

function auto_voucher_sale($sale_id, $amount, $customer_id) {
    $vouchers = db_read(DB_VOUCHERS);
    $id = next_id(DB_VOUCHERS);
    $vouchers[] = ['id' => $id, 'voucher_no' => $id, 'date' => today(), 'description' => "فاکتور فروش شماره $sale_id", 'items' => [['account_id' => 5, 'description' => "مشتری $customer_id", 'debit' => $amount, 'credit' => 0], ['account_id' => 16, 'description' => 'فروش داخلی', 'debit' => 0, 'credit' => $amount]], 'total_debit' => $amount, 'total_credit' => $amount, 'status' => 'auto', 'created_at' => now()];
    db_write(DB_VOUCHERS, $vouchers);
}

function info_box($title, $text) {
    echo '<div class="info-box"><div class="info-box-title">' . $title . '</div><div class="info-box-text">' . $text . '</div></div>';
}
?>
