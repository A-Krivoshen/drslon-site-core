# krivoshein.site — шпаргалка по устройству сайта

> Для себя: «где что лежит, как править, что не ломать».  
> Актуально на **июнь 2026**, плагин **drslon-site-core v0.3.0**.

---

## 1. Главная идея (одним абзацем)

Сайт **krivoshein.site** — WordPress с темой `drslon-blog-theme`.  
Вся «умная» логика (шорткоды, ACF-экраны, прайс, лендинги услуг) вынесена в плагин:

```
/wp-content/plugins/drslon-site-core-main/
```

**Git-репозиторий плагина:** `git@github.com:A-Krivoshen/drslon-site-core.git` (ветка `main`).

Страницы в редакторе WordPress почти пустые — там только **один шорткод**.  
Текст и блоки редактируются либо в **ACF-экранах** в админке, либо в **PHP/CSS** плагина.

---

## 2. Карта страниц → шорткод → где править

| Страница | URL | ID | Шорткод в редакторе | Где править контент |
|----------|-----|----|---------------------|---------------------|
| Главная | `/` | 17 | `[krv_services_landing]` | **Лендинг услуг** (ACF) |
| Сервисы | `/servisy/` | 6202 | `[krv_services_pages_showcase]` | **Витрина сервисов** (ACF) |
| Партнёры | `/partnery/` | 9584 | `[krv_partners_grid]` | **Партнёры** (ACF) + CPT `partner` |
| **Прайс** | `/prays-list/` | 9772 | `[krv_price_list]` | **Прайс-лист** (ACF) + `price-list-widget.php` |
| Контакты | `/contacts/` | 75 | (блоки темы) | Редактор страницы + баннер из плагина |
| Инструменты | whois, dns, speed test… | см. ниже | `[krv_service_page]` | **Сервисные страницы** (ACF) |

### Инструменты (шорткод `[krv_service_page]`)

| ID | Страница |
|----|----------|
| 6186 | Whois |
| 6204 | Информация о домене |
| 7287 | Punycode |
| 7304 | DNS Lookup |
| 7323 | Whois lookup |
| 7352 | Crontab |
| 7369 | Firewall |
| 7459 | Сетевые маски |
| 7529 | Speed test |
| 9051 | Site checker |

В редакторе каждой такой страницы есть **синяя подсказка** — куда идти в админке.

---

## 3. ACF-экраны в админке WordPress

Путь: **WP Admin → левое меню** (пункты плагина).

| Пункт меню | Slug (техн.) | Что настраивает |
|------------|--------------|-----------------|
| Лендинг услуг | `krv-services-landing` | Главная: аватар, имя, услуги, цены |
| Витрина сервисов | `krv-services-showcase` | Страница «Сервисы» |
| Сервисные страницы | `krv-service-pages` | Интро для инструментов |
| Партнёры | `krv-partners` | Заголовок витрины партнёров |
| **Прайс-лист** | `krv-price-list` | Hero, trust strip, дисклеймер |

**Важно:** данные ACF хранятся с `post_id` = slug экрана (не `option`). Это сделано специально — иначе админка выглядит пустой.

---

## 4. Прайс-лист `/prays-list/` — как устроен

### 4.1 Файлы

| Файл | Назначение |
|------|------------|
| `includes/price-list-widget.php` | HTML страницы + шорткод `[krv_price_list]` |
| `includes/price-list-acf.php` | ACF-поля (hero, trust, disclaimer) |
| `assets/css/price-list-widget.css` | Стили |
| `assets/js/price-list-widget.js` | Sticky nav, табы цен, mobile CTA |

### 4.2 Структура страницы (сверху вниз)

