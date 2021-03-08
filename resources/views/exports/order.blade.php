<table>
    <thead>
    <tr>
        <th>物流导入编号</th>
        <th>收件人详细地址</th>
        <th>收件人姓名</th>
        <th>收件人手机</th>
        <th>订单编号</th>
        <th>商品名称</th>
        <th>销售规格</th>
        <th>商品编码</th>
        <th>数量</th>
        <th>商品金额</th>
        <th>代收货款</th>
        <th>寄件公司</th>
        <th>寄件人姓名</th>
        <th>寄件人手机</th>
        <th>寄件人详细地址</th>
        <th>卖家备注</th>
        <th>运费</th>
    </tr>
    </thead>
    <tbody>
    @foreach($orders as $order)
        <tr>
            <td>{{ $order['logistic_no']}}</td>
            <td>{{ $order['address'] }}</td>
            <td>{{ $order['name'] }}</td>
            <td>{{ $order['phone'] }}</td>
            <td>{{ $order['oid'] }}</td>
            <td>{{ $order['title'] }}</td>
            <td>规格</td>
            <td>{{ $order['product_id'] }}</td>
            <td>1</td>
            <td>{{ $order['price'] }}</td>
            <td>10</td>
            <td>深圳同柑科技农业发展有限公司</td>
            <td>陆旭林</td>
            <td>15678165640</td>
            <td>广西南宁京东快递仓</td>
            <td>{{$order['beizhu']}}</td>
            <td></td>
        </tr>
    @endforeach
    </tbody>
</table>