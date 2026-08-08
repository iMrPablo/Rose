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
