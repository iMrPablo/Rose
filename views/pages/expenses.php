<div class="card">
    <div class="card-title">
        <span>هزینه‌ها</span>
        <div class="card-title-actions">
            <a data-action="new_expense" class="btn btn-primary btn-sm nav-link">ثبت هزینه</a>
            <form method="POST" action="?action=reset_section" class="inline-form" onsubmit="return confirm('آیا از حذف تمام هزینه‌ها مطمئن هستید؟')">
                <input type="hidden" name="section" value="expenses">
                <button type="submit" class="btn btn-danger btn-sm">ریست</button>
            </form>
        </div>
    </div>
    <table>
        <tr><th>تاریخ</th><th>سرفصل</th><th>مبلغ</th><th>توضیحات</th></tr>
        <?php foreach (array_reverse($expenses) as $ex):
            $at = ''; foreach ($accounts as $a) if ($a['id'] == $ex['account_id']) $at = $a['title'];
        ?>
        <tr><td><?= e($ex['date']) ?></td><td><?= e($at) ?></td><td><?= format_money($ex['amount']) ?></td><td><?= e($ex['description']) ?></td></tr>
        <?php endforeach; ?>
        <?php if (empty($expenses)): ?><tr><td colspan="4" class="text-center">هزینه‌ای ثبت نشده است</td></tr><?php endif; ?>
    </table>
</div>
