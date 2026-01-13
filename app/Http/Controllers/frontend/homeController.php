<?php

namespace App\Http\Controllers\frontend;
use Illuminate\Support\Facades\Http;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\slider;
use App\Models\whereToStudy;
use App\Models\studentInformation;
use App\Models\Blog;
use App\Models\webInner;
use App\Models\siteInformation;
use App\Models\feesCategory;
use App\Models\contactForm;
use App\Models\PremiumCourse;
use App\Models\PremiumCourseCategory;
use App\Models\PremiumCourseSubcategory;
use App\Models\PremiumCourseChildCategory;
use App\Models\PremiumCourseReview;
use App\Models\Order;
use App\Models\onlineFee;
use App\Models\Campaign;
use App\Models\Coupon;
use App\Models\Booking;
use App\Models\internationalStudentLife;
use App\Models\Testimonial;
use App\Models\HomeHighlight;
use App\Models\Partner;
use App\Models\About;
use RealRashid\SweetAlert\Facades\Alert;
use Carbon\Carbon;
use App\Mail\ContactFormMail;
use Illuminate\Support\Facades\Mail;
use App\Mail\BookingConfirmationMail;
use App\Mail\BookingReplyMail;
use App\Mail\AdmissionReciveMail;
use App\Mail\AdmissionReplyMail;
use App\Mail\ProgramSyllabusAdminMail;
use App\Mail\ProgramSyllabusMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
 // At the top

class homeController extends Controller
{
     public function index(){
        $cacheTtl = 600;
        $sliders = Cache::remember('home_sliders', $cacheTtl, function () {
            return slider::where('status', 1)->latest()->get();
        });
        $slider = $sliders->first();
        $whereToStudies = Cache::remember('home_where_to_studies', $cacheTtl, function () {
            return whereToStudy::where('is_done', 1)->orderBy('priority', 'ASC')->get();
        });
        $blogs = Cache::remember('home_blogs', $cacheTtl, function () {
            return Blog::where('homePage', 1)->latest()->limit(6)->get();
        });
         $cover = Cache::remember('home_blog_cover', $cacheTtl, function () {
            return Blog::where('homePage', 1)
                ->where('cover', 1)
                ->latest()
                ->first();
        });
         $info = Cache::remember('site_information', 1800, function () {
            return siteInformation::first();
        });
         $premiumCourses = Cache::remember('home_premium_courses', $cacheTtl, function () {
            return PremiumCourse::where('status', 1)
                ->where('type', '!=', 'single')
                ->where('type', '!=', 'free')
                ->latest()
                ->limit(6)
                ->get();
        });
        $homeCategories = Cache::remember('home_categories', $cacheTtl, function () {
            return PremiumCourseCategory::withCount(['courses' => function ($query) {
                    $query->where('status', 1);
                }])
                ->where('show_on_homepage', true)
                ->orderBy('name')
                ->get();
        });
        $homeCourseTabs = Cache::remember('home_course_tabs', $cacheTtl, function () {
            $tabs = PremiumCourseCategory::query()
                ->where('show_on_homepage', true)
                ->whereHas('courses', function ($query) {
                    $query->where('status', 1);
                })
                ->orderBy('name')
                ->take(5)
                ->get();

            // Hydrate each tab with its own slice of published courses to avoid a global limit across all categories
            $tabs->each(function ($category) {
                $category->setRelation('courses', $category->courses()
                    ->where('status', 1)
                    ->orderByDesc('updated_at')
                    ->take(6)
                    ->get());
            });

            return $tabs;
        });
        $studentsViewingCourses = Cache::remember('home_students_viewing_courses', $cacheTtl, function () {
            return PremiumCourse::query()
                ->where('status', 1)
                ->orderByDesc('updated_at')
                ->take(10)
                ->get();
        });
        $testimonials = Cache::remember('home_testimonials', $cacheTtl, function () {
            $items = Testimonial::query()
                ->where('is_active', true)
                ->orderBy('display_order')
                ->orderByDesc('created_at')
                ->take(10)
                ->get();

            return $items->isEmpty() ? $this->fallbackTestimonials() : $items;
        });
        $benefitHighlights = Cache::remember('home_benefit_highlights', $cacheTtl, function () {
            return HomeHighlight::query()
                ->where('is_active', true)
                ->orderBy('display_order')
                ->orderBy('title')
                ->get();
        });
        $partners = Cache::remember('home_partners', $cacheTtl, function () {
            return Partner::query()
                ->where('is_active', true)
                ->orderBy('display_order')
                ->orderBy('name')
                ->get();
        });
        $latestNews = Cache::remember('home_latest_news', $cacheTtl, function () {
            return Blog::query()
                ->where('status', 1)
                ->latest()
                ->take(6)
                ->get();
        });
        $about = Cache::remember('home_about', 1800, function () {
            return About::first();
        });

        $promoPopup = Cache::remember('home_promo_popup', 300, function () {
            $activeCampaign = Campaign::active()->orderBy('starts_at')->first();
            $activeCoupon = Coupon::active()->get()->first(function ($coupon) {
                return $coupon->isCurrentlyActive();
            });

            if ($activeCampaign) {
                $discountLabel = $activeCampaign->discount_type === 'percentage'
                    ? rtrim(rtrim(number_format($activeCampaign->discount_value, 2), '0'), '.') . '% off'
                    : '$' . number_format($activeCampaign->discount_value, 2) . ' off';

                return [
                    'type' => 'campaign',
                    'title' => $activeCampaign->name,
                    'highlight' => $activeCampaign->badge_label ?: 'Limited Time',
                    'message' => $activeCampaign->description ?: 'A fresh campaign is live across select categories.',
                    'detail' => $discountLabel . ' select premium courses right now.',
                    'cta_label' => 'Browse Courses',
                    'cta_url' => route('premium-courses'),
                    'code' => null,
                    'expires' => optional($activeCampaign->ends_at)->format('M j, Y'),
                    'storage_key' => 'promo_campaign_' . $activeCampaign->id,
                ];
            }

            if ($activeCoupon) {
                $discountLabel = $activeCoupon->type === 'percentage'
                    ? rtrim(rtrim(number_format($activeCoupon->amount, 2), '0'), '.') . '% off'
                    : '$' . number_format($activeCoupon->amount, 2) . ' off';

                return [
                    'type' => 'coupon',
                    'title' => $discountLabel,
                    'highlight' => 'Coupon',
                    'message' => $activeCoupon->notes ?: 'Apply the coupon at checkout before it expires.',
                    'detail' => $discountLabel . ' on your next enrollment.',
                    'cta_label' => 'Shop Premium Courses',
                    'cta_url' => route('premium-courses'),
                    'code' => $activeCoupon->code,
                    'expires' => optional($activeCoupon->expires_at)->format('M j, Y'),
                    'storage_key' => 'promo_coupon_' . $activeCoupon->id,
                ];
            }

            return null;
        });

                // dd($cover);
        // dd( $whereToStudies );
        return view('frontend.home', compact('slider', 'sliders', 'whereToStudies', 'blogs', 'cover', 'info' , 'premiumCourses', 'homeCategories', 'homeCourseTabs', 'studentsViewingCourses', 'testimonials', 'benefitHighlights', 'partners', 'latestNews', 'about', 'promoPopup'));
    }


