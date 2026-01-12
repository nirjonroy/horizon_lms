<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $programTitle }} Syllabus</title>
</head>
<body style="margin:0;padding:0;background-color:#f3f5f7;font-family:Arial,Helvetica,sans-serif;color:#1f2933;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f3f5f7;padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff;border-radius:12px;overflow:hidden;">
                    <tr>
                        <td style="padding:24px;text-align:center;">
                            <img src="{{ $logoUrl }}" alt="Horizons Unlimited" width="140" style="display:block;margin:0 auto;">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <img src="{{ $heroImage }}" alt="{{ $programTitle }}" width="600" style="display:block;width:100%;height:auto;">
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:32px;">
                            <p style="margin:0 0 12px;font-size:16px;">Hi {{ $userName }},</p>
                            <h1 style="margin:0 0 12px;font-size:26px;line-height:1.3;color:#0f172a;">{{ $programTitle }}</h1>
                            <p style="margin:0 0 18px;font-size:15px;line-height:1.6;color:#475569;">
                                Thanks for your interest in {{ $programTitle }} from {{ $universityName }}.
                                We have attached the syllabus PDF to this email for easy access.
                            </p>
                            @if(!empty($shortDescription))
                                <p style="margin:0 0 20px;font-size:15px;line-height:1.6;color:#475569;">
                                    {{ \Illuminate\Support\Str::limit($shortDescription, 240) }}
                                </p>
                            @endif

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;margin:0 0 20px;">
                                <tr>
                                    <td style="padding:10px 0;border-bottom:1px solid #e2e8f0;font-size:14px;color:#64748b;">Degree</td>
                                    <td style="padding:10px 0;border-bottom:1px solid #e2e8f0;font-size:14px;color:#0f172a;font-weight:bold;">{{ $degreeName }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:10px 0;border-bottom:1px solid #e2e8f0;font-size:14px;color:#64748b;">Duration</td>
                                    <td style="padding:10px 0;border-bottom:1px solid #e2e8f0;font-size:14px;color:#0f172a;font-weight:bold;">{{ $duration }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:10px 0;border-bottom:1px solid #e2e8f0;font-size:14px;color:#64748b;">Format</td>
                                    <td style="padding:10px 0;border-bottom:1px solid #e2e8f0;font-size:14px;color:#0f172a;font-weight:bold;">{{ $programType }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:10px 0;border-bottom:1px solid #e2e8f0;font-size:14px;color:#64748b;">Tuition</td>
                                    <td style="padding:10px 0;border-bottom:1px solid #e2e8f0;font-size:14px;color:#0f172a;font-weight:bold;">
                                        @php
                                            $hasDiscount = $yearlyFee > 0 && $totalFee > 0;
                                            $displayFee = $hasDiscount ? $yearlyFee : $totalFee;
                                        @endphp
                                        ${{ number_format($displayFee, 2) }}
                                        @if($hasDiscount)
                                            <span style="color:#94a3b8;text-decoration:line-through;font-weight:normal;margin-left:6px;">
                                                ${{ number_format($totalFee, 2) }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            </table>

                            <table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 0 20px;">
                                <tr>
                                    <td style="padding-right:12px;">
                                        <a href="{{ $consultUrl }}" style="display:inline-block;padding:12px 22px;background-color:#ef4444;color:#ffffff;text-decoration:none;border-radius:6px;font-size:14px;font-weight:bold;">Schedule a call</a>
                                    </td>
                                    <td>
                                        <a href="{{ $applyUrl }}" style="display:inline-block;padding:12px 22px;background-color:#0f172a;color:#ffffff;text-decoration:none;border-radius:6px;font-size:14px;font-weight:bold;">Apply now</a>
                                    </td>
                                </tr>
                            </table>

                            @if(!empty($highlights))
                                <h3 style="margin:0 0 10px;font-size:16px;color:#0f172a;">Program highlights</h3>
                                <ul style="margin:0;padding-left:18px;color:#475569;font-size:14px;line-height:1.6;">
                                    @foreach($highlights as $highlight)
                                        <li>{{ $highlight }}</li>
                                    @endforeach
                                </ul>
                            @endif

                            <p style="margin:20px 0 0;font-size:14px;line-height:1.6;color:#475569;">
                                If you have any questions, reply to this email or contact our admissions team.
                            </p>
                            <p style="margin:16px 0 0;font-size:14px;color:#0f172a;">Best regards,<br>Horizons Unlimited Team</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color:#0f172a;color:#e2e8f0;padding:16px;text-align:center;font-size:12px;">
                            Horizons Unlimited, Helping learners choose the right university and program.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
