<?php
// ============================================
// ПАРСИНГ ДАННЫХ ИЗ CSV С ЗАЩИТОЙ ОТ ОШИБОК
// ============================================
$csvFile = 'forbidden-city-visitors.csv';
$data = [];
if (file_exists($csvFile)) {
    $handle = fopen($csvFile, 'r');
    if ($handle) {
        $bom = fread($handle, 3);
        if ($bom != "\xef\xbb\xbf") rewind($handle);
        $header = fgetcsv($handle, 0, ',', '"', '\\');
        while (($row = fgetcsv($handle, 0, ',', '"', '\\')) !== false) {
            if (count($row) > 0 && $row[0] !== '') $data[] = $row;
        }
        fclose($handle);
    }
}

$yearData = [];
foreach ($data as $row) {
    $year = trim($row[0]);
    $monthlyValues = [];
    for ($i = 1; $i <= 12; $i++) {
        $monthlyValues[] = (int)str_replace([' ', ','], '', trim($row[$i]));
    }
    $total = (int)str_replace([' ', ','], '', trim($row[13]));
    if ($total > 0) {
        $yearData[] = ['year' => $year, 'monthly' => $monthlyValues, 'total' => $total];
    }
}

// Защита на случай отсутствия CSV
if (empty($yearData)) {
    $yearData = [['year' => '2000', 'monthly' => array_fill(0, 12, 0), 'total' => 0]];
}

$years = array_map(function($item) { return $item['year']; }, $yearData);
$totals = array_map(function($item) { return $item['total']; }, $yearData);
$monthsChinese = ['正月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月'];
$monthsEnglish = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

$buildingInventory = [
    ['id' => 1, 'name_en' => 'Main Ceremonial Halls', 'name_zh' => '主要礼仪殿堂', 'count' => 3],
    ['id' => 2, 'name_en' => 'Primary Imperial Palaces', 'name_zh' => '主要皇宫', 'count' => 3],
    ['id' => 3, 'name_en' => 'Consort & Prince Palaces', 'name_zh' => '妃子与皇子宫殿', 'count' => 12],
    ['id' => 4, 'name_en' => 'Secondary Residential Halls', 'name_zh' => '次要住宅殿堂', 'count' => 40],
    ['id' => 5, 'name_en' => 'Pavilions and Gazebos', 'name_zh' => '亭阁', 'count' => 100],
    ['id' => 6, 'name_en' => 'Gates and Gatehouses', 'name_zh' => '门楼', 'count' => 45],
    ['id' => 7, 'name_en' => 'Service Buildings', 'name_zh' => '服务建筑', 'count' => 750],
    ['id' => 8, 'name_en' => 'Corner Towers', 'name_zh' => '角楼', 'count' => 4],
];

$landmarkData = [
    ['name_en' => 'Hall of Supreme Harmony', 'name_zh' => '太和殿', 'visitors' => 2500000],
    ['name_en' => 'Hall of Central Harmony', 'name_zh' => '中和殿', 'visitors' => 1800000],
    ['name_en' => 'Hall of Preserving Harmony', 'name_zh' => '保和殿', 'visitors' => 1600000],
    ['name_en' => 'Palace of Heavenly Purity', 'name_zh' => '乾清宫', 'visitors' => 2200000],
    ['name_en' => 'Palace of Earthly Tranquility', 'name_zh' => '坤宁宫', 'visitors' => 1900000],
    ['name_en' => 'Hall of Mental Cultivation', 'name_zh' => '养心殿', 'visitors' => 2100000],
    ['name_en' => 'Belvedere of Benevolence', 'name_zh' => '慈宁宫', 'visitors' => 1400000],
];

$yearDataJson = json_encode($yearData, JSON_UNESCAPED_UNICODE);
$yearsJson = json_encode($years);
$totalsJson = json_encode($totals);
$monthsChineseJson = json_encode($monthsChinese, JSON_UNESCAPED_UNICODE);
$monthsEnglishJson = json_encode($monthsEnglish);
$buildingNamesEnJson = json_encode(array_column($buildingInventory, 'name_en'));
$buildingNamesChJson = json_encode(array_column($buildingInventory, 'name_zh'), JSON_UNESCAPED_UNICODE);
$buildingCountsJson = json_encode(array_column($buildingInventory, 'count'));
$landmarkNamesEnJson = json_encode(array_column($landmarkData, 'name_en'));
$landmarkNamesChJson = json_encode(array_column($landmarkData, 'name_zh'), JSON_UNESCAPED_UNICODE);
$landmarkVisitorsJson = json_encode(array_column($landmarkData, 'visitors'));

