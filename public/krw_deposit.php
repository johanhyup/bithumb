<?php
// 파일명: krw_deposit.php
// 필요에 따라 서버에서 세션 또는 JWT 등을 통해 인증 상태 체크 후 아래 페이지 출력

// 매일 00시에 일 입금한도를 초기화하려면 서버단에서 날짜 기반으로 관리하거나
// JS localStorage로 오늘 날짜를 체크해 리셋할 수 있습니다.
// 여기선 간단히 JS로 처리 예시

?>
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=375, initial-scale=1.0">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/krw_deposit.css">
  <title>원화 입출금</title>
  <style>
    
  </style>
</head>
<body>
<div class="mobile-wrapper">
<!-- 메인 -->
<div id="page-main">
  <div class="header">
    <span class="back">←</span>
    <span class="tab">원화 입출금</span>
    <span class="menu">한도안내</span>
  </div>
  <div class="balance-box">
    <div class="balance-info">
      <div class="left">총 보유</div>
      <div class="right">
        <div class="totals" id="totalBalance">0원</div>
      </div>
    </div>
    <div class="balance-info" style="margin-top:7px;">
      <div class="left gray">사용중</div>
      <div class="right sub" id="inUseBalance">0원</div>
    </div>
    <div class="balance-info">
      <div class="left gray">출금가능</div>
      <div class="right sub" id="availBalance">0원</div>
    </div>
    <div class="bal-btns mt8">
      <button id="goDeposit" class="selected">입금하기</button>
      <button id="goWithdraw">출금하기</button>
    </div>
  </div>
  <div class="alarm-row mt16">
    <div class="icon">W</div>
    <span class="txt"><b>쌓인 예치금 이용료</b> 받기 완료!</span>
    <button class="subbtn">내역 보기</button>
  </div>
  <div class="filter-bar mt16">
    <span class="on">180일▼</span>
    <span>거래▼</span>
    <span>상태▼</span>
  </div>
  <div class="tx-list" id="depositList">
    <!-- 입금 내역이 표기될 영역 -->
  </div>
  <div class="spacer"></div>
</div>

<!-- 입금 페이지 -->
<div id="page-deposit">
  <div class="d-header">
    <span>원화 입금</span>
    <span class="close" id="closeDeposit">×</span>
  </div>
  <div class="dcon">
    <div class="d-label">연결 계좌</div>
    <div class="d-input-row">
      <div class="d-row-title">NH농협</div>
      <div class="d-row-v">// 계좌번호 불러오기 (수정불가)</div>
    </div>
    <div class="d-label d-field-money">입금 금액</div>
    <div class="d-money-inputbox">
      <input type="text" id="depositAmount" value="5000" readonly>
      <span>원</span>
    </div>
    <div class="d-money-noti">
      일 잔여한도 <span id="remainLimit">500,000,000</span>원
    </div>
    <div class="d-btn-row">
      <button data-add="10000">+1만</button>
      <button data-add="50000">+5만</button>
      <button data-add="100000">+10만</button>
      <button data-add="1000000">+100만</button>
    </div>
    <div class="d-info mt16">
      • 은행 점검시간(23:25~00:35)에는 진행이 원활하지 않을 수 있습니다.<br>
      • 입금 신청 시 연결된 입출금 계좌에서 해당 금액이 빠져나갑니다.<br>
      • 입금가능 금액은 연결 계좌 한도와 동일하며, 초과 시 실패할 수 있습니다.<br>
      • 연결 계좌 변경 시 재연결이 필요합니다.
    </div>
    <div class="d-info-foot mt16">
      <div class="bank-warning">
        <span>은행 점검시간에는 입금예약만 가능합니다.</span>
        <span class="checkicon"></span>
      </div>
    </div>
    <button class="btn-next" id="btnNext">다음</button>
  </div>
</div>

<!-- 확인 모달 -->
<div id="confirmOverlay">
  <div id="confirmBox">
    <p id="confirmText"></p>
    <div id="confirmButtons">
      <button id="confirmYes">확인</button>
      <button id="confirmNo">취소</button>
    </div>
  </div>
</div>
</div>
<script>
/* 일 한도 관리 (JS 예시) */
const DAY_LIMIT = 500000000;

function getTodayKey() {
  const d = new Date();
  return d.getFullYear() + '-' + (d.getMonth()+1) + '-' + d.getDate();
}

function getDailyUsed() {
  const saved = localStorage.getItem('dailyUsedData');
  if(!saved) return { date:getTodayKey(), used:0 };
  try {
    const obj = JSON.parse(saved);
    if(obj.date !== getTodayKey()) {
      return { date:getTodayKey(), used:0 };
    } else {
      return obj;
    }
  } catch(e) {
    return { date:getTodayKey(), used:0 };
  }
}

function setDailyUsed(amt) {
  const info = getDailyUsed();
  info.used += amt;
  info.date = getTodayKey();
  localStorage.setItem('dailyUsedData', JSON.stringify(info));
}

function calcRemainLimit() {
  const info = getDailyUsed();
  const rem = DAY_LIMIT - info.used;
  return rem>0? rem:0;
}

/* UI 제어 */
const pageMain = document.getElementById('page-main');
const pageDeposit = document.getElementById('page-deposit');
const closeDeposit = document.getElementById('closeDeposit');
const goDeposit = document.getElementById('goDeposit');
const goWithdraw = document.getElementById('goWithdraw'); // 버튼만, 동작은 추후 확장

