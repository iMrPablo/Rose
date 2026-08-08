<div class="stats">
    <div class="stat success"><div class="stat-label">موجودی نقد</div><div class="stat-value"><?= format_money($total_cash) ?></div></div>
    <div class="stat"><div class="stat-label">موجودی بانک</div><div class="stat-value"><?= format_money($total_bank) ?></div></div>
    <div class="stat warning"><div class="stat-label">حساب‌های دریافتنی</div><div class="stat-value"><?= format_money($total_receivable) ?></div></div>
    <div class="stat danger"><div class="stat-label">حساب‌های پرداختنی</div><div class="stat-value"><?= format_money($total_payable) ?></div></div>
    <div class="stat"><div class="stat-label">تعداد اسناد</div><div class="stat-value"><?= count($vouchers) ?></div></div>
    <div class="stat warning"><div class="stat-label">چک‌های در جریان</div><div class="stat-value"><?= $pending_checks ?></div></div>
</div>

<div class="card">
    <div class="card-title"><span>اطلاعات دوره مالی</span></div>
    <?php if ($period): ?>
    <table>
        <tr><th style="width:200px">نام دوره</th><td><?= e($period['name']) ?></td></tr>
        <tr><th>تاریخ شروع</th><td><?= e($period['start_date']) ?></td></tr>
        <tr><th>تاریخ پایان</th><td><?= e($period['end_date']) ?></td></tr>
        <tr><th>وضعیت</th><td><span class="badge badge-<?= $period['is_closed']?'danger':'success' ?>"><?= $period['is_closed']?'بسته':'باز' ?></span></td></tr>
    </table>
    <?php else: ?><p>دوره مالی فعالی تعریف نشده است.</p><?php endif; ?>
</div>

<div class="card">
    <div class="card-title">
        <span>آخرین اسناد</span>
        <div class="card-title-actions">
            <a data-action="vouchers" class="btn btn-sm nav-link">مشاهده همه</a>
        </div>
    </div>
    <table>
        <tr><th>شماره</th><th>تاریخ</th><th>توضیحات</th><th>مبلغ</th><th>وضعیت</th></tr>
        <?php
        $recent = array_slice(array_reverse($vouchers), 0, 5);
        foreach ($recent as $v):
        ?>
        <tr>
            <td><?= $v['voucher_no'] ?></td>
            <td><?= e($v['date']) ?></td>
            <td><?= e($v['description']) ?></td>
            <td><?= format_money($v['total_debit']) ?></td>
            <td><span class="badge badge-<?= $v['status']==='auto'?'info':'success' ?>"><?= e($v['status']) ?></span></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($recent)): ?><tr><td colspan="5" class="text-center">سندی ثبت نشده است</td></tr><?php endif; ?>
    </table>
</div>

<div class="card">
    <div class="card-title">
        <span>ریست سریع سیستم</span>
    </div>
    <p style="color: var(--text-light); margin-bottom: 15px">با ریست سیستم، تمامی اطلاعات وارد شده (اسناد، تراکنش‌ها، مشتریان، تأمین‌کنندگان و...) حذف می‌شود. ساختار سرفصل‌ها، دوره‌ها و تنظیمات باقی می‌ماند.</p>
    <form method="POST" action="?action=reset_section" onsubmit="return confirm('آیا از ریست کل سیستم مطمئن هستید؟ این عمل قابل بازگشت نیست.')">
        <input type="hidden" name="section" value="all">
        <button type="submit" class="btn btn-danger">ریست کامل سیستم</button>
    </form>
</div>
