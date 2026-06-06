<?php
// article-generator.php

// Handle form submit
$generatedFile = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title   = trim($_POST['title']   ?? '');
    $keyword = trim($_POST['keyword'] ?? '');

    if ($title === '' || $keyword === '') {
        $error = 'Please fill in both Title and Keyword.';
    } else {
        // Slug generator yang stabil
        $slug = strtolower($keyword);
        $slug = preg_replace('/\s+/', '-', $slug);        // spasi → dash
        $slug = preg_replace('/[^a-z0-9\-]/', '', $slug); // hapus selain huruf/angka/dash
        $slug = preg_replace('/-+/', '-', $slug);         // dash ganda → 1 dash
        $slug = trim($slug, '-');                         // hilangkan dash di pinggir

        if ($slug === '') {
            $slug = 'article-' . time();
        }

        // Directory for articles
        $outputDir = realpath(__DIR__ . '/../articles');
        if ($outputDir === false) {
            $outputDir = __DIR__ . '/../articles';
        }
        if (!is_dir($outputDir)) {
            @mkdir($outputDir, 0775, true);
        }

        $filePath = $outputDir . '/' . $slug . '.php';

        // If exists, add suffix
        if (file_exists($filePath)) {
            $filePath = $outputDir . '/' . $slug . '-' . time() . '.php';
        }

        $safeTitle    = htmlspecialchars($title,   ENT_QUOTES, 'UTF-8');
        $safeKeyword  = htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8');

        // Fixed image list (light theme)
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

        if (@file_put_contents($filePath, $fileContent) === false) {
            $error = 'Failed to write article file. Please check folder permissions.';
        } else {
            $generatedFile = [
                'path' => $filePath,
            ];
        }
    }
}
?>

<?php include('../template/start.php'); ?>

<section class="min-h-[60vh] flex items-center justify-center bg-slate-50 py-16">
  <div class="w-full max-w-xl mx-auto px-4 md:px-6">
    <div class="mb-8 text-center">
      <h1 class="text-2xl md:text-3xl font-semibold text-slate-900 mb-2">
        Article Generator
      </h1>
      <p class="text-sm text-slate-600">
        Type an article title and a main keyword. The generator will create a new PHP file 
        containing a short AI prompt and a Bali Diving thumbnail image.
      </p>
    </div>

    <?php if (!empty($error)): ?>
      <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 flex items-start gap-2">
        <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
        <span><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></span>
      </div>
    <?php endif; ?>

    <?php if (!empty($generatedFile)): ?>
      <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 flex items-start gap-3">
        <i class="fa-solid fa-circle-check mt-0.5"></i>
        <div>
          <p class="font-medium mb-1">Article prompt file created successfully.</p>
          <p class="text-xs text-emerald-700/90 break-all">
            File path: <code class="text-[11px]"><?php echo htmlspecialchars($generatedFile['path'], ENT_QUOTES, 'UTF-8'); ?></code>
          </p>
          <p class="text-xs text-emerald-700/80 mt-1">
            Open this file in the browser, copy the prompt, and paste it into your AI writer.
          </p>
        </div>
      </div>
    <?php endif; ?>

    <form method="post" class="space-y-5 bg-white border border-slate-200 rounded-2xl p-5 md:p-6 shadow-xl">
      <div>
        <label for="title" class="block text-sm font-medium text-slate-900 mb-1.5">
          Article Title
        </label>
        <input
          type="text"
          id="title"
          name="title"
          required
          placeholder="For example: Complete Guide to Diving in Bali"
          class="w-full rounded-xl bg-slate-50 border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500/80 focus:border-sky-500 transition"
          value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title'], ENT_QUOTES, 'UTF-8') : ''; ?>"
        >
      </div>

      <div>
        <label for="keyword" class="block text-sm font-medium text-slate-900 mb-1.5">
          Main Keyword
        </label>
        <input
          type="text"
          id="keyword"
          name="keyword"
          required
          placeholder="For example: Bali diving safety tips"
          class="w-full rounded-xl bg-slate-50 border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500/80 focus:border-sky-500 transition"
          value="<?php echo isset($_POST['keyword']) ? htmlspecialchars($_POST['keyword'], ENT_QUOTES, 'UTF-8') : ''; ?>"
        >
        <p class="mt-1 text-[11px] text-slate-500">
          This keyword will be inserted inside the AI prompt as the main SEO focus.
        </p>
      </div>

      <button
        type="submit"
        class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-sky-500 hover:bg-sky-600 text-sm font-medium text-white px-4 py-2.5 shadow-lg shadow-sky-500/30 transition"
      >
        <i class="fa-solid fa-wand-magic-sparkles text-xs"></i>
        <span>Generate Prompt File</span>
      </button>
    </form>
  </div>
</section>

<?php include('../template/end.php'); ?>
