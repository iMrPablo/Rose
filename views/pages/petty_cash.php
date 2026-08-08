<div class="card">
    <div class="card-title">
        <span>تنخواه‌ها</span>
        <div class="card-title-actions">
            <a data-action="new_petty_cash" class="btn btn-primary btn-sm nav-link">تنخواه جدید</a>
            <form method="POST" action="?action=reset_section" class="inline-form" onsubmit="return confirm('آیا از ریست تنخواه‌ها مطمئن هستید؟')">
                <input type="hidden" name="section" value="petty_cash">
                <button type="submit" class="btn btn-danger btn-sm">ریست</button>
            </form>
        </div>
    </div>
    <table>
        <tr><th>عنوان</th><th>تحویل‌دار</th><th>مبلغ اولیه</th><th>مانده</th></tr>
        <?php foreach ($petty as $p): ?>
        <tr><td><?= e($p['name']) ?></td><td><?= e($p['holder']) ?></td><td><?= format_money($p['amount']) ?></td><td><?= format_money($p['balance']) ?></td></tr>
        <?php endforeach; ?>
    </table>
</div>
