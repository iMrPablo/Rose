<div class="card">
    <div class="card-title"><span>صورت سود و زیان</span><button onclick="window.print()" class="btn btn-sm">چاپ</button></div>
    <div style="margin: 20px 0 10px; font-weight: 500; color: var(--primary)">درآمدها</div>
    <table>
        <?php $ti = 0; foreach ($incomes as $i): $n = $i['credit'] - $i['debit']; $ti += $n; ?>
        <tr><td><?= e($i['title']) ?></td><td><?= format_money($n) ?></td></tr>
        <?php endforeach; ?>
        <tr style="background: var(--success-light); font-weight: bold"><td>جمع درآمدها</td><td><?= format_money($ti) ?></td></tr>
    </table>
    <div style="margin: 20px 0 10px; font-weight: 500; color: var(--primary)">هزینه‌ها</div>
    <table>
        <?php $te = 0; foreach ($expenses as $e): $n = $e['debit'] - $e['credit']; $te += $n; ?>
        <tr><td><?= e($e['title']) ?></td><td><?= format_money($n) ?></td></tr>
        <?php endforeach; ?>
        <tr style="background: var(--danger-light); font-weight: bold"><td>جمع هزینه‌ها</td><td><?= format_money($te) ?></td></tr>
    </table>
    <table style="margin-top: 20px; background: <?= ($ti - $te) >= 0 ? 'var(--success-light)' : 'var(--danger-light)' ?>">
        <tr style="font-weight: bold; font-size: 16px"><td>سود (زیان) خالص</td><td><?= format_money($ti - $te) ?></td></tr>
    </table>
</div>
