<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <title>akdong-api</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/header.css">
  <link rel="stylesheet" href="css/nav.css">
  <script src="js/search.js" defer></script>
</head>

<body>
<div class="mobile-wrapper">
  <div class="bth-header">
    <div class="bth-header__left">
      <div class="bth-header__logo">
        <img src="https://content.bithumb.com/resources/img/comm/seo/favicon-96x96.png" alt="bithumb">
      </div>
      <div class="bth-header__title">거래소</div>
    </div>
    <div class="bth-header__right">
      <button class="search-btn" id="openSearcha">
        <svg viewBox="0 0 24 24">
          <path d="M11 2a9 9 0 016.32 15.32l4.68 4.68-1.41 1.41-4.68-4.68A9 9 0 1111 2zm0 2a7 7 0 100 14 7 7 0 000-14z"/>
        </svg>
      </button>
    </div>
  </div>

  <div class="modal-backdrop" id="modalBackdrop">
    <div class="modal">
      <input type="text" id="searchInput" placeholder="코인 검색">
      <div class="search-results" id="searchResults"></div>
    </div>
  </div>

  <div class="page-container">
    <div class="bth-tab-container">
      <div class="bth-tab-item bth-tab-active" id="tabKRW" onclick="switchMarket('KRW')">원화</div>
      <div class="bth-tab-item" id="tabBTC" onclick="switchMarket('BTC')">BTC</div>
    </div>
        <!-- 리스트 헤더 -->
        <div class="coin-list-container">
        <div class="coin-list__header">
          <div class="name-col" onclick="toggleSort('symbol')">
            자산명 <span class="sort-btn">▲</span><span class="sort-btn">▼</span>
          </div>
          <div class="name-col1" onclick="toggleSort('price')">
            현재가 <span class="sort-btn">▲</span><span class="sort-btn">▼</span>
          </div>
          <div class="name-col2" onclick="toggleSort('changeRate')">
            변동 <span class="sort-btn">▲</span><span class="sort-btn">▼</span>
          </div>
          <div class="name-col3" onclick="toggleSort('volume')">
            거래금액 <span class="sort-btn">▲</span><span class="sort-btn">▼</span>
          </div>
        </div>
        <div class="coin-list" id="coinList"></div>
      </div>
    </div>
  </div>

  <?php include __DIR__ . '/frames/nav.php'; ?>
  

</body>
</html>
