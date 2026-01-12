@extends('backend.app')

@section('content')
<div class="content-wrapper">
    @include('backend.topnav')

    <section class="content">
        <div class="container-fluid">
            <div class="row justify-content-center mt-3">
                <div class="col-lg-9">
                    <form action="{{ route('admin.campaigns.update', $campaign) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-white border-0 pb-0 d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="card-title mb-0">Edit Campaign</h3>
                                    <small class="text-muted">Changes take effect immediately for matching courses.</small>
                                </div>
                                <a href="{{ route('admin.campaigns.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
                            </div>

                            @if (session('success'))
                                <div class="alert alert-success mx-4 mt-3">
                                    {{ session('success') }}
                                </div>
                            @endif

                            @if ($errors->any())
                                <div class="alert alert-danger mx-4 mt-3">
                                    <strong>Please review the highlighted fields.</strong>
                                </div>
                            @endif

                            @include('backend.campaigns._form', ['campaign' => $campaign, 'courseTypes' => $courseTypes])
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
