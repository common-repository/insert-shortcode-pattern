﻿=== Plugin Name ===
Contributors: 3vweb
Donate link: http://3v-web.ru/donate/
Tags: shortcode, шаблон, PHP, javascript, Html, footer, добавление PHP кода, корректная вставка javascript
Requires at least: 4.6.1
Tested up to: 5.2.2
Stable tag: 1.1.1
Requires PHP: 5.2.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Шаблонный текст вставляемый на страницу при помощи шорткода. HTML теги, PHP код

== Description ==

Этот плагин позволяет создавать шаблоны и вставлять заданный текст на страницу при помощи использования короткого кода (шорткода). В качестве места размещения можно указать подвал сайта (footer), либо в месте вставки короткого кода (шорткода) на странице. В качестве шаблона можно использовать любые HTML теги (в т.ч. javascript) без нарушения целостности, а также PHP код.

== Installation ==

1. Активируйте плагин через меню Плагины.
2. Используйте Настройки->Insert ShortCode Pattern для настройки плагина

== Frequently Asked Questions ==

= Как добавить PHP код в шаблон =

PHP кода вставляется в стандартном виде <?php /*Ваш код*/ ?>, как если бы создавали обычный PHP фаил.

= В каком случае шаблон будет добавлен в footer (подвал) сайта =

Если задать в настройках место расположения «В подвале сайта», то указанный шаблон будет добавлен только на странице, где Вы вставили шорткод. Это может пригодится если Вы вставляете javascript код (чтоб не затормаживать загрузку страницы), который должен отображаться только на отдельных страницах.

== Screenshots ==

1. Визуальное оформление настройки плагина.
2. Дополнительное ссылка для навигации «Insert ShortCode Pattern» в основных настройках.
3. Добавление шорткода в стандартном редакторе

== Changelog ==

= 1.0 =
* Первая версия в репозитрии

= 1.1 =
* Интернационализация плагина

= 1.1.1 =
* Добавлен ru_RU

== Upgrade Notice ==

= 1.0 =
Первая версия в репозитрии

= 1.1 =
Интернационализация плагина

= 1.1.1 =
Добавлен ru_RU
