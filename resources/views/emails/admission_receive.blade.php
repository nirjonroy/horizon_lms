<!DOCTYPE html>
<html>
<head>
    <title>New Admission Received</title>
</head>
<body style="font-family: Arial, sans-serif; color: #111827; background-color: #f9fafb; margin: 0; padding: 24px;">
    <div style="max-width: 720px; margin: 0 auto; background-color: #ffffff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 24px;">
        <h2 style="margin-top: 0;">New Admission Application</h2>
        <p>A new application has been submitted with the following details:</p>

        <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
            <tr>
                <td style="padding: 8px; border: 1px solid #e5e7eb;">Full name</td>
                <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ $studentData['full_name'] ?? trim(($studentData['first_name'] ?? '') . ' ' . ($studentData['surname'] ?? '')) }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border: 1px solid #e5e7eb;">Has middle name</td>
                <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ $studentData['has_middle_name'] ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border: 1px solid #e5e7eb;">Middle name</td>
                <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ $studentData['middle_name'] ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border: 1px solid #e5e7eb;">Email</td>
                <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ $studentData['email'] ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border: 1px solid #e5e7eb;">Phone</td>
                <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ trim(($studentData['country_code'] ?? '') . ' ' . ($studentData['phone'] ?? '')) ?: 'N/A' }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border: 1px solid #e5e7eb;">Date of birth</td>
                <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ $studentData['date_of_birth'] ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border: 1px solid #e5e7eb;">Gender</td>
                <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ $studentData['gender'] ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border: 1px solid #e5e7eb;">City</td>
                <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ $studentData['city'] ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border: 1px solid #e5e7eb;">Country of citizenship</td>
                <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ $studentData['nationality'] ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border: 1px solid #e5e7eb;">Country of residence</td>
                <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ $studentData['country_of_residence'] ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border: 1px solid #e5e7eb;">Concentration</td>
                <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ $studentData['subject_of_interest'] ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border: 1px solid #e5e7eb;">Bachelor's degree</td>
                <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ $studentData['has_bachelors_degree'] ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border: 1px solid #e5e7eb;">Graduation degree</td>
                <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ $studentData['graduation_degree'] ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border: 1px solid #e5e7eb;">Graduation college</td>
                <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ $studentData['graduation_college'] ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border: 1px solid #e5e7eb;">Graduation month/year</td>
                <td style="padding: 8px; border: 1px solid #e5e7eb;">
                    {{ $studentData['graduation_month'] ?? 'N/A' }} {{ $studentData['graduation_year'] ?? '' }}
                </td>
            </tr>
            <tr>
                <td style="padding: 8px; border: 1px solid #e5e7eb;">Graduation marks</td>
                <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ $studentData['graduation_marks'] ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border: 1px solid #e5e7eb;">Master's degree</td>
                <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ $studentData['has_masters_degree'] ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border: 1px solid #e5e7eb;">Work experience</td>
                <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ $studentData['work_experience_years'] ?? 'N/A' }} years</td>
            </tr>
            <tr>
                <td style="padding: 8px; border: 1px solid #e5e7eb;">Company</td>
                <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ $studentData['company_name'] ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border: 1px solid #e5e7eb;">Industry</td>
                <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ $studentData['industry'] ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border: 1px solid #e5e7eb;">Job role</td>
                <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ $studentData['job_role'] ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border: 1px solid #e5e7eb;">Level of study</td>
                <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ $studentData['course_and_degree'] ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border: 1px solid #e5e7eb;">Preferred university</td>
                <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ $studentData['selected_university_name'] ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border: 1px solid #e5e7eb;">Program</td>
                <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ $studentData['selected_program_name'] ?? ($studentData['course_name'] ?? 'N/A') }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border: 1px solid #e5e7eb;">Preferred session</td>
                <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ $studentData['preferred_session'] ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border: 1px solid #e5e7eb;">Referral code</td>
                <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ $studentData['referral_code'] ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border: 1px solid #e5e7eb;">Comments</td>
                <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ $studentData['comments'] ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border: 1px solid #e5e7eb;">Disclaimer accepted</td>
                <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ !empty($studentData['disclaimer_accepted']) ? 'Yes' : 'No' }}</td>
            </tr>
        </table>
    </div>
</body>
</html>
