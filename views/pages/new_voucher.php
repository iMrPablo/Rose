<div class="card">
    <div class="card-title"><span>ثبت سند حسابداری جدید</span></div>
    <form method="POST" action="?action=save_voucher">
        <div class="form-grid">
            <div class="form-group"><label>تاریخ</label><input type="text" name="date" value="<?= today() ?>" placeholder="1403/01/01" required></div>
            <div class="form-group"><label>توضیحات سند</label><input type="text" name="description_main" required></div>
        </div>
        <div style="margin: 20px 0 12px; font-size: 14px; color: var(--text-light); font-weight: 500">سطرهای سند</div>
        <table id="voucherItems">
            <thead><tr><th>سرفصل</th><th>توضیحات</th><th>بدهکار</th><th>بستانکار</th><th style="width:50px"></th></tr></thead>
            <tbody>
                <?php for ($i = 0; $i < 2; $i++): ?>
                <tr>
                    <td><select name="account_id[]" required style="width:100%;padding:10px;border:1px solid var(--border);border-radius:4px"><option value="">انتخاب کنید</option><?php foreach ($leaf_accounts as $a): ?><option value="<?= $a['id'] ?>"><?= e($a['code']) ?> - <?= e($a['title']) ?></option><?php endforeach; ?></select></td>
                    <td><input type="text" name="description[]" style="width:100%;padding:10px;border:1px solid var(--border);border-radius:4px"></td>
                    <td><input type="number" name="debit[]" step="0.01" min="0" class="debit-input" style="width:100%;padding:10px;border:1px solid var(--border);border-radius:4px"></td>
                    <td><input type="number" name="credit[]" step="0.01" min="0" class="credit-input" style="width:100%;padding:10px;border:1px solid var(--border);border-radius:4px"></td>
                    <td><button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove();calcTotal()">×</button></td>
                </tr>
                <?php endfor; ?>
            </tbody>
            <tfoot>
                <tr><td colspan="5"><button type="button" class="btn btn-success btn-sm" onclick="addRow()">+ سطر جدید</button></td></tr>
                <tr style="background: var(--bg)"><th>جمع بدهکار</th><td id="totalDebit">0</td><th>جمع بستانکار</th><td id="totalCredit">0</td><th>اختلاف: <span id="diff">0</span></th></tr>
            </tfoot>
        </table>
        <div class="mt-2">
            <button type="submit" class="btn btn-primary">ثبت سند</button>
            <a data-action="vouchers" class="btn nav-link">انصراف</a>
        </div>
    </form>
</div>
