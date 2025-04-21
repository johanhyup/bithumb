<?php
// assets.php
?>
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <title>자산현황</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/assets.css">
  <link rel="stylesheet" href="css/nav.css">
  <link rel="stylesheet" href="css/header.css">
  <script src="js/search.js"></script>
</head>
<body>
<div class="mobile-wrapper">
  <div class="bth-header">
    <div class="bth-header__left">
      <div class="bth-header__logo">
        <img src="https://content.bithumb.com/resources/img/comm/seo/favicon-96x96.png" alt="">
      </div>
      <div class="bth-header__title">자산현황</div>
    </div>
    <div class="bth-header__right"></div>
  </div>

  <div class="page-container">
    <div class="bth-tab-container">
      <div class="bth-tab-item bth-tab-active" data-tab="asset">나의자산</div>
      <div class="bth-tab-item"           data-tab="history">거래내역</div>
      <div class="bth-tab-item"           data-tab="orders">주문내역</div>
      <div class="bth-tab-item"           data-tab="open">미체결</div>
    </div>

    <div id="asset" class="content content-active">
      <div class="asset-header">
        <div class="asset-header-all">※총 보유자산</div>
        <div class="asset-header__balance" id="totalAsset">0 원</div>
        <div class="asset-header__info">
          <div class="info-row">
            <span class="info-label">평가손익</span>
            <span class="info-value" id="evalProfit">0 원</span>
          </div>
          <div class="info-row">
            <span class="info-label">수익률</span>
            <span class="info-value" id="profitRate">0%</span>
          </div>
          <div class="info-row">
            <span class="info-label">총 매수금액</span>
            <span class="info-value" id="totalBuy">0 원</span>
          </div>
          <div class="info-row">
            <span class="info-label">주문가능원화</span>
            <span class="info-value" id="availableKrw">0 원</span>
          </div>
          <div class="info-row">
            <span class="info-label">보유원화</span>
            <span class="info-value" id="inUseKrw">0 원</span>
          </div>
        </div>
      </div>
      <label class="hide-small">
        <input type="checkbox" id="hideSmall"> 소액 자산 숨기기 (&lt;5000원)
      </label>
      <div class="portfolio-list" id="portfolio"></div>
    </div>

    <div id="history" class="content">
      <table id="history-table">
        <thead><tr>
          <th>일시</th><th>자산</th><th>구분</th><th>수량</th>
          <th>가격</th><th>금액</th><th>수수료</th><th>상태</th>
        </tr></thead>
        <tbody></tbody>
      </table>
    </div>

    <div id="orders" class="content">
      <table id="orders-table">
        <thead><tr>
          <th>UUID</th><th>자산</th><th>구분</th><th>타입</th>
          <th>수량</th><th>가격</th><th>상태</th><th>시간</th>
        </tr></thead>
        <tbody></tbody>
      </table>
    </div>

    <div id="open" class="content">
      <table id="open-table">
        <thead><tr>
          <th>UUID</th><th>자산</th><th>구분</th><th>타입</th>
          <th>수량</th><th>가격</th><th>잔량</th><th>시간</th>
        </tr></thead>
        <tbody></tbody>
      </table>
    </div>
  </div>
