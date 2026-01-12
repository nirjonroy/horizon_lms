<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\backend\dashboardController;
use App\Http\Controllers\backend\siteInformationController;
use App\Http\Controllers\backend\whereToStudyController;
use App\Http\Controllers\backend\internationalStudentLifeController;
use App\Http\Controllers\backend\aboutController;
use App\Http\Controllers\backend\feesCategoryController;
use App\Http\Controllers\backend\onlineFeeController;
use App\Http\Controllers\backend\studentInformationController;
use App\Http\Controllers\backend\sliderController;
use App\Http\Controllers\backend\contactFormController;
use App\Http\Controllers\backend\blogController;
use App\Http\Controllers\backend\userController;
use App\Http\Controllers\backend\ConsultationController;
use App\Http\Controllers\backend\ScriptController;
use App\Http\Controllers\backend\SeoController;
use App\Http\Controllers\frontend\homeController;
use App\Http\Controllers\frontend\CartController;
use App\Http\Controllers\frontend\PremiumCourseReviewController;
use App\Http\Controllers\backend\webInnerController;
use App\Http\Controllers\backend\PremiumCourseCategoryController;
use App\Http\Controllers\backend\PremiumCourseChildCategoryController;
use App\Http\Controllers\backend\PremiumCourseController;
use App\Http\Controllers\backend\PremiumCourseSubcategoryController;
use App\Http\Controllers\backend\OrderController;
use App\Http\Controllers\backend\CouponController;
use App\Http\Controllers\backend\CampaignController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\backend\RedirectRuleController;
use App\Http\Controllers\backend\PageController;
use App\Http\Controllers\backend\TestimonialController;
use App\Http\Controllers\backend\HomeHighlightController;
use App\Http\Controllers\backend\PartnerController;
use App\Http\Controllers\frontend\PageController as FrontendPageController;
use App\Models\PremiumCourseCategory;
use App\Models\PremiumCourseChildCategory;
use App\Models\PremiumCourseSubcategory;
use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Response;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
// Route::get('/register', function() {
//     return redirect('/');
// })->name('register')->middleware('auth');

Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');

Route::get('/robots.txt', function () {
    $content = implode("\n", [
        'User-agent: *',
        'Allow: /',
        '',
        'Sitemap: ' . route('sitemap'),
    ]);

    return Response::make($content, 200)
        ->header('Content-Type', 'text/plain');
});

Route::get('/', [homeController::class, 'index'])->name('home.index');
Route::get('apply/{slug?}', [homeController::class, 'apply_now'])->name('apply.now');
Route::get('apply-now', [homeController::class, 'apply_now'])->name('apply.legacy');
Route::post('apply-now-form', [homeController::class, 'apply_now_form'])->name('apply.form');
Route::post('contact-form-form', [homeController::class, 'contact_form'])->name('contact.form');
Route::get('who-we-are', [homeController::class, 'about_us'])->name('who_we_are');
Route::get('search', [homeController::class, 'globalSearch'])->name('search');
Route::get('course-categories', [homeController::class, 'courseCategories'])->name('course.categories');
Route::get('price-and-plan', [homeController::class, 'pricingPlans'])->name('price.plan');
Route::get('where-to-study/{id}', [HomeController::class, 'whereToStudyId'])
    ->whereNumber('id')                           // only digits
    ->name('where.to.studybyID');
Route::get('/where-to-study/esgci', function () {
    return redirect()->route(
        'where.to.study',
        ['slug' => 'esgci-ecole-superieure-de-genie-informatique'],
        301
    );
});
Route::get('university/{slug}', [HomeController::class, 'whereToStudy'])
    ->where('slug', '^(?!\d+$)[A-Za-z0-9-]+$')    // not all digits; letters/numbers/hyphens
    ->name('where.to.study');
Route::get('university/{slug}/program/{program}', [HomeController::class, 'universityProgramDetails'])
    ->where([
        'slug' => '^(?!\d+$)[A-Za-z0-9-]+$',
        'program' => '[A-Za-z0-9-]+',
    ])
    ->name('university.program.show');
