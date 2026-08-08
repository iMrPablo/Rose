<div class="card">
    <div class="card-title"><span>ثبت مشتری جدید</span></div>
    <form method="POST" action="?action=save_customer">
        <div class="form-grid">
            <div class="form-group"><label>کد مشتری</label><input type="text" name="code" required></div>
            <div class="form-group"><label>نام</label><input type="text" name="name" required></div>
            <div class="form-group"><label>تلفن</label><input type="text" name="phone"></div>
            <div class="form-group"><label>کد ملی</label><input type="text" name="national_id"></div>
            <div class="form-group"><label>آدرس</label><input type="text" name="address"></div>
        </div>
        <button type="submit" class="btn btn-primary">ذخیره</button>
        <a data-action="customers" class="btn nav-link">انصراف</a>
    </form>
</div>
