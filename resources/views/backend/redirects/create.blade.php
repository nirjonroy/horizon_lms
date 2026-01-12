@extends('backend.app')

@section('content')
<div class="container-fluid py-3">
    @include('backend.redirects.partials.form', [
        'action' => route('admin.redirects.store'),
        'method' => 'POST',
        'cardTitle' => 'Add Redirection',
        'submitLabel' => 'Add Redirection',
    ])
</div>
@endsection

