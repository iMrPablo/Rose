<?php
// Render wrappers. HTML/PHP templates live in views/pages/.
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
    include __DIR__ . '/../views/pages/dashboard.php';
}

function render_vouchers() {
    $vouchers = db_read(DB_VOUCHERS);
    info_box('اسناد حسابداری', 'در این بخش تمامی اسناد حسابداری ثبت شده نمایش داده می‌شود. سند حسابداری ثبت یک رویداد مالی است که شامل یک یا چند سطر بدهکار و بستانکار می‌باشد. اسناد می‌توانند به صورت دستی یا خودکار از عملیات خرید و فروش صادر شوند. جمع بدهکار و بستانکار هر سند باید برابر باشد.');
    include __DIR__ . '/../views/pages/vouchers.php';
}

function render_new_voucher() {
    $accounts = db_read(DB_ACCOUNTS);
    $leaf_accounts = array_filter($accounts, fn($a) => $a['type'] === 'account');
    info_box('ثبت سند جدید', 'هر سند حداقل باید دو سطر داشته باشد و جمع بدهکار و بستانکار باید برابر باشد. سرفصل‌های حساب را از لیست انتخاب کنید و مبلغ بدهکار یا بستانکار را وارد نمایید. سیستم به صورت خودکار جمع‌ها را محاسبه و اختلاف را نمایش می‌دهد.');
    include __DIR__ . '/../views/pages/new_voucher.php';
}

function render_accounts() {
    $accounts = db_read(DB_ACCOUNTS);
    info_box('سرفصل‌ها و کدینگ', 'در این بخش ساختار درختی حساب‌های حسابداری تعریف می‌شود. کدینگ حسابداری شامل سه سطح است: سرفصل کل، گروه و حساب معین. هر حساب معین می‌تواند در اسناد حسابداری استفاده شود.');
    include __DIR__ . '/../views/pages/accounts.php';
}

function render_new_account() {
    $accounts = db_read(DB_ACCOUNTS);
    info_box('ایجاد سرفصل جدید', 'برای ایجاد سرفصل جدید، کد یکتا، عنوان و نوع سرفصل را مشخص کنید. سرفصل والد را برای ایجاد ساختار درختی انتخاب کنید.');
    include __DIR__ . '/../views/pages/new_account.php';
}

function render_transactions() {
    $transactions = db_read(DB_TRANSACTIONS);
    info_box('دریافت و پرداخت', 'در این بخش عملیات دریافت و پرداخت وجه ثبت می‌شود. دریافت هنگام دریافت وجه از مشتری و پرداخت هنگام پرداخت وجه به تأمین‌کننده ثبت می‌شود. با ثبت هر عملیات، مانده حساب طرف مقابل به صورت خودکار به‌روزرسانی می‌شود.');
    include __DIR__ . '/../views/pages/transactions.php';
}

function render_new_transaction() {
    $accounts = array_filter(db_read(DB_ACCOUNTS), fn($a) => $a['type'] === 'account');
    $customers = db_read(DB_CUSTOMERS);
    $suppliers = db_read(DB_SUPPLIERS);
    info_box('ثبت دریافت / پرداخت', 'نوع عملیات، مبلغ و حساب مربوطه را مشخص کنید. در صورتی که عملیات مربوط به مشتری یا تأمین‌کننده خاصی است، طرف حساب را انتخاب کنید تا مانده حساب آن‌ها به صورت خودکار به‌روزرسانی شود.');
    include __DIR__ . '/../views/pages/new_transaction.php';
}

function render_checks() {
    $checks = db_read(DB_CHECKS);
    info_box('مدیریت چک‌ها', 'در این بخش چک‌های دریافتی و پرداختی مدیریت می‌شوند. با استفاده از دکمه «تغییر وضعیت» می‌توانید وضعیت هر چک را به «در جریان»، «وصول شده» یا «برگشتی» تغییر دهید.');
    include __DIR__ . '/../views/pages/checks.php';
}

