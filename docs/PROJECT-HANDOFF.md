# PROJECT-HANDOFF

Единый контекст krivoshein.site для следующего агента.
Читать этот файл в начале сессии и сверять его с GitHub.
После завершённого этапа обновлять этот же файл, не заводить вторую историю.

Источник кода: GitHub, ветка `main`.
Этот файл: зафиксированные решения и состояние на 14 ноября 2026, после выкладки.
Секретов здесь нет. Пароли, токены и ключи сюда не писать.

`docs/SITE-GUIDE.md` устарел (июль 2026, плагин 0.4.2, тема 0.6.0, деплой «коммитом внутри корня сайта»). Корни сайтов не являются git-репозиториями. Актуальный порядок деплоя в разделе 6.

## 1. Бизнес

Владелец: ИП Кривошеин Алексей Сергеевич (Dr.Slon).
Сайт: https://krivoshein.site/
Работа официальная, по договору.

Услуги, у каждой свой лендинг:

- WordPress: доработка, блоки, сопровождение.
- VPS: Linux, Nginx, PHP, Docker, SSL, файрвол, бэкапы, запуск.
- Лендинги.
- Яндекс Директ.
- Боты Telegram и MAX.
- Сайты и сервисы на Flask.
- Подготовка сайта к нейропоиску и ассистентам (AI-ready).

На сайте есть список компаний, с которыми владелец работал.
Это не портфолио сделанных сайтов. Задачи разные, часть работ закрытая.
Не удалять компании из-за недоступности их сайтов, не выдумывать кейсы, не приписывать владельцу разработку всех этих сайтов и не раскрывать закрытые сведения.

Блог технический: WordPress, Linux, Ubuntu, Nginx, Docker, DevOps, VPS, Python, Flask, Директ, Telegram, MAX, AI, DNS, домены.
Задача текущего этапа: вести читателя к подходящей услуге, не превращая статьи в рекламу.

## 2. Архитектура

- krivoshein.site: WordPress, PHP, Nginx, тема `drslon-blog-theme`, логика услуг в плагине `drslon-site-core`.
- Шесть лендингов: статические HTML, CSS, JavaScript.
- flask.krivoshein.site: Flask, Gunicorn, systemd, Nginx. Код приложения на сервере отдельно от статических лендингов.
- Прайс на главном сайте: ACF options, post id `krv-price-list`, поле `price_tabs`. Живые цены лежат в `wp_options`, а не в тексте страниц.
- Брифы на лендингах и в формах главного сайта готовят текст в браузере. Заявку они не отправляют. Человек сам копирует текст в Telegram или MAX. Не называть подготовку текста отправкой заявки.
- Рекомендации Яндекс РСЯ остаются под статьёй. Коммерческий блок их не заменяет.
- Список компаний и юридические страницы этим этапом не менялись.

## 3. Девять репозиториев

Все на GitHub у A-Krivoshen, рабочая ветка `main`.
Локальная чистая копия для этой сессии: `optimization-2026-11-14/worktrees/` в каталоге проекта.
Старые каталоги (`wordpress-plugins/drslon-site-core`, `wordpress-themes/drslon-blog-theme`, `Landings_Krivoshein/*`, `flask-krivoshein` без git) грязные и отстают. Не сбрасывать их и не выкладывать с них.

