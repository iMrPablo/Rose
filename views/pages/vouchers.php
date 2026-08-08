<div class="card">
    <div class="card-title">
        <span>لیست اسناد حسابداری</span>
        <div class="card-title-actions">
            <a data-action="new_voucher" class="btn btn-primary btn-sm nav-link">ثبت سند جدید</a>
            <form method="POST" action="?action=reset_section" class="inline-form" onsubmit="return confirm('آیا از حذف تمام اسناد مطمئن هستید؟')">
                <input type="hidden" name="section" value="vouchers">
                <button type="submit" class="btn btn-danger btn-sm">ریست اسناد</button>
            </form>
        </div>
    </div>
    <table>
        <tr><th>شماره</th><th>تاریخ</th><th>توضیحات</th><th>بدهکار</th><th>بستانکار</th><th>وضعیت</th></tr>
        <?php foreach (array_reverse($vouchers) as $v): ?>
        <tr>
            <td><?= $v['voucher_no'] ?></td>
            <td><?= e($v['date']) ?></td>
            <td><?= e($v['description']) ?></td>
            <td><?= format_money($v['total_debit']) ?></td>
            <td><?= format_money($v['total_credit']) ?></td>
            <td><span class="badge badge-<?= $v['status']==='auto'?'info':($v['status']==='posted'?'success':'warning') ?>"><?= e($v['status']) ?></span></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($vouchers)): ?><tr><td colspan="6" class="text-center">سندی ثبت نشده است</td></tr><?php endif; ?>
    </table>
</div>
