<div class="card">
    <div class="card-title"><span>گردش حساب اشخاص</span></div>
    <form method="GET" style="margin-bottom: 20px; display: flex; gap: 12px; flex-wrap: wrap">
        <input type="hidden" name="action" value="party_report">
        <select name="party_type" onchange="this.form.submit()" style="padding: 12px 16px; border: 1px solid var(--border); border-radius: 6px">
            <option value="">انتخاب نوع</option>
            <option value="customer" <?= $selected_type==='customer'?'selected':'' ?>>مشتری</option>
            <option value="supplier" <?= $selected_type==='supplier'?'selected':'' ?>>تأمین‌کننده</option>
        </select>
        <?php if ($selected_type): ?>
        <select name="party_id" onchange="this.form.submit()" style="padding: 12px 16px; border: 1px solid var(--border); border-radius: 6px">
            <option value="">انتخاب شخص</option>
            <?php
            $parties = $selected_type === 'customer' ? $customers : $suppliers;
            foreach ($parties as $p):
            ?>
            <option value="<?= $p['id'] ?>" <?= $selected_id==$p['id']?'selected':'' ?>><?= e($p['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <?php endif; ?>
    </form>
    <?php if ($selected_type && $selected_id):
        $party = null;
        $parties = $selected_type === 'customer' ? $customers : $suppliers;
        foreach ($parties as $p) if ($p['id'] == $selected_id) $party = $p;
        $entries = [];
        foreach ($transactions as $t) {
            if ($t['party_type'] === $selected_type && $t['party_id'] == $selected_id) {
                $entries[] = ['date' => $t['date'], 'description' => $t['description'], 'debit' => $t['type'] === 'payment' ? $t['amount'] : 0, 'credit' => $t['type'] === 'receipt' ? $t['amount'] : 0];
            }
        }
        usort($entries, fn($a, $b) => strcmp($a['date'], $b['date']));
    ?>
    <div style="margin: 20px 0 10px; font-weight: 500; color: var(--primary)"><?= e($party['name']) ?> - مانده فعلی: <?= format_money($party['balance']) ?></div>
    <table>
        <tr><th>تاریخ</th><th>توضیحات</th><th>بدهکار</th><th>بستانکار</th></tr>
        <?php foreach ($entries as $e): ?>
        <tr><td><?= e($e['date']) ?></td><td><?= e($e['description']) ?></td><td><?= format_money($e['debit']) ?></td><td><?= format_money($e['credit']) ?></td></tr>
        <?php endforeach; ?>
    </table>
    <?php endif; ?>
</div>
