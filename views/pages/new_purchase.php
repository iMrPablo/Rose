<div class="card">
    <div class="card-title"><span>ثبت فاکتور خرید</span></div>
    <form method="POST" action="?action=save_purchase">
        <div class="form-grid">
            <div class="form-group"><label>تاریخ</label><input type="text" name="date" value="<?= today() ?>" required></div>
            <div class="form-group"><label>تأمین‌کننده</label><select name="supplier_id" required><?php foreach ($suppliers as $s): ?><option value="<?= $s['id'] ?>"><?= e($s['name']) ?></option><?php endforeach; ?></select></div>
        </div>
        <div style="margin: 18px 0 10px; font-size: 14px; color: var(--text-light); font-weight: 500">اقلام فاکتور</div>
        <table id="purchaseItems">
            <thead><tr><th>نام کالا</th><th>تعداد</th><th>قیمت واحد</th><th>مبلغ</th><th></th></tr></thead>
            <tbody>
                <tr>
                    <td><input type="text" name="product_name[]" required style="width:100%;padding:10px;border:1px solid var(--border);border-radius:4px"></td>
                    <td><input type="number" name="qty[]" step="0.01" value="1" required class="qty" style="width:100%;padding:10px;border:1px solid var(--border);border-radius:4px"></td>
                    <td><input type="number" name="price[]" step="0.01" value="0" required class="price" style="width:100%;padding:10px;border:1px solid var(--border);border-radius:4px"></td>
                    <td class="amount">0</td>
                    <td><button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()">×</button></td>
                </tr>
            </tbody>
            <tfoot>
                <tr><td colspan="3"><button type="button" class="btn btn-success btn-sm" onclick="addPurchaseRow()">+ سطر</button></td><th>جمع کل:</th><td id="purchaseTotal">0</td></tr>
            </tfoot>
        </table>
        <div class="form-group mt-2"><label>توضیحات</label><input type="text" name="description"></div>
        <button type="submit" class="btn btn-primary">ثبت و صدور سند</button>
        <a data-action="purchases" class="btn nav-link">انصراف</a>
    </form>
</div>
