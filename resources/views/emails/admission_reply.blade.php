<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Application Received</title>
</head>
@php
    $programTitle = $studentData['selected_program_name'] ?? ($studentData['course_name'] ?? 'your program');
    $universityName = $studentData['selected_university_name'] ?? config('app.name', 'Horizons Unlimited');
    $logoUrl = $studentData['logo_url'] ?? null;
    $heroImage = $studentData['selected_university_image'] ?? null;
    $fallbackHero = asset('frontend/assets/images/img8.jpg');
    $heroImage = $heroImage ?: $fallbackHero;
    $applyUrl = $studentData['apply_url'] ?? route('apply.now', $studentData['selected_program_slug'] ?? null);
    $consultUrl = $studentData['consult_url'] ?? route('consultation.step1');
    $firstName = $studentData['first_name'] ?? 'Student';
    $phone = trim(($studentData['country_code'] ?? '') . ' ' . ($studentData['phone'] ?? ''));
    $phone = $phone !== '' ? $phone : 'N/A';
    $graduationDate = trim(($studentData['graduation_month'] ?? '') . ' ' . ($studentData['graduation_year'] ?? ''));
    $graduationDate = $graduationDate !== '' ? $graduationDate : 'N/A';
    $workYears = ($studentData['work_experience_years'] ?? '') !== ''
        ? $studentData['work_experience_years'] . ' years'
        : 'N/A';
    $summaryRows = [
        ['Full name', $studentData['full_name'] ?? 'N/A'],
        ['Has middle name', $studentData['has_middle_name'] ?? 'N/A'],
        ['Middle name', $studentData['middle_name'] ?? 'N/A'],
        ['Email', $studentData['email'] ?? 'N/A'],
        ['Phone', $phone],
        ['Date of birth', $studentData['date_of_birth'] ?? 'N/A'],
        ['Gender', $studentData['gender'] ?? 'N/A'],
        ['City', $studentData['city'] ?? 'N/A'],
        ['Country of citizenship', $studentData['nationality'] ?? 'N/A'],
        ['Country of residence', $studentData['country_of_residence'] ?? 'N/A'],
        ['Concentration', $studentData['subject_of_interest'] ?? 'N/A'],
        ["Bachelor's degree", $studentData['has_bachelors_degree'] ?? 'N/A'],
        ['Graduation degree', $studentData['graduation_degree'] ?? 'N/A'],
        ['Graduation college', $studentData['graduation_college'] ?? 'N/A'],
        ['Graduation month/year', $graduationDate],
        ['Graduation marks', $studentData['graduation_marks'] ?? 'N/A'],
        ["Master's degree", $studentData['has_masters_degree'] ?? 'N/A'],
        ['Work experience', $workYears],
        ['Company', $studentData['company_name'] ?? 'N/A'],
        ['Industry', $studentData['industry'] ?? 'N/A'],
        ['Job role', $studentData['job_role'] ?? 'N/A'],
        ['Level of study', $studentData['course_and_degree'] ?? 'N/A'],
        ['Preferred university', $universityName],
        ['Program', $programTitle],
        ['Preferred session', $studentData['preferred_session'] ?? 'N/A'],
        ['Referral code', $studentData['referral_code'] ?? 'N/A'],
        ['Comments', $studentData['comments'] ?? 'N/A'],
        ['Disclaimer accepted', !empty($studentData['disclaimer_accepted']) ? 'Yes' : 'No'],
    ];
@endphp
<body style="margin:0;padding:0;background-color:#f3f5f7;font-family:Arial,Helvetica,sans-serif;color:#1f2933;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f3f5f7;padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff;border-radius:12px;overflow:hidden;">
                    @if($logoUrl)
                        <tr>
                            <td style="padding:24px;text-align:center;">
                                <img src="{{ $logoUrl }}" alt="Horizons Unlimited" width="140" style="display:block;margin:0 auto;">
                            </td>
                        </tr>
                    @endif
                    @if($heroImage)
                        <tr>
                            <td>
                                <img src="{{ $heroImage }}" alt="{{ $universityName }}" width="600" style="display:block;width:100%;height:auto;">
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <td style="padding:32px;">
                            <p style="margin:0 0 12px;font-size:16px;">Hi {{ $firstName }},</p>
                            <h1 style="margin:0 0 12px;font-size:24px;line-height:1.3;color:#0f172a;">Application received</h1>
                            <p style="margin:0 0 18px;font-size:15px;line-height:1.6;color:#475569;">
                                Thanks for submitting your application for {{ $programTitle }} at {{ $universityName }}.
                                Our admissions team is reviewing your details and will follow up shortly.
                            </p>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;margin:0 0 20px;">
                                @foreach ($summaryRows as $row)
                                    <tr>
                                        <td style="padding:10px 0;border-bottom:1px solid #e2e8f0;font-size:14px;color:#64748b;">{{ $row[0] }}</td>
                                        <td style="padding:10px 0;border-bottom:1px solid #e2e8f0;font-size:14px;color:#0f172a;font-weight:bold;">{{ $row[1] }}</td>
                                    </tr>
                                @endforeach
                            </table>

                            <table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 0 20px;">
                                <tr>
                                    <td style="padding-right:12px;">
                                        <a href="{{ $consultUrl }}" style="display:inline-block;padding:12px 22px;background-color:#ef4444;color:#ffffff;text-decoration:none;border-radius:6px;font-size:14px;font-weight:bold;">Schedule a call</a>
                                    </td>
                                    <td>
                                        <a href="{{ $applyUrl }}" style="display:inline-block;padding:12px 22px;background-color:#0f172a;color:#ffffff;text-decoration:none;border-radius:6px;font-size:14px;font-weight:bold;">View program</a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0;font-size:14px;line-height:1.6;color:#475569;">
                                If you need to update anything, reply to this email and we will help.
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
