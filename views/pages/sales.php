<div class="card">
    <div class="card-title">
        <span>فاکتورهای فروش</span>
        <div class="card-title-actions">
            <a data-action="new_sale" class="btn btn-primary btn-sm nav-link">فاکتور جدید</a>
            <form method="POST" action="?action=reset_section" class="inline-form" onsubmit="return confirm('آیا از حذف تمام فاکتورهای فروش مطمئن هستید؟')">
                <input type="hidden" name="section" value="sales">
                <button type="submit" class="btn btn-danger btn-sm">ریست</button>
            </form>
        </div>
    </div>
    <table>
        <tr><th>شماره</th><th>تاریخ</th><th>مشتری</th><th>مبلغ کل</th></tr>
        <?php foreach (array_reverse($sales) as $s):
            $cn = ''; foreach ($customers as $c) if ($c['id'] == $s['customer_id']) $cn = $c['name'];
        ?>
        <tr><td><?= $s['invoice_no'] ?></td><td><?= e($s['date']) ?></td><td><?= e($cn) ?></td><td><?= format_money($s['total']) ?></td></tr>
        <?php endforeach; ?>
        <?php if (empty($sales)): ?><tr><td colspan="4" class="text-center">فاکتوری ثبت نشده است</td></tr><?php endif; ?>
    </table>
</div>
