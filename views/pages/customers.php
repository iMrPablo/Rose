<div class="card">
    <div class="card-title">
        <span>مشتریان</span>
        <div class="card-title-actions">
            <a data-action="new_customer" class="btn btn-primary btn-sm nav-link">مشتری جدید</a>
            <form method="POST" action="?action=reset_section" class="inline-form" onsubmit="return confirm('آیا از حذف تمام مشتریان مطمئن هستید؟')">
                <input type="hidden" name="section" value="customers">
                <button type="submit" class="btn btn-danger btn-sm">ریست</button>
            </form>
        </div>
    </div>
    <table>
        <tr><th>کد</th><th>نام</th><th>تلفن</th><th>کد ملی</th><th>مانده حساب</th></tr>
        <?php foreach ($customers as $c): ?>
        <tr><td><?= e($c['code']) ?></td><td><?= e($c['name']) ?></td><td><?= e($c['phone']) ?></td><td><?= e($c['national_id']) ?></td><td><?= format_money($c['balance']) ?></td></tr>
        <?php endforeach; ?>
        <?php if (empty($customers)): ?><tr><td colspan="5" class="text-center">مشتری‌ای ثبت نشده است</td></tr><?php endif; ?>
    </table>
</div>
