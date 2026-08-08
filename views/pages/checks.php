<div class="card">
    <div class="card-title">
        <span>مدیریت چک‌ها</span>
        <div class="card-title-actions">
            <a data-action="new_check" class="btn btn-primary btn-sm nav-link">ثبت چک جدید</a>
            <form method="POST" action="?action=reset_section" class="inline-form" onsubmit="return confirm('آیا از حذف تمام چک‌ها مطمئن هستید؟')">
                <input type="hidden" name="section" value="checks">
                <button type="submit" class="btn btn-danger btn-sm">ریست چک‌ها</button>
            </form>
        </div>
    </div>
    <table>
        <tr><th>شماره چک</th><th>نوع</th><th>بانک</th><th>مبلغ</th><th>سررسید</th><th>صادر کننده</th><th>تغییر وضعیت</th></tr>
        <?php foreach (array_reverse($checks) as $c): ?>
        <tr>
            <td><?= e($c['check_no']) ?></td>
            <td><span class="badge badge-<?= $c['type']==='received'?'success':'danger' ?>"><?= $c['type']==='received'?'دریافتی':'پرداختی' ?></span></td>
            <td><?= e($c['bank']) ?></td>
            <td><?= format_money($c['amount']) ?></td>
            <td><?= e($c['due_date']) ?></td>
            <td><?= e($c['issuer']) ?></td>
            <td>
                <form method="POST" action="?action=change_check_status" class="inline-form">
                    <input type="hidden" name="check_id" value="<?= $c['id'] ?>">
                    <select name="new_status" class="status-select" onchange="this.form.submit()">
                        <option value="pending" <?= $c['status']==='pending'?'selected':'' ?>>در جریان</option>
                        <option value="cleared" <?= $c['status']==='cleared'?'selected':'' ?>>وصول شده</option>
                        <option value="bounced" <?= $c['status']==='bounced'?'selected':'' ?>>برگشتی</option>
                    </select>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($checks)): ?><tr><td colspan="7" class="text-center">چکی ثبت نشده است</td></tr><?php endif; ?>
    </table>
</div>