const depositAmountInput = document.getElementById('depositAmount');
const remainLimitEl = document.getElementById('remainLimit');
const btnNext = document.getElementById('btnNext');

const confirmOverlay = document.getElementById('confirmOverlay');
const confirmText = document.getElementById('confirmText');
const confirmYes = document.getElementById('confirmYes');
const confirmNo = document.getElementById('confirmNo');

/* balances */
const totalBalanceEl = document.getElementById('totalBalance');
const inUseBalanceEl = document.getElementById('inUseBalance');
const availBalanceEl = document.getElementById('availBalance');

/* deposit list */
const depositListEl = document.getElementById('depositList');

// 입금하기 탭
goDeposit.onclick = () => {
  pageDeposit.classList.add('open');
  pageMain.style.display = 'none';
  refreshRemainLimit();
};

// 입금페이지 닫기
closeDeposit.onclick = () => {
  pageDeposit.classList.remove('open');
  pageMain.style.display = 'block';
};

// 출금하기 (추후)
goWithdraw.onclick = () => {
  alert('추후 출금 기능 예정');
};

/* 잔여한도 갱신 */
function refreshRemainLimit(){
  const r = calcRemainLimit();
  remainLimitEl.textContent = r.toLocaleString('ko-KR');
}

/* +버튼 */
document.querySelectorAll('.d-btn-row button').forEach(b=>{
  b.addEventListener('click',()=>{
    const val = parseInt(b.getAttribute('data-add'),10)||0;
    const curr = parseInt(depositAmountInput.value.replace(/,/g,''),10)||0;
    depositAmountInput.value = (curr+val).toLocaleString('ko-KR');
  });
});

/* 다음 버튼 -> 확인 모달 */
btnNext.onclick = () => {
  const amountStr = depositAmountInput.value.replace(/,/g,'');
  const amount = parseInt(amountStr, 10) || 0;
  if(amount<=0) { alert('입금금액을 확인해주세요'); return; }
  if(amount>calcRemainLimit()){
    alert('일 잔여한도를 초과했습니다.');
    return;
  }
  confirmText.textContent = `${amount.toLocaleString('ko-KR')}원을 입금하시겠습니까?`;
  confirmOverlay.classList.add('open');
};

/* 모달 확인 -> 실제 입금 API */
confirmYes.onclick = () => {
  confirmOverlay.classList.remove('open');
  const amountStr = depositAmountInput.value.replace(/,/g,'');
  const amount = parseInt(amountStr,10);

  // 여기서 원화 입금 API 호출. 임시로 fetch 예시
  // 실제론 JWT 등 인증 필요
  // 2차 인증은 kakao 로 예시
  fetch('/api/krw_deposit_api.php', {
    method:'POST',
    headers:{ 'Content-Type':'application/json' },
    body: JSON.stringify({
      amount: amount,
      two_factor_type: 'kakao'
    })
  })
  .then(r=>r.json())
  .then(j=>{
    // 성공 시
    setDailyUsed(amount);
    refreshRemainLimit();
    alert('입금 요청 완료');
    loadDepositList();
    // 잔액도 갱신
    loadBalance();
  })
  .catch(e=>console.log(e));
};

/* 모달 취소 */
confirmNo.onclick = () => {
  confirmOverlay.classList.remove('open');
};

/* 잔액 불러오기 (/api/balance.php) */
function loadBalance(){
  fetch('/api/balance.php')
  .then(r=>r.json())
  .then(j=>{
    if(!j || j.status!=='0000') return;
    const d = j.data;
    const t = parseFloat(d.total_krw)||0;
    const inuse = parseFloat(d.in_use_krw)||0;
    const av = parseFloat(d.available_krw)||0;
    totalBalanceEl.textContent = t.toLocaleString('ko-KR') + '원';
    inUseBalanceEl.textContent = inuse.toLocaleString('ko-KR') + '원';
    availBalanceEl.textContent = av.toLocaleString('ko-KR') + '원';
  });
}

/* 입금 리스트 불러오기 (예: Bithumb KRW 입금 리스트 API) */
function loadDepositList(){
  depositListEl.innerHTML = '';
  fetch('/api/krw_deposit_api.php') // 실제 Bithumb API 호출 대행
  .then(r=>r.json())
  .then(list=>{
    if(!Array.isArray(list)) return;
    list.forEach(item=>{
      // 일단 예시로 type이 deposit, state=ACCEPTED, amount, done_at
      // UI에 '예치금 이용료'라 적혀있지만, 여기선 편의상 'KRW 입금' 표기
      const row = document.createElement('div');
      row.className = 'tx-row';
      row.innerHTML = `
        <div>
          <span class="desc">KRW 입금</span>
          <span class="date">${(item.done_at||'').substr(2,8)}</span>
        </div>
        <div class="amt">${parseInt(item.amount,10).toLocaleString('ko-KR')}원</div>
      `;
      depositListEl.appendChild(row);
    });
  })
  .catch(e=>console.log(e));
}
document.querySelector('.back').addEventListener('click', () => {
  window.location.href = 'deposit.php';
});
/* 초기 실행 */
loadBalance();
loadDepositList();
refreshRemainLimit();
</script>
</body>
</html>
