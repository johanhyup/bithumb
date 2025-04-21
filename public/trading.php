<?php
// trading.php (차트 탭 추가 및 캔들차트 통합)

$symbol = isset($_GET['symbol']) ? strtoupper($_GET['symbol']) : 'BTC';
$market = isset($_GET['market']) ? strtoupper($_GET['market']) : 'KRW';
?>
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <title>Trading Page</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/trading.css">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Chart.js 및 Financial 플러그인 -->
  <script src="/js/chart.js"></script>
  <script src="/js/chartjs-chart-financial.js"></script>
</head>
<body>
  <div class="mobile-wrapper">
    <div class="page-wrap">

      <!-- 상단 헤더 -->
      <div class="header-area">
        <div class="symbol-info">
          <div class="symbol-line">
            <div class="back-arrow" onclick="location.href='index.php'">←</div>
            <div class="symbol-text"><?php echo $symbol; ?> (<?php echo $market; ?>)</div>
          </div>
          <div id="currentPrice" class="big-price">0</div>
          <div class="price-change-line">
            <span id="priceChange" class="price-change">+0</span>
            (<span id="priceChangeRate" class="price-change-rate">+0%</span>)
          </div>
        </div>
        <div class="chart-area">
          <canvas id="miniChart" width="210" height="90"></canvas>
          <div class="chart-volume" id="chartVolumeLabel"></div>
        </div>
      </div>

      <!-- 상단 탭 -->
      <div class="tabs-area">
        <div class="trade-tab trade-tab-active">주문</div>
        <div class="trade-tab">호가</div>
        <div class="trade-tab">차트</div>
      </div>

      <!-- 주문/호가 영역 -->
      <div class="content-area" id="orderContent">
        <div class="orderbook-container">
          <div class="orderbook-list" id="orderbookList"></div>
        </div>
        <div class="right-side">
          <div class="trade-forms">
            <div class="trade-form-header">
              <div id="tabBuy" class="active">매수</div>
              <div id="tabSell">매도</div>
            </div>
            <div id="buyForm" class="form-section">
              <div class="available-wrap">주문가능 금액: <span id="buyAvailable">0</span> KRW</div>
              <div class="price-input-wrapper">
                <input type="text" id="buyPrice" class="form-input" placeholder="가격" value="0">
                <div class="updown-buttons">
                  <button onclick="changePrice(1,'buy')">▲</button>
                  <button onclick="changePrice(-1,'buy')">▼</button>
                </div>
              </div>
              <div class="amount-input-wrapper">
                <input type="text" id="buyAmount" class="form-input" placeholder="수량" value="0">
              </div>
              <div class="range-wrap">
                <input type="range" min="0" max="100" value="0" class="range-input" id="buyRange" oninput="updateBuyAmount()">
                <div class="range-markers">
                  <span onclick="setBuyRange(0)">0%</span>
                  <span onclick="setBuyRange(25)">25%</span>
                  <span onclick="setBuyRange(50)">50%</span>
                  <span onclick="setBuyRange(75)">75%</span>
                  <span onclick="setBuyRange(100)">100%</span>
                </div>
              </div>
              <div class="total-amount">총 주문금액: <span id="buyTotal">0</span> KRW</div>
              <div class="btn-row">
                <button onclick="resetBuy()">초기화</button>
                <button style="background:#e14a4a;color:#fff;" onclick="placeBuy()">매수</button>
              </div>
            </div>
            <div id="sellForm" class="form-section" style="display:none;">
              <div class="available-wrap">주문가능 수량: <span id="sellAvailable">0</span> <?php echo $symbol; ?></div>
              <div class="price-input-wrapper">
                <input type="text" id="sellPrice" class="form-input" placeholder="가격" value="0">
                <div class="updown-buttons">
                  <button onclick="changePrice(1,'sell')">▲</button>
                  <button onclick="changePrice(-1,'sell')">▼</button>
                </div>
              </div>
              <div class="amount-input-wrapper">
                <input type="text" id="sellAmount" class="form-input" placeholder="수량" value="0">
              </div>
              <div class="range-wrap">
                <input type="range" min="0" max="100" value="0" class="range-input" id="sellRange" oninput="updateSellAmount()">
                <div class="range-markers">
                  <span onclick="setSellRange(0)">0%</span>
                  <span onclick="setSellRange(25)">25%</span>
                  <span onclick="setSellRange(50)">50%</span>
                  <span onclick="setSellRange(75)">75%</span>
                  <span onclick="setSellRange(100)">100%</span>
                </div>
              </div>
              <div class="total-amount">총 주문금액: <span id="sellTotal">0</span> KRW</div>
              <div class="btn-row">
                <button onclick="resetSell()">초기화</button>
                <button style="background:#4a5ae1;color:#fff;" onclick="placeSell()">매도</button>
              </div>
            </div>
          </div>
          <div class="transaction-box">
            <div class="transaction-header">
              <span>가격</span><span>수량</span><span>시간</span>
            </div>
            <div class="transaction-list" id="transactionList"></div>
          </div>
        </div>
      </div>

      <!-- 차트 영역 -->
      <div class="content-area" id="chartContent" style="display:none; padding:12px;">
        <canvas id="candleChart" width="100%" height="300"></canvas>
      </div>

    </div>
  </div>

  <script>
    const symbol  = '<?php echo $symbol;?>';
    const market  = '<?php echo $market;?>';

    // DOM 요소
    const currentPriceEl    = document.getElementById('currentPrice');
    const priceChangeEl     = document.getElementById('priceChange');
    const priceChangeRateEl = document.getElementById('priceChangeRate');
    const chartVolumeEl     = document.getElementById('chartVolumeLabel');
    const orderbookListEl   = document.getElementById('orderbookList');
    const transactionListEl = document.getElementById('transactionList');

    const tabs      = document.querySelectorAll('.trade-tab');
    const orderArea = document.getElementById('orderContent');
    const chartArea = document.getElementById('chartContent');

    const tabBuyEl    = document.getElementById('tabBuy');
    const tabSellEl   = document.getElementById('tabSell');
    const buyFormEl   = document.getElementById('buyForm');
    const sellFormEl  = document.getElementById('sellForm');

    // 탭 전환
    tabs.forEach((t,i)=>{
      t.onclick = () => {
        tabs.forEach(x=>x.classList.remove('trade-tab-active'));
        t.classList.add('trade-tab-active');
        if(i === 2) {
          orderArea.style.display = 'none';
          chartArea.style.display = '';
          if(!window.candleChart) initCandleChart();
        } else {
          chartArea.style.display = 'none';
          orderArea.style.display = '';
        }
      };
    });

    // 매수/매도 폼 전환
    tabBuyEl.onclick = () => {
      tabBuyEl.classList.add('active');
      tabSellEl.classList.remove('active');
      buyFormEl.style.display = '';
      sellFormEl.style.display = 'none';
    };
    tabSellEl.onclick = () => {
      tabSellEl.classList.add('active');
      tabBuyEl.classList.remove('active');
      sellFormEl.style.display = '';
      buyFormEl.style.display = 'none';
    };

    // 잔고 로드
    function loadBalance(){
      fetch('/api/balance.php')
        .then(r=>r.json())
        .then(j=>{
          if(!j.data) return;
          document.getElementById('buyAvailable').textContent  = (parseFloat(j.data.available_krw)||0).toLocaleString('ko-KR');
          document.getElementById('sellAvailable').textContent = (parseFloat(j.data['available_'+symbol.toLowerCase()])||0).toLocaleString('ko-KR');
        });
    }
    loadBalance();

    // 매수 로직
    function resetBuy(){
      document.getElementById('buyPrice').value = 0;
      document.getElementById('buyAmount').value = 0;
      document.getElementById('buyRange').value = 0;
      document.getElementById('buyTotal').textContent = '0';
    }
    function changePrice(v, side){
      const el = side==='buy' ? document.getElementById('buyPrice') : document.getElementById('sellPrice');
      el.value = (parseFloat(el.value)||0) + v;
      side==='buy' ? calcBuyTotal() : calcSellTotal();
    }
    function updateBuyAmount(){
      const n = parseInt(document.getElementById('buyRange').value,10);
      document.getElementById('buyAmount').value = n;
      calcBuyTotal();
    }
    function setBuyRange(n){
      document.getElementById('buyRange').value = n;
      updateBuyAmount();
    }
    function calcBuyTotal(){
      const p = parseFloat(document.getElementById('buyPrice').value)||0;
      const a = parseFloat(document.getElementById('buyAmount').value)||0;
      document.getElementById('buyTotal').textContent = (p*a).toLocaleString('ko-KR');
    }
    function placeBuy(){
      const b = new URLSearchParams();
      b.append('order_currency', symbol);
      b.append('payment_currency', market);
      b.append('units', document.getElementById('buyAmount').value);
      b.append('price', document.getElementById('buyPrice').value);
      b.append('type', 'bid');
      fetch('/api/trade',{method:'POST',body:b})
        .then(r=>r.json()).then(()=>loadBalance());
    }

    // 매도 로직
    function resetSell(){
      document.getElementById('sellPrice').value = 0;
      document.getElementById('sellAmount').value = 0;
      document.getElementById('sellRange').value = 0;
      document.getElementById('sellTotal').textContent = '0';
    }
    function updateSellAmount(){
      const n = parseInt(document.getElementById('sellRange').value,10);
      document.getElementById('sellAmount').value = n;
      calcSellTotal();
    }
    function setSellRange(n){
      document.getElementById('sellRange').value = n;
      updateSellAmount();
    }
    function calcSellTotal(){
      const p = parseFloat(document.getElementById('sellPrice').value)||0;
      const a = parseFloat(document.getElementById('sellAmount').value)||0;
      document.getElementById('sellTotal').textContent = (p*a).toLocaleString('ko-KR');
    }
    function placeSell(){
      const b = new URLSearchParams();
      b.append('order_currency', symbol);
      b.append('payment_currency', market);
      b.append('units', document.getElementById('sellAmount').value);
      b.append('price', document.getElementById('sellPrice').value);
      b.append('type', 'ask');
      fetch('/api/trade',{method:'POST',body:b})
        .then(r=>r.json()).then(()=>loadBalance());
    }

    // WebSocket 연결 & 처리
    let ws, scrolled=false;
    function initWS(){
      ws = new WebSocket('wss://ws-api.bithumb.com/websocket/v1');
      ws.binaryType = 'arraybuffer';
      ws.onopen = ()=>{
        ws.send(JSON.stringify([
          { ticket:'ticket' },
          { type:'ticker',    codes:[`${market}-${symbol}`] },
          { type:'trade',     codes:[`${market}-${symbol}`] },
          { type:'orderbook', codes:[`${market}-${symbol}`], level:1 },
          { format:'DEFAULT' }
        ]));
      };
      ws.onmessage = evt=>{
        const raw = evt.data instanceof ArrayBuffer ? new TextDecoder().decode(evt.data) : evt.data;
        const msg = JSON.parse(raw);
        if(msg.type==='ticker')    onTicker(msg);
        if(msg.type==='trade')     onTrade(msg);
        if(msg.type==='orderbook') onOrderbook(msg);
      };
      ws.onclose = ()=> setTimeout(initWS,3000);
    }
    initWS();

    function onTicker(m){
      const p  = m.trade_price||0;
      const cp = m.change_price||0;
      const cr = m.change_rate||0;
      const v  = m.acc_trade_volume_24h||0;
      currentPriceEl.textContent    = p.toLocaleString('ko-KR');
      currentPriceEl.classList.toggle('up',   cp>0);
      currentPriceEl.classList.toggle('down', cp<0);
      priceChangeEl.textContent     = (cp>0?'+':'')+cp.toLocaleString('ko-KR');
      priceChangeRateEl.textContent = (cp>0?'+':'')+cr.toFixed(2)+'%';
      priceChangeEl.classList.toggle('up',   cp>0);
      priceChangeEl.classList.toggle('down', cp<0);
      priceChangeRateEl.classList.toggle('up',   cp>0);
      priceChangeRateEl.classList.toggle('down', cp<0);
      chartVolumeEl.textContent     = '거래량: '+Math.floor(v).toLocaleString('ko-KR');
    }

    function onTrade(m){
      const p = m.trade_price||0;
      const q = (m.trade_volume||0).toFixed(6).replace(/\.?0+$/,'');
      const t = m.trade_time||'';
      const up = m.ask_bid==='BID';
      const itm = document.createElement('div');
      itm.className = 'trade-item '+(up?'up':'down');
      itm.innerHTML = `<div>${p.toLocaleString('ko-KR')}</div><div>${q}</div><div>${t}</div>`;
      transactionListEl.appendChild(itm);
      transactionListEl.scrollTop = transactionListEl.scrollHeight;
    }

    function onOrderbook(m){
      orderbookListEl.innerHTML = '';
      const u = m.orderbook_units||[];
      u.slice().reverse().slice(0,45).forEach(x=>addOBRow(x.ask_price,x.ask_size,'ask'));
      u.slice(0,45).forEach(x=>addOBRow(x.bid_price,x.bid_size,'bid'));
      if(!scrolled){
        setTimeout(()=>{
          orderbookListEl.scrollTop = orderbookListEl.scrollHeight/2 - orderbookListEl.clientHeight/2;
          scrolled = true;
        },100);
      }
    }
    function addOBRow(price,qty,type){
      const row = document.createElement('div');
      row.className = 'orderbook-item '+(type==='ask'?'ask-bg':'bid-bg');
      row.innerHTML = `<div class="orderbook-price ${type==='ask'?'down':'up'}" data-price="${price}">${price.toLocaleString('ko-KR')}</div><div class="orderbook-qty">${parseFloat(qty).toFixed(6).replace(/\.?0+$/,'')}</div>`;
      orderbookListEl.appendChild(row);
      row.querySelector('.orderbook-price').onclick = ()=>{
        if(tabBuyEl.classList.contains('active')){
          document.getElementById('buyPrice').value = price;
          calcBuyTotal();
        } else {
          document.getElementById('sellPrice').value = price;
          calcSellTotal();
        }
      };
    }

    // 미니 차트
    function fetchMiniChart(){
      fetch(`https://api.bithumb.com/public/candlestick/${symbol}_${market}/24h`)
        .then(r=>r.json()).then(j=>{
          if(j.status!=='0000') return;
          const closes = j.data.slice(-30).map(v=>parseFloat(v[2]));
          drawMiniChart(closes);
        });
    }
    const mcCanvas = document.getElementById('miniChart');
    const mcCtx    = mcCanvas.getContext('2d');
    function drawMiniChart(d){
      const w = mcCanvas.width, h = mcCanvas.height;
      mcCtx.clearRect(0,0,w,h);
      if(d.length<2) return;
      const mn = Math.min(...d), mx = Math.max(...d), rg = mx-mn||1;
      mcCtx.beginPath();
      d.forEach((v,i)=>{
        const x = (i/(d.length-1))*w;
        const y = h-((v-mn)/rg*h);
        i===0?mcCtx.moveTo(x,y):mcCtx.lineTo(x,y);
      });
      mcCtx.strokeStyle = '#ff6600';
      mcCtx.lineWidth   = 1.5;
      mcCtx.stroke();
    }
    fetchMiniChart();
    setInterval(fetchMiniChart,30000);

    // 캔들 차트 (Financial)
    const { CandlestickController, CandlestickElement, TimeScale, LinearScale, Tooltip, Legend } = ChartFinancial;
    Chart.register(CandlestickController, CandlestickElement, TimeScale, LinearScale, Tooltip, Legend);

    function initCandleChart(){
      fetch(`https://api.bithumb.com/v1/candles/minutes/1?market=${market}-${symbol}&count=30`)
        .then(r=>r.json()).then(j=>{
          const arr = (j.data||[]).reverse();
          const data = arr.map(d=>({
            x: new Date(d.candle_date_time_kst),
            o: +d.opening_price,
            h: +d.high_price,
            l: +d.low_price,
            c: +d.trade_price
          }));
          const ctx = document.getElementById('candleChart').getContext('2d');
          window.candleChart = new Chart(ctx,{
            type: 'candlestick',
            data: { datasets: [{ label:`${symbol}/${market} (1분)`, data }] },
            options:{
              scales:{
                x:{ type:'time', time:{ unit:'minute', tooltipFormat:'HH:mm' } },
                y:{ beginAtZero:false }
              },
              plugins:{ legend:{ display:false } }
            }
          });
        });
    }
  </script>
</body>
</html>
