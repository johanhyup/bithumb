<?php
// 필요에 따라 로그인/인증 체크

$symbol = isset($_GET['symbol']) ? strtoupper($_GET['symbol']) : 'COIN';
$coinNames = [
  'BTC' => '비트코인 (BTC)',
  'XRP' => '리플 (XRP)',
  // 다른 코인명 추가
];
$coinLabel = $coinNames[$symbol] ?? "$symbol ($symbol)";
?>
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=375, initial-scale=1.0">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/coin_deposit.css">
  <title><?php echo $coinLabel; ?> 입출금</title>

</head>
<body>
<div class="mobile-wrapper">
  <div class="header">
    <span class="back" onclick="history.back()">←</span>
    <span class="tab"><?php echo $coinLabel; ?> 입출금</span>
  </div>
  <div class="balance-box">
    <div class="balance-info">
      <div class="left">총 보유</div>
      <div class="right"><div class="totals" id="totalBalance">0 <?php echo $symbol; ?></div></div>
    </div>
    <!-- 사용중/출금가능 등 필요하면 추가 -->
  </div>
  <div class="bal-btns">
    <button id="goDeposit" class="selected">입금하기</button>
    <button id="goWithdraw">출금하기</button>
  </div>
  <!-- 입금/출금 폼 placeholder -->
  <div id="depositForm">
    <!-- 기능 연결 전 placeholder 영역 -->
  </div>
</div>
</body>
</html>
