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
