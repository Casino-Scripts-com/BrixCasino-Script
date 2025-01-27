const {promisify} = require('util');
const requestify = require('requestify');
const RedisClient = require('redis').createClient();
const getAsync = promisify(RedisClient.get).bind(RedisClient);
const setAsync = promisify(RedisClient.set).bind(RedisClient);

exports.cvars = {timer  : 0}
exports._i = 0;
exports._now = 1;
exports._data = [1];

exports.reload = function() {
    setTimeout(() => {
		return this.init();
	}, 2000);
}

exports.init = async() => {
    let res = await this.request('crash/init');
    if(!res) return this.reload();

    this.log('Current game #' + res.id);

    this.cvars.timer = res.timer;
    this.log('Set `timer` cvar to ' + res.timer);

    console.log(res.status);

    if(res.status == 0) this.startTimer();
    if(res.status == 1) this.startSlider();
    if(res.status == 2) this.newGame();
}

exports.startTimer = async() => {
    this.log('`timer` is ' + this.cvars.timer + ' seconds!');
    let currentTime = this.cvars.timer*10;
    let timer = setInterval(() => {
        if(currentTime <= 0)
        {
            clearInterval(timer);
            this.startSlider();
            return;
        }
        currentTime--;
        // publish
        this.emit({
            type : 'timer',
            value : (currentTime/10).toFixed(1)
        });
    }, 100);
}

exports.startSlider = async() => {
    let res = await this.request('crash/slider');
    if(!res) return this.reload();
    
    if(typeof this.animateInterval != 'undefined') clearInterval(this.animateInterval);

    await setAsync('cashout', 1); // accept cashout

    this.float = parseFloat(res); // float
    this._i = 0;
    this._now = 0;
    this._data = [];
    this.options.colors[0] = 'rgba(43, 43, 43, 0.6)';

    this.animateInterval = setInterval(async() => {
        this._i++;
        this._now = parseFloat(Math.pow(Math.E, 0.00006*this._i*1000/20));
        let crashed = false;
        if(this._now > this.float)
        {
            // etc code
            clearInterval(this.animateInterval);
            this.options.colors[0] = this._colors.red;
            this._now = this.float;
            crashed = true;
            await setAsync('cashout', 0) // disable cashout
            setTimeout(() => {
                this.newGame();
            }, 3000);
        } else {
            this.options.colors[0] = this.getColors(this._now);
        }

        this._data.push([this._i, this._now]);
        this.options.xaxis.max = Math.max(this._i, 5000/2000);
        this.options.yaxis.max = Math.max(this._now, 2);

        await setAsync('float', this._now.toFixed(2));

        this.emit({
            type : 'slider',
            data : this._data,
            options : this.options,
            float : parseFloat(this._now.toFixed(2)),
            crashed : crashed,
            color : this.getColors(this._now)
        });
    }, 50)
}

exports.newGame = async() => {
    let res = await this.request('crash/newGame');
    if(!res) return this.reload();

    if(res.success) 
    {
        this.log('Current game #' + res.id);
        this.startTimer();
    }
}

exports.getFrame = (float) => {
    return new Promise((res, rej) => {
        let i = 0, now = 0;
        while(now < float)
        {
            i++;
            now = parseFloat(Math.pow(Math.E, 0.00006*i*1000/20));
        }
        return res(i);
    });
}

exports.request = (path, body) => {
    return new Promise(async(response, reject) => {
        requestify.post('https://zubrix.ru/api/' + path, body || {})
        .then((res) => {
            return response(JSON.parse(res.body));
        }, (err) => {
            console.log('[API] error with request to /api/' + path);
            console.log(err.body);
            return response(false);
        });
    });
}

exports.log = (log) => console.log('[CRASH] ' + log);

exports.emit = (json) => RedisClient.publish('crash', JSON.stringify(json));

exports._colors = {
    one: 'rgb(104, 165, 254)',
    two: 'rgb(150, 50, 196)',
    three: 'rgb(255, 95, 234)',
    four: '#91b647',
    five: 'rgb(252, 203, 120)',
    red: '#fe4747',
    border: '#FFCC00',
    text: '#ffe169'
}

exports.options = {
    xaxis : {
        max : Math.max(1, 5000/2000),
        min : 1,
		color: "rgba(43, 43, 43, 0.6)",
        ticks : {
            show : false
        }
    },
    yaxis : {
        max : Math.max(1.003004504503377*1, 2),
        min : 1,
		color: "rgba(43, 43, 43, 0.6)"
    },
    series: {
        lines: { fill: true },
    },
    grid: {
        borderColor: "rgba(43, 43, 43, 0.6)",
		borderWidth: {
			top: 0,
			right: 0,
			left: 2,
			bottom: 2
		}
    },
    colors : ['#ffcc0073']
}

exports.getColors = function(n)
{
    if(n > 8.49) return this._colors.five;
    if(n > 6.49) return this._colors.four;
    if(n > 3.99) return this._colors.three;
    if(n > 1.99) return this._colors.two;
    if(n < 1.2) return '#fe4747';
    return this._colors.one;
}

this.init();