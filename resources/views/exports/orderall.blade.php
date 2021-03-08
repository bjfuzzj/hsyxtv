<table>
    <thead>
    <tr>
        <th>收件人详细地址</th>
        <th>收件人姓名</th>
        <th>收件人电话</th>
        <th>订单编号</th>
        <th>商品名称</th>
        <th>销售规格</th>
        <th>商品编码</th>
        <th>数量</th>
        <th>单价</th>
        <th>实付金额</th>
        <th>用户昵称</th>
        <th>下单时间</th>
        <th>支付时间</th>
        <th>付款方式</th>
        <th>确认收货时间</th>
        <th>发货状态</th>
        <th>快递公司</th>
        <th>快递单号</th>
        <th>一级昵称</th>
        <th>一级佣金</th>
        <th>二级昵称</th>
        <th>二级佣金</th>
        <th>自购返利</th>
        <th>买家备注</th>
        <th>卖家备注</th>
    </tr>
    </thead>
    <tbody>
    @foreach($orders as $order)
        <tr>
            <td>{{ $order['address'] }}</td>
            <td>{{ $order['name'] }}</td>
            <td>{{ $order['phone'] }}</td>
            <td>{{ $order['oid'] }}</td>
            <td>{{ $order['title'] }}</td>
            <td>规格</td>
            <td>{{ $order['product_id'] }}</td>
            <td>{{ $order['buy_num'] }}</td>
            <td>{{ $order['price'] }}</td>
            <td>{{ $order['amount_paid'] }}</td>
            <td>{{ $order['nick_name'] }}</td>
            <td>{{ $order['create_time'] }}</td>
            <td>{{ $order['paid_time'] }}</td>
            <td>微信</td>
            <td></td>
            <td>{{$order['status_desc']}}</td>
            <td>{{$order['express_name']}}</td>
            <td>{{$order['express_no']}}</td>
            <td>{{$order['yiji_name']}}</td>
            <td>{{$order['yiji_mount']}}</td>
            <td>0</td>
            <td>0</td>
            <td>{{$order['zigou_mount']}}</td>
            <td>{{$order['remark']}}</td>
            <td>{{$order['adminnote']}}</td>
        </tr>
    @endforeach
    </tbody>
</table>