// Route::get('online-study-options', [homeController::class, 'onlineStudyOption'])->name('online.study.option');
// Route::get('international-student-life/{id}', [homeController::class, 'student_life'])->name('international.student.life');
// Route::get('find-how-to-apply', [homeController::class, 'how_to_apply'])->name('how.to.apply');
// Route::get('fees-and-cost', [homeController::class, 'fees_cost'])->name('fees.cost');
// Route::get('entry-requirement', [homeController::class, 'entry_requirement'])->name('entry.req');
// Route::get('application-process', [homeController::class, 'application_process'])->name('application.process');
// Route::get('accommodation', [homeController::class, 'accommodation'])->name('accommodation');
Route::get('courses', [homeController::class, 'premium_courses'])->name('premium-courses');
Route::get('free-courses', [homeController::class, 'free_courses'])->name('free-courses');
Route::permanentRedirect('Horizons-global-courses', 'courses');
Route::permanentRedirect('Horizons-global-free-courses', 'free-courses');
Route::get('course/{slug}', [HomeController::class, 'premium_course_details'])
    ->name('course.show');

Route::prefix('courses')->group(function () {
    Route::get('{category:slug}', [HomeController::class, 'showCategory'])
        ->name('courses.category.show');

    Route::get('{category:slug}/{subcategory:slug}', [HomeController::class, 'showSubcategory'])
        ->name('courses.subcategory.show');

    Route::get('{category:slug}/{subcategory:slug}/{childCategory:slug}', [HomeController::class, 'showChildCategory'])
        ->name('courses.child.show');
});

Route::prefix('category')->group(function () {
    Route::get('sub-category/child-category/{childCategory:slug}', function (PremiumCourseChildCategory $childCategory) {
        $childCategory->loadMissing(['category', 'subcategory']);
        abort_unless($childCategory->category && $childCategory->subcategory, 404);

        return redirect()->route('courses.child.show', [
            'category' => $childCategory->category->slug,
            'subcategory' => $childCategory->subcategory->slug,
            'childCategory' => $childCategory->slug,
        ], 301);
    })->name('category.child.show');

    Route::get('sub-category/{subcategory:slug}', function (PremiumCourseSubcategory $subcategory) {
        $subcategory->loadMissing('category');
        abort_unless($subcategory->category, 404);

        return redirect()->route('courses.subcategory.show', [
            'category' => $subcategory->category->slug,
            'subcategory' => $subcategory->slug,
        ], 301);
    })->name('category.subcategory.show');

    Route::get('{category:slug}', function (PremiumCourseCategory $category) {
        return redirect()->route('courses.category.show', [
            'category' => $category->slug,
        ], 301);
    })->name('category.show');
});
Route::get('page/{slug}', [FrontendPageController::class, 'show'])
    ->where('slug', '[A-Za-z0-9-]+')
    ->name('page.show');
Route::get('premium-course-details/{slug}/{id}', function ($slug, $id) {
    // Find the course by ID (to ensure it exists and slug is still valid)
    $course = App\Models\PremiumCourse::find($id);

    if ($course) {
        // Redirect permanently (SEO-safe)
        return redirect()->route('course.show', ['slug' => $course->slug], 301);
    }

    // If not found, redirect to the courses list or show 404
    return redirect()->to('/courses')->with('error', 'Course not found');
})->whereNumber('id');


// Route::get('support-for-study-abroad', [homeController::class, 'support_study_abroad'])->name('support.study.abroad');
// Route::get('support-career-preparation', [homeController::class, 'career_preparation'])->name('support.career.preparation');
Route::get('contact-us', [homeController::class, 'contact_us'])->name('contact.us');
Route::get('clear-cache', [homeController::class, 'clear_cache'])->name('clear.cache');
Route::get('blog', [homeController::class, 'all_blogs'])->name('all.blogs');
// 301 permanent redirect
Route::permanentRedirect('all-blogs', 'blog');
Route::get('blog-details/{id}', function (int $id) {
    $post = App\Models\Blog::findOrFail($id); // must exist in the same table you use for slugs
    return redirect()->route('blog.details', ['slug' => $post->slug], 301);
})->whereNumber('id');

