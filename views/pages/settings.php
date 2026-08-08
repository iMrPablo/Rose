<div class="card">
    <div class="card-title"><span>تنظیمات سیستم</span></div>
    <form method="POST" action="?action=settings">
        <div class="form-grid">
            <div class="form-group"><label>نام شرکت</label><input type="text" name="company_name" value="<?= e($settings['company_name'] ?? 'رز') ?>" required></div>
            <div class="form-group"><label>واحد پول</label><input type="text" name="currency" value="<?= e($settings['currency'] ?? 'ریال') ?>" required></div>
        </div>
        <button type="submit" class="btn btn-primary">ذخیره</button>
    </form>
</div>
<div class="card">
    <div class="card-title"><span>اطلاعات سیستم</span></div>
    <table>
        <tr><th style="width:200px">نسخه</th><td><?= e($settings['version'] ?? '01 بتا') ?></td></tr>
        <tr><th>زمان نصب</th><td><?= e($settings['installed_at'] ?? '-') ?></td></tr>
        <tr><th>نسخه PHP</th><td><?= phpversion() ?></td></tr>
    </table>
</div>
