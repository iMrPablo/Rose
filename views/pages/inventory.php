<div class="card">
    <div class="card-title">
        <span>موجودی انبار</span>
        <div class="card-title-actions">
            <form method="POST" action="?action=reset_section" class="inline-form" onsubmit="return confirm('آیا از ریست انبار مطمئن هستید؟')">
                <input type="hidden" name="section" value="inventory">
                <button type="submit" class="btn btn-danger btn-sm">ریست انبار</button>
            </form>
        </div>
    </div>
    <table>
        <tr><th>نام کالا</th><th>موجودی</th><th>میانگین قیمت</th></tr>
        <?php foreach ($inventory as $i): ?>
        <tr><td><?= e($i['name']) ?></td><td><?= format_money($i['qty']) ?></td><td><?= format_money($i['avg_price']) ?></td></tr>
        <?php endforeach; ?>
        <?php if (empty($inventory)): ?><tr><td colspan="3" class="text-center">کالایی در انبار موجود نیست</td></tr><?php endif; ?>
    </table>
</div>
