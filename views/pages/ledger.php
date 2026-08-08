<div class="card">
    <div class="card-title"><span>دفتر کل</span></div>
    <form method="GET" style="margin-bottom: 20px">
        <input type="hidden" name="action" value="ledger">
        <select name="account_id" onchange="this.form.submit()" style="padding: 12px 16px; border: 1px solid var(--border); border-radius: 6px; min-width: 300px">
            <option value="">انتخاب حساب</option>
            <?php foreach ($leaf_accounts as $a): ?>
            <option value="<?= $a['id'] ?>" <?= $selected==$a['id']?'selected':'' ?>><?= e($a['code']) ?> - <?= e($a['title']) ?></option>
            <?php endforeach; ?>
        </select>
    </form>
    <?php if ($selected):
        $account = null;
        foreach ($leaf_accounts as $a) if ($a['id'] == $selected) $account = $a;
        $entries = [];
        foreach ($vouchers as $v) foreach ($v['items'] as $item) if ($item['account_id'] == $selected) {
            $entries[] = ['date' => $v['date'], 'voucher_no' => $v['voucher_no'], 'description' => $item['description'] ?: $v['description'], 'debit' => $item['debit'], 'credit' => $item['credit']];
        }
    ?>
    <div style="margin: 20px 0 10px; font-weight: 500; color: var(--primary)"><?= e($account['title']) ?></div>
    <table>
        <tr><th>تاریخ</th><th>شماره سند</th><th>توضیحات</th><th>بدهکار</th><th>بستانکار</th><th>مانده</th></tr>
        <?php $balance = 0; foreach ($entries as $e): $balance += $e['debit'] - $e['credit']; ?>
        <tr><td><?= e($e['date']) ?></td><td><?= $e['voucher_no'] ?></td><td><?= e($e['description']) ?></td><td><?= format_money($e['debit']) ?></td><td><?= format_money($e['credit']) ?></td><td><?= format_money($balance) ?></td></tr>
        <?php endforeach; ?>
    </table>
    <?php endif; ?>
</div>
