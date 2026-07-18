<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Contact Query Received</title>
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
                                New Contact Query Received
                            </p>
                        </td>
                    </tr>

                    {{-- Message Body --}}
                    <tr>
                        <td style="padding: 35px 35px 15px;">
                            <h2 style="color: #1a1a2e; margin: 0 0 12px; font-size: 22px;">
                                Hello Admin,
                            </h2>
                            <p style="color: #555; margin: 0; font-size: 15px; line-height: 1.7;">
                                A new query has been submitted via the contact form on EAY Sports storefront.
                            </p>
                        </td>
                    </tr>

                    {{-- Query Box --}}
                    <tr>
                        <td style="padding: 10px 35px 25px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color: #f0f4ff; border-radius: 10px; border: 1px solid #d4dffa;">
                                <tr>
                                    <td style="padding: 25px 30px;">
                                        <p style="color: #0f3460; margin: 0 0 5px; font-size: 12px; text-transform: uppercase; font-weight: 700; letter-spacing: 1px;">
                                            Query Details
                                        </p>
                                        <hr style="border: none; border-top: 1px solid #d4dffa; margin: 12px 0;">

                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="padding: 8px 0; color: #777; font-size: 14px; width: 120px;">Name:</td>
                                                <td style="padding: 8px 0; color: #1a1a2e; font-size: 15px; font-weight: 600;">
                                                    {{ $query->name }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; color: #777; font-size: 14px;">Email:</td>
                                                <td style="padding: 8px 0; color: #1a1a2e; font-size: 15px; font-weight: 600;">
                                                    <a href="mailto:{{ $query->email }}" style="color: #0f3460; text-decoration: none;">{{ $query->email }}</a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; color: #777; font-size: 14px;">Subject:</td>
                                                <td style="padding: 8px 0; color: #1a1a2e; font-size: 15px; font-weight: 600;">
                                                    {{ $query->subject }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; color: #777; font-size: 14px; vertical-align: top;">Message:</td>
                                                <td style="padding: 8px 0; color: #1a1a2e; font-size: 15px; line-height: 1.6;">
                                                    {!! nl2br(e($query->message)) !!}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Admin Portal Access Button --}}
                    <tr>
                        <td align="center" style="padding: 5px 35px 30px;">
                            <a href="{{ url('/admin/contact-queries') }}"
                               style="display: inline-block; background: linear-gradient(135deg, #0f3460, #1a1a2e); color: #ffffff; padding: 14px 40px; border-radius: 8px; text-decoration: none; font-size: 15px; font-weight: 600; letter-spacing: 0.5px;">
                                View in Admin Portal →
                            </a>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background-color: #f8f9fb; padding: 25px 35px; text-align: center; border-top: 1px solid #eee;">
                            <p style="color: #999; margin: 0; font-size: 13px;">
                                This is an automated message from {{ config('app.name', 'EAY Sports') }}.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>