    protected function fallbackTestimonials()
    {
        $samples = [
            [
                'name' => 'Kevin Martin',
                'role' => 'Student',
                'rating' => 5,
                'message' => 'My children and I LOVE The Horizon! The courses are fantastic and the instructors are so fun and knowledgeable. I only wish we found it sooner.',
                'avatar' => 'frontend/assets/images/small-avatar-1.jpg',
            ],
            [
                'name' => 'Oliver Beddows',
                'role' => 'Student',
                'rating' => 5,
                'message' => 'No matter what you want to learn, you\'ll find an amazing selection of courses here. The instructors are so knowledgeable while being fun and interesting.',
                'avatar' => 'frontend/assets/images/small-avatar-2.jpg',
            ],
            [
                'name' => 'Jackob Hallac',
                'role' => 'Student',
                'rating' => 5,
                'message' => 'I really recommend this site to all my friends and anyone who\'s willing to learn real skills. This platform lets you learn from experts at a convenient time.',
                'avatar' => 'frontend/assets/images/small-avatar-3.jpg',
            ],
            [
                'name' => 'Lubic Duble',
                'role' => 'Student',
                'rating' => 5,
                'message' => 'Thank you Horizon! You\'ve renewed my passion for learning and my dream of becoming a web developer.',
                'avatar' => 'frontend/assets/images/small-avatar-4.jpg',
            ],
            [
                'name' => 'Daniel Ward',
                'role' => 'Student',
                'rating' => 5,
                'message' => 'I found this platform when I had no funds for a college education. It has been a lifesaver because I can now freelance using the skills I learned here.',
                'avatar' => 'frontend/assets/images/small-avatar-5.jpg',
            ],
        ];

        return collect($samples)->map(fn ($attributes) => Testimonial::make($attributes));
    }
    public function apply_now(Request $request, $slug = null){
        $selectedFee = null;
        if ($slug) {
            $selectedFee = onlineFee::where('slug', $slug)->first();
        } elseif ($request->query('program')) {
            $selectedFee = onlineFee::where('slug', $request->query('program'))
                ->orWhere('program', $request->query('program'))
                ->first();
        }

        $selectedProgramSlug = optional($selectedFee)->slug;
        $selectedProgramName = optional($selectedFee)->program;
        $selectedUniversityId = optional($selectedFee)->university_id;

        $programOptions = Cache::remember('apply_now_program_options', 900, function () {
            return onlineFee::query()
                ->where('status', 1)
                ->get(['id', 'program', 'slug', 'university_id']);
        });

        return view('frontend.apply_now_page', compact(
            'selectedProgramSlug',
            'selectedProgramName',
            'selectedUniversityId',
            'programOptions',
            'selectedFee'
        ));
    }