function render_new_check() {
    $accounts = array_filter(db_read(DB_ACCOUNTS), fn($a) => $a['type'] === 'account');
    info_box('ثبت چک جدید', 'نوع چک (دریافتی یا پرداختی)، شماره چک، نام بانک، مبلغ و تاریخ سررسید را وارد کنید.');
    include __DIR__ . '/../views/pages/new_check.php';
}

function render_cashboxes() {
    $cashboxes = db_read(DB_CASHBOXES);
    info_box('صندوق‌ها', 'صندوق‌های نقدی شرکت در این بخش مدیریت می‌شوند. هر صندوق می‌تواند موجودی جداگانه‌ای داشته باشد و در عملیات دریافت و پرداخت قابل انتخاب است.');
    include __DIR__ . '/../views/pages/cashboxes.php';
}

function render_new_cashbox() {
    info_box('ایجاد صندوق جدید', 'نام صندوق را وارد کنید. موجودی اولیه صفر است و با ثبت عملیات دریافت و پرداخت تغییر می‌کند.');
    include __DIR__ . '/../views/pages/new_cashbox.php';
}

function render_banks() {
    $banks = db_read(DB_BANKS);
    info_box('بانک‌ها', 'حساب‌های بانکی شرکت در این بخش مدیریت می‌شوند. هر حساب شامل نام بانک، شماره حساب، شعبه و موجودی است.');
    include __DIR__ . '/../views/pages/banks.php';
}

function render_new_bank() {
    info_box('ثبت بانک جدید', 'نام بانک، شماره حساب و شعبه را وارد کنید. موجودی اولیه صفر است.');
    include __DIR__ . '/../views/pages/new_bank.php';
}

function render_petty_cash() {
    $petty = db_read(DB_PETTY_CASH);
    info_box('تنخواه', 'تنخواه مبلغی است که برای هزینه‌های کوچک در اختیار یک شخص قرار می‌گیرد. هر تنخواه شامل مبلغ اولیه، تحویل‌دار و مانده فعلی است.');
    include __DIR__ . '/../views/pages/petty_cash.php';
}

function render_new_petty_cash() {
    info_box('ایجاد تنخواه جدید', 'عنوان تنخواه، نام تحویل‌دار و مبلغ اولیه را وارد کنید.');
    include __DIR__ . '/../views/pages/new_petty_cash.php';
}

function render_customers() {
    $customers = db_read(DB_CUSTOMERS);
    info_box('مشتریان', 'اطلاعات مشتریان شرکت در این بخش مدیریت می‌شود. مشتری شخصی است که از شرکت کالا یا خدمات خریداری می‌کند. مانده حساب مشتری نشان‌دهنده مبلغی است که باید به شرکت پرداخت کند.');
    include __DIR__ . '/../views/pages/customers.php';
}

function render_new_customer() {
    info_box('ثبت مشتری جدید', 'کد یکتا، نام و سایر اطلاعات مشتری را وارد کنید.');
    include __DIR__ . '/../views/pages/new_customer.php';
}

function render_suppliers() {
    $suppliers = db_read(DB_SUPPLIERS);
    info_box('تأمین‌کنندگان', 'اطلاعات تأمین‌کنندگان شرکت در این بخش مدیریت می‌شود. تأمین‌کننده شخصی است که شرکت از او کالا یا خدمات خریداری می‌کند.');
    include __DIR__ . '/../views/pages/suppliers.php';
}

function render_new_supplier() {
    info_box('ثبت تأمین‌کننده جدید', 'کد یکتا، نام و سایر اطلاعات تأمین‌کننده را وارد کنید.');
    include __DIR__ . '/../views/pages/new_supplier.php';
}

