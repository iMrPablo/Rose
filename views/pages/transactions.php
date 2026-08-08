<div class="card">
    <div class="card-title">
        <span>دریافت و پرداخت</span>
        <div class="card-title-actions">
            <a data-action="new_transaction" class="btn btn-primary btn-sm nav-link">ثبت جدید</a>
            <form method="POST" action="?action=reset_section" class="inline-form" onsubmit="return confirm('آیا از حذف تمام تراکنش‌ها مطمئن هستید؟')">
                <input type="hidden" name="section" value="transactions">
                <button type="submit" class="btn btn-danger btn-sm">ریست تراکنش‌ها</button>
            </form>
        </div>
    </div>
    <table>
        <tr><th>تاریخ</th><th>نوع</th><th>مبلغ</th><th>طرف حساب</th><th>توضیحات</th><th>شماره پیگیری</th></tr>
        <?php foreach (array_reverse($transactions) as $t): ?>
        <tr>
            <td><?= e($t['date']) ?></td>
            <td><span class="badge badge-<?= $t['type']==='receipt'?'success':'danger' ?>"><?= $t['type']==='receipt'?'دریافت':'پرداخت' ?></span></td>
            <td><?= format_money($t['amount']) ?></td>
            <td><?= e($t['party_type']) ?> #<?= $t['party_id'] ?></td>
            <td><?= e($t['description']) ?></td>
            <td><?= e($t['reference']) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($transactions)): ?><tr><td colspan="6" class="text-center">عملیاتی ثبت نشده است</td></tr><?php endif; ?>
    </table>
</div>
