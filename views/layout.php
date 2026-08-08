<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>رز | سیستم حسابداری</title>

    <link rel="stylesheet" href="assets/css/style.css">
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
    <script src="assets/js/app.js"></script>
</body>
</html>