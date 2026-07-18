<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageContent;
use App\Traits\FlashNotifications;
use Illuminate\Http\Request;

class AdminPageContentController extends Controller
{
    use FlashNotifications;

    /**
     * Show the edit form for a specific page.
     */
    public function edit(string $page)
    {
        $allowedPages = ['about', 'faq', 'privacy', 'terms'];
        if (! in_array($page, $allowedPages)) {
            abort(404);
        }

        $contents = PageContent::where('page_key', $page)
            ->get()
            ->pluck('content_data', 'section_key')
            ->toArray();

        return view('admin.pages.edit', compact('page', 'contents'));
    }

    /**
     * Update the content of a specific page.
     */
    public function update(Request $request, string $page)
    {
        $allowedPages = ['about', 'faq', 'privacy', 'terms'];
        if (! in_array($page, $allowedPages)) {
            abort(404);
        }

        if ($page === 'about') {
            $request->validate([
                'hero.title' => 'required|string|max:255',
                'hero.subtitle' => 'required|string|max:255',
                'hero.description' => 'required|string|max:1000',
                'stats' => 'required|array',
                'stats.*.value' => 'required|string|max:255',
                'stats.*.label' => 'required|string|max:255',
                'values' => 'required|array',
                'values.*.icon' => 'required|string|max:255',
                'values.*.title' => 'required|string|max:255',
                'values.*.desc' => 'required|string|max:1000',
                'team' => 'required|array',
                'team.*.name' => 'required|string|max:255',
                'team.*.role' => 'required|string|max:255',
                'team.*.initials' => 'required|string|max:10',
                'team.*.gradient' => 'required|string|max:255',
            ]);

            PageContent::updateOrCreate(
                ['page_key' => 'about', 'section_key' => 'hero'],
                ['content_data' => $request->input('hero')]
            );
            PageContent::updateOrCreate(
                ['page_key' => 'about', 'section_key' => 'stats'],
                ['content_data' => array_values($request->input('stats', []))]
            );
            PageContent::updateOrCreate(
                ['page_key' => 'about', 'section_key' => 'values'],
                ['content_data' => array_values($request->input('values', []))]
            );
            PageContent::updateOrCreate(
                ['page_key' => 'about', 'section_key' => 'team'],
                ['content_data' => array_values($request->input('team', []))]
            );

        } elseif ($page === 'faq') {
            $request->validate([
                'hero.title' => 'required|string|max:255',
                'hero.description' => 'required|string|max:1000',
                'faqs' => 'required|array',
                'faqs.*.category' => 'required|string|max:255',
                'faqs.*.q' => 'required|string|max:1000',
                'faqs.*.a' => 'required|string|max:2000',
            ]);

            PageContent::updateOrCreate(
                ['page_key' => 'faq', 'section_key' => 'hero'],
                ['content_data' => $request->input('hero')]
            );
            PageContent::updateOrCreate(
                ['page_key' => 'faq', 'section_key' => 'faqs'],
                ['content_data' => array_values($request->input('faqs', []))]
            );

        } elseif ($page === 'privacy') {
            $request->validate([
                'hero.title' => 'required|string|max:255',
                'hero.description' => 'required|string|max:1000',
                'hero.last_updated' => 'required|string|max:255',
                'sections' => 'required|array',
                'sections.*.id' => 'required|string|max:255',
                'sections.*.icon' => 'required|string|max:255',
                'sections.*.title' => 'required|string|max:255',
                'sections.*.content' => 'required|string|max:2000',
            ]);

            PageContent::updateOrCreate(
                ['page_key' => 'privacy', 'section_key' => 'hero'],
                ['content_data' => $request->input('hero')]
            );
            PageContent::updateOrCreate(
                ['page_key' => 'privacy', 'section_key' => 'sections'],
                ['content_data' => array_values($request->input('sections', []))]
            );

        } elseif ($page === 'terms') {
            $request->validate([
                'hero.title' => 'required|string|max:255',
                'hero.description' => 'required|string|max:1000',
                'hero.effective_date' => 'required|string|max:255',
                'sections' => 'required|array',
                'sections.*.id' => 'required|string|max:255',
                'sections.*.icon' => 'required|string|max:255',
                'sections.*.title' => 'required|string|max:255',
                'sections.*.content' => 'required|string|max:2000',
            ]);

            PageContent::updateOrCreate(
                ['page_key' => 'terms', 'section_key' => 'hero'],
                ['content_data' => $request->input('hero')]
            );
            PageContent::updateOrCreate(
                ['page_key' => 'terms', 'section_key' => 'sections'],
                ['content_data' => array_values($request->input('sections', []))]
            );
        }

        $this->successNotification(ucfirst($page).' page contents updated successfully.');

        return redirect()->back();
    }
}
