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
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>رز | سیستم حسابداری</title>
<style>
:root {
    --primary: #1a365d;
    --primary-light: #2c5282;
    --primary-dark: #0f2440;
    --accent: #3182ce;
    --bg: #f7fafc;
    --card: #ffffff;
    --border: #e2e8f0;
    --text: #2d3748;
    --text-light: #718096;
    --success: #38a169;
    --success-light: #f0fff4;
    --danger: #e53e3e;
    --danger-light: #fff5f5;
    --warning: #d69e2e;
    --warning-light: #fffaf0;
    --info: #3182ce;
    --info-light: #ebf8ff;
}

* { box-sizing: border-box; margin: 0; padding: 0; }

body { 
    font-family: 'Vazirmatn', 'Vazir', Tahoma, Arial, sans-serif; 
    background: var(--bg); 
    color: var(--text); 
    font-size: 14px;
    line-height: 1.7;
}

.app { display: flex; min-height: 100vh; }

.sidebar { 
    width: 270px; 
    background: var(--primary); 
    position: fixed;
    height: 100vh;
    overflow-y: auto;
    padding: 0;
    display: flex;
    flex-direction: column;
}

.sidebar-logo { 
    padding: 25px 20px;
    background: var(--primary-dark);
    text-align: center;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}

.logo-circle {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid rgba(255,255,255,0.3);
    margin-bottom: 12px;
    user-select: none;
    -webkit-user-drag: none;
    -webkit-touch-callout: none;
}

.sidebar-logo h1 { 
    font-size: 26px; 
    color: #fff; 
    font-weight: 600;
    letter-spacing: 2px;
    margin: 0;
}

.sidebar-logo span {
    display: block;
    margin-top: 4px;
    color: rgba(255,255,255,0.6);
    font-size: 11px;
}

.sidebar-nav {
    flex: 1;
    overflow-y: auto;
    padding-bottom: 10px;
}

.nav-section {
    padding: 16px 25px 6px;
    color: rgba(255,255,255,0.4);
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: 2px;
}

.nav-item { 
    display: block; 
    padding: 11px 25px; 
    color: rgba(255,255,255,0.75); 
    text-decoration: none; 
    transition: all 0.2s;
    font-size: 13px;
    border-right: 3px solid transparent;
    cursor: pointer;
}

.nav-item:hover { 
    background: rgba(255,255,255,0.06);
    color: #fff;
}

.nav-item.active { 
    background: linear-gradient(90deg, rgba(49,130,206,0.25) 0%, rgba(49,130,206,0.08) 100%);
    color: #fff;
    border-right-color: #63b3ed;
    font-weight: 500;
}

.sidebar-footer {
    padding: 15px 25px;
    border-top: 1px solid rgba(255,255,255,0.1);
    text-align: center;
    background: var(--primary-dark);
}

.footer-designer {
    color: rgba(255,255,255,0.85);
    font-size: 12px;
    margin-bottom: 3px;
}

.footer-version {
    color: #63b3ed;
    font-size: 11px;
    letter-spacing: 1px;
    font-weight: 500;
}

.main { 
    flex: 1; 
    margin-right: 270px;
    padding: 25px 35px;
}

.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
    padding-bottom: 18px;
    border-bottom: 1px solid var(--border);
}

.header h2 {
    font-size: 22px;
    font-weight: 600;
    color: var(--primary);
}

.header .date {
    color: var(--text-light);
    font-size: 13px;
}

.info-box {
    background: var(--info-light);
    border: 1px solid #bee3f8;
    border-right: 4px solid var(--info);
    border-radius: 6px;
    padding: 16px 18px;
    margin-bottom: 22px;
}

.info-box-title {
    font-weight: 600;
    color: var(--info);
    margin-bottom: 6px;
    font-size: 14px;
}

.info-box-text {
    color: var(--text);
    font-size: 13px;
    line-height: 1.9;
}

.card { 
    background: var(--card); 
    padding: 28px; 
    margin-bottom: 22px;
    border: 1px solid var(--border);
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}

.card-title { 
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 18px;
    padding-bottom: 14px;
    border-bottom: 1px solid var(--border);
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: var(--primary);
    gap: 10px;
    flex-wrap: wrap;
}

.card-title-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.stats { 
    display: grid; 
    grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); 
    gap: 18px; 
    margin-bottom: 22px;
}

.stat { 
    background: var(--card);
    padding: 22px;
    border: 1px solid var(--border);
    border-radius: 8px;
    transition: all 0.3s;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    border-right: 4px solid var(--accent);
}

.stat:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    transform: translateY(-2px);
}

.stat-label { 
    color: var(--text-light); 
    font-size: 12px; 
    margin-bottom: 8px;
}

.stat-value { 
    font-size: 24px; 
    font-weight: 700;
    color: var(--primary);
}

.stat.success { border-right-color: var(--success); }
.stat.danger { border-right-color: var(--danger); }
.stat.warning { border-right-color: var(--warning); }

.form-grid { 
    display: grid; 
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); 
    gap: 18px; 
    margin-bottom: 18px;
}

.form-group { margin-bottom: 16px; }

.form-group label { 
    display: block; 
    margin-bottom: 7px; 
    color: var(--text-light); 
    font-size: 12px;
    font-weight: 500;
}

.form-group input, 
.form-group select, 
.form-group textarea { 
    width: 100%; 
    padding: 11px 13px; 
    border: 1px solid var(--border); 
    border-radius: 6px;
    font-size: 14px;
    transition: all 0.2s;
    background: #fff;
    font-family: inherit;
}

.form-group input:focus, 
.form-group select:focus { 
    outline: none; 
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(49, 130, 206, 0.1);
}

.btn { 
    padding: 10px 22px; 
    border: 1px solid var(--border);
    border-radius: 6px;
    cursor: pointer; 
    font-size: 13px; 
    text-decoration: none; 
    display: inline-block; 
    transition: all 0.2s;
    background: #fff;
    color: var(--text);
    font-weight: 500;
    font-family: inherit;
}

.btn:hover { background: var(--bg); }

