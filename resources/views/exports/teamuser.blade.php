<table>
    <thead>
    <tr>
        <th>用户昵称</th>
        <th>用户头像</th>
        <th>总贡献</th>
        <th>加入团队时间</th>
    </tr>
    </thead>
    <tbody>
    @foreach($users as $user)
        <tr>
            <td>{{$user['nick_name']}}</td>
            <td>{{ $user['head_image'] }}</td>
            <td>{{ $user['total_rebate'] }}</td>
            <td>{{ $user['join_time'] }}</td>
        </tr>
    @endforeach
    </tbody>
</table>