function render_purchases() {
    $purchases = db_read(DB_PURCHASES);
    $suppliers = db_read(DB_SUPPLIERS);
    info_box('فاکتورهای خرید', 'فاکتورهای خرید ثبت شده در این بخش نمایش داده می‌شود. با ثبت هر فاکتور، موجودی انبار افزایش یافته، مانده حساب تأمین‌کننده بدهکار می‌شود و سند حسابداری به صورت خودکار صادر می‌گردد.');
    include __DIR__ . '/../views/pages/purchases.php';
}

function render_new_purchase() {
    $suppliers = db_read(DB_SUPPLIERS);
    info_box('ثبت فاکتور خرید', 'تأمین‌کننده و اقلام کالا را مشخص کنید. با ثبت فاکتور، موجودی انبار به‌روزرسانی شده، مانده حساب تأمین‌کننده افزایش یافته و سند حسابداری به صورت خودکار صادر می‌شود.');
    include __DIR__ . '/../views/pages/new_purchase.php';
}

function render_sales() {
    $sales = db_read(DB_SALES);
    $customers = db_read(DB_CUSTOMERS);
    info_box('فاکتورهای فروش', 'فاکتورهای فروش ثبت شده در این بخش نمایش داده می‌شود. با ثبت هر فاکتور، موجودی انبار کاهش یافته، مانده حساب مشتری بدهکار می‌شود و سند حسابداری به صورت خودکار صادر می‌گردد.');
    include __DIR__ . '/../views/pages/sales.php';
}

function render_new_sale() {
    $customers = db_read(DB_CUSTOMERS);
    info_box('ثبت فاکتور فروش', 'مشتری و اقلام کالا را مشخص کنید. با ثبت فاکتور، موجودی انبار کاهش یافته، مانده حساب مشتری افزایش یافته و سند حسابداری به صورت خودکار صادر می‌شود.');
    include __DIR__ . '/../views/pages/new_sale.php';
}

function render_inventory() {
    $inventory = db_read(DB_INVENTORY);
    info_box('موجودی انبار', 'موجودی کالاهای انبار در این بخش نمایش داده می‌شود. با ثبت فاکتور خرید، موجودی افزایش و با ثبت فاکتور فروش، موجودی کاهش می‌یابد.');
    include __DIR__ . '/../views/pages/inventory.php';
}

function render_expenses() {
    $expenses = db_read(DB_EXPENSES);
    $accounts = db_read(DB_ACCOUNTS);
    info_box('هزینه‌ها', 'هزینه‌های شرکت در این بخش ثبت و مدیریت می‌شود. هزینه‌ها شامل حقوق، اجاره، قبوض و سایر هزینه‌های عملیاتی هستند.');
    include __DIR__ . '/../views/pages/expenses.php';
}

function render_new_expense() {
    $accounts = array_filter(db_read(DB_ACCOUNTS), fn($a) => $a['type'] === 'account' && str_starts_with($a['code'], '5'));
    info_box('ثبت هزینه', 'سرفصل مربوطه، مبلغ و توضیحات را وارد کنید.');
    include __DIR__ . '/../views/pages/new_expense.php';
}

function render_income() {
    $income = db_read(DB_INCOME);
    $accounts = db_read(DB_ACCOUNTS);
    info_box('درآمدها', 'درآمدهای شرکت در این بخش ثبت و مدیریت می‌شود. درآمدها شامل فروش کالا، ارائه خدمات و سایر درآمدها هستند.');
    include __DIR__ . '/../views/pages/income.php';
}

function render_new_income() {
    $accounts = array_filter(db_read(DB_ACCOUNTS), fn($a) => $a['type'] === 'account' && str_starts_with($a['code'], '4'));
    info_box('ثبت درآمد', 'سرفصل مربوطه، مبلغ و توضیحات را وارد کنید.');
    include __DIR__ . '/../views/pages/new_income.php';
}

