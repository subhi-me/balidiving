<?php
// auto_article_creator.php
// Automatically creates a prompt article file from search queries

if (isset($_GET['q'])) {
    $rawQuery = trim((string)$_GET['q']);
    if ($rawQuery !== '') {
        // Clean and sanitize the query
        $keyword = strip_tags($rawQuery);
        // Retain only safe alphanumeric characters, spaces, dashes, and basic quotes/punctuation
        $keyword = preg_replace('/[^\w\s\-\?\!\'\"]+/u', '', $keyword);
        $keyword = preg_replace('/\s+/', ' ', trim($keyword));
        
        // Limit query length to prevent overly long filenames/titles
        $keyword = mb_substr($keyword, 0, 60);

        if ($keyword !== '') {
            // Generate clean slug for filename
            $slug = strtolower($keyword);
            $slug = preg_replace('/\s+/', '-', $slug);        // space -> dash
            $slug = preg_replace('/[^a-z0-9\-]/', '', $slug); // remove non-alphanumeric/dash
            $slug = preg_replace('/-+/', '-', $slug);         // double dash -> single dash
            $slug = trim($slug, '-');                         // trim dashes

            if ($slug !== '') {
                // Ensure output directory points specifically to the absolute path of main-website/articles
                $outputDir = realpath(__DIR__ . '/../articles');
                if ($outputDir === false) {
                    $outputDir = __DIR__ . '/../articles';
                }
                
                // Create the directory if it doesn't exist
                if (!is_dir($outputDir)) {
                    @mkdir($outputDir, 0775, true);
                }

                $filePath = $outputDir . '/' . $slug . '.php';

                // Check if file already exists to prevent duplicates or unauthorized overwriting
                if (!file_exists($filePath)) {
                    $title = ucwords($keyword);
                    $safeTitle = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
                    $safeKeyword = htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8');

                    // Thumbnail images pool
                    $thumbs = [
                        "https://balidiving.com/images/thumbnails/10-bali-diving-underwater.jpg",
                        "https://balidiving.com/images/thumbnails/11-bali-diving-underwater.jpg",
                        "https://balidiving.com/images/thumbnails/12-bali-diving-underwater.jpg",
                        "https://balidiving.com/images/thumbnails/13-bali-diving-underwater.jpg",
                        "https://balidiving.com/images/thumbnails/2-bali-diving-underwater.jpg",
                        "https://balidiving.com/images/thumbnails/3-bali-diving-underwater.jpg",
                        "https://balidiving.com/images/thumbnails/4-bali-diving-underwater.jpg",
                        "https://balidiving.com/images/thumbnails/5-bali-diving-underwater.jpg",
                        "https://balidiving.com/images/thumbnails/6-bali-diving-underwater.jpg",
                        "https://balidiving.com/images/thumbnails/7-bali-diving-underwater.jpg",
                        "https://balidiving.com/images/thumbnails/8-bali-diving-underwater.jpg",
                        "https://balidiving.com/images/thumbnails/9-bali-diving-underwater.jpg",
                    ];
                    $chosenImage = $thumbs[array_rand($thumbs)];

                    // Generate prompt content (identical template format as article-creator.php)
                    $fileContent = <<<PHP
<?php include('../template/start.php'); ?>

<section class="bg-slate-50" style="padding-top:100px; padding-bottom:100px;">
  <div class="max-w-3xl mx-auto px-4 md:px-6 text-slate-900">

    <header class="mb-10 text-center">
      <p class="text-[11px] tracking-[0.28em] uppercase text-sky-700 mb-3">
        Bali Diving · Article Prompt
      </p>

      <h1 class="text-3xl md:text-4xl font-semibold mb-4 leading-tight">
        {$safeTitle}
      </h1>

      <h2 class="text-lg md:text-xl text-slate-700 max-w-2xl mx-auto">
        Copy this prompt and paste it into your AI writer. The AI should return a ready-to-use Tailwind article section about "{$safeKeyword}" for Bali Diving.
      </h2>
    </header>

    <figure class="mb-10 overflow-hidden rounded-2xl border border-slate-200 shadow-xl bg-white">
      <img 
        src="{$chosenImage}"
        alt="{$safeKeyword}"
        class="w-full h-auto object-cover"
      >
    </figure>

    <article class="text-base leading-relaxed">

      <h3 class="text-xl font-semibold text-slate-900 mt-4 mb-3">
        AI Prompt (paste this into ChatGPT or similar)
      </h3>

      <div class="mt-4 rounded-2xl bg-slate-900 text-slate-50 text-sm p-5 md:p-6 shadow-lg overflow-x-auto">
        <pre class="whitespace-pre-wrap font-mono text-[12px] leading-relaxed">You are a senior copywriter and front-end developer for Bali Diving (Balidiving.com), a professional dive center in Bali, Indonesia.

Create ONE complete &lt;section&gt; in HTML using Tailwind CSS, in natural human-like English.

Requirements:
- Main article title (topic): "{$safeTitle}"
- Main SEO keyword (use naturally, not spam): "{$safeKeyword}"
- Location focus: Bali, Indonesia
- Brand: Bali Diving (Balidiving.com)

Writing style:
- Sound like a real human diver who knows Bali very well.
- Clear, simple English, friendly and professional.
- Give practical, specific tips connected to real diving in Bali.
- Keep paragraphs short and easy to read on mobile.

Layout (Tailwind, follow my style):
- Wrap everything in: &lt;section class="bg-slate-50 py-16"&gt; ... &lt;/section&gt;
- Inside section, use: &lt;div class="max-w-3xl mx-auto px-4 md:px-6 text-slate-900"&gt; ... &lt;/div&gt;
- Start with a &lt;header&gt; that has:
  - A small label line for Bali Diving.
  - An &lt;h1&gt; for the main title.
  - A short intro paragraph summarizing what the reader will get.
- Then add an &lt;article&gt; with:
  - 3–5 subsections using &lt;h2&gt; or &lt;h3&gt; and &lt;p&gt;.
  - At least one list (&lt;ul&gt; or &lt;ol&gt;) with concrete tips.
- Use Tailwind classes similar to:
  - Spacing: mb-4, mb-6, mt-10, py-16, px-4, md:px-6
  - Typography: text-slate-700, text-slate-900, text-lg, text-xl, font-semibold, leading-relaxed
  - Layout: max-w-3xl, mx-auto

Content guidelines:
- Explain why "{$safeKeyword}" is important for divers in Bali.
- Mention 2–4 real dive areas (for example: Tulamben, Amed, Padang Bai, Nusa Penida) when relevant.
- Connect everything to safe, relaxed, happy diving with Bali Diving.
- End with a short call-to-action inviting readers to contact Bali Diving or visit Balidiving.com.

Output rules:
- Return ONLY the HTML &lt;section&gt; block as final answer.
- Do NOT include &lt;html&gt;, &lt;head&gt;, &lt;body&gt;, or PHP.
- Do NOT mention that you are an AI.
- Do NOT include this prompt text in the output.</pre>
      </div>

    </article>
  </div>
</section>

<?php include('../template/end.php'); ?>
PHP;

                    @file_put_contents($filePath, $fileContent);
                }
            }
        }
    }
}