    public function about_us(){
        return view('frontend.about_us');
    }
   

public function apply_now_form(Request $request){
    $currentYear = (int) now()->year;
    $rules = [
        'full_name' => 'required|string|max:255',
        'first_name' => 'required|string|max:255|regex:/^[a-zA-Z\\s]+$/',
        'middle_name' => 'required_if:has_middle_name,yes|string|max:255|regex:/^[a-zA-Z\\s]+$/',
        'has_middle_name' => 'required|in:yes,no',
        'surname' => 'required|string|max:255|regex:/^[a-zA-Z\\s]+$/',
        'email' => 'required|email|max:255',
        'country_code' => 'required|string',
        'phone' => 'required|string|min:8|max:20',
        'dob_month' => 'required|integer|min:1|max:12',
        'dob_day' => 'required|integer|min:1|max:31',
        'dob_year' => 'required|integer|min:1900|max:' . $currentYear,
        'gender' => 'required|in:Male,Female,Non-Binary,Non-Conforming,Prefer not to respond',
        'city' => 'required|string|max:255',
        'nationality' => 'required|string|max:255',
        'country_of_residence' => 'required|string|max:255',
        'subject_of_interest' => 'required|string|max:255',
        'has_bachelors_degree' => 'required|in:yes,no',
        'graduation_degree' => 'required_if:has_bachelors_degree,yes|string|max:255',
        'graduation_college' => 'required_if:has_bachelors_degree,yes|string|max:255',
        'graduation_month' => 'required_if:has_bachelors_degree,yes|integer|min:1|max:12',
        'graduation_year' => 'required_if:has_bachelors_degree,yes|integer|min:1900|max:' . $currentYear,
        'graduation_marks' => 'required_if:has_bachelors_degree,yes|string|max:50',
        'has_masters_degree' => 'required|in:yes,no',
        'work_experience_years' => 'required|integer|min:0|max:60',
        'company_name' => 'required|string|max:255',
        'industry' => 'required|string|max:255',
        'job_role' => 'required|string|max:255',
        'course_and_degree' => 'required|string|max:255',
        'course_name' => 'nullable|string|max:255',
        'preferred_session' => 'required|string|max:255',
        'referral_code' => 'nullable|string|max:255',
        'comments' => 'nullable|string|max:1000',
        'disclaimer_accepted' => 'accepted',
        'selected_university_id' => 'required|integer|exists:where_to_studies,id',
        'selected_program' => 'required|string|max:255',
    ];

    $validatedData = $request->validate($rules);

    if (($validatedData['has_middle_name'] ?? 'no') !== 'yes') {
        $validatedData['middle_name'] = null;
    }

    if (($validatedData['has_bachelors_degree'] ?? 'no') !== 'yes') {
        $validatedData['graduation_degree'] = null;
        $validatedData['graduation_college'] = null;
        $validatedData['graduation_month'] = null;
        $validatedData['graduation_year'] = null;
        $validatedData['graduation_marks'] = null;
    }
    $monthNames = [
        1 => 'January',
        2 => 'February',
        3 => 'March',
        4 => 'April',
        5 => 'May',
        6 => 'June',
        7 => 'July',
        8 => 'August',
        9 => 'September',
        10 => 'October',
        11 => 'November',
        12 => 'December',
    ];
    if (!empty($validatedData['graduation_month'])) {
        $gradMonth = (int) $validatedData['graduation_month'];
        $validatedData['graduation_month'] = $monthNames[$gradMonth] ?? $validatedData['graduation_month'];
    }

    $dateOfBirth = null;
    try {
        $dateOfBirth = Carbon::createFromDate(
            (int) $validatedData['dob_year'],
            (int) $validatedData['dob_month'],
            (int) $validatedData['dob_day']
        )->format('F j, Y');
    } catch (\Exception $e) {
        $dateOfBirth = null;
    }
    $validatedData['date_of_birth'] = $dateOfBirth;
    $validatedData['disclaimer_accepted'] = true;

    // Resolve selected university and program details
    $selectedUniversityName = null;
    $selectedUniversityImage = null;
    $resolveUniversity = function ($universityId) use (&$selectedUniversityName, &$selectedUniversityImage) {
        if (! $universityId) {
            return;
        }

        $university = DB::table('where_to_studies')
            ->select('name', 'slider1')
            ->where('id', $universityId)
            ->first();

        if (! $university) {
            return;
        }

        $selectedUniversityName = $university->name;
        if (! empty($university->slider1)) {
            $selectedUniversityImage = filter_var($university->slider1, FILTER_VALIDATE_URL)
                ? $university->slider1
                : asset($university->slider1);
        }
    };

    if ($validatedData['selected_university_id'] ?? false) {
        $resolveUniversity($validatedData['selected_university_id']);
    }

    $selectedProgramName = $validatedData['selected_program'] ?? null;
    $selectedProgramSlug = null;
    if ($selectedProgramName) {
        $fee = onlineFee::where('slug', $selectedProgramName)->orWhere('program', $selectedProgramName)->first();
        if ($fee) {
            $selectedProgramSlug = $fee->slug;
            $selectedProgramName = $fee->program;
            $validatedData['selected_university_id'] = $validatedData['selected_university_id'] ?? $fee->university_id;
            if (! $selectedUniversityName && $fee->university_id) {
                $resolveUniversity($fee->university_id);
            }
        }
    }

    // Keep legacy course_name populated with the selected program for emails/records
    $validatedData['course_name'] = $selectedProgramName ?: $validatedData['course_name'];
    $validatedData['selected_university_name'] = $selectedUniversityName;
    $validatedData['selected_program_name'] = $selectedProgramName;
    $validatedData['selected_program_slug'] = $selectedProgramSlug;

    $siteInfo = siteInformation::first();
    $logoUrl = null;
    if (! empty(optional($siteInfo)->logo)) {
        $logoUrl = filter_var($siteInfo->logo, FILTER_VALIDATE_URL)
            ? $siteInfo->logo
            : asset($siteInfo->logo);
    }

    $emailData = $validatedData;
    $emailData['selected_university_image'] = $selectedUniversityImage;
    $emailData['logo_url'] = $logoUrl;
    $emailData['apply_url'] = $selectedProgramSlug
        ? route('apply.now', $selectedProgramSlug)
        : route('apply.now');
    $emailData['consult_url'] = route('consultation.step1');

    $studentInformation = new studentInformation();
    $studentInformation->fill($validatedData);
    // dd($studentInformation);
    $studentInformation->save();

    try {
        Mail::to($validatedData['email'])->send(new AdmissionReplyMail($emailData));
    } catch (\Exception $e) {
        Log::error('Admission email to user failed: ' . $e->getMessage());
    }

    try {
        Mail::to('imad@thehorizonsunlimited.com')->send(new AdmissionReciveMail($emailData));
    } catch (\Exception $e) {
        Log::error('Admission email to admin failed: ' . $e->getMessage());
    }

    return redirect()->back()->with('success', 'Student information saved successfully');
}


    public function contact_form(Request $request){
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|min:8|max:20',
            'message' => 'required|string',
        ]);

        // Create a new instance of the model and fill it with validated data
        $contactForm = new contactForm();
        $contactForm->fill($validatedData);

        // Save the model instance to the database
        $contactForm->save();
        
        
         // Send the email to the user (who submitted the form)
            Mail::to($validatedData['email'])->send(new ContactFormMail($validatedData));

        //   Send the email to your email (roynirjon18@gmail.com)
          Mail::to('imad@thehorizonsunlimited.com')->send(new ContactFormMail($validatedData));
 
        // Redirect back with a success message

        return redirect()->back()->with('success', 'Message Sent Successfully');
    }

    public function whereToStudyId(int $id)
{
    $study = WhereToStudy::findOrFail($id);
    return redirect()->route('where.to.study', ['slug' => $study->slug], 301);
}

