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
