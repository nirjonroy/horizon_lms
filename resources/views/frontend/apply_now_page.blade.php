@extends('frontend.app')
@php
    $SeoSettings = \App\Models\SeoSetting::forPage('apply-now');
    $siteInfo = DB::table('site_information')->first();
    $keywordsArray = $SeoSettings && $SeoSettings->keywords ? json_decode($SeoSettings->keywords, true) : [];
    if (!is_array($keywordsArray)) {
        $keywordsArray = [];
    }
    $normalizeUrl = function ($path) {
        if (!$path) {
            return null;
        }
        return filter_var($path, FILTER_VALIDATE_URL) ? $path : asset($path);
    };
    $rawMetaImage = optional($SeoSettings)->image ?: ($siteInfo->logo ?? null);
    $metaImage = $normalizeUrl($rawMetaImage);
    $seoTitle = optional($SeoSettings)->seo_title ?? config('app.name');
    $seoDescription = optional($SeoSettings)->seo_description ?? '';
    $siteName = optional($SeoSettings)->site_name ?? $seoTitle;
    $author = optional($SeoSettings)->author ?? ($siteInfo->title ?? config('app.name'));
    $publisher = optional($SeoSettings)->publisher ?? $author;
    $copyright = optional($SeoSettings)->copyright ?? ($siteInfo->title ?? config('app.name'));
    $favicon = $normalizeUrl($siteInfo->logo ?? null);
    $keywordsContent = !empty($keywordsArray) ? implode(', ', $keywordsArray) : '';
    $universities = DB::table('where_to_studies')->where('is_done', 1)->orderBy('name')->get();
    $programOptions = $programOptions ?? collect();
    $programList = $programOptions->map(function ($p) {
        return [
            'slug' => $p->slug,
            'name' => $p->program,
            'university_id' => $p->university_id,
        ];
    })->values();
    $selectedUniversityId = $selectedUniversityId ?? null;
    $selectedProgramSlug = $selectedProgramSlug ?? null;
    $subjectOptions = ['Arts and Design', 'Engineering', 'Medical', 'Business/Commerce'];
    $sessionOptions = [
        'Autumn ( September - November )',
        'Spring ( January - April )',
        'Summer ( May - July )',
    ];
@endphp
@section('title', $seoTitle)
@section('seos')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <meta name="title" content="{{ $seoTitle }}">
    <meta name="description" content="{{ $seoDescription }}">
    <meta name="keywords" content="{{ $keywordsContent }}">
    <meta property="og:title" content="{{ $seoTitle }}">
    <meta property="og:description" content="{{ $seoDescription }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="{{ $siteName }}">
    @if($metaImage)
        <meta property="og:image" content="{{ $metaImage }}">
    @endif
    <meta property="og:locale" content="en_US">
    <meta property="og:type" content="website">
    <meta name="author" content="{{ $author }}">
    <meta name="publisher" content="{{ $publisher }}">
    <meta name="copyright" content="{{ $copyright }}">
    <meta name="language" content="english">
    <meta name="distribution" content="global">
    <meta name="rating" content="general">
    <link rel="canonical" href="{{ url()->current() }}">
    @if($favicon)
        <link rel="icon" type="image/png" sizes="32x32" href="{{ $favicon }}">
    @endif
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seoTitle }}">
    <meta name="twitter:description" content="{{ $seoDescription }}">
    @if($metaImage)
        <meta name="twitter:image" content="{{ $metaImage }}">
    @endif
    <meta name="twitter:site" content="{{ url()->current() }}">
@endsection

@section('content')
<section class="py-5" style="background: linear-gradient(120deg, #001d42, #e6443c);">
    <div class="container text-center text-white">
        <span class="badge bg-white text-primary mb-3">Apply Now</span>
        <h1 class="display-5 fw-bold mb-3" style="color:white">Start Your Application</h1>
        <p class="mb-0 text-white-50" style="color:white !important">Tell us about your goals and we will match you with the right program, university, and start date.</p>
    </div>
</section>