.btn-primary { background: var(--primary); color: #fff; border-color: var(--primary); }
.btn-primary:hover { background: var(--primary-light); }

.btn-success { background: var(--success); color: #fff; border-color: var(--success); }
.btn-success:hover { background: #2f855a; }

.btn-danger { background: var(--danger); color: #fff; border-color: var(--danger); }
.btn-danger:hover { background: #c53030; }

.btn-warning { background: var(--warning); color: #fff; border-color: var(--warning); }
.btn-warning:hover { background: #b7791f; }

.btn-sm { padding: 6px 12px; font-size: 12px; }

table { width: 100%; border-collapse: collapse; }

table th, table td { 
    padding: 11px; 
    text-align: right; 
    border-bottom: 1px solid var(--border);
    font-size: 13px;
}

table th { 
    color: var(--text-light);
    font-weight: 600;
    font-size: 12px;
    background: var(--bg);
}

table tr:hover { background: var(--bg); }

.badge { 
    display: inline-block; 
    padding: 4px 10px; 
    font-size: 11px;
    border-radius: 4px;
    font-weight: 500;
}

.badge-success { background: var(--success-light); color: var(--success); }
.badge-warning { background: var(--warning-light); color: var(--warning); }
.badge-danger { background: var(--danger-light); color: var(--danger); }
.badge-info { background: var(--info-light); color: var(--info); }

.alert { 
    padding: 14px 18px; 
    margin-bottom: 20px; 
    border-radius: 6px;
    font-size: 13px;
}

.alert-success { background: var(--success-light); color: var(--success); border: 1px solid #c6f6d5; }
.alert-error { background: var(--danger-light); color: var(--danger); border: 1px solid #fed7d7; }

.install-box { 
    max-width: 550px; 
    margin: 80px auto; 
    background: var(--card); 
    padding: 45px; 
    border: 1px solid var(--border);
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    text-align: center;
}

.install-box h1 { 
    color: var(--primary); 
    font-size: 32px;
    font-weight: 700;
    margin-bottom: 8px;
}

.install-box .subtitle {
    color: var(--text-light);
    margin-bottom: 35px;
    font-size: 14px;
}

.install-step { 
    padding: 16px; 
    background: var(--bg);
    margin-bottom: 10px; 
    display: flex; 
    align-items: center; 
    gap: 15px;
    border-radius: 8px;
    text-align: right;
}

.install-step .num { 
    width: 34px; 
    height: 34px; 
    background: var(--primary);
    color: #fff; 
    border-radius: 50%; 
    display: flex; 
    align-items: center; 
    justify-content: center; 
    font-size: 14px;
    font-weight: 600;
    flex-shrink: 0;
}

.install-step strong { display: block; color: var(--text); font-size: 14px; }
.install-step p { color: var(--text-light); font-size: 12px; margin-top: 2px; }

.page-content { display: none; }
.page-content.active { display: block; }

.text-center { text-align: center; }
.mt-2 { margin-top: 18px; }
.mb-2 { margin-bottom: 18px; }

.status-select {
    padding: 5px 8px;
    border: 1px solid var(--border);
    border-radius: 4px;
    font-size: 12px;
    background: #fff;
    cursor: pointer;
    font-family: inherit;
}

.inline-form { display: inline; }

@media print {
    .sidebar, .btn, .info-box { display: none !important; }
    .main { margin-right: 0; padding: 0; }
}

@media (max-width: 768px) {
    .sidebar { display: none; }
    .main { margin-right: 0; padding: 15px; }
    .form-grid { grid-template-columns: 1fr; }
    .stats { grid-template-columns: 1fr; }
}
</style>
</head>
<body>
<?php
$flash = get_flash();
$settings = db_read(DB_SETTINGS);
$company_name = $settings['company_name'] ?? 'رز';

if ($action === 'install' && !is_installed()):
?>
<div class="install-box">
    <img src="https://abrehamrahi.ir/o/temp/3YOCettc/" class="logo-circle" style="width:90px;height:90px;" oncontextmenu="return false;" ondragstart="return false;">
    <h1>رز</h1>
    <div class="subtitle">سیستم حسابداری یکپارچه | نسخه 01 بتا</div>
    <div style="margin-bottom: 25px">
        <div class="install-step">
            <div class="num">1</div>
            <div><strong>بررسی پیش‌نیازها</strong><p>PHP <?= phpversion() ?> | JSON</p></div>
        </div>
        <div class="install-step">
            <div class="num">2</div>
            <div><strong>ایجاد دیتابیس</strong><p>فایل‌های JSON در پوشه data/</p></div>
        </div>
        <div class="install-step">
            <div class="num">3</div>
            <div><strong>آماده‌سازی</strong><p>سیستم بدون نیاز به لاگین</p></div>
        </div>
    </div>
    <?php if ($flash): ?><div class="alert alert-<?= $flash['type'] ?>"><?= e($flash['msg']) ?></div><?php endif; ?>
    <form method="POST">
        <button type="submit" class="btn btn-primary" style="width: 100%">شروع نصب</button>
    </form>
    <div style="margin-top: 20px; color: var(--text-light); font-size: 12px">طراح آقای پابلو</div>
</div>
<?php else: ?>
<div class="app">
    <aside class="sidebar">
        <div class="sidebar-logo">
            <img src="https://abrehamrahi.ir/o/temp/3YOCettc/" class="logo-circle" alt="رز" oncontextmenu="return false;" ondragstart="return false;">
            <h1>رز</h1>
            <span><?= e($company_name) ?></span>
        </div>
        
        <div class="sidebar-nav">
            <div class="nav-section">اصلی</div>
            <a data-action="dashboard" class="nav-item">داشبورد</a>
            
            <div class="nav-section">حسابداری</div>
            <a data-action="vouchers" class="nav-item">اسناد حسابداری</a>
            <a data-action="new_voucher" class="nav-item">ثبت سند جدید</a>
            <a data-action="accounts" class="nav-item">سرفصل‌ها</a>
            
            <div class="nav-section">دریافت و پرداخت</div>
            <a data-action="transactions" class="nav-item">دریافت و پرداخت</a>
            <a data-action="checks" class="nav-item">چک‌ها</a>
            
            <div class="nav-section">صندوق و بانک</div>
            <a data-action="cashboxes" class="nav-item">صندوق‌ها</a>
            <a data-action="banks" class="nav-item">بانک‌ها</a>
            <a data-action="petty_cash" class="nav-item">تنخواه</a>
            
            <div class="nav-section">اشخاص</div>
            <a data-action="customers" class="nav-item">مشتریان</a>
            <a data-action="suppliers" class="nav-item">تأمین‌کنندگان</a>
            
            <div class="nav-section">عملیات</div>
            <a data-action="purchases" class="nav-item">خرید</a>
            <a data-action="sales" class="nav-item">فروش</a>
            <a data-action="inventory" class="nav-item">انبار</a>
            <a data-action="expenses" class="nav-item">هزینه‌ها</a>
            <a data-action="income" class="nav-item">درآمدها</a>
            
            <div class="nav-section">گزارشات</div>
            <a data-action="trial_balance" class="nav-item">تراز آزمایشی</a>
            <a data-action="balance_sheet" class="nav-item">ترازنامه</a>
            <a data-action="profit_loss" class="nav-item">سود و زیان</a>
            <a data-action="ledger" class="nav-item">دفتر کل</a>
            <a data-action="party_report" class="nav-item">گردش حساب</a>
            
            <div class="nav-section">تنظیمات</div>
            <a data-action="periods" class="nav-item">دوره‌های مالی</a>
            <a data-action="backup" class="nav-item">پشتیبان‌گیری</a>
            <a data-action="settings" class="nav-item">تنظیمات</a>
        </div>
        
        <div class="sidebar-footer">
            <div class="footer-designer">طراح آقای پابلو</div>
            <div class="footer-version">نسخه 01 بتا</div>
        </div>
    </aside>
    
    <main class="main">
        <div class="header">
            <h2 id="page-title">داشبورد</h2>
            <div class="date"><?= jdate('Y/m/d') ?></div>
        </div>
        
        <?php if ($flash): ?><div class="alert alert-<?= $flash['type'] ?>"><?= e($flash['msg']) ?></div><?php endif; ?>
        
        <div id="page-dashboard" class="page-content">
            <?php render_dashboard(); ?>
        </div>
        <div id="page-vouchers" class="page-content"><?php render_vouchers(); ?></div>
        <div id="page-new_voucher" class="page-content"><?php render_new_voucher(); ?></div>
        <div id="page-accounts" class="page-content"><?php render_accounts(); ?></div>
        <div id="page-new_account" class="page-content"><?php render_new_account(); ?></div>
        <div id="page-transactions" class="page-content"><?php render_transactions(); ?></div>
        <div id="page-new_transaction" class="page-content"><?php render_new_transaction(); ?></div>
        <div id="page-checks" class="page-content"><?php render_checks(); ?></div>
        <div id="page-new_check" class="page-content"><?php render_new_check(); ?></div>
        <div id="page-cashboxes" class="page-content"><?php render_cashboxes(); ?></div>
        <div id="page-new_cashbox" class="page-content"><?php render_new_cashbox(); ?></div>
        <div id="page-banks" class="page-content"><?php render_banks(); ?></div>
        <div id="page-new_bank" class="page-content"><?php render_new_bank(); ?></div>
        <div id="page-petty_cash" class="page-content"><?php render_petty_cash(); ?></div>
        <div id="page-new_petty_cash" class="page-content"><?php render_new_petty_cash(); ?></div>
        <div id="page-customers" class="page-content"><?php render_customers(); ?></div>
        <div id="page-new_customer" class="page-content"><?php render_new_customer(); ?></div>
        <div id="page-suppliers" class="page-content"><?php render_suppliers(); ?></div>
        <div id="page-new_supplier" class="page-content"><?php render_new_supplier(); ?></div>
        <div id="page-purchases" class="page-content"><?php render_purchases(); ?></div>
        <div id="page-new_purchase" class="page-content"><?php render_new_purchase(); ?></div>
        <div id="page-sales" class="page-content"><?php render_sales(); ?></div>
        <div id="page-new_sale" class="page-content"><?php render_new_sale(); ?></div>
        <div id="page-inventory" class="page-content"><?php render_inventory(); ?></div>
        <div id="page-expenses" class="page-content"><?php render_expenses(); ?></div>
        <div id="page-new_expense" class="page-content"><?php render_new_expense(); ?></div>
        <div id="page-income" class="page-content"><?php render_income(); ?></div>
        <div id="page-new_income" class="page-content"><?php render_new_income(); ?></div>
        <div id="page-trial_balance" class="page-content"><?php render_trial_balance(); ?></div>
        <div id="page-balance_sheet" class="page-content"><?php render_balance_sheet(); ?></div>
        <div id="page-profit_loss" class="page-content"><?php render_profit_loss(); ?></div>
        <div id="page-ledger" class="page-content"><?php render_ledger(); ?></div>
        <div id="page-party_report" class="page-content"><?php render_party_report(); ?></div>
        <div id="page-periods" class="page-content"><?php render_periods(); ?></div>
        <div id="page-new_period" class="page-content"><?php render_new_period(); ?></div>
        <div id="page-backup" class="page-content"><?php render_backup(); ?></div>
        <div id="page-settings" class="page-content"><?php render_settings(); ?></div>
    </main>
</div>

<script>
const titles = {
    dashboard: 'داشبورد', vouchers: 'اسناد حسابداری', new_voucher: 'ثبت سند جدید',
    accounts: 'سرفصل‌ها', new_account: 'سرفصل جدید', transactions: 'دریافت و پرداخت',
    new_transaction: 'ثبت دریافت/پرداخت', checks: 'چک‌ها', new_check: 'چک جدید',
    cashboxes: 'صندوق‌ها', new_cashbox: 'صندوق جدید', banks: 'بانک‌ها', new_bank: 'بانک جدید',
    petty_cash: 'تنخواه', new_petty_cash: 'تنخواه جدید', customers: 'مشتریان',
    new_customer: 'مشتری جدید', suppliers: 'تأمین‌کنندگان', new_supplier: 'تأمین‌کننده جدید',
    purchases: 'خرید', new_purchase: 'فاکتور خرید', sales: 'فروش', new_sale: 'فاکتور فروش',
    inventory: 'انبار', expenses: 'هزینه‌ها', new_expense: 'ثبت هزینه', income: 'درآمدها',
    new_income: 'ثبت درآمد', trial_balance: 'تراز آزمایشی', balance_sheet: 'ترازنامه',
    profit_loss: 'سود و زیان', ledger: 'دفتر کل', party_report: 'گردش حساب',
    periods: 'دوره‌های مالی', new_period: 'دوره جدید', backup: 'پشتیبان‌گیری', settings: 'تنظیمات'
};

function showPage(action) {
    document.querySelectorAll('.page-content').forEach(p => p.classList.remove('active'));
    const target = document.getElementById('page-' + action);
    if (target) target.classList.add('active');
    
    document.querySelectorAll('.nav-item').forEach(a => {
        a.classList.toggle('active', a.dataset.action === action);
    });
    
    document.getElementById('page-title').textContent = titles[action] || action;
    window.scrollTo(0, 0);
}

document.querySelectorAll('.nav-item').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        const action = this.dataset.action;
        history.replaceState(null, '', '#' + action);
        showPage(action);
    });
});

window.addEventListener('hashchange', () => {
    showPage(window.location.hash.slice(1) || 'dashboard');
});

document.addEventListener('contextmenu', function(e) {
    if (e.target.classList.contains('logo-circle')) e.preventDefault();
});

document.querySelectorAll('.logo-circle').forEach(img => {
    img.addEventListener('dragstart', e => e.preventDefault());
});

const initial = window.location.hash.slice(1) || '<?= e($_SESSION['last_action'] ?? 'dashboard') ?>';
showPage(initial);
</script>
<?php endif; ?>

<?php
function render_dashboard() {
    $vouchers = db_read(DB_VOUCHERS);
    $customers = db_read(DB_CUSTOMERS);
    $suppliers = db_read(DB_SUPPLIERS);
    $cashboxes = db_read(DB_CASHBOXES);
    $banks = db_read(DB_BANKS);
    $checks = db_read(DB_CHECKS);
    
    $total_cash = array_sum(array_column($cashboxes, 'balance'));
    $total_bank = array_sum(array_column($banks, 'balance'));
    $total_receivable = array_sum(array_column($customers, 'balance'));
    $total_payable = array_sum(array_column($suppliers, 'balance'));
    $pending_checks = count(array_filter($checks, fn($c) => $c['status'] === 'pending'));
    $period = current_period();
    
    info_box('داشبورد', 'داشبورد نمای کلی از وضعیت مالی شرکت را نشان می‌دهد. در این بخش می‌توانید موجودی نقدی، موجودی بانک، حساب‌های دریافتنی و پرداختنی، تعداد اسناد ثبت شده و چک‌های در جریان را مشاهده کنید. این اطلاعات به صورت خودکار از سایر بخش‌های سیستم جمع‌آوری می‌شود.');
?>
<div class="stats">
    <div class="stat success"><div class="stat-label">موجودی نقد</div><div class="stat-value"><?= format_money($total_cash) ?></div></div>
    <div class="stat"><div class="stat-label">موجودی بانک</div><div class="stat-value"><?= format_money($total_bank) ?></div></div>
    <div class="stat warning"><div class="stat-label">حساب‌های دریافتنی</div><div class="stat-value"><?= format_money($total_receivable) ?></div></div>
    <div class="stat danger"><div class="stat-label">حساب‌های پرداختنی</div><div class="stat-value"><?= format_money($total_payable) ?></div></div>
    <div class="stat"><div class="stat-label">تعداد اسناد</div><div class="stat-value"><?= count($vouchers) ?></div></div>
    <div class="stat warning"><div class="stat-label">چک‌های در جریان</div><div class="stat-value"><?= $pending_checks ?></div></div>
</div>

<div class="card">
    <div class="card-title"><span>اطلاعات دوره مالی</span></div>
    <?php if ($period): ?>
    <table>
        <tr><th style="width:200px">نام دوره</th><td><?= e($period['name']) ?></td></tr>
        <tr><th>تاریخ شروع</th><td><?= e($period['start_date']) ?></td></tr>
        <tr><th>تاریخ پایان</th><td><?= e($period['end_date']) ?></td></tr>
        <tr><th>وضعیت</th><td><span class="badge badge-<?= $period['is_closed']?'danger':'success' ?>"><?= $period['is_closed']?'بسته':'باز' ?></span></td></tr>
    </table>
    <?php else: ?><p>دوره مالی فعالی تعریف نشده است.</p><?php endif; ?>
</div>

<div class="card">
    <div class="card-title">
        <span>آخرین اسناد</span>
        <div class="card-title-actions">
            <a data-action="vouchers" class="btn btn-sm nav-link">مشاهده همه</a>
        </div>
    </div>
    <table>
        <tr><th>شماره</th><th>تاریخ</th><th>توضیحات</th><th>مبلغ</th><th>وضعیت</th></tr>
        <?php
        $recent = array_slice(array_reverse($vouchers), 0, 5);
        foreach ($recent as $v):
        ?>
        <tr>
            <td><?= $v['voucher_no'] ?></td>
            <td><?= e($v['date']) ?></td>
            <td><?= e($v['description']) ?></td>
            <td><?= format_money($v['total_debit']) ?></td>
            <td><span class="badge badge-<?= $v['status']==='auto'?'info':'success' ?>"><?= e($v['status']) ?></span></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($recent)): ?><tr><td colspan="5" class="text-center">سندی ثبت نشده است</td></tr><?php endif; ?>
    </table>
</div>

<div class="card">
    <div class="card-title">
        <span>ریست سریع سیستم</span>
    </div>
    <p style="color: var(--text-light); margin-bottom: 15px">با ریست سیستم، تمامی اطلاعات وارد شده (اسناد، تراکنش‌ها، مشتریان، تأمین‌کنندگان و...) حذف می‌شود. ساختار سرفصل‌ها، دوره‌ها و تنظیمات باقی می‌ماند.</p>
    <form method="POST" action="?action=reset_section" onsubmit="return confirm('آیا از ریست کل سیستم مطمئن هستید؟ این عمل قابل بازگشت نیست.')">
        <input type="hidden" name="section" value="all">
        <button type="submit" class="btn btn-danger">ریست کامل سیستم</button>
    </form>
</div>

<script>
document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        const action = this.dataset.action;
        history.replaceState(null, '', '#' + action);
        showPage(action);
    });
});
</script>
<?php
}

