<!DOCTYPE html>
<html>
<head>
    <title>New Admission Received</title>
</head>
<body>
    <h2>New Admission Application</h2>
    <p><strong>Name:</strong> {{ $studentData['first_name'] }} {{ $studentData['surname'] }}</p>
    <p><strong>Email:</strong> {{ $studentData['email'] }}</p>
    <p><strong>Country Code:</strong> {{ $studentData['country_code'] }}</p>
    <p><strong>Phone:</strong> {{ $studentData['phone'] }}</p>
    <p><strong>Country Of Residence:</strong> {{ $studentData['country_of_residence'] }}</p>
    <p><strong>Nationality:</strong> {{ $studentData['nationality'] }}</p>
    @if (!empty($studentData['selected_university_name']))
        <p><strong>Preferred University:</strong> {{ $studentData['selected_university_name'] }}</p>
    @endif
    <p><strong>Course / Program:</strong> {{ $studentData['selected_program_name'] ?? $studentData['course_name'] }}</p>
    <p><strong>Subject:</strong> {{ $studentData['subject_of_interest'] }}</p>
    <p><strong>Preferred Session:</strong> {{ $studentData['preferred_session'] }}</p>
    <p><strong>Comments:</strong> {{ $studentData['comments'] ?? 'N/A' }}</p>
</body>
</html>
