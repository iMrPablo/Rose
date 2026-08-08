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