function render_vouchers() {
    $vouchers = db_read(DB_VOUCHERS);
    info_box('اسناد حسابداری', 'در این بخش تمامی اسناد حسابداری ثبت شده نمایش داده می‌شود. سند حسابداری ثبت یک رویداد مالی است که شامل یک یا چند سطر بدهکار و بستانکار می‌باشد. اسناد می‌توانند به صورت دستی یا خودکار از عملیات خرید و فروش صادر شوند. جمع بدهکار و بستانکار هر سند باید برابر باشد.');
?>
<div class="card">
    <div class="card-title">
        <span>لیست اسناد حسابداری</span>
        <div class="card-title-actions">
            <a data-action="new_voucher" class="btn btn-primary btn-sm nav-link">ثبت سند جدید</a>
            <form method="POST" action="?action=reset_section" class="inline-form" onsubmit="return confirm('آیا از حذف تمام اسناد مطمئن هستید؟')">
                <input type="hidden" name="section" value="vouchers">
                <button type="submit" class="btn btn-danger btn-sm">ریست اسناد</button>
            </form>
        </div>
    </div>
    <table>
        <tr><th>شماره</th><th>تاریخ</th><th>توضیحات</th><th>بدهکار</th><th>بستانکار</th><th>وضعیت</th></tr>
        <?php foreach (array_reverse($vouchers) as $v): ?>
        <tr>
            <td><?= $v['voucher_no'] ?></td>
            <td><?= e($v['date']) ?></td>
            <td><?= e($v['description']) ?></td>
            <td><?= format_money($v['total_debit']) ?></td>
            <td><?= format_money($v['total_credit']) ?></td>
            <td><span class="badge badge-<?= $v['status']==='auto'?'info':($v['status']==='posted'?'success':'warning') ?>"><?= e($v['status']) ?></span></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($vouchers)): ?><tr><td colspan="6" class="text-center">سندی ثبت نشده است</td></tr><?php endif; ?>
    </table>
</div>
<script>
document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        history.replaceState(null, '', '#' + this.dataset.action);
        showPage(this.dataset.action);
    });
});
</script>
<?php
}

function render_new_voucher() {
    $accounts = db_read(DB_ACCOUNTS);
    $leaf_accounts = array_filter($accounts, fn($a) => $a['type'] === 'account');
    info_box('ثبت سند جدید', 'هر سند حداقل باید دو سطر داشته باشد و جمع بدهکار و بستانکار باید برابر باشد. سرفصل‌های حساب را از لیست انتخاب کنید و مبلغ بدهکار یا بستانکار را وارد نمایید. سیستم به صورت خودکار جمع‌ها را محاسبه و اختلاف را نمایش می‌دهد.');
?>
<div class="card">
    <div class="card-title"><span>ثبت سند حسابداری جدید</span></div>
    <form method="POST" action="?action=save_voucher">
        <div class="form-grid">
            <div class="form-group"><label>تاریخ</label><input type="text" name="date" value="<?= today() ?>" placeholder="1403/01/01" required></div>
            <div class="form-group"><label>توضیحات سند</label><input type="text" name="description_main" required></div>
        </div>
        <div style="margin: 20px 0 12px; font-size: 14px; color: var(--text-light); font-weight: 500">سطرهای سند</div>
        <table id="voucherItems">
            <thead><tr><th>سرفصل</th><th>توضیحات</th><th>بدهکار</th><th>بستانکار</th><th style="width:50px"></th></tr></thead>
            <tbody>
                <?php for ($i = 0; $i < 2; $i++): ?>
                <tr>
                    <td><select name="account_id[]" required style="width:100%;padding:10px;border:1px solid var(--border);border-radius:4px"><option value="">انتخاب کنید</option><?php foreach ($leaf_accounts as $a): ?><option value="<?= $a['id'] ?>"><?= e($a['code']) ?> - <?= e($a['title']) ?></option><?php endforeach; ?></select></td>
                    <td><input type="text" name="description[]" style="width:100%;padding:10px;border:1px solid var(--border);border-radius:4px"></td>
                    <td><input type="number" name="debit[]" step="0.01" min="0" class="debit-input" style="width:100%;padding:10px;border:1px solid var(--border);border-radius:4px"></td>
                    <td><input type="number" name="credit[]" step="0.01" min="0" class="credit-input" style="width:100%;padding:10px;border:1px solid var(--border);border-radius:4px"></td>
                    <td><button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove();calcTotal()">×</button></td>
                </tr>
                <?php endfor; ?>
            </tbody>
            <tfoot>
                <tr><td colspan="5"><button type="button" class="btn btn-success btn-sm" onclick="addRow()">+ سطر جدید</button></td></tr>
                <tr style="background: var(--bg)"><th>جمع بدهکار</th><td id="totalDebit">0</td><th>جمع بستانکار</th><td id="totalCredit">0</td><th>اختلاف: <span id="diff">0</span></th></tr>
            </tfoot>
        </table>
        <div class="mt-2">
            <button type="submit" class="btn btn-primary">ثبت سند</button>
            <a data-action="vouchers" class="btn nav-link">انصراف</a>
        </div>
    </form>
</div>
<script>
function addRow() {
    const tbody = document.querySelector('#voucherItems tbody');
    const row = tbody.querySelector('tr').cloneNode(true);
    row.querySelectorAll('input').forEach(i => i.value = '');
    row.querySelectorAll('select').forEach(s => s.selectedIndex = 0);
    tbody.appendChild(row);
}
function calcTotal() {
    let td = 0, tc = 0;
    document.querySelectorAll('.debit-input').forEach(i => td += parseFloat(i.value) || 0);
    document.querySelectorAll('.credit-input').forEach(i => tc += parseFloat(i.value) || 0);
    document.getElementById('totalDebit').textContent = td.toLocaleString();
    document.getElementById('totalCredit').textContent = tc.toLocaleString();
    const diff = Math.abs(td - tc);
    document.getElementById('diff').textContent = diff.toLocaleString();
    document.getElementById('diff').style.color = diff < 0.01 ? 'var(--success)' : 'var(--danger)';
}
document.addEventListener('input', e => { if (e.target.classList.contains('debit-input') || e.target.classList.contains('credit-input')) calcTotal(); });
calcTotal();
document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        history.replaceState(null, '', '#' + this.dataset.action);
        showPage(this.dataset.action);
    });
});
</script>
<?php
}

function render_accounts() {
    $accounts = db_read(DB_ACCOUNTS);
    info_box('سرفصل‌ها و کدینگ', 'در این بخش ساختار درختی حساب‌های حسابداری تعریف می‌شود. کدینگ حسابداری شامل سه سطح است: سرفصل کل، گروه و حساب معین. هر حساب معین می‌تواند در اسناد حسابداری استفاده شود.');
?>
<div class="card">
    <div class="card-title">
        <span>سرفصل‌ها و کدینگ حسابداری</span>
        <div class="card-title-actions">
            <a data-action="new_account" class="btn btn-primary btn-sm nav-link">سرفصل جدید</a>
            <form method="POST" action="?action=reset_section" class="inline-form" onsubmit="return confirm('آیا از حذف تمام سرفصل‌ها مطمئن هستید؟ این عمل بسیار خطرناک است!')">
                <input type="hidden" name="section" value="accounts">
                <button type="submit" class="btn btn-danger btn-sm">ریست سرفصل‌ها</button>
            </form>
        </div>
    </div>
    <table>
        <tr><th>کد</th><th>عنوان</th><th>نوع</th><th>سطح</th></tr>
        <?php foreach ($accounts as $a): ?>
        <tr>
            <td><?= e($a['code']) ?></td>
            <td style="padding-right: <?= ($a['level']-1)*20 + 12 ?>px"><?= e($a['title']) ?></td>
            <td><span class="badge badge-<?= $a['type']==='account'?'success':($a['type']==='group'?'info':'warning') ?>"><?= e($a['type']) ?></span></td>
            <td><?= $a['level'] ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
<script>
document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        history.replaceState(null, '', '#' + this.dataset.action);
        showPage(this.dataset.action);
    });
});
</script>
<?php
}

function render_new_account() {
    $accounts = db_read(DB_ACCOUNTS);
    info_box('ایجاد سرفصل جدید', 'برای ایجاد سرفصل جدید، کد یکتا، عنوان و نوع سرفصل را مشخص کنید. سرفصل والد را برای ایجاد ساختار درختی انتخاب کنید.');
?>
<div class="card">
    <div class="card-title"><span>ایجاد سرفصل جدید</span></div>
    <form method="POST" action="?action=save_account">
        <div class="form-grid">
            <div class="form-group"><label>کد سرفصل</label><input type="text" name="code" required></div>
            <div class="form-group"><label>عنوان</label><input type="text" name="title" required></div>
            <div class="form-group"><label>نوع</label><select name="type" required><option value="header">سرفصل کل</option><option value="group">گروه</option><option value="account">حساب معین</option></select></div>
            <div class="form-group"><label>سطح</label><input type="number" name="level" value="1" min="1" max="5" required></div>
            <div class="form-group"><label>سرفصل والد</label><select name="parent"><option value="0">ندارد (ریشه)</option><?php foreach ($accounts as $a): ?><option value="<?= $a['id'] ?>"><?= e($a['code']) ?> - <?= e($a['title']) ?></option><?php endforeach; ?></select></div>
        </div>
        <button type="submit" class="btn btn-primary">ذخیره</button>
        <a data-action="accounts" class="btn nav-link">انصراف</a>
    </form>
</div>
<script>
document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        history.replaceState(null, '', '#' + this.dataset.action);
        showPage(this.dataset.action);
    });
});
</script>
<?php
}

