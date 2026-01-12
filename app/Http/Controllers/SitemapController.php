<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\PremiumCourse;
use App\Models\PremiumCourseCategory;
use App\Models\PremiumCourseChildCategory;
use App\Models\PremiumCourseSubcategory;
use App\Models\internationalStudentLife;
use App\Models\whereToStudy;
use Carbon\CarbonInterface;
use DateTimeInterface;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $xml = Cache::remember('sitemap.xml', now()->addHour(), function () {
            $urls = collect()
                ->merge($this->staticUrls())
                ->merge($this->whereToStudyUrls())
                ->merge($this->premiumCourseCategoryUrls())
                ->merge($this->premiumCourseSubcategoryUrls())
                ->merge($this->premiumCourseChildCategoryUrls())
                ->merge($this->premiumCourseUrls())
                ->merge($this->blogUrls());

            return $this->buildXml($urls);
        });

        return response($xml, 200)->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    private function staticUrls(): Collection
    {
        $now = now();

        return collect([
            [
                'loc' => route('home.index'),
                'lastmod' => $now,
                'changefreq' => 'daily',
                'priority' => 1.0,
            ],
            [
                'loc' => route('apply.now'),
                'lastmod' => $now,
                'changefreq' => 'monthly',
                'priority' => 0.7,
            ],
            [
                'loc' => route('who_we_are'),
                'lastmod' => $now,
                'changefreq' => 'yearly',
                'priority' => 0.5,
            ],
            // [
            //     'loc' => route('online.study.option'),
            //     'lastmod' => $now,
            //     'changefreq' => 'monthly',
            //     'priority' => 0.6,
            // ],
            // [
            //     'loc' => route('how.to.apply'),
            //     'lastmod' => $now,
            //     'changefreq' => 'monthly',
            //     'priority' => 0.6,
            // ],
            // [
            //     'loc' => route('fees.cost'),
            //     'lastmod' => $now,
            //     'changefreq' => 'monthly',
            //     'priority' => 0.6,
            // ],
            // [
            //     'loc' => route('entry.req'),
            //     'lastmod' => $now,
            //     'changefreq' => 'monthly',
            //     'priority' => 0.6,
            // ],
            // [
            //     'loc' => route('application.process'),
            //     'lastmod' => $now,
            //     'changefreq' => 'monthly',
            //     'priority' => 0.6,
            // ],
            // [
            //     'loc' => route('accommodation'),
            //     'lastmod' => $now,
            //     'changefreq' => 'monthly',
            //     'priority' => 0.6,
            // ],
            [
                'loc' => route('premium-courses'),
                'lastmod' => $now,
                'changefreq' => 'weekly',
                'priority' => 0.7,
            ],
            [
                'loc' => route('free-courses'),
                'lastmod' => $now,
                'changefreq' => 'weekly',
                'priority' => 0.6,
            ],
            // [
            //     'loc' => route('support.study.abroad'),
            //     'lastmod' => $now,
            //     'changefreq' => 'monthly',
            //     'priority' => 0.6,
            // ],
            // [
            //     'loc' => route('support.career.preparation'),
            //     'lastmod' => $now,
            //     'changefreq' => 'monthly',
            //     'priority' => 0.5,
            // ],
            [
                'loc' => route('contact.us'),
                'lastmod' => $now,
                'changefreq' => 'monthly',
                'priority' => 0.7,
            ],
            [
                'loc' => route('all.blogs'),
                'lastmod' => $now,
                'changefreq' => 'daily',
                'priority' => 0.7,
            ],
            [
                'loc' => route('consultation.book'),
                'lastmod' => $now,
                'changefreq' => 'monthly',
                'priority' => 0.5,
            ],
            [
                'loc' => route('webinner.view'),
                'lastmod' => $now,
                'changefreq' => 'monthly',
                'priority' => 0.4,
            ],
            [
                'loc' => route('consultation.step1'),
                'lastmod' => $now,
                'changefreq' => 'monthly',
                'priority' => 0.5,
            ],
        ]);
    }

    private function whereToStudyUrls(): Collection
    {
        return whereToStudy::query()
            ->where('is_done', 1)
            ->get()
            ->filter(fn ($study) => filled($study->slug ?? null))
            ->map(function ($study) {
                return [
                    'loc' => route('where.to.study', ['slug' => $study->slug]),
                    'lastmod' => $study->updated_at ?? $study->created_at,
                    'changefreq' => 'weekly',
                    'priority' => 0.8,
                ];
            });
    }

    private function premiumCourseUrls(): Collection
    {
        return PremiumCourse::query()
            ->get()
            ->filter(fn ($course) => filled($course->slug ?? null))
            ->map(function ($course) {
                return [
                    'loc' => route('course.show', ['slug' => $course->slug]),
                    'lastmod' => $course->updated_at ?? $course->created_at,
                    'changefreq' => 'weekly',
                    'priority' => 0.7,
                ];
            });
    }

    private function blogUrls(): Collection
    {
        return Blog::query()
            ->get()
            ->filter(fn ($post) => filled($post->slug ?? null))
            ->map(function ($post) {
                return [
                    'loc' => route('blog.details', ['slug' => $post->slug]),
                    'lastmod' => $post->updated_at ?? $post->created_at,
                    'changefreq' => 'weekly',
                    'priority' => 0.6,
                ];
            });
    }

    private function premiumCourseCategoryUrls(): Collection
    {
        return PremiumCourseCategory::query()
            ->get()
            ->filter(fn ($category) => filled($category->slug ?? null))
            ->map(function ($category) {
                return [
                    'loc' => route('courses.category.show', ['category' => $category->slug]),
                    'lastmod' => $category->updated_at ?? $category->created_at,
                    'changefreq' => 'weekly',
                    'priority' => 0.7,
                ];
            });
    }

    private function premiumCourseSubcategoryUrls(): Collection
    {
        return PremiumCourseSubcategory::query()
            ->with('category:id,slug,updated_at,created_at')
            ->get()
            ->filter(function ($subcategory) {
                return filled($subcategory->slug ?? null)
                    && $subcategory->category
                    && filled($subcategory->category->slug ?? null);
            })
            ->map(function ($subcategory) {
                return [
                    'loc' => route('courses.subcategory.show', [
                        'category' => $subcategory->category->slug,
                        'subcategory' => $subcategory->slug,
                    ]),
                    'lastmod' => $subcategory->updated_at
                        ?? $subcategory->created_at
                        ?? $subcategory->category->updated_at
                        ?? $subcategory->category->created_at,
                    'changefreq' => 'weekly',
                    'priority' => 0.6,
                ];
            });
    }

    private function premiumCourseChildCategoryUrls(): Collection
    {
        return PremiumCourseChildCategory::query()
            ->with([
                'category:id,slug,updated_at,created_at',
                'subcategory:id,slug,updated_at,created_at',
            ])
            ->get()
            ->filter(function ($childCategory) {
                return filled($childCategory->slug ?? null)
                    && $childCategory->category
                    && filled($childCategory->category->slug ?? null)
                    && $childCategory->subcategory
                    && filled($childCategory->subcategory->slug ?? null);
            })
            ->map(function ($childCategory) {
                return [
                    'loc' => route('courses.child.show', [
                        'category' => $childCategory->category->slug,
                        'subcategory' => $childCategory->subcategory->slug,
                        'childCategory' => $childCategory->slug,
                    ]),
                    'lastmod' => $childCategory->updated_at
                        ?? $childCategory->created_at
                        ?? $childCategory->subcategory->updated_at
                        ?? $childCategory->subcategory->created_at,
                    'changefreq' => 'weekly',
                    'priority' => 0.6,
                ];
            });
    }

    // private function internationalStudentLifeUrls(): Collection
    // {
    //     return internationalStudentLife::query()
    //         ->get()
    //         ->map(function ($life) {
    //             return [
    //                 'loc' => route('international.student.life', ['id' => $life->id]),
    //                 'lastmod' => $life->updated_at ?? $life->created_at,
    //                 'changefreq' => 'monthly',
    //                 'priority' => 0.5,
    //             ];
    //         });
    // }

    private function buildXml(Collection $urls): string
    {
        $entries = $urls
            ->filter(fn ($url) => filled($url['loc'] ?? null))
            ->unique('loc')
            ->values();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

        foreach ($entries as $entry) {
            $xml .= '  <url>' . PHP_EOL;
            $xml .= '    <loc>' . htmlspecialchars((string) $entry['loc'], ENT_XML1) . '</loc>' . PHP_EOL;

            $lastmod = $this->formatDate($entry['lastmod'] ?? null);
            if ($lastmod) {
                $xml .= '    <lastmod>' . $lastmod . '</lastmod>' . PHP_EOL;
            }

            if (!empty($entry['changefreq'])) {
                $xml .= '    <changefreq>' . htmlspecialchars((string) $entry['changefreq'], ENT_XML1) . '</changefreq>' . PHP_EOL;
            }

            if (isset($entry['priority']) && $entry['priority'] !== '') {
                $priority = is_numeric($entry['priority'])
                    ? number_format((float) $entry['priority'], 1, '.', '')
                    : htmlspecialchars((string) $entry['priority'], ENT_XML1);

                $xml .= '    <priority>' . $priority . '</priority>' . PHP_EOL;
            }

            $xml .= '  </url>' . PHP_EOL;
        }

        $xml .= '</urlset>' . PHP_EOL;

        return $xml;
    }

    private function formatDate(mixed $value): ?string
    {
        if ($value instanceof CarbonInterface) {
            return $value->copy()->tz('UTC')->toAtomString();
        }

        if ($value instanceof DateTimeInterface) {
            return Carbon::instance($value)->tz('UTC')->toAtomString();
        }

        if (is_string($value) && $value !== '') {
            try {
                return Carbon::parse($value)->tz('UTC')->toAtomString();
            } catch (\Throwable) {
                return null;
            }
        }

        return null;
    }
}
