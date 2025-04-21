<?php include 'frames/nav.php'; ?>
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <title>입출금</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/header.css">
  <link rel="stylesheet" href="css/nav.css">
  <link rel="stylesheet" href="css/deposit.css">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>

  </style>
</head>
<body>
<div class="mobile-wrapper">
  <div class="bth-header">
    <div class="bth-header__left">
      <div class="bth-header__logo">
        <img src="https://content.bithumb.com/resources/img/comm/seo/favicon-96x96.png" alt="bithumb">
      </div>
      <div class="bth-header__title">입출금</div>
    </div>
    <div class="bth-header__right"></div>
  </div>
  <div class="page-container">
    <div class="dep-top-tab">
      <button class="active">입출금</button>
      <button>내역</button>
    </div>
    <div class="coin-list" id="coinList"></div>
  </div>
</div>
<script>
function formatKrw(value) {
  const n = parseFloat(value) || 0;
  return n.toLocaleString() + "원";
}

function formatNumber(value) {
  const n = parseFloat(value) || 0;
  return n.toLocaleString();
}

function loadBalance() {
  fetch('/api/balance.php')
    .then(res => res.json())
    .then(result => {
      if (result.status === "0000" && result.data) renderCoins(result.data);
      else document.getElementById('coinList').innerHTML = '<div style="text-align:center; padding: 20px; color:#999;">데이터가 없습니다.</div>';
    })
    .catch(() => {
      document.getElementById('coinList').innerHTML = '<div style="text-align:center; padding: 20px; color:#999;">불러오기 실패</div>';
    });
}

function renderCoins(data) {
  const listEl = document.getElementById('coinList');
  listEl.innerHTML = "";
  let hasCoin = false;
  for (let key in data) {
    if (!key.startsWith('total_')) continue;
    const symbol = key.replace('total_','').toUpperCase();
    const balance = parseFloat(data[key]) || 0;
    if (balance <= 0) continue;
    const lastPrice = parseFloat(data[`xcoin_last_${symbol.toLowerCase()}`]) || 0;
    const evalAmount = balance * lastPrice;
    let coinName = symbol;
    if (symbol === 'BTC') coinName = '비트코인 (BTC)';
    else if (symbol === 'XRP') coinName = '리플 (XRP)';
    const item = document.createElement('div');
    item.className = 'coin-item';
    item.innerHTML = `
      <div class="coin-left-text">
        <div class="coin-left-name">${coinName}</div>
        <div class="coin-left-status">출금가능</div>
      </div>
      <div class="coin-right">
        <div class="coin-right-balance">${formatNumber(balance)}</div>
        <div class="coin-right-krw">~ ${formatKrw(evalAmount)}</div>
      </div>
    `;
    item.addEventListener('click', () => {
      if (symbol === 'KRW') location.href = 'krw_deposit.php';
      else location.href = `coin_deposit.php?symbol=${symbol}`;
    });
    listEl.appendChild(item);
    hasCoin = true;
  }
  if (!hasCoin) listEl.innerHTML = '<div style="text-align:center; padding: 20px; color:#999;">보유중인 코인이 없습니다.</div>';
}

loadBalance();
</script>
</body>
</html>