function render_trial_balance() {
    $accounts = db_read(DB_ACCOUNTS);
    $vouchers = db_read(DB_VOUCHERS);
    $leaf_accounts = array_filter($accounts, fn($a) => $a['type'] === 'account');
    $balances = [];
    foreach ($leaf_accounts as $a) $balances[$a['id']] = ['debit' => 0, 'credit' => 0, 'code' => $a['code'], 'title' => $a['title']];
    foreach ($vouchers as $v) foreach ($v['items'] as $item) if (isset($balances[$item['account_id']])) { $balances[$item['account_id']]['debit'] += $item['debit']; $balances[$item['account_id']]['credit'] += $item['credit']; }
    info_box('تراز آزمایشی', 'تراز آزمایشی گزارش جمع بدهکار و بستانکار تمامی حساب‌ها را نمایش می‌دهد. جمع کل بدهکار باید با جمع کل بستانکار برابر باشد. این گزارش برای کنترل صحت ثبت اسناد استفاده می‌شود.');
    include __DIR__ . '/../views/pages/trial_balance.php';
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
    include __DIR__ . '/../views/pages/balance_sheet.php';
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
    include __DIR__ . '/../views/pages/profit_loss.php';
}

function render_ledger() {
    $accounts = db_read(DB_ACCOUNTS);
    $vouchers = db_read(DB_VOUCHERS);
    $leaf_accounts = array_filter($accounts, fn($a) => $a['type'] === 'account');
    $selected = (int)($_GET['account_id'] ?? 0);
    info_box('دفتر کل', 'دفتر کل گزارش تمامی تراکنش‌های یک حساب خاص را به همراه مانده حساب نمایش می‌دهد. با انتخاب هر حساب، تمامی اسنادی که آن حساب در آن‌ها استفاده شده است، نمایش داده می‌شود.');
    include __DIR__ . '/../views/pages/ledger.php';
}

function render_party_report() {
    $customers = db_read(DB_CUSTOMERS);
    $suppliers = db_read(DB_SUPPLIERS);
    $transactions = db_read(DB_TRANSACTIONS);
    $selected_type = $_GET['party_type'] ?? '';
    $selected_id = (int)($_GET['party_id'] ?? 0);
    info_box('گردش حساب اشخاص', 'در این بخش گردش حساب مشتریان و تأمین‌کنندگان را مشاهده کنید. با انتخاب نوع شخص و نام او، تمامی تراکنش‌های مالی به همراه مانده حساب نمایش داده می‌شود.');
    include __DIR__ . '/../views/pages/party_report.php';
}

function render_periods() {
    $periods = db_read(DB_PERIODS);
    info_box('دوره‌های مالی', 'دوره مالی بازه زمانی است که گزارش‌های مالی برای آن تهیه می‌شود. معمولاً هر دوره مالی یک سال است. با بستن دوره، امکان ثبت سند جدید برای آن دوره وجود نخواهد داشت.');
    include __DIR__ . '/../views/pages/periods.php';
}

function render_new_period() {
    info_box('ایجاد دوره مالی جدید', 'نام دوره، تاریخ شروع و پایان را مشخص کنید. با انتخاب گزینه فعال، دوره قبلی غیرفعال خواهد شد.');
    include __DIR__ . '/../views/pages/new_period.php';
}

function render_backup() {
    info_box('پشتیبان‌گیری و بازیابی', 'در این بخش می‌توانید از تمامی اطلاعات سیستم پشتیبان تهیه کنید و در صورت نیاز آن را بازیابی کنید. بازیابی اطلاعات، تمام داده‌های فعلی را جایگزین می‌کند.');
    include __DIR__ . '/../views/pages/backup.php';
}

function render_settings() {
    $settings = db_read(DB_SETTINGS);
    info_box('تنظیمات سیستم', 'در این بخش تنظیمات کلی سیستم مانند نام شرکت و واحد پول را تغییر دهید. این اطلاعات در سربرگ و گزارش‌ها نمایش داده می‌شود.');
    include __DIR__ . '/../views/pages/settings.php';
}

?>