public function whereToStudy(string $slug)
{
    $studies = WhereToStudy::where('slug', $slug)->firstOrFail(); // 404 if not found

    if ((int) $studies->is_done === 1) {
        $id = $studies->id;

        $blogs = Blog::where('where_to_study_id', $id)
            ->latest()->limit(6)->get();

        $cover = Blog::where('where_to_study_id', $id)
            ->where('cover', 1)
            ->latest()->first();

        $categories = FeesCategory::whereHas('onlineFees', function ($q) use ($id) {
                $q->where('university_id', $id);
            })->with(['onlineFees' => function ($q) use ($id) {
                $q->where('university_id', $id);
            }])->get();

        $latest_course = OnlineFee::where('university_id', $id)
            ->where('recommend', 1)
            ->latest()->limit(4)->get();

        return view('frontend.where_to_study',
            compact('studies', 'blogs', 'cover', 'categories', 'latest_course'));
    }

    return view('frontend.commingsoon', compact('studies'));
}

    public function universityProgramDetails(string $slug, string $program)
    {
        $studies = WhereToStudy::where('slug', $slug)->firstOrFail();

        if ((int) $studies->is_done !== 1) {
            return view('frontend.commingsoon', compact('studies'));
        }

        $program = OnlineFee::query()
            ->with('feesCategory')
            ->where('slug', $program)
            ->where('university_id', $studies->id)
            ->firstOrFail();

        $relatedPrograms = OnlineFee::query()
            ->select('id', 'program', 'short_name', 'slug', 'total_fee', 'yearly', 'duration', 'university_id')
            ->where('university_id', $studies->id)
            ->where('id', '!=', $program->id)
            ->latest('updated_at')
            ->take(4)
            ->get();

        return view('frontend.university_program_details', compact(
            'studies',
            'program',
            'relatedPrograms'
        ));
    }

    public function downloadProgramSyllabus(Request $request, string $slug, string $program)
    {
        $studies = WhereToStudy::where('slug', $slug)->firstOrFail();

        if ((int) $studies->is_done !== 1) {
            return redirect()->route('where.to.study', $studies->slug);
        }

        $program = OnlineFee::query()
            ->where('slug', $program)
            ->where('university_id', $studies->id)
            ->firstOrFail();

        if (! $program->syllabus_pdf) {
            return redirect()->back()->with('error', 'Syllabus is not available for this program.');
        }

        $fullPath = public_path($program->syllabus_pdf);
        if (! file_exists($fullPath)) {
            return redirect()->back()->with('error', 'Syllabus file is missing.');
        }

        $user = auth()->user();
        try {
            Mail::to($user->email)->send(new ProgramSyllabusMail($user, $studies, $program, $program->syllabus_pdf));
        } catch (\Exception $e) {
            Log::error('Program syllabus email failed: ' . $e->getMessage());
        }

        $siteInfo = siteInformation::first();
        $adminEmails = collect([
            optional($siteInfo)->email1,
            optional($siteInfo)->email2,
            config('mail.from.address'),
            'imad@thehorizonsunlimited.com',
        ])
            ->map(fn ($email) => is_string($email) ? trim($email) : null)
            ->filter(fn ($email) => filled($email))
            ->unique()
            ->values();

        if ($adminEmails->isEmpty()) {
            Log::warning('Program syllabus admin email skipped: no admin email configured.');
        } else {
            try {
                Mail::to($adminEmails->all())->send(new ProgramSyllabusAdminMail(
                    $user,
                    $studies,
                    $program,
                    $request->ip(),
                    (string) $request->header('User-Agent')
                ));
            } catch (\Exception $e) {
                Log::error('Program syllabus admin email failed: ' . $e->getMessage());
            }
        }

        $downloadName = Str::slug($program->program ?: ($program->short_name ?: 'program')) . '-syllabus.pdf';

        return response()->download($fullPath, $downloadName);
    }

    public function onlineStudyOption(){
        $study = whereToStudy::all();
        return view('frontend.online_study_options', compact('study'));
    }
    public function student_life($id){
        $lifes = internationalStudentLife::find($id);
        $blogs = Blog::where('life_style_id', $id)->latest()->limit(6)->get();
        $cover =  Blog::where('life_style_id', $id)
             ->where('cover', 1)
             ->latest()
             ->first();
        return view('frontend.life_style', compact('lifes', 'blogs', 'cover'));
    }
    public function how_to_apply(){
        return view('frontend.how_to_apply');
    }
    public function fees_cost(){
        return view('frontend.fees_cost');
    }
    public function entry_requirement(){
        return view('frontend.entry_requirement');
    }

    public function application_process(){
        return view('frontend.application_process');
    }

    public function accommodation(){
        return view('frontend.accommodation');
    }
    
    public function premium_courses(Request $request){
        $search = trim((string) $request->input('search', ''));
        $sort = $request->query('sort', 'newest');
        $priceMinFilter = $request->query('price_min');
        $priceMaxFilter = $request->query('price_max');
        $cacheTtl = 600;

        if ($redirect = $this->maybeRedirectToCategoryPage($request)) {
            return $redirect;
        }

        $filters = $this->resolveCourseFilters($request);
        $categoryId = optional($filters['category'])->id;
        $subcategoryId = optional($filters['subcategory'])->id;
        $childCategoryId = optional($filters['child'])->id;

        $applySearch = function ($query) use ($search) {
            return $query->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $like = '%' . $search . '%';
                    $inner->where('title', 'like', $like)
                        ->orWhere('instructor', 'like', $like)
                        ->orWhere('short_description', 'like', $like);
                });
            });
        };

        $applyTaxonomyFilters = function ($query) use ($categoryId, $subcategoryId, $childCategoryId) {
            return $query
                ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))
                ->when($subcategoryId, fn ($q) => $q->where('subcategory_id', $subcategoryId))
                ->when($childCategoryId, fn ($q) => $q->where('child_category_id', $childCategoryId));
        };

        $applyPriceFilters = function ($query) use ($priceMinFilter, $priceMaxFilter) {
            return $query
                ->when($priceMinFilter !== null && $priceMinFilter !== '', fn ($q) => $q->where('price', '>=', (float) $priceMinFilter))
                ->when($priceMaxFilter !== null && $priceMaxFilter !== '', fn ($q) => $q->where('price', '<=', (float) $priceMaxFilter));
        };

        $applySorting = function ($query) use ($sort) {
            switch ($sort) {
                case 'oldest':
                    $query->oldest('updated_at');
                    break;
                case 'price_high':
                    $query->orderBy('price', 'desc');
                    break;
                case 'price_low':
                    $query->orderBy('price', 'asc');
                    break;
                case 'name':
                    $query->orderBy('title');
                    break;
                default:
                    $query->latest('updated_at');
                    break;
            }
        };

        $singleQuery = PremiumCourse::where('type', 'single')
            ->where('status', 1);
        $applySearch($singleQuery);
        $applyTaxonomyFilters($singleQuery);
        $applyPriceFilters($singleQuery);
        $applySorting($singleQuery);

        $all_courses = $singleQuery->paginate(20)->withQueryString();

        $fullAccessQuery = PremiumCourse::where('type', '!=', 'single')
            ->where('type', '!=', 'free')
            ->where('status', 1);
        $applySearch($fullAccessQuery);
        $applyTaxonomyFilters($fullAccessQuery);
        $applyPriceFilters($fullAccessQuery);
        $applySorting($fullAccessQuery);

        $full_access = $fullAccessQuery->get();

        $categories = Cache::remember('premium_course_categories', $cacheTtl, function () {
            return PremiumCourseCategory::query()
                ->withCount(['courses as premium_courses_count' => function ($query) {
                    $query->where('type', 'single')
                        ->where('status', 1);
                }])
                ->orderBy('name')
                ->get();
        });

        $subcategories = $filters['category']
            ? Cache::remember('premium_course_subcategories_' . $filters['category']->id, $cacheTtl, function () use ($filters) {
                return PremiumCourseSubcategory::query()
                    ->where('category_id', $filters['category']->id)
                    ->orderBy('name')
                    ->get();
            })
            : collect();

        $childCategories = $filters['subcategory']
            ? Cache::remember('premium_course_child_categories_' . $filters['subcategory']->id, $cacheTtl, function () use ($filters) {
                return PremiumCourseChildCategory::query()
                    ->where('subcategory_id', $filters['subcategory']->id)
                    ->orderBy('name')
                    ->get();
            })
            : collect();

        $priceStats = Cache::remember('premium_course_price_stats', $cacheTtl, function () {
            return PremiumCourse::query()
                ->where('type', 'single')
                ->where('status', 1)
                ->selectRaw('MIN(price) as min_price, MAX(price) as max_price')
                ->first();
        });

        return view('frontend.premium_courses', compact(
            'all_courses',
            'full_access',
            'search',
            'categories',
            'subcategories',
            'childCategories',
            'priceStats',
            'sort'
        ) + [
            'activeCategory' => $filters['category'],
            'activeSubcategory' => $filters['subcategory'],
            'activeChildCategory' => $filters['child'],
            'priceFilter' => [
                'min' => $priceMinFilter,
                'max' => $priceMaxFilter,
            ],
        ]);
    }

    public function bundlePrograms(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $sort = $request->query('sort', 'newest');
        $priceMinFilter = $request->query('price_min');
        $priceMaxFilter = $request->query('price_max');

        $applySearch = function ($query) use ($search) {
            return $query->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $like = '%' . $search . '%';
                    $inner->where('title', 'like', $like)
                        ->orWhere('instructor', 'like', $like)
                        ->orWhere('short_description', 'like', $like);
                });
            });
        };

        $applyPriceFilters = function ($query) use ($priceMinFilter, $priceMaxFilter) {
            return $query
                ->when($priceMinFilter !== null && $priceMinFilter !== '', fn ($q) => $q->where('price', '>=', (float) $priceMinFilter))
                ->when($priceMaxFilter !== null && $priceMaxFilter !== '', fn ($q) => $q->where('price', '<=', (float) $priceMaxFilter));
        };

        $applySorting = function ($query) use ($sort) {
            switch ($sort) {
                case 'oldest':
                    $query->oldest('updated_at');
                    break;
                case 'price_high':
                    $query->orderBy('price', 'desc');
                    break;
                case 'price_low':
                    $query->orderBy('price', 'asc');
                    break;
                case 'name':
                    $query->orderBy('title');
                    break;
                default:
                    $query->latest('updated_at');
                    break;
            }
        };

        $bundleQuery = PremiumCourse::where('type', '!=', 'single')
            ->where('type', '!=', 'free')
            ->where('status', 1);
        $applySearch($bundleQuery);
        $applyPriceFilters($bundleQuery);
        $applySorting($bundleQuery);

        $full_access = $bundleQuery->paginate(20)->withQueryString();

        $emptyCourses = new LengthAwarePaginator(
            [],
            0,
            $full_access->perPage(),
            $full_access->currentPage(),
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        $priceStats = PremiumCourse::query()
            ->where('type', '!=', 'single')
            ->where('type', '!=', 'free')
            ->where('status', 1)
            ->selectRaw('MIN(price) as min_price, MAX(price) as max_price')
            ->first();

        return view('frontend.premium_courses', [
            'all_courses' => $emptyCourses,
            'full_access' => $full_access,
            'search' => $search,
            'categories' => collect(),
            'subcategories' => collect(),
            'childCategories' => collect(),
            'priceStats' => $priceStats,
            'sort' => $sort,
            'activeCategory' => null,
            'activeSubcategory' => null,
            'activeChildCategory' => null,
            'priceFilter' => [
                'min' => $priceMinFilter,
                'max' => $priceMaxFilter,
            ],
            'showBundlesOnly' => true,
        ]);
    }

    public function globalSearch(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        if ($search === '') {
            return redirect()->route('home.index')->with('warning', 'Please enter a search term before searching.');
        }

        $like = '%' . $search . '%';

        $premiumCourses = PremiumCourse::query()
            ->select('id', 'title', 'slug', 'image', 'short_description', 'type', 'price', 'updated_at')
            ->where('status', 1)
            ->where(function ($query) use ($like) {
                $query->where('title', 'like', $like)
                    ->orWhere('instructor', 'like', $like)
                    ->orWhere('short_description', 'like', $like);
            })
            ->latest('updated_at')
            ->take(12)
            ->get();

        $onlineFees = OnlineFee::query()
            ->with(['feesCategory:id,name'])
            ->select('id', 'program', 'short_name', 'total_fee', 'yearly', 'duration', 'link', 'degree_id', 'university_id', 'type', 'status', 'updated_at')
            ->where('status', 1)
            ->where(function ($query) use ($like) {
                $query->where('program', 'like', $like)
                    ->orWhere('short_name', 'like', $like)
                    ->orWhere('type', 'like', $like);
            })
            ->latest('updated_at')
            ->take(12)
            ->get();

        $universities = WhereToStudy::query()
            ->select('id', 'name', 'slug', 'short_description', 'slider1', 'updated_at')
            ->where('is_done', 1)
            ->where(function ($query) use ($like) {
                $query->where('name', 'like', $like)
                    ->orWhere('short_description', 'like', $like);
            })
            ->latest('updated_at')
            ->take(12)
            ->get();

        $totalResults = $premiumCourses->count() + $onlineFees->count() + $universities->count();

        return view('frontend.search_results', [
            'search' => $search,
            'premiumCourses' => $premiumCourses,
            'onlineFees' => $onlineFees,
            'universities' => $universities,
            'totalResults' => $totalResults,
        ]);
    }

    private function resolveCourseFilters(Request $request): array
    {
        $category = null;
        if ($slug = $request->query('category')) {
            $category = PremiumCourseCategory::where('slug', $slug)->first();
        }

        $subcategory = null;
        if ($slug = $request->query('subcategory')) {
            $subcategory = PremiumCourseSubcategory::where('slug', $slug)->first();
        }

        $child = null;
        if ($slug = $request->query('child')) {
            $child = PremiumCourseChildCategory::where('slug', $slug)->first();
        }

        if ($subcategory && $category && $subcategory->category_id !== $category->id) {
            $subcategory = null;
        }

        if ($child && $subcategory && $child->subcategory_id !== $subcategory->id) {
            $child = null;
        }

        if ($child && $category && $child->category_id !== $category->id) {
            $child = null;
        }

        if ($child && ! $subcategory) {
            $subcategory = PremiumCourseSubcategory::find($child->subcategory_id);
        }

        if ($subcategory && ! $category) {
            $category = PremiumCourseCategory::find($subcategory->category_id);
        }

        return [
            'category' => $category,
            'subcategory' => $subcategory,
            'child' => $child,
        ];
    }

