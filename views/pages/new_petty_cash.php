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
