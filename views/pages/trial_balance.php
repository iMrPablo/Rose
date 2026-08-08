<div class="card">
    <div class="card-title"><span>تراز آزمایشی</span><button onclick="window.print()" class="btn btn-sm">چاپ</button></div>
    <table>
        <tr><th>کد</th><th>عنوان</th><th>بدهکار</th><th>بستانکار</th></tr>
        <?php
        $td = 0; $tc = 0;
        foreach ($balances as $b):
            if ($b['debit'] == 0 && $b['credit'] == 0) continue;
            $td += $b['debit']; $tc += $b['credit'];
        ?>
        <tr><td><?= e($b['code']) ?></td><td><?= e($b['title']) ?></td><td><?= format_money($b['debit']) ?></td><td><?= format_money($b['credit']) ?></td></tr>
        <?php endforeach; ?>
        <tr style="background: var(--bg); font-weight: bold"><td colspan="2">جمع کل</td><td><?= format_money($td) ?></td><td><?= format_money($tc) ?></td></tr>
    </table>
</div>
