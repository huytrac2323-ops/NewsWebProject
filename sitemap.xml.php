<?php
/**
 * V-NEWS CMS - Sơ đồ trang web Sitemap XML chuẩn SEO cho Google Search Console
 */
header('Content-Type: application/xml; charset=utf-8');
require_once __DIR__ . '/config/config.php';

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <!-- Trang chủ -->
    <url>
        <loc><?= url() ?></loc>
        <changefreq>hourly</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc><?= url('contact.php') ?></loc>
        <changefreq>monthly</changefreq>
        <priority>0.5</priority>
    </url>
    <url>
        <loc><?= url('page.php?slug=gioi-thieu') ?></loc>
        <changefreq>monthly</changefreq>
        <priority>0.5</priority>
    </url>

    <!-- Chuyên mục -->
    <?php
    try {
        $stmtCats = $pdo->query("SELECT slug FROM categories");
        while ($c = $stmtCats->fetch()) {
            echo '<url>';
            echo '<loc>' . htmlspecialchars(url('category.php?slug=' . urlencode($c['slug']))) . '</loc>';
            echo '<changefreq>daily</changefreq>';
            echo '<priority>0.8</priority>';
            echo '</url>';
        }
    } catch (Exception $e) {}
    ?>

    <!-- Bài viết đã xuất bản -->
    <?php
    try {
        $stmtArticles = $pdo->query("SELECT slug, created_at, updated_at FROM articles WHERE status = 'published' ORDER BY id DESC LIMIT 500");
        while ($a = $stmtArticles->fetch()) {
            $lastmod = !empty($a['updated_at']) ? date('c', strtotime($a['updated_at'])) : date('c', strtotime($a['created_at']));
            echo '<url>';
            echo '<loc>' . htmlspecialchars(url('article.php?slug=' . urlencode($a['slug']))) . '</loc>';
            echo '<lastmod>' . $lastmod . '</lastmod>';
            echo '<changefreq>weekly</changefreq>';
            echo '<priority>0.9</priority>';
            echo '</url>';
        }
    } catch (Exception $e) {}
    ?>
</urlset>
