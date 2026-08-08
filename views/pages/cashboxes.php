<div class="card">
    <div class="card-title">
        <span>صندوق‌ها</span>
        <div class="card-title-actions">
            <a data-action="new_cashbox" class="btn btn-primary btn-sm nav-link">صندوق جدید</a>
            <form method="POST" action="?action=reset_section" class="inline-form" onsubmit="return confirm('آیا از ریست صندوق‌ها مطمئن هستید؟')">
                <input type="hidden" name="section" value="cashboxes">
                <button type="submit" class="btn btn-danger btn-sm">ریست</button>
            </form>
        </div>
    </div>
    <table>
        <tr><th>نام صندوق</th><th>موجودی</th></tr>
        <?php foreach ($cashboxes as $c): ?>
        <tr><td><?= e($c['name']) ?></td><td><?= format_money($c['balance']) ?></td></tr>
        <?php endforeach; ?>
    </table>
</div>