function render_transactions() {
    $transactions = db_read(DB_TRANSACTIONS);
    info_box('دریافت و پرداخت', 'در این بخش عملیات دریافت و پرداخت وجه ثبت می‌شود. دریافت هنگام دریافت وجه از مشتری و پرداخت هنگام پرداخت وجه به تأمین‌کننده ثبت می‌شود. با ثبت هر عملیات، مانده حساب طرف مقابل به صورت خودکار به‌روزرسانی می‌شود.');
?>
<div class="card">
    <div class="card-title">
        <span>دریافت و پرداخت</span>
        <div class="card-title-actions">
            <a data-action="new_transaction" class="btn btn-primary btn-sm nav-link">ثبت جدید</a>
            <form method="POST" action="?action=reset_section" class="inline-form" onsubmit="return confirm('آیا از حذف تمام تراکنش‌ها مطمئن هستید؟')">
                <input type="hidden" name="section" value="transactions">
                <button type="submit" class="btn btn-danger btn-sm">ریست تراکنش‌ها</button>
            </form>
        </div>
    </div>
    <table>
        <tr><th>تاریخ</th><th>نوع</th><th>مبلغ</th><th>طرف حساب</th><th>توضیحات</th><th>شماره پیگیری</th></tr>
        <?php foreach (array_reverse($transactions) as $t): ?>
        <tr>
            <td><?= e($t['date']) ?></td>
            <td><span class="badge badge-<?= $t['type']==='receipt'?'success':'danger' ?>"><?= $t['type']==='receipt'?'دریافت':'پرداخت' ?></span></td>
            <td><?= format_money($t['amount']) ?></td>
            <td><?= e($t['party_type']) ?> #<?= $t['party_id'] ?></td>
            <td><?= e($t['description']) ?></td>
            <td><?= e($t['reference']) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($transactions)): ?><tr><td colspan="6" class="text-center">عملیاتی ثبت نشده است</td></tr><?php endif; ?>
    </table>
</div>
<script>
document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        history.replaceState(null, '', '#' + this.dataset.action);
        showPage(this.dataset.action);
    });
});
</script>
<?php
}

function render_new_transaction() {
    $accounts = array_filter(db_read(DB_ACCOUNTS), fn($a) => $a['type'] === 'account');
    $customers = db_read(DB_CUSTOMERS);
    $suppliers = db_read(DB_SUPPLIERS);
    info_box('ثبت دریافت / پرداخت', 'نوع عملیات، مبلغ و حساب مربوطه را مشخص کنید. در صورتی که عملیات مربوط به مشتری یا تأمین‌کننده خاصی است، طرف حساب را انتخاب کنید تا مانده حساب آن‌ها به صورت خودکار به‌روزرسانی شود.');
?>
<div class="card">
    <div class="card-title"><span>ثبت دریافت / پرداخت</span></div>
    <form method="POST" action="?action=save_transaction">
        <div class="form-grid">
            <div class="form-group"><label>نوع عملیات</label><select name="type" required><option value="receipt">دریافت</option><option value="payment">پرداخت</option></select></div>
            <div class="form-group"><label>تاریخ</label><input type="text" name="date" value="<?= today() ?>" placeholder="1403/01/01" required></div>
            <div class="form-group"><label>مبلغ</label><input type="number" name="amount" step="0.01" required></div>
            <div class="form-group"><label>حساب</label><select name="account_id" required><?php foreach ($accounts as $a): ?><option value="<?= $a['id'] ?>"><?= e($a['code']) ?> - <?= e($a['title']) ?></option><?php endforeach; ?></select></div>
            <div class="form-group"><label>نوع طرف حساب</label><select name="party_type"><option value="">-- ندارد --</option><option value="customer">مشتری</option><option value="supplier">تأمین‌کننده</option></select></div>
            <div class="form-group"><label>طرف حساب</label><select name="party_id"><option value="0">--</option><?php foreach ($customers as $c): ?><option value="<?= $c['id'] ?>">[مشتری] <?= e($c['name']) ?></option><?php endforeach; ?><?php foreach ($suppliers as $s): ?><option value="<?= $s['id'] ?>">[تأمین‌کننده] <?= e($s['name']) ?></option><?php endforeach; ?></select></div>
            <div class="form-group"><label>شماره پیگیری</label><input type="text" name="reference"></div>
            <div class="form-group"><label>توضیحات</label><input type="text" name="description"></div>
        </div>
        <button type="submit" class="btn btn-primary">ثبت</button>
        <a data-action="transactions" class="btn nav-link">انصراف</a>
    </form>
</div>
<script>
document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        history.replaceState(null, '', '#' + this.dataset.action);
        showPage(this.dataset.action);
    });
});
</script>
<?php
}

function render_checks() {
    $checks = db_read(DB_CHECKS);
    info_box('مدیریت چک‌ها', 'در این بخش چک‌های دریافتی و پرداختی مدیریت می‌شوند. با استفاده از دکمه «تغییر وضعیت» می‌توانید وضعیت هر چک را به «در جریان»، «وصول شده» یا «برگشتی» تغییر دهید.');
?>
<div class="card">
    <div class="card-title">
        <span>مدیریت چک‌ها</span>
        <div class="card-title-actions">
            <a data-action="new_check" class="btn btn-primary btn-sm nav-link">ثبت چک جدید</a>
            <form method="POST" action="?action=reset_section" class="inline-form" onsubmit="return confirm('آیا از حذف تمام چک‌ها مطمئن هستید؟')">
                <input type="hidden" name="section" value="checks">
                <button type="submit" class="btn btn-danger btn-sm">ریست چک‌ها</button>
            </form>
        </div>
    </div>
    <table>
        <tr><th>شماره چک</th><th>نوع</th><th>بانک</th><th>مبلغ</th><th>سررسید</th><th>صادر کننده</th><th>تغییر وضعیت</th></tr>
        <?php foreach (array_reverse($checks) as $c): ?>
        <tr>
            <td><?= e($c['check_no']) ?></td>
            <td><span class="badge badge-<?= $c['type']==='received'?'success':'danger' ?>"><?= $c['type']==='received'?'دریافتی':'پرداختی' ?></span></td>
            <td><?= e($c['bank']) ?></td>
            <td><?= format_money($c['amount']) ?></td>
            <td><?= e($c['due_date']) ?></td>
            <td><?= e($c['issuer']) ?></td>
            <td>
                <form method="POST" action="?action=change_check_status" class="inline-form">
                    <input type="hidden" name="check_id" value="<?= $c['id'] ?>">
                    <select name="new_status" class="status-select" onchange="this.form.submit()">
                        <option value="pending" <?= $c['status']==='pending'?'selected':'' ?>>در جریان</option>
                        <option value="cleared" <?= $c['status']==='cleared'?'selected':'' ?>>وصول شده</option>
                        <option value="bounced" <?= $c['status']==='bounced'?'selected':'' ?>>برگشتی</option>
                    </select>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($checks)): ?><tr><td colspan="7" class="text-center">چکی ثبت نشده است</td></tr><?php endif; ?>
    </table>
</div>
<script>
document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        history.replaceState(null, '', '#' + this.dataset.action);
        showPage(this.dataset.action);
    });
});
</script>
<?php
}

function render_new_check() {
    $accounts = array_filter(db_read(DB_ACCOUNTS), fn($a) => $a['type'] === 'account');
    info_box('ثبت چک جدید', 'نوع چک (دریافتی یا پرداختی)، شماره چک، نام بانک، مبلغ و تاریخ سررسید را وارد کنید.');
?>
<div class="card">
    <div class="card-title"><span>ثبت چک جدید</span></div>
    <form method="POST" action="?action=save_check">
        <div class="form-grid">
            <div class="form-group"><label>نوع چک</label><select name="type" required><option value="received">دریافتی</option><option value="paid">پرداختی</option></select></div>
            <div class="form-group"><label>شماره چک</label><input type="text" name="check_no" required></div>
            <div class="form-group"><label>بانک</label><input type="text" name="bank" required></div>
            <div class="form-group"><label>مبلغ</label><input type="number" name="amount" step="0.01" required></div>
            <div class="form-group"><label>تاریخ سررسید</label><input type="text" name="due_date" placeholder="1403/01/01" required></div>
            <div class="form-group"><label>صادر کننده / در وجه</label><input type="text" name="issuer"></div>
            <div class="form-group"><label>حساب مربوطه</label><select name="account_id" required><?php foreach ($accounts as $a): ?><option value="<?= $a['id'] ?>"><?= e($a['code']) ?> - <?= e($a['title']) ?></option><?php endforeach; ?></select></div>
        </div>
        <button type="submit" class="btn btn-primary">ثبت</button>
        <a data-action="checks" class="btn nav-link">انصراف</a>
    </form>
</div>
<script>
document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        history.replaceState(null, '', '#' + this.dataset.action);
        showPage(this.dataset.action);
    });
});
</script>
<?php
}

function render_cashboxes() {
    $cashboxes = db_read(DB_CASHBOXES);
    info_box('صندوق‌ها', 'صندوق‌های نقدی شرکت در این بخش مدیریت می‌شوند. هر صندوق می‌تواند موجودی جداگانه‌ای داشته باشد و در عملیات دریافت و پرداخت قابل انتخاب است.');
?>
<div class="card">
    <div class="card-title">
        <span>صندوق‌ها</span>
        <div class="card-title-actions">
            <a data-action="new_cashbox" class="btn btn-primary btn-sm nav-link">صندوق جدید</a>
            <form method="POST" action="?action=reset_section" class="inline-form" onsubmit="return confirm('آیا از ریست صندوق‌ها مطمئن هستید؟')">
                <input type="hidden" name="section" value="cashboxes">
                <button type="submit" class="btn btn-danger btn-sm">ریست</button>
            </form>
        </div>
    </div>
    <table>
        <tr><th>نام صندوق</th><th>موجودی</th></tr>
        <?php foreach ($cashboxes as $c): ?>
        <tr><td><?= e($c['name']) ?></td><td><?= format_money($c['balance']) ?></td></tr>
        <?php endforeach; ?>
    </table>
</div>
<script>
document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        history.replaceState(null, '', '#' + this.dataset.action);
        showPage(this.dataset.action);
    });
});
</script>
<?php
}

function render_new_cashbox() {
    info_box('ایجاد صندوق جدید', 'نام صندوق را وارد کنید. موجودی اولیه صفر است و با ثبت عملیات دریافت و پرداخت تغییر می‌کند.');
?>
<div class="card">
    <div class="card-title"><span>ایجاد صندوق جدید</span></div>
    <form method="POST" action="?action=save_cashbox">
        <div class="form-group"><label>نام صندوق</label><input type="text" name="name" required></div>
        <button type="submit" class="btn btn-primary">ذخیره</button>
        <a data-action="cashboxes" class="btn nav-link">انصراف</a>
    </form>
</div>
<script>
document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        history.replaceState(null, '', '#' + this.dataset.action);
        showPage(this.dataset.action);
    });
});
</script>
<?php
}

function render_banks() {
    $banks = db_read(DB_BANKS);
    info_box('بانک‌ها', 'حساب‌های بانکی شرکت در این بخش مدیریت می‌شوند. هر حساب شامل نام بانک، شماره حساب، شعبه و موجودی است.');
?>
<div class="card">
    <div class="card-title">
        <span>بانک‌ها</span>
        <div class="card-title-actions">
            <a data-action="new_bank" class="btn btn-primary btn-sm nav-link">بانک جدید</a>
            <form method="POST" action="?action=reset_section" class="inline-form" onsubmit="return confirm('آیا از ریست بانک‌ها مطمئن هستید؟')">
                <input type="hidden" name="section" value="banks">
                <button type="submit" class="btn btn-danger btn-sm">ریست</button>
            </form>
        </div>
    </div>
    <table>
        <tr><th>نام بانک</th><th>شماره حساب</th><th>شعبه</th><th>موجودی</th></tr>
        <?php foreach ($banks as $b): ?>
        <tr><td><?= e($b['name']) ?></td><td><?= e($b['account_no']) ?></td><td><?= e($b['branch']) ?></td><td><?= format_money($b['balance']) ?></td></tr>
        <?php endforeach; ?>
    </table>
</div>
<script>
document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        history.replaceState(null, '', '#' + this.dataset.action);
        showPage(this.dataset.action);
    });
});
</script>
<?php
}