Route::get('blog/{slug}', [homeController::class, 'blog_details'])->name('blog.details');
Route::get('consultation-book', [homeController::class, 'consultation_book'])->name('consultation.book');
Route::get('webinners', [homeController::class, 'webinners'])->name('webinner.view');

Route::get('/book-consultation', [homeController::class, 'showStep1'])->name('consultation.step1');
Route::get('book-consultation/personal-info', [HomeController::class, 'showStep2'])->name('consultation.step2');
Route::post('/book-consultation/personal-infos', [HomeController::class, 'submitBooking'])->name('consultation.personal-info');
Route::get('/confirmation', [homeController::class, 'confirmation'])->name('consultation.confirmation');

Route::get('/clear', function(){
    
     \Artisan::call('route:clear');
     \Artisan::call('optimize:clear');
     \Artisan::call('cache:clear');
     \Artisan::call('view:clear');
    return 'done';
});



// Admin Auth
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [\App\Http\Controllers\Admin\Auth\LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [\App\Http\Controllers\Admin\Auth\LoginController::class, 'login']);
    Route::post('logout', [\App\Http\Controllers\Admin\Auth\LoginController::class, 'logout'])->name('logout');

    Route::middleware(['admin'])->group(function () {
        Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/dashboard', [dashboardController::class, 'index'])->name('dashboard.index');
    // site informations
    Route::get('/site-information', [siteInformationController::class, 'index'])->name('siteinfo.index');
    Route::get('/site-edit/{id}', [siteInformationController::class, 'edit'])->name('siteinfo.edit');
    Route::post('/site-update/{id}', [siteInformationController::class, 'update'])->name('siteinfo.update');
    // where to study
    Route::get('/where-to-study-index', [whereToStudyController::class, 'index'])->name('whereToStudy.index');
    Route::get('/where-to-study-create', [whereToStudyController::class, 'create'])->name('whereToStudy.create');
    Route::get('/where-to-study-edit/{id}', [whereToStudyController::class, 'edit'])->name('whereToStudy.edit');
    Route::post('/where-to-study-store', [whereToStudyController::class, 'store'])->name('whereToStudy.store');
    Route::post('/where-to-study-update/{id}', [whereToStudyController::class, 'update'])->name('whereToStudy.update');
    Route::delete('/where-to-study/{id}', [whereToStudyController::class, 'destroy'])->name('whereToStudy.destroy');
    
     Route::resource('pages', PageController::class)->only(['index', 'edit', 'update']);

    // international Student life
    Route::get('/international-student-life-index', [internationalStudentLifeController::class, 'index'])->name('internationalStdLife.index');
    Route::get('/international-student-life-create', [internationalStudentLifeController::class, 'create'])->name('studentlife.create');
    Route::get('/international-student-life-edit/{id}', [internationalStudentLifeController::class, 'edit'])->name('studentlife.edit');
    Route::post('/international-student-life-store', [internationalStudentLifeController::class, 'store'])->name('studentlife.store');
    Route::delete('/international-student-life-delete/{id}', [internationalStudentLifeController::class, 'destroy'])->name('studentlife.destroy');
    Route::post('/international-student-life-update/{id}', [internationalStudentLifeController::class, 'update'])->name('studentlife.update');

    Route::get('/about-us', [aboutController::class, 'index'])->name('about.index');
    Route::get('/about-us-edit', [aboutController::class, 'edit'])->name('about.edit');
    Route::post('/about-us-update', [aboutController::class, 'update'])->name('about-us.update');

    Route::resource('testimonials', TestimonialController::class)->except(['show']);
    Route::resource('home-highlights', HomeHighlightController::class)->except(['show']);
    Route::resource('partners', PartnerController::class)->except(['show']);

    // fees Category
    Route::get('/fees-category-index', [feesCategoryController::class, 'index'])->name('fees.category.index');
    Route::get('/fees-category-create', [feesCategoryController::class, 'create'])->name('feesCategory.create');
    Route::post('/fees-category-store', [feesCategoryController::class, 'store'])->name('fees-category.store');
    Route::get('/fees-category-edit/{id}', [feesCategoryController::class, 'edit'])->name('feesCategory.edit');
    Route::delete('/fees-category-delete/{id}', [feesCategoryController::class, 'destroy'])->name('feesCategory.destroy');
    Route::post('/fees-category-update/{id}', [feesCategoryController::class, 'update'])->name('feesCategory.update');

    // online Course
    
     // fees Category
     Route::get('/fees-online-index', [onlineFeeController::class, 'index'])->name('fees.online.index');
     Route::get('/fees-online-create', [onlineFeeController::class, 'create'])->name('fees.online.create');
     Route::post('/fees-online-store', [onlineFeeController::class, 'store'])->name('fees.online.store');
     Route::get('/fees-online-edit/{id}', [onlineFeeController::class, 'edit'])->name('fees.online.edit');
     Route::delete('/fees-online-delete/{id}', [onlineFeeController::class, 'destroy'])->name('fees.online.destroy');
     Route::post('/fees-online-update/{id}', [onlineFeeController::class, 'update'])->name('fees.online.update');
     Route::get('/fees-online-recomanded/{id}', [onlineFeeController::class, 'recommand'])->name('fees.online.recomand');
     Route::get('/fees-online-not-recomanded/{id}', [onlineFeeController::class, 'not_recommand'])->name('fees.online.notRecomand');

    Route::get('/student-informations', [studentInformationController::class, 'index'])->name('studentInformation.index');
    Route::get('/student-informations-view/{id}', [studentInformationController::class, 'show'])->name('studentInformation.show');
    Route::get('/contact-informations', [contactFormController::class, 'index'])->name('contact.index');
    Route::get('/contact-view/{id}', [contactFormController::class, 'show'])->name('contact.view');

    Route::get('all-consultation', [ConsultationController::class, 'index'])->name('consultation.index');
    Route::get('/view-consultation/{id}', [ConsultationController::class, 'show'])->name('consultation.view');

    Route::get('Scripts-form', [ScriptController::class, 'index'])->name('script.index');
    Route::put('Scripts-update', [ScriptController::class, 'update'])->name('script.update');

    Route::get('seo-setup',[SeoController::Class, 'seoSetup'])->name('seo-setup');
    
    Route::put('update-seo-setup/{id}',[SeoController::Class, 'updateSeoSetup'])->name('update-seo-setup');
    Route::get('get-seo-setup/{id}',[SeoController::Class, 'getSeoSetup'])->name('get-seo-setup');
    
    Route::resource('redirects', RedirectRuleController::class)->except(['show']);

    Route::get('/slider-index', [sliderController::class, 'index'])->name('slider.index');
    Route::get('/slider-create', [sliderController::class, 'create'])->name('slider.create');
    Route::post('/slider-store', [sliderController::class, 'store'])->name('slider.store');
    Route::get('/slider-edit/{id}', [sliderController::class, 'edit'])->name('slider.edit');
    Route::post('/slider-update/{id}', [sliderController::class, 'update'])->name('slider.update');
    Route::delete('/slider-delete/{id}', [sliderController::class, 'destroy'])->name('slider.delete');

    Route::get('/blog-index', [blogController::class, 'index'])->name('blog.index');
    Route::get('/blog-create', [blogController::class, 'create'])->name('blog.create');
    Route::post('/blog-store', [blogController::class, 'store'])->name('blog.store');
    Route::get('/blog-edit/{id}', [blogController::class, 'edit'])->name('blog.edit');
    Route::post('/blog-update/{id}', [blogController::class, 'update'])->name('blog.update');
    Route::delete('/blog-delete/{id}', [blogController::class, 'destroy'])->name('blog.delete');
    
    Route::get('/webinner-index', [webInnerController::class, 'index'])->name('webinner.index');
    Route::get('/webinner-create', [webInnerController::class, 'create'])->name('webinner.create');
    Route::post('/webinner-store', [webInnerController::class, 'store'])->name('webinner.store');
    Route::get('/webinner-edit/{id}', [webInnerController::class, 'edit'])->name('webinner.edit');
    Route::post('/webinner-update/{id}', [webInnerController::class, 'update'])->name('webinner.update');
    Route::delete('/webinner-delete/{id}', [webInnerController::class, 'destroy'])->name('webinner.delete');
    
    Route::get('/permium-course', [PremiumCourseController::class, 'index'])->name('courses.index');
    Route::get('/permium-course-create', [PremiumCourseController::class, 'create'])->name('courses.create');
    Route::post('/permium-course-store', [PremiumCourseController::class, 'store'])->name('courses.store');
    Route::get('/permium-course-edit/{id}', [PremiumCourseController::class, 'edit'])->name('courses.edit');
    Route::put('/permium-course-update/{id}', [PremiumCourseController::class, 'update'])->name('courses.update');
    Route::post('/permium-course-toggle/{course}', [PremiumCourseController::class, 'toggleStatus'])->name('courses.toggle');
    Route::delete('/permium-course-delete/{id}', [PremiumCourseController::class, 'destroy'])->name('courses.destroy');

    Route::post('premium-course-categories/{premiumCourseCategory}/toggle-home', [PremiumCourseCategoryController::class, 'toggleHome'])
        ->name('premium-course-categories.toggle-home');

    Route::resource('premium-course-categories', PremiumCourseCategoryController::class)
        ->only(['index', 'store', 'edit', 'update', 'destroy']);

    Route::resource('premium-course-subcategories', PremiumCourseSubcategoryController::class)
        ->only(['index', 'store', 'edit', 'update', 'destroy']);

    Route::resource('premium-course-child-categories', PremiumCourseChildCategoryController::class)
        ->only(['index', 'store', 'edit', 'update', 'destroy']);

    Route::resource('coupons', CouponController::class)->except(['show']);
    Route::resource('campaigns', CampaignController::class)->except(['show']);


    
    Route::get('user-registraion-page', [userController::class, 'show'])->name('backend.user.registration');
    Route::post('user-registraion-update/{id}', [userController::class, 'update'])->name('backend.user.update');

    // Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
    });
});


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/register', function() {
    return redirect('/');
});
Route::get('university/{slug}/program/{program}/syllabus', [HomeController::class, 'downloadProgramSyllabus'])
    ->where([
        'slug' => '^(?!\d+$)[A-Za-z0-9-]+$',
        'program' => '[A-Za-z0-9-]+',
    ])
    ->name('university.program.syllabus');
