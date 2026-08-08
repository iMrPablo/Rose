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

document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        const action = this.dataset.action;
        history.replaceState(null, '', '#' + action);
        showPage(action);
    });
});

document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        history.replaceState(null, '', '#' + this.dataset.action);
        showPage(this.dataset.action);
    });
});

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

document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        history.replaceState(null, '', '#' + this.dataset.action);
        showPage(this.dataset.action);
    });
});

document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        history.replaceState(null, '', '#' + this.dataset.action);
        showPage(this.dataset.action);
    });
});

document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        history.replaceState(null, '', '#' + this.dataset.action);
        showPage(this.dataset.action);
    });
});

document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        history.replaceState(null, '', '#' + this.dataset.action);
        showPage(this.dataset.action);
    });
});

document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        history.replaceState(null, '', '#' + this.dataset.action);
        showPage(this.dataset.action);
    });
});

document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        history.replaceState(null, '', '#' + this.dataset.action);
        showPage(this.dataset.action);
    });
});

document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        history.replaceState(null, '', '#' + this.dataset.action);
        showPage(this.dataset.action);
    });
});

document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        history.replaceState(null, '', '#' + this.dataset.action);
        showPage(this.dataset.action);
    });
});

document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        history.replaceState(null, '', '#' + this.dataset.action);
        showPage(this.dataset.action);
    });
});

document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        history.replaceState(null, '', '#' + this.dataset.action);
        showPage(this.dataset.action);
    });
});

document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        history.replaceState(null, '', '#' + this.dataset.action);
        showPage(this.dataset.action);
    });
});

document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        history.replaceState(null, '', '#' + this.dataset.action);
        showPage(this.dataset.action);
    });
});

document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        history.replaceState(null, '', '#' + this.dataset.action);
        showPage(this.dataset.action);
    });
});

document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        history.replaceState(null, '', '#' + this.dataset.action);
        showPage(this.dataset.action);
    });
});

document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        history.replaceState(null, '', '#' + this.dataset.action);
        showPage(this.dataset.action);
    });
});

document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        history.replaceState(null, '', '#' + this.dataset.action);
        showPage(this.dataset.action);
    });
});

document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        history.replaceState(null, '', '#' + this.dataset.action);
        showPage(this.dataset.action);
    });
});

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

document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        history.replaceState(null, '', '#' + this.dataset.action);
        showPage(this.dataset.action);
    });
});

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

document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        history.replaceState(null, '', '#' + this.dataset.action);
        showPage(this.dataset.action);
    });
});

document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        history.replaceState(null, '', '#' + this.dataset.action);
        showPage(this.dataset.action);
    });
});

document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        history.replaceState(null, '', '#' + this.dataset.action);
        showPage(this.dataset.action);
    });
});

document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        history.replaceState(null, '', '#' + this.dataset.action);
        showPage(this.dataset.action);
    });
});

document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        history.replaceState(null, '', '#' + this.dataset.action);
        showPage(this.dataset.action);
    });
});

document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        history.replaceState(null, '', '#' + this.dataset.action);
        showPage(this.dataset.action);
    });
});
