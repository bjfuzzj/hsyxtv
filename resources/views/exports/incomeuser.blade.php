<table>
    <thead>
    <tr>
        <th>用户id</th>
        <th>用户昵称</th>
        <th>月份</th>
        <th>下单时间</th>
        <th>订单金额</th>
        <th>分成金额</th>
        <th>状态</th>
    </tr>
    </thead>
    <tbody>
    @foreach($users as $user)
        <tr>
            <td>{{$user['ouid']}}</td>
            <td>{{ $user['ounick_name'] }}</td>
            <td>{{ $user['month'] }}</td>
            <td>{{ $user['order_time'] }}</td>
            <td>{{ $user['amount_paid'] }}</td>
            <td>{{ $user['rebate_amount'] }}</td>
            <td>{{ $user['status_desc'] }}</td>
        </tr>
    @endforeach
    </tbody>
</table>