<?php

$ebookPostTypes = array_values(array_filter(array_map(
    'trim',
    explode(',', (string) env('PRODUCT_EBOOK_POST_TYPES', 'product'))
)));

if ($ebookPostTypes === []) {
    $ebookPostTypes = ['product'];
}

$categoryTaxonomies = array_values(array_filter(array_map(
    'trim',
    explode(',', (string) env('PRODUCT_EBOOK_TAXONOMIES', 'product_cat'))
)));

if ($categoryTaxonomies === []) {
    $categoryTaxonomies = ['product_cat'];
}

$includedCategorySlugs = array_values(array_filter(array_map(
    'trim',
    explode(',', (string) env('PRODUCT_EBOOK_CATEGORY_SLUGS', 'ebook,books'))
)));

if ($includedCategorySlugs === []) {
    $includedCategorySlugs = ['ebook', 'books'];
}

$authorKeys = array_values(array_filter(array_map(
    'trim',
    explode(',', (string) env('PRODUCT_EBOOK_AUTHOR_KEYS', 'author,book_author,_book_author,_author'))
)));

$isbnKeys = array_values(array_filter(array_map(
    'trim',
    explode(',', (string) env('PRODUCT_EBOOK_ISBN_KEYS', 'isbn,_isbn,book_isbn'))
)));

$languageKeys = array_values(array_filter(array_map(
    'trim',
    explode(',', (string) env('PRODUCT_EBOOK_LANGUAGE_KEYS', 'language,book_language,_language'))
)));

$pagesKeys = array_values(array_filter(array_map(
    'trim',
    explode(',', (string) env('PRODUCT_EBOOK_PAGES_KEYS', 'pages,page_count,book_pages,_pages'))
)));

$formatKeys = array_values(array_filter(array_map(
    'trim',
    explode(',', (string) env('PRODUCT_EBOOK_FORMAT_KEYS', 'format,book_format,_format'))
)));

$priceKeys = array_values(array_filter(array_map(
    'trim',
    explode(',', (string) env('PRODUCT_EBOOK_PRICE_KEYS', '_price,price,sale_price'))
)));

$oldPriceKeys = array_values(array_filter(array_map(
    'trim',
    explode(',', (string) env('PRODUCT_EBOOK_OLD_PRICE_KEYS', '_regular_price,regular_price,old_price'))
)));

$downloadUrlKeys = array_values(array_filter(array_map(
    'trim',
    explode(',', (string) env('PRODUCT_EBOOK_DOWNLOAD_KEYS', 'download_url,ebook_download_url,_downloadable_files,_file_url,file_url'))
)));

$externalUrlKeys = array_values(array_filter(array_map(
    'trim',
    explode(',', (string) env('PRODUCT_EBOOK_EXTERNAL_KEYS', '_product_url,external_url,book_url,purchase_url'))
)));

$coverImageKeys = array_values(array_filter(array_map(
    'trim',
    explode(',', (string) env('PRODUCT_EBOOK_COVER_KEYS', 'cover_image,book_cover,_thumbnail_url,image'))
)));

$excerptKeys = array_values(array_filter(array_map(
    'trim',
    explode(',', (string) env('PRODUCT_EBOOK_EXCERPT_KEYS', 'excerpt,short_description,_short_description'))
)));

return [
    'table_prefix' => env('PRODUCT_TABLE_PREFIX', 'wpao_'),
    'ebook_post_types' => $ebookPostTypes,
    'category_taxonomies' => $categoryTaxonomies,
    'included_category_slugs' => $includedCategorySlugs,
    'require_downloadable' => filter_var(env('PRODUCT_EBOOK_REQUIRE_DOWNLOADABLE', true), FILTER_VALIDATE_BOOL),
    'meta_keys' => [
        'author' => $authorKeys,
        'isbn' => $isbnKeys,
        'language' => $languageKeys,
        'pages' => $pagesKeys,
        'format' => $formatKeys,
        'price' => $priceKeys,
        'old_price' => $oldPriceKeys,
        'download_url' => $downloadUrlKeys,
        'external_url' => $externalUrlKeys,
        'cover_image' => $coverImageKeys,
        'excerpt' => $excerptKeys,
    ],
];
