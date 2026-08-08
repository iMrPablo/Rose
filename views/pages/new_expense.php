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
