@php
    $selectedZone = $selected ?? 'America/New_York';
    $timeZoneLabels = [
        'UTC' => 'Coordinated Universal Time',
        'America/New_York' => 'Eastern Time (US & Canada)',
        'America/Chicago' => 'Central Time (US & Canada)',
        'America/Denver' => 'Mountain Time (US & Canada)',
        'America/Los_Angeles' => 'Pacific Time (US & Canada)',
        'America/Toronto' => 'Canada (Toronto)',
        'America/Sao_Paulo' => 'Brazil / Sao Paulo',
        'Europe/London' => 'United Kingdom',
        'Europe/Berlin' => 'Central Europe',
        'Europe/Paris' => 'Paris / Central Europe',
        'Africa/Cairo' => 'Cairo / EET',
        'Africa/Johannesburg' => 'South Africa',
        'Asia/Dubai' => 'Dubai / GST',
        'Asia/Kolkata' => 'India',
        'Asia/Dhaka' => 'Bangladesh',
        'Asia/Bangkok' => 'Thailand',
        'Asia/Singapore' => 'Singapore',
        'Asia/Tokyo' => 'Japan',
        'Australia/Sydney' => 'Australia (Sydney)',
        'Pacific/Auckland' => 'New Zealand',
    ];
@endphp
@foreach($timeZoneLabels as $zone => $label)
    @php
        $offset = \Carbon\Carbon::now($zone)->format('P');
    @endphp
    <option value="{{ $zone }}" {{ $selectedZone === $zone ? 'selected' : '' }}>
        {{ $label }} (GMT{{ $offset }})
    </option>
@endforeach
