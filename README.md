### Конфигурация и запуск Docker
Пример конфига в файле *env-example* если не требуются изменения то его достаточно переименовать в *.env*

Для запуска и сборке контейнеров нужно в директории проекта запустить команду.
```bash
docker-compose up -d --build
```

### Сборка проекта
Код проекта расположен в директории *www*.

1. Переименовать файл env-example в .env и внести туда коррективы при необходимости.
2. Зайти в контейнер с брокером 
```bash
    docker exec -it arcane_broker bash
``` 
и создать новый топик 
```bash
cd /opt/kafka/bin && ./kafka-topics.sh --create --topic video-upload --bootstrap-server localhost:9092
```
3. Зайти в контейнер с php
```bash
    docker exec -it arcane_broker php
```
* Собрать все зависимости
```bash
composer install
```
* Создать уникальный ключ приложения
```bash
./artisan key:generate
```

* Сделать миграцию базы
```bash
./artisan migrate
```

### Запуск проекта
1. Зайти в контейнер с php
```bash
    docker exec -it arcane_broker php
``` 
2. Запустить консюмир (При необходимость можно несколько для ускорения асинхронных операций)
```bash
./artisan consume:video-upload &
```

3. Запуск обработчика задач (Также можно несколько для увеличения скорости работы)
```bash
./artisan queue:work &
```