public function free_courses(){
        $all_courses = PremiumCourse::where('type', 'single')
            ->where('status', 1)
            ->latest()
            ->paginate(20);
        $full_access = PremiumCourse::where('type',  'free')
            ->where('status', 1)
            ->latest()
            ->get();
        // dd($all_courses);
        return view('frontend.free_courses', compact('all_courses', 'full_access'));
    }
    
    public function courseCategories(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $categories = PremiumCourseCategory::query()
            ->withCount([
                'subcategories',
                'courses as premium_courses_count' => function ($query) {
                    $query->where('type', 'single');
                },
            ])
            ->when($search !== '', function ($query) use ($search) {
                $like = '%' . $search . '%';
                $query->where(function ($inner) use ($like) {
                    $inner->where('name', 'like', $like)
                        ->orWhere('description', 'like', $like);
                });
            })
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('frontend.category_index', compact('categories', 'search'));
    }

    public function pricingPlans()
    {
        $siteInfo = siteInformation::first();
        $planTypes = ['monthly', 'yearly', 'lifetime', 'weekly'];

        $featuredPlans = collect($planTypes)->map(function ($type) {
            return PremiumCourse::query()
                ->where('status', 1)
                ->where('type', $type)
                ->orderByDesc('updated_at')
                ->first();
        })->filter();

        if ($featuredPlans->isEmpty()) {
            $featuredPlans = PremiumCourse::query()
                ->whereIn('type', $planTypes)
                ->where('status', 1)
                ->orderByDesc('updated_at')
                ->take(3)
                ->get();
        }

        $plans = PremiumCourse::query()
            ->whereIn('type', $planTypes)
            ->where('status', 1)
            ->orderByDesc('updated_at')
            ->paginate(12);

        $heroPlan = $featuredPlans->first() ?? $plans->first();

        $faqs = [
            [
                'question' => 'Are these tuition fees guaranteed?',
                'answer' => 'All published fees reflect the latest information from our university partners. Final tuition and discounts are confirmed in your offer letter.',
            ],
            [
                'question' => 'Can I pay in installments?',
                'answer' => 'Yes. Most programs allow monthly or quarterly installment plans. Our advisors help you choose a schedule that fits your budget.',
            ],
            [
                'question' => 'Do you offer scholarships or discounts?',
                'answer' => 'Many universities provide merit-based scholarships and limited promotional discounts. Book a consultation to see which ones you qualify for.',
            ],
        ];

        return view('frontend.pricing', [
            'featuredPlans' => $featuredPlans,
            'plans' => $plans,
            'faqs' => $faqs,
            'siteInfo' => $siteInfo,
            'heroPlan' => $heroPlan,
        ]);
    }
    

    public function premium_course_details(string $slug)
    {
        // Fetch course by slug only
        $course = PremiumCourse::where('slug', $slug)
            ->where('status', 1)
            ->firstOrFail();

        $reviews = PremiumCourseReview::query()
            ->with('user')
            ->where('premium_course_id', $course->id)
            ->where('is_approved', true)
            ->latest()
            ->get();

        $reviewCount = $reviews->count();
        $averageRating = $reviewCount ? round((float) $reviews->avg('rating'), 1) : null;

        $userReview = null;
        if (auth()->check()) {
            $userReview = PremiumCourseReview::query()
                ->where('premium_course_id', $course->id)
                ->where('user_id', auth()->id())
                ->first();
        }

        $hasPurchased = false;
        if (auth()->check()) {
            $hasPurchased = Order::query()
                ->where('user_id', auth()->id())
                ->where('status', 'paid')
                ->whereRaw(
                    "JSON_CONTAINS(orders.items, JSON_OBJECT('id', ?), '$')",
                    [$course->id]
                )
                ->exists();
        }

        // Get most popular courses (excluding current)
        $mostPopularCourses = DB::table('premium_courses')
            ->select(
                'premium_courses.id',
                'premium_courses.slug',
                'premium_courses.title',
                'premium_courses.instructor',
                'premium_courses.long_description',
                'premium_courses.short_description',
                'premium_courses.price',
                'premium_courses.image',
                'premium_courses.duration',
                'premium_courses.effort',
                'premium_courses.questions',
                'premium_courses.format',
                'premium_courses.status',
                'premium_courses.created_at',
                'premium_courses.updated_at',
                DB::raw('COUNT(orders.id) AS order_count')
            )
            ->leftJoin('orders', function ($join) {
                // assuming orders.items is JSON array containing {"id": course_id}
                $join->on(DB::raw("JSON_CONTAINS(orders.items, JSON_OBJECT('id', premium_courses.id), '$')"), '=', DB::raw('1'));
            })
            ->where('premium_courses.id', '!=', $course->id)
            ->where('premium_courses.status', 1)
            ->groupBy(
                'premium_courses.id',
                'premium_courses.slug',
                'premium_courses.title',
                'premium_courses.instructor',
                'premium_courses.long_description',
                'premium_courses.short_description',
                'premium_courses.price',
                'premium_courses.image',
                'premium_courses.duration',
                'premium_courses.effort',
                'premium_courses.questions',
                'premium_courses.format',
                'premium_courses.status',
                'premium_courses.created_at',
                'premium_courses.updated_at'
            )
            ->orderByDesc('order_count')
            ->limit(5)
            ->get();

        return view('frontend.premium_course_details', compact(
            'course',
            'mostPopularCourses',
            'reviews',
            'reviewCount',
            'averageRating',
            'userReview',
            'hasPurchased'
        ));
    }

    public function showCategory(Request $request, PremiumCourseCategory $category)
    {
        $search = trim((string) $request->query('search', ''));

        $subcategories = $category->subcategories()
            ->withCount(['courses as premium_courses_count' => function ($query) {
                $query->where('type', 'single');
            }])
            ->orderBy('name')
            ->get();

        $courses = $this->paginateCoursesForTaxonomy($category->id, null, null, $search);

        return $this->renderCategoryPage([
            'context' => 'category',
            'category' => $category,
            'subcategory' => null,
            'childCategory' => null,
            'primaryItems' => $subcategories,
            'primaryItemsType' => $subcategories->isEmpty() ? null : 'subcategory',
            'courses' => $courses,
            'search' => $search,
        ]);
    }

    public function showSubcategory(Request $request, PremiumCourseCategory $category, PremiumCourseSubcategory $subcategory)
    {
        $search = trim((string) $request->query('search', ''));
        abort_unless($subcategory->category_id === $category->id, 404);

        $childCategories = $subcategory->childCategories()
            ->withCount(['courses as premium_courses_count' => function ($query) {
                $query->where('type', 'single');
            }])
            ->orderBy('name')
            ->get();

        $courses = $this->paginateCoursesForTaxonomy(
            $category->id,
            $subcategory->id,
            null,
            $search
        );

        return $this->renderCategoryPage([
            'context' => 'subcategory',
            'category' => $category,
            'subcategory' => $subcategory,
            'childCategory' => null,
            'primaryItems' => $childCategories,
            'primaryItemsType' => $childCategories->isEmpty() ? null : 'child',
            'courses' => $courses,
            'search' => $search,
        ]);
    }

    public function showChildCategory(
        Request $request,
        PremiumCourseCategory $category,
        PremiumCourseSubcategory $subcategory,
        PremiumCourseChildCategory $childCategory
    )
    {
        $search = trim((string) $request->query('search', ''));
        abort_unless(
            $childCategory->category_id === $category->id &&
            $childCategory->subcategory_id === $subcategory->id &&
            $subcategory->category_id === $category->id,
            404
        );

        $courses = $this->paginateCoursesForTaxonomy(
            $category->id,
            $subcategory->id,
            $childCategory->id,
            $search
        );

        return $this->renderCategoryPage([
            'context' => 'child',
            'category' => $category,
            'subcategory' => $subcategory,
            'childCategory' => $childCategory,
            'primaryItems' => collect(),
            'primaryItemsType' => null,
            'courses' => $courses,
            'search' => $search,
        ]);
    }

    private function paginateCoursesForTaxonomy(?int $categoryId, ?int $subcategoryId, ?int $childCategoryId, string $search = '', int $perPage = 9)
    {
        $query = PremiumCourse::where('type', 'single')
            ->where('status', 1)
            ->latest();

        if ($search !== '') {
            $query->where(function ($inner) use ($search) {
                $like = '%' . $search . '%';
                $inner->where('title', 'like', $like)
                    ->orWhere('instructor', 'like', $like)
                    ->orWhere('short_description', 'like', $like);
            });
        }

        return $query
            ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))
            ->when($subcategoryId, fn ($q) => $q->where('subcategory_id', $subcategoryId))
            ->when($childCategoryId, fn ($q) => $q->where('child_category_id', $childCategoryId))
            ->paginate($perPage)
            ->withQueryString();
    }

    private function renderCategoryPage(array $payload)
    {
        $category = $payload['category'] ?? null;
        $subcategory = $payload['subcategory'] ?? null;
        $childCategory = $payload['childCategory'] ?? null;
        $primaryItems = collect($payload['primaryItems'] ?? []);
        $context = $payload['context'] ?? 'category';
        $search = $payload['search'] ?? '';
        $courses = $payload['courses'] ?? null;

        $pageTitle = match ($context) {
            'subcategory' => optional($subcategory)->name ?? optional($category)->name ?? 'Courses',
            'child' => optional($childCategory)->name ?? optional($subcategory)->name ?? optional($category)->name ?? 'Courses',
            default => optional($category)->name ?? 'Courses',
        };

        $rawSummary = $childCategory->description
            ?? ($subcategory->description ?? ($category->description ?? 'Browse curated premium courses aligned to your interest.'));
        $pageSummary = Str::limit(strip_tags($rawSummary), 220);

        $heroImage = $childCategory->image
            ?? ($subcategory->image ?? ($category->image ?? null));

        $breadcrumbs = collect([
            ['label' => 'Home', 'url' => route('home.index')],
            ['label' => 'Courses', 'url' => route('premium-courses')],
        ]);

        if ($category) {
            $breadcrumbs->push([
                'label' => $category->name,
                'url' => route('courses.category.show', ['category' => $category->slug]),
            ]);
        }

        if ($subcategory) {
            $breadcrumbs->push([
                'label' => $subcategory->name,
                'url' => route('courses.subcategory.show', [
                    'category' => $category->slug,
                    'subcategory' => $subcategory->slug,
                ]),
            ]);
        }

        if ($childCategory) {
            $breadcrumbs->push([
                'label' => $childCategory->name,
                'url' => null,
            ]);
        }

        return view('frontend.category_grid', [
            'pageTitle' => $pageTitle,
            'pageSummary' => $pageSummary,
            'heroImage' => $this->resolveMediaPath($heroImage),
            'breadcrumbs' => $breadcrumbs,
            'primaryItems' => $primaryItems,
            'primaryItemsType' => $payload['primaryItemsType'] ?? null,
            'courses' => $courses,
            'context' => $context,
            'category' => $category,
            'subcategory' => $subcategory,
            'childCategory' => $childCategory,
            'search' => $search,
            'seo' => $this->buildTaxonomySeoData($category, $subcategory, $childCategory, $pageTitle, $pageSummary),
            'activeDescription' => $childCategory->description
                ?? ($subcategory->description ?? ($category->description ?? null)),
        ]);
    }

    private function maybeRedirectToCategoryPage(Request $request)
    {
        if ($slug = $request->query('child')) {
            $child = PremiumCourseChildCategory::with(['category', 'subcategory'])->where('slug', $slug)->first();
            if ($child && $child->category && $child->subcategory) {
                return redirect()->route(
                    'courses.child.show',
                    $this->appendSearchToParams([
                        'category' => $child->category->slug,
                        'subcategory' => $child->subcategory->slug,
                        'childCategory' => $child->slug,
                    ], $request)
                );
            }
        }

        if ($slug = $request->query('subcategory')) {
            $subcategory = PremiumCourseSubcategory::with('category')->where('slug', $slug)->first();
            if ($subcategory && $subcategory->category) {
                return redirect()->route(
                    'courses.subcategory.show',
                    $this->appendSearchToParams([
                        'category' => $subcategory->category->slug,
                        'subcategory' => $subcategory->slug,
                    ], $request)
                );
            }
        }

        if ($slug = $request->query('category')) {
            if ($category = PremiumCourseCategory::where('slug', $slug)->first()) {
                return redirect()->route(
                    'courses.category.show',
                    $this->appendSearchToParams([
                        'category' => $category->slug,
                    ], $request)
                );
            }
        }

        return null;
    }

    private function appendSearchToParams(array $parameters, Request $request): array
    {
        if ($search = $request->query('search')) {
            $parameters['search'] = $search;
        }

        return $parameters;
    }

    private function resolveMediaPath(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        return filter_var($path, FILTER_VALIDATE_URL) ? $path : asset($path);
    }

    private function buildTaxonomySeoData(?PremiumCourseCategory $category, ?PremiumCourseSubcategory $subcategory, ?PremiumCourseChildCategory $childCategory, string $pageTitle, string $pageSummary): array
    {
        $siteName = config('app.name', 'Horizons');
        $entity = $childCategory ?? $subcategory ?? $category;

        $title = $entity && $entity->meta_title
            ? $entity->meta_title
            : "{$pageTitle} | {$siteName}";

        $rawDescription = $entity && $entity->meta_description
            ? $entity->meta_description
            : $pageSummary;
        $description = Str::limit(strip_tags($rawDescription), 160);

        $metaImage = $entity->meta_image ?? $entity->image ?? optional($category)->image ?? null;

        return [
            'title' => $title,
            'description' => $description,
            'keywords' => $entity->keywords ?? '',
            'author' => $entity->author ?? $siteName,
            'publisher' => $entity->publisher ?? ($entity->author ?? $siteName),
            'copyright' => $entity->copyright ?? ($entity->author ?? $siteName),
            'site_name' => $entity->site_name ?? $siteName,
            'meta_image' => $this->resolveMediaPath($metaImage),
        ];
    }

    
    public function support_study_abroad(){
        return view('frontend.support_study_abroad');
    }

    public function career_preparation(){
        return view('frontend.career_preparation');
    }

    public function contact_us(){
        $info = siteInformation::first();
        return view('frontend.contact_us', compact('info'));
    }

    public function clear_cache(){
         \Artisan::call('cache:clear');
    \Artisan::call('route:clear');
    \Artisan::call('view:clear');
    \Artisan::call('config:clear');
    \Artisan::call('config:cache');
    return redirect()->back()->with('success', 'Application cache has been cleared.');
    }
    
    public function webinners()
    {
        $webinners = webInner::latest()->paginate(30);
        return view('frontend.webinners', compact('webinners'));

    }
    
    public function all_blogs()
    {
        $blogs = Blog::latest()->paginate(30);
        return view('frontend.all_blogs', compact('blogs'));

    }

    public function blog_details($slug){
        $blog = Blog::where('slug', $slug)->firstOrFail();
        $recentBlogs = Blog::where('id', '!=', $blog->id)
            ->latest()
            ->take(5)
            ->get();
        return view('frontend.blog_details', compact('blog', 'recentBlogs'));
    }

