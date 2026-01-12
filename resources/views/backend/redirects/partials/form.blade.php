@php
    $redirect = $redirect ?? null;
    $sources = old('sources', $redirect->sources ?? [['pattern' => '', 'match_type' => 'exact']]);
    $redirectType = old('redirect_type', $redirect->redirect_type ?? '301');
    $isActive = old('is_active', $redirect->is_active ?? true);
    $caseInsensitive = old('case_insensitive', $redirect->case_insensitive ?? false);

    $matchTypeOptions = [
        'exact' => 'Exact',
        'prefix' => 'Starts With',
        'contains' => 'Contains',
    ];

    $redirectTypes = [
        '301' => '301 Permanent Move',
        '302' => '302 Temporary Move',
        '307' => '307 Temporary Redirect',
        '410' => '410 Content Deleted',
        '451' => '451 Unavailable for Legal Reasons',
    ];
@endphp

@if ($errors->any())
    <div class="alert alert-danger">
        <p class="mb-1 font-weight-bold">Please fix the following issues:</p>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card">
    <div class="card-header">
        <h3 class="card-title mb-0">{{ $cardTitle ?? 'Redirection Details' }}</h3>
    </div>

    <form action="{{ $action }}" method="POST">
        @csrf
        @if (!empty($method) && strtoupper($method) !== 'POST')
            @method($method)
        @endif

        <div class="card-body">
            <div class="form-group">
                <label class="font-weight-semibold">Source URLs</label>
                <div id="redirect-sources">
                    @foreach($sources as $index => $source)
                        <div class="redirect-source border rounded p-3 mb-3" data-index="{{ $index }}">
                            <div class="form-row">
                                <div class="col-md-8">
                                    <label>Source URL</label>
                                    <input
                                        type="text"
                                        name="sources[{{ $index }}][pattern]"
                                        class="form-control"
                                        value="{{ $source['pattern'] ?? '' }}"
                                        placeholder="/old-path or full URL"
                                        required
                                    >
                                </div>
                                <div class="col-md-3">
                                    <label>Match Type</label>
                                    <select
                                        name="sources[{{ $index }}][match_type]"
                                        class="form-control"
                                    >
                                        @foreach($matchTypeOptions as $value => $label)
                                            <option value="{{ $value }}" @selected(($source['match_type'] ?? 'exact') === $value)>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-1 d-flex align-items-end">
                                    <button type="button" class="btn btn-outline-danger btn-block remove-source" title="Remove">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="d-flex align-items-center">
                    <div class="form-check mr-3">
                        <input type="checkbox" class="form-check-input" id="caseInsensitive" name="case_insensitive" value="1" @checked($caseInsensitive)>
                        <label for="caseInsensitive" class="form-check-label">Ignore Case</label>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="add-source">
                        <i class="fas fa-plus mr-1"></i> Add another
                    </button>
                </div>
            </div>

            <div class="form-group">
                <label>Destination URL</label>
                <input
                    type="text"
                    name="destination_url"
                    class="form-control"
                    value="{{ old('destination_url', $redirect->destination_url ?? '') }}"
                    placeholder="https://example.com/new-path or /new-path"
                    required
                >
            </div>

            <div class="form-group">
                <label>Redirection Type</label>
                <div class="btn-group-toggle flex-wrap d-flex" data-toggle="buttons">
                    @foreach($redirectTypes as $value => $label)
                        <label class="btn btn-outline-primary mr-2 mb-2 @if($redirectType === $value) active @endif">
                            <input type="radio" name="redirect_type" value="{{ $value }}" autocomplete="off" @checked($redirectType === $value)>
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="form-group">
                <label>Status</label>
                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                    <label class="btn btn-outline-success @if((int) $isActive === 1) active @endif">
                        <input type="radio" name="is_active" value="1" autocomplete="off" @checked((int) $isActive === 1)>
                        Activate
                    </label>
                    <label class="btn btn-outline-secondary @if((int) $isActive === 0) active @endif">
                        <input type="radio" name="is_active" value="0" autocomplete="off" @checked((int) $isActive === 0)>
                        Deactivate
                    </label>
                </div>
            </div>
        </div>

        <div class="card-footer text-right">
            <a href="{{ route('admin.redirects.index') }}" class="btn btn-light">Cancel</a>
            <button type="submit" class="btn btn-primary">{{ $submitLabel ?? 'Save Redirection' }}</button>
        </div>
    </form>
</div>

<template id="redirect-source-template">
    <div class="redirect-source border rounded p-3 mb-3" data-index="__INDEX__">
        <div class="form-row">
            <div class="col-md-8">
                <label>Source URL</label>
                <input
                    type="text"
                    name="sources[__INDEX__][pattern]"
                    class="form-control"
                    placeholder="/old-path or full URL"
                    required
                >
            </div>
            <div class="col-md-3">
                <label>Match Type</label>
                <select name="sources[__INDEX__][match_type]" class="form-control">
                    @foreach($matchTypeOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="button" class="btn btn-outline-danger btn-block remove-source" title="Remove">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>
</template>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const sourcesWrapper = document.getElementById('redirect-sources');
        const template = document.getElementById('redirect-source-template');
        const addSourceBtn = document.getElementById('add-source');

        const updateRemoveButtonsState = () => {
            const rows = sourcesWrapper.querySelectorAll('.redirect-source');
            rows.forEach(button => {
                const removeBtn = button.querySelector('.remove-source');
                if (!removeBtn) {
                    return;
                }
                removeBtn.disabled = rows.length === 1;
            });
        };

        const addSourceRow = () => {
            const clone = template.innerHTML.replace(/__INDEX__/g, Date.now());
            sourcesWrapper.insertAdjacentHTML('beforeend', clone);
            updateRemoveButtonsState();
        };

        sourcesWrapper.addEventListener('click', function (event) {
            const target = event.target.closest('.remove-source');
            if (!target) {
                return;
            }

            const row = target.closest('.redirect-source');
            if (!row) {
                return;
            }

            row.remove();
            updateRemoveButtonsState();
        });

        if (addSourceBtn) {
            addSourceBtn.addEventListener('click', addSourceRow);
        }

        updateRemoveButtonsState();
    });
</script>
@endpush

