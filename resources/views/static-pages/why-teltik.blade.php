@extends('layouts.app')

@section('content')

<section class="whyTeltik">

    <div class="wrapper">

        <h1>Why Teltik?</h1>
        <p>Because today's world relies on safety, communications and connectivity, the relationship between them is taking on a whole new meaning. It's not just about telephony anymore. It's about keeping tabs on family members and balancing work all while on the move, extending far beyond the confines of a home.</p>
        <p>All plans are bundled with the family safety service OneTouch GPS. We take family safety to a whole new level. Meet the OneTouchGPS Family Safety service. Enjoy total peace of mind for your family knowing help is always just a touch away included with all plans. OneTouch GPS ensures total peace of mind designed for modern life, including a full range of support from live agents, certified specialists, and 24/7. Coupled with a mobility partner, using a solid 4G / 5G network infrastructure, Teltik Communications is the answer to all your telecommunication needs.</p>
        <h1>What we offer:</h1>
        <p>Creating your Teltik account and placing your first order, gives you a special telecommunications business package - a bundle of two powerful and cost-effective services.</p>
        <h2>1. Wireless Service with Unlimited Talk, Text, and Data</h2>
        <p>Using the powerful nationwide 4G LTE / 5G network, your always connected. Prices start as low as $20.00 a month, with no contract. We assure you, with this service, you could not be happier.

            Every wireless service plan comes with unlimited talk, text, and data and also includes the following features:</p>
        <ul>
            <li>Unlimited access to the nationwide network</li>
            <li>Use your device as an internet hotspot</li>
            <li>Unlimited calls to Canada and Mexico at no additional charge</li>
            <li>The ability to port in your existing number from any other carrier</li>
            <li>All basic features like voicemail, 3-way calling, etc.</li>
            <li>The service will work on most unlocked GSM-network phones.</li>
            <li>And again, all at the lowest price-point in the country</li>

        </ul>
        <h3>Sample Pricing:</h3>
        <p><br> All plans include unlimited talk, text, and data, as well as all features mentioned above. Amount of 4G LTE data is subject to the package you choose. However, after you run out of your allotted amount, your data speeds will simply be throttled to slower speeds
            for the duration of that billing cycle.</p>
        <div>
            <ul>
                <li>2GB of 4G LTE data - $20.00</li>
                <li>6GB of 4G LTE data - $30.00</li>
                <li>UNLIMITED 4G LTE data - $40.00</li>
            </ul>
            * All prices listed are before tax.</div>
            
        <h1>Ready. Set. Mobilize.</h1>

    </div>

</section>
<!-- end content -->

<!-- FOOTER -->

<div class="map">
	<div class="close-map">x</div>
	<iframe src="https://maps.t-mobile.com/" frameborder="0"></iframe>
</div>


<div class="overlay">&nbsp;</div>

<div id="cart-drop-mobile">
	<a href="#" class="btn style1 btn-cart">
		<i class="fa fa-shopping-cart"></i>
		Your Cart (1)
	</a>
	<a href="#" class="btn style3 place-order-btn">Place Order</a>
	<div class="drop-con">
		
		<strong>Selected Solutions</strong>

		<ul class="cart-list">
			<li>
				<div class="img-wrap"></div>
				<div class="info">
					<table>
						<tr>
							<td>Device: iPhoneX</td>
							<td><strong>$340</strong></td>
						</tr>
						<tr>
							<td>Plan: <strong>N/A</strong></td>
							<td><strong>--</strong></td>
						</tr>
						<tr>
							<td>Sim Card: <strong>N/A</strong></td>
							<td><strong>--</strong></td>
						</tr>
						<tr>
							<td>Add-Ons: <strong>N/A</strong></td>
							<td><strong>--</strong></td>
						</tr>
					</table>
				</div>
				<div class="clearfix"></div>
				<div class="btn-set-action">
					<div class="text-right">
						<a href="#">
							<i class="fa fa-pencil-alt"></i>
							Edit
						</a>
					</div>
					<div class="text-left">
						<a href="#">
							<i class="fa fa-trash-alt"></i>
							Remove
						</a>
					</div>
				</div>
			</li>
		</ul>

		<div class="summary">
			<table>
				<tr>
					<td>Subtotal:</td>
					<td>$69.95</td>
				</tr>
				<tr>
					<td>Shipping:</td>
					<td>$0.00</td>
				</tr>
				<tr>
					<td>Coupons:</td>
					<td>-$15.00</td>
				</tr>
				<tr>
					<td>Tax/Fees:</td>
					<td>$7.95</td>
				</tr>
			</table>
		</div>

		<div class="total">
			<table>
				<tr>
					<td>Account Credits</td>
					<td>-$27.00</td>
				</tr>
				<tr>
					<td><strong>Total Due Today</strong></td>
					<td><strong>$87.95</strong></td>
				</tr>
			</table>
		</div>

		<a href="#" class="btn">Place Order</a>

	</div>
</div>

@endsection

@push('js')

{!! Html::script('https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js') !!}
{!! Html::script('js/jquery.marquee.min.js') !!}
{!! Html::script('js/bootstrap.min.js') !!}
{!! Html::script('js/functions.min.js') !!}
{!! Html::script('js/main.js') !!}

@endpush


<!-- end FOOTER -->