function render_new_bank() {
    info_box('ثبت بانک جدید', 'نام بانک، شماره حساب و شعبه را وارد کنید. موجودی اولیه صفر است.');
?>
<div class="card">
    <div class="card-title"><span>ثبت بانک جدید</span></div>
    <form method="POST" action="?action=save_bank">
        <div class="form-grid">
            <div class="form-group"><label>نام بانک</label><input type="text" name="name" required></div>
            <div class="form-group"><label>شماره حساب</label><input type="text" name="account_no" required></div>
            <div class="form-group"><label>شعبه</label><input type="text" name="branch"></div>
        </div>
        <button type="submit" class="btn btn-primary">ذخیره</button>
        <a data-action="banks" class="btn nav-link">انصراف</a>
    </form>
</div>
<script>
document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        history.replaceState(null, '', '#' + this.dataset.action);
        showPage(this.dataset.action);
    });
});
</script>
<?php
}

function render_petty_cash() {
    $petty = db_read(DB_PETTY_CASH);
    info_box('تنخواه', 'تنخواه مبلغی است که برای هزینه‌های کوچک در اختیار یک شخص قرار می‌گیرد. هر تنخواه شامل مبلغ اولیه، تحویل‌دار و مانده فعلی است.');
?>
<div class="card">
    <div class="card-title">
        <span>تنخواه‌ها</span>
        <div class="card-title-actions">
            <a data-action="new_petty_cash" class="btn btn-primary btn-sm nav-link">تنخواه جدید</a>
            <form method="POST" action="?action=reset_section" class="inline-form" onsubmit="return confirm('آیا از ریست تنخواه‌ها مطمئن هستید؟')">
                <input type="hidden" name="section" value="petty_cash">
                <button type="submit" class="btn btn-danger btn-sm">ریست</button>
            </form>
        </div>
    </div>
    <table>
        <tr><th>عنوان</th><th>تحویل‌دار</th><th>مبلغ اولیه</th><th>مانده</th></tr>
        <?php foreach ($petty as $p): ?>
        <tr><td><?= e($p['name']) ?></td><td><?= e($p['holder']) ?></td><td><?= format_money($p['amount']) ?></td><td><?= format_money($p['balance']) ?></td></tr>
        <?php endforeach; ?>
    </table>
</div>
<script>
document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        history.replaceState(null, '', '#' + this.dataset.action);
        showPage(this.dataset.action);
    });
});
</script>
<?php
}

function render_new_petty_cash() {
    info_box('ایجاد تنخواه جدید', 'عنوان تنخواه، نام تحویل‌دار و مبلغ اولیه را وارد کنید.');
?>
<div class="card">
    <div class="card-title"><span>ایجاد تنخواه جدید</span></div>
    <form method="POST" action="?action=save_petty_cash">
        <div class="form-grid">
            <div class="form-group"><label>عنوان تنخواه</label><input type="text" name="name" required></div>
            <div class="form-group"><label>تحویل‌دار</label><input type="text" name="holder" required></div>
            <div class="form-group"><label>مبلغ</label><input type="number" name="amount" step="0.01" required></div>
        </div>
        <button type="submit" class="btn btn-primary">ذخیره</button>
        <a data-action="petty_cash" class="btn nav-link">انصراف</a>
    </form>
</div>
<script>
document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        history.replaceState(null, '', '#' + this.dataset.action);
        showPage(this.dataset.action);
    });
});
</script>
<?php
}

function render_customers() {
    $customers = db_read(DB_CUSTOMERS);
    info_box('مشتریان', 'اطلاعات مشتریان شرکت در این بخش مدیریت می‌شود. مشتری شخصی است که از شرکت کالا یا خدمات خریداری می‌کند. مانده حساب مشتری نشان‌دهنده مبلغی است که باید به شرکت پرداخت کند.');
?>
<div class="card">
    <div class="card-title">
        <span>مشتریان</span>
        <div class="card-title-actions">
            <a data-action="new_customer" class="btn btn-primary btn-sm nav-link">مشتری جدید</a>
            <form method="POST" action="?action=reset_section" class="inline-form" onsubmit="return confirm('آیا از حذف تمام مشتریان مطمئن هستید؟')">
                <input type="hidden" name="section" value="customers">
                <button type="submit" class="btn btn-danger btn-sm">ریست</button>
            </form>
        </div>
    </div>
    <table>
        <tr><th>کد</th><th>نام</th><th>تلفن</th><th>کد ملی</th><th>مانده حساب</th></tr>
        <?php foreach ($customers as $c): ?>
        <tr><td><?= e($c['code']) ?></td><td><?= e($c['name']) ?></td><td><?= e($c['phone']) ?></td><td><?= e($c['national_id']) ?></td><td><?= format_money($c['balance']) ?></td></tr>
        <?php endforeach; ?>
        <?php if (empty($customers)): ?><tr><td colspan="5" class="text-center">مشتری‌ای ثبت نشده است</td></tr><?php endif; ?>
    </table>
</div>
<script>
document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        history.replaceState(null, '', '#' + this.dataset.action);
        showPage(this.dataset.action);
    });
});
</script>
<?php
}

function render_new_customer() {
    info_box('ثبت مشتری جدید', 'کد یکتا، نام و سایر اطلاعات مشتری را وارد کنید.');
?>
<div class="card">
    <div class="card-title"><span>ثبت مشتری جدید</span></div>
    <form method="POST" action="?action=save_customer">
        <div class="form-grid">
            <div class="form-group"><label>کد مشتری</label><input type="text" name="code" required></div>
            <div class="form-group"><label>نام</label><input type="text" name="name" required></div>
            <div class="form-group"><label>تلفن</label><input type="text" name="phone"></div>
            <div class="form-group"><label>کد ملی</label><input type="text" name="national_id"></div>
            <div class="form-group"><label>آدرس</label><input type="text" name="address"></div>
        </div>
        <button type="submit" class="btn btn-primary">ذخیره</button>
        <a data-action="customers" class="btn nav-link">انصراف</a>
    </form>
</div>
<script>
document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        history.replaceState(null, '', '#' + this.dataset.action);
        showPage(this.dataset.action);
    });
});
</script>
<?php
}

function render_suppliers() {
    $suppliers = db_read(DB_SUPPLIERS);
    info_box('تأمین‌کنندگان', 'اطلاعات تأمین‌کنندگان شرکت در این بخش مدیریت می‌شود. تأمین‌کننده شخصی است که شرکت از او کالا یا خدمات خریداری می‌کند.');
?>
<div class="card">
    <div class="card-title">
        <span>تأمین‌کنندگان</span>
        <div class="card-title-actions">
            <a data-action="new_supplier" class="btn btn-primary btn-sm nav-link">تأمین‌کننده جدید</a>
            <form method="POST" action="?action=reset_section" class="inline-form" onsubmit="return confirm('آیا از حذف تمام تأمین‌کنندگان مطمئن هستید؟')">
                <input type="hidden" name="section" value="suppliers">
                <button type="submit" class="btn btn-danger btn-sm">ریست</button>
            </form>
        </div>
    </div>
    <table>
        <tr><th>کد</th><th>نام</th><th>تلفن</th><th>کد ملی</th><th>مانده حساب</th></tr>
        <?php foreach ($suppliers as $s): ?>
        <tr><td><?= e($s['code']) ?></td><td><?= e($s['name']) ?></td><td><?= e($s['phone']) ?></td><td><?= e($s['national_id']) ?></td><td><?= format_money($s['balance']) ?></td></tr>
        <?php endforeach; ?>
        <?php if (empty($suppliers)): ?><tr><td colspan="5" class="text-center">تأمین‌کننده‌ای ثبت نشده است</td></tr><?php endif; ?>
    </table>
</div>
<script>
document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        history.replaceState(null, '', '#' + this.dataset.action);
        showPage(this.dataset.action);
    });
});
</script>
<?php
}

function render_new_supplier() {
    info_box('ثبت تأمین‌کننده جدید', 'کد یکتا، نام و سایر اطلاعات تأمین‌کننده را وارد کنید.');
?>
<div class="card">
    <div class="card-title"><span>ثبت تأمین‌کننده جدید</span></div>
    <form method="POST" action="?action=save_supplier">
        <div class="form-grid">
            <div class="form-group"><label>کد</label><input type="text" name="code" required></div>
            <div class="form-group"><label>نام</label><input type="text" name="name" required></div>
            <div class="form-group"><label>تلفن</label><input type="text" name="phone"></div>
            <div class="form-group"><label>کد ملی</label><input type="text" name="national_id"></div>
            <div class="form-group"><label>آدرس</label><input type="text" name="address"></div>
        </div>
        <button type="submit" class="btn btn-primary">ذخیره</button>
        <a data-action="suppliers" class="btn nav-link">انصراف</a>
    </form>
</div>
<script>
document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        history.replaceState(null, '', '#' + this.dataset.action);
        showPage(this.dataset.action);
    });
});
</script>
<?php
}

function render_purchases() {
    $purchases = db_read(DB_PURCHASES);
    $suppliers = db_read(DB_SUPPLIERS);
    info_box('فاکتورهای خرید', 'فاکتورهای خرید ثبت شده در این بخش نمایش داده می‌شود. با ثبت هر فاکتور، موجودی انبار افزایش یافته، مانده حساب تأمین‌کننده بدهکار می‌شود و سند حسابداری به صورت خودکار صادر می‌گردد.');
?>
<div class="card">
    <div class="card-title">
        <span>فاکتورهای خرید</span>
        <div class="card-title-actions">
            <a data-action="new_purchase" class="btn btn-primary btn-sm nav-link">فاکتور جدید</a>
            <form method="POST" action="?action=reset_section" class="inline-form" onsubmit="return confirm('آیا از حذف تمام فاکتورهای خرید مطمئن هستید؟')">
                <input type="hidden" name="section" value="purchases">
                <button type="submit" class="btn btn-danger btn-sm">ریست</button>
            </form>
        </div>
    </div>
    <table>
        <tr><th>شماره</th><th>تاریخ</th><th>تأمین‌کننده</th><th>مبلغ کل</th></tr>
        <?php foreach (array_reverse($purchases) as $p):
            $sn = ''; foreach ($suppliers as $s) if ($s['id'] == $p['supplier_id']) $sn = $s['name'];
        ?>
        <tr><td><?= $p['invoice_no'] ?></td><td><?= e($p['date']) ?></td><td><?= e($sn) ?></td><td><?= format_money($p['total']) ?></td></tr>
        <?php endforeach; ?>
        <?php if (empty($purchases)): ?><tr><td colspan="4" class="text-center">فاکتوری ثبت نشده است</td></tr><?php endif; ?>
    </table>
</div>
<script>
document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        history.replaceState(null, '', '#' + this.dataset.action);
        showPage(this.dataset.action);
    });
});
</script>
<?php
}

