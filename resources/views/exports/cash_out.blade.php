<table>
    <thead>
    <tr>
        <th>序号</th>
        <th>支付单号</th>
        <th>微信昵称</th>
        <th>真实姓名</th>
        <th>手机号</th>
        <th>身份证号</th>
        <th>提现金额</th>
        <th>已支付金额</th>
        <th>个税金额</th>
        <th>状态</th>
        <th>微信支付业务单号</th>
        <th>申请时间</th>
        <th>付款时间</th>
        <th>审核人</th>
        <th>审核时间</th>
    </tr>
    </thead>
    <tbody>
    @foreach($recordList as $idx => $item)
        <tr>
            <td>{{ ++$idx }}</td>
            <td>{{ $item['trade_no'] }}</td>
            <td>{{ $item['nick_name'] }}</td>
            <td>{{ $item['real_name'] }}</td>
            <td>{{ !empty($item['mobile']) ? '`' . $item['mobile'] : '' }}</td>
            <td>{{ !empty($item['id_card']) ? '`' . $item['id_card'] : '' }}</td>
            <td>{{ $item['amount_total'] }}</td>
            <td>{{ $item['amount_payment'] }}</td>
            <td>{{ $item['person_taxes'] }}</td>
            <td>{{ $item['status_desc'] }}</td>
            <td>{{ !empty($item['payment_no']) ? '`' .$item['payment_no'] : '' }}</td>
            <td>{{ $item['created_at'] }}</td>
            <td>{{ $item['payment_time'] }}</td>
            <td>{{ $item['auditor_name'] }}</td>
            <td>{{ $item['audit_time'] }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
