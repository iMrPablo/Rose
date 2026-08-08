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