$totalAllVisits = array_sum($totals);
$maxVisits = empty($totals) ? 0 : max($totals);
$avgVisits = empty($totals) ? 0 : round($totalAllVisits / count($totals));
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
  <title>Forbidden City Portal</title>

  <!-- Шрифты -->
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Noto+Serif+SC:wght@400;600;700&family=Domine&family=PT+Serif+Caption&family=PT+Serif&display=swap" rel="stylesheet">
  
  <!-- Библиотеки и стили -->
  <link rel="stylesheet" type="text/css" href="./css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="./css/animate.min.css">
  <link rel="stylesheet" type="text/css" href="style.css?v=20260817">

  <script src="https://cdn.jsdelivr.net/npm/echarts@5/dist/echarts.min.js"></script>
  <script type="importmap">
  {
    "imports": {
      "three": "./js/three.module.js",
      "three/addons/": "./js/"
    }
  }
  </script>

  <style>
    :root {
      --primary: #541b1e;
      --accent: #d4af37;
      --paper1: #faf3e6;
      --paper2: #f5e6d3;
    }

    html, body { margin: 0; padding: 0; width: 100%; min-height: 100vh; font-family: 'Playfair Display', serif; background: linear-gradient(135deg, var(--paper1) 0%, var(--paper2) 100%); }
    body.lang-zh { font-family: 'Noto Serif SC', serif; }
    body.lang-en .lang-zh-content { display: none !important; }
    body.lang-zh .lang-en-content { display: none !important; }
    body.mode-fullscreen { overflow: hidden; height: 100vh; }

    /* ================= НАВИГАЦИЯ ================= */
    .top-nav-wrapper {
      position: fixed; top: 15px; left: 50%; transform: translateX(-50%); z-index: 999999; display: flex; gap: 10px;
      background: rgba(255, 255, 255, 0.95); border: 2px solid var(--primary); border-radius: 12px; padding: 8px 15px;
      box-shadow: 0 4px 15px rgba(84, 27, 30, 0.3); backdrop-filter: blur(5px); align-items: center; flex-wrap: wrap;
    }
    .nav-tab { background: none; border: none; font-size: 14px; font-weight: 700; color: var(--primary); cursor: pointer; padding: 6px 12px; border-radius: 6px; transition: 0.3s; }
    .nav-tab:hover { background: rgba(212, 175, 55, 0.15); }
    .nav-tab.active { background: var(--primary); color: white; }
    .global-lang-switcher { display: flex; gap: 5px; border-left: 2px solid var(--accent); padding-left: 10px; margin-left: 5px; }
    .gl-btn { background: none; border: 1px solid var(--primary); color: var(--primary); border-radius: 4px; padding: 4px 8px; font-size: 12px; cursor: pointer; font-weight: bold; }
    .gl-btn.active { background: var(--primary); color: white; }

    /* ================= СЕКЦИИ (ИСПРАВЛЕНО) ================= */
    .app-section { display: none; width: 100%; min-height: 100vh; position: relative; z-index: 10; padding-top: 80px; }
    .app-section.active { display: block; }
    #section-home { padding-top: 0; } /* Убрали глобальный отступ для главной, чтобы фон доходил до верха */
    #section-map.active, #section-game.active { padding-top: 0; position: fixed; inset: 0; z-index: 20; overflow: hidden; background: linear-gradient(135deg, var(--paper1) 0%, var(--paper2) 100%); }

    /* ===== 图库 cell + caption + lightbox ===== */
    .fc-gallery__cell {
      position: relative;
      overflow: hidden;
      border-radius: 12px;
      margin-bottom: 18px;
      cursor: zoom-in;
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .fc-gallery__cell img { display: block; width: 100%; transition: transform 0.4s ease; }
    .fc-gallery__cell:hover img { transform: scale(1.06); }
    .fc-gallery__caption {
      position: absolute; left: 0; right: 0; bottom: 0;
      padding: 14px 16px 16px;
      background: linear-gradient(180deg, rgba(84,27,30,0) 0%, rgba(84,27,30,0.88) 70%, rgba(84,27,30,0.95) 100%);
      color: #f3e2c4;
      transform: translateY(35%);
      transition: transform 0.35s ease;
      pointer-events: none;
    }
    .fc-gallery__cell:hover .fc-gallery__caption { transform: translateY(0); }
    .fc-gallery__title { font-size: 16px; font-weight: 700; margin: 0 0 4px 0; color: #f6dfac; letter-spacing: 1px; }
    .fc-gallery__desc { font-size: 12px; line-height: 1.5; margin: 0; opacity: 0.95; }
    @media (max-width: 991px) {
      .fc-gallery__caption { transform: translateY(0); padding: 10px 12px; }
      .fc-gallery__title { font-size: 14px; }
      .fc-gallery__desc { display: none; }
    }
    /* Lightbox */
    .fc-lightbox {
      position: fixed; inset: 0; z-index: 9999999;
      display: none; align-items: center; justify-content: center;
      background: rgba(10,3,4,0.94); backdrop-filter: blur(8px);
    }
    .fc-lightbox.open { display: flex; animation: fc-fade-in 0.25s ease; }
    @keyframes fc-fade-in { from { opacity: 0; } to { opacity: 1; } }
    .fc-lightbox__img {
      max-width: 90vw; max-height: 82vh;
      object-fit: contain; border-radius: 6px;
      box-shadow: 0 16px 50px rgba(0,0,0,0.6);
    }
    .fc-lightbox__close, .fc-lightbox__prev, .fc-lightbox__next {
      position: absolute; background: rgba(84,27,30,0.7); color: #f6dfac;
      border: 2px solid #d4af37; border-radius: 50%;
      width: 56px; height: 56px;
      display: flex; align-items: center; justify-content: center;
      cursor: pointer; font-size: 22px; font-weight: 700;
      transition: 0.25s; user-select: none;
    }
    .fc-lightbox__close:hover, .fc-lightbox__prev:hover, .fc-lightbox__next:hover {
      background: #d4af37; color: #541b1e; transform: scale(1.08);
    }
    .fc-lightbox__close { top: 24px; right: 24px; }
    .fc-lightbox__prev { left: 24px; top: 50%; transform: translateY(-50%); }
    .fc-lightbox__next { right: 24px; top: 50%; transform: translateY(-50%); }
    .fc-lightbox__prev:hover { transform: translateY(-50%) scale(1.08); }
    .fc-lightbox__next:hover { transform: translateY(-50%) scale(1.08); }
    .fc-lightbox__caption {
      position: absolute; left: 0; right: 0; bottom: 0;
      padding: 30px 24px 24px;
      background: linear-gradient(180deg, transparent 0%, rgba(10,3,4,0.85) 60%);
      color: #f3e2c4; text-align: center;
    }
    .fc-lightbox__title { font-size: 22px; font-weight: 700; color: #f6dfac; margin: 0 0 6px; letter-spacing: 2px; }
    .fc-lightbox__desc { font-size: 14px; opacity: 0.9; margin: 0; }
    .fc-lightbox__counter {
      position: absolute; top: 30px; left: 50%; transform: translateX(-50%);
      color: #d4af37; font-size: 14px; letter-spacing: 4px;
    }
    @media (max-width: 600px) {
      .fc-lightbox__close, .fc-lightbox__prev, .fc-lightbox__next { width: 44px; height: 44px; font-size: 18px; }
    }

    /* ===== Visit Info 参观指南 ===== */
    #section-visit { padding-top: 90px; padding-bottom: 60px; min-height: 100vh; background: linear-gradient(180deg, #faf3e6 0%, #f5ead2 100%); }
    .fc-visit__header { padding: 30px 0 36px; }
    .fc-visit__crest { color: #d4af37; font-size: 26px; letter-spacing: 12px; margin-bottom: 6px; }
    .fc-visit__title { font-family: 'Noto Serif SC', 'Playfair Display', serif; font-size: 42px; color: #541b1e; font-weight: 700; margin: 0 0 10px; letter-spacing: 4px; }
    .fc-visit__divider { width: 120px; height: 3px; background: linear-gradient(90deg, transparent 0%, #d4af37 50%, transparent 100%); margin: 0 auto 16px; }
    .fc-visit__sub { font-size: 15px; color: #6b4a26; line-height: 1.8; max-width: 680px; margin: 0 auto; font-style: italic; }
    .fc-visit__layout { display: grid; grid-template-columns: 1fr 1fr; gap: 36px; align-items: start; margin-bottom: 40px; }
    .fc-visit__map { position: sticky; top: 100px; }
    .fc-visit__map-frame { position: relative; width: 100%; padding-top: 100%; border: 3px solid #d4af37; border-radius: 12px; box-shadow: 0 8px 30px rgba(84,27,30,0.18); background: #fff; overflow: hidden; }
    .fc-visit__map-frame iframe { position: absolute; inset: 0; width: 100%; height: 100%; }
    .fc-visit__map-pin { position: absolute; top: 14px; left: 14px; background: #541b1e; color: #f6dfac; width: 42px; height: 42px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 22px; border: 2px solid #d4af37; box-shadow: 0 4px 12px rgba(0,0,0,0.3); z-index: 2; }
    .fc-visit__map-note { margin-top: 12px; padding: 10px 16px; background: rgba(255,255,255,0.85); border-left: 4px solid #d4af37; border-radius: 4px; color: #5b3a16; font-size: 13px; font-style: italic; }
    .fc-visit__panel { background: rgba(255,255,255,0.85); border: 2px solid #d4af37; border-radius: 12px; overflow: hidden; box-shadow: 0 8px 30px rgba(84,27,30,0.15); }
    .fc-visit__tabs { display: grid; grid-template-columns: repeat(4, 1fr); border-bottom: 2px solid #d4af37; background: linear-gradient(180deg, #f8efd6 0%, #efe2bf 100%); }
    .fc-visit__tab { background: none; border: none; padding: 16px 8px; cursor: pointer; font-family: 'Noto Serif SC', serif; font-size: 14px; font-weight: 700; color: #7c1418; transition: 0.25s; border-right: 1px solid rgba(212,175,55,0.3); letter-spacing: 1px; }
    .fc-visit__tab:last-child { border-right: none; }
    .fc-visit__tab:hover { background: rgba(212,175,55,0.2); }
    .fc-visit__tab.active { background: #541b1e; color: #f6dfac; box-shadow: inset 0 -3px 0 #d4af37; }
    .fc-visit__panels { padding: 28px 26px; min-height: 360px; }
    .fc-visit__pane { display: none; animation: fc-fade-in 0.3s ease; }
    .fc-visit__pane.active { display: block; }
    .fc-visit__gate-title { font-family: 'Noto Serif SC', serif; font-size: 22px; color: #541b1e; font-weight: 700; margin: 0 0 14px; padding-bottom: 10px; border-bottom: 2px dashed #d4af37; letter-spacing: 1px; }
    .fc-visit__gate-desc { font-size: 14px; line-height: 1.85; color: #4a2a2c; margin: 0 0 18px; }
    .fc-visit__meta { list-style: none; padding: 0; margin: 0; }
    .fc-visit__meta li { padding: 10px 12px; margin-bottom: 8px; background: linear-gradient(90deg, rgba(212,175,55,0.10) 0%, transparent 100%); border-left: 3px solid #d4af37; font-size: 13px; color: #4a2a2c; display: flex; flex-direction: column; gap: 3px; }
    .fc-visit__meta li strong { color: #7c1418; font-size: 12px; letter-spacing: 1px; text-transform: uppercase; }
    .fc-visit__notice { background: linear-gradient(135deg, #541b1e 0%, #7c1418 100%); color: #f3e2c4; border: 2px solid #d4af37; border-radius: 12px; padding: 24px 30px; text-align: center; box-shadow: 0 8px 24px rgba(84,27,30,0.25); position: relative; }
    .fc-visit__notice::before, .fc-visit__notice::after { content: "✦"; position: absolute; top: 12px; color: #d4af37; font-size: 14px; }
    .fc-visit__notice::before { left: 20px; }
    .fc-visit__notice::after { right: 20px; }
    .fc-visit__notice-title { font-family: 'Noto Serif SC', serif; font-size: 20px; color: #f6dfac; letter-spacing: 4px; margin-bottom: 10px; }
    .fc-visit__notice-body { font-size: 13px; line-height: 1.8; color: #f3e2c4; max-width: 780px; margin: 0 auto; opacity: 0.95; }
    @media (max-width: 992px) {
      .fc-visit__layout { grid-template-columns: 1fr; }
      .fc-visit__map { position: static; }
      .fc-visit__title { font-size: 32px; }
      .fc-visit__tabs { grid-template-columns: repeat(2, 1fr); }
      .fc-visit__tab { border-bottom: 1px solid rgba(212,175,55,0.3); }
      .fc-visit__tab:nth-child(2) { border-right: none; }
    }
    @media (max-width: 576px) {
      .fc-visit__title { font-size: 26px; }
      .fc-visit__sub { font-size: 13px; }
      .fc-visit__panels { padding: 20px 16px; }
    }

    /* ===== Hero 下方 About 独立区 ===== */
    .fc-about-block {
      background: linear-gradient(135deg, #541b1e 0%, #6a2429 100%);
      padding: 100px 24px;
      text-align: center;
      color: #f3e2c4;
    }
    .fc-about-block__title {
      font-family: 'Noto Serif SC', 'Playfair Display', serif;
      font-size: clamp(28px, 3.5vw, 44px);
      font-weight: 700;
      letter-spacing: 4px;
      color: #f6dfac;
      margin: 0 0 30px 0;
      position: relative;
      display: inline-block;
    }
    .fc-about-block__title::before, .fc-about-block__title::after {
      content: "✦";
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      color: #d4af37;
      font-size: 14px;
    }
    .fc-about-block__title::before { left: -36px; }
    .fc-about-block__title::after { right: -36px; }
    .fc-about-block__lead {
      max-width: 820px;
      margin: 0 auto;
      font-size: clamp(15px, 1.4vw, 18px);
      line-height: 2;
      color: #f3e2c4;
      letter-spacing: 0.5px;
    }

    /* ===== 首页 全屏 Hero（沉浸式） ===== */
    .fc-culture-banner {
      position: relative;
      width: 100%;
      height: 100vh;
      min-height: 600px;
      display: flex; align-items: center; justify-content: center;
      overflow: hidden;
      background: #1a0708;
    }
    .fc-culture-banner__bg {
      position: absolute; inset: 0; z-index: 1;
    }
    .fc-culture-banner__bg img {
      width: 100%; height: 100%;
      object-fit: cover; object-position: center 35%;
      display: block;
      filter: brightness(0.78) saturate(1.08);
    }
    .fc-culture-banner__bg::after {
      content: ""; position: absolute; inset: 0;
      background:
        linear-gradient(180deg, rgba(20,6,8,0.55) 0%, rgba(20,6,8,0.20) 35%, rgba(20,6,8,0.20) 65%, rgba(20,6,8,0.78) 100%),
        radial-gradient(ellipse at center, transparent 30%, rgba(20,6,8,0.45) 100%);
      pointer-events: none;
    }
    .fc-culture-banner__text {
      position: relative; z-index: 2;
      text-align: center;
      padding: 80px 24px;
      max-width: 1000px;
    }
    .fc-culture-banner__title {
      font-family: 'Noto Serif SC', 'Playfair Display', serif;
      font-size: clamp(56px, 9vw, 120px);
      font-weight: 700;
      letter-spacing: clamp(4px, 1vw, 14px);
      margin: 0;
      color: #f6dfac;
      text-shadow:
        0 4px 30px rgba(0,0,0,0.7),
        0 2px 0 rgba(212,175,55,0.35);
      line-height: 1.1;
    }
    .fc-culture-banner__title small {
      display: block;
      font-size: clamp(12px, 1.2vw, 16px);
      letter-spacing: clamp(4px, 0.8vw, 10px);
      color: rgba(246,223,172,0.82);
      font-weight: 400;
      margin-top: 18px;
      font-family: 'Playfair Display', 'Times New Roman', serif;
    }
    .fc-culture-banner__divider {
      width: clamp(80px, 12vw, 140px); height: 2px;
      margin: clamp(20px, 3vw, 32px) auto;
      background: linear-gradient(90deg, transparent, #d4af37 50%, transparent);
    }
    .fc-culture-banner__sub {
      font-size: clamp(15px, 1.4vw, 20px);
      line-height: 1.9;
      color: rgba(246,223,172,0.95);
      margin: 0;
      letter-spacing: 1px;
      text-shadow: 0 2px 10px rgba(0,0,0,0.6);
      max-width: 720px; margin-left: auto; margin-right: auto;
    }
    .fc-culture-banner__corner {
      position: absolute; width: clamp(50px, 7vw, 80px); height: clamp(50px, 7vw, 80px);
      border: 2px solid #d4af37; opacity: 0.85;
      z-index: 3;
      top: clamp(60px, 8vh, 90px);
    }
    .fc-culture-banner__corner--tl { left: 32px; border-right: none; border-bottom: none; }
    .fc-culture-banner__corner--tr { right: 32px; border-left: none; border-bottom: none; }
    .fc-culture-banner__corner--bl {
      top: auto; bottom: 100px;
      left: 32px; border-right: none; border-top: none;
    }
    .fc-culture-banner__corner--br {
      top: auto; bottom: 100px;
      right: 32px; border-left: none; border-top: none;
    }
    .fc-culture-banner__scroll {
      position: absolute; left: 50%; bottom: 28px;
      transform: translateX(-50%);
      z-index: 3; color: rgba(246,223,172,0.9);
      font-size: 12px; letter-spacing: 4px;
      animation: fc-bounce 2.4s ease-in-out infinite;
      pointer-events: none;
    }
    .fc-culture-banner__scroll::after {
      content: ""; display: block;
      width: 1px; height: 36px;
      margin: 8px auto 0;
      background: linear-gradient(180deg, rgba(246,223,172,0.7), transparent);
    }
    @keyframes fc-bounce {
      0%, 100% { transform: translate(-50%, 0); opacity: 0.85; }
      50% { transform: translate(-50%, 10px); opacity: 0.4; }
    }
    @media (max-width: 600px) {
      .fc-culture-banner__corner { width: 36px; height: 36px; top: 70px; }
      .fc-culture-banner__corner--bl { bottom: 70px; }
      .fc-culture-banner__corner--br { bottom: 70px; }
    }

    /* ================= ИНДЕКС: СТИЛИ БЛОКОВ ================= */
    /* Тёмно-красный фон заходит под переключатель */
    #bloc-1, #bloc-1-zh { padding-top: 90px !important; background-color: var(--primary) !important; }
    .language-switcher { display: none !important; }
    
    /* Timeline */
    .fc-timeline-wrapper { padding: 60px 0; position: relative; background: #541b1e; }
    .fc-timeline-wrapper h2 { color: #f6dfac !important; }
    .fc-timeline { position: relative; max-width: 1000px; margin: 0 auto; display: flex; justify-content: space-between; align-items: flex-start; }
    .fc-timeline::before { content: ''; position: absolute; top: 40px; left: 5%; right: 5%; height: 3px; background: #d4af37; z-index: 1; }
    .fc-timeline-item { position: relative; z-index: 2; width: 15%; text-align: center; transition: 0.3s; }
    .fc-timeline-item:hover { transform: translateY(-10px); }
    .fc-timeline-year { font-size: 1.3rem; font-weight: 700; color: #f6dfac; margin-bottom: 8px; font-family: 'Domine', serif; background: #541b1e; display: inline-block; padding: 0 10px; }
    .fc-timeline-icon { width: 64px; height: 64px; margin: 0 auto 12px; display: flex; align-items: center; justify-content: center; background: #fff9ef; border: 2px solid #d4af37; border-radius: 50%; box-shadow: 0 4px 14px rgba(0,0,0,0.3); transition: 0.4s; position: relative; z-index: 3; }
    .fc-timeline-icon svg { width: 36px; height: 36px; }
    .fc-timeline-item:hover .fc-timeline-icon { transform: rotate(8deg) scale(1.1); box-shadow: 0 6px 20px rgba(212,175,55,0.6); }
    .fc-timeline-dot { display: none; }
    .fc-timeline-content { background: #fff9ef; border: 1px solid #d4af37; padding: 14px 12px; border-radius: 12px; box-shadow: 0 8px 20px rgba(0,0,0,0.2); font-size: 0.85rem; color: #5b3a16; line-height: 1.55; }
    /* 6 个时间点连不上，用横线横向扫通 */
    .fc-timeline::before { content: ''; position: absolute; top: 50%; left: 4%; right: 4%; height: 3px; background: linear-gradient(90deg, transparent 0%, #d4af37 5%, #d4af37 95%, transparent 100%); z-index: 1; transform: translateY(20px); }
    /* icon 样式以位置定位代替 transform */
    @media (max-width: 900px) {
      .fc-timeline { flex-wrap: wrap; gap: 30px 16px; }
      .fc-timeline-item { width: 30%; }
    }
    @media (max-width: 600px) {
      .fc-timeline-item { width: 45%; }
    }
    .fc-timeline-title { font-weight: bold; color: #7c1418; margin-bottom: 5px; display: block; }
    
    /* Route */
    .fc-route-container { padding: 40px 0 60px 0; }
    .fc-route-row { position: relative; display: flex; flex-wrap: wrap; justify-content: center; }
    .fc-route-step { text-align: center; padding: 25px 20px 20px; border: 1px solid #e7d7b5; border-radius: 16px; background: #fff9ef; margin: 30px 10px 20px; box-shadow: 0 10px 25px rgba(84,27,30,.08); flex: 1; min-width: 220px; max-width: 280px; z-index: 2; transition: 0.3s; }
    .fc-route-step:hover { transform: translateY(-8px); box-shadow: 0 15px 35px rgba(84,27,30,.15); border-color: #d4af37; }
    .fc-route-icon { width: 46px; height: 46px; background: radial-gradient(circle at 30% 30%, #ffdf8c 0%, #d4af37 45%, #541b1e 100%); border-radius: 50%; color: #fff4d2; font-size: 20px; font-weight: bold; line-height: 46px; margin: -48px auto 15px auto; box-shadow: 0 4px 10px rgba(84,27,30,.4); }
    .fc-route-title { font-weight: 700; color: #541b1e; font-size: 1.15rem; margin-bottom: 8px; }
    .fc-route-desc { color: #7b6a54; font-size: 0.9rem; line-height: 1.5; }
    
    /* Artifacts */
    .fc-artifact-container { padding: 60px 0; }
    .fc-artifact-card { background: #541b1e; border: 1px solid #d4af37; border-radius: 16px; max-width: 600px; margin: 0 auto; overflow: hidden; box-shadow: 0 15px 35px rgba(0,0,0,.3); transition: 0.3s; }
    .fc-artifact-card:hover { transform: translateY(-5px); box-shadow: 0 20px 40px rgba(212, 175, 55, 0.2); }
    .fc-artifact-img-wrap { width: 100%; height: 300px; background: #111; border-bottom: 2px solid #d4af37; }
    .fc-artifact-img-wrap img { width: 100%; height: 100%; object-fit: contain; object-position: center; display: block; }
    .fc-artifact-info { padding: 25px; text-align: center; }
    .fc-artifact-dynasty { display: inline-block; background: #d4af37; color: #541b1e; padding: 4px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: bold; margin-bottom: 12px; }
    .fc-artifact-title { color: #f6dfac; font-size: 1.5rem; font-weight: 700; margin-bottom: 15px; }
    .fc-artifact-desc { color: #ecdcb9; line-height: 1.6; font-size: 0.95rem; margin: 0; }
    .fc-slider-arrow { background: #541b1e; color: #f6dfac; border: 1px solid #d4af37; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px; box-shadow: 0 4px 10px rgba(0,0,0,.4); transition: 0.3s; }

    /* Footer - тёмно-красный */
    .fc-footer { background: var(--primary); padding: 40px 0; margin-top: 50px; text-align: center; border-top: 2px solid var(--accent); }
    .fc-footer a { color: #f6dfac; font-weight: bold; text-decoration: none; font-size: 16px; letter-spacing: 1px; }

    /* Фонари */
    .lantern { position: fixed; width: 30px; height: 40px; background: rgb(84, 27, 30); border-radius: 50%; box-shadow: inset 0 0 15px rgba(255, 200, 0, 0.6), 0 0 15px rgba(84, 27, 30, 0.8); z-index: 5; pointer-events: none;}
    .lantern::before, .lantern::after { content: ''; position: absolute; width: 100%; height: 6px; background: rgb(212, 175, 55); left: 0; }
    .lantern::before { top: -6px; border-radius: 50%; } .lantern::after { bottom: -6px; border-radius: 50%; }
    .lantern-left { left: 20px; top: 80px; } .lantern-right { right: 20px; top: 80px; }
    @media (max-width: 768px) { .lantern { display: none; } }

    /* ЧАТ ВИДЖЕТ */
    .fc-chat { position: fixed; right: 18px; bottom: 18px; z-index: 20000; font-family: 'PT Serif', serif; }
    .fc-chat-toggle{ width:70px;height:70px;border:none;border-radius:50%; background: radial-gradient(circle at 30% 30%, #ffdf8c 0%, #d4af37 45%, #8b1b1f 100%); color:#5b0f12;font-size:30px;cursor:pointer;position:relative; box-shadow: 0 0 0 4px rgba(255, 221, 140, .45), 0 12px 30px rgba(84,27,30,.5); transition:.2s ease; }
    .fc-chat-toggle.hidden { display: none !important; }
    .fc-chat-panel{ width:360px; max-width:92vw; height:540px; max-height:75vh; background:#fff9ef; border:1px solid #e7d7b5; border-radius:16px; box-shadow:0 20px 45px rgba(84,27,30,.25); overflow:hidden; display:none; margin-bottom:10px; }
    .fc-chat-panel.open{ display:flex; flex-direction:column; }
    .fc-chat-head{ background:linear-gradient(145deg,#541b1e,#7c1418); color:#f6dfac; padding:12px 14px; display:flex; align-items:center; justify-content:space-between; border-bottom:1px solid #3a0f12; }
    .fc-chat-title{font-weight:700; font-size:15px;}
    .fc-chat-close{ border:none;background:transparent;color:#f6dfac;font-size:22px;cursor:pointer;line-height:1; }
    .fc-chat-messages{ flex:1; overflow:auto; padding:12px; background: radial-gradient(500px 220px at 0% 0%, rgba(212,175,55,.12), transparent 70%), #fffaf2; }
    .fc-msg{display:flex; margin:8px 0;}
    .fc-msg.user{justify-content:flex-end;}
    .fc-bubble{ max-width:85%; padding:9px 11px; border-radius:12px; line-height:1.4; font-size:14px; border:1px solid #ecdcb9; }
    .fc-msg.bot .fc-bubble{ background:#fff4dd; border-left:3px solid #d4af37; color:#3a2a1d; }
    .fc-msg.user .fc-bubble{ background:#f3e6cc; border-right:3px solid #b9902c; color:#2f241a; }
    .fc-chat-quick{ padding:8px 10px; border-top:1px solid #efdfbb; background:#fff3da; display:grid; grid-template-columns:1fr 1fr; gap:6px; }
    .fc-qbtn{ border:1px solid #e3cca0; background:#fffaf0; color:#5b3a16; border-radius:8px; padding:7px 8px; font-size:12px; cursor:pointer; }
    .fc-chat-input-wrap{ border-top:1px solid #efdfbb; padding:10px; background:#fffaf0; }
    .fc-chat-input-wrap input{ width:100%; border:1px solid #dcc49a; border-radius:8px; padding:8px 10px; font-size:13px; margin-bottom:6px; box-sizing: border-box; }
    .fc-row{ display:flex; gap:6px; }
    #fc-send{ width:44px; border:none; border-radius:8px; background:#541b1e; color:#f3d38a; cursor:pointer; }

    /* ================= ДАШБОРД ================= */
    /* Data Dashboard: light theme to match site */
    #section-dashboard {
      background: transparent;
      padding-top: 100px;
      color: inherit;
    }
    #section-dashboard .container { max-width: 1400px; margin: 0 auto; padding: 0 30px 60px; }

    #section-dashboard .section-title {
      text-align: center;
      font-size: 28px;
      color: var(--primary);
      margin-bottom: 12px;
      font-weight: 700;
      letter-spacing: 2px;
    }
    #section-dashboard .section-subtitle {
      text-align: center;
      font-size: 11px;
      color: rgba(84,27,30,0.6);
      letter-spacing: 5px;
      margin-bottom: 40px;
      font-weight: 500;
    }
    #section-dashboard .section-subtitle::before,
    #section-dashboard .section-subtitle::after { content: '◆'; margin: 0 12px; opacity: 0.5; }

    /* Stat cards: white + red border (with hover animation) */
    #section-dashboard .stats-row {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
      gap: 24px;
      margin-bottom: 28px;
    }
    #section-dashboard .stat-card {
      background: white;
      border: 3px solid var(--primary);
      border-radius: 8px;
      padding: 26px 20px;
      text-align: center;
      box-shadow: 0 4px 12px rgba(84,27,30,0.1);
      transition: all 0.3s ease;
      cursor: pointer;
      position: relative;
      overflow: hidden;
    }
    #section-dashboard .stat-card::before {
      content: '';
      position: absolute;
      top: 0; left: -100%;
      width: 100%; height: 100%;
      background: linear-gradient(90deg, transparent, rgba(212,175,55,0.08), transparent);
      transition: left 0.7s ease;
    }
    #section-dashboard .stat-card:hover::before { left: 100%; }
    #section-dashboard .stat-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 8px 24px rgba(84,27,30,0.2);
    }
    #section-dashboard .stat-card .stat-icon { font-size: 32px; margin-bottom: 12px; }
    #section-dashboard .stat-card .stat-value {
      font-size: 32px;
      font-weight: 900;
      color: var(--primary);
      margin-bottom: 8px;
      font-variant-numeric: tabular-nums;
      letter-spacing: 1px;
    }
    #section-dashboard .stat-card .stat-label {
      font-size: 13px;
      color: var(--primary);
      font-weight: 600;
      letter-spacing: 2px;
    }
    #section-dashboard .stat-card.stat-card--highlight {
      background: linear-gradient(135deg, #fff8e8 0%, #fff3d6 100%);
      border-color: #d4af37;
      box-shadow: 0 4px 16px rgba(212,175,55,0.2);
    }

    /* Year control: white + red border */
    #section-dashboard .year-control {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 16px;
      margin-bottom: 28px;
      padding: 14px 24px;
      background: white;
      border: 2px solid var(--primary);
      border-radius: 8px;
      flex-wrap: wrap;
      box-shadow: 0 4px 12px rgba(84,27,30,0.08);
    }
    #section-dashboard .year-control > label { color: var(--primary); font-size: 12px; letter-spacing: 2px; font-weight: 600; }
    #section-dashboard .year-control select {
      background: white; color: var(--primary);
      border: 2px solid var(--primary);
      border-radius: 6px;
      padding: 8px 14px;
      font-size: 14px;
      font-weight: 700;
      cursor: pointer;
      outline: none;
    }
    #section-dashboard .year-control select:hover { border-color: #d4af37; color: #8b2323; }
    #section-dashboard .g-btn-secondary {
      background: white;
      color: var(--primary);
      border: 2px solid var(--primary);
      border-radius: 6px;
      padding: 8px 14px;
      font-size: 12px;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.2s;
      letter-spacing: 1px;
    }
    #section-dashboard .g-btn-secondary:hover { background: var(--primary); color: white; border-color: var(--primary); }
    #section-dashboard .year-divider { color: rgba(84,27,30,0.3); font-size: 14px; user-select: none; }

    /* Insight card: light + gold border */
    #section-dashboard .insight-card {
      background: linear-gradient(135deg, #fff8e8 0%, #fffaf0 100%);
      border: 2px solid #d4af37;
      border-radius: 8px;
      padding: 18px 24px;
      margin-bottom: 28px;
      display: flex;
      align-items: center;
      gap: 14px;
      box-shadow: 0 4px 12px rgba(212,175,55,0.1);
    }
    #section-dashboard .insight-icon { font-size: 22px; }
    #section-dashboard .insight-text { color: #2a1a1a; font-size: 14px; line-height: 1.6; }
    #section-dashboard .insight-text strong { color: var(--primary); font-weight: 700; }

    /* Chart wrappers: white + red border */
    #section-dashboard .charts-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 24px;
      margin-bottom: 28px;
    }
    #section-dashboard .chart-wrapper {
      background: white;
      border: 2px solid var(--primary);
      border-radius: 8px;
      padding: 22px;
      box-shadow: 0 4px 12px rgba(84,27,30,0.08);
      transition: all 0.3s ease;
    }
    #section-dashboard .chart-wrapper:hover {
      border-color: #d4af37;
      box-shadow: 0 6px 20px rgba(212,175,55,0.2);
    }
    #section-dashboard .chart-title {
      font-size: 15px;
      font-weight: 700;
      margin-bottom: 18px;
      color: var(--primary);
      letter-spacing: 2px;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    #section-dashboard .chart-title::before {
      content: '';
      width: 3px;
      height: 16px;
      background: linear-gradient(180deg, #d4af37, #8b2323);
      border-radius: 2px;
    }
    #section-dashboard .chart { width: 100%; height: 360px; }

    /* TOP 5 card */
    #section-dashboard .top5-card {
      background: white;
      border: 2px solid var(--primary);
      border-radius: 8px;
      padding: 22px;
      box-shadow: 0 4px 12px rgba(84,27,30,0.08);
    }
    #section-dashboard .top5-title {
      font-size: 15px;
      font-weight: 700;
      margin-bottom: 18px;
      color: var(--primary);
      letter-spacing: 2px;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    #section-dashboard .top5-title::before {
      content: '';
      width: 3px;
      height: 16px;
      background: linear-gradient(180deg, #d4af37, #8b2323);
      border-radius: 2px;
    }
    #section-dashboard .top5-row {
      display: flex;
      align-items: center;
      gap: 16px;
      padding: 11px 0;
      border-bottom: 1px solid rgba(84,27,30,0.08);
    }
    #section-dashboard .top5-row:last-child { border-bottom: none; }
    #section-dashboard .top5-row .rank {
      width: 30px;
      height: 30px;
      border-radius: 50%;
      background: linear-gradient(135deg, #d4af37, #8b2323);
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 900;
      font-size: 14px;
      flex-shrink: 0;
    }
    #section-dashboard .top5-row .rank.rank-1 { background: linear-gradient(135deg, #ffd700, #d4af37); box-shadow: 0 0 8px rgba(212,175,55,0.4); color: #2a1a1a; }
    #section-dashboard .top5-row .rank.rank-2 { background: linear-gradient(135deg, #e8e8e8, #a8a8a8); color: #2a1a1a; }
    #section-dashboard .top5-row .rank.rank-3 { background: linear-gradient(135deg, #cd7f32, #8b4513); color: white; }
    #section-dashboard .top5-row .info { flex: 1; min-width: 0; }
    #section-dashboard .top5-row .name { font-size: 14px; color: #2a1a1a; font-weight: 600; margin-bottom: 5px; }
    #section-dashboard .top5-row .bar-track { height: 6px; background: rgba(84,27,30,0.08); border-radius: 3px; overflow: hidden; }
    #section-dashboard .top5-row .bar-fill {
      height: 100%;
      background: linear-gradient(90deg, #d4af37, #e8c860);
      border-radius: 3px;
      transition: width 0.9s cubic-bezier(.4,0,.2,1);
    }
    #section-dashboard .top5-row .visits {
      font-size: 13px;
      color: var(--primary);
      font-weight: 700;
      min-width: 80px;
      text-align: right;
      font-variant-numeric: tabular-nums;
    }

    @media (max-width: 1024px) {
      #section-dashboard .charts-grid { grid-template-columns: 1fr; }
      #section-dashboard .chart { height: 320px; }
    }


    /* ================= МИНИ-ИГРА ================= */
    #section-game::before { content:''; position: absolute; inset: 0; background-image: repeating-linear-gradient(45deg, transparent, transparent 2px, rgba(84, 27, 30, 0.03) 2px, rgba(84, 27, 30, 0.03) 4px); pointer-events:none; z-index: 1; }
    #section-game .game-app { position: absolute; inset: 0; z-index: 2; display: grid; grid-template-rows: auto 1fr auto; gap: 12px; padding: 80px 14px 14px; }
    #section-game .panel { background: rgba(255,255,255,0.9); border: 3px solid var(--primary); border-radius: 12px; padding: 12px; box-shadow: 0 10px 40px rgba(84, 27, 30, 0.14); }
    #section-game .cols { height: 100%; display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    #section-game .col { display: flex; flex-direction: column; gap: 10px; min-height: 0; }
    #section-game .col__head { padding: 10px 12px; border: 2px solid rgba(84,27,30,0.2); border-radius: 12px; background: #fff; font-weight: 700; display:flex; justify-content: space-between; }
    #section-game .list { flex: 1 1 auto; overflow: auto; display: grid; gap: 10px; min-height:0; padding-bottom: 10px; }
    #section-game .card { display:flex; align-items:center; justify-content: space-between; border: 2px solid rgba(84,27,30,0.16); border-radius: 12px; background: #fff; padding: 10px; cursor: pointer; transition: 0.2s; user-select: none; }
    #section-game .card:hover { transform: translateY(-1px); border-color: rgba(212,175,55,0.9); background: rgba(212,175,55,0.08); }
    #section-game .card.is-selected { border-color: rgba(212,175,55,0.95); box-shadow: 0 0 0 3px rgba(212,175,55,0.18); background: rgba(212,175,55,0.12); }
    #section-game .card.paired { border-color: var(--pair-border); background: var(--pair-bg); }
    #section-game .card.is-correct { outline: 2px solid rgba(31,139,76,0.65); }
    #section-game .card.is-wrong { outline: 2px solid rgba(179,38,30,0.65); }
    #section-game .dot { width: 14px; height: 14px; border-radius: 999px; border: 2px solid rgba(84,27,30,0.28); background: rgba(212,175,55,0.2); flex-shrink: 0; }
    #section-game .card.paired .dot { border-color: var(--pair-border); background: color-mix(in srgb, var(--pair-bg) 65%, #ffffff 35%); }
    #section-game .g-btn { border:none; border-radius: 12px; padding: 10px 12px; font-weight: 800; color:#fff; background: var(--primary); cursor:pointer; }
    #section-game .g-btn.secondary { background: rgba(255,255,255,0.96); color: var(--primary); border: 2px solid rgba(84,27,30,0.22); }
    
    /* ИСПРАВЛЕННАЯ МОДАЛКА ИГРЫ */
    #game-overlay { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; z-index: 9999999; background: rgba(0,0,0,0.7); backdrop-filter: blur(5px); display: none; align-items: center; justify-content: center; }
    #game-overlay.show { display: flex !important; }
    .game-modal { background: #fff; border: 4px solid var(--primary); border-radius: 14px; padding: 20px; width: 95%; max-width: 900px; max-height: 85vh; display: flex; flex-direction: column; }
    .game-modal .resultGrid { display:grid; grid-template-columns: 1fr 1fr; gap:10px; overflow-y:auto; margin-top: 15px; padding-right: 5px; }
    .game-modal .resultItem { border:2px solid rgba(84,27,30,0.16); border-radius:12px; padding:10px; font-size: 12px;}
    .game-modal .resultItem.ok { border-color: rgba(31,139,76,0.45); background: rgba(31,139,76,0.05); }
    .game-modal .resultItem.bad { border-color: rgba(179,38,30,0.45); background: rgba(179,38,30,0.05); }
    @media (max-width: 900px){ #section-game .cols, .game-modal .resultGrid { grid-template-columns: 1fr; } }

    /* ================= 3D КАРТА ================= */
    #section-map::before { content:''; position: absolute; inset: 0; background-image: repeating-linear-gradient(45deg, transparent, transparent 2px, rgba(84, 27, 30, 0.03) 2px, rgba(84, 27, 30, 0.03) 4px); pointer-events:none; z-index: 1; }
    #section-map #scene { position: absolute; inset: 0; width: 100%; height: 100%; z-index: 2; }
    #section-map .side-panel { position: absolute; top: 80px; right: 16px; bottom: 16px; width: min(360px, 92vw); background: rgba(255,255,255,0.92); border: 3px solid var(--primary); border-radius: 12px; z-index: 10; display: flex; flex-direction: column; box-shadow: 0 10px 40px rgba(84, 27, 30, 0.18); backdrop-filter: blur(6px);}
    #section-map .side-panel__header { padding: 10px 12px; border-bottom: 2px solid rgba(212,175,55,0.8); font-weight: bold; color: var(--primary);}
    #section-map .side-panel__list { overflow: auto; padding: 10px; display: flex; flex-direction: column; gap: 8px; }
    #section-map .building-item { border: 2px solid rgba(84, 27, 30, 0.16); background: #fff; padding: 10px; border-radius: 10px; cursor: pointer; display: flex; gap: 10px; text-align: left; font-size: 13px; font-weight:bold; color: var(--primary); transition: 0.12s;}
    #section-map .building-item:hover, #section-map .building-item.is-active { border-color: rgba(212,175,55,0.95); background: rgba(212,175,55,0.10); }
    #section-map .building-item__badge { background: var(--primary); color: #fff; padding: 4px 8px; border-radius: 999px; font-size: 11px; }
    #section-map .top-card { position: absolute; left: 50%; top: 80px; transform: translateX(-50%); background: #fff; border: 3px solid var(--primary); border-radius: 10px; width: min(920px, 92vw); z-index: 15; opacity:0; pointer-events:none; transition: 0.2s; padding: 15px; box-shadow: 0 10px 40px rgba(84, 27, 30, 0.18);}
    #section-map .top-card.show { opacity:1; pointer-events:auto; }
    #section-map #hoverTip { position:fixed; display:none; z-index: 45; background:#fff; border: 2px solid var(--primary); border-radius: 8px; padding: 6px 10px; font-size: 12px; font-weight: 700; color: var(--primary); pointer-events:none;}
    #status { position:fixed; left:12px; bottom:12px; z-index: 60; background:#fff; border: 2px solid var(--primary); border-radius: 8px; padding: 8px 10px; color: var(--primary); font-size: 12px; font-weight:bold;}
    /* ================================================
       TOUR SECTION (Imperial Tour) — Dark Heritage Theme
       ================================================ */
    #section-tour {
      background: #0a0a14;
      color: #f5e9d0;
      padding-top: 0;
      overflow-x: hidden;
    }
    #section-tour.active { display: block; }

    /* Hero */
    .tour-hero {
      position: relative;
      width: 100%;
      height: 100vh;
      min-height: 640px;
      overflow: hidden;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .tour-hero__bg {
      position: absolute;
      inset: 0;
      width: 100%;
      height: 100%;
      object-fit: cover;
      object-position: center;
      filter: brightness(0.55) saturate(1.1);
      z-index: 0;
    }
    .tour-hero__overlay {
      position: absolute;
      inset: 0;
      background: radial-gradient(circle at center, transparent 0%, rgba(10,10,20,0.6) 60%, rgba(10,10,20,0.95) 100%);
      z-index: 1;
    }
    .tour-hero__content {
      position: relative;
      z-index: 2;
      max-width: 920px;
      padding: 0 32px;
      text-align: center;
    }
    .tour-hero__badge {
      display: inline-block;
      padding: 8px 20px;
      border: 1px solid #d4af37;
      border-radius: 999px;
      color: #d4af37;
      font-size: 12px;
      letter-spacing: 3px;
      font-weight: 600;
      margin-bottom: 28px;
      text-transform: uppercase;
    }
    .tour-hero__title {
      font-size: clamp(36px, 6vw, 72px);
      font-weight: 800;
      line-height: 1.1;
      margin: 0 0 24px;
      color: #fff;
      text-shadow: 0 2px 16px rgba(0,0,0,0.5);
    }
    .tour-hero__subtitle {
      font-size: clamp(15px, 1.4vw, 19px);
      line-height: 1.7;
      color: rgba(245,233,208,0.85);
      max-width: 720px;
      margin: 0 auto 40px;
    }
    .tour-hero__cta {
      display: inline-block;
      padding: 16px 36px;
      background: linear-gradient(135deg, #d4af37 0%, #b8941f 100%);
      color: #1a1a2e;
      border-radius: 999px;
      font-size: 15px;
      font-weight: 700;
      letter-spacing: 1.5px;
      text-decoration: none;
      transition: transform 0.3s, box-shadow 0.3s;
      box-shadow: 0 8px 24px rgba(212,175,55,0.35);
    }
    .tour-hero__cta:hover {
      transform: translateY(-3px);
      box-shadow: 0 12px 32px rgba(212,175,55,0.5);
    }
    .tour-hero__scroll-hint {
      position: absolute;
      bottom: 32px;
      left: 50%;
      transform: translateX(-50%);
      color: rgba(245,233,208,0.6);
      font-size: 12px;
      letter-spacing: 2px;
      animation: tour-bounce 2s infinite;
      z-index: 2;
    }
    @keyframes tour-bounce {
      0%,100% { transform: translate(-50%, 0); }
      50% { transform: translate(-50%, 8px); }
    }

    /* Block common */
    .tour-block {
      padding: 100px 24px;
      position: relative;
    }
    .tour-container {
      max-width: 1180px;
      margin: 0 auto;
    }
    .tour-block__label {
      color: #d4af37;
      font-size: 12px;
      letter-spacing: 4px;
      font-weight: 700;
      margin-bottom: 16px;
      text-transform: uppercase;
    }
    .tour-block__title {
      font-size: clamp(28px, 4vw, 44px);
      font-weight: 800;
      line-height: 1.2;
      color: #fff;
      margin: 0 0 32px;
      max-width: 820px;
    }
    .tour-block__title--center {
      text-align: center;
      margin-left: auto;
      margin-right: auto;
    }
    .tour-block__body {
      font-size: 16px;
      line-height: 1.85;
      color: rgba(245,233,208,0.85);
      max-width: 880px;
    }
    .tour-block__body p { margin: 0 0 18px; }
    .tour-block__body em { color: #d4af37; font-style: normal; font-weight: 600; }

    /* Intro */
    .tour-intro {
      background: linear-gradient(180deg, #0a0a14 0%, #11111e 100%);
    }

    /* Axis layout */
    .tour-axis {
      background: #11111e;
    }
    .tour-axis__layout {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 60px;
      align-items: start;
    }
    .tour-axis__map {
      position: relative;
      border-radius: 12px;
      overflow: hidden;
      border: 1px solid rgba(212,175,55,0.3);
      background: #1a1a2e;
      box-shadow: 0 16px 48px rgba(0,0,0,0.5);
    }
    .tour-axis__map img {
      display: block;
      width: 100%;
      height: auto;
      opacity: 0.85;
    }
    .tour-axis__pin {
      position: absolute;
      width: 14px;
      height: 14px;
      background: #d4af37;
      border: 3px solid #1a1a2e;
      border-radius: 50%;
      transform: translate(-50%, -50%);
      box-shadow: 0 0 12px rgba(212,175,55,0.8);
      cursor: pointer;
      transition: transform 0.3s;
    }
    .tour-axis__pin:hover { transform: translate(-50%, -50%) scale(1.4); }
    .tour-axis__svg {
      display: block;
      width: 100%;
      height: auto;
      max-height: 700px;
    }
    .tour-axis__building { cursor: pointer; transition: opacity 0.3s; }
    .tour-axis__building:hover { opacity: 0.85; }
    .tour-axis__building:hover rect,
    .tour-axis__building:hover circle:first-child {
      fill: rgba(212,175,55,0.4) !important;
    }
    .tour-axis__pin-labels {
      margin-top: 20px;
      display: flex;
      flex-direction: column;
      gap: 6px;
    }
    .tour-axis__pin-label {
      padding: 6px 12px;
      background: rgba(26,26,46,0.5);
      border-left: 3px solid rgba(212,175,55,0.4);
      border-radius: 4px;
      font-size: 12px;
      color: rgba(245,233,208,0.8);
      cursor: pointer;
      transition: all 0.3s;
    }
    .tour-axis__pin-label:hover {
      background: rgba(26,26,46,0.9);
      border-left-color: #d4af37;
      transform: translateX(4px);
    }


    .tour-axis__steps {
      display: flex;
      flex-direction: column;
      gap: 18px;
      max-height: 720px;
      overflow-y: auto;
      padding-right: 8px;
    }
    .tour-axis__steps::-webkit-scrollbar { width: 6px; }
    .tour-axis__steps::-webkit-scrollbar-track { background: transparent; }
    .tour-axis__steps::-webkit-scrollbar-thumb { background: rgba(212,175,55,0.3); border-radius: 3px; }
    .tour-axis__step {
      display: flex;
      gap: 16px;
      padding: 18px;
      background: rgba(26,26,46,0.6);
      border-left: 3px solid rgba(212,175,55,0.4);
      border-radius: 6px;
      transition: all 0.3s;
    }
    .tour-axis__step:hover {
      background: rgba(26,26,46,0.9);
      border-left-color: #d4af37;
      transform: translateX(4px);
    }
    .tour-axis__step-num {
      font-size: 28px;
      font-weight: 800;
      color: #d4af37;
      line-height: 1;
      min-width: 48px;
    }
    .tour-axis__step-content h3 {
      margin: 0 0 8px;
      font-size: 16px;
      color: #fff;
      font-weight: 700;
    }
    .tour-axis__step-content p {
      margin: 0;
      font-size: 14px;
      line-height: 1.6;
      color: rgba(245,233,208,0.7);
    }

    /* Recommend */
    .tour-recommend { background: #0a0a14; }
    .tour-recommend__grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 24px;
    }
    .tour-recommend__card {
      position: relative;
      background: #1a1a2e;
      border-radius: 12px;
      overflow: hidden;
      text-decoration: none;
      color: #f5e9d0;
      transition: transform 0.3s, box-shadow 0.3s;
      display: block;
      border: 1px solid rgba(212,175,55,0.2);
    }
    .tour-recommend__card:hover {
      transform: translateY(-6px);
      box-shadow: 0 16px 48px rgba(212,175,55,0.25);
      border-color: rgba(212,175,55,0.6);
    }
    .tour-recommend__card-img {
      width: 100%;
      height: 200px;
      background-size: cover;
      background-position: center;
      filter: brightness(0.65) saturate(0.9);
      transition: filter 0.3s;
    }
    .tour-recommend__card:hover .tour-recommend__card-img { filter: brightness(0.85) saturate(1.1); }
    .tour-recommend__card-num {
      position: absolute;
      top: 16px;
      left: 16px;
      width: 36px;
      height: 36px;
      background: rgba(10,10,20,0.85);
      border: 1px solid #d4af37;
      color: #d4af37;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 800;
      font-size: 14px;
    }
    .tour-recommend__card h3 {
      margin: 16px 20px 8px;
      font-size: 18px;
      font-weight: 700;
      color: #fff;
    }
    .tour-recommend__card p {
      margin: 0 20px 20px;
      font-size: 13px;
      line-height: 1.6;
      color: rgba(245,233,208,0.7);
    }

    /* Stats */
    .tour-stats { background: #11111e; text-align: center; }
    .tour-stats__grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 32px;
      margin-top: 48px;
    }
    .tour-stat {
      padding: 36px 20px;
      background: rgba(26,26,46,0.5);
      border: 1px solid rgba(212,175,55,0.2);
      border-radius: 12px;
      transition: border-color 0.3s, transform 0.3s;
    }
    .tour-stat:hover {
      border-color: rgba(212,175,55,0.6);
      transform: translateY(-4px);
    }
    .tour-stat__num {
      font-size: clamp(36px, 5vw, 56px);
      font-weight: 800;
      color: #d4af37;
      line-height: 1;
      margin-bottom: 12px;
    }
    .tour-stat__label {
      font-size: 13px;
      letter-spacing: 2px;
      color: rgba(245,233,208,0.7);
      text-transform: uppercase;
    }

    /* Network */
    .tour-network { background: #0a0a14; }
    .tour-network__wrap {
      background: #11111e;
      border-radius: 12px;
      padding: 32px;
      border: 1px solid rgba(212,175,55,0.2);
      margin-bottom: 24px;
    }
    .tour-network__svg {
      width: 100%;
      height: auto;
      max-height: 500px;
    }
    .tour-network__caption {
      text-align: center;
      font-size: 14px;
      color: rgba(245,233,208,0.65);
      max-width: 720px;
      margin: 0 auto;
    }

    /* Timeline */
    .tour-timeline { background: #11111e; }
    .tour-timeline__list {
      display: flex;
      flex-direction: column;
      gap: 24px;
      max-width: 820px;
    }
    .tour-timeline__item {
      display: grid;
      grid-template-columns: 120px 1fr;
      gap: 24px;
      padding: 24px 28px;
      background: rgba(26,26,46,0.6);
      border-radius: 10px;
      border-left: 3px solid #d4af37;
      transition: background 0.3s;
    }
    .tour-timeline__item:hover { background: rgba(26,26,46,0.9); }
    .tour-timeline__date {
      font-size: 26px;
      font-weight: 800;
      color: #d4af37;
      letter-spacing: 1px;
    }
    .tour-timeline__body h3 {
      margin: 0 0 8px;
      color: #fff;
      font-size: 18px;
    }
    .tour-timeline__body p {
      margin: 0;
      color: rgba(245,233,208,0.75);
      font-size: 14px;
      line-height: 1.7;
    }

    /* CTA */
    .tour-cta {
      background: linear-gradient(180deg, #11111e 0%, #1a1a2e 100%);
      text-align: center;
      padding: 120px 24px;
    }
    .tour-cta p {
      max-width: 640px;
      margin: 0 auto 32px;
      color: rgba(245,233,208,0.75);
      font-size: 15px;
      line-height: 1.8;
    }

    /* Responsive */
    @media (max-width: 1024px) {
      .tour-axis__layout { grid-template-columns: 1fr; }
      .tour-axis__steps { max-height: none; }
      .tour-recommend__grid { grid-template-columns: repeat(2, 1fr); }
      .tour-stats__grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 640px) {
      .tour-recommend__grid { grid-template-columns: 1fr; }
      .tour-stats__grid { grid-template-columns: 1fr; }
      .tour-timeline__item { grid-template-columns: 1fr; gap: 12px; }
      .tour-block { padding: 64px 16px; }
    }

    /* Museum Map Bloc */
    .fc-map-bloc {
      background: linear-gradient(180deg, #1a0708 0%, #2a1010 100%);
      padding: 80px 24px;
    }
    .fc-map-bloc__inner {
      max-width: 1200px;
      margin: 0 auto;
      text-align: center;
    }
    .fc-map-bloc__title {
      font-family: 'Noto Serif SC', serif;
      color: #d4af37;
      font-size: 36px;
      font-weight: 800;
      letter-spacing: 4px;
      margin: 0 0 12px;
    }
    .fc-map-bloc__sub {
      color: rgba(245,233,208,0.75);
      font-size: 15px;
      margin: 0 auto 36px;
      max-width: 640px;
      line-height: 1.8;
    }
    .fc-map-bloc__frame {
      position: relative;
      max-width: 760px;
      margin: 0 auto;
      background: #faf3e6;
      padding: 12px;
      border: 2px solid #d4af37;
      border-radius: 4px;
      box-shadow: 0 16px 48px rgba(0,0,0,0.5);
      cursor: zoom-in;
      transition: transform 0.3s ease;
    }
    .fc-map-bloc__frame:hover { transform: translateY(-4px); }
    .fc-map-bloc__frame img {
      display: block;
      width: 100%;
      height: auto;
    }
    .fc-map-bloc__caption {
      margin-top: 18px;
      color: rgba(245,233,208,0.6);
      font-size: 13px;
      letter-spacing: 1px;
    }
    .fc-map-bloc__caption strong { color: #d4af37; }

    /* Map lightbox */
    .fc-map-lightbox {
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.92);
      display: none;
      align-items: center;
      justify-content: center;
      z-index: 9999;
      padding: 40px;
      cursor: zoom-out;
    }
    .fc-map-lightbox.active { display: flex; }
    .fc-map-lightbox img {
      max-width: 100%;
      max-height: 100%;
      object-fit: contain;
      box-shadow: 0 0 64px rgba(0,0,0,0.6);
    }
    .fc-map-lightbox__close {
      position: absolute;
      top: 24px;
      right: 32px;
      background: none;
      border: none;
      color: #faf3e6;
      font-size: 36px;
      cursor: pointer;
      line-height: 1;
    }

  </style>
</head>
<body>
      <!-- ГЛОБАЛЬНАЯ НАВИГАЦИЯ -->
  <div class="top-nav-wrapper">
    <button class="nav-tab active" data-target="section-home">
      <span class="lang-en-content">Home</span><span class="lang-zh-content">主页</span>
    </button>
    <button class="nav-tab" data-target="section-map">
      <span class="lang-en-content">3D Map</span><span class="lang-zh-content">3D地图</span>
    </button>
    <button class="nav-tab" data-target="section-dashboard">
      <span class="lang-en-content">Dashboard</span><span class="lang-zh-content">数据面板</span>
    </button>
    <button class="nav-tab" data-target="section-game">
      <span class="lang-en-content">Mini Game</span><span class="lang-zh-content">小游戏</span>
    </button>
    <button class="nav-tab" data-target="section-tour">
      <span class="lang-en-content">Imperial Tour</span><span class="lang-zh-content">中轴线漫游</span>
    </button>
    <button class="nav-tab" data-target="section-visit">
      <span class="lang-en-content">Visit Info</span><span class="lang-zh-content">参观指南</span>
    </button>

    <div class="global-lang-switcher">
      <button class="gl-btn active" data-lang="en">EN</button>
      <button class="gl-btn" data-lang="zh">中文</button>
    </div>
  </div>

  <!-- РАЗДЕЛ 1: ГЛАВНАЯ (INDEX) -->
  <div id="section-home" class="app-section active">
    
    <!-- Фонари главной -->
    <div class="lantern lantern-left"></div>
    <div class="lantern lantern-right"></div>

    <div class="page-container">
      
      <!-- ================= EN ВЕРСИЯ ГЛАВНОЙ ================= -->
      <div class="lang-en-content">
        <!-- Блок 1 (с темно-красным фоном до самого верха) -->
        <div class="bloc full-width-bloc bgc-6057 d-bloc" id="bloc-1">
          <!-- 首页全屏 Hero -->
          <div class="fc-culture-banner" style="position:relative;width:100vw !important;height:100vh !important;max-width:none !important;margin:0 !important;left:50%;right:50%;margin-left:-50vw !important;margin-right:-50vw !important;background:#1a0708;">
            <span class="fc-culture-banner__corner fc-culture-banner__corner--tl"></span>
            <span class="fc-culture-banner__corner fc-culture-banner__corner--tr"></span>
            <span class="fc-culture-banner__corner fc-culture-banner__corner--bl"></span>
            <span class="fc-culture-banner__corner fc-culture-banner__corner--br"></span>
            <div class="fc-culture-banner__bg">
              <img src="img/hero-bg.jpg" alt="Forbidden City panorama">
            </div>
            <div class="fc-culture-banner__text">
              <h1 class="fc-culture-banner__title">
                Forbidden City
                <small>FORBIDDEN · CITY · CULTURE</small>
              </h1>
              <div class="fc-culture-banner__divider"></div>
              <p class="fc-culture-banner__sub">六百年的皇家宫殿，一座活着的文化博物馆。<br>Six centuries of imperial residence, one living cultural museum.</p>
            </div>
            <div class="fc-culture-banner__scroll">SCROLL</div>
          </div>
          <div class="container bloc-no-padding">
            
            <!-- About Forbidden City — Hero 下方独立区块 -->
            <div class="fc-about-block">
              <div class="container">
                <h2 class="fc-about-block__title">About Forbidden City</h2>
                <p class="fc-about-block__lead">
                  The Forbidden City is located in the center of Beijing and is the largest and most famous palace complex in China. It was built in the early 15th century (during the Ming dynasty) and served as the main residence of the emperors of the Ming and Qing dynasties for more than 500 years. Today it is the Palace Museum, which houses collections of imperial artworks, clothing, porcelain, bronze objects, and documents.
                </p>
              </div>
            </div>
          </div>
        </div>

        <div class="bloc none d-bloc fc-timeline-wrapper animated fadeIn" id="bloc-timeline-en">
          <div class="container">
            <h2 class="text-center mb-5 h2-style tc-657">Epochs of the Forbidden City</h2>
            <div class="fc-timeline">
              <div class="fc-timeline-item">
                <div class="fc-timeline-icon">
                  <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M8 28 L32 14 L56 28 L52 28 L52 50 L12 50 L12 28 Z" fill="#7c1418" stroke="#d4af37" stroke-width="1.5"/>
                    <rect x="20" y="38" width="24" height="12" fill="#f6dfac" stroke="#d4af37" stroke-width="1"/>
                    <rect x="29" y="38" width="6" height="12" fill="#541b1e"/>
                    <line x1="8" y1="28" x2="56" y2="28" stroke="#d4af37" stroke-width="1"/>
                    <circle cx="20" cy="22" r="2" fill="#d4af37"/><circle cx="32" cy="18" r="2.5" fill="#d4af37"/><circle cx="44" cy="22" r="2" fill="#d4af37"/>
                  </svg>
                </div>
                <div class="fc-timeline-year">1420</div>
                <div class="fc-timeline-content"><span class="fc-timeline-title">Construction Completed</span><br>Built under the Yongle Emperor of the Ming Dynasty, marking the beginning of its legacy as the imperial seat.</div>
              </div>
              <div class="fc-timeline-item">
                <div class="fc-timeline-icon">
                  <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M32 10 a22 22 0 0 1 0 44 a22 22 0 0 1 0 -44 Z" fill="#541b1e" stroke="#d4af37" stroke-width="1.5"/>
                    <path d="M32 10 a22 22 0 0 0 0 44" fill="#1a3a5c" stroke="#d4af37" stroke-width="1"/>
                    <text x="20" y="38" font-size="11" font-weight="700" fill="#f6dfac" font-family="serif">明</text>
                    <text x="38" y="38" font-size="11" font-weight="700" fill="#f6dfac" font-family="serif">清</text>
                  </svg>
                </div>
                <div class="fc-timeline-year">1644</div>
                <div class="fc-timeline-content"><span class="fc-timeline-title">Ming-Qing Transition</span><br>The Qing army entered Beijing; the Forbidden City continued as the imperial palace for another 268 years.</div>
              </div>
              <div class="fc-timeline-item">
                <div class="fc-timeline-icon">
                  <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M14 24 L20 20 L20 16 L44 16 L44 20 L50 24 L48 26 L48 32 L16 32 L16 26 Z" fill="#d4af37" stroke="#7c1418" stroke-width="1"/>
                    <rect x="22" y="32" width="20" height="6" fill="#d4af37" stroke="#7c1418" stroke-width="1"/>
                    <circle cx="32" cy="40" r="3" fill="#d4af37"/>
                    <path d="M32 43 L32 50 M28 30 L28 24 M36 30 L36 24 M20 30 L20 24 M44 30 L44 24" stroke="#7c1418" stroke-width="1.5"/>
                  </svg>
                </div>
                <div class="fc-timeline-year">1912</div>
                <div class="fc-timeline-content"><span class="fc-timeline-title">End of Empire</span><br>Puyi, the last Emperor of China, abdicated the throne, ending over 2000 years of imperial rule.</div>
              </div>
              <div class="fc-timeline-item">
                <div class="fc-timeline-icon">
                  <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M14 28 L32 18 L50 28 L46 28 L46 46 L18 46 L18 28 Z" fill="#5b3a16" stroke="#d4af37" stroke-width="1.5"/>
                    <rect x="24" y="34" width="16" height="12" fill="#fff9ef" stroke="#d4af37" stroke-width="1"/>
                    <rect x="30" y="34" width="4" height="12" fill="#5b3a16"/>
                    <line x1="14" y1="28" x2="50" y2="28" stroke="#d4af37" stroke-width="1"/>
                    <circle cx="32" cy="14" r="3" fill="#d4af37"/>
                    <text x="29" y="54" font-size="7" font-weight="700" fill="#5b3a16" font-family="serif">Museum</text>
                  </svg>
                </div>
                <div class="fc-timeline-year">1925</div>
                <div class="fc-timeline-content"><span class="fc-timeline-title">Palace Museum</span><br>The Forbidden City officially became the Palace Museum, opening its gates and treasures to the public.</div>
              </div>
              <div class="fc-timeline-item">
                <div class="fc-timeline-icon">
                  <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="32" cy="32" r="22" fill="#1a3a5c" stroke="#d4af37" stroke-width="2"/>
                    <path d="M10 32 Q32 12 54 32 Q32 52 10 32 Z" fill="#2a5a8c" stroke="#d4af37" stroke-width="1"/>
                    <circle cx="32" cy="32" r="3" fill="#d4af37"/>
                    <ellipse cx="20" cy="24" rx="6" ry="3" fill="#7c1418" opacity="0.8"/>
                    <ellipse cx="44" cy="40" rx="6" ry="3" fill="#7c1418" opacity="0.8"/>
                  </svg>
                </div>
                <div class="fc-timeline-year">1987</div>
                <div class="fc-timeline-content"><span class="fc-timeline-title">UNESCO Heritage</span><br>Recognized globally as a World Heritage Site for its unparalleled Chinese palatial architecture.</div>
              </div>
              <div class="fc-timeline-item">
                <div class="fc-timeline-icon">
                  <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="32" cy="32" r="20" fill="none" stroke="#d4af37" stroke-width="2.5"/>
                    <circle cx="32" cy="32" r="13" fill="none" stroke="#d4af37" stroke-width="2"/>
                    <circle cx="32" cy="32" r="6" fill="#7c1418"/>
                    <text x="32" y="36" text-anchor="middle" font-size="9" font-weight="700" fill="#f6dfac" font-family="serif">600</text>
                  </svg>
                </div>
                <div class="fc-timeline-year">2020</div>
                <div class="fc-timeline-content"><span class="fc-timeline-title">600-Year Anniversary</span><br>The Forbidden City celebrated 600 years of history, reaffirming its role as a living cultural museum.</div>
              </div>
            </div>
          </div>
        </div>

        <div class="bloc full-width-bloc bgc-5592 d-bloc" id="bloc-2">
          <div class="container bloc-no-padding-lg"><div class="row"><div class="col"><h1 class="text-center mb-4 h1-gallery-style tc-657">Gallery</h1></div></div></div>
        </div>


        <div class="bloc none bgc-5592 d-bloc" id="bloc-3">
          <div class="container bloc-lg bloc-sm-lg">
            <div class="row voffset-clear-xs">
              <div class="col-12 col-lg-12 offset-lg-0 text-lg-start order-lg-0">
                <div class="row">
                  <div class="col">
                    <div class="fc-gallery__cell" data-gallery-img="img/3570a88e-7849-4655-bb06-92c13844cd2f.JPG" data-title-en="Corner Tower" data-title-zh="角楼" data-desc-en="The most intricate wooden structure atop the palace walls." data-desc-zh="紫禁城城墙上最精巧的木结构建筑。">
                      <img src="img/3570a88e-7849-4655-bb06-92c13844cd2f.JPG" class="img-fluid mx-auto d-block img-rd-md" alt="gallery">
                      <div class="fc-gallery__caption">
                        <h4 class="fc-gallery__title"><span class="lang-en-content">Corner Tower</span><span class="lang-zh-content">角楼</span></h4>
                        <p class="fc-gallery__desc"><span class="lang-en-content">The most intricate wooden structure atop the palace walls.</span><span class="lang-zh-content">紫禁城城墙上最精巧的木结构建筑。</span></p>
                      </div>
                    </div>
                  </div>
                  <div class="col">
                    <div class="fc-gallery__cell" data-gallery-img="img/Screenshot%202026-03-20%20at%2013.38.22.png" data-title-en="Hall of Supreme Harmony" data-title-zh="太和殿" data-desc-en="The highest rank of palace architecture in the Forbidden City." data-desc-zh="紫禁城内等级最高的宫殿建筑。">
                      <img src="img/Screenshot%202026-03-20%20at%2013.38.22.png" class="img-fluid mx-auto d-block img-rd-md" alt="gallery">
                      <div class="fc-gallery__caption">
                        <h4 class="fc-gallery__title"><span class="lang-en-content">Hall of Supreme Harmony</span><span class="lang-zh-content">太和殿</span></h4>
                        <p class="fc-gallery__desc"><span class="lang-en-content">The highest rank of palace architecture.</span><span class="lang-zh-content">紫禁城内等级最高的宫殿。</span></p>
                      </div>
                    </div>
                  </div>
                  <div class="col">
                    <div class="fc-gallery__cell" data-gallery-img="img/Screenshot%202026-03-20%20at%2013.38.02.png" data-title-en="Golden Glazed Tiles" data-title-zh="金瓦琉璃" data-desc-en="Yellow glazed tiles reserved for imperial use." data-desc-zh="黃色琉璃瓦为皇家专属。">
                      <img src="img/Screenshot%202026-03-20%20at%2013.38.02.png" class="img-fluid mx-auto d-block img-rd-md" alt="gallery">
                      <div class="fc-gallery__caption">
                        <h4 class="fc-gallery__title"><span class="lang-en-content">Golden Glazed Tiles</span><span class="lang-zh-content">金瓦琉璃</span></h4>
                        <p class="fc-gallery__desc"><span class="lang-en-content">Yellow glazed tiles reserved for imperial use.</span><span class="lang-zh-content">黃色琉璃瓦为皇家专属。</span></p>
                      </div>
                    </div>
                  </div>
                  <div class="col">
                    <div class="fc-gallery__cell" data-gallery-img="img/2026-03-20%2013.15.36.jpg" data-title-en="Imperial Garden" data-title-zh="御花园" data-desc-en="A tranquil garden at the northern tip of the palace." data-desc-zh="紫禁城最北端的幽静宫苑。">
                      <img src="img/2026-03-20%2013.15.36.jpg" class="img-fluid mx-auto d-block img-rd-md" alt="gallery">
                      <div class="fc-gallery__caption">
                        <h4 class="fc-gallery__title"><span class="lang-en-content">Imperial Garden</span><span class="lang-zh-content">御花园</span></h4>
                        <p class="fc-gallery__desc"><span class="lang-en-content">A tranquil garden at the northern tip of the palace.</span><span class="lang-zh-content">紫禁城最北端的幽静宫苑。</span></p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="row voffset-clear-xs voffset mb-lg-5 pb-lg-3 mt-4">
              <div class="col-6 col-sm-3">
                <div class="fc-gallery__cell" data-gallery-img="img/e1bcd183-0a1f-4a41-9fd6-2b7456ccf96c.JPG" data-title-en="Meridian Gate" data-title-zh="午门" data-desc-en="The grand southern entrance of the palace." data-desc-zh="紫禁城南端的庄严入口。">
                  <img src="img/e1bcd183-0a1f-4a41-9fd6-2b7456ccf96c.JPG" class="img-fluid mx-auto d-block img-rd-md" alt="gallery">
                  <div class="fc-gallery__caption">
                    <h4 class="fc-gallery__title"><span class="lang-en-content">Meridian Gate</span><span class="lang-zh-content">午门</span></h4>
                    <p class="fc-gallery__desc"><span class="lang-en-content">The grand southern entrance.</span><span class="lang-zh-content">紫禁城南端的庄严入口。</span></p>
                  </div>
                </div>
              </div>
              <div class="col-6 col-sm-3">
                <div class="fc-gallery__cell" data-gallery-img="img/d1779a08-ad5f-4726-86e7-b12792f0bf67.JPG" data-title-en="Palace of Heavenly Purity" data-title-zh="乾清宫" data-desc-en="The main residence of Ming & Qing emperors." data-desc-zh="明清两代帝王的正寝宫。">
                  <img src="img/d1779a08-ad5f-4726-86e7-b12792f0bf67.JPG" class="img-fluid mx-auto d-block img-rd-md" alt="gallery">
                  <div class="fc-gallery__caption">
                    <h4 class="fc-gallery__title"><span class="lang-en-content">Palace of Heavenly Purity</span><span class="lang-zh-content">乾清宫</span></h4>
                    <p class="fc-gallery__desc"><span class="lang-en-content">Ming & Qing emperors' residence.</span><span class="lang-zh-content">明清帝王正寝。</span></p>
                  </div>
                </div>
              </div>
              <div class="col-6 col-sm-3">
                <div class="fc-gallery__cell" data-gallery-img="img/d3fe0df3-2e1c-4df2-a19f-af8c56401bdf.JPG" data-title-en="Hall of Mental Cultivation" data-title-zh="养心殿" data-desc-en="The working residence of Qing emperors." data-desc-zh="清代帝王处理政务的寝宫。">
                  <img src="img/d3fe0df3-2e1c-4df2-a19f-af8c56401bdf.JPG" class="img-fluid mx-auto d-block img-rd-md" alt="gallery">
                  <div class="fc-gallery__caption">
                    <h4 class="fc-gallery__title"><span class="lang-en-content">Hall of Mental Cultivation</span><span class="lang-zh-content">养心殿</span></h4>
                    <p class="fc-gallery__desc"><span class="lang-en-content">The working residence of Qing emperors.</span><span class="lang-zh-content">清代帝王处理政务。</span></p>
                  </div>
                </div>
              </div>
              <div class="col-6 col-sm-3">
                <div class="fc-gallery__cell" data-gallery-img="img/40878c46-2ea6-42e7-ba98-c75859dfb885.JPG" data-title-en="Gate of Divine Prowess" data-title-zh="神武门" data-desc-en="The northern exit facing Jingshan Hill." data-desc-zh="紫禁城北门，与景山相望。">
                  <img src="img/40878c46-2ea6-42e7-ba98-c75859dfb885.JPG" class="img-fluid mx-auto d-block img-rd-md" alt="gallery">
                  <div class="fc-gallery__caption">
                    <h4 class="fc-gallery__title"><span class="lang-en-content">Gate of Divine Prowess</span><span class="lang-zh-content">神武门</span></h4>
                    <p class="fc-gallery__desc"><span class="lang-en-content">The northern exit facing Jingshan.</span><span class="lang-zh-content">紫禁城北门。</span></p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>


        <div class="bloc bgc-6057 d-bloc none" id="bloc-4">
          <div class="container bloc-no-padding-lg">
            <div class="row mb-lg-5 pb-lg-4 mt-5">
              <div class="col-lg-5 col-sm-8 offset-sm-2 offset-md-0 col-md-5 order-md-0 order-1 offset-lg-0">
                <picture><img src="img/Weixin%20Image_20260323130514_74_57.jpg" class="img-fluid mx-auto d-block img-10-style img-rd-md animated pulse" alt="Map"></picture>
              </div>
              <div class="col-lg-6 align-self-center col-md-7 offset-lg-1">
                <h1 class="text-center mb-4 text-md-start h1-style mx-auto d-block text-lg-center tc-657 mb-lg-3 animated fadeIn">Heart of Imperial Beijing</h1>
                <h3 class="float-none text-center mb-4 text-md-start h3-style tc-6194 text-w-90 animated fadeIn">
                  The Forbidden City covers an area of 72 hectares, with more than 980 surviving buildings arranged symmetrically along a north-south axis. Surrounded by a moat and 10-meter-high walls, it served as both a residence and a political and ceremonial center. The complex is divided into the Outer Court for state ceremonies and the Inner Court for the imperial family's private life. In 1987, it became a UNESCO World Heritage Site. Today, the Palace Museum receives millions of visitors annually and houses over 1.8 million artifacts. Restoration efforts continue to preserve its historical and artistic legacy.
                </h3>
              </div>
            </div>
          </div>
        </div>


        <div class="bloc bgc-5592 d-bloc none" id="bloc-route-en">
          <div class="container fc-route-container">
            <h1 class="text-center mb-4 h1-gallery-style tc-657 animated fadeIn">Recommended Route</h1>
            <div class="fc-route-row animated fadeInUp">
              <div class="fc-route-step"><div class="fc-route-icon">1</div><div class="fc-route-title">Meridian Gate</div><div class="fc-route-desc">The grand southern entrance where your imperial journey begins.</div></div>
              <div class="fc-route-step"><div class="fc-route-icon">2</div><div class="fc-route-title">Hall of Supreme Harmony</div><div class="fc-route-desc">The heart of the Outer Court, used for state ceremonies.</div></div>
              <div class="fc-route-step"><div class="fc-route-icon">3</div><div class="fc-route-title">Palace of Heavenly Purity</div><div class="fc-route-desc">The gateway to the Inner Court and the Emperor's residence.</div></div>
              <div class="fc-route-step"><div class="fc-route-icon">4</div><div class="fc-route-title">Imperial Garden</div><div class="fc-route-desc">A serene classical Chinese garden leading to the north exit.</div></div>
            </div>
          </div>
        </div>

        <div class="bloc bgc-6057 d-bloc none fc-artifact-container" id="bloc-artifact-en">
          <div class="container">
            <h1 class="text-center mb-5 h1-gallery-style tc-657 animated fadeIn">Artifacts of the Week</h1>
            <div id="artifactCarouselEN" class="carousel slide pb-5" data-bs-ride="carousel">
              <div class="carousel-inner">
                <div class="carousel-item active">
                  <div class="fc-artifact-card">
                    <div class="fc-artifact-img-wrap"><img src="img/1.jpg" alt="The Jade Cabbage"></div>
                    <div class="fc-artifact-info"><span class="fc-artifact-dynasty">Qing Dynasty, 19th Century</span><h3 class="fc-artifact-title">The Jadeite Cabbage</h3><p class="fc-artifact-desc">Carved from a single piece of jadeite, this masterpiece utilizes the natural colors of the stone to represent a cabbage with a locust and katydid, symbolizing purity and fertility.</p></div>
                  </div>
                </div>
                <div class="carousel-item">
                  <div class="fc-artifact-card">
                    <div class="fc-artifact-img-wrap"><img src="img/2.png" alt="Gold Chalice"></div>
                    <div class="fc-artifact-info"><span class="fc-artifact-dynasty">Qing Dynasty, 1739</span><h3 class="fc-artifact-title">Gold Chalice of Eternal Stability</h3><p class="fc-artifact-desc">Exquisitely inlaid with pearls, rubies, and sapphires, this chalice was used by the Qianlong Emperor during the ceremonial writing of the first character of the New Year.</p></div>
                  </div>
                </div>
                <div class="carousel-item">
                  <div class="fc-artifact-card">
                    <div class="fc-artifact-img-wrap"><img src="img/3.jpg" alt="Flask"></div>
                    <div class="fc-artifact-info"><span class="fc-artifact-dynasty">Ming Dynasty, 15th Century</span><h3 class="fc-artifact-title">Blue and White Porcelain Flask</h3><p class="fc-artifact-desc">A magnificent piece from the Yongle reign, featuring vibrant cobalt blue glaze and majestic dragon motifs, representing the peak of Ming dynasty porcelain artistry.</p></div>
                  </div>
                </div>
              </div>
              <button class="carousel-control-prev" type="button" data-bs-target="#artifactCarouselEN" data-bs-slide="prev"><span class="fc-slider-arrow">❮</span></button>
              <button class="carousel-control-next" type="button" data-bs-target="#artifactCarouselEN" data-bs-slide="next"><span class="fc-slider-arrow">❯</span></button>
            </div>
          </div>
        </div>
      </div>
</div>

      <!-- ================= ZH ВЕРСИЯ ГЛАВНОЙ ================= -->
      <div class="lang-zh-content">
        <!-- Блок 1 (с темно-красным фоном) -->
        <div class="bloc full-width-bloc bgc-6057 d-bloc" id="bloc-1-zh">
          <!-- 首页全屏 Hero（中文） -->
          <div class="fc-culture-banner" style="position:relative;width:100vw !important;height:100vh !important;max-width:none !important;margin:0 !important;left:50%;right:50%;margin-left:-50vw !important;margin-right:-50vw !important;background:#1a0708;">
            <span class="fc-culture-banner__corner fc-culture-banner__corner--tl"></span>
            <span class="fc-culture-banner__corner fc-culture-banner__corner--tr"></span>
            <span class="fc-culture-banner__corner fc-culture-banner__corner--bl"></span>
            <span class="fc-culture-banner__corner fc-culture-banner__corner--br"></span>
            <div class="fc-culture-banner__bg">
              <img src="img/hero-bg.jpg" alt="故宫金水桥全景">
            </div>
            <div class="fc-culture-banner__text">
              <h1 class="fc-culture-banner__title">
                紫 禁 城
                <small>FORBIDDEN · CITY · CULTURE</small>
              </h1>
              <div class="fc-culture-banner__divider"></div>
              <p class="fc-culture-banner__sub">六百年的皇家宫殿，一座活着的文化博物馆。<br>Six centuries of imperial residence, one living cultural museum.</p>
            </div>
            <div class="fc-culture-banner__scroll">SCROLL</div>
          </div>
          <div class="container bloc-no-padding">

            <!-- 关于紫禁城 — Hero 下方独立区块 -->
            <div class="fc-about-block">
              <div class="container">
                <h2 class="fc-about-block__title">关于紫禁城</h2>
                <p class="fc-about-block__lead">
                  紫禁城位于北京市中心，是中国最大和最著名的宫殿建筑群。它建于15世纪初期（明朝时代），曾是明、清两代皇帝的主要住所，历时五百多年。如今已成为故宫博物院，收藏着大量帝王艺术作品、服饰、瓷器、青铜器历史文献。
                </p>
              </div>
            </div>
          </div>
        </div>

        <div class="bloc none d-bloc fc-timeline-wrapper animated fadeIn" id="bloc-timeline-zh">
          <div class="container">
            <h2 class="text-center mb-5 h2-style tc-657">紫禁城历史纪年</h2>
            <div class="fc-timeline">
              <div class="fc-timeline-item">
                <div class="fc-timeline-icon">
                  <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M8 28 L32 14 L56 28 L52 28 L52 50 L12 50 L12 28 Z" fill="#7c1418" stroke="#d4af37" stroke-width="1.5"/>
                    <rect x="20" y="38" width="24" height="12" fill="#f6dfac" stroke="#d4af37" stroke-width="1"/>
                    <rect x="29" y="38" width="6" height="12" fill="#541b1e"/>
                    <line x1="8" y1="28" x2="56" y2="28" stroke="#d4af37" stroke-width="1"/>
                    <circle cx="20" cy="22" r="2" fill="#d4af37"/><circle cx="32" cy="18" r="2.5" fill="#d4af37"/><circle cx="44" cy="22" r="2" fill="#d4af37"/>
                  </svg>
                </div>
                <div class="fc-timeline-year">1420</div>
                <div class="fc-timeline-content"><span class="fc-timeline-title">紫禁城落成</span><br>由明成祖朱棣下令建造完工，正式成为明清两代皇帝的政治中心和居住之所。</div>
              </div>
              <div class="fc-timeline-item">
                <div class="fc-timeline-icon">
                  <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M32 10 a22 22 0 0 1 0 44 a22 22 0 0 1 0 -44 Z" fill="#541b1e" stroke="#d4af37" stroke-width="1.5"/>
                    <path d="M32 10 a22 22 0 0 0 0 44" fill="#1a3a5c" stroke="#d4af37" stroke-width="1"/>
                    <text x="20" y="38" font-size="11" font-weight="700" fill="#f6dfac" font-family="serif">明</text>
                    <text x="38" y="38" font-size="11" font-weight="700" fill="#f6dfac" font-family="serif">清</text>
                  </svg>
                </div>
                <div class="fc-timeline-year">1644</div>
                <div class="fc-timeline-content"><span class="fc-timeline-title">明清交替</span><br>清军入关，紫禁城继续作为皇家宫殿，承载了又268年的帝国记忆。</div>
              </div>
              <div class="fc-timeline-item">
                <div class="fc-timeline-icon">
                  <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M14 24 L20 20 L20 16 L44 16 L44 20 L50 24 L48 26 L48 32 L16 32 L16 26 Z" fill="#d4af37" stroke="#7c1418" stroke-width="1"/>
                    <rect x="22" y="32" width="20" height="6" fill="#d4af37" stroke="#7c1418" stroke-width="1"/>
                    <circle cx="32" cy="40" r="3" fill="#d4af37"/>
                    <path d="M32 43 L32 50 M28 30 L28 24 M36 30 L36 24 M20 30 L20 24 M44 30 L44 24" stroke="#7c1418" stroke-width="1.5"/>
                  </svg>
                </div>
                <div class="fc-timeline-year">1912</div>
                <div class="fc-timeline-content"><span class="fc-timeline-title">帝制终结</span><br>中国最后一位皇帝溥仪宣布退位，两千多年的封建帝制正式结束。</div>
              </div>
              <div class="fc-timeline-item">
                <div class="fc-timeline-icon">
                  <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M14 28 L32 18 L50 28 L46 28 L46 46 L18 46 L18 28 Z" fill="#5b3a16" stroke="#d4af37" stroke-width="1.5"/>
                    <rect x="24" y="34" width="16" height="12" fill="#fff9ef" stroke="#d4af37" stroke-width="1"/>
                    <rect x="30" y="34" width="4" height="12" fill="#5b3a16"/>
                    <line x1="14" y1="28" x2="50" y2="28" stroke="#d4af37" stroke-width="1"/>
                    <circle cx="32" cy="14" r="3" fill="#d4af37"/>
                    <text x="29" y="54" font-size="7" font-weight="700" fill="#5b3a16" font-family="serif">Museum</text>
                  </svg>
                </div>
                <div class="fc-timeline-year">1925</div>
                <div class="fc-timeline-content"><span class="fc-timeline-title">故宫博物院成立</span><br>紫禁城正式对外开放，更名为故宫博物院，皇家珍宝首次向公众展示。</div>
              </div>
              <div class="fc-timeline-item">
                <div class="fc-timeline-icon">
                  <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="32" cy="32" r="22" fill="#1a3a5c" stroke="#d4af37" stroke-width="2"/>
                    <path d="M10 32 Q32 12 54 32 Q32 52 10 32 Z" fill="#2a5a8c" stroke="#d4af37" stroke-width="1"/>
                    <circle cx="32" cy="32" r="3" fill="#d4af37"/>
                    <ellipse cx="20" cy="24" rx="6" ry="3" fill="#7c1418" opacity="0.8"/>
                    <ellipse cx="44" cy="40" rx="6" ry="3" fill="#7c1418" opacity="0.8"/>
                  </svg>
                </div>
                <div class="fc-timeline-year">1987</div>
                <div class="fc-timeline-content"><span class="fc-timeline-title">入选世界遗产</span><br>因其无与伦比的中国宫殿建筑艺术历史价值，被联合国教科文组织列入世界遗产名录。</div>
              </div>
              <div class="fc-timeline-item">
                <div class="fc-timeline-icon">
                  <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="32" cy="32" r="20" fill="none" stroke="#d4af37" stroke-width="2.5"/>
                    <circle cx="32" cy="32" r="13" fill="none" stroke="#d4af37" stroke-width="2"/>
                    <circle cx="32" cy="32" r="6" fill="#7c1418"/>
                    <text x="32" y="36" text-anchor="middle" font-size="9" font-weight="700" fill="#f6dfac" font-family="serif">600</text>
                  </svg>
                </div>
                <div class="fc-timeline-year">2020</div>
                <div class="fc-timeline-content"><span class="fc-timeline-title">建成600年</span><br>紫禁城迎来600周年纪念，再一次向世界展现其作为活博物馆的深远意义。</div>
              </div>
            </div>
          </div>
        </div>

        <div class="bloc full-width-bloc bgc-5592 d-bloc" id="bloc-2-zh">
          <div class="container bloc-no-padding-lg"><div class="row"><div class="col"><h1 class="text-center mb-4 h1-gallery-style tc-657">图库</h1></div></div></div>
        </div>


        <div class="bloc none bgc-5592 d-bloc" id="bloc-3-zh">
          <div class="container bloc-lg bloc-sm-lg">
            <div class="row voffset-clear-xs">
              <div class="col-12 col-lg-12 offset-lg-0 text-lg-start order-lg-0">
                <div class="row">
                  <div class="col">
                    <div class="fc-gallery__cell" data-gallery-img="img/3570a88e-7849-4655-bb06-92c13844cd2f.JPG" data-title-en="Corner Tower" data-title-zh="角楼" data-desc-en="The most intricate wooden structure atop the palace walls." data-desc-zh="紫禁城城墙上最精巧的木结构建筑。">
                      <img src="img/3570a88e-7849-4655-bb06-92c13844cd2f.JPG" class="img-fluid mx-auto d-block img-rd-md" alt="gallery">
                      <div class="fc-gallery__caption">
                        <h4 class="fc-gallery__title"><span class="lang-en-content">Corner Tower</span><span class="lang-zh-content">角楼</span></h4>
                        <p class="fc-gallery__desc"><span class="lang-en-content">The most intricate wooden structure atop the palace walls.</span><span class="lang-zh-content">紫禁城城墙上最精巧的木结构建筑。</span></p>
                      </div>
                    </div>
                  </div>
                  <div class="col">
                    <div class="fc-gallery__cell" data-gallery-img="img/Screenshot%202026-03-20%20at%2013.38.22.png" data-title-en="Hall of Supreme Harmony" data-title-zh="太和殿" data-desc-en="The highest rank of palace architecture in the Forbidden City." data-desc-zh="紫禁城内等级最高的宫殿建筑。">
                      <img src="img/Screenshot%202026-03-20%20at%2013.38.22.png" class="img-fluid mx-auto d-block img-rd-md" alt="gallery">
                      <div class="fc-gallery__caption">
                        <h4 class="fc-gallery__title"><span class="lang-en-content">Hall of Supreme Harmony</span><span class="lang-zh-content">太和殿</span></h4>
                        <p class="fc-gallery__desc"><span class="lang-en-content">The highest rank of palace architecture.</span><span class="lang-zh-content">紫禁城内等级最高的宫殿。</span></p>
                      </div>
                    </div>
                  </div>
                  <div class="col">
                    <div class="fc-gallery__cell" data-gallery-img="img/Screenshot%202026-03-20%20at%2013.38.02.png" data-title-en="Golden Glazed Tiles" data-title-zh="金瓦琉璃" data-desc-en="Yellow glazed tiles reserved for imperial use." data-desc-zh="黃色琉璃瓦为皇家专属。">
                      <img src="img/Screenshot%202026-03-20%20at%2013.38.02.png" class="img-fluid mx-auto d-block img-rd-md" alt="gallery">
                      <div class="fc-gallery__caption">
                        <h4 class="fc-gallery__title"><span class="lang-en-content">Golden Glazed Tiles</span><span class="lang-zh-content">金瓦琉璃</span></h4>
                        <p class="fc-gallery__desc"><span class="lang-en-content">Yellow glazed tiles reserved for imperial use.</span><span class="lang-zh-content">黃色琉璃瓦为皇家专属。</span></p>
                      </div>
                    </div>
                  </div>
                  <div class="col">
                    <div class="fc-gallery__cell" data-gallery-img="img/2026-03-20%2013.15.36.jpg" data-title-en="Imperial Garden" data-title-zh="御花园" data-desc-en="A tranquil garden at the northern tip of the palace." data-desc-zh="紫禁城最北端的幽静宫苑。">
                      <img src="img/2026-03-20%2013.15.36.jpg" class="img-fluid mx-auto d-block img-rd-md" alt="gallery">
                      <div class="fc-gallery__caption">
                        <h4 class="fc-gallery__title"><span class="lang-en-content">Imperial Garden</span><span class="lang-zh-content">御花园</span></h4>
                        <p class="fc-gallery__desc"><span class="lang-en-content">A tranquil garden at the northern tip of the palace.</span><span class="lang-zh-content">紫禁城最北端的幽静宫苑。</span></p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="row voffset-clear-xs voffset mb-lg-5 pb-lg-3 mt-4">
              <div class="col-6 col-sm-3">
                <div class="fc-gallery__cell" data-gallery-img="img/e1bcd183-0a1f-4a41-9fd6-2b7456ccf96c.JPG" data-title-en="Meridian Gate" data-title-zh="午门" data-desc-en="The grand southern entrance of the palace." data-desc-zh="紫禁城南端的庄严入口。">
                  <img src="img/e1bcd183-0a1f-4a41-9fd6-2b7456ccf96c.JPG" class="img-fluid mx-auto d-block img-rd-md" alt="gallery">
                  <div class="fc-gallery__caption">
                    <h4 class="fc-gallery__title"><span class="lang-en-content">Meridian Gate</span><span class="lang-zh-content">午门</span></h4>
                    <p class="fc-gallery__desc"><span class="lang-en-content">The grand southern entrance.</span><span class="lang-zh-content">紫禁城南端的庄严入口。</span></p>
                  </div>
                </div>
              </div>
              <div class="col-6 col-sm-3">
                <div class="fc-gallery__cell" data-gallery-img="img/d1779a08-ad5f-4726-86e7-b12792f0bf67.JPG" data-title-en="Palace of Heavenly Purity" data-title-zh="乾清宫" data-desc-en="The main residence of Ming & Qing emperors." data-desc-zh="明清两代帝王的正寝宫。">
                  <img src="img/d1779a08-ad5f-4726-86e7-b12792f0bf67.JPG" class="img-fluid mx-auto d-block img-rd-md" alt="gallery">
                  <div class="fc-gallery__caption">
                    <h4 class="fc-gallery__title"><span class="lang-en-content">Palace of Heavenly Purity</span><span class="lang-zh-content">乾清宫</span></h4>
                    <p class="fc-gallery__desc"><span class="lang-en-content">Ming & Qing emperors' residence.</span><span class="lang-zh-content">明清帝王正寝。</span></p>
                  </div>
                </div>
              </div>
              <div class="col-6 col-sm-3">
                <div class="fc-gallery__cell" data-gallery-img="img/d3fe0df3-2e1c-4df2-a19f-af8c56401bdf.JPG" data-title-en="Hall of Mental Cultivation" data-title-zh="养心殿" data-desc-en="The working residence of Qing emperors." data-desc-zh="清代帝王处理政务的寝宫。">
                  <img src="img/d3fe0df3-2e1c-4df2-a19f-af8c56401bdf.JPG" class="img-fluid mx-auto d-block img-rd-md" alt="gallery">
                  <div class="fc-gallery__caption">
                    <h4 class="fc-gallery__title"><span class="lang-en-content">Hall of Mental Cultivation</span><span class="lang-zh-content">养心殿</span></h4>
                    <p class="fc-gallery__desc"><span class="lang-en-content">The working residence of Qing emperors.</span><span class="lang-zh-content">清代帝王处理政务。</span></p>
                  </div>
                </div>
              </div>
              <div class="col-6 col-sm-3">
                <div class="fc-gallery__cell" data-gallery-img="img/40878c46-2ea6-42e7-ba98-c75859dfb885.JPG" data-title-en="Gate of Divine Prowess" data-title-zh="神武门" data-desc-en="The northern exit facing Jingshan Hill." data-desc-zh="紫禁城北门，与景山相望。">
                  <img src="img/40878c46-2ea6-42e7-ba98-c75859dfb885.JPG" class="img-fluid mx-auto d-block img-rd-md" alt="gallery">
                  <div class="fc-gallery__caption">
                    <h4 class="fc-gallery__title"><span class="lang-en-content">Gate of Divine Prowess</span><span class="lang-zh-content">神武门</span></h4>
                    <p class="fc-gallery__desc"><span class="lang-en-content">The northern exit facing Jingshan.</span><span class="lang-zh-content">紫禁城北门。</span></p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>


        <div class="bloc bgc-6057 d-bloc none" id="bloc-4-zh">
          <div class="container bloc-no-padding-lg">
            <div class="row mb-lg-5 pb-lg-4 mt-5">
              <div class="col-lg-5 col-sm-8 offset-sm-2 offset-md-0 col-md-5 order-md-0 order-1 offset-lg-0">
                <picture><img src="img/Weixin%20Image_20260323130514_74_57.jpg" class="img-fluid mx-auto d-block img-10-style img-rd-md animated pulse" alt="Map"></picture>
              </div>
              <div class="col-lg-6 align-self-center col-md-7 offset-lg-1">
                <h1 class="text-center mb-4 text-md-start h1-style mx-auto d-block text-lg-center tc-657 mb-lg-3 animated fadeIn">帝都的心脏</h1>
                <h3 class="float-none text-center mb-4 text-md-start h3-style tc-6194 text-w-90 animated fadeIn">
                  紫禁城占地面积72公顷保留下来的建筑有980余座，沿南北中轴线对称排列。被护城河和10米高的城墙环绕，既是皇帝的住所，也是进行国家政治和宗教活的场所。建筑群分为外朝和内廷两部分，分别用于国家大事和皇帝的私人生活。1987年被联合国教科文组织列为世界文化遗产。如今故宫博物院每年吸引百万游客来此参观，馆藏文物超过180万件，修复工作仍在持续进行中。
                </h3>
              </div>
            </div>
          </div>
        </div>


        <div class="bloc bgc-5592 d-bloc none" id="bloc-route-zh">
          <div class="container fc-route-container">
            <h1 class="text-center mb-4 h1-gallery-style tc-657 animated fadeIn">推荐参观路线</h1>
            <div class="fc-route-row animated fadeInUp">
              <div class="fc-route-step"><div class="fc-route-icon">1</div><div class="fc-route-title">午门</div><div class="fc-route-desc">宏伟的南面正门，您皇家之旅的起点。</div></div>
              <div class="fc-route-step"><div class="fc-route-icon">2</div><div class="fc-route-title">太和殿</div><div class="fc-route-desc">外朝的核心，明清皇帝举行国家大典的场所。</div></div>
              <div class="fc-route-step"><div class="fc-route-icon">3</div><div class="fc-route-title">乾清宫</div><div class="fc-route-desc">内廷的正门，曾是皇帝的日常居住和理政之处。</div></div>
              <div class="fc-route-step"><div class="fc-route-icon">4</div><div class="fc-route-title">御花园</div><div class="fc-route-desc">宁静的古典中式园林，通向北门（神武门）出口。</div></div>
            </div>
          </div>
        </div>

        <div class="bloc bgc-6057 d-bloc none fc-artifact-container" id="bloc-artifact-zh">
          <div class="container">
            <h1 class="text-center mb-5 h1-gallery-style tc-657 animated fadeIn">本周文物</h1>
            <div id="artifactCarouselZH" class="carousel slide pb-5" data-bs-ride="carousel">
              <div class="carousel-inner">
                <div class="carousel-item active">
                  <div class="fc-artifact-card">
                    <div class="fc-artifact-img-wrap"><img src="img/1.jpg" alt="翠玉白菜"></div>
                    <div class="fc-artifact-info"><span class="fc-artifact-dynasty">清代，19世纪</span><h3 class="fc-artifact-title">翠玉白菜</h3><p class="fc-artifact-desc">由一块半白半绿的翠玉雕刻而成，巧妙利用玉石天然色彩，菜叶上还雕有螽斯和蝗虫，寓意纯洁与多子多孙。</p></div>
                  </div>
                </div>
                <div class="carousel-item">
                  <div class="fc-artifact-card">
                    <div class="fc-artifact-img-wrap"><img src="img/2.png" alt="金瓯永固杯"></div>
                    <div class="fc-artifact-info"><span class="fc-artifact-dynasty">清代，1739年</span><h3 class="fc-artifact-title">金瓯永固杯</h3><p class="fc-artifact-desc">杯身镶嵌珍珠、红宝石与蓝宝石，做工精美，是乾隆皇帝在每年元旦举行开笔仪式时专用的酒杯，寓意江山永固。</p></div>
                  </div>
                </div>
                <div class="carousel-item">
                  <div class="fc-artifact-card">
                    <div class="fc-artifact-img-wrap"><img src="img/3.jpg" alt="青花龙纹扁壶"></div>
                    <div class="fc-artifact-info"><span class="fc-artifact-dynasty">明代，15世纪</span><h3 class="fc-artifact-title">青花龙纹扁壶</h3><p class="fc-artifact-desc">永乐时期的青花瓷杰作，采用进口苏麻离青料烧制，呈现出深邃的钴蓝色，壶身绘有生动威武的苍龙图案。</p></div>
                  </div>
                </div>
              </div>
              <button class="carousel-control-prev" type="button" data-bs-target="#artifactCarouselZH" data-bs-slide="prev"><span class="fc-slider-arrow">❮</span></button>
              <button class="carousel-control-next" type="button" data-bs-target="#artifactCarouselZH" data-bs-slide="next"><span class="fc-slider-arrow">❯</span></button>
            </div>
          </div>
        </div>

        <!-- 官方开放区域地图 -->
        <div class="bloc bgc-6057 d-bloc none fc-map-bloc" id="bloc-map-zh">
          <div class="fc-map-bloc__inner">
            <h2 class="fc-map-bloc__title">
              <span class="lang-en-content">Open Areas of the Palace</span>
              <span class="lang-zh-content">故宫博物院开放区域</span>
            </h2>
            <p class="fc-map-bloc__sub">
              <span class="lang-en-content">An official overview of all halls, courtyards, and gates open to visitors — from the Meridian Gate in the south to the Gate of Divine Prowess in the north.</span>
              <span class="lang-zh-content">官方全园导览图——从南午门到北神武门，涵盖中路、东路、西路所有对游客开放的宫殿、庭院与门楼。</span>
            </p>
            <div class="fc-map-bloc__frame" data-map-zoom>
              <img src="img/fc-museum-map.png" alt="Forbidden City Open Areas Map">
            </div>
            <p class="fc-map-bloc__caption">
              <span class="lang-en-content"><strong>Click the map to enlarge.</strong> Official Palace Museum open-area map, last revised 2025.</span>
              <span class="lang-zh-content"><strong>点击图片可放大查看。</strong>故宫博物院官方开放区域导览图，2025 最新版本。</span>
            </p>
          </div>
        </div>

      </div>
    </div>  <!-- /lang-zh-content -->

      <!-- ===== Visit Info 参观指南 ===== -->
      <div id="section-visit" class="app-section">
        <div class="fc-visit">
          <div class="container">
            <div class="fc-visit__header text-center">
              <div class="fc-visit__crest">❋</div>
              <h2 class="fc-visit__title">
                <span class="lang-en-content">Visit the Forbidden City</span>
                <span class="lang-zh-content">参观紫禁城</span>
              </h2>
              <div class="fc-visit__divider"></div>
              <p class="fc-visit__sub">
                <span class="lang-en-content">Six centuries of imperial grandeur, four ceremonial gates. Plan your journey through the heart of Ming &amp; Qing China.</span>
                <span class="lang-zh-content">六百年皇朝气象，四向帝阙门户。踏上通往明清中轴的旅程。</span>
              </p>
            </div>

            <div class="fc-visit__layout">
              <!-- 左：地图 -->
              <div class="fc-visit__map">
                <div class="fc-visit__map-frame">
                  <iframe
                    src="https://uri.amap.com/marker?position=116.397,39.916&name=故宫博物院&src=mapi&coordinate=gaode&callnative=1"
                    width="100%" height="100%" frameborder="0" scrolling="no"
                    style="border:0; border-radius:8px;"
                    title="Forbidden City on AMap">
                  </iframe>
                  <div class="fc-visit__map-pin">📍</div>
                </div>
                <div class="fc-visit__map-note">
                  <span class="lang-en-content">No. 4 Jingshan Front Street, Dongcheng District, Beijing</span>
                  <span class="lang-zh-content">北京市东城区景山前街4号</span>
                </div>
              </div>

              <!-- 右：tab 面板 -->
              <div class="fc-visit__panel">
                <div class="fc-visit__tabs">
                  <button class="fc-visit__tab active" data-gate="wumen">
                    <span class="lang-en-content">Meridian Gate</span>
                    <span class="lang-zh-content">午门</span>
                  </button>
                  <button class="fc-visit__tab" data-gate="shenwu">
                    <span class="lang-en-content">Gate of Divine Prowess</span>
                    <span class="lang-zh-content">神武门</span>
                  </button>
                  <button class="fc-visit__tab" data-gate="donghua">
                    <span class="lang-en-content">East Flowery Gate</span>
                    <span class="lang-zh-content">东华门</span>
                  </button>
                  <button class="fc-visit__tab" data-gate="xihua">
                    <span class="lang-en-content">West Flowery Gate</span>
                    <span class="lang-zh-content">西华门</span>
                  </button>
                </div>

                <div class="fc-visit__panels">
                  <!-- 午门 -->
                  <div class="fc-visit__pane active" data-gate="wumen">
                    <h3 class="fc-visit__gate-title">
                      <span class="lang-en-content">Meridian Gate · Wumen</span>
                      <span class="lang-zh-content">午门</span>
                    </h3>
                    <p class="fc-visit__gate-desc">
                      <span class="lang-en-content">The southern main entrance and the ceremonial heart of the palace. Five phoenix-tail arches, flanked by imposing corner towers, once witnessed imperial edicts and grand receptions.</span>
                      <span class="lang-zh-content">紫禁城南端的正门，皇朝礼仪的核心。五道御路、两侧耸立的阙楼，曾见证圣旨颁诏与凯旋盛典。</span>
                    </p>
                    <ul class="fc-visit__meta">
                      <li><strong><span class="lang-en-content">Hours</span><span class="lang-zh-content">开放时间</span></strong>
                        <span class="lang-en-content">Apr–Oct 08:30–17:00 · Nov–Mar 08:30–16:30</span>
                        <span class="lang-zh-content">4/1–10/31 08:30–17:00 · 11/1–3/31 08:30–16:30</span></li>
                      <li><strong><span class="lang-en-content">Tickets</span><span class="lang-zh-content">售票</span></strong>
                        <span class="lang-en-content">Sold 08:30–16:00 · Closed on Mondays (except holidays)</span>
                        <span class="lang-zh-content">8:30–16:00 售票 · 周一闭馆（法定节假日除外）</span></li>
                      <li><strong><span class="lang-en-content">Metro</span><span class="lang-zh-content">地铁</span></strong>
                        <span class="lang-en-content">Line 1 — Tiananmen East / Line 8 — National Art Museum</span>
                        <span class="lang-zh-content">1号线 天安门东 / 8号线 中国美术馆</span></li>
                    </ul>
                  </div>
                  <!-- 神武门 -->
                  <div class="fc-visit__pane" data-gate="shenwu">
                    <h3 class="fc-visit__gate-title">
                      <span class="lang-en-content">Gate of Divine Prowess · Shenwumen</span>
                      <span class="lang-zh-content">神武门</span>
                    </h3>
                    <p class="fc-visit__gate-desc">
                      <span class="lang-en-content">The northern gate, set against the green silhouette of Jingshan Hill. Reserved as the exit only — visitors depart here after completing the central axis.</span>
                      <span class="lang-zh-content">紫禁城北门，与景山依傍。作为出口专用，游客沿中轴线游览结束后由此离开。</span>
                    </p>
                    <ul class="fc-visit__meta">
                      <li><strong><span class="lang-en-content">Status</span><span class="lang-zh-content">状态</span></strong>
                        <span class="lang-en-content">Exit only · No re-entry permitted</span>
                        <span class="lang-zh-content">仅供出口 · 不可重复进入</span></li>
                      <li><strong><span class="lang-en-content">Hours</span><span class="lang-zh-content">开放时间</span></strong>
                        <span class="lang-en-content">Same as Meridian Gate</span>
                        <span class="lang-zh-content">与午门同步</span></li>
                      <li><strong><span class="lang-en-content">Metro</span><span class="lang-zh-content">地铁</span></strong>
                        <span class="lang-en-content">Line 6 — Beihai North / Line 8 — Shichahai</span>
                        <span class="lang-zh-content">6号线 北海北 / 8号线 什刹海</span></li>
                    </ul>
                  </div>
                  <!-- 东华门 -->
                  <div class="fc-visit__pane" data-gate="donghua">
                    <h3 class="fc-visit__gate-title">
                      <span class="lang-en-content">East Flowery Gate · Donghuamen</span>
                      <span class="lang-zh-content">东华门</span>
                    </h3>
                    <p class="fc-visit__gate-desc">
                      <span class="lang-en-content">Eastern side entrance — the gateway to the Treasure Gallery and the Clock Exhibition Hall. Originally reserved for court officials to enter daily.</span>
                      <span class="lang-zh-content">东侧门，是珍宝馆与钟表馆的专属入口。原为臣工每日入朝之门。</span>
                    </p>
                    <ul class="fc-visit__meta">
                      <li><strong><span class="lang-en-content">Use</span><span class="lang-zh-content">功能</span></strong>
                        <span class="lang-en-content">Treasure Gallery + Clock Exhibition Hall</span>
                        <span class="lang-zh-content">珍宝馆 + 钟表馆专用入口</span></li>
                      <li><strong><span class="lang-en-content">Exhibition Fee</span><span class="lang-zh-content">展览票</span></strong>
                        <span class="lang-en-content">CNY 10 for each of Treasure Gallery / Clock Hall</span>
                        <span class="lang-zh-content">珍宝馆 / 钟表馆 各 10 元</span></li>
                      <li><strong><span class="lang-en-content">Metro</span><span class="lang-zh-content">地铁</span></strong>
                        <span class="lang-en-content">Line 1 — Tiananmen East</span>
                        <span class="lang-zh-content">1号线 天安门东</span></li>
                    </ul>
                  </div>
                  <!-- 西华门 -->
                  <div class="fc-visit__pane" data-gate="xihua">
                    <h3 class="fc-visit__gate-title">
                      <span class="lang-en-content">West Flowery Gate · Xihuamen</span>
                      <span class="lang-zh-content">西华门</span>
                    </h3>
                    <p class="fc-visit__gate-desc">
                      <span class="lang-en-content">Western counterpart to Donghua. Once used by the imperial family to attend banquets at the Mountain of Accumulated Virtue. Today it stands quietly, opened by appointment for special tours.</span>
                      <span class="lang-zh-content">与东华门东西对称。昔为皇室赴北海御宴之路，今日常年幽静，偶有特别导览开放。</span>
                    </p>
                    <ul class="fc-visit__meta">
                      <li><strong><span class="lang-en-content">Status</span><span class="lang-zh-content">状态</span></strong>
                        <span class="lang-en-content">Closed to daily visitors · Special access only</span>
                        <span class="lang-zh-content">平日闭门 · 仅限特批导览</span></li>
                      <li><strong><span class="lang-en-content">Use</span><span class="lang-zh-content">功能</span></strong>
                        <span class="lang-en-content">Ceremonial passage · Photo permission only</span>
                        <span class="lang-zh-content">礼仪通道 · 需提前报备</span></li>
                      <li><strong><span class="lang-en-content">Metro</span><span class="lang-zh-content">地铁</span></strong>
                        <span class="lang-en-content">Line 1 — Tiananmen West</span>
                        <span class="lang-zh-content">1号线 天安门西</span></li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>

            <!-- 底部参观须知 -->
            <div class="fc-visit__notice">
              <div class="fc-visit__notice-title">
                <span class="lang-en-content">Visitor Notice</span>
                <span class="lang-zh-content">参观须知</span>
              </div>
              <div class="fc-visit__notice-body">
                <span class="lang-en-content">All visits require advance reservation via the official WeChat mini-program or dpm.org.cn. Security screening is mandatory. Large luggage, lighters, selfie sticks and professional tripods are not permitted inside the palace walls.</span>
                <span class="lang-zh-content">所有观众须通过官方微信公众号或 dpm.org.cn 实名预约。入园需接受安检，大件行李、打火机、自拍杆、专业三脚架等禁止带入宫墙之内。</span>
              </div>
            </div>
          </div>
        </div>
      </div>


      <!-- ===== Lightbox 全屏查看器 ===== -->
      <div class="fc-lightbox" id="fc-lightbox" role="dialog" aria-hidden="true">
        <div class="fc-lightbox__counter" id="fc-lightbox-counter"></div>
        <button class="fc-lightbox__close" id="fc-lightbox-close" aria-label="Close">✕</button>
        <button class="fc-lightbox__prev" id="fc-lightbox-prev" aria-label="Previous">‹</button>
        <button class="fc-lightbox__next" id="fc-lightbox-next" aria-label="Next">›</button>
        <img class="fc-lightbox__img" id="fc-lightbox-img" alt="">
        <div class="fc-lightbox__caption">
          <h3 class="fc-lightbox__title" id="fc-lightbox-title"></h3>
          <p class="fc-lightbox__desc" id="fc-lightbox-desc"></p>
        </div>
      </div>

      <script>
      (function() {
        var lb = document.getElementById('fc-lightbox');
        var lbImg = document.getElementById('fc-lightbox-img');
        var lbTitle = document.getElementById('fc-lightbox-title');
        var lbDesc = document.getElementById('fc-lightbox-desc');
        var lbCounter = document.getElementById('fc-lightbox-counter');
        var cells = document.querySelectorAll('.fc-gallery__cell');
        var listArr = Array.prototype.slice.call(cells);
        var current = -1;
        var currentLangIsZh = document.body.classList.contains('lang-zh');

        function open(i) {
          current = i;
          var c = listArr[i];
          lbImg.src = c.getAttribute('data-gallery-img');
          currentLangIsZh = document.body.classList.contains('lang-zh');
          lbTitle.textContent = currentLangIsZh ? c.getAttribute('data-title-zh') : c.getAttribute('data-title-en');
          lbDesc.textContent = currentLangIsZh ? c.getAttribute('data-desc-zh') : c.getAttribute('data-desc-en');
          lbCounter.textContent = (i+1) + ' / ' + listArr.length;
          lb.classList.add('open');
          lb.setAttribute('aria-hidden', 'false');
          document.body.style.overflow = 'hidden';
        }
        function close() {
          lb.classList.remove('open');
          lb.setAttribute('aria-hidden', 'true');
          document.body.style.overflow = '';
        }
        function next() { open((current + 1) % listArr.length); }
        function prev() { open((current - 1 + listArr.length) % listArr.length); }

        listArr.forEach(function(c, i) {
          c.addEventListener('click', function(e) { e.preventDefault(); open(i); });
        });
        document.getElementById('fc-lightbox-close').addEventListener('click', close);
        document.getElementById('fc-lightbox-next').addEventListener('click', next);
        document.getElementById('fc-lightbox-prev').addEventListener('click', prev);
        lb.addEventListener('click', function(e) { if (e.target === lb) close(); });
        document.addEventListener('keydown', function(e) {
          if (!lb.classList.contains('open')) return;
          if (e.key === 'Escape') close();
          if (e.key === 'ArrowRight') next();
          if (e.key === 'ArrowLeft') prev();
        });
        // 语言切换时如果 lightbox 打开，更新文字
        document.addEventListener('click', function(e) {
          if (!lb.classList.contains('open')) return;
          if (e.target.closest('.fc-lang-switcher') || e.target.closest('[data-lang]')) {
            setTimeout(function() {
              var c = listArr[current];
              currentLangIsZh = document.body.classList.contains('lang-zh');
              lbTitle.textContent = currentLangIsZh ? c.getAttribute('data-title-zh') : c.getAttribute('data-title-en');
              lbDesc.textContent = currentLangIsZh ? c.getAttribute('data-desc-zh') : c.getAttribute('data-desc-en');
            }, 50);
          }
        });
      })();
      </script>

      <!-- ТЕМНО-КРАСНЫЙ ПОДВАЛ (ФУТЕР) -->
      <footer class="fc-footer">
        <div class="fc-footer__copy">© 2026 Forbidden City Portal</div>
      </footer>

    
    <!-- ВИДЖЕТ ЧАТА -->
    <div id="fc-chat-widget" class="fc-chat">
      <button id="fc-chat-toggle" class="fc-chat-toggle pulse" aria-label="Open chat">🏮</button>
      <div id="fc-chat-panel" class="fc-chat-panel" aria-hidden="true">
        <div class="fc-chat-head">
          <div class="fc-chat-title" id="fc-title">Forbidden City Assistant</div>
          <button id="fc-chat-close" class="fc-chat-close" aria-label="Close">×</button>
        </div>
        <div id="fc-chat-messages" class="fc-chat-messages"></div>
        <div class="fc-chat-quick" id="fc-quick"></div>
        <div class="fc-chat-input-wrap">
          <div class="fc-row">
            <input id="fc-chat-input" type="text" placeholder="Type your message...">
            <button id="fc-send">➤</button>
          </div>
        </div>
      </div>
    </div>
      <!-- =========================================
       РАЗДЕЛ 2: 3D КАРТА (3DMAP)
       ========================================= -->
  <div id="section-map" class="app-section fullscreen-section">
    <canvas id="scene"></canvas>

      <div id="mapControls" style="position: absolute; top: 16px; left: 16px; z-index: 30; display: flex; gap: 8px;">
    <button id="mapResetBtn" title="Reset View (R)" style="background: rgba(255,255,255,0.95); border: 2px solid var(--primary); color: var(--primary); padding: 8px 14px; border-radius: 6px; cursor: pointer; font-size: 12px; font-weight: 700; letter-spacing: 1px; box-shadow: 0 2px 8px rgba(0,0,0,0.15);">
      <span class="lang-en-content">↻ RESET</span>
      <span class="lang-zh-content">↻ 重置视角</span>
    </button>
    <button id="mapDayNightBtn" title="Day/Night (N)" style="background: rgba(255,255,255,0.95); border: 2px solid var(--primary); color: var(--primary); padding: 8px 14px; border-radius: 6px; cursor: pointer; font-size: 14px; box-shadow: 0 2px 8px rgba(0,0,0,0.15);">☀️</button>
    <div id="mapZoomHint" style="background: rgba(0,0,0,0.65); color: #fff; padding: 8px 14px; border-radius: 6px; font-size: 11px; letter-spacing: 1px; align-self: center;">
      <span class="lang-en-content">Drag · Scroll · R reset · N night</span>
      <span class="lang-zh-content">拖拽 · 滚轮 · R 重置 · N 昼夜</span>
    </div>
  </div>
  <aside id="buildingPanel" class="side-panel" aria-label="Building list">
      <div class="side-panel__header">
        <div style="font-weight: 700; letter-spacing: 1px; margin: 0;">
          <span class="lang-en-content">Buildings</span>
          <span class="lang-zh-content">建筑物</span>
        </div>
        <div id="mapProgress" style="font-size: 11px; color: var(--primary); opacity: 0.7; margin-top: 4px;">
          <span class="lang-en-content">Visited 0 / 40</span>
          <span class="lang-zh-content">已查看 0 / 40</span>
        </div>
        <div style="height: 3px; background: rgba(84,27,30,0.1); border-radius: 2px; margin-top: 4px; overflow: hidden;">
          <div id="mapProgressBar" style="height: 100%; background: linear-gradient(90deg, #d4af37, #8b2323); width: 0%; transition: width 0.4s ease;"></div>
        </div>
      </div>
      <div style="padding: 8px 10px; border-bottom: 1px solid rgba(84,27,30,0.1);">
        <input id="mapSearch" type="text" placeholder="🔍 搜索 / Search..." style="width: 100%; padding: 6px 10px; border: 1.5px solid rgba(84,27,30,0.3); border-radius: 4px; font-size: 12px; outline: none; box-sizing: border-box; background: white; color: var(--primary);" />
        <div id="mapCategories" style="display: flex; flex-wrap: wrap; gap: 4px; margin-top: 6px;">
          <button class="map-cat-btn active" data-cat="all" style="border: 1px solid var(--primary); background: var(--primary); color: white; padding: 3px 8px; border-radius: 12px; font-size: 10px; cursor: pointer; font-weight: 600;">
            <span class="lang-en-content">All</span><span class="lang-zh-content">全部</span>
          </button>
          <button class="map-cat-btn" data-cat="gate" style="border: 1px solid rgba(84,27,30,0.4); background: white; color: var(--primary); padding: 3px 8px; border-radius: 12px; font-size: 10px; cursor: pointer; font-weight: 600;">
            <span class="lang-en-content">Gates</span><span class="lang-zh-content">门楼</span>
          </button>
          <button class="map-cat-btn" data-cat="outer" style="border: 1px solid rgba(84,27,30,0.4); background: white; color: var(--primary); padding: 3px 8px; border-radius: 12px; font-size: 10px; cursor: pointer; font-weight: 600;">
            <span class="lang-en-content">Outer Court</span><span class="lang-zh-content">外朝</span>
          </button>
          <button class="map-cat-btn" data-cat="inner" style="border: 1px solid rgba(84,27,30,0.4); background: white; color: var(--primary); padding: 3px 8px; border-radius: 12px; font-size: 10px; cursor: pointer; font-weight: 600;">
            <span class="lang-en-content">Inner Court</span><span class="lang-zh-content">内廷</span>
          </button>
          <button class="map-cat-btn" data-cat="garden" style="border: 1px solid rgba(84,27,30,0.4); background: white; color: var(--primary); padding: 3px 8px; border-radius: 12px; font-size: 10px; cursor: pointer; font-weight: 600;">
            <span class="lang-en-content">Garden</span><span class="lang-zh-content">花园</span>
          </button>
        </div>
      </div>
      <div id="buildingList" class="side-panel__list" role="list"></div>
    </aside>

    <div id="infoCard" class="top-card" aria-live="polite">
      <div style="display:flex; align-items:center; justify-content: space-between; gap: 12px; padding-top: 6px;">
        <div style="display:flex; align-items:baseline; gap:10px; color: var(--primary); font-weight: 700; letter-spacing: 1px; flex-wrap: wrap;">
          <span id="cardBadge" style="display:inline-flex; align-items:center; justify-content:center; padding: 6px 12px; border-radius: 999px; background: var(--primary); color: #fff; font-size: 13px;">#1</span>
          <span id="cardTitle" style="font-size: 18px; line-height: 1.15;">Building name</span>
        </div>
        <div style="display:flex; align-items:center; gap:10px;">
          <span style="color: var(--accent); font-weight: 700; letter-spacing: 3px; user-select:none;">◆ ✦ ◆ ✦ ◆</span>
          <button id="closeBtn" style="border: none; cursor: pointer; background: none; color: var(--primary); font-size: 18px; font-weight: 700; padding: 6px 10px; border-radius: 6px;">✕</button>
        </div>
      </div>
      <div style="height: 2px; background: var(--accent); margin: 10px 0 10px; opacity: .9;"></div>
      <div id="cardDesc" style="color: var(--primary); opacity: 0.85; font-size: 13px; line-height: 1.5;">Description</div>
      <div style="margin-top: 12px;">
        <a id="cardPanoLink" href="https://pano.dpm.org.cn/" target="_blank" rel="noopener noreferrer" style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; background: var(--primary); color: #fff; border-radius: 6px; font-size: 12px; font-weight: 600; text-decoration: none; letter-spacing: 0.5px; box-shadow: 0 2px 6px rgba(0,0,0,0.15);">
          🔗 <span class="lang-en-content">View in Official Forbidden City Panorama</span><span class="lang-zh-content">在故宫官网全景查看</span>
        </a>
      </div>
    </div>

    <div id="hoverTip"><span style="color: var(--accent); margin-right: 6px;">◆</span><span id="hoverTipText"></span></div>
    <div id="status">Loading…</div>
  </div>


  <!-- =========================================
       РАЗДЕЛ 3: ДАШБОРД С ГРАФИКАМИ (ECHART)
       ========================================= -->
  <div id="section-dashboard" class="app-section">
    <div class="container">
      <div class="section-title">
        <span class="lang-en-content">Visitor Statistics & Building Inventory</span>
        <span class="lang-zh-content">访问统计 · 建筑全景</span>
      </div>
      <div class="section-subtitle">
        <span class="lang-en-content">DATA INSIGHTS · FORBIDDEN CITY</span>
        <span class="lang-zh-content">DATA INSIGHTS · 故宫数据透视</span>
      </div>

      <div class="stats-row">
        <div class="stat-card" data-action="peak">
          <div class="stat-icon">👥</div>
          <div class="stat-value" data-count="<?php echo (int)$totalAllVisits; ?>">0</div>
          <div class="stat-label">
            <span class="lang-en-content">Total Visits</span><span class="lang-zh-content">总访问量</span>
          </div>
        </div>
        <div class="stat-card stat-card--highlight" data-action="peak">
          <div class="stat-icon">📈</div>
          <div class="stat-value" data-count="<?php echo (int)$maxVisits; ?>">0</div>
          <div class="stat-label">
            <span class="lang-en-content">Annual Peak</span><span class="lang-zh-content">年度峰值</span>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon">📊</div>
          <div class="stat-value" data-count="<?php echo (int)$avgVisits; ?>">0</div>
          <div class="stat-label">
            <span class="lang-en-content">Average Per Year</span><span class="lang-zh-content">年均访问</span>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon">📉</div>
          <div class="stat-value" id="selected-year-value" data-count="<?php echo (int)(isset($totals[0]) ? $totals[0] : 0); ?>">0</div>
          <div class="stat-label" id="selected-year-label">
            <span class="lang-en-content">2000 Visits</span><span class="lang-zh-content">2000年访问量</span>
          </div>
        </div>
      </div>

      <div class="year-control">
        <label>
          <span class="lang-en-content">SELECT YEAR</span>
          <span class="lang-zh-content">选择年份</span>
        </label>
        <select id="yearSelect"><?php foreach($years as $i => $y){ echo '<option value="'.$i.'">'.$y.'</option>'; } ?></select>
        <span class="year-divider">|</span>
        <button class="g-btn-secondary" id="goPeakBtn" type="button">
          <span class="lang-en-content">Peak Year</span><span class="lang-zh-content">峰值年</span>
        </button>
        <button class="g-btn-secondary" id="goFirstBtn" type="button">
          <span class="lang-en-content">First Year</span><span class="lang-zh-content">起始年</span>
        </button>
      </div>

      <div class="insight-card">
        <span class="insight-icon">💡</span>
        <span class="insight-text" id="insightText"></span>
      </div>

      <div class="charts-grid">
        <div class="chart-wrapper">
          <div class="chart-title">
            <span class="lang-en-content">Statistics by Year</span><span class="lang-zh-content">按年份统计</span>
          </div>
          <div id="barChart" class="chart"></div>
        </div>
        <div class="chart-wrapper">
          <div class="chart-title">
            <span class="lang-en-content">Monthly Distribution</span><span class="lang-zh-content">月度分布</span>
          </div>
          <div id="pieChart" class="chart"></div>
        </div>
        <div class="chart-wrapper">
          <div class="chart-title">
            <span class="lang-en-content">Building Inventory</span><span class="lang-zh-content">建筑库存分布</span>
          </div>
          <div id="buildingChart" class="chart"></div>
        </div>
        <div class="chart-wrapper">
          <div class="chart-title">
            <span class="lang-en-content">Major Landmarks</span><span class="lang-zh-content">著名景点流量</span>
          </div>
          <div id="landmarkChart" class="chart"></div>
        </div>
      </div>

      <div class="top5-card">
        <div class="top5-title">
          <span class="lang-en-content">🏆 TOP 5 LANDMARKS BY VISITORS</span>
          <span class="lang-zh-content">🏆 TOP 5 著名景点访问排行</span>
        </div>
        <div id="top5List"></div>
      </div>
    </div>
  </div>
<!-- =========================================
       РАЗДЕЛ 4: МИНИ ИГРА (INTTEST)
       ========================================= -->
  <div id="section-game" class="app-section fullscreen-section">
    <div class="game-app">
      <header class="panel" style="display:flex; justify-content: space-between; align-items:center;">
        <div>
          <h1 style="margin:0; font-size: 18px; color: var(--primary);">
            <span class="lang-en-content">Forbidden City — Match by Color</span>
            <span class="lang-zh-content">紫禁城 — 颜色匹配</span>
          </h1>
          <div style="margin-top:6px; font-size: 12px; opacity: .85;">
            <span class="lang-en-content">Click Name → click Description. Click linked card again to unpair.</span>
            <span class="lang-zh-content">点击名称 → 点击描述。再次点击已连接的卡片以取消配对。</span>
          </div>
        </div>
        <div id="scoreText" style="font-size: 12px; font-weight:bold; white-space: nowrap; color: var(--primary);">
          Connections: 0 / 40
        </div>
      </header>

      <main class="panel" style="min-height: 0;">
        <div class="cols">
          <section class="col">
            <div class="col__head">
              <span style="color: var(--primary);"><span class="lang-en-content">Names</span><span class="lang-zh-content">名称</span></span>
              <span style="color: var(--primary);">40</span>
            </div>
            <div class="list" id="namesList"></div>
          </section>
          <section class="col">
            <div class="col__head">
              <span style="color: var(--primary);"><span class="lang-en-content">Descriptions</span><span class="lang-zh-content">描述</span></span>
              <span style="color: var(--primary);">40</span>
            </div>
            <div class="list" id="descsList"></div>
          </section>
        </div>
      </main>

      <footer class="panel" style="display:flex; align-items:center; justify-content: space-between; flex-wrap:wrap;">
        <div style="font-size: 12px; opacity: .9; color: var(--primary);">
          <span class="lang-en-content">Tip: select a Name and click another Description to rebind.</span>
          <span class="lang-zh-content">提示：选择一个名称，然后点击另一个描述即可重新绑定。</span>
        </div>
        <div style="display:flex; gap:10px; margin-top:5px;">
          <button class="g-btn secondary" id="clearSelectedBtn" type="button">
            <span class="lang-en-content">Clear Pair</span><span class="lang-zh-content">清除配对</span>
          </button>
          <button class="g-btn secondary" id="resetBtn" type="button">
            <span class="lang-en-content">Reset</span><span class="lang-zh-content">重置</span>
          </button>
          <button class="g-btn" id="checkBtn" type="button">
            <span class="lang-en-content">Check</span><span class="lang-zh-content">检查答案</span>
          </button>
        </div>
      </footer>
    </div>

    <!-- Модальное окно результатов игры -->
    <div id="game-overlay">
      <div class="game-modal">
        <div style="display:flex; justify-content:space-between; align-items:center;">
          <h2 id="resultTitle" style="margin: 0; font-size: 18px; color: var(--primary);">Results</h2>
          <button class="g-btn secondary" id="closeOverlayBtn" type="button" style="padding: 4px 10px; font-size:12px;">
            <span class="lang-en-content">Close</span><span class="lang-zh-content">关闭</span>
          </button>
        </div>
        <p id="resultMeta" style="margin:8px 0 10px; font-size: 12px; opacity:.86; color: var(--primary);"></p>
        <div class="resultGrid" id="resultGrid"></div>
      </div>
    </div>
  </div>
  <!-- =========================================
       РАЗДЕЛ 5: ИМПЕРАТОРСКИЙ ТУР (IMPERIAL TOUR)
       ========================================= -->
  <div id="section-tour" class="app-section tour-section">

    <!-- Hero 区域 -->
    <section class="tour-hero">
      <div class="tour-hero__overlay"></div>
      <img class="tour-hero__bg" src="img/Screenshot 2026-03-20 at 13.14.03.png" alt="Forbidden City Aerial" />
      <div class="tour-hero__content">
        <div class="tour-hero__badge">
          <span class="lang-en-content">CULTURAL HERITAGE IMMERSIVE TOUR</span>
          <span class="lang-zh-content">文化遗产沉浸式漫游</span>
        </div>
        <h1 class="tour-hero__title">
          <span class="lang-en-content">Imperial Axis Panorama Tour</span>
          <span class="lang-zh-content">全景故宫中轴线漫游专题</span>
        </h1>
        <p class="tour-hero__subtitle">
          <span class="lang-en-content">A 600-year journey through the heart of imperial China — from the Meridian Gate to the Imperial Garden.</span>
          <span class="lang-zh-content">穿越明清两代 600 年历史，从午门到御花园，漫步紫禁城中轴线。</span>
        </p>
        <a href="https://pano.dpm.org.cn/#/panorama?regionId=20&secondAreaId=1&vpId=1870&sphPicId=1943181019715518464" target="_blank" rel="noopener noreferrer" class="tour-hero__cta">
          <span class="lang-en-content">Enter 360° Panorama →</span>
          <span class="lang-zh-content">进入 360° 全景 →</span>
        </a>
      </div>
      <div class="tour-hero__scroll-hint">↓ <span class="lang-en-content">scroll to explore</span><span class="lang-zh-content">向下滚动探索</span></div>
    </section>

    <!-- 项目背景 -->
    <section class="tour-block tour-intro">
      <div class="tour-container">
        <div class="tour-block__label">
          <span class="lang-en-content">01 · PROJECT OVERVIEW</span>
          <span class="lang-zh-content">01 · 项目概述</span>
        </div>
        <h2 class="tour-block__title">
          <span class="lang-en-content">Reviving Heritage Through Immersive Storytelling</span>
          <span class="lang-zh-content">以沉浸式叙事让文化遗产焕发新生</span>
        </h2>
        <div class="tour-block__body">
          <p>
            <span class="lang-en-content">As a UNESCO World Heritage Site, the Forbidden City attracts millions of visitors each year — yet space, time, and conservation constraints make it impossible for any single visitor to absorb its full historical depth. Traditional immersive tours rarely go beyond visual display, with limited multi-modal context and fragmented data management.</span>
            <span class="lang-zh-content">作为世界文化遗产地，故宫博物院每年接待上千万观众。然而受空间承载力、开放时间、保护要求等限制，实地游览难以全面领略其全貌。传统沉浸式漫游多停留在视觉展示层面，存在全景数据价值挖掘不足、多模态数据展示单一、大空间漫游开发困难、数据管理混乱等问题。</span>
          </p>
          <p>
            <span class="lang-en-content">Since 2015, the Palace Museum has accumulated a vast library of panoramic imagery and multi-modal data. In 2023, with funding from the Ministry of Culture and Tourism's National Key R&amp;D Program, we launched the project <em>"Multi-modal Data Value Mining Based on Panoramic Imagery for Immersive Cultural Heritage Tourism"</em>. After three years of fieldwork, data integration, and technical development, the Forbidden City Central Axis was selected as the pilot area — covering four core regions: Outer Southern Gate, Outer Court, Inner Court, and the Imperial Garden.</span>
            <span class="lang-zh-content">故宫博物院自 2015 年起积累海量全景影像与多模态数据。2023 年，在文化和旅游部国家文化和旅游科技创新研发重点项目资金资助下，正式启动《文化遗产型旅游目的地沉浸式漫游中基于全景影像的多模态数据价值挖掘研究》项目。历经三年规划与实践，项目以故宫中轴线区域为核心示范样本，突破传统全景展示局限，深度挖掘全景影像数据价值，构建起可复制、可推广的文化遗产数字化保护与沉浸式游览新模式。</span>
          </p>
        </div>
      </div>
    </section>

    <!-- 中轴线平面图 + 路线 -->
    <section class="tour-block tour-axis">
      <div class="tour-container">
        <div class="tour-block__label">
          <span class="lang-en-content">02 · THE CENTRAL AXIS</span>
          <span class="lang-zh-content">02 · 中轴线概览</span>
        </div>
        <h2 class="tour-block__title">
          <span class="lang-en-content">The Spine of an Empire</span>
          <span class="lang-zh-content">帝国之脊</span>
        </h2>
        <div class="tour-axis__layout">
          <div class="tour-axis__map">
            <svg viewBox="0 0 360 760" class="tour-axis__svg" xmlns="http://www.w3.org/2000/svg">
              <defs>
                <linearGradient id="mainHall" x1="0%" y1="0%" x2="0%" y2="100%">
                  <stop offset="0%" stop-color="#d4af37" stop-opacity="0.5"/>
                  <stop offset="100%" stop-color="#d4af37" stop-opacity="0.15"/>
                </linearGradient>
                <linearGradient id="sideHall" x1="0%" y1="0%" x2="0%" y2="100%">
                  <stop offset="0%" stop-color="#d4af37" stop-opacity="0.3"/>
                  <stop offset="100%" stop-color="#d4af37" stop-opacity="0.08"/>
                </linearGradient>
                <linearGradient id="labelBg" x1="0%" y1="0%" x2="0%" y2="100%">
                  <stop offset="0%" stop-color="rgba(212,175,55,0.95)"/>
                  <stop offset="100%" stop-color="rgba(180,140,40,0.85)"/>
                </linearGradient>
                <linearGradient id="labelBgSub" x1="0%" y1="0%" x2="0%" y2="100%">
                  <stop offset="0%" stop-color="rgba(40,35,70,0.95)"/>
                  <stop offset="100%" stop-color="rgba(25,20,50,0.95)"/>
                </linearGradient>
                <radialGradient id="gardenBg" cx="50%" cy="50%" r="50%">
                  <stop offset="0%" stop-color="#3a6b4e" stop-opacity="0.5"/>
                  <stop offset="100%" stop-color="#2a4a3e" stop-opacity="0.2"/>
                </radialGradient>
                <pattern id="roof" patternUnits="userSpaceOnUse" width="6" height="3">
                  <rect width="6" height="3" fill="#3d2817"/>
                  <line x1="0" y1="0" x2="6" y2="0" stroke="#5c3a1f" stroke-width="0.5"/>
                </pattern>
                <filter id="goldGlow" x="-50%" y="-50%" width="200%" height="200%">
                  <feGaussianBlur stdDeviation="2"/>
                  <feMerge><feMergeNode/><feMergeNode in="SourceGraphic"/></feMerge>
                </filter>
                <filter id="softGlow" x="-50%" y="-50%" width="200%" height="200%">
                  <feGaussianBlur stdDeviation="0.8"/>
                  <feMerge><feMergeNode/><feMergeNode in="SourceGraphic"/></feMerge>
                </filter>
              </defs>

              <!-- Background -->
              <rect x="0" y="0" width="360" height="760" fill="#16142e"/>
              <rect x="0" y="0" width="360" height="760" fill="url(#mainHall)" opacity="0.08"/>

              <!-- 顶部标题 -->
              <g transform="translate(180, 28)">
                <text text-anchor="middle" fill="#e8c860" font-size="14" font-weight="800" letter-spacing="6" filter="url(#softGlow)">紫禁城中轴线</text>
                <text text-anchor="middle" y="14" fill="rgba(212,175,55,0.5)" font-size="7" letter-spacing="3">THE CENTRAL AXIS · 永乐十八年 · 1420</text>
                <line x1="-30" y1="20" x2="30" y2="20" stroke="#d4af37" stroke-width="0.5" opacity="0.6"/>
              </g>

              <!-- Outer Wall (with corner towers) -->
              <rect x="30" y="55" width="300" height="660" fill="none" stroke="#d4af37" stroke-width="1.5" opacity="0.6"/>
              <rect x="35" y="60" width="290" height="650" fill="none" stroke="#d4af37" stroke-width="0.5" opacity="0.3"/>
              <rect x="22" y="48" width="316" height="674" fill="none" stroke="#5a8db5" stroke-width="0.8" stroke-dasharray="2 4" opacity="0.35"/>

              <!-- Corner towers -->
              <g fill="#d4af37" opacity="0.85">
                <polygon points="30,55 40,45 50,55"/>
                <polygon points="310,55 320,45 330,55"/>
                <polygon points="30,715 40,725 50,715"/>
                <polygon points="310,715 320,725 330,715"/>
              </g>

              <!-- Central axis road (3 lines) -->
              <g opacity="0.7">
                <line x1="160" y1="80" x2="160" y2="700" stroke="#d4af37" stroke-width="1" stroke-dasharray="3 5"/>
                <line x1="180" y1="80" x2="180" y2="700" stroke="#e8c860" stroke-width="2.5"/>
                <line x1="200" y1="80" x2="200" y2="700" stroke="#d4af37" stroke-width="1" stroke-dasharray="3 5"/>
              </g>

              <!-- Side halls (配殿) -->
              <g opacity="0.5">
                <rect x="75" y="495" width="55" height="18" fill="url(#sideHall)" stroke="#d4af37" stroke-width="0.6"/>
                <rect x="230" y="495" width="55" height="18" fill="url(#sideHall)" stroke="#d4af37" stroke-width="0.6"/>
                <rect x="75" y="305" width="55" height="18" fill="url(#sideHall)" stroke="#d4af37" stroke-width="0.6"/>
                <rect x="230" y="305" width="55" height="18" fill="url(#sideHall)" stroke="#d4af37" stroke-width="0.6"/>
              </g>

              <!-- ============ 1. 午门 (Wumen) ============ -->
              <g data-step="1" class="tour-axis__building">
                <!-- 建筑 -->
                <path d="M 88 632 L 88 658 L 100 658 L 100 642 L 260 642 L 260 658 L 272 658 L 272 632 Z"
                      fill="url(#sideHall)" stroke="#d4af37" stroke-width="1.3"/>
                <rect x="170" y="632" width="20" height="8" fill="#d4af37"/>
                <line x1="100" y1="649" x2="260" y2="649" stroke="#d4af37" stroke-width="0.5" opacity="0.4"/>
                <!-- 标识牌 (左侧) -->
                <g transform="translate(58, 615)">
                  <rect x="-30" y="-10" width="60" height="20" rx="3" fill="url(#labelBgSub)" stroke="#d4af37" stroke-width="0.8"/>
                  <text text-anchor="middle" y="4" fill="#e8c860" font-size="11" font-weight="700" letter-spacing="2">午门</text>
                  <line x1="30" y1="0" x2="42" y2="0" stroke="#d4af37" stroke-width="0.8"/>
                </g>
                <!-- 右侧数字 -->
                <g transform="translate(310, 645)">
                  <circle r="9" fill="#d4af37" stroke="#16142e" stroke-width="2"/>
                  <text text-anchor="middle" y="3.5" fill="#16142e" font-size="11" font-weight="800">1</text>
                </g>
              </g>

              <!-- ============ 2. 太和门 (Taihemen) ============ -->
              <g data-step="2" class="tour-axis__building">
                <rect x="115" y="545" width="130" height="30" fill="url(#sideHall)" stroke="#d4af37" stroke-width="1"/>
                <line x1="115" y1="552" x2="245" y2="552" stroke="#d4af37" stroke-width="0.5" opacity="0.5"/>
                <rect x="170" y="545" width="20" height="30" fill="#d4af37" opacity="0.35"/>
                <!-- 标识牌 (左侧) -->
                <g transform="translate(62, 560)">
                  <rect x="-32" y="-10" width="64" height="20" rx="3" fill="url(#labelBgSub)" stroke="#d4af37" stroke-width="0.8"/>
                  <text text-anchor="middle" y="4" fill="#e8c860" font-size="11" font-weight="700" letter-spacing="2">太和门</text>
                  <line x1="32" y1="0" x2="45" y2="0" stroke="#d4af37" stroke-width="0.8"/>
                </g>
                <!-- 右侧数字 -->
                <g transform="translate(310, 560)">
                  <circle r="9" fill="#d4af37" stroke="#16142e" stroke-width="2"/>
                  <text text-anchor="middle" y="3.5" fill="#16142e" font-size="11" font-weight="800">2</text>
                </g>
              </g>

              <!-- ============ 3. 太和殿 (Taihedian) - 主殿 ============ -->
              <g data-step="3" class="tour-axis__building">
                <!-- 双檐屋顶 -->
                <rect x="95" y="440" width="170" height="6" fill="url(#roof)" stroke="#d4af37" stroke-width="0.8"/>
                <rect x="90" y="446" width="180" height="6" fill="url(#roof)" stroke="#d4af37" stroke-width="0.8"/>
                <!-- 主体 -->
                <rect x="85" y="452" width="190" height="50" fill="url(#mainHall)" stroke="#d4af37" stroke-width="2" filter="url(#goldGlow)"/>
                <!-- 立柱 -->
                <line x1="115" y1="452" x2="115" y2="502" stroke="#d4af37" stroke-width="0.6" opacity="0.5"/>
                <line x1="145" y1="452" x2="145" y2="502" stroke="#d4af37" stroke-width="0.6" opacity="0.5"/>
                <line x1="180" y1="452" x2="180" y2="502" stroke="#d4af37" stroke-width="0.6" opacity="0.5"/>
                <line x1="215" y1="452" x2="215" y2="502" stroke="#d4af37" stroke-width="0.6" opacity="0.5"/>
                <line x1="245" y1="452" x2="245" y2="502" stroke="#d4af37" stroke-width="0.6" opacity="0.5"/>
                <!-- 主标识牌 (上方大型, 金色背景) -->
                <g transform="translate(180, 415)">
                  <rect x="-45" y="-14" width="90" height="28" rx="4" fill="url(#labelBg)" stroke="#e8c860" stroke-width="1.2" filter="url(#softGlow)"/>
                  <text text-anchor="middle" y="5" fill="#1a1438" font-size="15" font-weight="900" letter-spacing="4">太和殿</text>
                </g>
                <!-- 副标识 (下方说明) -->
                <text x="180" y="518" text-anchor="middle" fill="rgba(245,233,208,0.7)" font-size="8.5" font-style="italic" letter-spacing="2">金銮殿 · 御极之所</text>
                <!-- 数字 -->
                <g transform="translate(310, 477)">
                  <circle r="11" fill="#e8c860" stroke="#16142e" stroke-width="2.5"/>
                  <text text-anchor="middle" y="4" fill="#16142e" font-size="13" font-weight="900">3</text>
                </g>
              </g>

              <!-- ============ 4. 保和殿 (Baohedian) ============ -->
              <g data-step="4" class="tour-axis__building">
                <rect x="105" y="365" width="150" height="6" fill="url(#roof)" stroke="#d4af37" stroke-width="0.7"/>
                <rect x="100" y="371" width="160" height="36" fill="url(#sideHall)" stroke="#d4af37" stroke-width="1.3"/>
                <line x1="135" y1="371" x2="135" y2="407" stroke="#d4af37" stroke-width="0.5" opacity="0.5"/>
                <line x1="180" y1="371" x2="180" y2="407" stroke="#d4af37" stroke-width="0.5" opacity="0.5"/>
                <line x1="225" y1="371" x2="225" y2="407" stroke="#d4af37" stroke-width="0.5" opacity="0.5"/>
                <!-- 标识牌 (左侧) -->
                <g transform="translate(60, 389)">
                  <rect x="-32" y="-10" width="64" height="20" rx="3" fill="url(#labelBgSub)" stroke="#d4af37" stroke-width="0.8"/>
                  <text text-anchor="middle" y="4" fill="#e8c860" font-size="11" font-weight="700" letter-spacing="2">保和殿</text>
                  <line x1="32" y1="0" x2="45" y2="0" stroke="#d4af37" stroke-width="0.8"/>
                </g>
                <!-- 右侧数字 -->
                <g transform="translate(310, 389)">
                  <circle r="9" fill="#d4af37" stroke="#16142e" stroke-width="2"/>
                  <text text-anchor="middle" y="3.5" fill="#16142e" font-size="11" font-weight="800">4</text>
                </g>
              </g>

              <!-- ============ 5. 乾清门 (Qianqingmen) ============ -->
              <g data-step="5" class="tour-axis__building">
                <rect x="115" y="265" width="130" height="30" fill="url(#sideHall)" stroke="#d4af37" stroke-width="1"/>
                <line x1="115" y1="272" x2="245" y2="272" stroke="#d4af37" stroke-width="0.5" opacity="0.5"/>
                <rect x="170" y="265" width="20" height="30" fill="#d4af37" opacity="0.35"/>
                <!-- 标识牌 (左侧) -->
                <g transform="translate(60, 280)">
                  <rect x="-32" y="-10" width="64" height="20" rx="3" fill="url(#labelBgSub)" stroke="#d4af37" stroke-width="0.8"/>
                  <text text-anchor="middle" y="4" fill="#e8c860" font-size="11" font-weight="700" letter-spacing="2">乾清门</text>
                  <line x1="32" y1="0" x2="47" y2="0" stroke="#d4af37" stroke-width="0.8"/>
                </g>
                <!-- 右侧数字 -->
                <g transform="translate(310, 280)">
                  <circle r="9" fill="#d4af37" stroke="#16142e" stroke-width="2"/>
                  <text text-anchor="middle" y="3.5" fill="#16142e" font-size="11" font-weight="800">5</text>
                </g>
              </g>

              <!-- ============ 6. 乾清宫 (Qianqinggong) - 主殿 ============ -->
              <g data-step="6" class="tour-axis__building">
                <rect x="95" y="160" width="170" height="6" fill="url(#roof)" stroke="#d4af37" stroke-width="0.8"/>
                <rect x="90" y="166" width="180" height="6" fill="url(#roof)" stroke="#d4af37" stroke-width="0.8"/>
                <rect x="85" y="172" width="190" height="50" fill="url(#mainHall)" stroke="#d4af37" stroke-width="2" filter="url(#goldGlow)"/>
                <line x1="115" y1="172" x2="115" y2="222" stroke="#d4af37" stroke-width="0.6" opacity="0.5"/>
                <line x1="145" y1="172" x2="145" y2="222" stroke="#d4af37" stroke-width="0.6" opacity="0.5"/>
                <line x1="180" y1="172" x2="180" y2="222" stroke="#d4af37" stroke-width="0.6" opacity="0.5"/>
                <line x1="215" y1="172" x2="215" y2="222" stroke="#d4af37" stroke-width="0.6" opacity="0.5"/>
                <line x1="245" y1="172" x2="245" y2="222" stroke="#d4af37" stroke-width="0.6" opacity="0.5"/>
                <!-- 主标识牌 -->
                <g transform="translate(180, 135)">
                  <rect x="-45" y="-14" width="90" height="28" rx="4" fill="url(#labelBg)" stroke="#e8c860" stroke-width="1.2" filter="url(#softGlow)"/>
                  <text text-anchor="middle" y="5" fill="#1a1438" font-size="15" font-weight="900" letter-spacing="4">乾清宫</text>
                </g>
                <!-- 副标识 -->
                <text x="180" y="238" text-anchor="middle" fill="rgba(245,233,208,0.7)" font-size="8.5" font-style="italic" letter-spacing="2">正大光明 · 帝寝</text>
                <!-- 数字 -->
                <g transform="translate(310, 197)">
                  <circle r="11" fill="#e8c860" stroke="#16142e" stroke-width="2.5"/>
                  <text text-anchor="middle" y="4" fill="#16142e" font-size="13" font-weight="900">6</text>
                </g>
              </g>

              <!-- ============ 7. 御花园 (Yuhuayuan) ============ -->
              <g data-step="7" class="tour-axis__building">
                <!-- 花园绿地 -->
                <circle cx="180" cy="100" r="38" fill="url(#gardenBg)" stroke="#d4af37" stroke-width="1" stroke-dasharray="2 3"/>
                <!-- 树木 -->
                <circle cx="150" cy="83" r="6" fill="#3a6b4e" stroke="#5a8b6e" stroke-width="0.8"/>
                <circle cx="210" cy="83" r="6" fill="#3a6b4e" stroke="#5a8b6e" stroke-width="0.8"/>
                <circle cx="155" cy="115" r="5" fill="#3a6b4e" stroke="#5a8b6e" stroke-width="0.8"/>
                <circle cx="205" cy="115" r="5" fill="#3a6b4e" stroke="#5a8b6e" stroke-width="0.8"/>
                <!-- 亭子 -->
                <polygon points="180,92 188,96 188,104 180,108 172,104 172,96" fill="#8b4513" stroke="#d4af37" stroke-width="0.8"/>
                <polygon points="180,88 192,95 168,95" fill="url(#roof)" stroke="#d4af37" stroke-width="0.8"/>
                <!-- 假山 -->
                <path d="M 142 122 Q 150 115 158 124 L 152 128 Z" fill="#5a5a6e" stroke="#8a8a9e" stroke-width="0.5"/>
                <path d="M 202 122 Q 210 115 218 124 L 212 128 Z" fill="#5a5a6e" stroke="#8a8a9e" stroke-width="0.5"/>
                <!-- 标识牌 (上方) -->
                <g transform="translate(180, 60)">
                  <rect x="-45" y="-12" width="90" height="24" rx="3" fill="url(#labelBg)" stroke="#a8d4b8" stroke-width="1"/>
                  <text text-anchor="middle" y="4" fill="#1a1438" font-size="13" font-weight="800" letter-spacing="4">御花园</text>
                </g>
                <!-- 副标识 -->
                <text x="180" y="155" text-anchor="middle" fill="rgba(168,212,184,0.8)" font-size="8.5" font-style="italic" letter-spacing="2">亭台 · 古柏 · 假山</text>
                <!-- 数字 -->
                <g transform="translate(50, 100)">
                  <circle r="9" fill="#a8d4b8" stroke="#16142e" stroke-width="2"/>
                  <text text-anchor="middle" y="3.5" fill="#16142e" font-size="11" font-weight="800">7</text>
                </g>
              </g>

              <!-- 底部 Compass + Scale + 比例尺 -->
              <g transform="translate(50, 738)">
                <circle cx="0" cy="0" r="13" fill="#16142e" stroke="#d4af37" stroke-width="1"/>
                <text text-anchor="middle" y="-4" fill="#e8c860" font-size="9" font-weight="900">N</text>
                <polygon points="0,-10 -3,-5 3,-5" fill="#d4af37"/>
                <text text-anchor="middle" y="9" fill="rgba(212,175,55,0.6)" font-size="7">南</text>
              </g>

              <g transform="translate(180, 738)" fill="rgba(212,175,55,0.7)" font-size="8">
                <text text-anchor="middle" letter-spacing="3">★ 南 → 北 ★</text>
              </g>

              <g transform="translate(265, 738)">
                <line x1="0" y1="0" x2="50" y2="0" stroke="#d4af37" stroke-width="1.5"/>
                <line x1="0" y1="-3" x2="0" y2="3" stroke="#d4af37" stroke-width="1.5"/>
                <line x1="25" y1="-2" x2="25" y2="2" stroke="#d4af37" stroke-width="1"/>
                <line x1="50" y1="-3" x2="50" y2="3" stroke="#d4af37" stroke-width="1.5"/>
                <text x="25" y="12" text-anchor="middle" fill="rgba(212,175,55,0.7)" font-size="7">100m</text>
              </g>
            </svg></svg>
            </div>
          <div class="tour-axis__steps">
            <div class="tour-axis__step" data-step="1">
              <div class="tour-axis__step-num">01</div>
              <div class="tour-axis__step-content">
                <h3><span class="lang-en-content">Meridian Gate (Wumen)</span><span class="lang-zh-content">午门</span></h3>
                <p><span class="lang-en-content">The grand southern entrance. The Emperor's proclamations were issued here, and the U-shaped structure once held the morning court.</span><span class="lang-zh-content">紫禁城正南门，明清两代皇帝颁布诏书之处。平面呈"凹"字形，曾设朝审、廷杖等重大典礼。</span></p>
              </div>
            </div>
            <div class="tour-axis__step" data-step="2">
              <div class="tour-axis__step-num">02</div>
              <div class="tour-axis__step-content">
                <h3><span class="lang-en-content">Gate of Supreme Harmony (Taihemen)</span><span class="lang-zh-content">太和门</span></h3>
                <p><span class="lang-en-content">Guarded by bronze lions, this gate marks the entrance to the Outer Court — the political heart of imperial China.</span><span class="lang-zh-content">门前蹲坐两对铜狮子，标志外朝的起点，是明清皇帝举行"御门听政"之处。</span></p>
              </div>
            </div>
            <div class="tour-axis__step" data-step="3">
              <div class="tour-axis__step-num">03</div>
              <div class="tour-axis__step-content">
                <h3><span class="lang-en-content">Hall of Supreme Harmony (Taihedian)</span><span class="lang-zh-content">太和殿</span></h3>
                <p><span class="lang-en-content">The largest wooden structure in the complex. Used for enthronement ceremonies, imperial weddings, and the announcement of military victories.</span><span class="lang-zh-content">俗称"金銮殿"，紫禁城内体量最大的木结构建筑。用于皇帝登基、大婚、册立皇后、命将出征等大典。</span></p>
              </div>
            </div>
            <div class="tour-axis__step" data-step="4">
              <div class="tour-axis__step-num">04</div>
              <div class="tour-axis__step-content">
                <h3><span class="lang-en-content">Hall of Preserved Harmony (Baohedian)</span><span class="lang-zh-content">保和殿</span></h3>
                <p><span class="lang-en-content">Host of imperial banquets and the highest-level imperial examinations (palace exams) of the Qing dynasty.</span><span class="lang-zh-content">乾隆以后成为科举殿试场所，清代公主下嫁亦在此举行。殿后"云龙大石雕"重 200 余吨，为紫禁城最大石雕。</span></p>
              </div>
            </div>
            <div class="tour-axis__step" data-step="5">
              <div class="tour-axis__step-num">05</div>
              <div class="tour-axis__step-content">
                <h3><span class="lang-en-content">Gate of Heavenly Purity (Qianqingmen)</span><span class="lang-zh-content">乾清门</span></h3>
                <p><span class="lang-en-content">The threshold between state business (Outer Court) and family life (Inner Court).</span><span class="lang-zh-content">分隔外朝与内廷的标志性建筑。门内"御门听政"延续至雍正朝，军机处亦设于此门外。</span></p>
              </div>
            </div>
            <div class="tour-axis__step" data-step="6">
              <div class="tour-axis__step-num">06</div>
              <div class="tour-axis__step-content">
                <h3><span class="lang-en-content">Palace of Heavenly Purity (Qianqinggong)</span><span class="lang-zh-content">乾清宫</span></h3>
                <p><span class="lang-en-content">Primary residence of Ming and early Qing emperors. The plaque bears four imperial scripts, each written by a different emperor.</span><span class="lang-zh-content">明及清初皇帝的寝宫。"正大光明"匾额由顺治、康熙、雍正、乾隆四位皇帝先后题写，决定了清朝皇位继承。</span></p>
              </div>
            </div>
            <div class="tour-axis__step" data-step="7">
              <div class="tour-axis__step-num">07</div>
              <div class="tour-axis__step-content">
                <h3><span class="lang-en-content">Imperial Garden (Yuhuayuan)</span><span class="lang-zh-content">御花园</span></h3>
                <p><span class="lang-en-content">A classical Ming-and-Qing garden behind the Inner Court — pavilions, ancient cypresses, and rockeries arranged along the central axis.</span><span class="lang-zh-content">建于明代，以中轴对称布局，亭台楼阁与古树奇石交织。园内"连理柏"被誉为"帝王树"，见证朝代兴替。</span></p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- 推荐路线（横向卡片） -->
    <section class="tour-block tour-recommend">
      <div class="tour-container">
        <div class="tour-block__label">
          <span class="lang-en-content">03 · RECOMMENDED ROUTE</span>
          <span class="lang-zh-content">03 · 推荐路线</span>
        </div>
        <h2 class="tour-block__title">
          <span class="lang-en-content">A Half-Day Imperial Walk</span>
          <span class="lang-zh-content">半日皇城漫步</span>
        </h2>
        <div class="tour-recommend__grid">
          <a href="https://pano.dpm.org.cn/#/panorama?regionId=47" target="_blank" rel="noopener noreferrer" class="tour-recommend__card">
            <div class="tour-recommend__card-img" style="background-image: url('img/Screenshot 2026-03-20 at 13.38.02.png');"></div>
            <div class="tour-recommend__card-num">01</div>
            <h3><span class="lang-en-content">Meridian Gate</span><span class="lang-zh-content">午门</span></h3>
            <p><span class="lang-en-content">Begin at the southern gate, ascend the ramparts, and gaze north over the entire palace.</span><span class="lang-zh-content">从南门开始，登上城墙向北俯瞰整座宫殿。</span></p>
          </a>
          <a href="https://pano.dpm.org.cn/#/panorama?regionId=20" target="_blank" rel="noopener noreferrer" class="tour-recommend__card">
            <div class="tour-recommend__card-img" style="background-image: url('img/Screenshot 2026-03-20 at 13.14.03.png');"></div>
            <div class="tour-recommend__card-num">02</div>
            <h3><span class="lang-en-content">Outer Court</span><span class="lang-zh-content">外朝三殿</span></h3>
            <p><span class="lang-en-content">Walk through the three great halls — Supreme, Central, and Preserved Harmony.</span><span class="lang-zh-content">走过太和、中和、保和三座大殿。</span></p>
          </a>
          <a href="https://pano.dpm.org.cn/#/panorama?regionId=61" target="_blank" rel="noopener noreferrer" class="tour-recommend__card">
            <div class="tour-recommend__card-img" style="background-image: url('img/Screenshot 2026-03-20 at 13.38.22.png');"></div>
            <div class="tour-recommend__card-num">03</div>
            <h3><span class="lang-en-content">Inner Court</span><span class="lang-zh-content">内廷三宫</span></h3>
            <p><span class="lang-en-content">Discover the residence of emperors and empresses — three palaces of private life.</span><span class="lang-zh-content">探访帝后起居之所——乾清、交泰、坤宁三宫。</span></p>
          </a>
          <a href="https://pano.dpm.org.cn/#/panorama?regionId=63" target="_blank" rel="noopener noreferrer" class="tour-recommend__card">
            <div class="tour-recommend__card-img" style="background-image: url('img/Weixin Image_20260323130514_74_57.jpg');"></div>
            <div class="tour-recommend__card-num">04</div>
            <h3><span class="lang-en-content">Imperial Garden</span><span class="lang-zh-content">御花园</span></h3>
            <p><span class="lang-en-content">End at the classical garden — pavilions, pines, and the north gate (Gate of Divine Prowess).</span><span class="lang-zh-content">在古典园林中结束行程——亭台、古松与神武门。</span></p>
          </a>
        </div>
      </div>
    </section>

    <!-- 数据展示 -->
    <section class="tour-block tour-stats">
      <div class="tour-container">
        <div class="tour-block__label">
          <span class="lang-en-content">04 · BY THE NUMBERS</span>
          <span class="lang-zh-content">04 · 数据概览</span>
        </div>
        <h2 class="tour-block__title">
          <span class="lang-en-content">Scale of the Project</span>
          <span class="lang-zh-content">项目规模</span>
        </h2>
        <div class="tour-stats__grid">
          <div class="tour-stat">
            <div class="tour-stat__num">~1,000</div>
            <div class="tour-stat__label"><span class="lang-en-content">Panoramic Viewpoints</span><span class="lang-zh-content">全景视点</span></div>
          </div>
          <div class="tour-stat">
            <div class="tour-stat__num">4</div>
            <div class="tour-stat__label"><span class="lang-en-content">Core Regions</span><span class="lang-zh-content">核心区域</span></div>
          </div>
          <div class="tour-stat">
            <div class="tour-stat__num">2023</div>
            <div class="tour-stat__label"><span class="lang-en-content">Project Launch Year</span><span class="lang-zh-content">项目启动年</span></div>
          </div>
          <div class="tour-stat">
            <div class="tour-stat__num">600+</div>
            <div class="tour-stat__label"><span class="lang-en-content">Years of History</span><span class="lang-zh-content">年历史</span></div>
          </div>
        </div>
      </div>
    </section>

    <!-- 数据图谱 -->
    <section class="tour-block tour-network">
      <div class="tour-container">
        <div class="tour-block__label">
          <span class="lang-en-content">05 · MULTI-MODAL DATA NETWORK</span>
          <span class="lang-zh-content">05 · 多模态数据网络</span>
        </div>
        <h2 class="tour-block__title">
          <span class="lang-en-content">Connecting Imagery, Text, and Sound</span>
          <span class="lang-zh-content">连接影像、文字与声音</span>
        </h2>
        <div class="tour-network__wrap">
          <svg viewBox="0 0 800 500" class="tour-network__svg" xmlns="http://www.w3.org/2000/svg">
            <defs>
              <radialGradient id="nodeGlow" cx="50%" cy="50%" r="50%">
                <stop offset="0%" stop-color="#d4af37" stop-opacity="0.9"/>
                <stop offset="100%" stop-color="#d4af37" stop-opacity="0.1"/>
              </radialGradient>
            </defs>
            <g stroke="rgba(212,175,55,0.3)" stroke-width="1" fill="none">
              <line x1="400" y1="250" x2="180" y2="120"/>
              <line x1="400" y1="250" x2="620" y2="120"/>
              <line x1="400" y1="250" x2="150" y2="380"/>
              <line x1="400" y1="250" x2="650" y2="380"/>
              <line x1="400" y1="250" x2="400" y2="60"/>
              <line x1="400" y1="250" x2="400" y2="440"/>
              <line x1="180" y1="120" x2="620" y2="120"/>
              <line x1="150" y1="380" x2="650" y2="380"/>
            </g>
            <circle cx="400" cy="250" r="40" fill="url(#nodeGlow)"/>
            <circle cx="400" cy="250" r="22" fill="#d4af37"/>
            <text x="400" y="254" text-anchor="middle" fill="#1a1a2e" font-size="11" font-weight="700">PANORAMA</text>
            <g><circle cx="180" cy="120" r="28" fill="#1a1a2e" stroke="#d4af37" stroke-width="1.5"/><text x="180" y="115" text-anchor="middle" fill="#f5e9d0" font-size="10" font-weight="600">3D Models</text><text x="180" y="130" text-anchor="middle" fill="#d4af37" font-size="9">3D 模型</text></g>
            <g><circle cx="620" cy="120" r="28" fill="#1a1a2e" stroke="#d4af37" stroke-width="1.5"/><text x="620" y="115" text-anchor="middle" fill="#f5e9d0" font-size="10" font-weight="600">Audio Guide</text><text x="620" y="130" text-anchor="middle" fill="#d4af37" font-size="9">音频讲解</text></g>
            <g><circle cx="150" cy="380" r="28" fill="#1a1a2e" stroke="#d4af37" stroke-width="1.5"/><text x="150" y="375" text-anchor="middle" fill="#f5e9d0" font-size="10" font-weight="600">Historical Text</text><text x="150" y="390" text-anchor="middle" fill="#d4af37" font-size="9">历史文献</text></g>
            <g><circle cx="650" cy="380" r="28" fill="#1a1a2e" stroke="#d4af37" stroke-width="1.5"/><text x="650" y="375" text-anchor="middle" fill="#f5e9d0" font-size="10" font-weight="600">Maps & Plans</text><text x="650" y="390" text-anchor="middle" fill="#d4af37" font-size="9">地图规划</text></g>
            <g><circle cx="400" cy="60" r="28" fill="#1a1a2e" stroke="#d4af37" stroke-width="1.5"/><text x="400" y="55" text-anchor="middle" fill="#f5e9d0" font-size="10" font-weight="600">Spherical Video</text><text x="400" y="70" text-anchor="middle" fill="#d4af37" font-size="9">全景影像</text></g>
            <g><circle cx="400" cy="440" r="28" fill="#1a1a2e" stroke="#d4af37" stroke-width="1.5"/><text x="400" y="435" text-anchor="middle" fill="#f5e9d0" font-size="10" font-weight="600">VR Scenes</text><text x="400" y="450" text-anchor="middle" fill="#d4af37" font-size="9">VR 场景</text></g>
          </svg>
        </div>
        <p class="tour-network__caption">
          <span class="lang-en-content">A unified multi-modal knowledge graph links panoramic imagery with 3D models, audio narration, historical archives, and VR reconstructions.</span>
          <span class="lang-zh-content">统一的多模态知识图谱将全景影像与 3D 模型、音频讲解、历史档案、VR 重建相连接。</span>
        </p>
      </div>
    </section>

    <!-- 项目时间线 -->
    <section class="tour-block tour-timeline">
      <div class="tour-container">
        <div class="tour-block__label">
          <span class="lang-en-content">06 · PROJECT TIMELINE</span>
          <span class="lang-zh-content">06 · 项目时间线</span>
        </div>
        <h2 class="tour-block__title">
          <span class="lang-en-content">Three Years of Discovery</span>
          <span class="lang-zh-content">三年探索历程</span>
        </h2>
        <div class="tour-timeline__list">
          <div class="tour-timeline__item">
            <div class="tour-timeline__date">2015</div>
            <div class="tour-timeline__body">
              <h3><span class="lang-en-content">Data Accumulation Begins</span><span class="lang-zh-content">数据积累起步</span></h3>
              <p><span class="lang-en-content">The Palace Museum begins systematic panoramic image capture across the complex.</span><span class="lang-zh-content">故宫博物院启动全宫区系统性全景影像采集。</span></p>
            </div>
          </div>
          <div class="tour-timeline__item">
            <div class="tour-timeline__date">2023.11</div>
            <div class="tour-timeline__body">
              <h3><span class="lang-en-content">Project Launched</span><span class="lang-zh-content">项目正式启动</span></h3>
              <p><span class="lang-en-content">Funded by the Ministry of Culture and Tourism's National Key R&amp;D Program, the immersive-tourism research project begins.</span><span class="lang-zh-content">获文化和旅游部国家文化和旅游科技创新研发重点项目资助，项目正式启动。</span></p>
            </div>
          </div>
          <div class="tour-timeline__item">
            <div class="tour-timeline__date">2024</div>
            <div class="tour-timeline__body">
              <h3><span class="lang-en-content">Fieldwork & Data Integration</span><span class="lang-zh-content">实地采集与数据整合</span></h3>
              <p><span class="lang-en-content">Teams conduct on-site panoramic capture in the four core regions and integrate multi-modal sources.</span><span class="lang-zh-content">团队在四个核心区域进行实地全景采集，并整合多模态数据。</span></p>
            </div>
          </div>
          <div class="tour-timeline__item">
            <div class="tour-timeline__date">2025</div>
            <div class="tour-timeline__body">
              <h3><span class="lang-en-content">System Development</span><span class="lang-zh-content">系统研发</span></h3>
              <p><span class="lang-en-content">Hierarchical panorama data management, hot-region mapping, and viewer engine developed.</span><span class="lang-zh-content">研发分级分区的全景数据管理体系、热点区域映射和漫游引擎。</span></p>
            </div>
          </div>
          <div class="tour-timeline__item">
            <div class="tour-timeline__date">2026</div>
            <div class="tour-timeline__body">
              <h3><span class="lang-en-content">Public Release</span><span class="lang-zh-content">面向公众开放</span></h3>
              <p><span class="lang-en-content">The immersive Imperial Axis Panorama Tour launches to the public — fully reproducible across other heritage sites.</span><span class="lang-zh-content">中轴线全景漫游面向公众开放，成果可复制、可推广到其他文化遗产地。</span></p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA 收尾 -->
    <section class="tour-block tour-cta">
      <div class="tour-container">
        <h2 class="tour-block__title tour-block__title--center">
          <span class="lang-en-content">Step Inside the Palace</span>
          <span class="lang-zh-content">走进紫禁城</span>
        </h2>
        <p>
          <span class="lang-en-content">Click any building on the 3D Map to open the official Palace Museum 360° panorama — no registration, no plugin, just your browser.</span>
          <span class="lang-zh-content">在 3D 地图中点击任意建筑，即可打开故宫博物院官方 360° 全景——无需注册、无需插件，仅需浏览器。</span>
        </p>
        <a href="#" id="tourBackToMap" class="tour-hero__cta">
          <span class="lang-en-content">← Back to 3D Map</span>
          <span class="lang-zh-content">← 返回 3D 地图</span>
        </a>
      </div>
    </section>

  </div>


  <!-- =========================================
       СКРИПТЫ: ВКЛАДКИ, ЯЗЫКИ, ЧАТ
       ========================================= -->
  <script src="./js/bootstrap.bundle.min.js"></script>
  <!-- <script src="./js/blocs.min.js"></script> --> <!-- 禁用老 blocs，干扰 Visit Info 切换 -->
  
  <script>
    // --- ПЕРЕКЛЮЧЕНИЕ ВКЛАДОК ---
    const tabs = document.querySelectorAll('.nav-tab');
    const sections = document.querySelectorAll('.app-section');
    const langBtns = document.querySelectorAll('.gl-btn');

    tabs.forEach(tab => {
      tab.addEventListener('click', () => {
        tabs.forEach(t => t.classList.remove('active'));
        tab.classList.add('active');
        
        const targetId = tab.getAttribute('data-target');
        sections.forEach(sec => {
          sec.classList.remove('active');
          // 清除任何可能残留的 inline style（防止 Visit Info handler 留下的 hack）
          sec.removeAttribute('style');
          if (sec.id === targetId) sec.classList.add('active');
        });

        if (document.getElementById(targetId).classList.contains('fullscreen-section')) {
          document.body.classList.add('mode-fullscreen');
        } else {
          document.body.classList.remove('mode-fullscreen');
        }

        // Принудительно ресайзим графики
        if (targetId === 'section-dashboard') {
          setTimeout(() => { window.dispatchEvent(new Event('resize')); }, 100);
        }
      });
    });

    // --- ПЕРЕКЛЮЧЕНИЕ ЯЗЫКОВ ---
    function switchGlobalLanguage(lang) {
      langBtns.forEach(btn => btn.classList.toggle('active', btn.getAttribute('data-lang') === lang));
      document.body.classList.remove('lang-zh', 'lang-en');
      document.body.classList.add('lang-' + lang);
      document.documentElement.lang = lang === 'zh' ? 'zh-CN' : 'en-US';
      localStorage.setItem('preferredLanguage', lang);
      
      if (typeof window.updateChartsLanguage === 'function') window.updateChartsLanguage(lang);
      if (typeof window.fcApplyLang === 'function') window.fcApplyLang();
      if (typeof window.updateGameLanguage === 'function') window.updateGameLanguage(lang);
      if (typeof window.updateMapLanguage === 'function') window.updateMapLanguage(lang);
    }

    langBtns.forEach(btn => btn.addEventListener('click', () => switchGlobalLanguage(btn.getAttribute('data-lang'))));
    const savedLang = localStorage.getItem('preferredLanguage') || 'en';
    switchGlobalLanguage(savedLang);

    // --- ЧАТ ---
    (function(){
      const i18n = {
        en: { title: "Forbidden City Assistant", hello: "Welcome! If your question is not in the buttons, type your message in chat and support will reply to your email.", hint: "Type any custom question. Support will reply to your email.", inputPh: "Type your message...", sent: "✅ Notification sent. Support will contact you by email.", quick: { hours: "Opening hours", tickets: "Tickets", route: "How to get there", history: "Short history", other: "Other question" }, answers: { hours: "The museum usually works daytime; Monday is often closed. Please check the official schedule before visiting.", tickets: "Tickets are usually bought online in advance. In high season slots sell out quickly.", route: "The Forbidden City is in central Beijing near Tiananmen. Metro + short walk is the easiest way.", history: "The Forbidden City was the imperial palace of Ming and Qing dynasties and is now the Palace Museum." } },
        zh: { title: "紫禁城助手", hello: "欢迎！若您的问题不在快捷按钮中，请直接在聊天里输入，我们将通过邮箱回复您。", hint: "如果没有对应按钮问题，请直接输入消息，我们会通过邮箱回复。", inputPh: "请输入您的消息...", sent: "✅ 通知已发送，我们将通过邮箱联系您。", quick: { hours: "开放时间", tickets: "门票", route: "如何到达", history: "简要历史", other: "其他问题" }, answers: { hours: "故宫通常在白天开放，周一常闭馆。出行前请查看官方最新开放时间。", tickets: "门票通常需提前线上预约，旺季名额很快满。", route: "故宫位于北京市中心，靠近天安门，通常地铁加步行最方便。", history: "紫禁城是明清两代皇宫，现为故宫博物院。" } }
      };

      const panel = document.getElementById("fc-chat-panel");
      const toggle = document.getElementById("fc-chat-toggle");
      const closeBtn = document.getElementById("fc-chat-close");
      const messages = document.getElementById("fc-chat-messages");
      const quick = document.getElementById("fc-quick");
      const title = document.getElementById("fc-title");
      const textInput = document.getElementById("fc-chat-input");
      const sendBtn = document.getElementById("fc-send");

      function getLang(){ return document.body.classList.contains('lang-zh') ? 'zh' : 'en'; }
      function addMsg(text, role="bot", html=false){
        const row = document.createElement("div"); row.className = "fc-msg " + role;
        const b = document.createElement("div"); b.className = "fc-bubble";
        html ? b.innerHTML = text : b.textContent = text;
        row.appendChild(b); messages.appendChild(row);
        messages.scrollTop = messages.scrollHeight; return row;
      }

      function applyLang(){
        const t = i18n[getLang()];
        if(title) title.textContent = t.title;
        if(textInput) textInput.placeholder = t.inputPh;
        if (messages && !messages.dataset.started){ addMsg(t.hello, "bot"); messages.dataset.started = "1"; }
        if(quick) {
          quick.innerHTML = "";
          [["hours",t.quick.hours],["tickets",t.quick.tickets],["route",t.quick.route],["history",t.quick.history],["other",t.quick.other]].forEach(([k,v])=>{
            const btn = document.createElement("button"); btn.className = "fc-qbtn" + (k === "other" ? " full" : ""); btn.textContent = v;
            btn.onclick = () => {
              addMsg(v, "user");
              setTimeout(() => { if (k === "other") addMsg(t.hint, "bot"); else addMsg(t.answers[k], "bot"); }, 600);
            };
            quick.appendChild(btn);
          });
        }
      }
      
      if(toggle) toggle.onclick = () => { panel.classList.add("open"); toggle.style.display="none"; applyLang(); };
      if(closeBtn) closeBtn.onclick = () => { panel.classList.remove("open"); toggle.style.display="flex"; };
      window.fcApplyLang = applyLang;

      // ОБРАБОТКА ОТПРАВКИ СВОЕГО СООБЩЕНИЯ
      function handleSend() {
        const msg = textInput.value.trim();
        if (!msg) return; // Не отправляем пустые
        addMsg(msg, "user"); // Выводим вопрос юзера
        textInput.value = ""; // Очищаем инпут
        
        const t = i18n[getLang()];
        setTimeout(() => {
          addMsg(t.sent, "bot"); // Выводим ответ об успешной отправке
        }, 800);
      }
      
      if(sendBtn) sendBtn.onclick = handleSend;
      if(textInput) textInput.addEventListener("keypress", function(e) {
        if (e.key === "Enter") handleSend(); // Отправка по кнопке Enter
      });
    })();
  </script>

  <!-- =========================================
       СКРИПТЫ: ДАШБОРД ГРАФИКИ (ECHART)
       ========================================= -->
  <script>
  (() => {
    const yearData = <?php echo $yearDataJson; ?>;
    const years = <?php echo $yearsJson; ?>;
    const totals = <?php echo $totalsJson; ?>;
    const monthsChinese = <?php echo $monthsChineseJson; ?>;
    const monthsEnglish = <?php echo $monthsEnglishJson; ?>;
    const bNamesEn = <?php echo $buildingNamesEnJson; ?>;
    const bNamesZh = <?php echo $buildingNamesChJson; ?>;
    const bCounts = <?php echo $buildingCountsJson; ?>;
    const lNamesEn = <?php echo $landmarkNamesEnJson; ?>;
    const lNamesZh = <?php echo $landmarkNamesChJson; ?>;
    const lVisits = <?php echo $landmarkVisitorsJson; ?>;
    // ===== 升级版数据面板 JS（浅色主题 + 联动 + TOP5）=====
    const goldColor = '#d4af37';
    const goldBright = '#e8c860';
    const lightText = '#2a1a1a';
    const lightSplit = 'rgba(84,27,30,0.08)';
    const colors = ['#541b1e', '#d4af37', '#8b2323', '#f4d03f', '#a71930', '#c9a227', '#d62828', '#b8860b', '#bc3f4a', '#daa520', '#cc2936', '#c41e3a'];

    let bChart = echarts.init(document.getElementById('barChart'));
    let pChart = echarts.init(document.getElementById('pieChart'));
    let bdChart = echarts.init(document.getElementById('buildingChart'));
    let lChart = echarts.init(document.getElementById('landmarkChart'));
    let currentYearIdx = 0;
    let animTriggered = false;

    function animateValue(el, target, duration) {
      if (!el) return;
      duration = duration || 1200;
      const startTs = performance.now();
      const locale = document.body.classList.contains('lang-zh') ? 'zh-CN' : 'en-US';
      function tick(now) {
        const t = Math.min(1, (now - startTs) / duration);
        const eased = 1 - Math.pow(1 - t, 3);
        const v = Math.round(target * eased);
        el.textContent = v.toLocaleString(locale);
        if (t < 1) requestAnimationFrame(tick);
        else el.textContent = target.toLocaleString(locale);
      }
      requestAnimationFrame(tick);
    }

    function animateStatValues() {
      document.querySelectorAll('#section-dashboard .stat-value[data-count]').forEach(el => {
        const target = parseInt(el.dataset.count, 10);
        if (!isNaN(target)) animateValue(el, target, 1200);
      });
    }

    function lightAxis() {
      return {
        axisLine: { lineStyle: { color: 'rgba(84,27,30,0.3)' } },
        axisLabel: { color: lightText, fontSize: 11 },
        splitLine: { lineStyle: { color: lightSplit, type: 'dashed' } }
      };
    }
    function lightTooltip() {
      return {
        backgroundColor: 'rgba(255,255,255,0.98)',
        borderColor: '#d4af37',
        borderWidth: 1,
        textStyle: { color: lightText, fontSize: 12 },
        extraCssText: 'box-shadow: 0 4px 16px rgba(84,27,30,0.15);'
      };
    }

    // 找峰值年 idx
    function getPeakYearIdx() {
      let max = -1, idx = 0;
      totals.forEach((v, i) => { if (v > max) { max = v; idx = i; } });
      return idx;
    }

    // 生成数据摘要
    function renderInsight(isZh) {
      const el = document.getElementById('insightText');
      if (!el) return;
      const peakIdx = getPeakYearIdx();
      const peakYear = years[peakIdx];
      const peakVal = totals[peakIdx];
      // 当年数据
      const monthly = (yearData[currentYearIdx] && yearData[currentYearIdx].monthly) || [];
      let topMonthIdx = 0;
      monthly.forEach((v, i) => { if (v > monthly[topMonthIdx]) topMonthIdx = i; });
      const monthsArr = isZh ? monthsChinese : monthsEnglish;
      const trend = currentYearIdx > 0 && totals[currentYearIdx - 1] > 0
        ? ((totals[currentYearIdx] - totals[currentYearIdx - 1]) / totals[currentYearIdx - 1] * 100).toFixed(1)
        : null;

      // TOP 建筑
      const topBuildingIdx = bCounts.indexOf(Math.max(...bCounts));
      const topBuildingName = isZh ? bNamesZh[topBuildingIdx] : bNamesEn[topBuildingIdx];

      let html = '';
      if (isZh) {
        html = `<strong>${years[currentYearIdx]} 年</strong>访问量 <strong>${totals[currentYearIdx].toLocaleString('zh-CN')}</strong>，` +
                `高峰月为 <strong>${monthsArr[topMonthIdx]}</strong>，` +
                `较去年${trend !== null ? (trend >= 0 ? '<strong style="color:#a8d4b8">增长 ' + trend + '%</strong>' : '<strong style="color:#e8c860">下降 ' + Math.abs(trend) + '%</strong>') : '无对比'}。` +
                `历史峰值出现在 <strong>${peakYear} 年</strong>（${peakVal.toLocaleString('zh-CN')}），` +
                `库存最多建筑为 <strong>${topBuildingName}</strong>。`;
      } else {
        html = `<strong>${years[currentYearIdx]}</strong> recorded <strong>${totals[currentYearIdx].toLocaleString('en-US')}</strong> visits, ` +
                `peaking in <strong>${monthsArr[topMonthIdx]}</strong>, ` +
                `${trend !== null ? (trend >= 0 ? '<strong style="color:#a8d4b8">+' + trend + '%</strong>' : '<strong style="color:#e8c860">' + trend + '%</strong>') : 'no prior year'} year-over-year. ` +
                `All-time peak: <strong>${peakYear}</strong> (${peakVal.toLocaleString('en-US')}). ` +
                `Most inventoried: <strong>${topBuildingName}</strong>.`;
      }
      el.innerHTML = html;
    }

    // 渲染 TOP5
    function renderTop5(isZh) {
      const el = document.getElementById('top5List');
      if (!el) return;
      // 按访问量降序取前5
      const indexed = lVisits.map((v, i) => ({ name: isZh ? lNamesZh[i] : lNamesEn[i], visits: v, idx: i }));
      indexed.sort((a, b) => b.visits - a.visits);
      const top5 = indexed.slice(0, 5);
      const max = top5[0].visits;
      const locale = isZh ? 'zh-CN' : 'en-US';
      el.innerHTML = top5.map((item, i) => {
        const pct = (item.visits / max * 100).toFixed(1);
        const rankClass = i < 3 ? 'rank rank-' + (i + 1) : 'rank';
        return `<div class="top5-row">
          <div class="${rankClass}">${i + 1}</div>
          <div class="info">
            <div class="name">${item.name}</div>
            <div class="bar-track"><div class="bar-fill" style="width: ${pct}%"></div></div>
          </div>
          <div class="visits">${item.visits.toLocaleString(locale)}</div>
        </div>`;
      }).join('');
    }

    function renderCharts(lang) {
      if (!years || years.length === 0) return;
      const isZh = lang === 'zh';
      const locale = isZh ? 'zh-CN' : 'en-US';

      // 更新顶部卡片"选中年份"
      const yLabel = document.getElementById('selected-year-label');
      const yValue = document.getElementById('selected-year-value');
      if (yLabel) yLabel.innerHTML = isZh
        ? `<span class="lang-zh-content">${years[currentYearIdx]}年访问量</span>`
        : `<span class="lang-en-content">${years[currentYearIdx]} Visits</span>`;
      if (yValue) {
        yValue.dataset.count = totals[currentYearIdx] || 0;
        animateValue(yValue, totals[currentYearIdx] || 0, 900);
      }

      // 同步年份选择器
      const sel = document.getElementById('yearSelect');
      if (sel && parseInt(sel.value) !== currentYearIdx) sel.value = currentYearIdx;

      // ===== Bar Chart：按年柱状图 + 平均线 + 最高点标注 =====
      const avgLine = Math.round(totals.reduce((a, b) => a + b, 0) / totals.length);
      const peakIdx = getPeakYearIdx();
      const peakYear = years[peakIdx];
      const peakVal = totals[peakIdx];
      bChart.setOption({
        tooltip: Object.assign({ trigger: 'axis', formatter: (p) => {
          const v = p[0];
          return `<strong>${v.name} ${isZh ? '年' : ''}</strong><br/>${v.value.toLocaleString(locale)} ${isZh ? '访问' : 'visits'}`;
        } }, lightTooltip()),
        grid: { left: 50, right: 30, top: 40, bottom: 50 },
        xAxis: Object.assign({ type: 'category', data: years, axisLabel: { color: lightText, rotate: 45, fontSize: 10 } }, lightAxis()),
        yAxis: Object.assign({ type: 'value', axisLabel: { color: lightText, formatter: (val) => (val / 1000000).toFixed(1) + 'M' } }, lightAxis()),
        series: [{
          type: 'bar',
          data: totals.map((v, i) => ({
            value: v,
            itemStyle: i === currentYearIdx
              ? { color: { type: 'linear', x: 0, y: 0, x2: 0, y2: 1, colorStops: [{ offset: 0, color: '#ffd700' }, { offset: 1, color: '#d4af37' }] }, borderColor: '#fff', borderWidth: 2 }
              : { color: { type: 'linear', x: 0, y: 0, x2: 0, y2: 1, colorStops: [{ offset: 0, color: '#d4af37' }, { offset: 1, color: '#8b2323' }] }, borderRadius: [4, 4, 0, 0] }
          })),
          markLine: {
            symbol: ['none', 'none'],
            lineStyle: { color: '#e8c860', type: 'dashed', width: 1.5 },
            label: { color: '#e8c860', fontSize: 11, formatter: isZh ? `均值 ${(avgLine/1000000).toFixed(1)}M` : `Avg ${(avgLine/1000000).toFixed(1)}M` },
            data: [{ yAxis: avgLine }]
          },
          markPoint: {
            symbol: 'pin',
            symbolSize: 60,
            itemStyle: { color: '#ff6b35' },
            label: { color: '#fff', fontSize: 10, fontWeight: 700 },
            data: [{ name: isZh ? '峰值' : 'Peak', coord: [peakYear, peakVal], value: peakVal.toLocaleString(locale) }]
          }
        }]
      });

      // ===== Pie Chart：环形图 + 中心显示当年总访问 =====
      const months = isZh ? monthsChinese : monthsEnglish;
      const pieData = (yearData[currentYearIdx] && yearData[currentYearIdx].monthly)
        ? yearData[currentYearIdx].monthly.map((v, i) => ({ value: v, name: months[i] })).filter(d => d.value > 0)
        : [];
      const yearTotal = totals[currentYearIdx] || 0;
      pChart.setOption({
        tooltip: Object.assign({ trigger: 'item', formatter: (p) => `${p.name}<br/><strong>${p.value.toLocaleString(locale)}</strong> (${p.percent}%)` }, lightTooltip()),
        legend: { bottom: 0, textStyle: { color: lightText, fontSize: 10 }, itemWidth: 10, itemHeight: 10 },
        series: [{
          name: isZh ? '访问量' : 'Visits',
          type: 'pie',
          radius: ['45%', '72%'],
          center: ['50%', '42%'],
          avoidLabelOverlap: true,
          itemStyle: { borderRadius: 4, borderColor: '#fff', borderWidth: 2 },
          label: { color: lightText, fontSize: 10, formatter: '{b}\n{d}%' },
          labelLine: { lineStyle: { color: 'rgba(212,175,55,0.3)' } },
          data: pieData,
          color: colors
        }],
        graphic: [
          { type: 'text', left: 'center', top: '36%', style: { text: yearTotal.toLocaleString(locale), fill: '#8b2323', fontSize: 22, fontWeight: 900, textAlign: 'center' } },
          { type: 'text', left: 'center', top: '46%', style: { text: isZh ? '总访问' : 'TOTAL', fill: 'rgba(84,27,30,0.6)', fontSize: 10, letterSpacing: 3, textAlign: 'center' } }
        ]
      });

      // ===== Building Chart：横向 + 颜色梯度 =====
      const bMax = Math.max(...bCounts);
      const bMin = Math.min(...bCounts);
      bdChart.setOption({
        tooltip: Object.assign({ trigger: 'axis', axisPointer: { type: 'shadow' } }, lightTooltip()),
        grid: { left: 120, right: 30, top: 20, bottom: 30 },
        xAxis: Object.assign({ type: 'value', axisLabel: { color: lightText, fontSize: 10 } }, lightAxis()),
        yAxis: Object.assign({ type: 'category', data: isZh ? bNamesZh : bNamesEn, axisLabel: { color: lightText, fontSize: 11 } }, lightAxis()),
        series: [{
          type: 'bar',
          data: bCounts.map((v) => ({
            value: v,
            itemStyle: {
              color: { type: 'linear', x: 0, y: 0, x2: 1, y2: 0, colorStops: [
                { offset: 0, color: `hsl(${45 - (v - bMin) / (bMax - bMin) * 45}, 70%, 35%)` },
                { offset: 1, color: `hsl(${45 - (v - bMin) / (bMax - bMin) * 45}, 80%, 55%)` }
              ] },
              borderRadius: [0, 4, 4, 0]
            }
          })),
          barWidth: '60%'
        }]
      });

      // ===== Landmark Chart：玫瑰饼图 =====
      const lmData = (isZh ? lNamesZh : lNamesEn).map((n, i) => ({ name: n, value: lVisits[i] }));
      lChart.setOption({
        tooltip: Object.assign({ trigger: 'item', formatter: (p) => `${p.name}<br/><strong>${p.value.toLocaleString(locale)}</strong> (${p.percent}%)` }, lightTooltip()),
        legend: { orient: 'vertical', left: 'left', top: 'middle', textStyle: { color: lightText, fontSize: 11 } },
        series: [{
          name: isZh ? '访问量' : 'Visitors',
          type: 'pie',
          radius: ['25%', '75%'],
          center: ['65%', '50%'],
          roseType: 'area',
          itemStyle: { borderRadius: 4, borderColor: '#fff', borderWidth: 2 },
          label: { color: lightText, fontSize: 10 },
          labelLine: { lineStyle: { color: 'rgba(212,175,55,0.3)' } },
          data: lmData,
          color: colors
        }]
      });

      // 摘要 + TOP5
      renderInsight(isZh);
      renderTop5(isZh);
    }

    // 点击柱状图切换年份
    bChart.on('click', (p) => {
      currentYearIdx = p.dataIndex;
      renderCharts(document.body.classList.contains('lang-zh') ? 'zh' : 'en');
    });

    // 年份选择器联动
    const yearSelect = document.getElementById('yearSelect');
    if (yearSelect) {
      yearSelect.addEventListener('change', (e) => {
        currentYearIdx = parseInt(e.target.value, 10);
        renderCharts(document.body.classList.contains('lang-zh') ? 'zh' : 'en');
      });
    }
    const goPeakBtn = document.getElementById('goPeakBtn');
    if (goPeakBtn) {
      goPeakBtn.addEventListener('click', () => {
        currentYearIdx = getPeakYearIdx();
        renderCharts(document.body.classList.contains('lang-zh') ? 'zh' : 'en');
      });
    }
    const goFirstBtn = document.getElementById('goFirstBtn');
    if (goFirstBtn) {
      goFirstBtn.addEventListener('click', () => {
        currentYearIdx = 0;
        renderCharts(document.body.classList.contains('lang-zh') ? 'zh' : 'en');
      });
    }

    window.updateChartsLanguage = renderCharts;

    // 初次渲染
    renderCharts(document.body.classList.contains('lang-zh') ? 'zh' : 'en');

    // 当 dashboard 切到 active 时触发数字动画
    document.querySelectorAll('.nav-tab').forEach(btn => {
      btn.addEventListener('click', (e) => {
        const targetId = e.currentTarget.getAttribute('data-target');
        if (targetId === 'section-dashboard' && !animTriggered) {
          animTriggered = true;
          setTimeout(animateStatValues, 200);
        }
      });
    });

    // ===== Visit Info 单独保险 handler =====
    // 用 setTimeout 在下一个 tick 主动给 section-visit 加 active（即使主 nav handler 出了问题也能保证显示）
    document.querySelectorAll('.nav-tab').forEach(function(btn) {
      btn.addEventListener('click', function() {
        if (btn.getAttribute('data-target') === 'section-visit') {
          console.log('[VisitInfo] clicked');
          // 终极保险：手动重写所有相关属性
          document.querySelectorAll('.app-section').forEach(function(s) {
            s.classList.remove('active');
            s.style.display = '';
            s.style.visibility = '';
            s.style.opacity = '';
          });
          document.querySelectorAll('.nav-tab').forEach(function(t) { t.classList.remove('active'); });
          // 【关键】移除 body.mode-fullscreen！否则 body 被锁在 100vh，section 被裁剪
          document.body.classList.remove('mode-fullscreen');
          document.body.style.overflow = '';
          document.body.style.height = '';
          var sec = document.getElementById('section-visit');
          if (sec) {
            sec.classList.add('active');
            sec.setAttribute('style', 'display: block !important; position: relative !important; visibility: visible !important; opacity: 1 !important; z-index: 100 !important; min-height: 100vh !important;');
            btn.classList.add('active');
            console.log('[VisitInfo] forced display:', getComputedStyle(sec).display, 'body class:', document.body.className);
          }
        }
      });
    });

    document.querySelectorAll('.fc-visit__tab').forEach(function(tab) {
      tab.addEventListener('click', function() {
        var gate = this.getAttribute('data-gate');
        document.querySelectorAll('.fc-visit__tab').forEach(function(t) { t.classList.remove('active'); });
        document.querySelectorAll('.fc-visit__pane').forEach(function(p) { p.classList.remove('active'); });
        this.classList.add('active');
        var pane = document.querySelector('.fc-visit__pane[data-gate="' + gate + '"]');
        if (pane) pane.classList.add('active');
      });
    });

    window.addEventListener('resize', () => {
      if (document.getElementById('section-dashboard').classList.contains('active')) {
        bChart.resize(); pChart.resize(); bdChart.resize(); lChart.resize();
      }
    });
  })();
  </script>
  <!-- =========================================
       СКРИПТЫ: МИНИ ИГРА (INTTEST)
       ========================================= -->
  <script>
  (() => {
    const buildings = [
      { id: 1, title_en: "Meridian Gate", title_zh: "午门", desc_en: "The southern entrance to the Forbidden City, featuring five arches.", desc_zh: "紫禁城的南正门，设有五个拱门和独特的U形结构。" },
      { id: 2, title_en: "Gate of Divine Prowess", title_zh: "神武门", desc_en: "The main northern gate.", desc_zh: "主要的北门，最初名为玄武门，后避讳改名。" },
      { id: 3, title_en: "West Flowery Gate", title_zh: "西华门", desc_en: "A western side gate leading to the Outer Court.", desc_zh: "通往外朝的西侧门，以复杂的图案装饰闻名。" },
      { id: 4, title_en: "East Flowery Gate", title_zh: "东华门", desc_en: "The eastern counterpart to the West Flowery Gate.", desc_zh: "东侧门，是通往皇家工坊和宫殿东区的通道。" },
      { id: 5, title_en: "Gate of Supreme Harmony", title_zh: "太和门", desc_en: "The most important gate in the Outer Court.", desc_zh: "外朝最重要的门，由铜狮守卫，通向太和殿。" },
      { id: 6, title_en: "Hall of Supreme Harmony", title_zh: "太和殿", desc_en: "The largest structure, used for enthronement ceremonies.", desc_zh: "紫禁城内最大的建筑，用于举行登基大典等重大国家典礼。" },
      { id: 7, title_en: "Hall of Central Harmony", title_zh: "中和殿", desc_en: "A square-shaped hall where the emperor rested.", desc_zh: "方形大殿，皇帝在主持太和殿典礼前在此休息准备。" },
      { id: 8, title_en: "Hall of Preserved Harmony", title_zh: "保和殿", desc_en: "Used for imperial banquets and highest level examinations.", desc_zh: "用于举办皇家宴会，后来成为科举殿试的场所。" },
      { id: 9, title_en: "Gate of Heavenly Purity", title_zh: "乾清门", desc_en: "The main gate separating the Outer Court from the Inner Court.", desc_zh: "分隔外朝（国事）与内廷（居住区）的正门。" },
      { id: 10, title_en: "Tower of Enchanced Righteousness", title_zh: "弘义阁", desc_en: "One of the four corner towers guarding the Outer Court.", desc_zh: "守卫外朝的四座角楼之一，以复杂优雅的多重屋檐结构闻名。" },
      { id: 11, title_en: "Tower of State Benevolence", title_zh: "体仁阁", desc_en: "A symmetrical counterpart forming part of the defensive ensemble.", desc_zh: "与弘义阁对称的建筑，是外朝防御和建筑群的一部分。" },
      { id: 12, title_en: "Hall of Military Prowess", title_zh: "武英殿", desc_en: "A western hall used for military affairs.", desc_zh: "西部大殿，用于军事事务、皇家武举和存放武器。" },
      { id: 13, title_en: "Hall of Literary Glory", title_zh: "文华殿", desc_en: "An eastern hall dedicated to literature.", desc_zh: "东部大殿，致力于文学，作为图书馆和编纂典籍的地方。" },
      { id: 14, title_en: "Hall of Heavenly Purity", title_zh: "乾清宫", desc_en: "The main residence of early Ming and Qing emperors.", desc_zh: "明初和清初皇帝的主要居所，后作为内廷的接见大殿。" },
      { id: 15, title_en: "Hall of Union and Peace", title_zh: "交泰殿", desc_en: "A smaller hall symbolizing the union of emperor and empress.", desc_zh: "象征帝后结合的较小殿堂，存放着皇帝的玉玺。" },
      { id: 16, title_en: "Hall of Earthly Tranquility", title_zh: "坤宁宫", desc_en: "The primary residence of the empress.", desc_zh: "皇后的主要居所，也是清代满族萨满教仪式的中心场所。" },
      { id: 17, title_en: "Imperial Garden", title_zh: "御花园", desc_en: "A classical garden behind the Inner Court.", desc_zh: "内廷后方的古典园林，内有古柏、假山和供皇帝休憩的亭台。" },
      { id: 18, title_en: "Garden of Benevolent Peace", title_zh: "慈宁宫花园", desc_en: "A tranquil garden used for quiet contemplation.", desc_zh: "内廷西部的宁静花园，用于静思和宗教活动。" },
      { id: 19, title_en: "Palace of Benevolent Peace", title_zh: "慈宁宫", desc_en: "A western palace complex for the empress dowager.", desc_zh: "西部宫殿建筑群，通常与太后的居所和资深妃嫔的养老地相关。" },
      { id: 20, title_en: "Imperial Kitchen", title_zh: "御茶膳房", desc_en: "A complex dedicated to preparing meals.", desc_zh: "专门为皇帝、皇室家族及宫廷人员准备膳食的大型建筑群。" },
      { id: 21, title_en: "Southern Three Halls", title_zh: "南三所", desc_en: "A complex where imperial princes were educated.", desc_zh: "皇子接受儒家经典、书法和治国理政教育的建筑群。" },
      { id: 22, title_en: "Nine Dragon Screen", title_zh: "九龙壁", desc_en: "A magnificent glazed tile screen with nine coiled dragons.", desc_zh: "宏伟的琉璃瓦影壁，绘有九条盘龙，用于精神防护和装饰。" },
      { id: 23, title_en: "Hall of Mental Cultivation", title_zh: "养心殿", desc_en: "The de facto residence and administrative center.", desc_zh: "18世纪起清代皇帝的实际居所和行政中心，在此处理国事。" },
      { id: 24, title_en: "Shufang Lodge", title_zh: "漱芳斋", desc_en: "A studio for calligraphy and painting.", desc_zh: "书画工作室，作为学术追求和艺术欣赏的静谧退隐之地。" },
      { id: 25, title_en: "Palace of Longevity and Peace", title_zh: "寿康宫", desc_en: "A residential palace in the eastern part of the Inner Court.", desc_zh: "内廷东部的居住宫殿，常与皇后及妃嫔的住所相关联。" },
      { id: 26, title_en: "Hall of Braveness and Splendor", title_zh: "武成殿", desc_en: "A hall dedicated to military training and displaying armor.", desc_zh: "致力于军事训练和展示盔甲的殿堂，反映了满族对武艺的重视。" },
      { id: 27, title_en: "Hall of Double Brilliance", title_zh: "重华宫", desc_en: "A ceremonial hall in the eastern section.", desc_zh: "东部的仪式大殿，用于各种礼仪和皇室宗亲的聚会场所。" },
      { id: 28, title_en: "Hall of Honesty and Respect", title_zh: "咸福宫", desc_en: "One of the Six Western Palaces.", desc_zh: "西六宫之一，作为内廷妃嫔的住所。" },
      { id: 29, title_en: "Palace of Great Benevolence", title_zh: "景仁宫", desc_en: "A major western palace.", desc_zh: "明清时期主要西部宫殿，常为权势皇后和有影响力妃嫔的居所。" },
      { id: 30, title_en: "Palace of Bearing Heaven", title_zh: "承乾宫", desc_en: "A residential palace in the western section.", desc_zh: "西部的居住宫殿，是西六宫建筑群的一部分。" },
      { id: 31, title_en: "Palace of Gathering Essence", title_zh: "钟粹宫", desc_en: "A western palace known for its refined architecture.", desc_zh: "以精美建筑闻名的西部宫殿，作为受宠妃嫔的居所。" },
      { id: 32, title_en: "Hall for Ancestry Worship", title_zh: "奉先殿", desc_en: "The Imperial Ancestral Temple complex.", desc_zh: "皇室祖庙建筑群，用于举行庄严的祭祖仪式。" },
      { id: 33, title_en: "Palace of Prolonged Happiness", title_zh: "延禧宫", desc_en: "A residence in the eastern section of the Inner Court.", desc_zh: "内廷东部的住所，是东六宫建筑群的一部分。" },
      { id: 34, title_en: "Palace of Eternal Harmony", title_zh: "永和宫", desc_en: "A prominent eastern palace.", desc_zh: "重要的东部宫殿，作为妃嫔住所，有时也用于礼仪目的。" },
      { id: 35, title_en: "Palace of Sunlight", title_zh: "景阳宫", desc_en: "An eastern palace residence known for bright courtyards.", desc_zh: "东部的居住宫殿，属于东六宫，以其明亮宽敞的���院而闻名。" },
      { id: 36, title_en: "North Five Halls", title_zh: "北五所", desc_en: "A cluster of five halls in the northern part.", desc_zh: "内廷北部由五座殿堂组成的建筑群，用于多种行政和居住目的。" },
      { id: 37, title_en: "Imperial Study", title_zh: "南书房", desc_en: "The emperor's private library and workspace.", desc_zh: "皇帝的私人图书馆和工作场所，在此批阅奏章、研究国事。" },
      { id: 38, title_en: "Gate of Imperial Supremacy", title_zh: "皇极门", desc_en: "The entrance gate to a section used by retired emperors.", desc_zh: "皇极殿建筑群的入口，通往太上皇使用的区域。" },
      { id: 39, title_en: "Hall of Imperial Supremacy", title_zh: "皇极殿", desc_en: "A large hall serving as the residence for retired emperors.", desc_zh: "东北部的大型殿堂，在清代作为太上皇的居所。" },
      { id: 40, title_en: "Hall of Character Cultivation", title_zh: "宁寿宫", desc_en: "A hall used for leisure by retired emperors.", desc_zh: "皇极殿建筑群内的殿堂，供太上皇休闲、学习和进行宗教活动使用。" }
    ];

    const namesList = document.getElementById("namesList");
    const descsList = document.getElementById("descsList");
    const scoreText = document.getElementById("scoreText");
    const checkBtn = document.getElementById("checkBtn");
    const resetBtn = document.getElementById("resetBtn");
    const clearSelectedBtn = document.getElementById("clearSelectedBtn");
    const overlay = document.getElementById("game-overlay");
    const closeOverlayBtn = document.getElementById("closeOverlayBtn");
    const resultTitle = document.getElementById("resultTitle");
    const resultGrid = document.getElementById("resultGrid");

    function shorten(s, max=90){ s = (s || "").replace(/\s+/g, " ").trim(); return s.length <= max ? s : s.slice(0, max - 1) + "…"; }
    function shuffle(arr){ for(let i=arr.length-1;i>0;i--){ const j=Math.floor(Math.random()*(i+1)); [arr[i],arr[j]]=[arr[j],arr[i]]; } return arr; }

    function generateSoftPalette(n){
      const arr = [];
      for (let i = 0; i < n; i++){
        const hue = Math.round((360 / n) * i);
        const sat = 62 + (i % 4) * 6; const light = 82 - (i % 3) * 6;
        arr.push({ bg: `hsl(${hue} ${sat}% ${light}%)`, border: `hsl(${hue} ${Math.min(90, sat + 6)}% ${Math.max(45, light - 18)}%)`, ring: `hsl(${hue} ${Math.max(45, sat - 8)}% ${Math.max(58, light - 8)}%)` });
      }
      return arr;
    }
    
    const palette = generateSoftPalette(40);
    const left = buildings.map(x => ({...x}));
    const right = shuffle(buildings.map(x => ({...x, rid: "r_" + x.id + "_" + Math.random().toString(16).slice(2)})));
    const rightRidToId = new Map(right.map(r => [r.rid, r.id]));

    const leftEl = new Map(); const rightEl = new Map(); const links = new Map(); const leftColorIndex = new Map();
    let freeColorIndices = [...Array(40).keys()]; let selectedLeftId = null;

    function takeColor(){ return freeColorIndices.length === 0 ? 0 : freeColorIndices.shift(); }
    function releaseColor(idx){ if (!freeColorIndices.includes(idx)) freeColorIndices.push(idx); freeColorIndices.sort((a,b)=>a-b); }
    function applyPairStyle(el, idx){ const c = palette[idx % palette.length]; el.classList.add("paired"); el.style.setProperty("--pair-bg", c.bg); el.style.setProperty("--pair-border", c.border); el.style.setProperty("--pair-ring", c.ring); }
    function clearPairStyle(el){ el.classList.remove("paired"); el.style.removeProperty("--pair-bg"); el.style.removeProperty("--pair-border"); el.style.removeProperty("--pair-ring"); }

    function buildUI(lang){
      namesList.innerHTML = ""; descsList.innerHTML = "";
      const isZh = lang === 'zh';
      left.forEach(item => {
        const btn = document.createElement("div"); btn.className = "card"; btn.dataset.leftId = item.id;
        btn.innerHTML = `<div style="font-weight:700; font-size:13px; color:#541b1e;">${isZh ? item.title_zh : item.title_en}</div><span class="dot"></span>`;
        btn.addEventListener("click", () => { selectedLeftId = (selectedLeftId === item.id) ? null : item.id; paintSelection(); });
        btn.addEventListener("dblclick", () => { removePairByLeft(item.id); if (selectedLeftId === item.id) selectedLeftId = null; paintSelection(); repaintPairs(); updateScore(); });
        leftEl.set(item.id, btn); namesList.appendChild(btn);
      });

      right.forEach(item => {
        const btn = document.createElement("div"); btn.className = "card"; btn.dataset.rightRid = item.rid;
        btn.innerHTML = `<div><div style="font-weight:700; font-size:13px; color:#541b1e;">${shorten(isZh ? item.desc_zh : item.desc_en, 80)}</div></div><span class="dot"></span>`;
        btn.addEventListener("click", () => {
          if (selectedLeftId === null) return;
          const existingLeft = findLeftByRight(item.rid);
          if (existingLeft === selectedLeftId){ removePairByLeft(selectedLeftId); selectedLeftId = null; paintSelection(); repaintPairs(); updateScore(); return; }
          connectPair(selectedLeftId, item.rid); selectedLeftId = null; paintSelection(); repaintPairs(); updateScore();
        });
        rightEl.set(item.rid, btn); descsList.appendChild(btn);
      });
      paintSelection(); repaintPairs(); updateScore();
      
      // Если мо��алка результатов уже открыта, переводим и её
      if (overlay.classList.contains("show")) {
          renderResults();
      }
    }

    function paintSelection(){ leftEl.forEach((el, id) => el.classList.toggle("is-selected", selectedLeftId === id)); }
    function findLeftByRight(rid){ for (const [lid, rr] of links.entries()) if (rr === rid) return lid; return null; }
    function removePairByLeft(leftId){ if (!links.has(leftId)) return; links.delete(leftId); if (leftColorIndex.has(leftId)){ releaseColor(leftColorIndex.get(leftId)); leftColorIndex.delete(leftId); } }
    function connectPair(leftId, rightRid){ const oldLeft = findLeftByRight(rightRid); if (oldLeft !== null && oldLeft !== leftId) removePairByLeft(oldLeft); links.set(leftId, rightRid); if (!leftColorIndex.has(leftId)) leftColorIndex.set(leftId, takeColor()); }
    function repaintPairs(){ leftEl.forEach(el => clearPairStyle(el)); rightEl.forEach(el => clearPairStyle(el)); for (const [leftId, rightRid] of links.entries()){ const idx = leftColorIndex.get(leftId); if (idx === undefined) continue; if (leftEl.get(leftId)) applyPairStyle(leftEl.get(leftId), idx); if (rightEl.get(rightRid)) applyPairStyle(rightEl.get(rightRid), idx); } }
    function updateScore(){ document.getElementById("scoreText").textContent = document.body.classList.contains('lang-zh') ? `已连接: ${links.size} / 40` : `Connections: ${links.size} / 40`; }

    // Вынесли генерацию результатов в отдельную функцию, чтобы менять язык на лету
    function renderResults() {
      leftEl.forEach(el => el.classList.remove("is-correct","is-wrong")); rightEl.forEach(el => el.classList.remove("is-correct","is-wrong"));
      let correct = 0; resultGrid.innerHTML = "";
      const isZh = document.body.classList.contains('lang-zh');

      left.forEach(l => {
        const rr = links.get(l.id) || null; const chosenId = rr ? rightRidToId.get(rr) : null; const ok = chosenId === l.id;
        if (ok) correct++;
        if (leftEl.get(l.id)) leftEl.get(l.id).classList.add(ok ? "is-correct" : "is-wrong");
        if (rr && rightEl.get(rr)) rightEl.get(rr).classList.add(ok ? "is-correct" : "is-wrong");

        const bTitle = isZh ? l.title_zh : l.title_en;
        const chosenDesc = chosenId ? (isZh ? buildings.find(x=>x.id===chosenId).desc_zh : buildings.find(x=>x.id===chosenId).desc_en) : "—";
        const correctDesc = isZh ? l.desc_zh : l.desc_en;
        
        const item = document.createElement("div"); item.className = "resultItem " + (ok ? "ok" : "bad");
        item.innerHTML = `<div style="font-weight:800; color:#541b1e; margin-bottom:6px;">${bTitle}</div><div><span style="font-weight:bold;">${isZh?'已选:':'Chosen:'}</span> ${shorten(chosenDesc, 60)}</div><div style="margin-top:4px;"><span style="font-weight:bold;">${isZh?'正确:':'Correct:'}</span> ${shorten(correctDesc, 60)}</div><div style="margin-top:6px; font-weight:bold; color:${ok?'#1f8b4c':'#b3261e'}">${ok ? (isZh?'正确':'Correct') : (isZh?'错误':'Wrong')}</div>`;
        resultGrid.appendChild(item);
      });

      const pct = Math.round((correct / 40) * 100);
      resultTitle.textContent = isZh ? `结果: ${correct} / 40 (${pct}%)` : `Results: ${correct} / 40 (${pct}%)`;
      document.getElementById("resultMeta").textContent = isZh ? "格式：名称 → 您选择的描述 → 正确的描述 → 状态" : "Format: Title → Chosen description → Correct description → Status.";
    }

    checkBtn.addEventListener("click", () => {
      renderResults();
      overlay.classList.add("show");
    });

    resetBtn.addEventListener("click", () => { links.clear(); leftColorIndex.clear(); freeColorIndices = [...Array(40).keys()]; selectedLeftId = null; leftEl.forEach(el => el.classList.remove("is-correct","is-wrong")); rightEl.forEach(el => el.classList.remove("is-correct","is-wrong")); paintSelection(); repaintPairs(); updateScore(); });
    clearSelectedBtn.addEventListener("click", () => { if (selectedLeftId === null) return; removePairByLeft(selectedLeftId); selectedLeftId = null; paintSelection(); repaintPairs(); updateScore(); });
    closeOverlayBtn.addEventListener("click", ()=> overlay.classList.remove("show"));
    overlay.addEventListener("click", (e)=>{ if (e.target === overlay) overlay.classList.remove("show"); });

    window.updateGameLanguage = buildUI;
    buildUI(document.body.classList.contains('lang-zh') ? 'zh' : 'en');
  })();
  </script>

  <!-- =========================================
       СКРИПТЫ: 3D КАРТА (3DMAP)
       ========================================= -->
  <script type="module">
    import * as THREE from "three";
    import { OrbitControls } from "three/addons/controls/OrbitControls.js";

    const PLAN_URL = "./assets/plan-top.jpg";
    const PLANE_W = 600.5; const PLANE_H = 836;

    // В этом массиве тексты полностью совпадают с массивом из Игры
    const objects = [
      { id: 1, title_en: "Meridian Gate", title_zh: "午门", desc_en: "The southern entrance to the Forbidden City, featuring five arches.", desc_zh: "紫禁城的南正门，设有五个拱门和独特的U形结构。", type: "u", x: 300, y: 797, outerW: 100, outerD: 80, legT: 20, barT: 22, h: 16, color: 0xc89058 },
      { id: 2, title_en: "Gate of Divine Prowess", title_zh: "神武门", desc_en: "The main northern gate.", desc_zh: "主要的北门，最初名为玄武门，后避讳改名。", type: "rect", x: 300, y: 9, w: 47, d: 22, h: 13, color: 0xd6a162 },
      { id: 3, title_en: "West Flowery Gate", title_zh: "西华门", desc_en: "A western side gate leading to the Outer Court.", desc_zh: "通往外朝的西侧门，以复杂的图案装饰闻名。", type: "rect", x: 12, y: 667, w: 27, d: 40, h: 13, color: 0xd6a162 },
      { id: 4, title_en: "East Flowery Gate", title_zh: "东华门", desc_en: "The eastern counterpart to the West Flowery Gate.", desc_zh: "东侧门，是通往皇家工坊和宫殿东区的通道。", type: "rect", x: 589, y: 665, w: 27, d: 40, h: 13, color: 0xd6a162 },
      { id: 5, title_en: "Gate of Supreme Harmony", title_zh: "太和门", desc_en: "The most important gate in the Outer Court.", desc_zh: "外朝最重要的门，由铜狮守卫，通向太和殿。", type: "rect", x: 300, y: 635, w: 40, d: 30, h: 20, color: 0xd6a162 },
      { id: 6, title_en: "Hall of Supreme Harmony", title_zh: "太和殿", desc_en: "The largest structure, used for enthronement ceremonies.", desc_zh: "紫禁城内最大的建筑，用于举行登基大典等重大国家典礼。", type: "rect", x: 300, y: 465, w: 63, d: 33, h: 24, color: 0xe0b06f },
      { id: 7, title_en: "Hall of Central Harmony", title_zh: "中和殿", desc_en: "A square-shaped hall where the emperor rested.", desc_zh: "方形大殿，皇帝在主持太和殿典礼前在此休息准备。", type: "rect", x: 300, y: 410, w: 15, d: 30, h: 20, color: 0xd9a869 },
      { id: 8, title_en: "Hall of Preserved Harmony", title_zh: "保和殿", desc_en: "Used for imperial banquets and highest level examinations.", desc_zh: "用于举办皇家宴会，后来成为科举殿试的场所。", type: "rect", x: 300, y: 365, w: 57, d: 25, h: 24, color: 0xd19d5f },
      { id: 9, title_en: "Gate of Heavenly Purity", title_zh: "乾清门", desc_en: "The main gate separating the Outer Court from the Inner Court.", desc_zh: "分隔外朝（国事）与内廷（居住区）的正门。", type: "rect", x: 300, y: 290, w: 35, d: 15, h: 18, color: 0xd6a162 },
      { id: 10, title_en: "Tower of Enchanced Righteousness", title_zh: "弘义阁", desc_en: "One of the four corner towers guarding the Outer Court.", desc_zh: "守卫外朝的四座角楼之一，以复杂优雅的多重屋檐结构闻名。", type: "rect", x: 215, y: 549, w: 22, d: 37, h: 13, color: 0xd6a162 },
      { id: 11, title_en: "Tower of State Benevolence", title_zh: "体仁阁", desc_en: "A symmetrical counterpart forming part of the defensive ensemble.", desc_zh: "与弘义阁对称的建筑，是外朝防御和建筑群的一部分。", type: "rect", x: 385, y: 549, w: 22, d: 37, h: 13, color: 0xd6a162 },
      { id: 12, title_en: "Hall of Military Prowess", title_zh: "武英殿", desc_en: "A western hall used for military affairs.", desc_zh: "西部大殿，用于军事事务、皇家武举和存放武器。", type: "rect", x: 134, y: 612, w: 29, d: 38, h: 13, color: 0xd6a162 },
      { id: 13, title_en: "Hall of Literary Glory", title_zh: "文华殿", desc_en: "An eastern hall dedicated to literature.", desc_zh: "东部大殿，致力于文学，作为图书馆和编纂典籍的地方。", type: "rect", x: 445, y: 607, w: 29, d: 38, h: 13, color: 0xd6a162 },
      { id: 14, title_en: "Hall of Heavenly Purity", title_zh: "乾清宫", desc_en: "The main residence of early Ming and Qing emperors.", desc_zh: "明初和清初皇帝的主要居所，后作为内廷的接见大殿。", type: "rect", x: 300, y: 210, w: 40, d: 20, h: 18, color: 0xd6a162 },
      { id: 15, title_en: "Hall of Union and Peace", title_zh: "交泰殿", desc_en: "A smaller hall symbolizing the union of emperor and empress.", desc_zh: "象征帝后结合的较小殿堂，存放着皇帝的玉玺。", type: "rect", x: 300, y: 180, w: 17, d: 17, h: 14, color: 0xd6a162 },
      { id: 16, title_en: "Hall of Earthly Tranquility", title_zh: "坤宁宫", desc_en: "The primary residence of the empress.", desc_zh: "皇后的主要居所，也是清代满族萨满教仪式的中心场所。", type: "rect", x: 300, y: 155, w: 40, d: 15, h: 14, color: 0xd6a162 },
      { id: 17, title_en: "Imperial Garden", title_zh: "御花园", desc_en: "A classical garden behind the Inner Court.", desc_zh: "内廷后方的古典园林，内有古柏、假山和供皇帝休憩的亭台。", type: "rect", x: 300, y: 70, w: 18, d: 18, h: 18, color: 0xd6a162 },
      { id: 18, title_en: "Garden of Benevolent Peace", title_zh: "慈宁宫花园", desc_en: "A tranquil garden used for quiet contemplation.", desc_zh: "内廷西部的宁静花园，用于静思和宗教活动。", type: "rect", x: 100, y: 420, w: 17, d: 17, h: 13, color: 0xd6a162 },
      { id: 19, title_en: "Palace of Benevolent Peace", title_zh: "慈宁宫", desc_en: "A western palace complex for the empress dowager.", desc_zh: "西部宫殿建筑群，通常与太后的居所和资深妃嫔的养老地相关。", type: "rect", x: 127, y: 302, w: 30, d: 20, h: 13, color: 0xd6a162 },
      { id: 20, title_en: "Imperial Kitchen", title_zh: "御茶膳房", desc_en: "A complex dedicated to preparing meals.", desc_zh: "专门为皇帝、皇室家族及宫廷人员准备膳食的大型建筑群。", type: "rect", x: 465, y: 427, w: 27, d: 82, h: 13, color: 0xd6a162 },
      { id: 21, title_en: "Southern Three Halls", title_zh: "南三所", desc_en: "A complex where imperial princes were educated.", desc_zh: "皇子接受儒家经典、书法和治国理政教育的建筑群。", type: "rect", x: 520, y: 417, w: 65, d: 82, h: 13, color: 0xd6a162 },
      { id: 22, title_en: "Nine Dragon Screen", title_zh: "九龙壁", desc_en: "A magnificent glazed tile screen with nine coiled dragons.", desc_zh: "宏伟的琉璃瓦影壁，绘有九条盘龙，用于精神防护和装饰。", type: "rect", x: 528, y: 362, w: 85, d: 17, h: 13, color: 0xd6a162 },
      { id: 23, title_en: "Hall of Mental Cultivation", title_zh: "养心殿", desc_en: "The de facto residence and administrative center.", desc_zh: "18世纪起清代皇帝的实际居所和行政中心，在此处理国事。", type: "rect", x: 215, y: 245, w: 43, d: 22, h: 18, color: 0xd6a162 },
      { id: 24, title_en: "Shufang Lodge", title_zh: "漱芳斋", desc_en: "A studio for calligraphy and painting.", desc_zh: "书画工作室，作为学术追求和艺术欣赏的静谧退隐之地。", type: "rect", x: 230, y: 70, w: 30, d: 43, h: 18, color: 0xd6a162 },
      { id: 25, title_en: "Palace of Longevity and Peace", title_zh: "寿康宫", desc_en: "A residential palace in the eastern part of the Inner Court.", desc_zh: "内廷东部的居住宫殿，常与皇后及妃嫔的住所相关联。", type: "rect", x: 85, y: 170, w: 37, d: 43, h: 18, color: 0xd6a162 },
      { id: 26, title_en: "Hall of Braveness and Splendor", title_zh: "武成殿", desc_en: "A hall dedicated to military training and displaying armor.", desc_zh: "致力于军事训练和展示盔甲的殿堂，反映了满族对武艺的重视。", type: "rect", x: 84, y: 72, w: 46, d: 48, h: 18, color: 0xd6a162 },
      { id: 27, title_en: "Hall of Double Brilliance", title_zh: "重华宫", desc_en: "A ceremonial hall in the eastern section.", desc_zh: "东部的仪式大殿，用于各种礼仪和皇室宗亲的聚会场所。", type: "rect", x: 138, y: 67, w: 48, d: 40, h: 18, color: 0xd6a162 },
      { id: 28, title_en: "Hall of Honesty and Respect", title_zh: "咸福宫", desc_en: "One of the Six Western Palaces.", desc_zh: "西六宫之一，作为内廷妃嫔的住所。", type: "rect", x: 372, y: 255, w: 38, d: 30, h: 18, color: 0xd6a162 },
      { id: 29, title_en: "Palace of Great Benevolence", title_zh: "景仁宫", desc_en: "A major western palace.", desc_zh: "明清时期主要西部宫殿，常为权势皇后和有影响力妃嫔的居所。", type: "rect", x: 372, y: 205, w: 38, d: 30, h: 18, color: 0xd6a162 },
      { id: 30, title_en: "Palace of Bearing Heaven", title_zh: "承乾宫", desc_en: "A residential palace in the western section.", desc_zh: "西部的居住宫殿，是西六宫建筑群的一部分。", type: "rect", x: 372, y: 155, w: 38, d: 30, h: 18, color: 0xd6a162 },
      { id: 31, title_en: "Palace of Gathering Essence", title_zh: "钟粹宫", desc_en: "A western palace known for its refined architecture.", desc_zh: "以精美建筑闻名的西部宫殿，作为受宠妃嫔的居所。", type: "rect", x: 372, y: 115, w: 38, d: 30, h: 18, color: 0xd6a162 },
      { id: 32, title_en: "Hall for Ancestry Worship", title_zh: "奉先殿", desc_en: "The Imperial Ancestral Temple complex.", desc_zh: "皇室祖庙建筑群，用于举行庄严的祭祖仪式。", type: "rect", x: 440, y: 255, w: 40, d: 30, h: 18, color: 0xd6a162 },
      { id: 33, title_en: "Palace of Prolonged Happiness", title_zh: "延禧宫", desc_en: "A residence in the eastern section of the Inner Court.", desc_zh: "内廷东部的住所，是东六宫建筑群的一部分。", type: "rect", x: 412, y: 202, w: 32, d: 30, h: 18, color: 0xd6a162 },
      { id: 34, title_en: "Palace of Eternal Harmony", title_zh: "永和宫", desc_en: "A prominent eastern palace.", desc_zh: "重要的东部宫殿，作为妃嫔住所，有时也用于礼仪目的。", type: "rect", x: 412, y: 155, w: 32, d: 30, h: 18, color: 0xd6a162 },
      { id: 35, title_en: "Palace of Sunlight", title_zh: "景阳宫", desc_en: "An eastern palace residence known for bright courtyards.", desc_zh: "东部的居住宫殿，属于东六宫，以其明亮宽敞的庭院而闻名。", type: "rect", x: 412, y: 115, w: 32, d: 30, h: 18, color: 0xd6a162 },
      { id: 36, title_en: "North Five Halls", title_zh: "北五所", desc_en: "A cluster of five halls in the northern part.", desc_zh: "内廷北部由五座殿堂组成的建筑群，用于多种行政和居住��的。", type: "rect", x: 410, y: 70, w: 102, d: 45, h: 18, color: 0xd6a162 },
      { id: 37, title_en: "Imperial Study", title_zh: "南书房", desc_en: "The emperor's private library and workspace.", desc_zh: "皇帝的私人图书馆和工作场所，在此批阅奏章、研究国事。", type: "rect", x: 455, y: 115, w: 37, d: 30, h: 18, color: 0xd6a162 },
      { id: 38, title_en: "Gate of Imperial Supremacy", title_zh: "皇极门", desc_en: "The entrance gate to a section used by retired emperors.", desc_zh: "皇极殿建筑群的入口，通往太上皇使用的区域。", type: "rect", x: 527, y: 280, w: 37, d: 27, h: 18, color: 0xd6a162 },
      { id: 39, title_en: "Hall of Imperial Supremacy", title_zh: "皇极殿", desc_en: "A large hall serving as the residence for retired emperors.", desc_zh: "东北部的大型殿堂，在清代作为太上皇的居所。", type: "rect", x: 527, y: 230, w: 42, d: 27, h: 18, color: 0xd6a162 },
      { id: 40, title_en: "Hall of Character Cultivation", title_zh: "宁寿宫", desc_en: "A hall used for leisure by retired emperors.", desc_zh: "皇极殿建筑群内的殿堂，供太上皇休闲、学习和进行宗教活动使用。", type: "rect", x: 527, y: 110, w: 40, d: 24, h: 18, color: 0xd6a162 }
    ];

    const staticStructures = [
      { x: 300, y: 373, w: 102, d: 67, h: 3, color: 0xd1d1d1 }, { x: 300, y: 460, w: 102, d: 67, h: 3, color: 0xd1d1d1 }, { x: 300, y: 487, w: 70, d: 67, h: 3, color: 0xd1d1d1 },
      { x: 300, y: 400, w: 52, d: 67, h: 3, color: 0xd1d1d1 }, { x: 300, y: 373, w: 92, d: 57, h: 6, color: 0xd1d1d1 }, { x: 300, y: 460, w: 92, d: 57, h: 6, color: 0xd1d1d1 },
      { x: 300, y: 487, w: 60, d: 57, h: 6, color: 0xd1d1d1 }, { x: 300, y: 420, w: 42, d: 57, h: 6, color: 0xd1d1d1 }, { x: 300, y: 373, w: 82, d: 47, h: 9, color: 0xd1d1d1 },
      { x: 300, y: 460, w: 82, d: 47, h: 9, color: 0xd1d1d1 }, { x: 300, y: 487, w: 50, d: 47, h: 9, color: 0xd1d1d1 }, { x: 300, y: 420, w: 32, d: 47, h: 9, color: 0xd1d1d1 },
      { x: 300, y: 637, w: 12, d: 400, h: 2, color: 0xffffff }, { x: 300, y: 327, w: 12, d: 400, h: 2, color: 0xffffff }, { x: 3, y: 388, w: 5, d: 777, h: 9, color: 0xa3a3a3 },
      { x: 599, y: 388, w: 5, d: 777, h: 9, color: 0xa3a3a3 }, { x: 300, y: 2, w: 597, d: 5, h: 9, color: 0xa3a3a3 }, { x: 300, y: 774, w: 597, d: 5, h: 9, color: 0xa3a3a3 },
      { x: 215, y: 567, w: 8, d: 417, h: 6, color: 0x9c9368 }, { x: 385, y: 567, w: 8, d: 417, h: 6, color: 0x9c9368 }, { x: 300, y: 632, w: 175, d: 8, h: 5, color: 0x9c9368 },
      { x: 300, y: 469, w: 175, d: 8, h: 5, color: 0x9c9368 }, { x: 300, y: 362.5, w: 175, d: 8, h: 5, color: 0x9c9368 }, { x: 299, y: 290, w: 90, d: 9, h: 5, color: 0x9c9368 },
      { x: 299, y: 124, w: 90, d: 10, h: 5, color: 0x9c9368 }, { x: 258, y: 206.7, w: 9, d: 175.5, h: 6, color: 0x9c9368 }, { x: 342, y: 206.7, w: 9, d: 175.5, h: 6, color: 0x9c9368 }
    ];

    const renderer = new THREE.WebGLRenderer({ canvas: document.getElementById("scene"), antialias: true, alpha: true });
    renderer.setPixelRatio(Math.min(devicePixelRatio, 2)); renderer.setSize(innerWidth, innerHeight); renderer.setClearAlpha(0);

    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(48, innerWidth/innerHeight, 0.1, 6000);
    camera.position.set(420, 520, 460);

    const controls = new OrbitControls(camera, renderer.domElement);
    controls.target.set(0, 0, 0); controls.enableDamping = true; controls.dampingFactor = 0.08;
    controls.minPolarAngle = 0.48; controls.maxPolarAngle = 1.33; controls.update();

    const ambient = new THREE.AmbientLight(0xffffff, 0.92); scene.add(ambient);
    const sun = new THREE.DirectionalLight(0xffffff, 0.70); sun.position.set(280, 540, 220); scene.add(sun);

    const ground = new THREE.Mesh(new THREE.PlaneGeometry(PLANE_W, PLANE_H), new THREE.MeshStandardMaterial({ color: 0xffffff }));
    ground.rotation.x = -Math.PI/2; ground.material.roughness = 0.95; ground.material.metalness = 0.0; scene.add(ground);

    new THREE.TextureLoader().load(PLAN_URL, (tex) => {
      tex.colorSpace = THREE.SRGBColorSpace; ground.material.map = tex; ground.material.needsUpdate = true;
      document.getElementById("status").style.display = "none";
    }, undefined, () => {
      document.getElementById("status").textContent = "Texture not found. Showing objects only.";
      setTimeout(() => { document.getElementById("status").style.display = "none"; }, 3000);
    });

    const allParts = []; const partToRoot = new Map(); const roots = new Map();
    function imgToWorld(x, y){ return { wx: x - PLANE_W/2, wz: y - PLANE_H/2 }; }

    // 分层建筑：墙体 + 屋顶（主殿双层，普通单层）
    const ROOF_COLOR = 0x8b4513;        // 屋顶：朱红木
    const ROOF_GOLD = 0xc8a832;         // 琉璃瓦：金黄
    const TILE_GREY = 0x6b5d4f;         // 普通筒瓦：深褐灰
    const WALL_RED = 0x9c2818;          // 宫墙：朱红

    function createPart(rootMeta, cfg, colorHex){
      const baseColor = colorHex || 0xd6a162;
      const group = new THREE.Group();

      // 判断是否主殿（id 在主殿集合内）—— 双层屋檐 + 金色琉璃瓦
      const mainHalls = new Set([6, 7, 8, 14, 15, 16, 39]); // 太和/中和/保和/乾清/交泰/坤宁/皇极
      const isMain = mainHalls.has(rootMeta.id);

      const wallColor = isMain ? WALL_RED : baseColor;
      const roofColor = isMain ? ROOF_GOLD : TILE_GREY;
      const wallMat = new THREE.MeshStandardMaterial({ color: wallColor, roughness: 0.85 });
      const roofMat = new THREE.MeshStandardMaterial({ color: roofColor, roughness: 0.55, metalness: 0.15 });

      // 1) 墙体（占 75% 高度）
      const wallH = cfg.h * 0.75;
      const wall = new THREE.Mesh(new THREE.BoxGeometry(cfg.w, wallH, cfg.d), wallMat);
      wall.position.y = wallH / 2;
      group.add(wall);

      // 2) 屋顶：下层（屋檐，宽出 10%）
      const eaveOverhang = 0.18;
      const eaveW = cfg.w * (1 + eaveOverhang);
      const eaveD = cfg.d * (1 + eaveOverhang);
      const eaveH = cfg.h * (isMain ? 0.13 : 0.18);
      const eave = new THREE.Mesh(new THREE.BoxGeometry(eaveW, eaveH, eaveD), roofMat);
      eave.position.y = wallH + eaveH / 2;
      group.add(eave);

      // 3) 屋顶：上层（顶层屋脊）
      const topW = cfg.w * (isMain ? 0.7 : 0.85);
      const topD = cfg.d * (isMain ? 0.7 : 0.85);
      const topH = cfg.h * (isMain ? 0.12 : 0.07);
      const top = new THREE.Mesh(new THREE.BoxGeometry(topW, topH, topD), roofMat);
      top.position.y = wallH + eaveH + topH / 2;
      group.add(top);

      // 4) 主殿再加第二层屋檐
      if (isMain) {
        const eave2W = topW * 1.08;
        const eave2D = topD * 1.08;
        const eave2H = cfg.h * 0.06;
        const eave2 = new THREE.Mesh(new THREE.BoxGeometry(eave2W, eave2H, eave2D), roofMat);
        eave2.position.y = wallH + eaveH + topH + eave2H / 2;
        group.add(eave2);

        // 顶层脊
        const ridgeW = topW * 0.6;
        const ridgeH = cfg.h * 0.05;
        const ridge = new THREE.Mesh(new THREE.BoxGeometry(ridgeW, ridgeH, topD * 0.4), roofMat);
        ridge.position.y = wallH + eaveH + topH + eave2H + ridgeH / 2;
        group.add(ridge);
      }

      const p = imgToWorld(cfg.x, cfg.y);
      group.position.set(p.wx, 0.15, p.wz);

      // 把每个子 mesh 加到 raycaster 命中数组 + partToRoot
      // 注意：不要在 traverse 回调里 scene.add(child)，会破坏遍历
      const subMeshes = [];
      group.traverse(child => {
        if (child.isMesh) {
          child.userData = {
            baseH: cfg.h,
            scaleYCurrent: 1,
            scaleYTarget: 1,
            baseColor: child.material.color.getHex(),
            rootId: rootMeta.id,
            parentGroup: group
          };
          allParts.push(child);
          partToRoot.set(child, rootMeta.id);
          subMeshes.push(child);
        }
      });

      group.userData = { subMeshes, baseH: cfg.h };
      scene.add(group);
      return group;
    }

    objects.forEach(o => {
      roots.set(o.id, { meta: o, parts: [] });
      const c = o.color || 0xd6a162;
      if (o.type === "rect") roots.get(o.id).parts.push(createPart(o, { x:o.x, y:o.y, w:o.w, d:o.d, h:o.h }, c));
      else if (o.type === "u"){
        roots.get(o.id).parts.push(createPart(o, { x: o.x - (o.outerW/2 - o.legT/2), y:o.y, w:o.legT, d:o.outerD, h:o.h }, c));
        roots.get(o.id).parts.push(createPart(o, { x: o.x + (o.outerW/2 - o.legT/2), y:o.y, w:o.legT, d:o.outerD, h:o.h }, c));
        roots.get(o.id).parts.push(createPart(o, { x:o.x, y:o.y - (o.outerD/2 - o.barT/2), w:o.outerW, d:o.barT, h:o.h }, c));
      }
    });

    const staticMeshes = [];
    staticStructures.forEach(s => {
      const mesh = new THREE.Mesh(new THREE.BoxGeometry(s.w, s.h, s.d), new THREE.MeshStandardMaterial({ color: s.color || 0xe8dcc6 }));
      const p = imgToWorld(s.x, s.y); mesh.position.set(p.wx, s.h/2 + 0.15, p.wz);
      mesh.userData = { baseH: s.h, scaleYCurrent: 1, scaleYTarget: 1 };
      scene.add(mesh); staticMeshes.push(mesh);
    });

    const raycaster = new THREE.Raycaster(); const mouse = new THREE.Vector2();
    let hoveredRootId = null; let selectedRootId = null;
    const hoverTip = document.getElementById("hoverTip"); const hoverTipText = document.getElementById("hoverTipText");

    function setRootState(rootId, on){
      const root = roots.get(rootId); if (!root) return;
      root.parts.forEach(group => {
        // group.parts 改色
        if (group.userData && group.userData.subMeshes) {
          group.userData.subMeshes.forEach(mesh => {
            mesh.material.color.set(on ? 0xd4af37 : mesh.userData.baseColor);
          });
        }
        // 整体 group 高度缩放（基于 wall + roof 整体高度）
        const totalH = group.userData.baseH * 1.05;
        group.scale.y = on ? 1.20 : 1.0; // 比例缩放
      });
    }

    function setActiveListItem(id){
      document.querySelectorAll("#buildingList .building-item").forEach(el => el.classList.toggle("is-active", Number(el.dataset.id) === id));
    }

    function focusOnRoot(rootId, { openCard = true } = {}){
      const root = roots.get(rootId); if (!root) return;
      if (selectedRootId !== null && selectedRootId !== rootId) setRootState(selectedRootId, false);
      selectedRootId = rootId; setActiveListItem(rootId); setRootState(rootId, true); hoveredRootId = rootId;
      if (openCard) {
        const isZh = document.body.classList.contains('lang-zh');
        document.getElementById("cardBadge").textContent = `#${root.meta.id}`;
        document.getElementById("cardTitle").textContent = isZh ? root.meta.title_zh : root.meta.title_en;
        document.getElementById("cardDesc").textContent = isZh ? root.meta.desc_zh : root.meta.desc_en;
        // 官网全景链接：精确跳转到对应区域的全景点列表
        const panoLink = document.getElementById("cardPanoLink");
        if (panoLink) {
          // 建筑 → 故宫全景系统 regionId 映射
          // 数据来源：故宫全景系统 areaMap (https://pano.dpm.org.cn/static/js/app.ae7f67dd.js)
          const panoMap = {
            1: 47,   // Meridian Gate 午门 → 午门外南部
            2: 47,   // Gate of Divine Prowess 神武门 → 午门外南部
            3: 40,   // West Flowery Gate 西华门 → 文华殿区
            4: 40,   // East Flowery Gate 东华门 → 文华殿区
            5: 20,   // Gate of Supreme Harmony 太和门 → 太和门及前三殿区
            6: 20,   // Hall of Supreme Harmony 太和殿 → 太和门及前三殿区
            7: 20,   // Hall of Central Harmony 中和殿 → 太和门及前三殿区
            8: 20,   // Hall of Preserved Harmony 保和殿 → 太和门及前三殿区
            9: 61,   // Gate of Heavenly Purity 乾清门 → 乾清门及后三宫区
            10: 20,  // Tower of Enhanced Righteousness 弘义阁 → 太和门及前三殿区
            11: 20,  // Tower of State Benevolence 体仁阁 → 太和门及前三殿区
            12: 70,  // Hall of Military Prowess 武英殿 → 武英殿区
            13: 40,  // Hall of Literary Glory 文华殿 → 文华殿区
            14: 61,  // Hall of Heavenly Purity 乾清宫 → 乾清门及后三宫区
            15: 61,  // Hall of Union and Peace 交泰殿 → 乾清门及后三宫区
            16: 61,  // Hall of Earthly Tranquility 坤宁宫 → 乾清门及后三宫区
            17: 63,  // Imperial Garden 御花园 → 御花园区
            18: 69,  // Garden of Benevolent Peace 慈宁花园 → 慈宁花园
            19: 68,  // Palace of Benevolent Peace 慈宁宫 → 慈宁宫区
            20: 61,  // Imperial Kitchen 御茶膳房 → 乾清门及后三宫区
            21: 61,  // Southern Three Halls 南三所 → 乾清门及后三宫区
            22: 20,  // Nine Dragon Screen 九龙壁 → 太和门及前三殿区
            23: 50,  // Hall of Mental Cultivation 养心殿 → 养心殿区
            24: 49,  // Shufang Lodge 漱芳斋 → 漱芳斋区
            25: 64,  // Palace of Longevity and Peace 寿康宫 → 寿康宫区
            26: 68,  // Hall of Braveness and Splendor 武成殿 → 慈宁宫区
            27: 53,  // Hall of Double Brilliance 重华宫 → 重华宫区
            28: 44,  // Hall of Honesty and Respect 咸福宫 → 咸福宫区
            29: 59,  // Palace of Great Benevolence 景仁宫 → 景仁宫区
            30: 66,  // Palace of Bearing Heaven 承乾宫 → 承乾宫区
            31: 65,  // Palace of Gathering Essence 钟粹宫 → 钟粹宫区
            32: 67,  // Hall for Ancestry Worship 奉先殿 → 奉先殿区
            33: 48,  // Palace of Prolonged Happiness 延禧宫 → 延禧宫区
            34: 55,  // Palace of Eternal Harmony 永和宫 → 永和宫区
            35: 54,  // Palace of Sunlight 景阳宫 → 景阳宫区
            36: 61,  // North Five Halls 北五所 → 乾清门及后三宫区
            37: 61,  // Imperial Study 南书房 → 乾清门及后三宫区
            38: 56,  // Gate of Imperial Supremacy 皇极门 → 皇极门外戏衣库区
            39: 57,  // Hall of Imperial Supremacy 皇极殿 → 皇极殿区
            40: 71   // Hall of Character Cultivation 宁寿宫 → 宁寿宫花园区
          };
          const regionId = panoMap[root.meta.id];
          if (regionId) {
            // 跳转到故宫官网对应的区域（用户可以在地图上点选具体建筑）
            panoLink.href = `https://pano.dpm.org.cn/#/panorama?regionId=${regionId}`;
          } else {
            panoLink.href = "https://pano.dpm.org.cn/";
          }
        }
        document.getElementById("infoCard").classList.add("show");
      }
      const p = imgToWorld(root.meta.x, root.meta.y);
      controls.target.set(p.wx, 0, p.wz); controls.update();
    }

    const bList = document.getElementById("buildingList");
    
    // 建筑分类映射
    const BUILDING_CATEGORY = {
      1:'gate', 2:'gate', 3:'gate', 4:'gate',                  // 午/神武/西华/东华
      5:'outer', 6:'outer', 7:'outer', 8:'outer',             // 太和门+前三殿
      10:'outer', 11:'outer', 12:'outer', 13:'outer',         // 弘义/体仁/武英/文华
      9:'inner', 14:'inner', 15:'inner', 16:'inner',          // 乾清门+后三宫
      27:'inner', 28:'inner', 29:'inner', 30:'inner',          // 东西六宫
      17:'garden', 18:'garden', 39:'garden', 40:'garden',     // 御花园/慈宁/皇极/宁寿
    };
    // 默认分类（id > 18 的散落建筑）映射为 outer
    Object.keys(objects).forEach(k => {
      const id = objects[k].id;
      if (!BUILDING_CATEGORY[id]) BUILDING_CATEGORY[id] = 'outer';
    });

    let currentFilter = 'all';
    let currentSearch = '';
    const visitedIds = new Set(JSON.parse(localStorage.getItem('mapVisited') || '[]'));

    function renderMapUI(lang) {
      bList.innerHTML = '';
      const isZh = lang === 'zh';
      const searchLower = currentSearch.toLowerCase();
      let visibleCount = 0;
      objects.sort((a,b)=>a.id-b.id).forEach(o => {
        // 分类筛选
        if (currentFilter !== 'all' && BUILDING_CATEGORY[o.id] !== currentFilter) return;
        // 搜索筛选
        if (searchLower) {
          const haystack = (o.title_zh + ' ' + o.title_en + ' #' + o.id).toLowerCase();
          if (!haystack.includes(searchLower)) return;
        }
        visibleCount++;
        const btn = document.createElement("button"); btn.type = "button"; btn.className = "building-item"; btn.dataset.id = o.id;
        const visitedMark = visitedIds.has(o.id) ? '<span style="color:#d4af37;margin-left:4px;font-weight:900;">✓</span>' : '';
        btn.innerHTML = `<span class="building-item__badge">#${o.id}</span><span style="flex:1;">${isZh ? o.title_zh : o.title_en}</span>${visitedMark}`;
        btn.style.display = 'flex';
        btn.style.alignItems = 'center';
        btn.onmouseenter = () => setRootState(o.id, true);
        btn.onmouseleave = () => { if (selectedRootId !== o.id && hoveredRootId !== o.id) setRootState(o.id, false); };
        btn.onclick = () => focusOnRoot(o.id, { openCard: true });
        bList.appendChild(btn);
      });
      if (visibleCount === 0) {
        const empty = document.createElement('div');
        empty.style.cssText = 'padding: 20px; text-align: center; color: rgba(84,27,30,0.5); font-size: 12px;';
        empty.textContent = isZh ? '无匹配建筑' : 'No matches';
        bList.appendChild(empty);
      }
      updateProgress();
      if(selectedRootId) focusOnRoot(selectedRootId, { openCard: document.getElementById("infoCard").classList.contains("show") });
    }

    function updateProgress() {
      const visitedCount = visitedIds.size;
      const pct = (visitedCount / 40) * 100;
      const isZh = document.body.classList.contains('lang-zh');
      document.getElementById('mapProgress').innerHTML = isZh
        ? `<span class="lang-en-content">Visited 0 / 40</span><span class="lang-zh-content">已查看 ${visitedCount} / 40</span>`
        : `<span class="lang-en-content">Visited ${visitedCount} / 40</span><span class="lang-zh-content">已查看 ${visitedCount} / 40</span>`;
      document.getElementById('mapProgressBar').style.width = pct + '%';
      localStorage.setItem('mapVisited', JSON.stringify([...visitedIds]));
    }

    // 搜索框事件
    document.getElementById('mapSearch').addEventListener('input', (e) => {
      currentSearch = e.target.value.trim();
      renderMapUI(document.body.classList.contains('lang-zh') ? 'zh' : 'en');
    });

    // 分类筛选事件
    document.querySelectorAll('.map-cat-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        document.querySelectorAll('.map-cat-btn').forEach(b => {
          b.classList.remove('active');
          b.style.background = 'white';
          b.style.color = 'var(--primary)';
        });
        btn.classList.add('active');
        btn.style.background = 'var(--primary)';
        btn.style.color = 'white';
        currentFilter = btn.dataset.cat;
        renderMapUI(document.body.classList.contains('lang-zh') ? 'zh' : 'en');
      });
    });

    // 标记已访问：focusOnRoot 时记录
    const origFocusOnRoot2 = focusOnRoot;
    focusOnRoot = function(rootId, opts) {
      origFocusOnRoot2(rootId, opts);
      if (!visitedIds.has(rootId)) {
        visitedIds.add(rootId);
        renderMapUI(document.body.classList.contains('lang-zh') ? 'zh' : 'en');
      }
    };

    window.updateMapLanguage = renderMapUI;
    renderMapUI(document.body.classList.contains('lang-zh') ? 'zh' : 'en');

    document.getElementById("closeBtn").addEventListener("click", ()=> document.getElementById("infoCard").classList.remove("show"));

    window.addEventListener("pointermove", (e)=>{
      if(!document.getElementById('section-map').classList.contains('active')) return;
      mouse.x = (e.clientX / innerWidth) * 2 - 1; mouse.y = -(e.clientY / innerHeight) * 2 + 1;
      raycaster.setFromCamera(mouse, camera);
      const hit = raycaster.intersectObjects(allParts)[0];

      if (!hit){
        if (hoveredRootId !== null && hoveredRootId !== selectedRootId) setRootState(hoveredRootId, false);
        hoveredRootId = null; hoverTip.style.display = "none"; return;
      }
      const rid = partToRoot.get(hit.object);
      if (rid !== hoveredRootId){
        if (hoveredRootId !== null && hoveredRootId !== selectedRootId) setRootState(hoveredRootId, false);
        hoveredRootId = rid; setRootState(hoveredRootId, true);
      }
      const meta = roots.get(rid).meta;
      hoverTipText.textContent = `#${meta.id} — ${document.body.classList.contains('lang-zh') ? meta.title_zh : meta.title_en}`;
      hoverTip.style.display = "block"; hoverTip.style.left = (e.clientX + 12) + "px"; hoverTip.style.top  = (e.clientY + 12) + "px";
    });

    window.addEventListener("click", ()=>{ if (hoveredRootId !== null && document.getElementById('section-map').classList.contains('active')) focusOnRoot(hoveredRootId, { openCard: true }); });

    // ===== 氛围升级（昼夜 + 雾 + 天空 + 灯光 + 粒子）=====
    let isNight = false;

    // 天空盒：球形渐变（白天蓝白 / 夜晚深紫蓝）
    const skyGeo = new THREE.SphereGeometry(3000, 32, 16);
    const skyMat = new THREE.MeshBasicMaterial({ color: 0xcfdcea, side: THREE.BackSide, fog: false });
    const sky = new THREE.Mesh(skyGeo, skyMat);
    scene.add(sky);

    // 雾
    scene.fog = new THREE.Fog(0xcfdcea, 1200, 2800);

    // 月光（夜晚用）
    const moonLight = new THREE.DirectionalLight(0x6b8caa, 0);
    moonLight.position.set(-280, 540, -220);
    scene.add(moonLight);

    // 月亮（天空盒内的小球）
    const moonGeo = new THREE.SphereGeometry(35, 16, 12);
    const moonMat = new THREE.MeshBasicMaterial({ color: 0xfffce8, fog: false });
    const moon = new THREE.Mesh(moonGeo, moonMat);
    moon.position.set(-1200, 1500, -800);
    scene.add(moon);
    const sunOrb = new THREE.Mesh(
      new THREE.SphereGeometry(45, 16, 12),
      new THREE.MeshBasicMaterial({ color: 0xfff3a8, fog: false })
    );
    sunOrb.position.set(1200, 1500, 800);
    scene.add(sunOrb);

    // 建筑夜灯（PointLight 集合 - 主殿金色灯笼）
    const nightLights = [];
    [6, 7, 8, 14, 15, 16, 39].forEach(id => {
      const root = roots.get(id); if (!root) return;
      const p = imgToWorld(root.meta.x, root.meta.y);
      const pl = new THREE.PointLight(0xffb85a, 0, 80, 2);
      pl.position.set(p.wx, root.meta.h * 1.5, p.wz);
      scene.add(pl);
      nightLights.push(pl);
    });

    // 粒子（漂浮的"光尘"）
    const dustCount = 220;
    const dustGeo = new THREE.BufferGeometry();
    const dustPos = new Float32Array(dustCount * 3);
    for (let i = 0; i < dustCount; i++) {
      dustPos[i * 3]     = (Math.random() - 0.5) * PLANE_W * 1.2;
      dustPos[i * 3 + 1] = Math.random() * 220;
      dustPos[i * 3 + 2] = (Math.random() - 0.5) * PLANE_H * 1.2;
    }
    dustGeo.setAttribute('position', new THREE.BufferAttribute(dustPos, 3));
    const dustMat = new THREE.PointsMaterial({
      color: 0xfff3a8, size: 2.5, sizeAttenuation: true,
      transparent: true, opacity: 0, fog: false,
      blending: THREE.AdditiveBlending, depthWrite: false
    });
    const dust = new THREE.Points(dustGeo, dustMat);
    scene.add(dust);

    function toggleDayNight() {
      isNight = !isNight;
      const btn = document.getElementById('mapDayNightBtn');
      if (!btn) return;
      btn.textContent = isNight ? '🌙' : '☀️';

      // 动画过渡
      const dur = 800;
      const start = performance.now();
      const fromSky = skyMat.color.getHex();
      const toSky = isNight ? 0x141432 : 0xcfdcea;
      const fromFog = scene.fog.color.getHex();
      const toFog = isNight ? 0x141432 : 0xcfdcea;
      const fromAmb = ambient.intensity;
      const toAmb = isNight ? 0.18 : 0.92;
      const fromSun = sun.intensity;
      const toSun = isNight ? 0.10 : 0.70;
      const fromMoon = moonLight.intensity;
      const toMoon = isNight ? 0.55 : 0;
      const fromDust = dustMat.opacity;
      const toDust = isNight ? 0.55 : 0;
      const fromLight = nightLights.map(l => l.intensity);
      const toLightVal = isNight ? 1.4 : 0;

      // 修复：使用临时 Color 做 lerpColors，避免双重 lerp
      const _tmpColor = new THREE.Color();
      const _fromSkyC = new THREE.Color(fromSky);
      const _fromFogC = new THREE.Color(fromFog);
      function step() {
        const t = Math.min(1, (performance.now() - start) / dur);
        const eased = 1 - Math.pow(1 - t, 2);
        _tmpColor.lerpColors(_fromSkyC, _toSkyC, eased);
        skyMat.color.copy(_tmpColor);
        _tmpColor.lerpColors(_fromFogC, _toFogC, eased);
        scene.fog.color.copy(_tmpColor);
        ambient.intensity = fromAmb + (toAmb - fromAmb) * eased;
        sun.intensity = fromSun + (toSun - fromSun) * eased;
        moonLight.intensity = fromMoon + (toMoon - fromMoon) * eased;
        dustMat.opacity = fromDust + (toDust - fromDust) * eased;
        nightLights.forEach((l, i) => {
          l.intensity = fromLight[i] + (toLightVal - fromLight[i]) * eased;
        });
        if (t < 1) requestAnimationFrame(step);
      }
      step();
    }

    document.getElementById('mapDayNightBtn').addEventListener('click', toggleDayNight);

    // 粒子漂浮动画（放在 animation loop 中）
    const dustAnimStep = () => {
      const pos = dust.geometry.attributes.position.array;
      for (let i = 0; i < dustCount; i++) {
        pos[i * 3 + 1] += 0.06;
        if (pos[i * 3 + 1] > 220) pos[i * 3 + 1] = 0;
      }
      dust.geometry.attributes.position.needsUpdate = true;
    };

    // ===== 互动体验升级 =====
    const INITIAL_CAM_POS = camera.position.clone();
    const INITIAL_TARGET = controls.target.clone();
    let cameraAnimating = false;
    let camTween = null;

    function tweenCamera(toPos, toTarget, duration = 900) {
      cameraAnimating = true;
      const startPos = camera.position.clone();
      const startTarget = controls.target.clone();
      const startTime = performance.now();
      camTween = { startPos, startTarget, toPos, toTarget, startTime, duration };
    }

    // 点击建筑时，相机平滑飞向建筑
    const origFocusOnRoot = focusOnRoot;
    focusOnRoot = function(rootId, opts) {
      origFocusOnRoot(rootId, opts);
      const root = roots.get(rootId); if (!root) return;
      const p = imgToWorld(root.meta.x, root.meta.y);
      const targetPos = new THREE.Vector3(p.wx + 220, root.meta.h * 3.5 + 80, p.wz + 220);
      tweenCamera(targetPos, new THREE.Vector3(p.wx, 0, p.wz), 900);
    };

    // 重置视角
    function resetCamera() {
      tweenCamera(INITIAL_CAM_POS, INITIAL_TARGET, 700);
    }
    document.getElementById("mapResetBtn").addEventListener("click", resetCamera);

    // 快捷键
    window.addEventListener("keydown", (e) => {
      if (!document.getElementById('section-map').classList.contains('active')) return;
      if (e.key === 'r' || e.key === 'R') resetCamera();
      if (e.key === 'n' || e.key === 'N') toggleDayNight();
    });

    // 在渲染循环里处理 tween
    const origRender = renderer.render.bind(renderer);
    renderer.render = function(scene, camera) {
      if (cameraAnimating && camTween) {
        const t = Math.min(1, (performance.now() - camTween.startTime) / camTween.duration);
        const eased = 1 - Math.pow(1 - t, 3);
        camera.position.lerpVectors(camTween.startPos, camTween.toPos, eased);
        controls.target.lerpVectors(camTween.startTarget, camTween.toTarget, eased);
        if (t >= 1) { cameraAnimating = false; camTween = null; }
      }
      origRender(scene, camera);
    };

    window.addEventListener("resize", ()=>{
      if(document.getElementById('section-map').classList.contains('active')){
        camera.aspect = innerWidth/innerHeight; camera.updateProjectionMatrix(); renderer.setSize(innerWidth, innerHeight);
      }
    });

    renderer.setAnimationLoop(()=>{
      if(document.getElementById('section-map').classList.contains('active')){
        // 新版：sub-mesh 不再独立缩放（group 已经缩放过了）
        controls.update(); dustAnimStep(); renderer.render(scene, camera);
      }
    });
  </script>
<script>
    // ============================================
    // TOUR SECTION: SVG building / Step / Pin-label sync + Back to Map
    // ============================================
    (function() {
      // 1. Back to Map button
      const backBtn = document.getElementById('tourBackToMap');
      if (backBtn) {
        backBtn.addEventListener('click', function(e) {
          e.preventDefault();
          const mapTab = document.querySelector('.nav-tab[data-target="section-map"]');
          if (mapTab) mapTab.click();
          window.scrollTo({ top: 0, behavior: 'smooth' });
        });
      }

      // 2. SVG building / Step / Pin-label sync
      const buildings = document.querySelectorAll('.tour-axis__building');
      const steps = document.querySelectorAll('.tour-axis__step');
      const pinLabels = document.querySelectorAll('.tour-axis__pin-label');

      function highlight(targetStep, on) {
        buildings.forEach(b => {
          if (b.getAttribute('data-step') === targetStep) {
            b.style.opacity = on ? '1' : '';
            b.querySelector('rect, circle').style.filter = on ? 'drop-shadow(0 0 8px #d4af37)' : '';
          } else {
            b.style.opacity = on ? '0.4' : '';
          }
        });
        steps.forEach(s => {
          if (s.getAttribute('data-step') === targetStep) {
            if (on) {
              s.style.background = 'rgba(26,26,46,0.95)';
              s.style.borderLeftColor = '#d4af37';
              s.style.transform = 'translateX(4px)';
            } else {
              s.style.background = '';
              s.style.borderLeftColor = '';
              s.style.transform = '';
            }
          } else if (on) {
            s.style.opacity = '0.5';
          } else {
            s.style.opacity = '';
          }
        });
        pinLabels.forEach(l => {
          if (l.getAttribute('data-step') === targetStep) {
            if (on) {
              l.style.background = 'rgba(26,26,46,0.95)';
              l.style.borderLeftColor = '#d4af37';
              l.style.transform = 'translateX(4px)';
              l.style.color = '#d4af37';
            } else {
              l.style.background = '';
              l.style.borderLeftColor = '';
              l.style.transform = '';
              l.style.color = '';
            }
          } else if (on) {
            l.style.opacity = '0.5';
          } else {
            l.style.opacity = '';
          }
        });
      }

      buildings.forEach(b => {
        const step = b.getAttribute('data-step');
        b.addEventListener('mouseenter', () => highlight(step, true));
        b.addEventListener('mouseleave', () => highlight(step, false));
      });
      steps.forEach(s => {
        const step = s.getAttribute('data-step');
        s.addEventListener('mouseenter', () => highlight(step, true));
        s.addEventListener('mouseleave', () => highlight(step, false));
      });
      pinLabels.forEach(l => {
        const step = l.getAttribute('data-step');
        l.addEventListener('mouseenter', () => highlight(step, true));
        l.addEventListener('mouseleave', () => highlight(step, false));
      });
    })();


    // Museum Map Zoom Lightbox
    (function() {
      const frames = document.querySelectorAll("[data-map-zoom]");
      if (!frames.length) return;
      const lb = document.createElement("div");
      lb.className = "fc-map-lightbox";
      lb.innerHTML = "<button class=\"fc-map-lightbox__close\" aria-label=\"Close\">&times;</button><img alt=\"\">";
      document.body.appendChild(lb);
      const lbImg = lb.querySelector("img");
      const closeBtn = lb.querySelector(".fc-map-lightbox__close");
      frames.forEach(f => {
        f.addEventListener("click", () => {
          const src = f.querySelector("img").getAttribute("src");
          lbImg.setAttribute("src", src);
          lb.classList.add("active");
        });
      });
      lb.addEventListener("click", () => lb.classList.remove("active"));
      closeBtn.addEventListener("click", (e) => {
        e.stopPropagation();
        lb.classList.remove("active");
      });
    })();
</script>
</body>
</html>