function render_new_purchase() {
    $suppliers = db_read(DB_SUPPLIERS);
    info_box('ثبت فاکتور خرید', 'تأمین‌کننده و اقلام کالا را مشخص کنید. با ثبت فاکتور، موجودی انبار به‌روزرسانی شده، مانده حساب تأمین‌کننده افزایش یافته و سند حسابداری به صورت خودکار صادر می‌شود.');
?>
<div class="card">
    <div class="card-title"><span>ثبت فاکتور خرید</span></div>
    <form method="POST" action="?action=save_purchase">
        <div class="form-grid">
            <div class="form-group"><label>تاریخ</label><input type="text" name="date" value="<?= today() ?>" required></div>
            <div class="form-group"><label>تأمین‌کننده</label><select name="supplier_id" required><?php foreach ($suppliers as $s): ?><option value="<?= $s['id'] ?>"><?= e($s['name']) ?></option><?php endforeach; ?></select></div>
        </div>
        <div style="margin: 18px 0 10px; font-size: 14px; color: var(--text-light); font-weight: 500">اقلام فاکتور</div>
        <table id="purchaseItems">
            <thead><tr><th>نام کالا</th><th>تعداد</th><th>قیمت واحد</th><th>مبلغ</th><th></th></tr></thead>
            <tbody>
                <tr>
                    <td><input type="text" name="product_name[]" required style="width:100%;padding:10px;border:1px solid var(--border);border-radius:4px"></td>
                    <td><input type="number" name="qty[]" step="0.01" value="1" required class="qty" style="width:100%;padding:10px;border:1px solid var(--border);border-radius:4px"></td>
                    <td><input type="number" name="price[]" step="0.01" value="0" required class="price" style="width:100%;padding:10px;border:1px solid var(--border);border-radius:4px"></td>
                    <td class="amount">0</td>
                    <td><button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()">×</button></td>
                </tr>
            </tbody>
            <tfoot>
                <tr><td colspan="3"><button type="button" class="btn btn-success btn-sm" onclick="addPurchaseRow()">+ سطر</button></td><th>جمع کل:</th><td id="purchaseTotal">0</td></tr>
            </tfoot>
        </table>
        <div class="form-group mt-2"><label>توضیحات</label><input type="text" name="description"></div>
        <button type="submit" class="btn btn-primary">ثبت و صدور سند</button>
        <a data-action="purchases" class="btn nav-link">انصراف</a>
    </form>
</div>
<script>
function addPurchaseRow() {
    const tbody = document.querySelector('#purchaseItems tbody');
    const row = tbody.querySelector('tr').cloneNode(true);
    row.querySelectorAll('input').forEach(i => i.value = i.classList.contains('qty') ? 1 : (i.classList.contains('price') ? 0 : ''));
    row.querySelector('.amount').textContent = '0';
    tbody.appendChild(row);
}
document.addEventListener('input', function(e) {
    if (e.target.classList.contains('qty') || e.target.classList.contains('price')) {
        const row = e.target.closest('tr');
        const q = parseFloat(row.querySelector('.qty').value) || 0;
        const p = parseFloat(row.querySelector('.price').value) || 0;
        row.querySelector('.amount').textContent = (q * p).toLocaleString();
        let total = 0;
        document.querySelectorAll('#purchaseItems tbody tr').forEach(r => {
            const qv = parseFloat(r.querySelector('.qty').value) || 0;
            const pv = parseFloat(r.querySelector('.price').value) || 0;
            total += qv * pv;
        });
        document.getElementById('purchaseTotal').textContent = total.toLocaleString();
    }
});
document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        history.replaceState(null, '', '#' + this.dataset.action);
        showPage(this.dataset.action);
    });
});
</script>
<?php
}

function render_sales() {
    $sales = db_read(DB_SALES);
    $customers = db_read(DB_CUSTOMERS);
    info_box('فاکتورهای فروش', 'فاکتورهای فروش ثبت شده در این بخش نمایش داده می‌شود. با ثبت هر فاکتور، موجودی انبار کاهش یافته، مانده حساب مشتری بدهکار می‌شود و سند حسابداری به صورت خودکار صادر می‌گردد.');
?>
<div class="card">
    <div class="card-title">
        <span>فاکتورهای فروش</span>
        <div class="card-title-actions">
            <a data-action="new_sale" class="btn btn-primary btn-sm nav-link">فاکتور جدید</a>
            <form method="POST" action="?action=reset_section" class="inline-form" onsubmit="return confirm('آیا از حذف تمام فاکتورهای فروش مطمئن هستید؟')">
                <input type="hidden" name="section" value="sales">
                <button type="submit" class="btn btn-danger btn-sm">ریست</button>
            </form>
        </div>
    </div>
    <table>
        <tr><th>شماره</th><th>تاریخ</th><th>مشتری</th><th>مبلغ کل</th></tr>
        <?php foreach (array_reverse($sales) as $s):
            $cn = ''; foreach ($customers as $c) if ($c['id'] == $s['customer_id']) $cn = $c['name'];
        ?>
        <tr><td><?= $s['invoice_no'] ?></td><td><?= e($s['date']) ?></td><td><?= e($cn) ?></td><td><?= format_money($s['total']) ?></td></tr>
        <?php endforeach; ?>
        <?php if (empty($sales)): ?><tr><td colspan="4" class="text-center">فاکتوری ثبت نشده است</td></tr><?php endif; ?>
    </table>
</div>
<script>
document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        history.replaceState(null, '', '#' + this.dataset.action);
        showPage(this.dataset.action);
    });
});
</script>
<?php
}

function render_new_sale() {
    $customers = db_read(DB_CUSTOMERS);
    info_box('ثبت فاکتور فروش', 'مشتری و اقلام کالا را مشخص کنید. با ثبت فاکتور، موجودی انبار کاهش یافته، مانده حساب مشتری افزایش یافته و سند حسابداری به صورت خودکار صادر می‌شود.');
?>
<div class="card">
    <div class="card-title"><span>ثبت فاکتور فروش</span></div>
    <form method="POST" action="?action=save_sale">
        <div class="form-grid">
            <div class="form-group"><label>تاریخ</label><input type="text" name="date" value="<?= today() ?>" required></div>
            <div class="form-group"><label>مشتری</label><select name="customer_id" required><?php foreach ($customers as $c): ?><option value="<?= $c['id'] ?>"><?= e($c['name']) ?></option><?php endforeach; ?></select></div>
        </div>
        <div style="margin: 18px 0 10px; font-size: 14px; color: var(--text-light); font-weight: 500">اقلام فاکتور</div>
        <table id="saleItems">
            <thead><tr><th>نام کالا</th><th>تعداد</th><th>قیمت واحد</th><th>مبلغ</th><th></th></tr></thead>
            <tbody>
                <tr>
                    <td><input type="text" name="product_name[]" required style="width:100%;padding:10px;border:1px solid var(--border);border-radius:4px"></td>
                    <td><input type="number" name="qty[]" step="0.01" value="1" required class="qty" style="width:100%;padding:10px;border:1px solid var(--border);border-radius:4px"></td>
                    <td><input type="number" name="price[]" step="0.01" value="0" required class="price" style="width:100%;padding:10px;border:1px solid var(--border);border-radius:4px"></td>
                    <td class="amount">0</td>
                    <td><button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()">×</button></td>
                </tr>
            </tbody>
            <tfoot>
                <tr><td colspan="3"><button type="button" class="btn btn-success btn-sm" onclick="addSaleRow()">+ سطر</button></td><th>جمع کل:</th><td id="saleTotal">0</td></tr>
            </tfoot>
        </table>
        <div class="form-group mt-2"><label>توضیحات</label><input type="text" name="description"></div>
        <button type="submit" class="btn btn-primary">ثبت و صدور سند</button>
        <a data-action="sales" class="btn nav-link">انصراف</a>
    </form>
</div>
<script>
function addSaleRow() {
    const tbody = document.querySelector('#saleItems tbody');
    const row = tbody.querySelector('tr').cloneNode(true);
    row.querySelectorAll('input').forEach(i => i.value = i.classList.contains('qty') ? 1 : (i.classList.contains('price') ? 0 : ''));
    row.querySelector('.amount').textContent = '0';
    tbody.appendChild(row);
}
document.addEventListener('input', function(e) {
    if (e.target.classList.contains('qty') || e.target.classList.contains('price')) {
        const row = e.target.closest('tr');
        const q = parseFloat(row.querySelector('.qty').value) || 0;
        const p = parseFloat(row.querySelector('.price').value) || 0;
        row.querySelector('.amount').textContent = (q * p).toLocaleString();
        let total = 0;
        document.querySelectorAll('#saleItems tbody tr').forEach(r => {
            const qv = parseFloat(r.querySelector('.qty').value) || 0;
            const pv = parseFloat(r.querySelector('.price').value) || 0;
            total += qv * pv;
        });
        document.getElementById('saleTotal').textContent = total.toLocaleString();
    }
});
document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        history.replaceState(null, '', '#' + this.dataset.action);
        showPage(this.dataset.action);
    });
});
</script>
<?php
}

function render_inventory() {
    $inventory = db_read(DB_INVENTORY);
    info_box('موجودی انبار', 'موجودی کالاهای انبار در این بخش نمایش داده می‌شود. با ثبت فاکتور خرید، موجودی افزایش و با ثبت فاکتور فروش، موجودی کاهش می‌یابد.');
?>
<div class="card">
    <div class="card-title">
        <span>موجودی انبار</span>
        <div class="card-title-actions">
            <form method="POST" action="?action=reset_section" class="inline-form" onsubmit="return confirm('آیا از ریست انبار مطمئن هستید؟')">
                <input type="hidden" name="section" value="inventory">
                <button type="submit" class="btn btn-danger btn-sm">ریست انبار</button>
            </form>
        </div>
    </div>
    <table>
        <tr><th>نام کالا</th><th>موجودی</th><th>میانگین قیمت</th></tr>
        <?php foreach ($inventory as $i): ?>
        <tr><td><?= e($i['name']) ?></td><td><?= format_money($i['qty']) ?></td><td><?= format_money($i['avg_price']) ?></td></tr>
        <?php endforeach; ?>
        <?php if (empty($inventory)): ?><tr><td colspan="3" class="text-center">کالایی در انبار موجود نیست</td></tr><?php endif; ?>
    </table>
</div>
<?php
}

function render_expenses() {
    $expenses = db_read(DB_EXPENSES);
    $accounts = db_read(DB_ACCOUNTS);
    info_box('هزینه‌ها', 'هزینه‌های شرکت در این بخش ثبت و مدیریت می‌شود. هزینه‌ها شامل حقوق، اجاره، قبوض و سایر هزینه‌های عملیاتی هستند.');
?>
<div class="card">
    <div class="card-title">
        <span>هزینه‌ها</span>
        <div class="card-title-actions">
            <a data-action="new_expense" class="btn btn-primary btn-sm nav-link">ثبت هزینه</a>
            <form method="POST" action="?action=reset_section" class="inline-form" onsubmit="return confirm('آیا از حذف تمام هزینه‌ها مطمئن هستید؟')">
                <input type="hidden" name="section" value="expenses">
                <button type="submit" class="btn btn-danger btn-sm">ریست</button>
            </form>
        </div>
    </div>
    <table>
        <tr><th>تاریخ</th><th>سرفصل</th><th>مبلغ</th><th>توضیحات</th></tr>
        <?php foreach (array_reverse($expenses) as $ex):
            $at = ''; foreach ($accounts as $a) if ($a['id'] == $ex['account_id']) $at = $a['title'];
        ?>
        <tr><td><?= e($ex['date']) ?></td><td><?= e($at) ?></td><td><?= format_money($ex['amount']) ?></td><td><?= e($ex['description']) ?></td></tr>
        <?php endforeach; ?>
        <?php if (empty($expenses)): ?><tr><td colspan="4" class="text-center">هزینه‌ای ثبت نشده است</td></tr><?php endif; ?>
    </table>
</div>
<script>
document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        history.replaceState(null, '', '#' + this.dataset.action);
        showPage(this.dataset.action);
    });
});
</script>
<?php
}

