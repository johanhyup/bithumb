<script>
function navigate(page) {
  console.log('Navigating to:', page); // 디버깅용 로그 추가
  switch (page) {
    case 'index':
      window.location.href = 'index.php';
      break;
    case 'assets':
      window.location.href = 'assets.php';
      break;
    case 'deposit':
      window.location.href = 'deposit.php';
      break;
    default:
      console.error('Unknown page:', page);
  }
}

function setActiveNavItem() {
  const currentPath = window.location.pathname;
  const navItems = document.querySelectorAll('.bth-bottom-nav__item');

  navItems.forEach(item => {
    const page = item.getAttribute('data-page');
    if (currentPath.includes(page)) {
      item.classList.add('bth-bottom-nav__item-active');
    } else {
      item.classList.remove('bth-bottom-nav__item-active');
    }
  });
}

// 페이지 로드 시 활성화된 네비게이션 아이템 설정
window.onload = setActiveNavItem;
</script>

<div class="bth-bottom-nav">
  <div class="bth-bottom-nav__item" data-page="index.php" onclick="navigate('index')">
    거래소
  </div>
  <div class="bth-bottom-nav__item" data-page="assets.php" onclick="navigate('assets')">
    자산현황
  </div>
  <div class="bth-bottom-nav__item" data-page="deposit.php" onclick="navigate('deposit')">
    입출금
  </div>
</div>

