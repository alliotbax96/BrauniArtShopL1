// const centrifuge = new Centrifuge("wss://server.brauniart.shop:8000/connection/websocket", {
// 	token: "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiJicmF1bmlhcnQiLCJleHAiOjE3Mzg3NDQ4ODgsImlhdCI6MTczODE0MDA4OH0.u0OxdLX-FoDLWtpaWkGQNHjv_3P4KSINFx6_YaqccrI"
// });
//
// centrifuge.on('connecting', function (ctx) {
// 	console.log(`connecting: ${ctx.code}, ${ctx.reason}`);
// }).on('connected', function (ctx) {
// 	console.log(`connected over ${ctx.transport}`);
// }).on('disconnected', function (ctx) {
// 	console.log(`disconnected: ${ctx.code}, ${ctx.reason}`);
// }).connect();
//
// const sub = centrifuge.newSubscription("channel");
//
// sub.on('publication', function (ctx) {
// 	if ($("#cart_id").val() == ctx.data.cartid) {
// 		window.location.replace('/order/' + ctx.data.payd);
// 	}
// }).on('subscribing', function (ctx) {
// 	console.log(`subscribing: ${ctx.code}, ${ctx.reason}`);
// }).on('subscribed', function (ctx) {
// 	console.log('subscribed', ctx);
// }).on('unsubscribed', function (ctx) {
// 	console.log(`unsubscribed: ${ctx.code}, ${ctx.reason}`);
// }).subscribe();

$("#checkout").on("click", function () {
	$.post('/add_order', { pay_type: $("input[name=\"pay_type\"]:checked").val() }, function (data) {
        var data = JSON.parse(data);
        window.open(data.invoice, '_blank');
        window.location.replace('/order/'+data.order_id);
	});
});