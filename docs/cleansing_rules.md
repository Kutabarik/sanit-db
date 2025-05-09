# Как будут работать, храниться и добавляться правила очистки в библиотеке

## Хранение правил
Правила очистки будут храниться в конфигурационном файле (`rules.json`). В этом файле описывается:
- Таблица, которая проверяется.
- Поля, для которых есть правила.
- Сами правила — тип проверки (дубликаты, формат, мусор и т.д.) и параметры (например, регулярное выражение для проверки формата).



## Загрузка правил при запуске анализа
При запуске работы библиотеки, конфигурационный файл загружается и преобразуется в набор объектов внутри библиотеки.  
Каждое правило превращается в отдельный **анализатор**, который отвечает за конкретную проверку.



## Выполнение правил
Библиотека подключается к базе данных, выбирает нужную таблицу и начинает выполнять все проверки, описанные в правилах:
- Проверка дублей по указанным полям.
- Проверка корректности форматов (email, телефон и т.д.).
- Поиск мусорных данных (например, строки типа "test", "123", "xxx").

Результаты всех проверок собираются в **единый отчет** — это список строк из таблицы с описанием каждой найденной проблемы.



## Отображение результатов
- В консольном режиме — отчет выводится в текстовой таблице прямо в терминал.
- В веб-интерфейсе — отчет отображается в виде таблицы на странице.
- Отчет может сохраняться в лог-файл (например, в формате JSON или CSV) для дальнейшего анализа.

На этом этапе библиотека только **показывает проблемы**, но сама ничего не исправляет.



## Добавление новых правил
Добавить новое правило можно двумя способами:
### В конфигурационный файл
Если это стандартная проверка (дубликаты, формат, мусор), то достаточно дописать новое правило в `rules.json` — библиотека сразу его подхватит при следующем запуске.

### Расширение библиотеки
Если потребуется новая уникальная проверка (например, сверка данных с внешним реестром), можно написать новый анализатор, добавить его в библиотеку и разрешить указывать его в `rules.json`.



## Исправление
- Исправление не выполняется автоматически.
- Если пользователь захочет исправить данные, он может вручную выбрать, какие строки исправить.
- Исправление будет отдельным шагом — его можно запустить после анализа.
