<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wholesale Order Placed Successfully</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f6f9;">

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f6f9; padding: 40px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="650" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">

                    {{-- Header --}}
                    <tr>
                        <td style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%); padding: 40px 30px; text-align: center;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 28px; font-weight: 700; letter-spacing: 1px;">
                                {{ config('app.name', 'EAY Sports') }}
                            </h1>
                            <p style="color: #a0c4ff; margin: 8px 0 0; font-size: 14px; letter-spacing: 0.5px;">
                                Wholesale Order Confirmation
                            </p>
                        </td>
                    </tr>

                    {{-- Body Intro --}}
                    <tr>
                        <td style="padding: 35px 35px 15px;">
                            <h2 style="color: #1a1a2e; margin: 0 0 12px; font-size: 22px;">
                                Thank You for Your Wholesale Order! 🎉
                            </h2>
                            <p style="color: #555; margin: 0; font-size: 15px; line-height: 1.7;">
                                Hi {{ $dealer->name }}, we've successfully received your B2B wholesale order <strong>#B2B-{{ $order->id }}</strong>. Our team is processing it and will update you shortly.
                            </p>
                        </td>
                    </tr>

                    {{-- Order Reference Card --}}
                    <tr>
                        <td style="padding: 10px 35px 15px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color: #f8f9fa; border-radius: 8px; border: 1px solid #e9ecef; padding: 15px 20px;">
                                <tr>
                                    <td>
                                        <p style="margin: 0; font-size: 14px; color: #555;">Order ID: <strong style="color: #1a1a2e;">#B2B-{{ $order->id }}</strong></p>
                                        <p style="margin: 4px 0 0; font-size: 14px; color: #555;">Status: <span style="background-color: #e3f2fd; color: #0d47a1; padding: 2px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">{{ $order->status }}</span></p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Shipping Address Card --}}
                    <tr>
                        <td style="padding: 10px 35px 20px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border: 1px solid #e9ecef; border-radius: 8px; padding: 20px;">
                                <tr>
                                    <td>
                                        <p style="color: #0f3460; font-weight: 700; font-size: 13px; margin: 0 0 8px; text-transform: uppercase;">Shipping Destination</p>
                                        <p style="margin: 0; font-size: 14px; color: #333;">{{ $order->shipping_address }}</p>
                                        <p style="margin: 4px 0 0; font-size: 14px; color: #333;">{{ $order->city }}, {{ $order->country }}</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Items Table --}}
                    <tr>
                        <td style="padding: 10px 35px 20px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse; width: 100%;">
                                <thead>
                                    <tr style="border-bottom: 2px solid #dee2e6;">
                                        <th align="left" style="padding: 10px 0; font-size: 13px; color: #495057; text-transform: uppercase;">Product</th>
                                        <th align="center" style="padding: 10px 0; font-size: 13px; color: #495057; text-transform: uppercase; width: 60px;">Qty</th>
                                        <th align="right" style="padding: 10px 0; font-size: 13px; color: #495057; text-transform: uppercase; width: 100px;">Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->items as $item)
                                        <tr style="border-bottom: 1px solid #eee;">
                                            <td style="padding: 12px 0; vertical-align: top;">
                                                <p style="margin: 0; font-size: 14px; font-weight: 600; color: #1a1a2e;">{{ $item->product_name }}</p>
                                                <p style="margin: 2px 0 0; font-size: 12px; color: #666;">
                                                    @if($item->size) Size: {{ $item->size }} | @endif
                                                    @if($item->custom_name) Name: {{ $item->custom_name }} | @endif
                                                    @if($item->custom_number) Number: {{ $item->custom_number }} @endif
                                                </p>
                                            </td>
                                            <td align="center" style="padding: 12px 0; font-size: 14px; color: #333; vertical-align: top;">
                                                {{ $item->qty }}
                                            </td>
                                            <td align="right" style="padding: 12px 0; font-size: 14px; color: #333; font-weight: 600; vertical-align: top;">
                                                ${{ number_format($item->unit_price * $item->qty, 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </td>
                    </tr>

                    {{-- Summary Calculations --}}
                    <tr>
                        <td style="padding: 10px 35px 30px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color: #f8f9fa; border-radius: 8px; padding: 20px;">
                                <tr style="border-top: 1px solid #ddd;">
                                    <td style="font-size: 16px; font-weight: 700; color: #1a1a2e; padding: 5px 0 0;">Total wholesale value:</td>
                                    <td align="right" style="font-size: 16px; font-weight: 700; color: #0f3460; padding: 5px 0 0;">${{ number_format($order->total_price, 2) }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Back to Portal Link --}}
                    <tr>
                        <td align="center" style="padding: 0 35px 35px;">
                            <a href="{{ url('/dealer/orders') }}"
                               style="display: inline-block; background: linear-gradient(135deg, #0f3460, #1a1a2e); color: #ffffff; padding: 14px 40px; border-radius: 8px; text-decoration: none; font-size: 15px; font-weight: 600; letter-spacing: 0.5px;">
                                View Wholesale Orders →
                            </a>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background-color: #f8f9fb; padding: 25px 35px; text-align: center; border-top: 1px solid #eee;">
                            <p style="color: #999; margin: 0; font-size: 13px;">
                                This is an automated wholesale invoice notification. If you have questions, please reach out to your account manager.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>
