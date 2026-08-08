<div class="card">
    <div class="card-title">
        <span>سرفصل‌ها و کدینگ حسابداری</span>
        <div class="card-title-actions">
            <a data-action="new_account" class="btn btn-primary btn-sm nav-link">سرفصل جدید</a>
            <form method="POST" action="?action=reset_section" class="inline-form" onsubmit="return confirm('آیا از حذف تمام سرفصل‌ها مطمئن هستید؟ این عمل بسیار خطرناک است!')">
                <input type="hidden" name="section" value="accounts">
                <button type="submit" class="btn btn-danger btn-sm">ریست سرفصل‌ها</button>
            </form>
        </div>
    </div>
    <table>
        <tr><th>کد</th><th>عنوان</th><th>نوع</th><th>سطح</th></tr>
        <?php foreach ($accounts as $a): ?>
        <tr>
            <td><?= e($a['code']) ?></td>
            <td style="padding-right: <?= ($a['level']-1)*20 + 12 ?>px"><?= e($a['title']) ?></td>
            <td><span class="badge badge-<?= $a['type']==='account'?'success':($a['type']==='group'?'info':'warning') ?>"><?= e($a['type']) ?></span></td>
            <td><?= $a['level'] ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
