<table>
    <thead>
    <tr>
        <th>id</th>
        <th>用户昵称</th>
        <th>真实姓名</th>
        <th>分销员等级</th>
        <th>分销员预售等级</th>
        <th>累计收入</th>
        <th>已提现收入</th>
        <th>可提现收入</th>
        <th>待结算收入</th>
        <th>个人所得税</th>
        <th>手机号</th>
        <th>上级昵称</th>
        <th>上级真实姓名</th>
        <th>加入时间</th>
    </tr>
    </thead>
    <tbody>
    @foreach($users as $user)
        <tr>
            <td>{{$user['id']}}</td>
            <td>{{ $user['nick_name'] }}</td>
            <td>{{ $user['real_name'] }}</td>
            <td>{{ $user['distributor_level'] }}</td>
            <td>{{ $user['advance_sale_level'] }}</td>
            <td>{{ $user['income_total'] }}</td>
            <td>{{ $user['income_used'] }}</td>
            <td>{{ $user['income_usable'] }}</td>
            <td>{{ $user['income_freeze'] }}</td>
            <td>{{ $user['person_taxes'] }}</td>
            <td>{{ $user['mobile'] }}</td>
            <td>{{ $user['d_nick_name'] }}</td>
            <td>{{ $user['d_real_name'] }}</td>
            <td>{{ $user['created_at'] }}</td>
        </tr>
    @endforeach
    </tbody>
</table>