// public function getTimeZoneData()
// {
//     $apiKey = 'A8PRL7GQ6QVQ';
//     $response = Http::get("http://api.timezonedb.com/v2.1/list-time-zone", [
//         'key' => $apiKey,
//         'format' => 'json',
//     ]);

//     if ($response->successful()) {
//         $timeZones = $response->json()['zones'];
//         // Process and use $timeZones as needed
//     } else {
//         // Handle error
//     }
// }



public function consultation_book()
{

    
    

        return view('frontend.book_consultancy');
   
}




    public function showStep1()
    {
        $timeSlots = Cache::remember('consultation_time_slots', 3600, function () {
            return $this->generateTimeSlots();
        });

        return view('frontend.book_consultancy', compact('timeSlots'));
    }
    
 private function generateTimeSlots($timezone = null)
{
    // Use provided timezone or fallback to app timezone
    $startTime = Carbon::createFromTime(7, 0, 0, $timezone ?? config('app.timezone')); // 7:00 AM
    $endTime = Carbon::createFromTime(19, 0, 0, $timezone ?? config('app.timezone'));  // 7:00 PM

    $interval = 20; // minutes
    $timeSlots = [];

    while ($startTime->lte($endTime)) {
        $timeSlots[] = $startTime->format('g:i A'); // e.g., 7:00 AM
        $startTime->addMinutes($interval);
    }

    return $timeSlots;
}

    public function showStep2(Request $request)
    {
        // Validate the query parameters
        $validated = $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required',
        ]);
    
        // Render the second step with the passed data
        return view('frontend.book_consultancy_step2', [
            'date' => $request->query('date'),
            'time' => $request->query('time'),
            'time_zone' => $request->query('time_zone'),
        ]);
    }
    

    

   public function submitBooking(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'date' => 'required|date|after_or_equal:today',
        'time' => 'required',
        'time_zone' => 'required',
        'first_name' => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
        'last_name' => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
        'country_code' => 'required|string',
        'phone' => 'required|string|max:35',
        'email' => 'required|email|max:255',
        'additional_info' => 'nullable|string|max:1000',
    ]);

        // Create a new booking (store phone without duplicating country code)
        $booking = Booking::create(collect($validatedData)->except('country_code')->toArray());

        // Build phone display with country code (avoid double prefix)
        $code = trim($validatedData['country_code'] ?? '');
        $phoneRaw = trim($validatedData['phone']);
        $phoneDisplay = Str::startsWith($phoneRaw, $code) ? $phoneRaw : trim($code . ' ' . $phoneRaw);

        // Prepare mail data (not persisted)
        $mailData = $booking->toArray();
        $mailData['phone'] = $phoneDisplay;
        $mailData['country_code'] = $code;

        // Generate calendar invite (20-minute slot)
        try {
            $start = Carbon::parse($validatedData['date'] . ' ' . $validatedData['time'], $validatedData['time_zone'] ?? config('app.timezone'));
            $end = (clone $start)->addMinutes(20);
            $uid = (string) Str::uuid();
            $mailData['calendar_ics'] = "BEGIN:VCALENDAR\r\nVERSION:2.0\r\nPRODID:-//Horizons Unlimited//Consultation//EN\r\nBEGIN:VEVENT\r\nUID:{$uid}\r\nDTSTAMP:" . now('UTC')->format('Ymd\THis\Z') . "\r\nDTSTART:" . $start->clone()->setTimezone('UTC')->format('Ymd\THis\Z') . "\r\nDTEND:" . $end->setTimezone('UTC')->format('Ymd\THis\Z') . "\r\nSUMMARY:Consultation with Horizons Unlimited\r\nDESCRIPTION:Consultation with Horizons Unlimited\r\nEND:VEVENT\r\nEND:VCALENDAR";
        } catch (\Exception $e) {
            Log::warning('Failed to generate ICS: ' . $e->getMessage());
        }

        // Try sending to the user
        try {
        Mail::to($validatedData['email'])->send(new BookingReplyMail($mailData));
        } catch (\Exception $e) {
            Log::error('Booking email to user failed: ' . $e->getMessage());
        }

        // Try sending to the admin
        try {
        Mail::to('imad@thehorizonsunlimited.com')->send(new BookingConfirmationMail($mailData));
        } catch (\Exception $e) {
            Log::error('Booking email to admin failed: ' . $e->getMessage());
        }

    // Redirect with success message
    return redirect()->route('consultation.confirmation')->with('success', 'Booking confirmed successfully!');
}


    

    
    public function confirmation()
{

    

    // Retrieve the latest booking from the database
    $booking = Booking::latest()->first();

    if (!$booking) {
        // Redirect to Step 1 if no booking data is found
        return redirect()->route('consultation.step1')->with('error', 'No booking found.');
    }

    

    // Display confirmation page with booking details
    return view('frontend.consultation_confirmation', compact('booking'));
}

    

}
