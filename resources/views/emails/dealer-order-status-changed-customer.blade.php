<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Wholesale Order Status Has Been Updated</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f6f9;">

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f6f9; padding: 40px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">

                    {{-- Header --}}
                    <tr>
                        <td style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%); padding: 40px 30px; text-align: center;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 28px; font-weight: 700; letter-spacing: 1px;">
                                {{ config('app.name', 'EAY Sports') }}
                            </h1>
                            <p style="color: #a0c4ff; margin: 8px 0 0; font-size: 14px; letter-spacing: 0.5px;">
                                Wholesale Order Update
                            </p>
                        </td>
                    </tr>

                    {{-- Body Intro --}}
                    <tr>
                        <td style="padding: 35px 35px 15px;">
                            <h2 style="color: #1a1a2e; margin: 0 0 12px; font-size: 22px;">
                                Hi {{ $dealer->name }},
                            </h2>
                            <p style="color: #555; margin: 0; font-size: 15px; line-height: 1.7;">
                                The status of your B2B wholesale order <strong>#B2B-{{ $order->id }}</strong> has been updated by EAY Sports.
                            </p>
                        </td>
                    </tr>

                    {{-- Status Card --}}
                    <tr>
                        <td style="padding: 10px 35px 25px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color: #f0f4ff; border-radius: 10px; border: 1px solid #d4dffa;">
                                <tr>
                                    <td style="padding: 25px 30px;">
                                        <p style="color: #0f3460; margin: 0 0 5px; font-size: 12px; text-transform: uppercase; font-weight: 700; letter-spacing: 1px;">
                                            Current Order Status
                                        </p>
                                        <hr style="border: none; border-top: 1px solid #d4dffa; margin: 12px 0;">

                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="padding: 8px 0; color: #777; font-size: 14px; width: 120px;">New Status:</td>
                                                <td style="padding: 8px 0; color: #1a1a2e; font-size: 16px; font-weight: 700;">
                                                    <span style="background-color: #e3f2fd; color: #0d47a1; padding: 4px 12px; border-radius: 4px;">{{ $order->status }}</span>
                                                </td>
                                            </tr>
                                            @if($order->admin_note)
                                                <tr>
                                                    <td style="padding: 8px 0; color: #777; font-size: 14px; vertical-align: top;">Update Note:</td>
                                                    <td style="padding: 8px 0; color: #1a1a2e; font-size: 14px; line-height: 1.6; font-style: italic;">
                                                        "{{ $order->admin_note }}"
                                                    </td>
                                                </tr>
                                            @endif
                                            <tr>
                                                <td style="padding: 8px 0; color: #777; font-size: 14px;">Total Value:</td>
                                                <td style="padding: 8px 0; color: #1a1a2e; font-size: 15px; font-weight: 600;">
                                                    ${{ number_format($order->total_price, 2) }}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- View Order Button --}}
                    <tr>
                        <td align="center" style="padding: 5px 35px 30px;">
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
                                If you did not expect this update or have concerns, please contact your account manager.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>
