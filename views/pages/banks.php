<div class="card">
    <div class="card-title">
        <span>بانک‌ها</span>
        <div class="card-title-actions">
            <a data-action="new_bank" class="btn btn-primary btn-sm nav-link">بانک جدید</a>
            <form method="POST" action="?action=reset_section" class="inline-form" onsubmit="return confirm('آیا از ریست بانک‌ها مطمئن هستید؟')">
                <input type="hidden" name="section" value="banks">
                <button type="submit" class="btn btn-danger btn-sm">ریست</button>
            </form>
        </div>
    </div>
    <table>
        <tr><th>نام بانک</th><th>شماره حساب</th><th>شعبه</th><th>موجودی</th></tr>
        <?php foreach ($banks as $b): ?>
        <tr><td><?= e($b['name']) ?></td><td><?= e($b['account_no']) ?></td><td><?= e($b['branch']) ?></td><td><?= format_money($b['balance']) ?></td></tr>
        <?php endforeach; ?>
    </table>
</div>