Route::post('course/{slug}/reviews', [PremiumCourseReviewController::class, 'store'])
    ->name('course.reviews.store');
Route::post('/add-to-cart/{id}', [CartController::class, 'addToCart'])->name('cart.add');
    Route::get('/buy-now/{id}', [CartController::class, 'buyNow'])->name('cart.buy_now');
    Route::get('/cart', [CartController::class, 'viewCart'])->name('cart.view');
    Route::get('/remove-cart/{id}', [CartController::class, 'removeCart'])->name('cart.remove');
    Route::post('/cart/apply-coupon', [CartController::class, 'applyCoupon'])->name('cart.coupon.apply');
    Route::post('/cart/remove-coupon', [CartController::class, 'removeCoupon'])->name('cart.coupon.remove');

     Route::post('/stripe/checkout', [StripeController::class, 'checkout'])->name('stripe.checkout');
    Route::get('/stripe/success', [StripeController::class, 'success'])->name('stripe.success');
    Route::get('/stripe/cancel', [StripeController::class, 'cancel'])->name('stripe.cancel');

Route::get('/checkout', [CartController::class, 'checkout'])->name('checkout');


Route::get('/payment', function () {
    return view('user.payment');
});

Route::get('/user-dashboard', function () {
    $orders = \App\Models\Order::where('user_id', Auth::id())->get();
    return view('user.dashboard', compact('orders'));
});




});



require __DIR__.'/auth.php';
