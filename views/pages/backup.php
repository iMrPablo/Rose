<div class="card">
    <div class="card-title"><span>پشتیبان‌گیری</span></div>
    <p style="color: var(--text-light); margin-bottom: 15px">تمامی اطلاعات سیستم در یک فایل JSON ذخیره می‌شود.</p>
    <form method="POST" action="?action=backup">
        <button type="submit" class="btn btn-primary">دانلود پشتیبان</button>
    </form>
</div>
<div class="card">
    <div class="card-title"><span>بازیابی اطلاعات</span></div>
    <p style="color: var(--danger); margin-bottom: 15px">⚠️ بازیابی اطلاعات، تمام داده‌های فعلی را جایگزین می‌کند.</p>
    <form method="POST" action="?action=restore" enctype="multipart/form-data">
        <div class="form-group"><label>فایل پشتیبان (JSON)</label><input type="file" name="backup_file" accept=".json" required></div>
        <button type="submit" class="btn btn-danger" onclick="return confirm('آیا مطمئن هستید؟')">بازیابی</button>
    </form>
</div>
