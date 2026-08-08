<div class="card">
    <div class="card-title">
        <span>فاکتورهای خرید</span>
        <div class="card-title-actions">
            <a data-action="new_purchase" class="btn btn-primary btn-sm nav-link">فاکتور جدید</a>
            <form method="POST" action="?action=reset_section" class="inline-form" onsubmit="return confirm('آیا از حذف تمام فاکتورهای خرید مطمئن هستید؟')">
                <input type="hidden" name="section" value="purchases">
                <button type="submit" class="btn btn-danger btn-sm">ریست</button>
            </form>
        </div>
    </div>
    <table>
        <tr><th>شماره</th><th>تاریخ</th><th>تأمین‌کننده</th><th>مبلغ کل</th></tr>
        <?php foreach (array_reverse($purchases) as $p):
            $sn = ''; foreach ($suppliers as $s) if ($s['id'] == $p['supplier_id']) $sn = $s['name'];
        ?>
        <tr><td><?= $p['invoice_no'] ?></td><td><?= e($p['date']) ?></td><td><?= e($sn) ?></td><td><?= format_money($p['total']) ?></td></tr>
        <?php endforeach; ?>
        <?php if (empty($purchases)): ?><tr><td colspan="4" class="text-center">فاکتوری ثبت نشده است</td></tr><?php endif; ?>
    </table>
</div>
