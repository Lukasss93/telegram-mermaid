<img src="./.assets/cover.png" alt="logo"/>

# Telegram Bot: Mermaid Generator

[![link](https://img.shields.io/badge/bot-%40newmermaidbot-blue)](https://t.me/newmermaidbot)
![status](https://img.shields.io/badge/status-online-green)
[![link](https://img.shields.io/badge/news-%40LKS93C-blue)](https://t.me/LKS93C)
[![link](https://img.shields.io/badge/support-%40Lukasss93Support-orange)](https://t.me/Lukasss93Support)
![GitHub](https://img.shields.io/github/license/Lukasss93/telegram-mermaid)

>  Create diagrams and visualizations using text and code. Powered by mermaid.js.

## 🛠 Built with

- Programming language: PHP 8.3
- Language framework: [Laravel](https://github.com/laravel/laravel)
- Bot framework: [Nutgram](https://github.com/SergiX44/Nutgram)

## 🛡 Requirements

- Apache / nginx
- SSL support
- PHP ≥ 8.1
- MariaDB ≥ 10.2.3 or Postgresql ≥ 9.5 or SQLite with JSON1 extension
- Crontab (to update cached statistics)
- GIT

## 🗃️ Flow chart
![flow](.assets/flow/flow.png)

## 🚀 First deploy

0. `cd <vhost-folder>`
1. `git clone https://github.com/<username>/telegram-mermaid.git`
2. `cd telegram-mermaid`
3. `cp .env.example .env`
4. Edit the `.env` file with your preferences
5. `wget https://getcomposer.org/download/latest-2.x/composer.phar`
6. `php composer.phar install`
7. `sudo chmod -R 775 bootstrap/`
8. `sudo chmod -R 775 storage/`
9. `php artisan migrate`
10. `php artisan nutgram:register-commands`
11. `php artisan nutgram:hook:set https://<domain>.<tls>/hook`

## 🌠 Continuous deployment
This project will be updated in production at every pushed tag to master branch.<br>
Check this github workflow: [deploy.yml](.github/workflows/deploy.yml)

## 📃 Changelog
Please see the [changelog.md](changelog.md) for more information on what has changed recently.

## 🏅 Credits
- [Luca Patera](https://github.com/Lukasss93)
- [All Contributors](https://github.com/Lukasss93/telegram-mermaid/contributors)

## 📖 License
Please see the [LICENSE.md](LICENSE.md) file for more information.