| Репозиторий | Домен | HEAD на 14 ноября 2026 после этого этапа |
|---|---|---|
| [drslon-blog-theme](https://github.com/A-Krivoshen/drslon-blog-theme) | krivoshein.site, тема | `602fd76b5517fd10dec8325f4522c2f32de5d116` |
| [drslon-site-core](https://github.com/A-Krivoshen/drslon-site-core) | krivoshein.site, плагин | код `e976263`, схема `/ceny/` `7e2517b`; документ передачи коммитится следом |
| [landing-krivoshein-site](https://github.com/A-Krivoshen/landing-krivoshein-site) | landing.krivoshein.site | `b878257d15547d75b282aeaaaf14195128e94e2e` |
| [wordpress-krivoshein-site](https://github.com/A-Krivoshen/wordpress-krivoshein-site) | wordpress.krivoshein.site | `39cbfb6abd2f3897edfe7532eb74e2c764872941` |
| [vps-krivoshein-site](https://github.com/A-Krivoshen/vps-krivoshein-site) | vps.krivoshein.site | `88880696a55688869aea09c00a7814d94f53f3ec` |
| [direct-krivoshein-site](https://github.com/A-Krivoshen/direct-krivoshein-site) | direct.krivoshein.site | `ab3c6e07dd07049cb80835e11eb3be0950c0e722` |
| [bots-krivoshein-site](https://github.com/A-Krivoshen/bots-krivoshein-site) | bots.krivoshein.site | `221221551f4dd511f043587ecb4a0009daf6a27f` |
| [ai-ready-krivoshein-site](https://github.com/A-Krivoshen/ai-ready-krivoshein-site) | ai-ready.krivoshein.site | `208b2c5ac3bcb2572c802561287a526b0bea61f8` |
| [flask-krivoshein](https://github.com/A-Krivoshen/flask-krivoshein) | flask.krivoshein.site | `1f5a8ec43b2d8dc348aba9822245a96d7d35f457` |

Рабочее дерево Flask с origin: `flask-krivoshein-native`. Дерево `flask-krivoshein` без origin не использовать.

## 4. Production-домены

- https://krivoshein.site/
- https://landing.krivoshein.site/
- https://wordpress.krivoshein.site/
- https://vps.krivoshein.site/
- https://direct.krivoshein.site/
- https://bots.krivoshein.site/
- https://flask.krivoshein.site/
- https://ai-ready.krivoshein.site/

Прайс: https://krivoshein.site/ceny/
Старый путь `/prays-list/` открывает тот же прайс. Не удалять URL и не делать массовые редиректы.

## 5. Стек

- WordPress + PHP + Nginx. Тема 0.6.2, плагин 0.4.5.
- ACF Pro для прайса и части карточек. Посев `krv_price_list_seeded_v2` уже выполнен, поэтому правка PHP-дефолтов сама живые цены не меняет.
- Лендинги: статический HTML, свой `sw.js` (cache-first: после правки HTML нужно новое имя кеша).
- Flask: Gunicorn и systemd `flask-krivoshein.service`. Этот этап приложение не менял.
- Аналитика главного сайта: Яндекс Метрика `51907757`, подключается темой после согласия.
- Счётчики лендингов, уже стоят в коде: landing `111067070`, wordpress `110135020`, vps `110134054`, direct `110134518`, bots `110121601`, flask `113799998`, ai-ready `110327266`.
- Кеш HTML главного сайта: WP Fastest Cache.

## 6. Деплой

SSH-алиас: `beget`, пользователь `drslon`.
Исходники на сервере: `/home/drslon/src/<repo>` на `main`.
Flask-приложение: `/home/drslon/apps/flask-krivoshein`. Его этот этап не трогал.

Корни отдачи, это копии, не git:

- тема: `/var/www/krivoshein.site/htdocs/wp-content/themes/drslon-blog-theme`
- плагин: `/var/www/krivoshein.site/htdocs/wp-content/plugins/drslon-site-core`
- лендинг: `/var/www/<имя>.krivoshein.site/htdocs`

Порядок, которым выложен этот этап:

1. Сверить каждый заменяемый файл с родительским коммитом. Все 14 существующих файлов совпали. Два новых PHP-файла темы на сервере отсутствовали.
2. Снять копию файлов и строк прайса.
3. `git pull --ff-only origin main` в четырёх исходниках.
4. `rsync -rlt --checksum --chown=www-data:www-data` только по списку файлов, без `--delete`.
5. Выполнить миграцию цены через WP-CLI и сбросить Fastest Cache.

WP-CLI: `sudo -n -u www-data wp --path=/var/www/krivoshein.site/htdocs --skip-plugins=max-autopost,wptelegram,wp-linkedin-auto-publish,share-on-mastodon`.
Плагин `drslon-site-core` при этом не пропускать: в нём миграция и прайс.
Социальные плагины пропускать, чтобы служебная команда их не дёрнула.

## 7. Правила Git

- Публиковать только fast-forward в `main`. Force push запрещён.
- Не удалять и не перетирать чужие коммиты.
- Не выкладывать production из отстающей локальной копии.
- Коммиты короткие, на английском, в стиле `fix:` / `feat:`.
- Секреты не коммитить.
- Откатывать только затронутый релиз, не весь сайт до ноября.

## 8. Что уже реализовано до этого этапа

14 ноября 2026 OpenCode выложил CRO-релиз: 8 сайтов, 9 репозиториев, 20 коммитов, smoke-тесты.
Сохранены URL, SEO, дизайн, клиентские данные и инфраструктура.
На лендингах единые контакты, тематические брифы в браузере, Telegram и MAX как равные каналы, сохранение выбранной услуги и пакета, копирование текста брифа.
Отчёты того этапа лежат в `optimization-2026-11-14/docs/` (`AUDIT.md`, `CHANGES.md`, `QA.md`). Часть разделов описывает состояние до выкладки. Факт после неё: восемь сайтов уже были опубликованы.

## 9. Что в работе сейчас

Отдельной ветки «в работе» нет. Код этого этапа в `main` и на production.
Следующий агент продолжает от этого файла, а не от грязных локальных копий.

## 10. Что завершено этим этапом

Цена базовой настройки VPS приведена к «от 10 000 ₽» только для этой услуги.

- Лендинг VPS: title, Open Graph, Twitter, описания WebSite и ProfessionalService, герой, главная карточка. В HTML лендинга больше нет «15 000».
- Кнопки «Заказать настройку», которые открывают бриф, переименованы в «Обсудить задачу». Бриф по-прежнему только готовит текст.
- Под ценой ссылка на https://krivoshein.site/ceny/#vps . Скрипт прайса открывает вкладку по хешу `vps`, `krv-tab-*` или `krv-panel-*`.
- Карточка прайса «Настройка VPS» в ACF: было «от 15 000 ₽», стало «от 10 000 ₽». Строка `krv-price-list_price_tabs_2_items_0_price`.
- Карточка «Настройка сервера под WordPress» на wordpress-лендинге: «от 10 000 ₽» и ссылка на VPS-лендинг.
- В Schema.org каталога услуг добавлен Offer «Настройка VPS», `minPrice` 10000, RUB. Каталог снова выводится и на `/ceny/`: проверка слагa смотрела только `prays-list`, а живая страница называется `ceny`.

Не тронуты другие 15 000 ₽:

- карточка «ACF и кастомные блоки» в прайсе;
- FAQ «кастомных блоков за 15 000 ₽»;
- карточка ACF на wordpress-лендинге.

Коммерческий блок после подходящих статей. Шорткод `[krv_service_offer]` в `templates/single.html` после текста и до навигации. Статьи не переписывались. Блок считается при отдаче страницы.

Подбор в `inc/service-offer-match.php`:

- категория из карты услуги даёт 5;
- сильный тег даёт 5, слабый (dns, domain, ssl, ssh, beget, firstvds, selectel, acf, elementor) даёт 2;
- блок показывается, если лучший счёт не меньше 5;
- при равенстве побеждает основная рубрика The SEO Framework (`_primary_term_category`), иначе порядок flask, bots, direct, wordpress, ai, vps, landing;
- мета `_krv_service_offer`: пусто значит авто, `off` прячет блок, известный id услуги ставит её явно. Метабокс «Коммерческая рекомендация» на записи.
- Категория `linux` сама по себе блок не включает, чтобы инструкции про рабочий стол не получали предложение сервера. Тег `nginx` на такой записи уже включает VPS.

Куда ведёт блок: VPS, WordPress, Flask, Директ, боты, лендинги, AI-ready. Адреса в каталоге функции `krv_service_offer_catalog()`.

На выгрузке 687 опубликованных записей от 14 ноября 2026 блок подходил 540: wordpress 192, vps 184, ai 124, direct 23, flask 15, bots 1, landing 1. Остальные 147 без блока. Это не записано в посты и изменится вместе с рубриками. Проверка: `php tests/service-offer-match.test.php`.

Цель Метрики в коде: `blog_service_click`, параметры `service` (slug услуги) и `page` (pathname без query). Событие уходит только если `window.krvConsent.analytics === true` и `window.ym` уже функция. Второй счётчик не создаётся. Персональные данные не передаются. Кабинет Метрики не открывался: цель в интерфейсе Метрики не подтверждена.

Проверенные внутренние ссылки коммерческих страниц ложных 404 не дали. `/kontakty/` не является адресом сайта, страница контактов `/contacts/`. Короткие партнёрские адреса `/yandexcloud`, `/ultravds`, `/clo` оставлены как есть.

## 11. Что осталось

- В кабинете Метрики завести или проверить цель `blog_service_click`. Без доступа этого не утверждать. База владельца за месяц, не из API и не заявки: 2043 посетителя, 2564 визита, 8568 просмотров, 19 переходов в мессенджеры, 1 действие с телефоном, 2 с email.
- AI-ready: публичный лендинг говорит «от 10 000 ₽», а `README.md`, `llms.txt`, `llms-full.txt` и `agents.md` пишут аудит «от 15 000 ₽». Владелец это не решал. Не менять заодно с VPS.
- Старые записи типа `price` со странными заголовками не переименовывать без решения владельца: `/price/sozdanie-i-vedenie-reklamnyh-kampaniy-v-yandeks-direkt/`, `/price/tehnicheskaya-podderzhka-sayta/`, `/price/devops/`, `/price/veb-razrabotka/`.
- Запись в рубрике `business` про смету упоминает несколько услуг, но по рубрике и тегу блок не показывается. Так и задумано: текст статьи не сканируется. Нужную услугу можно поставить мета-полем.
- Юридические тексты, модель cookie и серверное хранение заявок требуют отдельного согласия. «Понятно» не считать согласием на аналитику.
- Лендинги direct, landing, bots, ai-ready и Flask визуально не переделывались: каналы и брифы там уже были.

## 12. Решения владельца

- Базовая настройка VPS стоит от 10 000 ₽. Это окончательно. Меняется только эта услуга, не любой ценник 15 000 ₽.
- Бриф не отправляет заявку. Telegram и MAX равны.
- Компании в списке не равны сделанным сайтам.
- Обратимые правки кода можно выкладывать с backup и откатом. Force push нельзя. Юридические документы, конфиденциальные данные и платежи без отдельного согласия не менять.
- Релиз 14 ноября 2026 сохраняется: URL, дизайн, клиентские данные, инфраструктура.

## 13. Что запрещено

- Force push, удаление чужих коммитов, выкладка из старой копии.
- Массовая замена 15 000 ₽.
- Переписывание статей, реклама в каждой записи, случайная услуга, SEO-спам, сотни сгенерированных страниц.
- Удаление URL, массовые редиректы, удаление виджета РСЯ.
- Тяжёлый новый JS ради блока услуги.
- Называть подготовку брифа отправкой заявки и обещать автоотправку.
- Хранить персональные данные заявок на сервере без согласия.
- Включать Метрику после отказа и слать в неё персональные данные.
- Дублировать уже существующие цели `cta_telegram`, `cta_max`, `contact_open`, `contact_phone_click`, `contact_email_click`, `brief_start`, `brief_prepare`, `brief_copy`.
- Выдумывать рост конверсии, кейсы, факты аудита и доступ к кабинету Метрики.
- Менять юридические документы и модель согласия «заодно».
- Проверять пачкой сайты компаний и снимать их с сайта из-за недоступности.

## 14. Известные проблемы

- Противоречие AI-ready 10 000 / 15 000, см. раздел 11.
- `SITE-GUIDE.md` описывает старый деплой.
- Service worker лендингов cache-first. Имена кеша VPS и WordPress сменены на `krv-vps-price-20261114` и `krv-wordpress-price-20261114`. HTTP-проверка видит новый HTML сразу. Браузер с уже установленным worker обновит страницу после обновления worker.
- Миграция цены ищет строку с заголовком ровно «Настройка VPS» и ценой с числом 15 000. Если заголовок не найден, флаг `krv_price_vps_base_10000` не ставится. Сейчас строка найдена, флаг равен `1`, прежняя цена записана в `krv_price_vps_base_10000_from`.
- Повторный посев прайса из PHP не обновляет ACF: опция `krv_price_list_seeded_v2` уже есть. Не чинить цену правкой только дефолта.

## 15. Коммиты production

Выложены 14 ноября 2026. Backup: `/home/drslon/backups/vps-offer-20261114T124319Z`.

| Репозиторий | Коммит | Смысл |
|---|---|---|
| drslon-blog-theme | `602fd76b5517fd10dec8325f4522c2f32de5d116` | блок услуги после подходящих записей |
| drslon-site-core | `e97626383c413cbe60675f1c241f5823f4d7b425` | базовая цена VPS 10 000 и вкладка прайса по хешу |
| drslon-site-core | `7e2517b94f584d0f895114551cc593f9682caed4` | каталог Schema.org на `/ceny/` |
| vps-krivoshein-site | `88880696a55688869aea09c00a7814d94f53f3ec` | лендинг VPS от 10 000 ₽ |
| wordpress-krivoshein-site | `39cbfb6abd2f3897edfe7532eb74e2c764872941` | серверная карточка WordPress совпадает с базовой ценой VPS |

Предыдущий общий backup CRO, не использовать его для отката только этого этапа: `/home/drslon/backups/cro-release-20261114T102100Z`.

## 16. Как проверить

HTTP-проверка 14 ноября 2026 прошла. Клик в браузере и мобильная вёрстка в этой сессии не прогонялись: инструмента браузера не было.

- https://vps.krivoshein.site/ : title «от 10 000 ₽», в HTML нет «15 000», три «Обсудить задачу», нет «Заказать настройку», есть ссылка на `/ceny/#vps`.
- https://wordpress.krivoshein.site/ : «Настройка сервера под WordPress» от 10 000 ₽ и ссылка на VPS. Карточка ACF остаётся от 15 000 ₽.
- https://krivoshein.site/ceny/ : карточка «Настройка VPS» от 10 000 ₽. «ACF и кастомные блоки» и FAQ про блоки остаются от 15 000 ₽. В JSON-LD есть Offer «Настройка VPS» с `minPrice` 10000. В `price-list-widget.js` есть `activateFromHash`.
- https://krivoshein.site/bezopasnost-sayta/ : один блок на wordpress.krivoshein.site, цель `blog_service_click` только при согласии, блок РСЯ на месте.
- https://krivoshein.site/nginx-block-bad-bots/ : один блок на vps.krivoshein.site.
- https://krivoshein.site/ustanovka-rossiyskih-kornevyh-sertifikatov-v-ubuntu/ : блока нет, РСЯ на месте.
- Главная, `/contacts/`, bots-лендинг открываются. В логах Nginx и PHP после выкладки не было `PHP Fatal`, `Parse error`, `Uncaught`.
- Матчер: `php tests/service-offer-match.test.php` в репозитории темы печатает `service-offer-match: ok`.

## 17. Как откатить только этот релиз

Каталог `/home/drslon/backups/vps-offer-20261114T124319Z`.

1. Вернуть файлы из `files/` на те же пути от root `www-data`. Новые `inc/service-offer.php` и `inc/service-offer-match.php` удалить: в списке backup у них `existed: false`.
2. Вернуть цену из `prices-before.json` для строки с заголовком «Настройка VPS» (было «от 15 000 ₽») и удалить опции `krv_price_vps_base_10000` и `krv_price_vps_base_10000_from`.
3. Не трогать цену «ACF и кастомные блоки».
4. `wp fastest-cache clear all` той же командой WP-CLI, что в разделе 6.
5. Исходники в `/home/drslon/src` откатывать отдельным `git revert` публичных коммитов, без force push. Пока исходник новее корня сайта, следующая выкладка снова принесёт этот релиз.

## Следующий этап

Подтвердить цель `blog_service_click` в кабинете Метрики и решить, какая цена AI-ready верная: 10 000 на лендинге или 15 000 в `llms` и README. До решения владельца эти два места не выравнивать.