<section class="py-5 bg-light">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card border-0 shadow-lg rounded-4">
                    <div class="card-body p-4 p-md-5">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
                            <div>
                                <h2 class="h4 text-primary mb-1">Application Details</h2>
                                <p class="text-muted mb-0">Complete the form below and we will take care of the rest.</p>
                            </div>
                            <span class="badge bg-primary-subtle text-primary mt-3 mt-md-0">Step 1 of 2</span>
                        </div>
                        {{-- Ensure SweetAlert2 is available before firing --}}
                        <script>
                            function ensureSwal(callback) {
                                if (typeof window.Swal !== 'undefined') {
                                    callback();
                                    return;
                                }
                                var swalScript = document.createElement('script');
                                swalScript.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
                                swalScript.onload = callback;
                                document.head.appendChild(swalScript);
                            }
                        </script>
                        @if (session('success'))
                            <script>
                                ensureSwal(function () {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Submitted!',
                                        text: @json(session('success')),
                                        confirmButtonColor: '#ec5252'
                                    });
                                });
                            </script>
                        @endif
                        @if ($errors->any())
                            <script>
                                ensureSwal(function () {
                                    const messages = @json($errors->all());
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Please fix the highlighted fields',
                                        html: messages.map(m => `<div class="text-start">• ${m}</div>`).join(''),
                                        confirmButtonColor: '#ec5252'
                                    });
                                });
                            </script>
                        @endif
                        <form action="{{ route('apply.form') }}" method="POST" class="row g-4">
                            @csrf
                            <h3 class="h6 text-uppercase text-muted fw-semibold mb-0">Personal Information</h3>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">First Name</label>
                                <input type="text" name="first_name" value="{{ old('first_name') }}" class="form-control{{ $errors->has('first_name') ? ' is-invalid' : '' }}" placeholder="Enter your name" required>
                                @if($errors->has('first_name'))
                                    <div class="invalid-feedback">{{ $errors->first('first_name') }}</div>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Last Name</label>
                                <input type="text" name="surname" value="{{ old('surname') }}" class="form-control{{ $errors->has('surname') ? ' is-invalid' : '' }}" placeholder="Enter your surname" required>
                                @if($errors->has('surname'))
                                    <div class="invalid-feedback">{{ $errors->first('surname') }}</div>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email</label>
                                <input type="email" name="email" value="{{ old('email') }}" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" placeholder="Enter your email" required>
                                @if($errors->has('email'))
                                    <div class="invalid-feedback">{{ $errors->first('email') }}</div>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Phone Number</label>
                                <div class="input-group">
                                    <select
                                        name="country_code"
                                        id="country_code"
                                        class="form-select select2-dial{{ $errors->has('phone') || $errors->has('country_code') ? ' is-invalid' : '' }}"
                                        data-placeholder="Select code"
                                        required
                                    >
                                        <option value="" disabled {{ old('country_code') ? '' : 'selected' }}>Select code</option>
                                        @include('frontend.partials.country_dial_options')
                                    </select>
                                    <input type="text" name="phone" value="{{ old('phone') }}" class="form-control{{ $errors->has('phone') ? ' is-invalid' : '' }}" placeholder="Enter phone number" required>
                                </div>
                                @if($errors->has('phone'))
                                    <div class="invalid-feedback d-block">{{ $errors->first('phone') }}</div>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nationality</label>
                                <select name="nationality" class="form-select select2-country{{ $errors->has('nationality') ? ' is-invalid' : '' }}" data-placeholder="Please select" required>
                                    <option value="" disabled {{ old('nationality') ? '' : 'selected' }}>Please select</option>
                                    @include('frontend.partials.country_options', ['selectedCountry' => old('nationality')])
                                </select>
                                @if($errors->has('nationality'))
                                    <div class="invalid-feedback d-block">{{ $errors->first('nationality') }}</div>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Country of Residence</label>
                                <select name="country_of_residence" class="form-select select2-country{{ $errors->has('country_of_residence') ? ' is-invalid' : '' }}" data-placeholder="Please select" required>
                                    <option value="" disabled {{ old('country_of_residence') ? '' : 'selected' }}>Please select</option>
                                    @include('frontend.partials.country_options', ['selectedCountry' => old('country_of_residence')])
                                </select>
                                @if($errors->has('country_of_residence'))
                                    <div class="invalid-feedback d-block">{{ $errors->first('country_of_residence') }}</div>
                                @endif
                            </div>
                            <div class="col-12">
                                <hr>
                            </div>
                            <h3 class="h6 text-uppercase text-muted fw-semibold mb-0">Study Preferences</h3>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Level of Study</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="course_and_degree" id="studyUndergrad" value="Undergraduate" {{ old('course_and_degree', 'Undergraduate') === 'Undergraduate' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="studyUndergrad">Undergraduate (pathway to a bachelor’s degree)</label>
                                </div>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="radio" name="course_and_degree" id="studyPostgrad" value="Postgraduate" {{ old('course_and_degree') === 'Postgraduate' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="studyPostgrad">Postgraduate (pathway to a master’s degree)</label>
                                </div>
                                @if($errors->has('course_and_degree'))
                                    <div class="invalid-feedback d-block">{{ $errors->first('course_and_degree') }}</div>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Subject of Interest</label>
                                <select name="subject_of_interest" class="form-select{{ $errors->has('subject_of_interest') ? ' is-invalid' : '' }}" required>
                                    <option value="" disabled {{ old('subject_of_interest') ? '' : 'selected' }}>Please select</option>
                                    @foreach ($subjectOptions as $subject)
                                        <option value="{{ $subject }}" {{ old('subject_of_interest') === $subject ? 'selected' : '' }}>{{ $subject }}</option>
                                    @endforeach
                                </select>
                                @if($errors->has('subject_of_interest'))
                                    <div class="invalid-feedback d-block">{{ $errors->first('subject_of_interest') }}</div>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Preferred University</label>
                                <select name="selected_university_id" id="selected_university_id" class="form-select{{ $errors->has('selected_university_id') ? ' is-invalid' : '' }}" required>
                                    <option value="" disabled {{ old('selected_university_id', $selectedUniversityId ?? null) ? '' : 'selected' }}>Select university</option>
                                    @foreach ($universities as $item)
                                        <option value="{{ $item->id }}" {{ old('selected_university_id', $selectedUniversityId ?? null) == $item->id ? 'selected' : '' }}>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @if($errors->has('selected_university_id'))
                                    <div class="invalid-feedback d-block">{{ $errors->first('selected_university_id') }}</div>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Program</label>
                                <select name="selected_program" id="selected_program" class="form-select{{ $errors->has('selected_program') ? ' is-invalid' : '' }}" required>
                                    <option value="" disabled {{ old('selected_program', $selectedProgramSlug ?? null) ? '' : 'selected' }}>Select program</option>
                                    @foreach ($programOptions as $program)
                                        <option value="{{ $program->slug ?? $program->program }}" {{ old('selected_program', $selectedProgramSlug ?? null) === ($program->slug ?? $program->program) ? 'selected' : '' }}>
                                            {{ $program->program }}
                                        </option>
                                    @endforeach
                                </select>
                                @if($errors->has('selected_program'))
                                    <div class="invalid-feedback d-block">{{ $errors->first('selected_program') }}</div>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Preferred Start Session</label>
                                <select name="preferred_session" class="form-select{{ $errors->has('preferred_session') ? ' is-invalid' : '' }}" required>
                                    <option value="" disabled {{ old('preferred_session') ? '' : 'selected' }}>Select session</option>
                                    @foreach ($sessionOptions as $session)
                                        <option value="{{ $session }}" {{ old('preferred_session') === $session ? 'selected' : '' }}>{{ $session }}</option>
                                    @endforeach
                                </select>
                                @if($errors->has('preferred_session'))
                                    <div class="invalid-feedback d-block">{{ $errors->first('preferred_session') }}</div>
                                @endif
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Comments or Questions</label>
                                <textarea name="comments" rows="5" class="form-control{{ $errors->has('comments') ? ' is-invalid' : '' }}" placeholder="Share any goals, requirements, or questions for our admissions team." required>{{ old('comments') }}</textarea>
                                @if($errors->has('comments'))
                                    <div class="invalid-feedback">{{ $errors->first('comments') }}</div>
                                @endif
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn theme-btn w-100">Submit Application <i class="la la-arrow-right ms-1"></i></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-5">
                <div class="card border-0 shadow-lg rounded-4 h-100">
                    <div class="card-body p-4 p-md-5">
                        <h3 class="h5 text-primary mb-3">Need Assistance?</h3>
                        <p class="text-muted mb-4">Our admissions experts are here to guide you through program selection, document preparation, and visa support.</p>
                        <div class="d-flex align-items-center mb-3">
                            <span class="icon-element icon-element-md bg-primary bg-opacity-10 text-primary me-3">
                                <i class="la la-envelope"></i>
                            </span>
                            <div>
                                <p class="text-muted mb-0">Email us</p>
                                <a href="mailto:info@thehorizonsunlimited.com" class="fw-semibold text-decoration-none">info@thehorizonsunlimited.com</a>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <span class="icon-element icon-element-md bg-primary bg-opacity-10 text-primary me-3">
                                <i class="la la-phone"></i>
                            </span>
                            <div>
                                <p class="text-muted mb-0">Call us</p>
                                <a href="tel:+183333STUDY" class="fw-semibold text-decoration-none">+ (833) 33-STUDY</a>
                            </div>
                        </div>
                        <div class="bg-light rounded-3 p-3 mb-4">
                            <p class="fw-semibold mb-1">What happens next?</p>
                            <ul class="text-muted small mb-0 ps-3">
                                <li>We review your application within one business day.</li>
                                <li>You receive tailored course recommendations and a call from our advisor.</li>
                                <li>We help you finalize documents, scholarships, and visa steps.</li>
                            </ul>
                        </div>
                        <p class="text-muted small mb-0">Submitting this form does not obligate you to enroll. It simply helps us prepare the best study pathway for you.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="py-5 bg-light border-bottom">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="icon-element icon-element-md bg-primary bg-opacity-10 text-primary mb-3">
                            <i class="la la-graduation-cap"></i>
                        </div>
                        <h3 class="h5">Global Programs</h3>
                        <p class="text-muted mb-0">Access bachelor’s, master’s, and pathway courses across Europe, the US, Canada, and beyond.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="icon-element icon-element-md bg-success bg-opacity-10 text-success mb-3">
                            <i class="la la-comments"></i>
                        </div>
                        <h3 class="h5">Dedicated Advisors</h3>
                        <p class="text-muted mb-0">Our team will review your information and contact you with tailored study recommendations.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="icon-element icon-element-md bg-warning bg-opacity-10 text-warning mb-3">
                            <i class="la la-clock"></i>
                        </div>
                        <h3 class="h5">Fast Response</h3>
                        <p class="text-muted mb-0">Expect a confirmation within 24 hours and a clear next-step plan for your application.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="py-5">
    <div class="container text-center">
        <h2 class="fw-bold text-primary mb-3">Scholarships & Flexible Payment Plans</h2>
        <p class="text-muted mb-4">Ask us about merit scholarships, installment plans, and country-specific funding opportunities.</p>
        <a href="{{ route('consultation.step1') }}" class="btn btn-outline-primary">Book a Free Consultation</a>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const dialSelect = document.getElementById('country_code');
        const phoneInput = document.querySelector('input[name="phone"]');
        const programSelect = document.getElementById('selected_program');
        const universitySelect = document.getElementById('selected_university_id');
        const programOptions = @json($programList);
        const preselectedProgram = "{{ old('selected_program', $selectedProgramSlug ?? '') }}";

        if (window.jQuery && $.fn.select2) {
            $('.select2-country').select2({
                placeholder: function () {
                    return $(this).data('placeholder') || 'Please select';
                },
                allowClear: true,
                width: '100%'
            });
            $('.select2-dial').select2({
                placeholder: function () {
                    return $(this).data('placeholder') || 'Select code';
                },
                width: '140px'
            });
            if (programSelect) {
                $(programSelect).select2({
                    placeholder: 'Select program',
                    width: '100%'
                });
            }
            if (universitySelect) {
                $(universitySelect).select2({
                    placeholder: 'Select university',
                    width: '100%'
                });
            }
        }

        const renderPrograms = (universityId) => {
            if (!programSelect) return;
            const current = programSelect.value;
            programSelect.innerHTML = '<option value="" disabled>Select program</option>';
            const filtered = programOptions.filter(p => !universityId || !p.university_id || p.university_id == universityId);
            filtered.forEach(p => {
                const opt = document.createElement('option');
                opt.value = p.slug || p.name;
                opt.textContent = p.name;
                programSelect.appendChild(opt);
            });
            const targetValue = preselectedProgram || current;
            if (targetValue) {
                programSelect.value = targetValue;
                if (window.jQuery && $.fn.select2) {
                    $(programSelect).val(targetValue).trigger('change.select2');
                }
            } else {
                programSelect.selectedIndex = 0;
            }
        };

        if (universitySelect) {
            universitySelect.addEventListener('change', (e) => {
                renderPrograms(e.target.value);
            });
            renderPrograms(universitySelect.value);
        } else {
            renderPrograms(null);
        }

        if (dialSelect) {
            const fallbackDial = "{{ old('country_code', '') }}";
            const initialDial = fallbackDial || dialSelect.value || (dialSelect.options[0] ? dialSelect.options[0].value : '');

            const applyDialCode = (code) => {
                if (!phoneInput || !code) return;
                const trimmed = phoneInput.value.trim();
                if (!trimmed || !trimmed.startsWith(code)) {
                    phoneInput.value = code + ' ';
                    phoneInput.focus();
                }
            };

            // Initialize selection and phone prefix
            if (window.jQuery && $.fn.select2) {
                $(dialSelect).val(initialDial).trigger('change.select2');
            } else if (initialDial) {
                dialSelect.value = initialDial;
            }
            if (phoneInput && !phoneInput.value.trim()) {
                applyDialCode(initialDial);
            }

            const onDialChange = (val) => applyDialCode(val);
            dialSelect.addEventListener('change', (e) => onDialChange(e.target.value));
            if (window.jQuery && $.fn.select2) {
                $(dialSelect).on('change.select2', function () {
                    onDialChange(this.value);
                });
            }
        }
    });
</script>
@endsection