function render_new_expense() {
    $accounts = array_filter(db_read(DB_ACCOUNTS), fn($a) => $a['type'] === 'account' && str_starts_with($a['code'], '5'));
    info_box('ثبت هزینه', 'سرفصل مربوطه، مبلغ و توضیحات را وارد کنید.');
?>
<div class="card">
    <div class="card-title"><span>ثبت هزینه</span></div>
    <form method="POST" action="?action=save_expense">
        <div class="form-grid">
            <div class="form-group"><label>تاریخ</label><input type="text" name="date" value="<?= today() ?>" required></div>
            <div class="form-group"><label>سرفصل هزینه</label><select name="account_id" required><?php foreach ($accounts as $a): ?><option value="<?= $a['id'] ?>"><?= e($a['code']) ?> - <?= e($a['title']) ?></option><?php endforeach; ?></select></div>
            <div class="form-group"><label>مبلغ</label><input type="number" name="amount" step="0.01" required></div>
            <div class="form-group"><label>توضیحات</label><input type="text" name="description"></div>
        </div>
        <button type="submit" class="btn btn-primary">ثبت</button>
        <a data-action="expenses" class="btn nav-link">انصراف</a>
    </form>
</div>
<script>
document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        history.replaceState(null, '', '#' + this.dataset.action);
        showPage(this.dataset.action);
    });
});
</script>
<?php
}

function render_income() {
    $income = db_read(DB_INCOME);
    $accounts = db_read(DB_ACCOUNTS);
    info_box('درآمدها', 'درآمدهای شرکت در این بخش ثبت و مدیریت می‌شود. درآمدها شامل فروش کالا، ارائه خدمات و سایر درآمدها هستند.');
?>
<div class="card">
    <div class="card-title">
        <span>درآمدها</span>
        <div class="card-title-actions">
            <a data-action="new_income" class="btn btn-primary btn-sm nav-link">ثبت درآمد</a>
            <form method="POST" action="?action=reset_section" class="inline-form" onsubmit="return confirm('آیا از حذف تمام درآمدها مطمئن هستید؟')">
                <input type="hidden" name="section" value="income">
                <button type="submit" class="btn btn-danger btn-sm">ریست</button>
            </form>
        </div>
    </div>
    <table>
        <tr><th>تاریخ</th><th>سرفصل</th><th>مبلغ</th><th>توضیحات</th></tr>
        <?php foreach (array_reverse($income) as $i):
            $at = ''; foreach ($accounts as $a) if ($a['id'] == $i['account_id']) $at = $a['title'];
        ?>
        <tr><td><?= e($i['date']) ?></td><td><?= e($at) ?></td><td><?= format_money($i['amount']) ?></td><td><?= e($i['description']) ?></td></tr>
        <?php endforeach; ?>
        <?php if (empty($income)): ?><tr><td colspan="4" class="text-center">درآمدی ثبت نشده است</td></tr><?php endif; ?>
    </table>
</div>
<script>
document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        history.replaceState(null, '', '#' + this.dataset.action);
        showPage(this.dataset.action);
    });
});
</script>
<?php
}

function render_new_income() {
    $accounts = array_filter(db_read(DB_ACCOUNTS), fn($a) => $a['type'] === 'account' && str_starts_with($a['code'], '4'));
    info_box('ثبت درآمد', 'سرفصل مربوطه، مبلغ و توضیحات را وارد کنید.');
?>
<div class="card">
    <div class="card-title"><span>ثبت درآمد</span></div>
    <form method="POST" action="?action=save_income">
        <div class="form-grid">
            <div class="form-group"><label>تاریخ</label><input type="text" name="date" value="<?= today() ?>" required></div>
            <div class="form-group"><label>سرفصل درآمد</label><select name="account_id" required><?php foreach ($accounts as $a): ?><option value="<?= $a['id'] ?>"><?= e($a['code']) ?> - <?= e($a['title']) ?></option><?php endforeach; ?></select></div>
            <div class="form-group"><label>مبلغ</label><input type="number" name="amount" step="0.01" required></div>
            <div class="form-group"><label>توضیحات</label><input type="text" name="description"></div>
        </div>
        <button type="submit" class="btn btn-primary">ثبت</button>
        <a data-action="income" class="btn nav-link">انصراف</a>
    </form>
</div>
<script>
document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        history.replaceState(null, '', '#' + this.dataset.action);
        showPage(this.dataset.action);
    });
});
</script>
<?php
}

function render_trial_balance() {
    $accounts = db_read(DB_ACCOUNTS);
    $vouchers = db_read(DB_VOUCHERS);
    $leaf_accounts = array_filter($accounts, fn($a) => $a['type'] === 'account');
    $balances = [];
    foreach ($leaf_accounts as $a) $balances[$a['id']] = ['debit' => 0, 'credit' => 0, 'code' => $a['code'], 'title' => $a['title']];
    foreach ($vouchers as $v) foreach ($v['items'] as $item) if (isset($balances[$item['account_id']])) { $balances[$item['account_id']]['debit'] += $item['debit']; $balances[$item['account_id']]['credit'] += $item['credit']; }
    info_box('تراز آزمایشی', 'تراز آزمایشی گزارش جمع بدهکار و بستانکار تمامی حساب‌ها را نمایش می‌دهد. جمع کل بدهکار باید با جمع کل بستانکار برابر باشد. این گزارش برای کنترل صحت ثبت اسناد استفاده می‌شود.');
?>
<div class="card">
    <div class="card-title"><span>تراز آزمایشی</span><button onclick="window.print()" class="btn btn-sm">چاپ</button></div>
    <table>
        <tr><th>کد</th><th>عنوان</th><th>بدهکار</th><th>بستانکار</th></tr>
        <?php
        $td = 0; $tc = 0;
        foreach ($balances as $b):
            if ($b['debit'] == 0 && $b['credit'] == 0) continue;
            $td += $b['debit']; $tc += $b['credit'];
        ?>
        <tr><td><?= e($b['code']) ?></td><td><?= e($b['title']) ?></td><td><?= format_money($b['debit']) ?></td><td><?= format_money($b['credit']) ?></td></tr>
        <?php endforeach; ?>
        <tr style="background: var(--bg); font-weight: bold"><td colspan="2">جمع کل</td><td><?= format_money($td) ?></td><td><?= format_money($tc) ?></td></tr>
    </table>
</div>
<?php
}

function render_balance_sheet() {
    $accounts = db_read(DB_ACCOUNTS);
    $vouchers = db_read(DB_VOUCHERS);
    $leaf_accounts = array_filter($accounts, fn($a) => $a['type'] === 'account');
    $balances = [];
    foreach ($leaf_accounts as $a) $balances[$a['id']] = ['debit' => 0, 'credit' => 0, 'code' => $a['code'], 'title' => $a['title']];
    foreach ($vouchers as $v) foreach ($v['items'] as $item) if (isset($balances[$item['account_id']])) { $balances[$item['account_id']]['debit'] += $item['debit']; $balances[$item['account_id']]['credit'] += $item['credit']; }
    $assets = []; $liabilities = []; $equity = [];
    foreach ($balances as $b) {
        if (str_starts_with($b['code'], '1')) $assets[] = $b;
        elseif (str_starts_with($b['code'], '2')) $liabilities[] = $b;
        elseif (str_starts_with($b['code'], '3')) $equity[] = $b;
    }
    info_box('ترازنامه', 'ترازنامه صورت مالی وضعیت دارایی‌ها، بدهی‌ها و حقوق صاحبان سهام شرکت را در یک تاریخ مشخص نشان می‌دهد. معادله: دارایی‌ها = بدهی‌ها + حقوق صاحبان سهام.');
?>
<div class="card">
    <div class="card-title"><span>ترازنامه</span><button onclick="window.print()" class="btn btn-sm">چاپ</button></div>
    <div style="margin: 20px 0 10px; font-weight: 500; color: var(--primary)">دارایی‌ها</div>
    <table>
        <?php $ta = 0; foreach ($assets as $a): $n = $a['debit'] - $a['credit']; $ta += $n; ?>
        <tr><td><?= e($a['title']) ?></td><td><?= format_money($n) ?></td></tr>
        <?php endforeach; ?>
        <tr style="background: var(--bg); font-weight: bold"><td>جمع دارایی‌ها</td><td><?= format_money($ta) ?></td></tr>
    </table>
    <div style="margin: 20px 0 10px; font-weight: 500; color: var(--primary)">بدهی‌ها</div>
    <table>
        <?php $tl = 0; foreach ($liabilities as $l): $n = $l['credit'] - $l['debit']; $tl += $n; ?>
        <tr><td><?= e($l['title']) ?></td><td><?= format_money($n) ?></td></tr>
        <?php endforeach; ?>
        <tr style="background: var(--bg); font-weight: bold"><td>جمع بدهی‌ها</td><td><?= format_money($tl) ?></td></tr>
    </table>
    <div style="margin: 20px 0 10px; font-weight: 500; color: var(--primary)">حقوق صاحبان سهام</div>
    <table>
        <?php $te = 0; foreach ($equity as $eq): $n = $eq['credit'] - $eq['debit']; $te += $n; ?>
        <tr><td><?= e($eq['title']) ?></td><td><?= format_money($n) ?></td></tr>
        <?php endforeach; ?>
        <tr style="background: var(--bg); font-weight: bold"><td>جمع حقوق صاحبان سهام</td><td><?= format_money($te) ?></td></tr>
    </table>
</div>
<?php
}

function render_profit_loss() {
    $accounts = db_read(DB_ACCOUNTS);
    $vouchers = db_read(DB_VOUCHERS);
    $leaf_accounts = array_filter($accounts, fn($a) => $a['type'] === 'account');
    $balances = [];
    foreach ($leaf_accounts as $a) $balances[$a['id']] = ['debit' => 0, 'credit' => 0, 'code' => $a['code'], 'title' => $a['title']];
    foreach ($vouchers as $v) foreach ($v['items'] as $item) if (isset($balances[$item['account_id']])) { $balances[$item['account_id']]['debit'] += $item['debit']; $balances[$item['account_id']]['credit'] += $item['credit']; }
    $incomes = []; $expenses = [];
    foreach ($balances as $b) {
        if (str_starts_with($b['code'], '4')) $incomes[] = $b;
        elseif (str_starts_with($b['code'], '5')) $expenses[] = $b;
    }
    info_box('صورت سود و زیان', 'صورت سود و زیان عملکرد مالی شرکت را در یک دوره مشخص نشان می‌دهد. این گزارش شامل درآمدها و هزینه‌ها است و سود یا زیان خالص را محاسبه می‌کند.');
?>
<div class="card">
    <div class="card-title"><span>صورت سود و زیان</span><button onclick="window.print()" class="btn btn-sm">چاپ</button></div>
    <div style="margin: 20px 0 10px; font-weight: 500; color: var(--primary)">درآمدها</div>
    <table>
        <?php $ti = 0; foreach ($incomes as $i): $n = $i['credit'] - $i['debit']; $ti += $n; ?>
        <tr><td><?= e($i['title']) ?></td><td><?= format_money($n) ?></td></tr>
        <?php endforeach; ?>
        <tr style="background: var(--success-light); font-weight: bold"><td>جمع درآمدها</td><td><?= format_money($ti) ?></td></tr>
    </table>
    <div style="margin: 20px 0 10px; font-weight: 500; color: var(--primary)">هزینه‌ها</div>
    <table>
        <?php $te = 0; foreach ($expenses as $e): $n = $e['debit'] - $e['credit']; $te += $n; ?>
        <tr><td><?= e($e['title']) ?></td><td><?= format_money($n) ?></td></tr>
        <?php endforeach; ?>
        <tr style="background: var(--danger-light); font-weight: bold"><td>جمع هزینه‌ها</td><td><?= format_money($te) ?></td></tr>
    </table>
    <table style="margin-top: 20px; background: <?= ($ti - $te) >= 0 ? 'var(--success-light)' : 'var(--danger-light)' ?>">
        <tr style="font-weight: bold; font-size: 16px"><td>سود (زیان) خالص</td><td><?= format_money($ti - $te) ?></td></tr>
    </table>
</div>
<?php
}

