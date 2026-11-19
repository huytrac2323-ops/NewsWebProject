<?php
/**
 * V-NEWS CMS - Kênh cấp tin RSS 2.0 (RSS Feed)
 */
header('Content-Type: application/rss+xml; charset=utf-8');
require_once __DIR__ . '/config/config.php';

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
<channel>
    <title><?= htmlspecialchars(get_setting('site_name', 'V-NEWS 24/7')) ?></title>
    <link><?= url() ?></link>
    <description><?= htmlspecialchars(get_setting('site_description', 'Kênh tin tức trực tuyến')) ?></description>
    <language>vi-VN</language>
    <atom:link href="<?= url('rss.xml.php') ?>" rel="self" type="application/rss+xml" />

    <?php
    try {
        $stmt = $pdo->query("
            SELECT a.*, c.name as category_name 
            FROM articles a
            LEFT JOIN categories c ON a.category_id = c.id
            WHERE a.status = 'published' 
            ORDER BY a.id DESC LIMIT 20
        ");
        while ($post = $stmt->fetch()) {
            echo '<item>';
            echo '<title>' . htmlspecialchars($post['title']) . '</title>';
            echo '<link>' . htmlspecialchars(url('article.php?slug=' . urlencode($post['slug']))) . '</link>';
            echo '<guid>' . htmlspecialchars(url('article.php?slug=' . urlencode($post['slug']))) . '</guid>';
            echo '<pubDate>' . date(DATE_RSS, strtotime($post['created_at'])) . '</pubDate>';
            echo '<category>' . htmlspecialchars($post['category_name']) . '</category>';
            echo '<description><![CDATA[' . htmlspecialchars($post['excerpt']) . ']]></description>';
            echo '</item>';
        }
    } catch (Exception $e) {}
    ?>
</channel>
</rss>
