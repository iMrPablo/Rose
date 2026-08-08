<div class="card">
    <div class="card-title">
        <span>درآمدها</span>
        <div class="card-title-actions">
            <a data-action="new_income" class="btn btn-primary btn-sm nav-link">ثبت درآمد</a>
            <form method="POST" action="?action=reset_section" class="inline-form" onsubmit="return confirm('آیا از حذف تمام درآمدها مطمئن هستید؟')">
                <input type="hidden" name="section" value="income">
                <button type="submit" class="btn btn-danger btn-sm">ریست</button>
            </form>
        </div>
    </div>
    <table>
        <tr><th>تاریخ</th><th>سرفصل</th><th>مبلغ</th><th>توضیحات</th></tr>
        <?php foreach (array_reverse($income) as $i):
            $at = ''; foreach ($accounts as $a) if ($a['id'] == $i['account_id']) $at = $a['title'];
        ?>
        <tr><td><?= e($i['date']) ?></td><td><?= e($at) ?></td><td><?= format_money($i['amount']) ?></td><td><?= e($i['description']) ?></td></tr>
        <?php endforeach; ?>
        <?php if (empty($income)): ?><tr><td colspan="4" class="text-center">درآمدی ثبت نشده است</td></tr><?php endif; ?>
    </table>
</div>
