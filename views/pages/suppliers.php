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
