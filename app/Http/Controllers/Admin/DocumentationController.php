<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\Environment\Environment;
use League\CommonMark\MarkdownConverter;

class DocumentationController extends Controller
{
    public function index()
    {
        $pages = $this->getPages();
        $first = $pages->first();

        if (! $first) {
            abort(404);
        }

        return redirect()->route('admin.docs.show', $first['slug']);
    }

    public function show(string $slug)
    {
        $pages = $this->getPages();
        $current = $pages->firstWhere('slug', $slug);

        if (! $current) {
            abort(404);
        }

        $path = resource_path("docs/{$slug}.md");
        $raw = File::get($path);

        // Strip frontmatter
        $content = preg_replace('/^---\s*\n.*?\n---\s*\n/s', '', $raw);

        // Convert internal doc links to proper routes
        $content = preg_replace_callback(
            '/\[([^\]]+)\]\(\/admin\/docs\/([a-z0-9-]+)\)/',
            fn ($m) => "[{$m[1]}](" . route('admin.docs.show', $m[2]) . ")",
            $content
        );

        $html = $this->convertMarkdown($content);

        return view('admin.docs.show', compact('pages', 'current', 'html', 'content'));
    }

    private function getPages()
    {
        $files = File::glob(resource_path('docs/*.md'));

        return collect($files)->map(function ($file) {
            $raw = File::get($file);
            $meta = $this->parseFrontmatter($raw);

            return [
                'slug' => pathinfo($file, PATHINFO_FILENAME),
                'title' => $meta['title'] ?? pathinfo($file, PATHINFO_FILENAME),
                'order' => (int) ($meta['order'] ?? 99),
            ];
        })->sortBy('order')->values();
    }

    private function parseFrontmatter(string $content): array
    {
        if (preg_match('/^---\s*\n(.*?)\n---\s*\n/s', $content, $matches)) {
            $meta = [];
            foreach (explode("\n", trim($matches[1])) as $line) {
                if (str_contains($line, ':')) {
                    [$key, $value] = explode(':', $line, 2);
                    $meta[trim($key)] = trim($value);
                }
            }
            return $meta;
        }

        return [];
    }

    private function convertMarkdown(string $markdown): string
    {
        $config = [];

        $environment = new Environment($config);
        $environment->addExtension(new \League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension());
        $environment->addExtension(new TableExtension());

        $converter = new MarkdownConverter($environment);

        return $converter->convert($markdown)->getContent();
    }
}