```
Hero (заголовок + кнопки + панель «Не знаете что нужно?»)
  ↓
Trust strip (3 пункта доверия — из ACF)
  ↓
Sticky nav: Сценарии · Пакеты · Цены · FAQ
  ↓
#krv-scenarios — 4 карточки сценариев + строка лендингов (wordpress/vps/direct/bots)
  ↓
#krv-packages — 3 пакета (диагностика / ремонт / поддержка)
  ↓
#krv-prices — табы: Сайты | Боты | Техника | Реклама | Поддержка | Формы
  ↓
«Что обычно входит в работу» (теги технологий)
  ↓
«Как проходит работа» (4 шага)
  ↓
#krv-faq — accordion
  ↓
Нижний CTA «Написать по задаче»
  ↓
Дисклеймер (из ACF)
  ↓
Mobile CTA (только на телефоне): Диагностика · TG · MAX
```

### 4.3 Что править где

| Хочу изменить… | Куда идти |
|----------------|-----------|
| Бейдж, заголовок hero, lead, trust, дисклеймер | **Прайс-лист** (ACF) |
| Цены, услуги, FAQ, сценарии, пакеты | `includes/price-list-widget.php` |
| Внешний вид (цвета, отступы) | `assets/css/price-list-widget.css` |
| Поведение табов / sticky / mobile CTA | `assets/js/price-list-widget.js` |

### 4.4 Воронка конверсии (куда ведут кнопки)

| Кнопка | Куда |
|--------|------|
| Обсудить задачу | `/contacts/?utm_…&utm_campaign=general#krv-contact-block` |
| Заказать диагностику | `/contacts/?…&topic=diagnostic#krv-contact-block` |
| Обсудить ремонт | `/contacts/?…&topic=repair#krv-contact-block` |
| Запросить поддержку | `/contacts/?…&topic=support#krv-contact-block` |
| Telegram | `t.me/DrSlon` + UTM |
| MAX | `krivoshein.site/max` → редирект на max.ru (новая вкладка) |
| Лендинги wordpress/vps/direct/bots | Поддомены + UTM `utm_medium=prays-list` |

На странице **Контакты** при `?topic=diagnostic|repair|support` показывается **контекстный баннер** (`includes/contacts-topic-banner.php`).

---

## 5. Лендинги на поддоменах

| Поддомен | Назначение |
|----------|------------|
| wordpress.krivoshein.site | Сайты WordPress |
| vps.krivoshein.site | Серверы, Nginx, Docker |
| direct.krivoshein.site | Яндекс Директ |
| bots.krivoshein.site | MAX/Telegram боты |

Общие токены дизайна: `assets/krv-landing-tokens.css` (цвет `#5181fe`).

---

## 6. MAX — короткая ссылка

**Проблема была:** `/max?utm_*` уводил на статью про MAX Autopost (WordPress redirect manager не понимал query string).

**Решение:** `includes/max-shortlink-bridge.php` — перехватывает `/max` и редиректит на профиль max.ru **даже с UTM**.

Кнопки MAX на прайсе: `target="_blank"`, без UTM на `/max`.

---

## 7. Кеш — когда и как сбрасывать

После правок прайса, ACF или плагина нужно сбросить кеш, иначе видишь старую версию.

```bash
cd /var/www/krivoshein.site/htdocs
wp eval 'Drslon_Cache_Purge_Bridge::purge_page_cache(9772);' --allow-root   # прайс
wp eval 'Drslon_Cache_Purge_Bridge::purge_page_cache(17);' --allow-root     # главная
wp eval 'Drslon_Cache_Purge_Bridge::purge_page_cache(75);' --allow-root     # контакты
wp eval 'Drslon_Cache_Purge_Bridge::purge_nginx_all();' --allow-root        # весь nginx redis
```

Модуль: `includes/cache-purge-bridge.php` — синхронизирует WP Fastest Cache и Nginx Redis.

При сохранении ACF автоматически чистится кеш нужной страницы.

---

## 8. Чего НЕ делать (частые ловушки)