function render_ledger() {
    $accounts = db_read(DB_ACCOUNTS);
    $vouchers = db_read(DB_VOUCHERS);
    $leaf_accounts = array_filter($accounts, fn($a) => $a['type'] === 'account');
    $selected = (int)($_GET['account_id'] ?? 0);
    info_box('دفتر کل', 'دفتر کل گزارش تمامی تراکنش‌های یک حساب خاص را به همراه مانده حساب نمایش می‌دهد. با انتخاب هر حساب، تمامی اسنادی که آن حساب در آن‌ها استفاده شده است، نمایش داده می‌شود.');
?>
<div class="card">
    <div class="card-title"><span>دفتر کل</span></div>
    <form method="GET" style="margin-bottom: 20px">
        <input type="hidden" name="action" value="ledger">
        <select name="account_id" onchange="this.form.submit()" style="padding: 12px 16px; border: 1px solid var(--border); border-radius: 6px; min-width: 300px">
            <option value="">انتخاب حساب</option>
            <?php foreach ($leaf_accounts as $a): ?>
            <option value="<?= $a['id'] ?>" <?= $selected==$a['id']?'selected':'' ?>><?= e($a['code']) ?> - <?= e($a['title']) ?></option>
            <?php endforeach; ?>
        </select>
    </form>
    <?php if ($selected):
        $account = null;
        foreach ($leaf_accounts as $a) if ($a['id'] == $selected) $account = $a;
        $entries = [];
        foreach ($vouchers as $v) foreach ($v['items'] as $item) if ($item['account_id'] == $selected) {
            $entries[] = ['date' => $v['date'], 'voucher_no' => $v['voucher_no'], 'description' => $item['description'] ?: $v['description'], 'debit' => $item['debit'], 'credit' => $item['credit']];
        }
    ?>
    <div style="margin: 20px 0 10px; font-weight: 500; color: var(--primary)"><?= e($account['title']) ?></div>
    <table>
        <tr><th>تاریخ</th><th>شماره سند</th><th>توضیحات</th><th>بدهکار</th><th>بستانکار</th><th>مانده</th></tr>
        <?php $balance = 0; foreach ($entries as $e): $balance += $e['debit'] - $e['credit']; ?>
        <tr><td><?= e($e['date']) ?></td><td><?= $e['voucher_no'] ?></td><td><?= e($e['description']) ?></td><td><?= format_money($e['debit']) ?></td><td><?= format_money($e['credit']) ?></td><td><?= format_money($balance) ?></td></tr>
        <?php endforeach; ?>
    </table>
    <?php endif; ?>
</div>
<?php
}

function render_party_report() {
    $customers = db_read(DB_CUSTOMERS);
    $suppliers = db_read(DB_SUPPLIERS);
    $transactions = db_read(DB_TRANSACTIONS);
    $selected_type = $_GET['party_type'] ?? '';
    $selected_id = (int)($_GET['party_id'] ?? 0);
    info_box('گردش حساب اشخاص', 'در این بخش گردش حساب مشتریان و تأمین‌کنندگان را مشاهده کنید. با انتخاب نوع شخص و نام او، تمامی تراکنش‌های مالی به همراه مانده حساب نمایش داده می‌شود.');
?>
<div class="card">
    <div class="card-title"><span>گردش حساب اشخاص</span></div>
    <form method="GET" style="margin-bottom: 20px; display: flex; gap: 12px; flex-wrap: wrap">
        <input type="hidden" name="action" value="party_report">
        <select name="party_type" onchange="this.form.submit()" style="padding: 12px 16px; border: 1px solid var(--border); border-radius: 6px">
            <option value="">انتخاب نوع</option>
            <option value="customer" <?= $selected_type==='customer'?'selected':'' ?>>مشتری</option>
            <option value="supplier" <?= $selected_type==='supplier'?'selected':'' ?>>تأمین‌کننده</option>
        </select>
        <?php if ($selected_type): ?>
        <select name="party_id" onchange="this.form.submit()" style="padding: 12px 16px; border: 1px solid var(--border); border-radius: 6px">
            <option value="">انتخاب شخص</option>
            <?php
            $parties = $selected_type === 'customer' ? $customers : $suppliers;
            foreach ($parties as $p):
            ?>
            <option value="<?= $p['id'] ?>" <?= $selected_id==$p['id']?'selected':'' ?>><?= e($p['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <?php endif; ?>
    </form>
    <?php if ($selected_type && $selected_id):
        $party = null;
        $parties = $selected_type === 'customer' ? $customers : $suppliers;
        foreach ($parties as $p) if ($p['id'] == $selected_id) $party = $p;
        $entries = [];
        foreach ($transactions as $t) {
            if ($t['party_type'] === $selected_type && $t['party_id'] == $selected_id) {
                $entries[] = ['date' => $t['date'], 'description' => $t['description'], 'debit' => $t['type'] === 'payment' ? $t['amount'] : 0, 'credit' => $t['type'] === 'receipt' ? $t['amount'] : 0];
            }
        }
        usort($entries, fn($a, $b) => strcmp($a['date'], $b['date']));
    ?>
    <div style="margin: 20px 0 10px; font-weight: 500; color: var(--primary)"><?= e($party['name']) ?> - مانده فعلی: <?= format_money($party['balance']) ?></div>
    <table>
        <tr><th>تاریخ</th><th>توضیحات</th><th>بدهکار</th><th>بستانکار</th></tr>
        <?php foreach ($entries as $e): ?>
        <tr><td><?= e($e['date']) ?></td><td><?= e($e['description']) ?></td><td><?= format_money($e['debit']) ?></td><td><?= format_money($e['credit']) ?></td></tr>
        <?php endforeach; ?>
    </table>
    <?php endif; ?>
</div>
<?php
}

function render_periods() {
    $periods = db_read(DB_PERIODS);
    info_box('دوره‌های مالی', 'دوره مالی بازه زمانی است که گزارش‌های مالی برای آن تهیه می‌شود. معمولاً هر دوره مالی یک سال است. با بستن دوره، امکان ثبت سند جدید برای آن دوره وجود نخواهد داشت.');
?>
<div class="card">
    <div class="card-title">
        <span>دوره‌های مالی</span>
        <div class="card-title-actions">
            <a data-action="new_period" class="btn btn-primary btn-sm nav-link">دوره جدید</a>
        </div>
    </div>
    <table>
        <tr><th>نام</th><th>از تاریخ</th><th>تا تاریخ</th><th>فعال</th><th>وضعیت</th><th>عملیات</th></tr>
        <?php foreach ($periods as $p): ?>
        <tr>
            <td><?= e($p['name']) ?></td>
            <td><?= e($p['start_date']) ?></td>
            <td><?= e($p['end_date']) ?></td>
            <td><?= $p['is_current']?'<span class="badge badge-success">بله</span>':'<span class="badge badge-warning">خیر</span>' ?></td>
            <td><?= $p['is_closed']?'<span class="badge badge-danger">بسته</span>':'<span class="badge badge-success">باز</span>' ?></td>
            <td>
                <?php if (!$p['is_closed']): ?>
                <form method="POST" action="?action=close_period" class="inline-form" onsubmit="return confirm('آیا از بستن دوره اطمینان دارید؟')">
                    <input type="hidden" name="period_id" value="<?= $p['id'] ?>">
                    <button type="submit" class="btn btn-danger btn-sm">بستن دوره</button>
                </form>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
<script>
document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        history.replaceState(null, '', '#' + this.dataset.action);
        showPage(this.dataset.action);
    });
});
</script>
<?php
}

function render_new_period() {
    info_box('ایجاد دوره مالی جدید', 'نام دوره، تاریخ شروع و پایان را مشخص کنید. با انتخاب گزینه فعال، دوره قبلی غیرفعال خواهد شد.');
?>
<div class="card">
    <div class="card-title"><span>ایجاد دوره مالی جدید</span></div>
    <form method="POST" action="?action=save_period">
        <div class="form-grid">
            <div class="form-group"><label>نام دوره</label><input type="text" name="name" required></div>
            <div class="form-group"><label>تاریخ شروع</label><input type="text" name="start_date" placeholder="1403/01/01" required></div>
            <div class="form-group"><label>تاریخ پایان</label><input type="text" name="end_date" placeholder="1403/12/29" required></div>
            <div class="form-group"><label>دوره فعال</label><select name="is_current"><option value="1">بله</option><option value="0">خیر</option></select></div>
        </div>
        <button type="submit" class="btn btn-primary">ذخیره</button>
        <a data-action="periods" class="btn nav-link">انصراف</a>
    </form>
</div>
<script>
document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        history.replaceState(null, '', '#' + this.dataset.action);
        showPage(this.dataset.action);
    });
});
</script>
<?php
}

function render_backup() {
    info_box('پشتیبان‌گیری و بازیابی', 'در این بخش می‌توانید از تمامی اطلاعات سیستم پشتیبان تهیه کنید و در صورت نیاز آن را بازیابی کنید. بازیابی اطلاعات، تمام داده‌های فعلی را جایگزین می‌کند.');
?>
<div class="card">
    <div class="card-title"><span>پشتیبان‌گیری</span></div>
    <p style="color: var(--text-light); margin-bottom: 15px">تمامی اطلاعات سیستم در یک فایل JSON ذخیره می‌شود.</p>
    <form method="POST" action="?action=backup">
        <button type="submit" class="btn btn-primary">دانلود پشتیبان</button>
    </form>
</div>
<div class="card">
    <div class="card-title"><span>بازیابی اطلاعات</span></div>
    <p style="color: var(--danger); margin-bottom: 15px">⚠️ بازیابی اطلاعات، تمام داده‌های فعلی را جایگزین می‌کند.</p>
    <form method="POST" action="?action=restore" enctype="multipart/form-data">
        <div class="form-group"><label>فایل پشتیبان (JSON)</label><input type="file" name="backup_file" accept=".json" required></div>
        <button type="submit" class="btn btn-danger" onclick="return confirm('آیا مطمئن هستید؟')">بازیابی</button>
    </form>
</div>
<?php
}

function render_settings() {
    $settings = db_read(DB_SETTINGS);
    info_box('تنظیمات سیستم', 'در این بخش تنظیمات کلی سیستم مانند نام شرکت و واحد پول را تغییر دهید. این اطلاعات در سربرگ و گزارش‌ها نمایش داده می‌شود.');
?>
<div class="card">
    <div class="card-title"><span>تنظیمات سیستم</span></div>
    <form method="POST" action="?action=settings">
        <div class="form-grid">
            <div class="form-group"><label>نام شرکت</label><input type="text" name="company_name" value="<?= e($settings['company_name'] ?? 'رز') ?>" required></div>
            <div class="form-group"><label>واحد پول</label><input type="text" name="currency" value="<?= e($settings['currency'] ?? 'ریال') ?>" required></div>
        </div>
        <button type="submit" class="btn btn-primary">ذخیره</button>
    </form>
</div>
<div class="card">
    <div class="card-title"><span>اطلاعات سیستم</span></div>
    <table>
        <tr><th style="width:200px">نسخه</th><td><?= e($settings['version'] ?? '01 بتا') ?></td></tr>
        <tr><th>زمان نصب</th><td><?= e($settings['installed_at'] ?? '-') ?></td></tr>
        <tr><th>نسخه PHP</th><td><?= phpversion() ?></td></tr>
    </table>
</div>
<?php
}
?>
</body>
</html>