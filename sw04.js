/* sw04.js (GENERAL FIX)
   - BYPASS semua POST/PUT/PATCH/DELETE (terutama checkout)
   - BYPASS semua request ke /template/api/
   - Hanya handle caching untuk GET HTML/navigation saja
*/

self.addEventListener("fetch", (event) => {
  const req = event.request;
  const url = new URL(req.url);

  // Debug (optional)
  // console.log("SW fetch:", req.method, url.pathname);

  // ✅ 1) Jangan pernah intercept API
  if (url.pathname.startsWith("/template/api/")) return;

  // ✅ 2) Jangan pernah intercept method selain GET (POST checkout harus murni network)
  if (req.method !== "GET") return;

  // ✅ 3) Kalau request minta JSON, jangan dihandle SW
  const accept = req.headers.get("accept") || "";
  if (accept.includes("application/json")) return;

  // ✅ 4) Hanya handle navigation/documents
  const isNav = req.mode === "navigate" || accept.includes("text/html");
  if (!isNav) return;

  event.respondWith((async () => {
    try {
      // network-first untuk halaman
      const res = await fetch(req);
      return res;
    } catch (e) {
      // fallback offline jika kamu punya
      const cached = await caches.match(req);
      if (cached) return cached;

      // fallback minimal
      return new Response("Offline", {
        status: 503,
        headers: { "Content-Type": "text/plain; charset=utf-8" }
      });
    }
  })());
});
