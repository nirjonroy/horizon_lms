@extends('backend.app')

@section('content')
<div class="container-fluid py-3">
    @include('backend.redirects.partials.form', [
        'redirect' => $redirect,
        'action' => route('admin.redirects.update', $redirect),
        'method' => 'PUT',
        'cardTitle' => 'Edit Redirection',
        'submitLabel' => 'Update Redirection',
    ])
</div>
@endsection

