<div class="card">
    <div class="card-title">
        <span>دوره‌های مالی</span>
        <div class="card-title-actions">
            <a data-action="new_period" class="btn btn-primary btn-sm nav-link">دوره جدید</a>
        </div>
    </div>
    <table>
        <tr><th>نام</th><th>از تاریخ</th><th>تا تاریخ</th><th>فعال</th><th>وضعیت</th><th>عملیات</th></tr>
        <?php foreach ($periods as $p): ?>
        <tr>
            <td><?= e($p['name']) ?></td>
            <td><?= e($p['start_date']) ?></td>
            <td><?= e($p['end_date']) ?></td>
            <td><?= $p['is_current']?'<span class="badge badge-success">بله</span>':'<span class="badge badge-warning">خیر</span>' ?></td>
            <td><?= $p['is_closed']?'<span class="badge badge-danger">بسته</span>':'<span class="badge badge-success">باز</span>' ?></td>
            <td>
                <?php if (!$p['is_closed']): ?>
                <form method="POST" action="?action=close_period" class="inline-form" onsubmit="return confirm('آیا از بستن دوره اطمینان دارید؟')">
                    <input type="hidden" name="period_id" value="<?= $p['id'] ?>">
                    <button type="submit" class="btn btn-danger btn-sm">بستن دوره</button>
                </form>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
