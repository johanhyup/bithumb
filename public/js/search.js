let currentMarket = 'KRW';
let coinData = [];
let marketNames = {};
let searchKeyword = '';
let currentSort = { key: '', direction: '' };


const coinNames = {
'BTC': '비트코인',
'ETH': '이더리움'
};

document.getElementById('openSearcha').addEventListener('click', () => {
document.getElementById('modalBackdrop').classList.add('active');
document.getElementById('searchInput').value = '';
searchKeyword = '';
renderSearchResults('');
document.getElementById('searchInput').focus();
});

document.getElementById('modalBackdrop').addEventListener('click', e => {
if (e.target === document.getElementById('modalBackdrop')) {
    document.getElementById('modalBackdrop').classList.remove('active');
}
});

document.getElementById('searchInput').addEventListener('input', e => {
searchKeyword = e.target.value.trim().toLowerCase();
renderSearchResults(searchKeyword);
renderCoinList();
});

function switchMarket(market) {
currentMarket = market;
document.getElementById('tabKRW').classList.toggle('bth-tab-active', market === 'KRW');
document.getElementById('tabBTC').classList.toggle('bth-tab-active', market === 'BTC');
fetchCoinData().then(fetchCoinData);
}

function fetchMarketNames() {
    return fetch(`https://api.bithumb.com/v1/market/all?isDetails=false`)
    .then(res=>res.json())
    .then(json=>{
        marketNames = {};
        json.forEach(item=>{
        let [_, sym] = item.market.split('-');
        marketNames[sym] = item.korean_name;
        });
    });
}


function fetchCoinData() {
return fetch(`https://api.bithumb.com/public/ticker/ALL_${currentMarket}`)
    .then(res => res.json())
    .then(json => {
    if (json.status === '0000') {
        const data = json.data;
        delete data.date;
        coinData = Object.keys(data).map(sym => {
        if (!data[sym].closing_price) return null;
        return {
            symbol: sym,
            name: marketNames[sym] || sym,
            price: parseFloat(data[sym].closing_price),
            open: parseFloat(data[sym].opening_price),
            high: parseFloat(data[sym].max_price),
            low: parseFloat(data[sym].min_price),
            close: parseFloat(data[sym].closing_price),
            change: parseFloat(data[sym].fluctate_24H),
            changeRate: parseFloat(data[sym].fluctate_rate_24H),
            volume: parseFloat(data[sym].acc_trade_value_24H)
        };
        }).filter(Boolean);

        if (currentSort.key) {
        sortBy(currentSort.key, currentSort.direction);
        } else {
        renderCoinList();
        }
    }
    });
}

function renderCoinList() {
const list = document.getElementById('coinList');
list.innerHTML = '';
const filtered = searchKeyword
    ? coinData.filter(c => c.name.toLowerCase().includes(searchKeyword) || c.symbol.toLowerCase().includes(searchKeyword))
    : coinData;

filtered.forEach(c => {
    const isUp = c.change > 0, isDown = c.change < 0;
    const cls = isUp ? 'up' : isDown ? 'down' : '';
    const el = document.createElement('div');
    el.className = 'coin-item';
    el.innerHTML = `
    <a href="trading.php?symbol=${c.symbol}&market=${currentMarket}">
        <div class="coin-item__chart">${generateCandleSVG(c)}</div>
        <div class="coin-item__name">${c.name}<br><span class="coin-item__symbol">${c.symbol}</span></div>
        <div class="coin-item__current ${cls}">${c.close.toLocaleString()}</div>
        <div class="coin-item__change ${cls}">${(c.changeRate>0?'+':'')+c.changeRate.toFixed(2)}%</div>
        <div class="coin-item__volume">${Math.floor(c.volume/1e6).toLocaleString()} 백만</div>
    </a>`;
    list.appendChild(el);
});
}

function renderSearchResults(query) {
const container = document.getElementById('searchResults');
container.innerHTML = '';
const filtered = coinData.filter(c => c.name.toLowerCase().includes(query) || c.symbol.toLowerCase().includes(query));
filtered.forEach(c => {
    const div = document.createElement('div');
    div.className = 'search-item';
    div.textContent = c.name;
    div.addEventListener('click', () => {
    window.location.href = `trading.php?symbol=${c.symbol}&market=${currentMarket}`;
    });
    container.appendChild(div);
});
}

function toggleSort(key) {
if (currentSort.key === key) {
    currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
} else {
    currentSort.key = key;
    currentSort.direction = 'asc';
}
sortBy(currentSort.key, currentSort.direction);
}

function sortBy(key, direction) {
coinData.sort((a, b) => {
    let valA = a[key], valB = b[key];
    if (typeof valA === 'string') valA = valA.toLowerCase(), valB = valB.toLowerCase();
    if (valA < valB) return direction === 'asc' ? -1 : 1;
    if (valA > valB) return direction === 'asc' ? 1 : -1;
    return 0;
});
renderCoinList();
}






function generateCandleSVG(c) {
    const w = 50, h = 20;

    // 유효성 체크: 숫자가 아니면 기본값으로 대체
    const open = isNaN(c.open) ? c.price : c.open;
    const close = isNaN(c.close) ? c.price : c.close;
    const high = isNaN(c.high) ? Math.max(open, close) : c.high;
    const low = isNaN(c.low) ? Math.min(open, close) : c.low;

    const range = (high - low) || 1;

    // y좌표 계산
    const yH = 0, yL = h;
    const yO = h - (open - low) / range * h;
    const yC = h - (close - low) / range * h;
    const rectY = Math.min(yO, yC);
    const rectH = Math.max(Math.abs(yC - yO), 1);

    const color = close > open ? 'red' : close < open ? 'blue' : 'gray';

    return `
    <svg width="${w}" height="${h}" xmlns="http://www.w3.org/2000/svg">
        <line x1="${w / 2}" y1="${yH}" x2="${w / 2}" y2="${yL}" stroke="${color}" stroke-width="1"/>
        <rect x="${w / 2 - 2}" y="${rectY}" width="4" height="${rectH}" fill="${color}"/>
    </svg>`;
}





fetchMarketNames().then(fetchCoinData);
setInterval(fetchCoinData, 5000);