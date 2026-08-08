<div class="card">
    <div class="card-title"><span>ایجاد دوره مالی جدید</span></div>
    <form method="POST" action="?action=save_period">
        <div class="form-grid">
            <div class="form-group"><label>نام دوره</label><input type="text" name="name" required></div>
            <div class="form-group"><label>تاریخ شروع</label><input type="text" name="start_date" placeholder="1403/01/01" required></div>
            <div class="form-group"><label>تاریخ پایان</label><input type="text" name="end_date" placeholder="1403/12/29" required></div>
            <div class="form-group"><label>دوره فعال</label><select name="is_current"><option value="1">بله</option><option value="0">خیر</option></select></div>
        </div>
        <button type="submit" class="btn btn-primary">ذخیره</button>
        <a data-action="periods" class="btn nav-link">انصراف</a>
    </form>
</div>