</div>
  <?php include __DIR__ . '/frames/nav.php'; ?>

  <script>
    const API = {
      accounts:  '/api/accounts.php',
      history:   '/api/history.php',
      orders:    '/api/orders.php',            // 주문내역 (done)
      open:      '/api/orders.php',            // 미체결 (default wait)
    };
    let markets = {};

    // 탭 전환
    document.querySelectorAll('.bth-tab-item').forEach(tab => {
      tab.onclick = () => {
        document.querySelectorAll('.bth-tab-item')
          .forEach(t=>t.classList.remove('bth-tab-active'));
        document.querySelectorAll('.content')
          .forEach(c=>c.classList.remove('content-active'));
        tab.classList.add('bth-tab-active');
        document.getElementById(tab.dataset.tab)
          .classList.add('content-active');
      };
    });

    // 마켓명 로드
    fetch('https://api.bithumb.com/v1/market/all?isDetails=false')
      .then(r=>r.json())
      .then(list=>{
        list.forEach(m=>markets[m.market]=m.korean_name);
        loadAssets(); loadHistory(); loadOrders(); loadOpen();
      });

    function loadAssets(){
      fetch(API.accounts).then(r=>r.json()).then(arr=>{
        const krw = arr.find(x=>x.currency==='KRW')||{};
        const avail = parseFloat(krw.balance)||0, inuse = parseFloat(krw.locked)||0;
        let totalBuy=0, totalVal=avail+inuse, evalProfit=0;

        document.getElementById('availableKrw').textContent = avail.toLocaleString()+' 원';
        document.getElementById('inUseKrw').textContent     = inuse.toLocaleString()+' 원';

        const port = document.getElementById('portfolio'); port.innerHTML='';

        arr.forEach(o=>{
          if(o.currency==='KRW') return;
          const bal = parseFloat(o.balance), lck = parseFloat(o.locked);
          const totalAmt = bal + lck;
          const avg = parseFloat(o.avg_buy_price)||0;
          const val = totalAmt * avg;
          totalBuy += val; totalVal += val; evalProfit += (val - totalAmt*avg);

          const card = document.createElement('div');
          card.className = 'asset-card';
          card.innerHTML = `
            <div class="title">${markets[o.currency]||o.currency} (${o.currency})</div>
            <div class="row"><span>보유수량</span><span>${totalAmt.toLocaleString()} ${o.currency}</span></div>
            <div class="row"><span>잠금수량</span><span>${lck.toLocaleString()} ${o.currency}</span></div>
            <div class="row"><span>평단가</span><span>${avg.toLocaleString()} KRW</span></div>
            <div class="row"><span>평가금액</span><span>${val.toLocaleString()} 원</span></div>
          `;
          port.appendChild(card);
        });

        document.getElementById('totalAsset').textContent  = totalVal.toLocaleString()+' 원';
        document.getElementById('totalBuy').textContent    = totalBuy.toLocaleString()+' 원';
        document.getElementById('evalProfit').textContent  = evalProfit.toLocaleString()+' 원';
        document.getElementById('profitRate').textContent = totalBuy>0
          ? ((evalProfit/totalBuy)*100).toFixed(2)+'%' : '0%';
      });
    }

    document.getElementById('hideSmall').onchange = e => {
      const hide = e.target.checked;
      document.querySelectorAll('.asset-card').forEach(c=>{
        const txt = c.querySelector('.row:last-child span:last-child').textContent;
        const num = parseFloat(txt.replace(/[^\d]/g,''))||0;
        c.style.display = (hide && num<5000)? 'none':'block';
      });
    };

function loadHistory() {
  fetch(API.history).then(r => r.json()).then(arr => {
    const tb = document.querySelector('#history-table tbody'); 
    tb.innerHTML = '';
    arr.forEach(o => {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${o.transfer_date}</td>
        <td>${o.order_currency}</td>
        <td>${o.payment_currency}</td>
        <td>${parseFloat(o.units).toLocaleString()}</td>
        <td>${parseFloat(o.price).toLocaleString()}원</td>
        <td>${parseFloat(o.amount).toLocaleString()}원</td>
        <td>${parseFloat(o.fee).toLocaleString()}</td>
        <td>${o.order_balance}</td>
      `;
      tb.appendChild(tr);
    });
  });
}

function loadOrders(){
  fetch(API.orders + '?states=done&page=1&limit=100&order_by=desc')
    .then(r=>r.json()).then(arr=>{
      const tb = document.querySelector('#orders-table tbody'); tb.innerHTML='';
      arr.forEach(o=>{
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td>${o.uuid}</td>
          <td>${o.market}</td>
          <td class="${o.side==='bid'?'up':'down'}">${o.side}</td>
          <td>${o.ord_type}</td>
          <td>${parseFloat(o.volume).toLocaleString()}</td>
          <td>${parseFloat(o.price).toLocaleString()}원</td>
          <td>${o.state}</td>
          <td>${o.created_at}</td>
        `;
        tb.appendChild(tr);
      });
    });
}

    function loadOpen(){
      // 미체결 (대기 중)
      fetch(API.open + '?page=1&limit=100&order_by=desc')
        .then(r=>r.json()).then(json=>{
          const arr = Array.isArray(json)? json : (Array.isArray(json.data)? json.data: []);
          const tb = document.querySelector('#open-table tbody'); tb.innerHTML='';
          arr.forEach(o=>{
            const tr = document.createElement('tr');
            tr.innerHTML = `
              <td>${o.uuid}</td>
              <td>${markets[o.market]||o.market}</td>
              <td class="${o.side==='bid'?'up':'down'}">${o.side}</td>
              <td>${o.ord_type}</td>
              <td>${parseFloat(o.volume).toLocaleString()}</td>
              <td>${parseFloat(o.price).toLocaleString()}원</td>
              <td>${parseFloat(o.remaining_volume).toLocaleString()}</td>
              <td>${o.created_at}</td>
            `;
            tb.appendChild(tr);
          });
        });
    }
  </script>
</body>
</html>
