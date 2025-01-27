const TelegramBot = require('node-telegram-bot-api');
const mysql = require('mysql');

const bot = new TelegramBot("6345622919:-fKux-gkk", {
    polling: {
        interval: 300,
        autoStart: true,
        params: {
            timeout: 10
        }
    }
})
const client = mysql.createPool({
    connectionLimit: 50,
    host: "localhost",
    user: "",
    database: "",
    password: ""
});

bot.on('message', async msg => {

    let chat_id = msg.chat.id,
        text = msg.text ? msg.text : '',
        settings = await db('SELECT * FROM settings ORDER BY id DESC');

    if(text.toLowerCase() === '/start') {
        return bot.sendMessage(chat_id, `Это официальный бот ZUBRIX в Telegram. Для привязки Telegram аккаунта, требуется ввести команду, которая указана <a href="https://zubrix.ru/bonus">zubrix.ru/bonus</a>.`, {
            parse_mode: "HTML",
            disable_web_page_preview: true
        });
    }

    else if(text.toLowerCase().startsWith('/bind')) {
        let unique_id = text.split("/bind ")[1] ? text.split("/bind ")[1] : 'undefined';
        let user = await db(`SELECT * FROM users WHERE unique_id = '${unique_id}'`);
        let check = await db(`SELECT * FROM users WHERE tg_id = ${chat_id}`);
        let subs = await bot.getChatMember('@Zubrix_BK', chat_id).catch((err) => {});

        if (!subs || subs.status == 'left' || subs.status == undefined) {
            return bot.sendMessage(chat_id, `Вы не подписаны на <a href="https://t.me/Zubrix_BK">канал</a>!`, {
                parse_mode: "HTML",
                disable_web_page_preview: true
            });
        }
        if(user.length < 1) return bot.sendMessage(chat_id, 'Мы не нашли этого пользователя', {
            parse_mode: "HTML"
        });
        if(check.length >= 1) return bot.sendMessage(chat_id, 'Этот аккаунт уже привязан!');
        if(user[0].tg_bonus_use == 1) return bot.sendMessage(chat_id, 'Пользователь уже получил награду');

        await db(`UPDATE users SET tg_id = ${chat_id} WHERE unique_id = '${unique_id}'`);
        await db(`UPDATE users SET tg_id = ${chat_id} WHERE tg_bonus = '1'`);
        return bot.sendMessage(chat_id, `Ваш аккаунт успешно привязан!`);
    }
});

function db(databaseQuery) {
    return new Promise(data => {
        client.query(databaseQuery, function (error, result) {
            if (error) {
                console.log(error);
                throw error;
            }
            try {
                data(result);

            } catch (error) {
                data({});
                throw error;
            }

        });

    });
    client.end()
}
