<div class="card">
    <div class="card-title"><span>ترازنامه</span><button onclick="window.print()" class="btn btn-sm">چاپ</button></div>
    <div style="margin: 20px 0 10px; font-weight: 500; color: var(--primary)">دارایی‌ها</div>
    <table>
        <?php $ta = 0; foreach ($assets as $a): $n = $a['debit'] - $a['credit']; $ta += $n; ?>
        <tr><td><?= e($a['title']) ?></td><td><?= format_money($n) ?></td></tr>
        <?php endforeach; ?>
        <tr style="background: var(--bg); font-weight: bold"><td>جمع دارایی‌ها</td><td><?= format_money($ta) ?></td></tr>
    </table>
    <div style="margin: 20px 0 10px; font-weight: 500; color: var(--primary)">بدهی‌ها</div>
    <table>
        <?php $tl = 0; foreach ($liabilities as $l): $n = $l['credit'] - $l['debit']; $tl += $n; ?>
        <tr><td><?= e($l['title']) ?></td><td><?= format_money($n) ?></td></tr>
        <?php endforeach; ?>
        <tr style="background: var(--bg); font-weight: bold"><td>جمع بدهی‌ها</td><td><?= format_money($tl) ?></td></tr>
    </table>
    <div style="margin: 20px 0 10px; font-weight: 500; color: var(--primary)">حقوق صاحبان سهام</div>
    <table>
        <?php $te = 0; foreach ($equity as $eq): $n = $eq['credit'] - $eq['debit']; $te += $n; ?>
        <tr><td><?= e($eq['title']) ?></td><td><?= format_money($n) ?></td></tr>
        <?php endforeach; ?>
        <tr style="background: var(--bg); font-weight: bold"><td>جمع حقوق صاحبان سهام</td><td><?= format_money($te) ?></td></tr>
    </table>
</div>