| Нельзя | Почему |
|--------|--------|
| Вставлять весь HTML прайса в редактор WordPress | KSES вырежет `<style>` и сломает вёрстку |
| Использовать `wp_update_post()` для HTML прайса | То же самое — KSES |
| Править страницу 9772 в редакторе | Там только `[krv_price_list]` — контент в плагине |
| Дублировать `id` на якорях | Ломается навигация (#krv-prices и т.д.) |
| Вешать UTM на `/max` в кнопках | Раньше ломало редирект; сейчас bridge чинит, но кнопки без UTM — осознанно |

**Правильно:** правки в PHP/ACF/CSS → commit → push → purge cache.

---

## 9. Деплой изменений плагина

```bash
cd /var/www/krivoshein.site/htdocs/wp-content/plugins/drslon-site-core-main

# после правок
git add -A
git commit -m "описание"
git push origin main

# сброс кеша (см. раздел 7)
```

Версия плагина: `drslon-site-core.php` → `Version:` и `DRSLON_SITE_CORE_VERSION`.

---

## 10. История версий плагина (кратко)

| Версия | Что сделали |
|--------|-------------|
| 0.2.1 | Шорткод `[krv_price_list]`, страница прайса |
| 0.2.2–0.2.3 | Модули, CSS в отдельные файлы |
| 0.2.4 | `[krv_service_page]` для инструментов |
| 0.2.5 | Фикс ACF `post_id` — админка не пустая |
| 0.2.6 | Аватар через медиатеку |
| 0.2.7 | Воронка «диагностика» на прайсе |
| 0.2.8 | Bridge для `/max` |
| 0.2.9 | MAX в новой вкладке |
| **0.3.0** | UX-аудит прайса: sticky nav, табы, ACF, mobile CTA, trust, FAQ accordion |

---

## 11. Структура плагина (дерево файлов)

```
drslon-site-core-main/
├── drslon-site-core.php          # точка входа, версия
├── docs/
│   └── SITE-GUIDE.md             # этот файл
├── includes/
│   ├── price-list-widget.php     # прайс HTML
│   ├── price-list-acf.php        # ACF прайса
│   ├── contacts-topic-banner.php # баннер на контактах
│   ├── max-shortlink-bridge.php  # /max → max.ru
│   ├── acf-options-sync.php      # подсказки в редакторе + миграции ACF
│   ├── assets-loader.php         # CSS/JS по шорткодам
│   ├── cache-purge-bridge.php    # WPFC + Nginx purge
│   ├── service-page-registry.php
│   ├── legacy-arkai-child-functions.php  # старый код из темы
│   └── shortcodes/
│       ├── services-landing.php
│       ├── service-page-shell.php
│       ├── services-pages-showcase.php
│       ├── clients-grid.php
│       └── partners-grid.php
└── assets/
    ├── css/
    │   ├── price-list-widget.css
    │   ├── contacts-topic-banner.css
    │   ├── services-landing.css
    │   └── ...
    └── js/
        └── price-list-widget.js
```

---

## 12. Быстрые ответы «что делать если…»

| Ситуация | Действие |
|----------|----------|
| Поменять заголовок на прайсе | ACF → Прайс-лист |
| Поменять цену «Лендинг от 50 000» | `price-list-widget.php` → блок «Услуги и цены» |
| Не вижу изменений на сайте | Purge cache (раздел 7) |
| Кнопка MAX ведёт не туда | Проверить `max-shortlink-bridge.php` и Clearfy redirect |
| Пустой экран ACF в админке | Проверить `post_id` = slug; см. `acf-options-sync.php` |
| Нужен новый шорткод-страница | 1) страница с шорткодом 2) ACF экран 3) hint в `acf-options-sync.php` 4) assets-loader |

---

## 13. Дизайн-система (общее)

- Акцент: `#5181fe`
- Шрифт: Inter
- Радиусы карточек: 20–24px
- Стиль: карточки, pill-кнопки, мягкие тени

Прайс и лендинги на поддоменах визуально согласованы.

---

## 14. Что можно сделать потом (не срочно)

- [ ] Вынести в ACF ещё блоки прайса (пакеты, FAQ, сценарии)
- [ ] Добавить `consulting.krivoshein.site` в прайс (если нужно)
- [ ] Автотесты на шорткоды (как на python.domaintools.site)

---

*Файл создан для навигации по проекту. При крупных изменениях — обновляй разделы 4, 10 и 12.*